<?php
/**
 * Dress Importer
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

class Loveforever_Dress_Importer {

	private $post_type = 'dress';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_dress_importer_route' ) );
		add_action( 'admin_menu', array( $this, 'register_xml_importer_page' ) );
	}

	public function register_dress_importer_route() {
		register_rest_route(
			'dress-importer/v1',
			'/import',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'dress_importer_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'dress-importer/v1',
			'/progress',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'dress_importer_progress_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'dress-importer/v1',
			'/clear',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'clear_all_dresses' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Initialize import progress tracking
	 *
	 * @param  int $total_items Total number of items to import.
	 * @return void
	 */
	public function init_import_progress( $total_items ) {
		$progress = array(
			'total'      => $total_items,
			'imported'   => 0,
			'percentage' => 0,
			'status'     => 'in_progress',
			'started_at' => time(),
		);

		set_transient( 'loveforever_import_progress', $progress, 24 * HOUR_IN_SECONDS );
	}

	/**
	 * Update import progress
	 *
	 * @param  int  $imported_count Current number of imported items.
	 * @param  bool $is_complete    Whether the import is complete.
	 * @return array Updated progress data
	 */
	public function update_import_progress( $imported_count, $is_complete = false ) {
		$progress = get_transient( 'loveforever_import_progress' );

		if ( ! $progress ) {
			return false;
		}

		$progress['imported']   = $imported_count;
		$progress['percentage'] = round( ( $imported_count / $progress['total'] ) * 100 );

		if ( $is_complete ) {
			$progress['status']       = 'completed';
			$progress['completed_at'] = time();
		}

		set_transient( 'loveforever_import_progress', $progress, 24 * HOUR_IN_SECONDS );

		return $progress;
	}

	/**
	 * Get current import progress
	 *
	 * @return array|bool Progress data or false if no import in progress
	 */
	public function get_import_progress() {
		return get_transient( 'loveforever_import_progress' );
	}

	private function clean_product_name( $name ) {
		$prefixes_to_remove = array(
			'Свадебное платье ',
			'Вечернее платье ',
			'Платье на выпускной ',
			'Платье ',
			'Аксессуар ',
		);

		// Remove the prefixes
		foreach ( $prefixes_to_remove as $prefix ) {
			if ( strpos( $name, $prefix ) === 0 ) {
				return trim( str_replace( $prefix, '', $name ) );
			}
		}

		return $name;
	}

	private function extract_product_slug( $url ) {
		$url = rtrim( $url, '/' );

		// Get the last part of the URL
		$parts = explode( '/', $url );
		$slug  = end( $parts );

		return $slug;
	}

	private function prepare_post_data( $product ) {
		$name = $this->clean_product_name( (string) $product->name );
		$slug = $this->extract_product_slug( (string) $product->url );

		$product_args = array(
			'post_title'  => $name,
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
		);

		if ( ! empty( $slug ) ) {
			$product_args['post_name'] = $slug;
		}

		return $product_args;
	}

	private function apply_product_category( $post_id, $product ) {
		if ( isset( $product->collectionId ) && ! empty( (string) $product->collectionId ) ) {
			$term = get_term_by( 'slug', (string) $product->collectionId, 'dress_category' );
			if ( $term ) {
				wp_set_object_terms( $post_id, $term->term_id, 'dress_category' );
				update_field( "dress_order_$term->term_id", 0, $post_id );
			}
		}
	}

	private function apply_product_meta( $post_id, $product ) {
		// Check if oldprice exists
		if ( isset( $product->oldprice ) && ! empty( (string) $product->oldprice ) ) {
			// Set has_discount to true
			update_field( 'has_discount', true, $post_id );

			// Set price_with_discount to the value of price from XML
			update_field( 'price_with_discount', (string) $product->price, $post_id );

			// Set price to the value of oldprice from XML
			update_field( 'price', (string) $product->oldprice, $post_id );
		} else {
			// If no oldprice, set has_discount to false and use regular price
			update_field( 'has_discount', false, $post_id );
			update_field( 'price', (string) $product->price, $post_id );
		}

		update_field( 'availability', 'true' == $product->store, $post_id );

		update_post_meta( $post_id, 'final_price', (string) $product->price );
	}

	/**
	 * Загружает несколько изображений параллельно
	 *
	 * @param  array $image_urls Массив URL изображений для загрузки
	 * @return array Массив ID загруженных изображений или WP_Error
	 */
	private function download_images_in_parallel( $image_urls ) {
		if ( empty( $image_urls ) ) {
			return array();
		}

		// Готовим данные для параллельных запросов
		$temp_files = array();
		$image_ids  = array();

		// Включаем необходимые файлы для загрузки медиа
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			include_once ABSPATH . 'wp-admin/includes/image.php';
			include_once ABSPATH . 'wp-admin/includes/file.php';
			include_once ABSPATH . 'wp-admin/includes/media.php';
		}

		for ( $i = 0; $i < count( $image_urls ); $i++ ) {
			$url       = $image_urls[ $i ];
			$temp_file = download_url( (string) $url );

			if ( is_wp_error( $temp_file ) ) {
				// Если не удалось загрузить, записываем ошибку
				$image_ids[ $i ] = $temp_file;
			} else {
				// Сохраняем ссылку на временный файл для последующей обработки
				$temp_files[ $i ] = $temp_file;
			}
		}

		// Шаг 2: Обрабатываем успешно загруженные временные файлы
		foreach ( $temp_files as $index => $temp_file ) {
			$file_array = array(
				'name'     => basename( (string) $image_urls[ $index ] ),
				'tmp_name' => $temp_file,
			);

			// Добавляем файл в медиабиблиотеку
			$image_id = media_handle_sideload( $file_array );

			// Сохраняем результат
			$image_ids[ $index ] = $image_id;
		}

		return $image_ids;
	}

	private function import_images( $post_id, $product ) {
		if ( isset( $product->picture ) ) {
			if ( is_array( $product->picture ) || $product->picture instanceof \Traversable ) {
				$images = $product->picture;

				if ( count( $images ) > 0 ) {
					$featured_image_id = loveforever_download_and_add_image_to_library( (string) esc_url( trim( $images[0] ) ) );

					if ( ! is_wp_error( $featured_image_id ) ) {
						set_post_thumbnail( $post_id, $featured_image_id );
					} else {
						error_log( 'Ошибка загрузки главного изображения: ' . $featured_image_id->get_error_message() );
					}

					if ( count( $images ) > 1 ) {
						$image_array = array();
						for ( $i = 1; $i < count( $images ); $i++ ) {
							$image_id = loveforever_download_and_add_image_to_library( (string) esc_url( trim( $images[ $i ] ) ) );

							if ( ! is_wp_error( $image_id ) ) {
								$image_array[] = array( 'image' => $image_id );
							} else {
								error_log( 'Ошибка загрузки дополнительного изображения: ' . $image_id->get_error_message() );
							}
						}
						update_field( 'images', $image_array, $post_id );
					}
				}
			} else {
				$featured_image_id = loveforever_download_and_add_image_to_library( (string) $product->picture );

				if ( ! is_wp_error( $featured_image_id ) ) {
					set_post_thumbnail( $post_id, $featured_image_id );
				} else {
					error_log( 'Ошибка загрузки главного изображения: ' . $featured_image_id->get_error_message() );
				}
			}
		}
	}

	public function import_products_batch( $offset, $limit ) {
		$xml      = simplexml_load_file( get_template_directory() . '/loveforever.xml' );
		$products = $xml->shop->offers->offer;

		$total_products = count( $products );

		$progress = $this->get_import_progress();

		$imported_count     = $progress ? $progress['imported'] : 0;
		$processed_in_batch = 0;

		$batch_end = min( $offset + $limit, $total_products );

		for ( $i = $offset; $i < $batch_end; $i++ ) {
			$product = $products[ $i ];

			$imported_product = $this->import_product( $product );

			if ( ! is_wp_error( $imported_product ) ) {
				++$imported_count;
			}

			++$processed_in_batch;

			$processed_count = $offset + $processed_in_batch;

			$this->update_import_progress( $imported_count, $processed_count >= $total_products );
		}

		$total_imported = $imported_count;

		return array(
			'total_imported' => $total_imported,
			'processed'      => $processed_count,
			'is_complete'    => $processed_count >= $total_products,
		);
	}

	public function dress_importer_callback( $request ) {
		$params = $request->get_params();
		$limit  = isset( $params['limit'] ) ? intval( $params['limit'] ) : 1;
		$offset = isset( $params['offset'] ) ? intval( $params['offset'] ) : 0;

		// Первое обращение - получаем общее количество товаров
		if ( $offset === 0 ) {
			delete_transient( 'loveforever_import_progress' );
			$xml_file = get_template_directory() . '/loveforever.xml';

			if ( ! file_exists( $xml_file ) ) {
				return new WP_REST_Response(
					array(
						'message' => 'XML файл не найден',
					),
					404
				);
			}

			$xml = simplexml_load_file( $xml_file );
			if ( false === $xml ) {
				return new WP_REST_Response(
					array(
						'message' => 'Ошибка при загрузке XML файла',
					),
					500
				);
			}

			$total_items = count( $xml->shop->offers->offer );
			$this->init_import_progress( $total_items );
		}

		$result = $this->import_products_batch( $offset, $limit );

		return new WP_REST_Response(
			array(
				'message'        => "Импортировано {$result['total_imported']} товаров",
				'total_imported' => $result['total_imported'],
				'processed'      => $result['processed'],
				'is_complete'    => $result['is_complete'],
				'next_offset'    => $result['is_complete'] ? null : $offset + $limit,
				'progress'       => $this->get_import_progress(),
			),
			200
		);
	}

	/**
	 * Callback function for progress check endpoint.
	 *
	 * @return WP_REST_Response
	 */
	public function dress_importer_progress_callback() {
		$progress = $this->get_import_progress();

		if ( ! $progress ) {
			return new WP_REST_Response(
				array(
					'message'  => 'Нет активного процесса импорта',
					'progress' => null,
				),
				200,
				array( 'Content-Type' => 'application/json' )
			);
		}

		return new WP_REST_Response(
			array(
				'message'  => 'Прогресс импорта',
				'progress' => $progress,
			),
			200,
			array( 'Content-Type' => 'application/json' )
		);
	}

	public function import_product( $product ) {
		$product_args = $this->prepare_post_data( $product );

		$post_id = wp_insert_post( $product_args );

		if ( $post_id ) {
			$this->apply_product_category( $post_id, $product );
			$this->apply_product_meta( $post_id, $product );
			$this->import_images( $post_id, $product );

			return $post_id;
		}

		return new WP_Error( 'import_failed', 'Не удалось импортировать товар: ' . $product_args['post_title'] );
	}

	/**
	 * Usage example as a WordPress admin page
	 */
	public function xml_importer_admin_page() {
		?>
		<div class="wrap">
			<h1>XML Product Importer</h1>
			<p>Import products from XML file with featured images and ACF repeater fields.</p>
		<?php $this->display_import_form(); ?>
		</div>
		<?php
	}

	/**
	 * Display the import form
	 */
	public function display_import_form() {
		?>
		<div id="importProgressMessages" style="display: grid; row-gap: 20px"></div>
		<form id="xmlImporterForm" method="post" enctype="multipart/form-data">
			<p class="submit">
				<input type="submit" name="import_xml" class="button button-primary" value="Import Products" />
			</p>
		</form>
		<script>
			let isLoading = false;
			const $submitButton = jQuery('#xmlImporterForm').find('[type="submit"]');

			function startImport() {
				importBatch(0);
			}

			function importBatch(offset) {
				const batchSize = 1; // Размер пакета
				
				// Показ индикатора прогресса
				updateProgressUI("Импортируем товары...");
				
				// AJAX запрос
				jQuery.ajax({
					url: location.origin + '/wp-json/dress-importer/v1/import',
					method: 'POST',
					data: {
						offset: offset,
						batch_size: batchSize
					},
					success: function(response) {
						console.log({response});
						
						// Обновление прогресса
						updateProgressUI(`Импортировано ${response.total_imported} из ${response.progress.total} товаров (${response.progress.percentage}%)`);
						
						// Если импорт не завершен, продолжаем с новым смещением
						if (!response.is_complete && response.next_offset !== null) {
							setTimeout(function() {
								importBatch(response.next_offset);
							}, 2_500); // Небольшая пауза между запросами
						} else {
							updateProgressUI("Импорт завершен!");
							isLoading = false;
							$submitButton.attr('disabled', isLoading);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.error("Ошибка импорта:", errorThrown);
						updateProgressUI(`Произошла ошибка при импорте. Ошибка: ${textStatus}`);
					}
				});
			}

			function updateProgressUI(message) {
				jQuery('#importProgressMessages').append(`<div>${message}</div>`);
			}

			jQuery('#xmlImporterForm').on('submit', function(e) {
				e.preventDefault();
				if (!isLoading) {
					isLoading = true;
					$submitButton.attr('disabled', isLoading);
					startImport();
				}
			});
		</script>
		<?php
	}

	/**
	 * Register admin page
	 */
	public function register_xml_importer_page() {
		add_menu_page(
			'XML Product Importer',
			'XML Importer',
			'manage_options',
			'xml-product-importer',
			array( $this, 'xml_importer_admin_page' ),
			'dashicons-upload',
			30
		);
	}

	public function clear_all_dresses() {
		$post_type  = $this->post_type; // Тип постов
		$limit      = -1; // Кол-во постов
		$acf_fields = array( 'video', 'images' ); // Названия ACF полей

		$args = array(
			'post_type'      => $post_type,
			'posts_per_page' => $limit,
			'post_status'    => 'any',
			'fields'         => 'ids',
		);

		$post_ids = get_posts( $args );

		foreach ( $post_ids as $post_id ) {
			$thumbnail_id = get_post_thumbnail_id( $post_id );
			if ( $thumbnail_id ) {
				wp_delete_attachment( $thumbnail_id, true );
			}

			foreach ( $acf_fields as $field_name ) {
				$field_value = get_field( $field_name, $post_id );

				if ( ! empty( $field_value ) ) {
					if ( 'video' === $field_name ) {
						wp_delete_attachment( $field_value['ID'], true );
					}

					if ( 'images' === $field_name ) {
						foreach ( $field_value as $field_value_item ) {
							if ( ! empty( $field_value_item['image'] ) ) {
								wp_delete_attachment( $field_value_item['image']['ID'] );
							}
						}
					}
				}
			}

			wp_delete_post( $post_id, true );
		}

		return new WP_REST_Response(
			array(
				'message'       => 'Товары успешно удалены',
				'total_deleted' => count( $post_ids ),
			),
			200
		);
	}
}

