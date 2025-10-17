<?php
/**
 * WordPress Cron XML Feed Generator
 *
 * @package LoveForever
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Cron_XML_Feed {

	/**
	 * Hook name for the cron event
	 */
	const CRON_HOOK = 'xml_feed_generation';

	/**
	 * Initialize the cron system
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( self::CRON_HOOK, array( $this, 'generate_feeds' ) );
	}

	/**
	 * Initialize cron on plugin activation
	 */
	public function init() {
		// Schedule cron if not already scheduled
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			// Get saved interval or use default
			$interval = get_option( 'xml_feed_cron_interval', 'xml_feed_interval' );
			wp_schedule_event( time(), $interval, self::CRON_HOOK );
		}
	}

	/**
	 * Add custom cron interval
	 */
	public function add_cron_intervals( $schedules ) {
		$schedules['xml_feed_interval'] = array(
			'interval' => 6 * HOUR_IN_SECONDS, // 6 hours
			'display'  => __( 'Every 6 Hours (XML Feed Generation)' ),
		);

		$schedules['xml_feed_hourly'] = array(
			'interval' => HOUR_IN_SECONDS, // 1 hour
			'display'  => __( 'Every Hour (XML Feed Generation)' ),
		);

		$schedules['xml_feed_5min'] = array(
			'interval' => 5 * MINUTE_IN_SECONDS, // 5 minutes
			'display'  => __( 'Every 5 Minutes (XML Feed Generation)' ),
		);

		$schedules['xml_feed_15min'] = array(
			'interval' => 15 * MINUTE_IN_SECONDS, // 15 minutes
			'display'  => __( 'Every 15 Minutes (XML Feed Generation)' ),
		);

		$schedules['xml_feed_30min'] = array(
			'interval' => 30 * MINUTE_IN_SECONDS, // 30 minutes
			'display'  => __( 'Every 30 Minutes (XML Feed Generation)' ),
		);

		return $schedules;
	}

	/**
	 * Main cron function to generate all feeds
	 */
	public function generate_feeds() {
		// Increase memory limit for this operation
		ini_set( 'memory_limit', '512M' );

		// Log start
		$this->log( 'XML Feed generation started via WP-Cron' );

		try {
			// Get generator instance
			$generator = XML_Feed_Generator::get_instance();

			// Generate feeds one by one to avoid memory issues
			$feeds_to_generate = array(
				// Full feeds
				// array(
				// 'category' => 'wedding',
				// 'limit'    => null,
				// ),
				// array(
				// 'category' => 'evening',
				// 'limit'    => null,
				// ),
				// array(
				// 'category' => 'prom',
				// 'limit'    => null,
				// ),
				// array(
				// 'category' => 'wedding-sale',
				// 'limit'    => null,
				// ),
				// Limited feeds
				// array(
				// 'category' => 'wedding',
				// 'limit'    => 360,
				// ),
				// array(
				// 'category' => 'prom',
				// 'limit'    => 96,
				// ),

				// array(
				// 'category' => 'wedding',
				// 'limit'    => 48,
				// ),
				// array(
				// 'category' => 'evening',
				// 'limit'    => 48,
				// ),
				// array(
				// 'category' => 'prom',
				// 'limit'    => 48,
				// ),
				array(
					'category'    => 'wedding',
					'limit'       => 240,
					'output_path' => ABSPATH . 'xml/yml_wedding.xml',
				),
				array(
					'category'    => 'evening',
					'limit'       => 240,
					'output_path' => ABSPATH . 'xml/yml_evening_2.xml',
				),
				array(
					'category'    => 'prom',
					'limit'       => 240,
					'output_path' => ABSPATH . 'xml/yml_prom_3.xml',
				),
			);

			$success_count = 0;
			$error_count   = 0;
			$errors        = array();

			// Generate individual feeds
			foreach ( $feeds_to_generate as $feed_config ) {
				$category    = $feed_config['category'];
				$limit       = $feed_config['limit'];
				$output_path = $feed_config['output_path'];
				$feed_name   = '';

				if ( $output_path ) {
					$feed_name = basename( $output_path );
				} else {
					$feed_name = $category . ( $limit ? "-{$limit}" : '' );
				}

				$this->log( "Generating feed for {$feed_name}" );

				$result = $generator->generate_feed( $category, $output_path, $limit );

				if ( is_wp_error( $result ) ) {
					$error_message = $result->get_error_message();
					$errors[]      = "{$feed_name}: {$error_message}";
					++$error_count;
					$this->log( "Failed to generate {$feed_name}: {$error_message}", 'error' );
				} else {
					++$success_count;
					$this->log( "Successfully generated {$feed_name}" );
				}

				// Clear memory after each feed
				wp_cache_flush();
				if ( function_exists( 'gc_collect_cycles' ) ) {
					gc_collect_cycles();
				}
			}

			// Generate combined feed
			$this->log( 'Generating combined feed' );
			$combined_result = $generator->generate_combined_feed();

			if ( is_wp_error( $combined_result ) ) {
				$error_message = $combined_result->get_error_message();
				$errors[]      = "combined: {$error_message}";
				++$error_count;
				$this->log( "Failed to generate combined feed: {$error_message}", 'error' );
			} else {
				++$success_count;
				$this->log( 'Successfully generated combined feed' );
			}

			// Log completion
			$message = sprintf(
				'XML Feed generation completed. Success: %d, Errors: %d',
				$success_count,
				$error_count
			);
			$this->log( $message );

			// Store results in options for admin display
			update_option(
				'xml_feed_last_run',
				array(
					'timestamp'     => current_time( 'mysql' ),
					'success_count' => $success_count,
					'error_count'   => $error_count,
					'errors'        => $errors,
					'status'        => $error_count > 0 ? 'partial' : 'success',
				)
			);

		} catch ( Exception $e ) {
			$error_message = 'Exception during XML feed generation: ' . $e->getMessage();
			$this->log( $error_message, 'error' );

			update_option(
				'xml_feed_last_run',
				array(
					'timestamp'     => current_time( 'mysql' ),
					'success_count' => 0,
					'error_count'   => 1,
					'errors'        => array( $error_message ),
					'status'        => 'error',
				)
			);
		}
	}


	/**
	 * Get cron status information
	 */
	public function get_cron_status() {
		$next_run = wp_next_scheduled( self::CRON_HOOK );
		$last_run = get_option( 'xml_feed_last_run', array() );

		return array(
			'is_scheduled'      => $next_run !== false,
			'next_run'          => $next_run ? date( 'Y-m-d H:i:s', $next_run ) : null,
			'next_run_relative' => $next_run ? human_time_diff( $next_run ) : null,
			'last_run'          => $last_run,
			'cron_disabled'     => defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON,
		);
	}

	/**
	 * Logging function
	 */
	private function log( $message, $level = 'info' ) {
		$log_file  = ABSPATH . 'xml-feed-cron.log';
		$timestamp = current_time( 'Y-m-d H:i:s' );
		$log_entry = "[{$timestamp}] [{$level}] {$message}\n";

		file_put_contents( $log_file, $log_entry, FILE_APPEND | LOCK_EX );

		// Also log to WordPress debug log if enabled
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( "XML Feed Cron: {$message}" );
		}
	}

	/**
	 * Unschedule cron on deactivation
	 */
	public function unschedule_cron() {
		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
		}
	}

	/**
	 * Reschedule cron with new interval
	 */
	public function reschedule_cron( $interval = 'xml_feed_interval' ) {
		// Unschedule current
		$this->unschedule_cron();

		// Schedule new
		wp_schedule_event( time(), $interval, self::CRON_HOOK );
	}
}

// Initialize the cron system
new WP_Cron_XML_Feed();

// Add custom cron intervals
add_filter( 'cron_schedules', array( new WP_Cron_XML_Feed(), 'add_cron_intervals' ) );
