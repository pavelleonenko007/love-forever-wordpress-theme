<?php
/**
 * Options
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( file_exists( __DIR__ . '/configurator.php' ) ) {
	include_once __DIR__ . '/configurator.php';
}

if ( function_exists( 'acf_add_options_page' ) && current_user_can( 'manage_options' ) ) {
	acf_add_options_page(
		array(
			'page_title'      => __( 'Options', 'wtw-translate' ),
			'menu_title'      => __( 'Options', 'wtw-translate' ),
			'menu_slug'       => 'options',
			'parent_slug'     => 'themes.php',
			'update_button'   => __( 'Update' ),
			'updated_message' => __( 'Item updated.' ),
			'autoload'        => true,
		)
	);
}

if ( function_exists( 'acf_add_options_page' ) && current_user_can( 'manage_options' ) ) {
	acf_add_options_page(
		array(
			'page_title'      => __( 'Site configurator', 'wtw-translate' ),
			'menu_title'      => __( 'Configurator', 'wtw-translate' ),
			'menu_slug'       => 'config',
			'icon_url'        => 'dashicons-screenoptions',
			'parent_slug'     => 'tools.php',
			'update_button'   => __( 'Update' ),
			'updated_message' => __( 'Item updated.' ),
			'autoload'        => true,
		)
	);
}

if ( function_exists( 'acf_add_options_page' ) && current_user_can( 'manage_options' ) ) {
	acf_add_options_page(
		array(
			'page_title'      => 'Страница отзывов',
			'menu_title'      => 'Страница отзывов',
			'menu_slug'       => 'reviews-options-page',
			'parent_slug'     => 'edit.php?post_type=review',
			'update_button'   => 'Обновить',
			'updated_message' => 'Изменения сохранены',
			'autoload'        => true,
		)
	);
}

if ( function_exists( 'acf_add_options_page' ) && current_user_can( 'manage_options' ) ) {
	acf_add_options_page(
		array(
			'page_title'      => 'Шапка сайта',
			'menu_title'      => 'Шапка сайта',
			'menu_slug'       => 'website-header-options',
			// 'parent_slug'     => 'edit.php?post_type=review',
			'update_button'   => 'Обновить',
			'updated_message' => 'Изменения сохранены',
			'autoload'        => true,
		)
	);

	acf_add_options_page(
		array(
			'page_title'      => 'Футер',
			'menu_title'      => 'Футер сайта',
			'menu_slug'       => 'website-footer-options',
			// 'parent_slug'     => 'edit.php?post_type=review',
			'update_button'   => 'Обновить',
			'updated_message' => 'Изменения сохранены',
			'autoload'        => true,
		)
	);
}
