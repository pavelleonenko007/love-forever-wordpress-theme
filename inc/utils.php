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
