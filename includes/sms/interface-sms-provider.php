<?php
/**
 *  Interface sms provider
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

interface SmsProviderInterface {
	public function send( string $phone, string $text ): bool;
}
