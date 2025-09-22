<?php
/**
 * Schema Settings
 * Настройки для JSON Schema разметки
 *
 * @package loveforever
 */

defined( 'ABSPATH' ) || exit;

/**
 * Добавляет настройки адреса организации в админку WordPress
 */
function loveforever_add_organization_address_settings() {
	// Добавляем секцию настроек
	add_settings_section(
		'loveforever_organization_schema',
		'Настройки адреса организации для Schema',
		'loveforever_organization_schema_section_callback',
		'general'
	);

	// Поля для адреса.
	$address_fields = array(
		'organization_street_address' => 'Улица и дом',
		'organization_locality'       => 'Город',
		'organization_postal_code'    => 'Почтовый индекс',
		'organization_region'         => 'Регион/Область',
		'organization_country'        => 'Страна (код)',
	);

	foreach ( $address_fields as $field_name => $field_label ) {
		add_settings_field(
			$field_name,
			$field_label,
			'loveforever_organization_address_field_callback',
			'general',
			'loveforever_organization_schema',
			array( 'field_name' => $field_name )
		);

		register_setting( 'general', $field_name, 'sanitize_text_field' );
	}
}

/**
 * Callback для секции настроек
 */
function loveforever_organization_schema_section_callback() {
	echo '<p>Настройте адрес организации для корректного отображения в JSON Schema разметке.</p>';
}

/**
 * Callback для полей адреса.
 *
 * @param array $args Аргументы поля.
 */
function loveforever_organization_address_field_callback( $args ) {
	$field_name = $args['field_name'];
	$value      = get_option( $field_name, '' );

	// Значения по умолчанию.
	$defaults = array(
		'organization_street_address' => 'ул. Примерная, д. 123',
		'organization_locality'       => 'Москва',
		'organization_postal_code'    => '123456',
		'organization_region'         => 'Московская область',
		'organization_country'        => 'RU',
	);

	$placeholder = isset( $defaults[ $field_name ] ) ? $defaults[ $field_name ] : '';

	printf(
		'<input type="text" name="%s" value="%s" placeholder="%s" class="regular-text" />',
		esc_attr( $field_name ),
		esc_attr( $value ),
		esc_attr( $placeholder )
	);
}

// Подключаем настройки только в админке.
if ( is_admin() ) {
	add_action( 'admin_init', 'loveforever_add_organization_address_settings' );
}

/**
 * Получает настройки адреса организации с fallback на значения по умолчанию.
 *
 * @return array Массив с настройками адреса.
 */
function loveforever_get_organization_address_settings() {
	$settings = array(
		'street_address' => get_option( 'organization_street_address', 'ул. Примерная, д. 123' ),
		'locality'       => get_option( 'organization_locality', 'Москва' ),
		'postal_code'    => get_option( 'organization_postal_code', '123456' ),
		'region'         => get_option( 'organization_region', 'Московская область' ),
		'country'        => get_option( 'organization_country', 'RU' ),
	);

	return $settings;
}
