<?php
/**
 * Dress Importer
 *
 * @package LoveForever
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LoveForever_Dress_Importer
 */
class LoveForever_Dress_Importer {
	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get instance of this class.
	 *
	 * @return LoveForever_Dress_Importer
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private $post_type = 'dress';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_import_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_import_dresses', array( $this, 'ajax_import_dresses' ) );
	}

	/**
	 * Add import page to admin menu.
	 */
	public function add_import_page() {
		add_submenu_page(
			'edit.php?post_type=dress',
			'Импорт платьев',
			'Импорт платьев',
			'manage_options',
			'dress-import',
			array( $this, 'render_import_page' )
		);
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( 'dress_page_dress-import' !== $screen->id ) {
			return;
		}

		wp_enqueue_style(
			'loveforever-dress-importer',
			get_template_directory_uri() . '/css/dress-importer.css',
			array(),
			filemtime( get_template_directory() . '/css/dress-importer.css' )
		);

		wp_enqueue_script(
			'loveforever-dress-importer',
			get_template_directory_uri() . '/js/dress-importer.js',
			array( 'jquery' ),
			filemtime( get_template_directory() . '/js/dress-importer.js' ),
			true
		);

		wp_localize_script(
			'loveforever-dress-importer',
			'loveforeverDressImporter',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'import_dresses_nonce' ),
				'importing' => 'Импорт платьев...',
				'complete'  => 'Импорт завершен!',
				'error'     => 'Произошла ошибка при импорте.',
			)
		);
	}

	/**
	 * Render import page.
	 */
	public function render_import_page() {
		?>
		<div class="wrap">
			<h1>Импорт платьев</h1>
			<div class="dress-importer-container">
				<form id="dress-import-form" method="post" enctype="multipart/form-data">
					<div class="form-field">
						<label for="dress-xml">Выберите XML файл:</label>
						<input type="file" name="dress-xml" id="dress-xml" accept=".xml" required>
					</div>
					<div class="form-field">
						<button type="submit" class="button button-primary">Импортировать</button>
					</div>
				</form>
				<div id="import-progress" style="display: none;">
					<div class="progress-bar">
						<div class="progress-bar-fill"></div>
					</div>
					<div class="progress-status"></div>
				</div>
			</div>
		</div>
		<?php
	}


	private function clean_product_name( $product_name ) {
		error_log( 'Start cleaning name for ' . $product_name );
		$prefixes_to_remove = array(
			'Свадебное платье ',
			'Вечернее платье ',
			'Платье на выпускной ',
			'Платье ',
			'Аксессуар ',
		);

		// Remove the prefixes
		foreach ( $prefixes_to_remove as $prefix ) {
			if ( strpos( $product_name, $prefix ) === 0 ) {
				return trim( str_replace( $prefix, '', $product_name ) );
			}
		}

		return $product_name;
	}

	private function extract_product_slug( $url ) {
		error_log( 'Start extracting slug from ' . $url );
		$url = rtrim( $url, '/' );

		// Get the last part of the URL
		$parts = explode( '/', $url );
		$slug  = end( $parts );

		return $slug;
	}

	private function prepare_product_data( $product ) {
		error_log( 'Preparing data for ' . $product->name );
		$title = $this->clean_product_name( (string) $product->name );
		$slug  = $this->extract_product_slug( (string) $product->url );

		$product_args = array(
			'post_title'   => $title,
			'post_type'    => $this->post_type,
			'post_content' => trim( (string) $product->description ),
			'post_status'  => 'publish',
		);

		if ( ! empty( $slug ) ) {
			$product_args['post_name'] = $slug;
		}

		return $product_args;
	}

	private function apply_product_category( $post_id, $product ) {
		error_log( 'Apply category for product with ID: ' . $post_id );
		// Set category.
		if ( ! empty( $product->collectionId ) ) {
			$term = get_term_by( 'slug', (string) $product->collectionId, 'dress_category' );
			if ( $term ) {
				wp_set_object_terms( $post_id, $term->term_id, 'dress_category' );
				update_field( "dress_order_$term->term_id", 0, $post_id );
			}
		}
	}

	private function apply_product_meta( $post_id, $product ) {
		error_log( 'Start appling product meta' );
		$price            = absint( $product->price );
		$old_price        = isset( $product->oldprice ) ? absint( $product->oldprice ) : 0;
		$discount_percent = 0;

		if ( 0 < $old_price ) {
			update_field( 'has_discount', true, $post_id );
			update_field( 'price_with_discount', $price, $post_id );
			update_field( 'price', $old_price, $post_id );

			$discount_percent = round( 100 - ( $price / $old_price * 100 ) );
		} else {
			update_field( 'has_discount', false, $post_id );
			update_field( 'price', $price, $post_id );
		}

		update_field( 'discount_percent', $discount_percent, $post_id );

		update_field( 'availability', 'true' == $product->store, $post_id );

		update_post_meta( $post_id, 'final_price', (string) $price );
	}

	private function import_images( $post_id, $product ) {
		error_log( 'Start import images' );

		ob_start();
		var_dump( $product->picture );
		$log = ob_get_clean();

		error_log( 'Log: ' . $log );

		if ( ! isset( $product->picture ) ) {
			return;
		}

		$pictures = $product->picture;

		if ( $pictures instanceof \SimpleXMLElement && $pictures->count() > 0 ) {
			$image_ids = array();

			foreach ( $pictures as $picture ) {
				$image_id = $this->import_image( (string) esc_url( trim( $picture ) ), $post_id );

				error_log( 'Image: ' . (string) $image_id );

				if ( $image_id ) {
					$image_ids[] = $image_id;
				}
			}

			error_log( 'Image uploaded ' . count( $image_ids ) . ' ' . wp_json_encode( $image_ids ) );

			if ( ! empty( $image_ids ) ) {
				set_post_thumbnail( $post_id, $image_ids[0] );

				$gallery_images = array_map( fn( $id ) => array( 'image' => $id ), array_slice( $image_ids, 1 ) );
				update_field( 'images', $gallery_images, $post_id );

				error_log( 'Successfully attached ' . count( $image_ids ) . ' images to post ' . $post_id );
			}
		} else {
			$feature_image_id = $this->import_image( (string) esc_url( trim( $pictures ) ), $post_id );
			set_post_thumbnail( $post_id, $feature_image_id );
		}
	}

	public function import_product( $product ) {
		$product_args = $this->prepare_product_data( $product );

		error_log( 'Product arguments: ' . wp_json_encode( $product_args, JSON_UNESCAPED_UNICODE ) );

		$post_id = wp_insert_post( $product_args );

		error_log( 'Создан новый товар ' . $product_args['post_title'] . 'с ID: ' . $post_id );

		if ( $post_id ) {
			$this->apply_product_category( $post_id, $product );
			$this->apply_product_meta( $post_id, $product );
			$this->import_images( $post_id, $product );

			return $post_id;
		}

		return new WP_Error( 'import_failed', 'Не удалось импортировать товар: ' . $product_args['post_title'] );
	}

	/**
	 * Handle AJAX import request.
	 */
	public function ajax_import_dresses() {
		check_ajax_referer( 'import_dresses_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Недостаточно прав для выполнения операции.' );
		}

		// Enable error logging.
		error_log( 'Starting dress import process' );

		// Получаем или создаем временный файл для хранения прогресса.
		$temp_file = get_temp_dir() . 'dress_import_' . get_current_user_id() . '.tmp';

		// Если это первый запрос.
		if ( ! isset( $_POST['current_row'] ) ) {
			if ( ! isset( $_FILES['file'] ) ) {
				error_log( 'No file uploaded' );
				wp_send_json_error( 'Файл не был загружен.' );
			}

			$file = array_map( 'sanitize_text_field', wp_unslash( $_FILES['file'] ) );
			error_log( 'File type: ' . $file['type'] );

			if ( 'text/xml' !== $file['type'] && 'application/xml' !== $file['type'] ) {
				error_log( 'Invalid file type: ' . $file['type'] );
				wp_send_json_error( 'Неверный формат файла. Пожалуйста, загрузите XML файл.' );
			}

			// Сохраняем XML файл во временный файл.
			if ( ! move_uploaded_file( $file['tmp_name'], $temp_file ) ) {
				error_log( 'Failed to move uploaded file to: ' . $temp_file );
				wp_send_json_error( 'Не удалось сохранить файл.' );
			}

			// Count actual number of dresses in XML.
			$total_rows = 0;
			$xml        = simplexml_load_file( $temp_file );
			if ( $xml ) {
				$total_rows = count( $xml->shop->offers->offer );
			}

			error_log( 'Total dresses in file: ' . $total_rows );
			$current_row = 0;
			$imported    = 0;
			$skipped     = 0;
		} else {
			// Получаем сохраненный прогресс.
			$current_row = isset( $_POST['current_row'] ) ? intval( $_POST['current_row'] ) : 0;
			$imported    = isset( $_POST['imported'] ) ? intval( $_POST['imported'] ) : 0;
			$skipped     = isset( $_POST['skipped'] ) ? intval( $_POST['skipped'] ) : 0;
			$total_rows  = isset( $_POST['total_rows'] ) ? intval( $_POST['total_rows'] ) : 0;
			error_log( "Continuing import from row {$current_row}. Imported: {$imported}, Skipped: {$skipped}" );
		}

		$xml = simplexml_load_file( $temp_file );
		if ( false === $xml ) {
			error_log( 'Failed to load XML file: ' . $temp_file );
			wp_send_json_error( 'Не удалось загрузить XML файл.' );
		}

		$offers = $xml->shop->offers->offer;
		if ( ! isset( $offers[ $current_row ] ) ) {
			error_log( 'Import completed. Final stats - Imported: ' . $imported . ', Skipped: ' . $skipped );
			// Импорт завершен.
			unlink( $temp_file ); // Удаляем временный файл.
			wp_send_json_success(
				array(
					'imported' => $imported,
					'skipped'  => $skipped,
					'total'    => $total_rows,
					'complete' => true,
				)
			);
		}

		$offer = $offers[ $current_row ];
		error_log( 'Processing dress ' . ( $current_row + 1 ) . ': ' . (string) $offer->name );

		// Validate data.
		if ( empty( $offer->name ) ) {
			error_log( 'Skipping dress ' . ( $current_row + 1 ) . ': Empty name' );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
			return;
		}

		$imported_product = $this->import_product( $offer );

		if ( is_wp_error( $imported_product ) ) {
			error_log( 'Failed to create dress post: ' . $imported_product->get_error_message() );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
			return;
		}

		error_log( 'Successfully imported dress - Name: ' . $offer->name );
		++$imported;

		$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
	}

	/**
	 * Send progress update via AJAX.
	 *
	 * @param int $current_row Current row number.
	 * @param int $total_rows Total number of rows.
	 * @param int $imported Number of imported dresses.
	 * @param int $skipped Number of skipped dresses.
	 */
	private function send_progress_update( $current_row, $total_rows, $imported, $skipped ) {
		$progress = round( ( $current_row / $total_rows ) * 100 );

		wp_send_json_success(
			array(
				'progress'    => $progress,
				'imported'    => $imported,
				'skipped'     => $skipped,
				'total'       => $total_rows,
				'current_row' => $current_row,
				'complete'    => false,
				'message'     => sprintf(
					'Обработано %d из %d платьев (%d%%). Импортировано: %d, Пропущено: %d',
					$current_row,
					$total_rows,
					$progress,
					$imported,
					$skipped
				),
			)
		);
	}

	/**
	 * Import image from URL.
	 *
	 * @param string $url Image URL.
	 * @return int|false Attachment ID or false on failure.
	 */
	private function import_image( $url ) {
		error_log( 'Downloading... ' . $url );

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			return false;
		}

		$file_array = array(
			'name'     => basename( $url ),
			'tmp_name' => $tmp,
		);

		$attachment_id = media_handle_sideload( $file_array, 0 );
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp );
			return false;
		}

		return $attachment_id;
	}
}

// Initialize the importer.
LoveForever_Dress_Importer::get_instance();
