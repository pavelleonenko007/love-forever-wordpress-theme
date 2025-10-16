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

// function loveforever_download_and_add_image_to_library( $image_url, $timeout = 300 ) {
// if ( empty( $image_url ) || ! filter_var( $image_url, FILTER_VALIDATE_URL ) ) {
// return new WP_Error( 400, 'Невалидная ссылка на изображение: ' . esc_url_raw( $image_url ) );
// }

// if ( ! function_exists( 'media_handle_sideload' ) ) {
// require_once ABSPATH . 'wp-admin/includes/image.php';
// require_once ABSPATH . 'wp-admin/includes/file.php';
// require_once ABSPATH . 'wp-admin/includes/media.php';
// }

// $temp_file = null;
// $attempt   = 0;
// $retries   = 2;

// Повторяем загрузку при ошибке
// while ( $attempt < $retries ) {
// $temp_file = download_url( $image_url, $timeout );

// if ( ! is_wp_error( $temp_file ) ) {
// break;
// }

// Если ошибка временная — пробуем снова
// $error_msg = $temp_file->get_error_message();
// error_log( '[loveforever_download] Попытка #' . ( $attempt + 1 ) . ' не удалась: ' . $error_msg . ', [ССЫЛКА НА ФАЙЛ]: ' . esc_url_raw( $image_url ) );

// Только при определённых ошибках стоит ретраить
// if ( strpos( $error_msg, 'cURL error 18' ) === false && strpos( $error_msg, 'timed out' ) === false ) {
// break; // Не сетевая ошибка — не ретраим
// }

// ++$attempt;
// sleep( 1 ); // небольшая пауза между попытками
// }

// if ( is_wp_error( $temp_file ) ) {
// return new WP_Error( $temp_file->get_error_code(), 'Ошибка загрузки файла: ' . $temp_file->get_error_message() . ', [ССЫЛКА НА ФАЙЛ]: ' . esc_url_raw( $image_url ) );
// }

// Устанавливаем переменные для размещения
// $file_array = array(
// 'name'     => sanitize_file_name( basename( $image_url ) ),
// 'tmp_name' => $temp_file,
// );

// add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

// $attachment_id = media_handle_sideload( $file_array );

// remove_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

// if ( is_wp_error( $attachment_id ) ) {
// @unlink( $temp_file );
// return new WP_Error( $attachment_id->get_error_code(), 'Ошибка добавления в медиабиблиотеку: ' . $attachment_id->get_error_message() . ', [ССЫЛКА НА ФАЙЛ]: ' . esc_url_raw( $image_url ) );
// }

// return $attachment_id;
// }

