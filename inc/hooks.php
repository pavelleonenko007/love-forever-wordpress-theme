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
			array( 'message' => 'Ошибка в запросе' ),
			400
		);
	}

	if ( ! isset( $_POST['name'] ) || empty( $_POST['name'] ) ) {
		wp_send_json_error(
			array( 'message' => 'Пожалуйста, введите ваше имя' ),
			400
		);
	}

	if ( ! isset( $_POST['phone'] ) || empty( $_POST['name'] ) || ! loveforever_is_valid_phone( $_POST['phone'] ) ) {
		wp_send_json_error(
			array( 'message' => 'Пожалуйста, введите корректный номер телефона' ),
			400
		);
	}

	if ( ! isset( $_POST['dress_category'] ) || empty( $_POST['dress_category'] ) ) {
		wp_send_json_error(
			array( 'message' => 'Пожалуйста, укажите тип платья' ),
			400
		);
	}

	if ( ! isset( $_POST['date'] ) || empty( $_POST['date'] ) ) {
		wp_send_json_error(
			array( 'message' => 'Пожалуйста, укажите дату' ),
			400
		);
	}

	if ( ! isset( $_POST['time'] ) || empty( $_POST['time'] ) ) {
		wp_send_json_error(
			array( 'message' => 'Пожалуйста, укажите время' ),
			400
		);
	}

	$name           = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	$phone          = sanitize_text_field( wp_unslash( $_POST['phone'] ) );
	$dress_category = is_array( $_POST['dress_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['dress_category'] ) ) : sanitize_text_field( wp_unslash( $_POST['dress_category'] ) );
	$date           = sanitize_text_field( wp_unslash( $_POST['date'] ) );
	$time           = sanitize_text_field( wp_unslash( $_POST['time'] ) );
	$ip_address     = loveforever_get_client_ip_address();

	$is_valid_fitting_time = loveforever_is_valid_fitting_datetime( $date . ' ' . $time, $dress_category );

	if ( $is_valid_fitting_time !== true ) {
		wp_send_json_error(
			array( 'message' => $is_valid_fitting_time ),
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
			),
			400
		);
	}

	update_field( 'fitting_type', $dress_category, $fitting_post_id );
	update_field( 'fitting_time', $date . ' ' . $time, $fitting_post_id );
	update_field( 'phone', $phone, $fitting_post_id );
	update_field( 'name', $name, $fitting_post_id );
	update_field( 'ip_address', $ip_address, $fitting_post_id );

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
				'feed'       => '<p>Товары с заданными параметрами не найдены</p>',
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

	echo loveforever_get_pagination_html( $products_query, $base_url );

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

add_action( 'pre_get_posts', 'loveforever_modify_dress_category_query' );
function loveforever_modify_dress_category_query( $query ) {
	if ( $query->is_tax( 'dress_category' ) ) {
		// $query->set( 'posts_per_page', 3 );
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
