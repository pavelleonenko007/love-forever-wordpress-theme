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

add_action( 'wp_ajax_toggle_product_favorite', 'loveforever_toggle_product_to_favorite_via_ajax' );
add_action( 'wp_ajax_nopriv_toggle_product_favorite', 'loveforever_toggle_product_to_favorite_via_ajax' );
function loveforever_toggle_product_to_favorite_via_ajax() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка запроса!',
			),
			400
		);
	}

	if ( empty( $_POST['product_id'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Не указан id товара',
			),
			400
		);
	}

	$product_id = sanitize_text_field( wp_unslash( $_POST['product_id'] ) );
	$favorites  = ! empty( $_COOKIE['favorites'] ) ? explode( ',', $_COOKIE['favorites'] ) : array();
	$status     = '';

	if ( in_array( $_POST['product_id'], $favorites ) ) {
		$favorites = array_filter(
			$favorites,
			function ( $id ) use ( $product_id ) {
				return $id !== $product_id;
			}
		);
		$status    = 'inactive';
	} else {
		$favorites[] = $product_id;
		$status      = 'active';
	}

	setcookie( 'favorites', implode( ',', $favorites ) );

	wp_send_json_success(
		array(
			'message' => 'active' === $status ? 'Товар добавлен в избранное!' : 'Товар удален из избранного!',
			'status'  => $status,
		),
		200
	);
}

add_action( 'pre_get_posts', 'loveforever_modify_dress_category_query' );
function loveforever_modify_dress_category_query( $query ) {
	if ( $query->is_tax( 'dress_category' ) ) {
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
	if ( is_singular( 'dress' ) ) {
		$product_id = get_the_ID();
		loveforever_update_recently_viewed_products( $product_id );
	}
}
