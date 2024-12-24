<?php
/**
 * Enqueue scripts
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_enqueue_scripts', 'loveforever_add_admin_scripts' );
function loveforever_add_admin_scripts() {
	wp_register_script( 'admin_script', get_template_directory_uri() . '/js/admin.js', array( 'jquery' ), false, true );
	wp_enqueue_script( 'admin_script' );
}

add_action( 'wp_enqueue_scripts', 'loveforever_add_site_scripts' );
function loveforever_add_site_scripts() {
	wp_deregister_script( 'jquery-core' );
	wp_register_script( 'jquery-core', '//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', false, false, true );
	wp_enqueue_script( 'jquery' );

	if ( ! is_admin() ) {
		wp_enqueue_script( 'jquery-ui-core', array( 'jquery' ) );
		wp_enqueue_script(
			'jquery-ui-slider',
			array( 'jquery', 'jquery-ui-core' )
		);
	}

	wp_enqueue_style( 'main', TEMPLATE_PATH . '/css/main.css', array(), time() );
	wp_enqueue_style( 'custom', TEMPLATE_PATH . '/css/custom.css', array( 'main' ), time() );

	wp_enqueue_script( 'custom', TEMPLATE_PATH . '/js/custom.js', array( 'jquery' ), time(), true );
}

add_filter( 'wp_default_scripts', 'loveforever_remove_jquery_migrate' );
function loveforever_remove_jquery_migrate( &$scripts ) {
	if ( ! is_admin() ) {
		$scripts->remove( 'jquery' );
		$scripts->add( 'jquery', false, array( 'jquery-core' ), '1.12.4' );
	}
}