new Loveforever_Dress_Importer();

// /**
// * Import dresses from XML file
// *
// * @return int Number of imported dresses
// */
// function loveforever_import_dresses() {
// $xml         = simplexml_load_file( get_template_directory() . '/loveforever.xml' );
// $total_items = count( $xml->shop->offers->offer );

// Initialize progress tracking
// loveforever_init_import_progress( $total_items );

// $imported_count = 0;

// foreach ( $xml->shop->offers->offer as $offer ) {
// Clean the product name
// $product_name = loveforever_clean_product_name( (string) $offer->name );

// Extract slug from URL
// $post_slug = '';
// if ( isset( $offer->url ) && ! empty( (string) $offer->url ) ) {
// $post_slug = loveforever_extract_slug_from_url( (string) $offer->url );
// }

// $post_args = array(
// 'post_title'  => $product_name,
// 'post_type'   => 'dress',
// 'post_status' => 'publish',
// );

// if ( ! empty( $post_slug ) ) {
// $post_args['post_name'] = $post_slug;
// }

// $post_id = wp_insert_post( $post_args );

// if ( $post_id ) {
// $category_slug = (string) $offer->collectionId;
// $term          = get_term_by( 'slug', $category_slug, 'dress_category' );
// if ( $term ) {
// wp_set_object_terms( $post_id, $term->term_id, 'dress_category' );
// }

