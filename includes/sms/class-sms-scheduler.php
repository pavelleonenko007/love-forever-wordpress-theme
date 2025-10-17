<?php
/**
 *  class-sms-scheduler
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

class SmsScheduler {
	public static function schedule_sms( int $timestamp, string $hook, array $args ): void {
		if ( ! wp_next_scheduled( $hook, $args ) ) {
			wp_schedule_single_event( $timestamp, $hook, $args );
		}
	}

	public static function clear_sms_events( int $timestamp, int $post_id ): void {
		wp_unschedule_event( $timestamp - 2 * HOUR_IN_SECONDS, 'send_reminder_sms', array( $post_id ) );
		wp_unschedule_event( $timestamp + 3 * HOUR_IN_SECONDS, 'send_feedback_sms', array( $post_id ) );
	}
}
