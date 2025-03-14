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
	if ( ! $image_url ) {
		return new WP_Error( 'no_image_url', 'No image URL provided.' );
	}

	$response = wp_remote_get( $image_url );

	// Проверка на ошибки запроса
	if ( is_wp_error( $response ) ) {
			return $response; // Возвращаем объект WP_Error
	}

	// Проверка на код ответа
	if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'invalid_response_code', 'Invalid response code: ' . wp_remote_retrieve_response_code( $response ) );
	}

	// Получаем содержимое изображения
	$image_data = wp_remote_retrieve_body( $response );
	if ( empty( $image_data ) ) {
			return new WP_Error( 'empty_image', 'Получены пустые данные изображения' );
	}

	$upload_dir = wp_upload_dir();
	$filename   = basename( $image_url );

	if ( wp_mkdir_p( $upload_dir['path'] ) ) {
		$file = $upload_dir['path'] . '/' . $filename;
	} else {
		$file = $upload_dir['basedir'] . '/' . $filename;
	}

	$file_written = file_put_contents( $file, $image_data );

	if ( false === $file_written ) {
		return new WP_Error( 'file_save_error', 'Не удалось сохранить изображение' );
	}

	$wp_filetype = wp_check_filetype( $filename, null );

	if ( empty( $wp_filetype['type'] ) ) {
		// Удаляем файл, если его тип не распознан
		@unlink( $file );
		return new WP_Error( 'invalid_filetype', 'Недопустимый тип файла изображения' );
	}

	$attachment  = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title'     => sanitize_file_name( $filename ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attach_id = wp_insert_attachment( $attachment, $file );

	// Проверяем, что attachment создан успешно
	if ( is_wp_error( $attach_id ) ) {
		@unlink( $file ); // Удаляем файл в случае ошибки
		return $attach_id;
	}

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

function loveforever_prepare_barba_container_data_attributes( $attributes ) {
	if ( ! is_array( $attributes ) || empty( $attributes ) ) {
		return '';
	}

	$data_attributes = array_map(
		function ( $key, $value ) {
			return sprintf( 'data-%s="%s"', esc_attr( $key ), esc_attr( $value ) );
		},
		array_keys( $attributes ),
		$attributes
	);

	return implode( ' ', $data_attributes );
}

function loveforever_pink_text_shortcode( $atts, $content = null ) {
	return '<span class="text-pink">' . wp_kses_post( $content ) . '</span>';
}
add_shortcode( 'pink', 'loveforever_pink_text_shortcode' );

function loveforever_get_favorites() {
	$favorites = ! empty( $_COOKIE['favorites'] ) ? explode( ',', $_COOKIE['favorites'] ) : array();
	return $favorites;
}

function loveforever_mask_phone( $phone ) {
	$masked_phone = substr( $phone, 0, 9 ) . '...';
	return $masked_phone;
}

function loveforever_is_user_has_manager_capability() {
	return current_user_can( 'edit_fittings' ) || current_user_can( 'manage_options' );
}

function loveforever_format_special_hours( array $special_hours ) {
	$formatted_slots = array();
	foreach ( $special_hours as $special_hours_item ) {
		$formatted_slots[ $special_hours_item['time'] ] = $special_hours_item['special_fittings_number'];
	}

	return $formatted_slots;
}
