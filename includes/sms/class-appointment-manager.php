<?php
/**
 *  class-appointment-manager
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

class AppointmentManager {
	private SmsService $sms;

	public function __construct( SmsService $sms ) {
		$this->sms = $sms;

		add_action( 'acf/save_post', array( $this, 'handle_fitting_save' ), 10 );
		add_action( 'send_reminder_sms', array( $this, 'handle_reminder_sms' ) );
		add_action( 'send_feedback_sms', array( $this, 'handle_feedback_sms' ) );
	}

	public function handle_fitting_save( $post_id ): void {
		if ( 'fitting' !== get_post_type( $post_id ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		Logger::log( 'Попытка отправки SMS ', compact( 'post_id' ) );

		$timezone = wp_timezone();

		$fitting_datetime = new DateTime( get_field( 'fitting_time', $post_id ), $timezone );
		$timestamp        = $fitting_datetime->getTimestamp();

		$phone = get_field( 'phone', $post_id );
		if ( ! $timestamp || ! $phone ) {
			return;
		}

		$old_timestamp = get_post_meta( $post_id, '_previous_fitting_time', true );
		if ( $old_timestamp && $old_timestamp !== $timestamp ) {
			SmsScheduler::clear_sms_events( $old_timestamp, $post_id );
		}

		update_post_meta( $post_id, '_previous_fitting_time', $timestamp );

		$this->sms->send_appointment_sms( $post_id );

		$current_datetime  = new DateTime( 'now', $timezone );
		$reminder_datetime = clone $fitting_datetime;
		$reminder_datetime->modify( '-2 hours' );

		if ( $reminder_datetime > $current_datetime ) {
			SmsScheduler::schedule_sms( $reminder_datetime->getTimestamp(), 'send_reminder_sms', array( $post_id ) );
		} else {
			Logger::log(
				'SMS: Напоминание не планируется - примерка менее чем через 2 часа',
				array(
					'post_id'      => $post_id,
					'fitting_time' => $fitting_datetime->format( 'd.m.Y H:i' ),
					'current_time' => $current_datetime->format( 'd.m.Y H:i' ),
					'timezone'     => $timezone->getName(),
				)
			);
		}

		$feedback_datetime = clone $fitting_datetime;
		$feedback_datetime->modify( '+2 hours' );
		SmsScheduler::schedule_sms( $feedback_datetime->getTimestamp(), 'send_feedback_sms', array( $post_id ) );
	}

	public function handle_reminder_sms( $post_id ): void {
		// TODO: rewrite send_reminder_sms
		$this->sms->send_reminder_sms( $post_id );
	}

	public function handle_feedback_sms( $post_id ): void {
		$this->sms->send_feedback_sms( $post_id );
	}
}
