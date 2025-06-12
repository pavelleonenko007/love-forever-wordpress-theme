<?php
/**
 * Interface sms provider
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing SMS routing options
 */
class SmsRoute {
	/**
	 * SMS only route
	 */
	public const SMS_ONLY = 'sms';

	/**
	 * Cascade route through multiple channels
	 */
	public const CASCADE = 'vk,viber,sms';

	/**
	 * Get the route value
	 *
	 * @param string $route The route constant
	 * @return string
	 */
	public static function get_value( string $route ): string {
		return $route;
	}
}

/**
 * Interface for SMS providers
 */
interface SmsProviderInterface {
	/**
	 * Send an SMS message
	 *
	 * @param string $phone The recipient's phone number
	 * @param string $text The message text
	 * @param string $route The route to use (SmsRoute constant)
	 * @return bool Whether the message was sent successfully
	 */
	public function send( string $phone, string $text, string $route ): bool;
}
