<?php
/**
 * Setup
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;
add_action( 'after_setup_theme', 'loveforever_setup_theme' );
function loveforever_setup_theme() {
	add_theme_support( 'menus' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'editor-styles' );
}
