<?php
/**
 * Hooks And Filters
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'wp_mail_from_name', 'loveforever_wp_mail_from_name' );
function loveforever_wp_mail_from_name( $from_name ) {
	$site_name = get_bloginfo( 'name' );
	$from_name = $site_name;
	return $from_name;
}

add_action( 'admin_menu', 'loveforever_remove_comments_admin_menu' );
function loveforever_remove_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}

add_action( 'wp_ajax_create_new_fitting_record', 'loveforever_create_new_fitting_record_via_ajax' );
add_action( 'wp_ajax_nopriv_create_new_fitting_record', 'loveforever_create_new_fitting_record_via_ajax' );
function loveforever_create_new_fitting_record_via_ajax() {
	if ( ! isset( $_POST['submit_fitting_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['submit_fitting_form_nonce'] ) ), 'submit_fitting_form' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка в запросе',
				'debug'   => 'Невалидный nonce',
			),
			400
		);
	}

	if ( ! isset( $_POST['name'] ) || empty( $_POST['name'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Пожалуйста, введите ваше имя',
				'debug'   => 'Поле имя не заполнено',
			),
			400
		);
	}

	if ( ! isset( $_POST['phone'] ) || empty( $_POST['name'] ) || ! loveforever_is_valid_phone( $_POST['phone'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Пожалуйста, введите корректный номер телефона',
				'debug'   => 'Некорректный номер телефона',
			),
			400
		);
	}

	if ( ! isset( $_POST['dress_category'] ) || empty( $_POST['dress_category'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Пожалуйста, укажите тип платья',
				'debug'   => 'Не указана категория платья',
			),
			400
		);
	}

	if ( ! isset( $_POST['date'] ) || empty( $_POST['date'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Пожалуйста, укажите желаемую дату примерки',
				'debug'   => 'Не указана желаемая дата примерки',
			),
			400
		);
	}

	if ( ! isset( $_POST['time'] ) || empty( $_POST['time'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Пожалуйста, укажите время',
				'debug'   => 'Не указано желаемое время примерки',
			),
			400
		);
	}

	$name                    = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	$phone                   = sanitize_text_field( wp_unslash( $_POST['phone'] ) );
	$dress_category          = is_array( $_POST['dress_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['dress_category'] ) ) : sanitize_text_field( wp_unslash( $_POST['dress_category'] ) );
	$date                    = sanitize_text_field( wp_unslash( $_POST['date'] ) );
	$time                    = sanitize_text_field( wp_unslash( $_POST['time'] ) );
	$ip_address              = loveforever_get_client_ip_address();
	$target_dress            = ! empty( $_POST['target_dress'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['target_dress'] ) ) : 0;
	$client_favorite_dresses = ! empty( $_POST['client_favorite_dresses'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['client_favorite_dresses'] ) ) ) : array();

	$is_valid_fitting_time = loveforever_is_valid_fitting_datetime( $date . ' ' . $time, $dress_category );

	if ( true !== $is_valid_fitting_time ) {
		wp_send_json_error(
			array(
				'message' => $is_valid_fitting_time,
				'debug'   => 'Невалидное время примерки',
			),
			400
		);
	}

	$fitting_post_data = array(
		'post_title'  => 'Новая примерка для ' . $name,
		'post_status' => 'publish',
		'post_type'   => 'fitting',
	);

	$fitting_post_id = wp_insert_post(
		$fitting_post_data
	);

	if ( is_wp_error( $fitting_post_id ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка при создании заявки. Обновите страницу и попробуйте еще раз',
				'debug'   => $fitting_post_id,
			),
			400
		);
	}

	update_field( 'fitting_type', $dress_category, $fitting_post_id );
	update_field( 'fitting_time', $date . ' ' . $time, $fitting_post_id );
	update_field( 'phone', $phone, $fitting_post_id );
	update_field( 'name', $name, $fitting_post_id );
	update_field( 'ip_address', $ip_address, $fitting_post_id );

	if ( ! empty( $target_dress ) ) {
		update_field( 'target_dress', $target_dress, $fitting_post_id );
	}

	if ( ! empty( $client_favorite_dresses ) ) {
		update_field( 'client_favorite_dresses', $client_favorite_dresses, $fitting_post_id );
	}

	wp_send_json_success(
		array(
			'message' => 'Вы успешно записались на примерку',
		),
		201
	);
}

add_action( 'wp_ajax_get_date_time_slots', 'loveforever_get_date_time_slots_via_ajax' );
add_action( 'wp_ajax_nopriv_get_date_time_slots', 'loveforever_get_date_time_slots_via_ajax' );
function loveforever_get_date_time_slots_via_ajax() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка запроса!',
			),
			400
		);
	}

	$date_increment_ratio = ! empty( $_POST['date-increment-ratio'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['date-increment-ratio'] ) ) : 0;

	$start_date  = gmdate( 'd.m.Y', strtotime( '+' . 3 * $date_increment_ratio . 'days', current_time( 'timestamp' ) ) );
	$end_date    = gmdate( 'd.m.Y', strtotime( '+' . 3 * $date_increment_ratio + 2 . 'days', current_time( 'timestamp' ) ) );
	$slots_range = Fitting_Slots::get_slots_range( $start_date, $end_date );

	$html = '';

	ob_start();
	foreach ( $slots_range as $slots_range_date => $slots ) : ?>
		<div class="fitting-form__day-column">
			<div class="fitting-form__day-column-head">
				<label class="fitting-form__day-input radio">
					<!-- <input class="radio__input" type="radio" name="date" id="" value="01.02"> -->
					<span class="radio__label"><?php echo esc_html( gmdate( 'd.m (D)', strtotime( $slots_range_date ) ) ); ?></span>
				</label>
			</div>
			<ol class="fitting-form__day-column-list">
				<?php foreach ( $slots as $time => $slot ) : ?>
				<li class="fitting-form__day-column-list-item">
					<label class="radio">
						<input 
							class="radio__input" 
							type="radio" 
							name="time" 
							id="" 
							value="<?php echo esc_attr( $time ); ?>"
							<?php echo 0 === $slot['available'] ? 'disabled' : ''; ?>
							data-js-fitting-form-date-value="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $slots_range_date ) ) ); ?>"
						>
						<span class="radio__label"><?php echo esc_html( $time ); ?></span>
					</label>
				</li>
				<?php endforeach; ?>
			</ol>
		</div>
		<?php
	endforeach;

	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'message' => 'Слоты успешно загружены',
			'html'    => $html,
		),
		200
	);
}

add_action( 'wp_ajax_get_fitting_time_slots', 'loveforever_get_date_fitting_time_slots_via_ajax' );
add_action( 'wp_ajax_nopriv_get_fitting_time_slots', 'loveforever_get_date_fitting_time_slots_via_ajax' );
function loveforever_get_date_fitting_time_slots_via_ajax() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка запроса!',
				'debug'   => 'Некорректный nonce',
			),
			400
		);
	}

	if ( empty( $_POST['date'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Укажите желаемую дату примерки',
				'debug'   => 'Не указана дата примерки',
			),
			400
		);
	}

	$date                   = sanitize_text_field( wp_unslash( $_POST['date'] ) );
	$fitting_slots_for_date = Fitting_Slots::get_day_slots( $date, current_time( 'timestamp' ) );

	wp_send_json_success(
		array(
			'slots'   => $fitting_slots_for_date,
			'message' => "Слоты для $date успешно загружены",
		)
	);
}

add_action( 'wp_ajax_get_filtered_products', 'loveforever_get_filtered_products_via_ajax' );
add_action( 'wp_ajax_get_filtered_products', 'loveforever_get_filtered_products_via_ajax' );
function loveforever_get_filtered_products_via_ajax() {
	if ( ! isset( $_POST['submit_filter_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['submit_filter_form_nonce'] ) ), 'submit_filter_form' ) ) {
		wp_send_json_error(
			array( 'message' => 'Ошибка в запросе' ),
			400
		);
	}

	$min_price = ! empty( $_POST['min-price'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['min-price'] ) ) : 0;
	$max_price = ! empty( $_POST['max-price'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['max-price'] ) ) : 1000000000;
	$page      = ! empty( $_POST['page'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['page'] ) ) : 1;
	$orderby   = ! empty( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'views';

	$products_query_args = array(
		'post_type'      => 'dress',
		'posts_per_page' => get_field( 'products_per_page', 'option' ),
		'paged'          => $page,
		'meta_query'     => array(
			array(
				'key'     => 'price',
				'value'   => array( $min_price, $max_price + 1 ),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			),
		),
	);

	switch ( $orderby ) {
		case 'date':
			$products_query_args['orderby'] = 'date';
			$products_query_args['order']   = 'DESC';
			break;
		case 'min-price':
			$products_query_args['meta_key'] = 'price';
			$products_query_args['orderby']  = 'meta_value_num';
			$products_query_args['order']    = 'ASC';
			break;
		case 'max-price':
			$products_query_args['meta_key'] = 'price';
			$products_query_args['orderby']  = 'meta_value_num';
			$products_query_args['order']    = 'DESC';
			break;
		default:
			$products_query_args['meta_key'] = 'product_views_count';
			$products_query_args['orderby']  = 'meta_value_num';
			$products_query_args['order']    = 'DESC';
			break;
	}

	if ( ! empty( $_POST['taxonomy'] ) && ! empty( $_POST[ $_POST['taxonomy'] ] ) ) {
		$taxonomy = sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) );
		$term_id  = sanitize_text_field( wp_unslash( $_POST[ $taxonomy ] ) );

		$products_query_args['tax_query'][] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => array( $term_id ),
		);
	}

	if ( ! empty( $_POST['silhouette'] ) ) {
		$products_query_args['tax_query'][] = array(
			'taxonomy' => 'silhouette',
			'field'    => 'term_id',
			'terms'    => array( (int) sanitize_text_field( wp_unslash( $_POST['silhouette'] ) ) ),
		);
	}

	$products_query = new WP_Query( $products_query_args );

	if ( ! $products_query->have_posts() ) {
		wp_send_json_success(
			array(
				'feed'       => '<div class="empty-content"><p>Товары с заданными параметрами не найдены</p></div>',
				'pagination' => '',
			),
			200
		);
	}

	ob_start();

	while ( $products_query->have_posts() ) :
		$products_query->the_post();
		?>
			<div id="w-node-_53fa07b3-8fd9-bf77-2e13-30ca426c3020-d315ac0c" class="test-grid">
				<?php get_template_part( 'components/dress-card' ); ?>
			</div>
		<?php
	endwhile;
	wp_reset_postdata();

	$feed = ob_get_clean();

	$base_url = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

	ob_start();

	echo loveforever_get_pagination_html(
		$products_query,
		array(
			'base_url'        => $base_url,
			'is_catalog_page' => true,
		)
	);

	$pagination = ob_get_clean();

	wp_send_json_success(
		array(
			'message'    => 'Товары получены успешно!',
			'feed'       => $feed,
			'pagination' => $pagination,
		),
		200
	);
}

add_action( 'wp_ajax_track_product_view', 'loveforever_track_product_view_via_ajax' );
add_action( 'wp_ajax_nopriv_track_product_view', 'loveforever_track_product_view_via_ajax' );
function loveforever_track_product_view_via_ajax() {
	// if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
	// wp_send_json_error(
	// array(
	// 'message' => 'Ошибка запроса!',
	// ),
	// 400
	// );
	// }

	if ( empty( $_POST['product_id'] ) ) {
		wp_send_json_error( array( 'message' => 'Id товара не передан!' ) );
	}

	$product_id = (int) sanitize_text_field( wp_unslash( $_POST['product_id'] ) );

	loveforever_update_viewed_products( $product_id );

	wp_send_json_success(
		array(
			'message' => "Товар с ID $product_id успешно добавлен в просмотренные",
		),
		201
	);
}

add_action( 'wp_ajax_add_review', 'loveforever_add_review_via_ajax' );
add_action( 'wp_ajax_nopriv_add_review', 'loveforever_add_review_via_ajax' );
function loveforever_add_review_via_ajax() {
	if ( ! isset( $_POST['_submit_review_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_submit_review_form_nonce'] ) ), 'submit_review_form' ) ) {
		wp_send_json_error(
			array( 'message' => 'Ошибка в запросе' ),
			400
		);
	}

	$errors = array();

	$name        = ! empty( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$date        = ! empty( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
	$review_text = ! empty( $_POST['review_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['review_text'] ) ) : '';

	if ( empty( $name ) ) {
		$errors['name'] = 'Пожалуйста, укажите ваше имя';
	}

	if ( empty( $date ) ) {
		$errors['date'] = 'Пожалуйста, укажите дату';
	}

	if ( empty( $review_text ) ) {
		$errors['review_text'] = 'Поле отзыва не может быть пустым';
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error(
			array(
				'message' => 'Заполните необходимые поля',
				'errors'  => $errors,
			),
			400
		);
	}

	$review_post_id = wp_insert_post(
		array(
			'post_type'    => 'review',
			'post_title'   => "Отзыв от $date от $name",
			'post_content' => '<p></p>',
			'post_status'  => 'pending',
		)
	);

	update_field( 'author', $name, $review_post_id );
	update_field( 'review_text', $review_text, $review_post_id );

	if ( ! empty( $_FILES ) && isset( $_FILES['file'] ) && ! empty( $_FILES['file'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		if ( is_array( $_FILES['file']['name'] ) ) {
			$image_array = array();

			$file = array(
				'name'     => $_FILES['file']['name'][0],
				'type'     => $_FILES['file']['type'][0],
				'tmp_name' => $_FILES['file']['tmp_name'][0],
				'error'    => $_FILES['file']['error'][0],
				'size'     => $_FILES['file']['size'][0],
			);

			$_FILES['single_attachment'] = $file;
			$featured_image_id           = media_handle_upload( 'single_attachment', 0 );

			// wp_send_json_success( $featured_image_id );

			if ( $featured_image_id && ! is_wp_error( $featured_image_id ) ) {
				set_post_thumbnail( $review_post_id, $featured_image_id );
				$image_array[] = array( 'image' => $featured_image_id );
			}

			// Handle additional images for carousel
			if ( count( $_FILES['file']['name'] ) > 1 ) {
				for ( $i = 1; $i < count( $_FILES['file']['name'] ); $i++ ) {
					$file = array(
						'name'     => $_FILES['file']['name'][ $i ],
						'type'     => $_FILES['file']['type'][ $i ],
						'tmp_name' => $_FILES['file']['tmp_name'][ $i ],
						'error'    => $_FILES['file']['error'][ $i ],
						'size'     => $_FILES['file']['size'][ $i ],
					);

					$_FILES['single_attachment'] = $file;

					$image_id = media_handle_upload( 'single_attachment', 0 );
					if ( $image_id && ! is_wp_error( $image_id ) ) {
						$image_array[] = array( 'image' => $image_id );
					}
				}

				if ( ! empty( $image_array ) ) {
					update_field( 'image_carousel', $image_array, $review_post_id );
				}
			}
		} else {
			$file = array(
				'name'     => $_FILES['file']['name'][0],
				'type'     => $_FILES['file']['type'][0],
				'tmp_name' => $_FILES['file']['tmp_name'][0],
				'error'    => $_FILES['file']['error'][0],
				'size'     => $_FILES['file']['size'][0],
			);

			$_FILES['single_attachment'] = $file;
			$featured_image_id           = media_handle_upload( 'single_attachment', 0 );
			if ( $featured_image_id && ! is_wp_error( $featured_image_id ) ) {
				set_post_thumbnail( $review_post_id, $featured_image_id );
			}
		}
	}

	wp_send_json_success(
		array(
			'message' => 'Отзыв успешно добавлен! В ближайшее время он будет опубликован',
		),
		201
	);
}

add_action( 'wp_ajax_add_product_to_favorites', 'loveforever_add_product_to_favorites_via_ajax' );
add_action( 'wp_ajax_nopriv_add_product_to_favorites', 'loveforever_add_product_to_favorites_via_ajax' );
function loveforever_add_product_to_favorites_via_ajax() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка добавления товара в избранное! Попробуйте перезагрузить страницу',
				'debug'   => 'Неверный nonce',
			),
			400
		);
	}

	if ( empty( $_POST['product-id'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка добавления товара в избранное! Попробуйте перезагрузить страницу',
				'debug'   => 'Id товара не передан!',
			),
			400
		);
	}

	$product_id           = (int) sanitize_text_field( wp_unslash( $_POST['product-id'] ) );
	$favorite_product_ids = array_map( 'intval', loveforever_get_favorites() );
	$message              = '';

	if ( in_array( $product_id, $favorite_product_ids, true ) ) {
		$favorite_product_ids = array_filter(
			$favorite_product_ids,
			function ( $id ) use ( $product_id ) {
				return $id !== $product_id;
			}
		);
		$message              = 'Товар успешно удален из избранного';
	} else {
		array_unshift( $favorite_product_ids, $product_id );
		$message = 'Товар успешно добавлен в избранное';
	}

	setcookie(
		'favorites',
		implode( ',', $favorite_product_ids ),
		time() + DAY_IN_SECONDS * 30,
		'/',
	);

	wp_send_json_success(
		array(
			'message'        => $message,
			'countFavorites' => count( $favorite_product_ids ),
			'favorites'      => $favorite_product_ids,
		),
		200
	);
}
add_action( 'pre_get_posts', 'loveforever_modify_dress_category_query' );
function loveforever_modify_dress_category_query( $query ) {
	if ( $query->is_tax( 'dress_category' ) ) {
		// $query->set( 'posts_per_page', 3 );
	}

	if ( $query->is_post_type_archive( 'review' ) && ! is_admin() ) {
		$query->set( 'posts_per_page', 3 );
	}
}

function loveforever_breadcrumbs_attribute_filter( $li_attributes, $type, $id ) {
	$pattern               = '/class="([^"]*)"/';
	$breadcrumb_item_class = 'breadcrumbs__item p-12-12 uper';

	if ( preg_match( $pattern, $li_attributes, $matches ) ) {
		$class_list   = array();
		$class_list[] = $breadcrumb_item_class;
		$class_list   = array_merge( $class_list, explode( ' ', $matches[1] ) );
		$class_list   = array_unique( $class_list );

		$new_class_string = implode( ' ', $class_list );

		return preg_replace( $pattern, 'class="' . $new_class_string . '"', $li_attributes );
	}

	return $li_attributes;
}

add_action( 'wp', 'loveforever_track_product_view' );
function loveforever_track_product_view() {
	if ( is_singular( 'dress' ) && empty( $_SERVER['HTTP_X_BARBA'] ) ) {
		$product_id = get_the_ID();
		loveforever_update_viewed_products( $product_id );
	}
}

add_action( 'wp_insert_post', 'loveforever_set_initial_product_views', 10, 2 );
function loveforever_set_initial_product_views( $post_id, $post ) {
	if ( 'dress' === $post->post_type ) {
		$views = get_post_meta( $post_id, 'product_views_count', true );

		if ( empty( $views ) ) {
			update_post_meta( $post_id, 'product_views_count', 0 );
		}
	}
}

add_filter( 'acf/format_value/type=text', 'do_shortcode' );
add_filter( 'acf/format_value/type=textarea', 'do_shortcode' );
