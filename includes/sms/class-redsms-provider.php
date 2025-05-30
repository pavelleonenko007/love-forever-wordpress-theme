<?php
/**
 *  class-redsms-provider
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;


class RedSmsProvider implements SmsProviderInterface {
	private static $instance = null;
	private $api_url         = 'https://cp.redsms.ru/api/message';
	private $api_key         = 'OFzbDDihLEhzmdqUznhuMmnc';
	private $login           = 'loveforever';
	private $sender          = 'Салон LoveForever';

	private function __construct() {}

	public static function get_instance(): self {
		return self::$instance ??= new self();
	}

	public function send( string $phone, string $text ): bool {
		$ts     = 'ts-value-' . time();
		$secret = md5( $ts . $this->api_key );

		$headers = array(
			'login'        => $this->login,
			'ts'           => $ts,
			'secret'       => $secret,
			'Content-Type' => 'application/json',
		);

		$body = wp_json_encode(
			array(
				'route' => 'viber,sms',
				'from'  => $this->sender,
				'to'    => $phone,
				'text'  => $text,
			)
		);

		$response = wp_remote_post(
			$this->api_url,
			array(
				'headers' => $headers,
				'body'    => $body,
				'timeout' => 50,
			)
		);

		if ( is_wp_error( $response ) ) {
			Logger::log(
				'RedSMS: WP Error при отправке SMS',
				array(
					'phone' => $phone,
					'error' => $response->get_error_message(),
				)
			);
			return false;
		}

		$code      = wp_remote_retrieve_response_code( $response );
		$body      = wp_remote_retrieve_body( $response );
		$body_json = json_decode( $body, true );

		Logger::log(
			'RedSMS: ответ HTTP',
			array(
				'code'  => $code,
				'body'  => $body_json,
				'phone' => $phone,
			)
		);

		if ( 200 !== $code ) {
			return false;
		}

		return true;
	}
}
