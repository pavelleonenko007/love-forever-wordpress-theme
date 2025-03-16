<?php
/**
 * Constants
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

define( 'TEMPLATE_PATH', get_template_directory_uri() );
define( 'DATA_WF_SITE', '6720c60d7d5faf3e7ea1ac86' );

// Инициализируем константы позже с помощью хука acf/init.
add_action( 'acf/init', 'loveforever_define_acf_constants' );

/**
 * Определяем константы, связанные с ACF, после его полной инициализации
 */
function loveforever_define_acf_constants() {
	// Социальные сети.
	if ( ! defined( 'VK_LINK' ) ) {
		define( 'VK_LINK', get_field( 'vk', 'option' ) );
	}
	if ( ! defined( 'TELEGRAM_LINK' ) ) {
		define( 'TELEGRAM_LINK', get_field( 'telegram', 'option' ) );
	}
	if ( ! defined( 'WHATSAPP_LINK' ) ) {
		define( 'WHATSAPP_LINK', get_field( 'whatsapp', 'option' ) );
	}
	if ( ! defined( 'INSTAGRAM_LINK' ) ) {
		define( 'INSTAGRAM_LINK', get_field( 'instagram', 'option' ) );
	}
	if ( ! defined( 'YOUTUBE_LINK' ) ) {
		define( 'YOUTUBE_LINK', get_field( 'youtube', 'option' ) );
	}

	// Контактная информация.
	if ( ! defined( 'PHONE' ) ) {
		define( 'PHONE', get_field( 'phone', 'option' ) );
	}
	if ( ! defined( 'EMAIL' ) ) {
		define( 'EMAIL', get_field( 'email', 'option' ) );
	}
	if ( ! defined( 'ADDRESS' ) ) {
		define( 'ADDRESS', get_field( 'address', 'option' ) );
	}
	if ( ! defined( 'WORKING_HOURS' ) ) {
		define( 'WORKING_HOURS', get_field( 'working_hours', 'option' ) );
	}
	if ( ! defined( 'MAP_LINK' ) ) {
		define( 'MAP_LINK', get_field( 'map_link', 'option' ) );
	}

	// Ссылка на политику конфиденциальности.
	if ( ! defined( 'PRIVACY_POLICY_LINK' ) ) {
		$privacy_policy = get_field( 'privacy_policy', 'option' );
		define( 'PRIVACY_POLICY_LINK', ! empty( $privacy_policy ) ? $privacy_policy['href'] : get_privacy_policy_url() );
	}
}

/**
 * Создаем функции-обертки для получения значений из ACF
 * Эти функции можно безопасно использовать на ранних стадиях выполнения
 */

/**
 * Получить ссылку на ВКонтакте
 *
 * @return string Ссылка на ВКонтакте или пустая строка
 */
function loveforever_get_vk_link() {
	return defined( 'VK_LINK' ) ? VK_LINK : '';
}

/**
 * Получить ссылку на Telegram
 *
 * @return string Ссылка на Telegram или пустая строка
 */
function loveforever_get_telegram_link() {
	return defined( 'TELEGRAM_LINK' ) ? TELEGRAM_LINK : '';
}

/**
 * Получить ссылку на WhatsApp
 *
 * @return string Ссылка на WhatsApp или пустая строка
 */
function loveforever_get_whatsapp_link() {
	return defined( 'WHATSAPP_LINK' ) ? WHATSAPP_LINK : '';
}

/**
 * Получить ссылку на Instagram
 *
 * @return string Ссылка на Instagram или пустая строка
 */
function loveforever_get_instagram_link() {
	return defined( 'INSTAGRAM_LINK' ) ? INSTAGRAM_LINK : '';
}

/**
 * Получить ссылку на YouTube
 *
 * @return string Ссылка на YouTube или пустая строка
 */
function loveforever_get_youtube_link() {
	return defined( 'YOUTUBE_LINK' ) ? YOUTUBE_LINK : '';
}

/**
 * Получить номер телефона
 *
 * @return string Номер телефона или пустая строка
 */
function loveforever_get_phone() {
	return defined( 'PHONE' ) ? PHONE : '';
}

/**
 * Получить email
 *
 * @return string Email или пустая строка
 */
function loveforever_get_email() {
	return defined( 'EMAIL' ) ? EMAIL : '';
}

/**
 * Получить адрес
 *
 * @return string Адрес или пустая строка
 */
function loveforever_get_address() {
	return defined( 'ADDRESS' ) ? ADDRESS : '';
}

/**
 * Получить рабочие часы
 *
 * @return string Рабочие часы или пустая строка
 */
function loveforever_get_working_hours() {
	return defined( 'WORKING_HOURS' ) ? WORKING_HOURS : '';
}

/**
 * Получить ссылку на карту
 *
 * @return string Ссылка на карту или пустая строка
 */
function loveforever_get_map_link() {
	return defined( 'MAP_LINK' ) ? MAP_LINK : '';
}

/**
 * Получить ссылку на политику конфиденциальности
 *
 * @return string Ссылка на политику конфиденциальности или ссылка по умолчанию
 */
function loveforever_get_privacy_policy_link() {
	return defined( 'PRIVACY_POLICY_LINK' ) ? PRIVACY_POLICY_LINK : get_privacy_policy_url();
}
