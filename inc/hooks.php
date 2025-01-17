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

	if ( ! isset( $_POST['fitting_type'] ) || empty( $_POST['fitting_type'] ) ) {
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

	$name         = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	$phone        = sanitize_text_field( wp_unslash( $_POST['phone'] ) );
	$fitting_type = is_array( $_POST['fitting_type'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['fitting_type'] ) ) : sanitize_text_field( wp_unslash( $_POST['fitting_type'] ) );
	$date         = sanitize_text_field( wp_unslash( $_POST['date'] ) );
	$time         = sanitize_text_field( wp_unslash( $_POST['time'] ) );

	$is_valid_fitting_time = loveforever_is_valid_fitting_datetime( $date . ' ' . $time, $fitting_type );

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

	update_field( 'fitting_type', $fitting_type, $fitting_post_id );
	update_field( 'fitting_time', $date . ' ' . $time, $fitting_post_id );
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
