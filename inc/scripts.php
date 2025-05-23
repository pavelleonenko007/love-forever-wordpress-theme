<?php
/**
 * Enqueue scripts
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_enqueue_scripts', 'loveforever_add_admin_scripts' );
function loveforever_add_admin_scripts() {
	wp_enqueue_style( 'admin', TEMPLATE_PATH . '/build/css/admin.css', array(), time() );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_register_script( 'admin_script', TEMPLATE_PATH . '/js/admin.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'admin_script' );
	wp_enqueue_script( 'loveforever-admin', TEMPLATE_PATH . '/build/js/admin.js', array( 'jquery' ), time(), true );
	wp_localize_script(
		'loveforever-admin',
		'LOVE_FOREVER_ADMIN',
		array(
			'NONCE'    => wp_create_nonce( 'loveforever-admin-nonce' ),
			'AJAX_URL' => admin_url( 'admin-ajax.php' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'loveforever_add_site_scripts' );
function loveforever_add_site_scripts() {
	wp_deregister_script( 'jquery-core' );
	wp_register_script( 'jquery-core', '//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', false, false, true );
	wp_enqueue_script( 'jquery' );

	wp_enqueue_style( 'main', TEMPLATE_PATH . '/css/main.css', array(), time() );
	wp_enqueue_style( 'bundle', TEMPLATE_PATH . '/build/css/bundle.css', array( 'main' ), time() );
	wp_enqueue_style( 'custom', TEMPLATE_PATH . '/css/custom.css', array( 'bundle' ), time() );

	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-ui-position' );
	wp_enqueue_script( 'jquery-ui-menu' );
	wp_enqueue_script( 'jquery-ui-slider' );
	wp_enqueue_script( 'jquery-ui-selectmenu' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'maps-yandex', 'https://api-maps.yandex.ru/v3/?apikey=4edbd054-8d5b-4022-81d1-3808d3f13102&lang=ru_RU', array( 'jquery' ), null );
	wp_enqueue_script( 'main', TEMPLATE_PATH . '/js/main.js', array( 'jquery' ), time(), true );
	wp_enqueue_script( 'front', TEMPLATE_PATH . '/js/front.js', array( 'main' ), time(), true );
	// wp_enqueue_script( 'barba', '//thevogne.ru/customfiles/barba.js', array( 'main' ), time(), true );
	// wp_enqueue_script( 'splide', '//cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js', array( 'barba' ), time(), true );
	// wp_enqueue_script( 'thevogne', '//thevogne.ru/clients/gavril/loveforever/scripts.js', array( 'splide' ), time(), true );
	wp_enqueue_script( 'custom', TEMPLATE_PATH . '/js/custom.js', array( 'front' ), time(), true );
	wp_enqueue_script( 'bundle', TEMPLATE_PATH . '/build/js/bundle.js', array( 'custom' ), time(), true );
	wp_localize_script(
		'bundle',
		'LOVE_FOREVER',
		array(
			'AJAX_URL' => admin_url( 'admin-ajax.php' ),
			'NONCE'    => wp_create_nonce( 'loveforever_nonce' ),
		)
	);
}

add_filter( 'wp_default_scripts', 'loveforever_remove_jquery_migrate' );
function loveforever_remove_jquery_migrate( &$scripts ) {
	if ( ! is_admin() ) {
		$scripts->remove( 'jquery' );
		$scripts->add( 'jquery', false, array( 'jquery-core' ), '1.12.4' );
	}
}
