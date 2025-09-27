<?php
/**
 * Test XML Feed Generation
 *
 * Simple test script to verify XML feed functionality
 *
 * @package LoveForever
 */

defined( 'ABSPATH' ) || exit;

// Load our XML generator class
require_once __DIR__ . '/class-xml-feed-generator.php';

/**
 * Test XML feed generation
 */
function test_xml_feed_generation() {
	echo "Testing XML Feed Generation...\n";
	
	$generator = XML_Feed_Generator::get_instance();
	
	// Get available categories
	$categories = $generator->get_available_categories();
	
	if ( empty( $categories ) ) {
		echo "No categories found!\n";
		return false;
	}
	
	echo "Found " . count( $categories ) . " allowed categories:\n";
	foreach ( $categories as $category ) {
		echo "  - {$category['name']} ({$category['slug']}) - {$category['count']} products\n";
	}
	
	// Test generating feed for first category
	$first_category = $categories[0];
	echo "\nTesting feed generation for: {$first_category['name']}\n";
	
	$result = $generator->generate_feed( $first_category['slug'] );
	
	if ( is_wp_error( $result ) ) {
		echo "Error: " . $result->get_error_message() . "\n";
		return false;
	}
	
	echo "Success! Feed generated for {$first_category['slug']}\n";
	
	// Check if files were created
	$upload_dir = wp_upload_dir();
	$upload_path = $upload_dir['basedir'] . '/xml/' . $first_category['slug'] . '.xml';
	$public_path = ABSPATH . 'xml/' . $first_category['slug'] . '.xml';
	
	if ( file_exists( $upload_path ) ) {
		$size = size_format( filesize( $upload_path ) );
		echo "Upload file created: {$upload_path} ({$size})\n";
	}
	
	if ( file_exists( $public_path ) ) {
		$size = size_format( filesize( $public_path ) );
		echo "Public file created: {$public_path} ({$size})\n";
		echo "Public URL: " . home_url( '/xml/' . $first_category['slug'] . '.xml' ) . "\n";
	}
	
	return true;
}

// Run test if called directly
if ( php_sapi_name() === 'cli' ) {
	test_xml_feed_generation();
}