// Check if oldprice exists
// if ( isset( $offer->oldprice ) && ! empty( (string) $offer->oldprice ) ) {
// Set has_discount to true
// update_field( 'has_discount', true, $post_id );

// Set price_with_discount to the value of price from XML
// update_field( 'price_with_discount', (string) $offer->price, $post_id );

// Set price to the value of oldprice from XML
// update_field( 'price', (string) $offer->oldprice, $post_id );
// } else {
// If no oldprice, set has_discount to false and use regular price
// update_field( 'has_discount', false, $post_id );
// update_field( 'price', (string) $offer->price, $post_id );
// }

// update_post_meta( $post_id, 'final_price', (string) $offer->price );

// $images = $offer->picture;
// if ( count( $images ) > 0 ) {
// $featured_image_id = loveforever_download_and_add_image_to_library( (string) $images[0] );
// set_post_thumbnail( $post_id, $featured_image_id );

// if ( count( $images ) > 1 ) {
// $image_array = array();
// for ( $i = 1; $i < count( $images ); $i++ ) {
// $image_id      = loveforever_download_and_add_image_to_library( (string) $images[ $i ] );
// $image_array[] = array( 'image' => $image_id );
// }
// update_field( 'images', $image_array, $post_id );
// }
// }

// ++$imported_count;

