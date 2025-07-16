<?php
/**
 *  class-logger
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;


class Logger {
	public static function log( string $message, $context = array() ): void {
		$log_entry = '[' . wp_date( 'Y-m-d H:i:s' ) . '] ' . $message;

		if ( ! empty( $context ) ) {
			$log_entry .= ' | ' . wp_json_encode( $context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		}

		error_log( $log_entry );
	}
}