function loveforever_download_and_add_image_to_library( $image_url, $timeout = 300 ) {
	// Проверяем URL
	if ( empty( $image_url ) || ! filter_var( $image_url, FILTER_VALIDATE_URL ) ) {
		return new WP_Error( 400, 'Невалидная ссылка на изображение: ' . $image_url );
	}

	$upload_dir = wp_upload_dir();
	if ( ! is_writable( $upload_dir['path'] ) ) {
		return new WP_Error( 'upload_not_writable', 'Каталог загрузки недоступен для записи.' );
	}

	$filename    = sanitize_file_name( basename( parse_url( $image_url, PHP_URL_PATH ) ) );
	$unique_name = wp_unique_filename( $upload_dir['path'], $filename );
	$destination = trailingslashit( $upload_dir['path'] ) . $unique_name;

	$retries  = 2;
	$attempts = 0;
	$success  = false;
	$error    = null;

	while ( $attempts < $retries ) {
		++$attempts;

		$fp = @fopen( $destination, 'wb' );
		if ( ! $fp ) {
			return new WP_Error( 'file_open_error', 'Не удалось создать файл для записи: ' . $destination );
		}

		$ch = curl_init( $image_url );
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_FILE           => $fp,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_TIMEOUT        => $timeout,
				CURLOPT_SSL_VERIFYPEER => true,
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_USERAGENT      => 'WordPress/' . get_bloginfo( 'version' ),
				CURLOPT_FAILONERROR    => true,
			)
		);

		$success    = curl_exec( $ch );
		$curl_error = curl_error( $ch );
		$http_code  = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		curl_close( $ch );
		fclose( $fp );

		if ( $success && $http_code === 200 && filesize( $destination ) > 0 ) {
			break;
		} else {
			@unlink( $destination );
			$error = "Попытка {$attempts}: ошибка cURL — {$curl_error}, HTTP: {$http_code}";
			error_log( '[loveforever_superfast_image_import] ' . $error );
			sleep( 1 ); // небольшая задержка перед повторной попыткой
		}
	}

	$mime_type      = 'image/jpeg';
	$check_filetype = wp_check_filetype( $unique_name, null );

	if ( ! empty( $check_filetype['type'] ) ) {
		// Если тип не определен, пробуем определить по MIME - типу из заголовка
		$mime_type = $check_filetype['type'];
	}

	// Подготавливаем данные для вставки в библиотеку медиа
	$attachment = array(
		'post_mime_type' => $mime_type,
		'post_title'     => preg_replace( '/\.[^.]+$/', '', $unique_name ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

	// Вставляем файл в библиотеку медиа
	$attachment_id = wp_insert_attachment( $attachment, $destination );

	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $destination );
		return $attachment_id;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$attachment_data = wp_generate_attachment_metadata( $attachment_id, $destination );

	wp_update_attachment_metadata( $attachment_id, $attachment_data );

	remove_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

	return $attachment_id;
}

// function loveforever_download_and_add_image_to_library( $image_url ) {
// Проверяем URL
// if ( empty( $image_url ) || ! filter_var( $image_url, FILTER_VALIDATE_URL ) ) {
// return new WP_Error( 400, 'Невалидная ссылка на изображение: ' . $image_url );
// }

// Получаем имя файла из URL
// $filename = basename( parse_url( $image_url, PHP_URL_PATH ) );

// Определяем временный путь для файла
// $upload_dir  = wp_upload_dir();
// $upload_path = $upload_dir['path'] . '/' . wp_unique_filename( $upload_dir['path'], $filename );

// Инициализируем cURL
// $ch = curl_init();

// Открываем файл для записи
// $fp = @fopen( $upload_path, 'wb' );
// if ( ! $fp ) {
// error_log( 'Cannot open file for writing: ' . $upload_path );
// return new WP_Error( 400, 'Cannot open file for writing: ' . $upload_path );
// }

// Настраиваем параметры cURL
// curl_setopt( $ch, CURLOPT_URL, $image_url );
// curl_setopt( $ch, CURLOPT_FILE, $fp );
// curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true ); // Следовать редиректам
// curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); // Не проверять SSL сертификаты
// curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
// curl_setopt( $ch, CURLOPT_TIMEOUT, 300 ); // Увеличенный таймаут для больших файлов
// curl_setopt( $ch, CURLOPT_USERAGENT, 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ) );
// curl_setopt( $ch, CURLOPT_FAILONERROR, true ); // Вернуть ошибку при 4xx кодах ответа

// Выполняем запрос
// $success = curl_exec( $ch );

// Проверяем на ошибки
// if ( ! $success ) {
// $error = curl_error( $ch );
// curl_close( $ch );
// fclose( $fp );
// @unlink( $upload_path ); // Удаляем неполный файл
// error_log( 'cURL Error: ' . $error . ' URL: ' . $image_url );
// return new WP_Error( 400, 'cURL Error: ' . $error . ' URL: ' . $image_url );
// }

// Получаем информацию о загрузке
// $http_code    = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
// $content_type = curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );

// Закрываем cURL сессию и файл
// curl_close( $ch );
// @fclose( $fp );

