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

// function loveforever_download_and_add_image_to_library( $image_url ) {
// if ( ! function_exists( 'media_handle_sideload' ) ) {
// require_once ABSPATH . 'wp-admin/includes/image.php';
// require_once ABSPATH . 'wp-admin/includes/file.php';
// require_once ABSPATH . 'wp-admin/includes/media.php';
// }

// Загружаем файл во временную директорию
// $tmp = download_url( $image_url );

// if ( is_wp_error( $tmp ) ) {
// return $tmp;
// }

// Устанавливаем переменные для размещения
// $file_array = array(
// 'name'     => basename( $image_url ),
// 'tmp_name' => $tmp,
// );

// $id = media_handle_sideload( $file_array );

// удалим временный файлы
// @unlink( $tmp );

// return $id;
// }

function loveforever_download_and_add_image_to_library( $image_url ) {
	// Проверяем URL
	if ( empty( $image_url ) || ! filter_var( $image_url, FILTER_VALIDATE_URL ) ) {
		return new WP_Error( 400, 'Невалидная ссылка на изображение: ' . $image_url );
	}

	// Получаем имя файла из URL
	$filename = basename( parse_url( $image_url, PHP_URL_PATH ) );

	// Определяем временный путь для файла
	$upload_dir  = wp_upload_dir();
	$upload_path = $upload_dir['path'] . '/' . wp_unique_filename( $upload_dir['path'], $filename );

	// Инициализируем cURL
	$ch = curl_init();

	// Открываем файл для записи
	$fp = @fopen( $upload_path, 'wb' );
	if ( ! $fp ) {
		error_log( 'Cannot open file for writing: ' . $upload_path );
		return new WP_Error( 400, 'Cannot open file for writing: ' . $upload_path );
	}

	// Настраиваем параметры cURL
	curl_setopt( $ch, CURLOPT_URL, $image_url );
	curl_setopt( $ch, CURLOPT_FILE, $fp );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true ); // Следовать редиректам
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); // Не проверять SSL сертификаты
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 300 ); // Увеличенный таймаут для больших файлов
	curl_setopt( $ch, CURLOPT_USERAGENT, 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ) );
	curl_setopt( $ch, CURLOPT_FAILONERROR, true ); // Вернуть ошибку при 4xx кодах ответа

	// Выполняем запрос
	$success = curl_exec( $ch );

	// Проверяем на ошибки
	if ( ! $success ) {
		$error = curl_error( $ch );
		curl_close( $ch );
		fclose( $fp );
		@unlink( $upload_path ); // Удаляем неполный файл
		error_log( 'cURL Error: ' . $error . ' URL: ' . $image_url );
		return new WP_Error( 400, 'cURL Error: ' . $error . ' URL: ' . $image_url );
	}

	// Получаем информацию о загрузке
	$http_code    = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	$content_type = curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );

	// Закрываем cURL сессию и файл
	curl_close( $ch );
	@fclose( $fp );

	// Проверяем HTTP код ответа
	if ( $http_code != 200 ) {
		@unlink( $upload_path );
		error_log( 'HTTP Error: ' . $http_code . ' URL: ' . $image_url );
		return new WP_Error( $http_code, 'HTTP Error: ' . $http_code . ' URL: ' . $image_url );
	}

	// Проверяем размер файла
	$filesize = filesize( $upload_path );
	if ( $filesize <= 0 ) {
		@unlink( $upload_path );
		error_log( 'Zero size file downloaded from: ' . $image_url );
		return new WP_Error( 400, 'Zero size file downloaded from: ' . $image_url );
	}

	$mime_type      = 'image/jpeg';
	$check_filetype = wp_check_filetype( $filename, null );

	if ( empty( $check_filetype['type'] ) ) {
		// Если тип не определен, пробуем определить по MIME-типу из заголовка
		$mime_type = $content_type ?: 'image/jpeg';
	} else {
		$mime_type = $check_filetype['type'];
	}

	// Подготавливаем данные для вставки в библиотеку медиа
	$attachment = array(
		'post_mime_type' => $mime_type,
		'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

	// Вставляем файл в библиотеку медиа
	$attachment_id = wp_insert_attachment( $attachment, $upload_path );

	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $upload_path );
		return $attachment_id;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_path );

	wp_update_attachment_metadata( $attachment_id, $attachment_data );

	remove_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

	return $attachment_id;
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

function loveforever_delete_intermediate_image_sizes( $sizes ) {
	$new_sizes       = array();
	$sizes_to_remove = array(
		'large',
		'medium_large',
		'medium',
		'1536x1536',
		'2048x2048',
	);

	if ( ! is_array( $sizes ) ) {
		return $sizes;
	}

	foreach ( $sizes as $size => $value ) {
		if ( in_array( $size, $sizes_to_remove, true ) ) {
			continue;
		}

		$new_sizes[ $size ] = $value;
	}

	return $new_sizes;
}
