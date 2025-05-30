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

		add_action( 'save_post_fitting', array( $this, 'handle_fitting_save' ), 10, 3 );
		add_action( 'send_reminder_sms', array( $this, 'handle_reminder_sms' ) );
		add_action( 'send_feedback_sms', array( $this, 'handle_feedback_sms' ) );
	}

	public function handle_fitting_save( $post_id, $post, $update ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$timestamp = strtotime( get_field( 'fitting_time', $post_id ) );
		$phone     = get_field( 'phone', $post_id );
		if ( ! $timestamp || ! $phone ) {
			return;
		}

		$old_timestamp = get_post_meta( $post_id, '_previous_fitting_time', true );
		if ( $old_timestamp && $old_timestamp !== $timestamp ) {
			SmsScheduler::clear_sms_events( $old_timestamp, $post_id );
		}

		update_post_meta( $post_id, '_previous_date', $timestamp );

		$this->sms->send_appointment_sms( $post_id );
		SmsScheduler::schedule_sms( $timestamp - 2 * HOUR_IN_SECONDS, 'send_reminder_sms', array( $post_id ) );
		SmsScheduler::schedule_sms( $timestamp + 2 * HOUR_IN_SECONDS, 'send_feedback_sms', array( $post_id ) );
	}

	public function handle_reminder_sms( $post_id ): void {
		//TODO: rewrite send_reminder_sms
		$this->sms->send_appointment_sms( $post_id );
	}

	public function handle_feedback_sms( $post_id ): void {
		$this->sms->send_feedback_sms( $post_id );
	}
}