// Проверяем HTTP код ответа
// if ( $http_code != 200 ) {
// @unlink( $upload_path );
// error_log( 'HTTP Error: ' . $http_code . ' URL: ' . $image_url );
// return new WP_Error( $http_code, 'HTTP Error: ' . $http_code . ' URL: ' . $image_url );
// }

// Проверяем размер файла
// $filesize = filesize( $upload_path );
// if ( $filesize <= 0 ) {
// @unlink( $upload_path );
// error_log( 'Zero size file downloaded from: ' . $image_url );
// return new WP_Error( 400, 'Zero size file downloaded from: ' . $image_url );
// }

// $mime_type      = 'image/jpeg';
// $check_filetype = wp_check_filetype( $filename, null );

// if ( empty( $check_filetype['type'] ) ) {
// Если тип не определен, пробуем определить по MIME-типу из заголовка
// $mime_type = $content_type ?: 'image/jpeg';
// } else {
// $mime_type = $check_filetype['type'];
// }

// Подготавливаем данные для вставки в библиотеку медиа
// $attachment = array(
// 'post_mime_type' => $mime_type,
// 'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
// 'post_content'   => '',
// 'post_status'    => 'inherit',
// );

// add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

// Вставляем файл в библиотеку медиа
// $attachment_id = wp_insert_attachment( $attachment, $upload_path );

// if ( is_wp_error( $attachment_id ) ) {
// @unlink( $upload_path );
// return $attachment_id;
// }

// require_once ABSPATH . 'wp-admin/includes/image.php';
// require_once ABSPATH . 'wp-admin/includes/media.php';

// $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_path );

// wp_update_attachment_metadata( $attachment_id, $attachment_data );

// remove_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );

// return $attachment_id;
// }

/**
 * Форматирует число в формат цены.
 *
 * @param float  $amount Сумма для форматирования.
 * @param int    $decimals Количество десятичных знаков.
 * @param string $decimal_separator Символ для разделения десятичных знаков.
 * @param string $thousands_separator Символ для разделения тысяч.
 * @return string Отформатированная цена.
 */
function loveforever_format_price( $amount, $decimals = 0, $decimal_separator = '.', $thousands_separator = ' ' ) {
	return number_format( $amount, $decimals, $decimal_separator, $thousands_separator ) . ' ₽';
}

/**
 * Get client IP address with fallback for various proxy configurations.
 *
 * @return string Client IP address or 'unknown' if not found.
 */
function loveforever_get_client_ip_address() {
	// Check for IP in various headers (in order of preference)
	$ip_headers = array(
		'HTTP_CF_CONNECTING_IP',     // Cloudflare
		'HTTP_X_FORWARDED_FOR',      // Load balancers/proxies
		'HTTP_X_FORWARDED',          // Proxy
		'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
		'HTTP_FORWARDED_FOR',        // Proxy
		'HTTP_FORWARDED',            // Proxy
		'REMOTE_ADDR',               // Standard
	);

	foreach ( $ip_headers as $header ) {
		if ( ! empty( $_SERVER[ $header ] ) ) {
			$ip = $_SERVER[ $header ];

			// Handle comma-separated IPs (take the first one)
			if ( strpos( $ip, ',' ) !== false ) {
				$ip = trim( explode( ',', $ip )[0] );
			}

			// Validate IP address
			if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
				return $ip;
			}
		}
	}

	// Fallback to REMOTE_ADDR even if it's private
	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	}

	return 'unknown';
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
	return current_user_can( 'edit_fittings' );
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

function loveforever_prepare_tag_attributes_as_string( $attributes ) {
	return array_reduce(
		array_keys( $attributes ),
		function ( $acc, $attr_name ) use ( $attributes ) {
			return $acc . ' ' . $attr_name . '="' . $attributes[ $attr_name ] . '"';
		},
		''
	);
}

