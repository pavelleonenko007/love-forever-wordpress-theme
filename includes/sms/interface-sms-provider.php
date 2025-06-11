<?php
/**
 *  Interface sms provider
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

enum SmsRoute: string {
	case SMS_ONLY = 'sms';
	case CASCADE  = 'vk,viber,sms';

	public function getValue(): string {
			return $this->value;
	}
}

interface SmsProviderInterface {
	public function send( string $phone, string $text, SmsRoute $route ): bool;
}
