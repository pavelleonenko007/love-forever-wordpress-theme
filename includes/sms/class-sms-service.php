<?php
/**
 *  class-sms-service
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

class SmsService {
	private SmsProviderInterface $provider;

	public function __construct( SmsProviderInterface $provider ) {
		$this->provider = $provider;
	}

	public function send_appointment_sms( int $post_id ): void {
		$timestamp = strtotime( get_field( 'fitting_time', $post_id ) );
		$phone     = get_field( 'phone', $post_id );

		if ( ! $phone || ! $timestamp ) {
			Logger::log( 'SMS: Не удалось получить телефон или дату для записи', compact( 'post_id', 'phone', 'timestamp' ) );
			return;
		}

		$fitting_date_time = date_i18n( 'd.m.Y в H:i', $timestamp );

		$text = "Ждём в LOVE FOREVER – $fitting_date_time на примерку:  м. Садовая, Вознесенский пр-кт 18 (9 мин. от метро). Тел: 8 (931) 341-20-36";

		$success = $this->provider->send( $phone, $text, SmsRoute::CASCADE );
		if ( ! $success ) {
			Logger::log( 'SMS: Не удалось отправить SMS с приглашением', compact( 'post_id', 'phone', 'text' ) );
		} else {
			Logger::log( 'SMS: SMS с приглашением отправлен', compact( 'post_id', 'phone', 'text', 'success' ) );
		}
	}

	public function send_reminder_sms( int $post_id ): void {
		$timestamp = strtotime( get_field( 'fitting_time', $post_id ) );
		$phone     = get_field( 'phone', $post_id );
		if ( ! $phone || ! $timestamp ) {
			return;
		}

		$fitting_date_time = date_i18n( 'd.m.Y в H:i', $timestamp );

		$text = "До Вашей примерки в салоне LOVE FOREVER - м. Садовая осталось 2 часа ($fitting_date_time). Тел. салона: 8 (931) 341-20-36";

		$success = $this->provider->send( $phone, $text, SmsRoute::CASCADE );
		if ( ! $success ) {
			Logger::log( 'SMS: Не удалось отправить SMS с напоминанием', compact( 'post_id', 'phone', 'text' ) );
		}
	}

	public function send_feedback_sms( int $post_id ): void {
		$phone = get_field( 'phone', $post_id );
		if ( ! $phone ) {
			return;
		}

		$text = 'Пожалуйста, оцените ваш визит в LOVE FOREVER – https://clck.ru/WRCeL';

		$success = $this->provider->send( $phone, $text, SmsRoute::SMS_ONLY );

		if ( ! $success ) {
			Logger::log( 'SMS: Не удалось отправить SMS с просьбой об отзыве', compact( 'post_id', 'phone' ) );
		}
	}

	public function send_favorites_sms( string $phone, string $favorites_link ): void {
		if ( empty( $phone ) || empty( $favorites_link ) ) {
			Logger::log( 'SMS: Не удалось отправить SMS с избранным - отсутствует телефон или ссылка', compact( 'phone', 'favorites_link' ) );
			return;
		}

		$text = "Список платьев, которые вы отметили, по ссылке ниже:
🔗 $favorites_link
Пусть выбор будет удобным — команда LOVE FOREVER!";

		$success = $this->provider->send( $phone, $text, SmsRoute::CASCADE );

		if ( ! $success ) {
			Logger::log( 'SMS: Не удалось отправить SMS с избранным', compact( 'phone', 'favorites_link', 'text' ) );
		} else {
			Logger::log( 'SMS: SMS с избранным отправлен', compact( 'phone', 'favorites_link', 'text', 'success' ) );
		}
	}
}