function loveforever_prepare_link_attributes( $attributes, $acf_link_data ) {
	if ( ! empty( $acf_link_data ) && is_array( $acf_link_data ) ) {
		$attributes         = array_merge( $attributes, $acf_link_data );
		$attributes['href'] = $acf_link_data['url'];
		unset( $attributes['url'] );
	}

	if ( ! empty( $acf_link_data['target'] ) && '_blank' === $attributes['target'] ) {
		$attributes['rel']        = 'noopener noreferrer';
		$attributes['title']      = $attributes['title'] . ' (открывается в новой вкладке)';
		$attributes['aria-label'] = $attributes['title'];
	}

	$attributes = array_filter( $attributes );

	return loveforever_prepare_tag_attributes_as_string( $attributes );
}

function loveforever_get_socials() {
	$socials = array();
	if ( ! empty( VK_LINK ) ) {
		$socials[] = array(
			'url'        => VK_LINK,
			'icon'       => 'vkIcon',
			'aria-label' => 'Ссылка на группy VK',
		);
	}

	if ( ! empty( TELEGRAM_LINK ) ) {
		$socials[] = array(
			'url'        => TELEGRAM_LINK,
			'icon'       => 'telegramIcon',
			'aria-label' => 'Ссылка на чат Telegram',
		);
	}

	if ( ! empty( TELEGRAM_LINK_2 ) ) {
		$socials[] = array(
			'url'        => TELEGRAM_LINK_2,
			'icon'       => 'telegramIcon',
			'aria-label' => 'Ссылка на чат в Telegram',
		);
	}

	if ( ! empty( WHATSAPP_LINK ) ) {
		$socials[] = array(
			'url'        => WHATSAPP_LINK,
			'icon'       => 'whatsappIcon',
			'aria-label' => 'Ссылка на чат WhatsApp',
		);
	}

	if ( ! empty( INSTAGRAM_LINK ) ) {
		$socials[] = array(
			'url'        => INSTAGRAM_LINK,
			'icon'       => 'instagramIcon',
			'aria-label' => 'Ссылка на аккаунт Instagram',
		);
	}

	return $socials;
}

function loveforever_get_share_buttons( $url = '#', $title = '' ) {
	$share_buttons = array(
		array(
			'url'  => "https://vk.com/share.php?url=$url",
			'icon' => 'vkIcon',
		),
		array(
			'url'  => "https://t.me/share/url?url=$url&text=$title",
			'icon' => 'telegramIcon',
		),
		array(
			'url'  => "https://api.whatsapp.com/send?text=$url",
			'icon' => 'whatsappIcon',
		),
	);

	return $share_buttons;
}

/**
 * Преобразует контент WYSIWYG редактора в аккордеон.
 * H2 заголовки становятся кнопками аккордеона, контент после H2 до следующего H2 - телом аккордеона.
 *
 * @param string $content Контент для преобразования.
 * @return string HTML структура аккордеона.
 */
