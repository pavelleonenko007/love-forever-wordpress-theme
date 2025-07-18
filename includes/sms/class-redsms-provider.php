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
	private $sender          = 'LoveForever';

	private function __construct() {}

	public static function get_instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Send an SMS message
	 *
	 * @param string $phone The recipient's phone number
	 * @param string $text The message text
	 * @param string $route The route to use (SmsRoute constant)
	 * @return bool Whether the message was sent successfully
	 */
	public function send( string $phone, string $text, string $route = SmsRoute::CASCADE ): bool {
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
				'route' => $route,
				'from'  => $this->sender,
				'to'    => str_replace( '+', '', $phone ),
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
