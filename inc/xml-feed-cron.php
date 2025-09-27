<?php
/**
 * Standalone XML Feed Generator for Cron
 *
 * This script can be run independently via system cron
 * Usage: php xml-feed-cron.php [category_slug] [--all]
 *
 * @package LoveForever
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	// Set WordPress root path
	$wp_root = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
	
	// Load WordPress
	require_once $wp_root . '/wp-load.php';
}

// Ensure we have WordPress loaded
if ( ! function_exists( 'get_posts' ) ) {
	die( "Error: WordPress not loaded properly\n" );
}

// Load our XML generator class
require_once __DIR__ . '/class-xml-feed-generator.php';

/**
 * Logging function for cron script
 *
 * @param string $message Log message
 * @param string $level   Log level (info, warning, error)
 */
function xml_feed_cron_log( $message, $level = 'info' ) {
	$timestamp = date( 'Y-m-d H:i:s' );
	$log_message = "[{$timestamp}] [{$level}] {$message}\n";
	
	// Output to console
	echo $log_message;
	
	// Also log to file
	$log_file = WP_CONTENT_DIR . '/xml-feed-cron.log';
	file_put_contents( $log_file, $log_message, FILE_APPEND | LOCK_EX );
}

/**
 * Main cron execution function
 */
function xml_feed_cron_main() {
	global $argv;
	
	xml_feed_cron_log( 'XML Feed Cron started' );
	
	// Parse command line arguments
	$category_slug = $argv[1] ?? '';
	$generate_all = in_array( '--all', $argv );
	$generate_required = in_array( '--required', $argv );
	$limit = 0;
	
	// Parse limit argument
	foreach ( $argv as $arg ) {
		if ( strpos( $arg, '--limit=' ) === 0 ) {
			$limit = intval( substr( $arg, 8 ) );
		}
	}
	
	$generator = XML_Feed_Generator::get_instance();
	
	try {
		if ( $generate_required ) {
			xml_feed_cron_log( 'Generating all required feeds (full + limited + combined)' );
			
			$results = $generator->generate_all_required_feeds();
			$success_count = 0;
			$error_count = 0;
			
			foreach ( $results as $feed_name => $result ) {
				if ( is_wp_error( $result ) ) {
					xml_feed_cron_log( "Failed to generate {$feed_name}: " . $result->get_error_message(), 'error' );
					$error_count++;
				} else {
					xml_feed_cron_log( "Successfully generated {$feed_name}" );
					$success_count++;
				}
			}
			
			xml_feed_cron_log( sprintf( 'All required feeds generation complete. Success: %d, Errors: %d', $success_count, $error_count ) );
		
		// Show generated files
		xml_feed_cron_log( 'Generated files:' );
		$public_xml_dir = ABSPATH . 'xml/';
		$files = array(
			'wedding.xml', 'evening.xml', 'prom.xml', 'wedding-sale.xml',
			'wedding-360.xml', 'evening-72.xml', 'prom-96.xml', 'combined.xml'
		);
		
		foreach ( $files as $file ) {
			$public_path = $public_xml_dir . $file;
			if ( file_exists( $public_path ) ) {
				$size = size_format( filesize( $public_path ) );
				xml_feed_cron_log( "  - {$file} ({$size})" );
			}
		}
			
		} elseif ( $generate_all ) {
			xml_feed_cron_log( 'Generating feeds for all allowed categories' );
			if ( $limit > 0 ) {
				xml_feed_cron_log( sprintf( 'Limiting to %d products per category', $limit ) );
			}
			
			$categories = $generator->get_available_categories();
			xml_feed_cron_log( sprintf( 'Found %d allowed categories', count( $categories ) ) );
			
			$results = $generator->generate_all_feeds( $limit );
			$success_count = 0;
			$error_count = 0;
			
			foreach ( $results as $cat_slug => $result ) {
				if ( is_wp_error( $result ) ) {
					xml_feed_cron_log( "Failed to generate feed for {$cat_slug}: " . $result->get_error_message(), 'error' );
					$error_count++;
				} else {
					xml_feed_cron_log( "Successfully generated feed for {$cat_slug}" );
					$success_count++;
				}
			}
			
			xml_feed_cron_log( sprintf( 'Generation complete. Success: %d, Errors: %d', $success_count, $error_count ) );
			
		} elseif ( ! empty( $category_slug ) ) {
			xml_feed_cron_log( "Generating feed for category: {$category_slug}" );
			if ( $limit > 0 ) {
				xml_feed_cron_log( sprintf( 'Limiting to %d products', $limit ) );
			}
			
			$result = $generator->generate_feed( $category_slug, null, $limit );
			
			if ( is_wp_error( $result ) ) {
				xml_feed_cron_log( 'Failed to generate feed: ' . $result->get_error_message(), 'error' );
				exit( 1 );
			}
			
			xml_feed_cron_log( "Successfully generated feed for {$category_slug}" );
			
		} else {
			xml_feed_cron_log( 'Usage: php xml-feed-cron.php [category_slug] [--all] [--required] [--limit=N]', 'error' );
			xml_feed_cron_log( 'Examples:', 'error' );
			xml_feed_cron_log( '  php xml-feed-cron.php wedding', 'error' );
			xml_feed_cron_log( '  php xml-feed-cron.php --all', 'error' );
			xml_feed_cron_log( '  php xml-feed-cron.php --required', 'error' );
			xml_feed_cron_log( '  php xml-feed-cron.php wedding --limit=50', 'error' );
			xml_feed_cron_log( '  php xml-feed-cron.php --all --limit=100', 'error' );
			exit( 1 );
		}
		
	} catch ( Exception $e ) {
		xml_feed_cron_log( 'Fatal error: ' . $e->getMessage(), 'error' );
		exit( 1 );
	}
	
	xml_feed_cron_log( 'XML Feed Cron completed successfully' );
}

/**
 * Validate environment before running
 */
function xml_feed_cron_validate_environment() {
	// Check if required functions exist
	if ( ! function_exists( 'get_posts' ) ) {
		xml_feed_cron_log( 'WordPress functions not available', 'error' );
		return false;
	}
	
	// Check if our generator class exists
	if ( ! class_exists( 'XML_Feed_Generator' ) ) {
		xml_feed_cron_log( 'XML_Feed_Generator class not found', 'error' );
		return false;
	}
	
	// Check if dress post type exists
	if ( ! post_type_exists( 'dress' ) ) {
		xml_feed_cron_log( 'Dress post type not found', 'error' );
		return false;
	}
	
	// Check if dress_category taxonomy exists
	if ( ! taxonomy_exists( 'dress_category' ) ) {
		xml_feed_cron_log( 'Dress category taxonomy not found', 'error' );
		return false;
	}
	
	return true;
}

// Run the cron job
if ( ! xml_feed_cron_validate_environment() ) {
	exit( 1 );
}

xml_feed_cron_main();
