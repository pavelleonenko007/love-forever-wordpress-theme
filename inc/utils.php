<?php
/**
 * Utils
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

function loveforever_get_current_url( $include_params = false ) {
	$protocol = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		$host = $_SERVER['HTTP_HOST'];
		$uri  = $_SERVER['REQUEST_URI'];
	if ( $include_params ) {
			return $protocol . $host . $uri;
	} else {
		$url_without_params = explode( '?', $uri )[0];
		return $protocol . $host . $url_without_params;
	}
}

function loveforever_is_current_url( string $test_url, $include_params = false ) {
	return loveforever_get_current_url( $include_params ) === $test_url;
}

function loveforever_download_and_add_image_to_library( $image_url ) {
	$upload_dir = wp_upload_dir();
	$image_data = file_get_contents( $image_url );
	$filename   = basename( $image_url );

	if ( wp_mkdir_p( $upload_dir['path'] ) ) {
		$file = $upload_dir['path'] . '/' . $filename;
	} else {
		$file = $upload_dir['basedir'] . '/' . $filename;
	}

	file_put_contents( $file, $image_data );

	$wp_filetype = wp_check_filetype( $filename, null );
	$attachment  = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title'     => sanitize_file_name( $filename ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attach_id = wp_insert_attachment( $attachment, $file );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	return $attach_id;
}

/**
 * Форматирует число в формат цены.
 *
 * @param float  $amount Сумма для форматирования.
 * @param int    $decimals Количество десятичных знаков.
 * @param string $decimal_separator Символ для разделения десятичных знаков.
 * @param string $thousands_separator Символ для разделения тысяч.
 * @return string Отформатированная цена.
 */
function loveforever_format_price( $amount, $decimals = 2, $decimal_separator = '.', $thousands_separator = ' ' ) {
	return number_format( $amount, $decimals, $decimal_separator, $thousands_separator );
}

function loveforever_get_client_ip_address() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = current( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
	} elseif ( ! empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
	} else {
			$ip = $_SERVER['REMOTE_ADDR'];
	}

	if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return $ip;
	}

	return '';
}

function loveforever_has_product_in_favorites( $id ) {
	$favorites = ! empty( $_COOKIE['favorites'] ) ? explode( ',', $_COOKIE['favorites'] ) : array();
	return in_array( $id, $favorites );
}