// Update progress after each item
// loveforever_update_import_progress( $imported_count );
// }
// }

// Mark import as complete
// loveforever_update_import_progress( $imported_count, true );

// return $imported_count;
// }

// /**
// * Registers a new REST API route for importing dresses.
// *
// * The route is `/dress-importer/v1/import` and accepts the `POST` method.
// * It calls the `loveforever_dress_importer_callback` function when invoked.
// *
// * @since 0.0.1
// */
// function loveforever_register_dress_importer_route() {
// register_rest_route(
// 'dress-importer/v1',
// '/import',
// array(
// 'methods'             => 'POST',
// 'callback'            => 'loveforever_dress_importer_callback',
// 'permission_callback' => '__return_true',
// )
// );

// Register progress check endpoint
// register_rest_route(
// 'dress-importer/v1',
// '/progress',
// array(
// 'methods'             => 'GET',
// 'callback'            => 'loveforever_dress_importer_progress_callback',
// 'permission_callback' => '__return_true',
// )
// );
// }
// add_action( 'rest_api_init', 'loveforever_register_dress_importer_route' );

// /**
// * Callback функция для REST API endpoint.
// *
// * @since 0.0.1
// */
// function loveforever_dress_importer_callback() {
// Reset progress before starting a new import
// delete_transient( 'loveforever_import_progress' );

// $imported_count = loveforever_import_dresses();
// return new WP_REST_Response(
// array(
// 'message'  => "Импорт завершен. Импортировано платьев: $imported_count",
// 'progress' => loveforever_get_import_progress(),
// ),
// 201,
// array( 'Content-Type' => 'application/json' )
// );
// }

// /**
// * Callback function for progress check endpoint.
// *
// * @return WP_REST_Response
// */
// function loveforever_dress_importer_progress_callback() {
// $progress = loveforever_get_import_progress();

// if ( ! $progress ) {
// return new WP_REST_Response(
// array(
// 'message'  => 'Нет активного процесса импорта',
// 'progress' => null,
// ),
// 200,
// array( 'Content-Type' => 'application/json' )
// );
// }

// return new WP_REST_Response(
// array(
// 'message'  => 'Прогресс импорта',
// 'progress' => $progress,
// ),
// 200,
// array( 'Content-Type' => 'application/json' )
// );
// }