function loveforever_convert_content_to_accordion( $content ) {
	if ( empty( $content ) ) {
		return $content;
	}

	// Разбиваем контент на части по H2 заголовкам
	$parts = preg_split( '/(<h2[^>]*>.*?<\/h2>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE );

	if ( count( $parts ) < 3 ) {
		// Если нет H2 заголовков, возвращаем исходный контент
		return $content;
	}

	$result          = '';
	$accordion_items = array();

	// Первая часть - контент до первого H2 (если есть)
	$intro_content = trim( $parts[0] );
	if ( ! empty( $intro_content ) ) {
		$result .= $intro_content;
	}

	// Обрабатываем пары заголовок-контент
	for ( $i = 1; $i < count( $parts ); $i += 2 ) {
		if ( isset( $parts[ $i ] ) && isset( $parts[ $i + 1 ] ) ) {
			$header       = trim( $parts[ $i ] );
			$body_content = trim( $parts[ $i + 1 ] );

			if ( ! empty( $header ) && ! empty( $body_content ) ) {
				$accordion_items[] = array(
					'header'  => $header,
					'content' => $body_content,
				);
			}
		}
	}

	// Генерируем HTML аккордеона
	if ( ! empty( $accordion_items ) ) {
		$result .= '<div class="lf-accordion-group">';

		foreach ( $accordion_items as $index => $item ) {
			$result .= '<div class="lf-accordion">';
			$result .= '<details class="lf-accordion__details">';
			$result .= '<summary class="lf-accordion__summary">';
			$result .= '<div class="lf-accordion__title" role="term" aria-details="' . esc_attr( 'accordion-content-' . $index ) . '">';
			$result .= $item['header'];
			$result .= '<svg class="lf-accordion__icon" width="10" height="6" viewbox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 10rem; height: 6rem;">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M4.28598 5.24977L0 0.74993L0.714289 0L5.00027 4.49984L9.28571 0.000560648L10 0.750491L4.99998 6L4.28569 5.25007L4.28598 5.24977Z" fill="black"></path>
			</svg>';
			$result .= '</div>';
			$result .= '</summary>';
			$result .= '</details>';
			$result .= '<div id="' . esc_attr( 'accordion-content-' . $index ) . '" class="lf-accordion__content" role="definition">';
			$result .= '<div class="lf-accordion__content-inner">';
			$result .= '<div class="lf-accordion__content-body flow">';
			$result .= $item['content'];
			$result .= '</div>';
			$result .= '</div>';
			$result .= '</div>';
			$result .= '</div>';
		}

		$result .= '</div>';
	}

	return $result;
}

function loveforever_get_all_brand_names() {
	$brand_names = array(
		'Amee',
		'Anagramma',
		'Anetty',
		'Aria Di Lusso',
		'Armonia',
		'AVE',
		'Belfaso',
		'Dress',
		'Emse',
		'Estelavia',
		'Fata Wedding',
		'Fler',
		'Kookla',
		'Kvinsenta',
		'Kvinsente',
		'Lanesta',
		'LE Salon',
		'Lezardi',
		'LF',
		'Lilovee',
		'Love Bridal',
		'Love Forever',
		'Lula Kavi',
		'Marry Mark',
		'Milva',
		'Mirono\'va',
		'Naviblue Group',
		'Paulain',
		'Penki',
		'Pompeo',
		'Promessa',
		'Rima Lav',
		'S. Markelova',
		'Shine Bridal',
		'Sofoly',
		'Sonya Soley Air',
		'Pompeo',
		'Promessa',
		'Rima Lav',
		'S. Markelova',
		'Shine Bridal',
		'Sofoly',
		'Sonya Soley Air',
		'Tarik Ediz',
		'Tatiana Kaplun',
		'Terani Couture',
		'Twiggy',
		'Unona / Jade',
		'Unona',
		'Veronica',
		'ZU-ZU',
	);

	return $brand_names;
}

/**
 * Удаляет указанные атрибуты (например, itemprop, itemtype и т.п.) из HTML.
 *
 * @param string $html HTML строка.
 * @param array  $attributes Массив атрибутов, которые нужно удалить.
 * @return string HTML без указанных атрибутов.
 */
function loveforever_remove_attributes_from_html( string $html, array $attributes = array() ): string {
	if ( empty( $attributes ) ) {
		return $html;
	}

	// Создаем DOMDocument и отключаем ошибки парсинга (HTML может быть невалидным)
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ) );
	libxml_clear_errors();

	$xpath = new DOMXPath( $dom );

	// Ищем все элементы, у которых есть хотя бы один из указанных атрибутов
	foreach ( $attributes as $attr ) {
		$nodes = $xpath->query( "//*[@$attr]" );
		foreach ( $nodes as $node ) {
			$node->removeAttribute( $attr );
		}
	}

	// Возвращаем чистый HTML (без <html><body>)
	$body      = $dom->getElementsByTagName( 'body' )->item( 0 );
	$innerHTML = '';
	foreach ( $body->childNodes as $child ) {
		$innerHTML .= $dom->saveHTML( $child );
	}

	return trim( $innerHTML );
}
