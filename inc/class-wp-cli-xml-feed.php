<?php
/**
 * WP-CLI Command for XML Feed Generation
 *
 * @package LoveForever
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * XML Feed WP-CLI Commands
 */
class WP_CLI_XML_Feed_Command {
	
	/**
	 * Generate XML feed for specific category
	 *
	 * ## OPTIONS
	 *
	 * <category_slug>
	 * : Category slug to generate feed for
	 *
	 * [--output=<path>]
	 * : Custom output path for XML file
	 *
	 * [--limit=<number>]
	 * : Maximum number of products to include (0 = no limit)
	 *
	 * ## EXAMPLES
	 *
	 *     wp xml-feed generate wedding
	 *     wp xml-feed generate wedding --output=/path/to/custom.xml
	 *     wp xml-feed generate wedding --limit=50
	 *
	 * @param array $args       Positional arguments
	 * @param array $assoc_args Associative arguments
	 */
	public function generate( $args, $assoc_args ) {
		$category_slug = $args[0] ?? '';
		$output_path = $assoc_args['output'] ?? null;
		$limit = isset( $assoc_args['limit'] ) ? intval( $assoc_args['limit'] ) : 0;
		
		if ( empty( $category_slug ) ) {
			WP_CLI::error( 'Category slug is required' );
		}
		
		WP_CLI::log( "Generating XML feed for category: {$category_slug}" );
		if ( $limit > 0 ) {
			WP_CLI::log( "Limiting to {$limit} products" );
		}
		
		$generator = XML_Feed_Generator::get_instance();
		$result = $generator->generate_feed( $category_slug, $output_path, $limit );
		
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
		}
		
		$upload_dir = wp_upload_dir();
		$filename = $category_slug;
		if ( $limit > 0 ) {
			$filename .= '-' . $limit;
		}
		$default_path = $upload_dir['basedir'] . '/xml/' . $filename . '.xml';
		$public_path = ABSPATH . 'xml/' . $filename . '.xml';
		
		WP_CLI::success( "XML feed generated successfully!" );
		WP_CLI::log( "Upload path: {$default_path}" );
		WP_CLI::log( "Public path: {$public_path}" );
		
		// Show file info
		if ( file_exists( $public_path ) ) {
			$file_size = size_format( filesize( $public_path ) );
			WP_CLI::log( "File size: {$file_size}" );
		}
	}
	
	/**
	 * Generate XML feeds for all allowed categories
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Show what would be generated without actually creating files
	 *
	 * [--limit=<number>]
	 * : Maximum number of products per category (0 = no limit)
	 *
	 * ## EXAMPLES
	 *
	 *     wp xml-feed generate-all
	 *     wp xml-feed generate-all --dry-run
	 *     wp xml-feed generate-all --limit=50
	 *
	 * @param array $args       Positional arguments
	 * @param array $assoc_args Associative arguments
	 */
	public function generate_all( $args, $assoc_args ) {
		$dry_run = isset( $assoc_args['dry-run'] );
		$limit = isset( $assoc_args['limit'] ) ? intval( $assoc_args['limit'] ) : 0;
		
		$generator = XML_Feed_Generator::get_instance();
		$categories = $generator->get_available_categories();
		
		if ( empty( $categories ) ) {
			WP_CLI::warning( 'No allowed categories found' );
			return;
		}
		
		WP_CLI::log( sprintf( 'Found %d allowed categories:', count( $categories ) ) );
		
		foreach ( $categories as $category ) {
			WP_CLI::log( sprintf( '  - %s (%s) - %d products', 
				$category['name'], 
				$category['slug'], 
				$category['count'] 
			) );
		}
		
		if ( $dry_run ) {
			WP_CLI::log( 'Dry run mode - no files will be generated' );
			return;
		}
		
		WP_CLI::log( 'Generating feeds for all categories...' );
		if ( $limit > 0 ) {
			WP_CLI::log( "Limiting to {$limit} products per category" );
		}
		
		$results = $generator->generate_all_feeds( $limit );
		$success_count = 0;
		$error_count = 0;
		
		foreach ( $results as $category_slug => $result ) {
			if ( is_wp_error( $result ) ) {
				WP_CLI::warning( "Failed to generate feed for {$category_slug}: " . $result->get_error_message() );
				$error_count++;
			} else {
				WP_CLI::log( "✓ Generated feed for {$category_slug}" );
				$success_count++;
			}
		}
		
		WP_CLI::success( sprintf( 'Generation complete! Success: %d, Errors: %d', $success_count, $error_count ) );
	}
	
	/**
	 * Generate all required feeds (full + limited + combined)
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Show what would be generated without actually creating files
	 *
	 * ## EXAMPLES
	 *
	 *     wp xml-feed generate_required
	 *     wp xml-feed generate_required --dry-run
	 *
	 * @param array $args       Positional arguments
	 * @param array $assoc_args Associative arguments
	 */
	public function generate_required( $args, $assoc_args ) {
		$dry_run = isset( $assoc_args['dry-run'] );
		
		$generator = XML_Feed_Generator::get_instance();
		
		WP_CLI::log( 'Generating all required feeds:' );
		WP_CLI::log( '  - Full feeds: wedding, evening, prom, wedding-sale' );
		WP_CLI::log( '  - Limited feeds: wedding-360, evening-72, prom-96' );
		WP_CLI::log( '  - Combined feed: combined.xml (all limited categories)' );
		
		if ( $dry_run ) {
			WP_CLI::log( 'Dry run mode - no files will be generated' );
			return;
		}
		
		WP_CLI::log( 'Starting generation...' );
		
		$results = $generator->generate_all_required_feeds();
		$success_count = 0;
		$error_count = 0;
		
		foreach ( $results as $feed_name => $result ) {
			if ( is_wp_error( $result ) ) {
				WP_CLI::warning( "Failed to generate {$feed_name}: " . $result->get_error_message() );
				$error_count++;
			} else {
				WP_CLI::log( "✓ Generated {$feed_name}" );
				$success_count++;
			}
		}
		
		WP_CLI::success( sprintf( 'All required feeds generation complete! Success: %d, Errors: %d', $success_count, $error_count ) );
		
			// Show generated files
			WP_CLI::log( '' );
			WP_CLI::log( 'Generated files:' );
			$public_xml_dir = ABSPATH . 'xml/';

			$files = array(
				'wedding.xml', 'evening.xml', 'prom.xml', 'wedding-sale.xml',
				'wedding-360.xml', 'evening-72.xml', 'prom-96.xml', 'combined.xml'
			);

			foreach ( $files as $file ) {
				$public_path = $public_xml_dir . $file;

				if ( file_exists( $public_path ) ) {
					$size = size_format( filesize( $public_path ) );
					WP_CLI::log( "  - {$file} ({$size})" );
				}
			}
	}
	
	/**
	 * List available categories for feed generation
	 *
	 * ## EXAMPLES
	 *
	 *     wp xml-feed list-categories
	 *
	 * @param array $args       Positional arguments
	 * @param array $assoc_args Associative arguments
	 */
	public function list_categories( $args, $assoc_args ) {
		$generator = XML_Feed_Generator::get_instance();
		$categories = $generator->get_available_categories();
		
		if ( empty( $categories ) ) {
			WP_CLI::warning( 'No allowed categories found' );
			return;
		}
		
		WP_CLI::log( sprintf( 'Allowed categories for feed generation (%d):', count( $categories ) ) );
		
		$table_data = array();
		foreach ( $categories as $category ) {
			$table_data[] = array(
				'Slug'    => $category['slug'],
				'Name'    => $category['name'],
				'Products' => $category['count'],
			);
		}
		
		WP_CLI\Utils\format_items( 'table', $table_data, array( 'Slug', 'Name', 'Products' ) );
	}
	
	/**
	 * Clean up generated XML files
	 *
	 * ## OPTIONS
	 *
	 * [--category=<slug>]
	 * : Clean only specific category files
	 *
	 * [--dry-run]
	 * : Show what would be deleted without actually deleting
	 *
	 * ## EXAMPLES
	 *
	 *     wp xml-feed clean
	 *     wp xml-feed clean --category=wedding
	 *     wp xml-feed clean --dry-run
	 *
	 * @param array $args       Positional arguments
	 * @param array $assoc_args Associative arguments
	 */
	public function clean( $args, $assoc_args ) {
		$category_slug = $assoc_args['category'] ?? null;
		$dry_run = isset( $assoc_args['dry-run'] );
		
		$upload_dir = wp_upload_dir();
		$upload_xml_dir = $upload_dir['basedir'] . '/xml/';
		$public_xml_dir = ABSPATH . 'xml/';
		
		$files_to_clean = array();
		
		if ( $category_slug ) {
			// Clean specific category
			$files_to_clean[] = $upload_xml_dir . $category_slug . '.xml';
			$files_to_clean[] = $public_xml_dir . $category_slug . '.xml';
		} else {
			// Clean all XML files
			if ( is_dir( $upload_xml_dir ) ) {
				$upload_files = glob( $upload_xml_dir . '*.xml' );
				$files_to_clean = array_merge( $files_to_clean, $upload_files );
			}
			
			if ( is_dir( $public_xml_dir ) ) {
				$public_files = glob( $public_xml_dir . '*.xml' );
				$files_to_clean = array_merge( $files_to_clean, $public_files );
			}
		}
		
		if ( empty( $files_to_clean ) ) {
			WP_CLI::log( 'No XML files found to clean' );
			return;
		}
		
		WP_CLI::log( sprintf( 'Found %d files to clean:', count( $files_to_clean ) ) );
		
		foreach ( $files_to_clean as $file ) {
			if ( file_exists( $file ) ) {
				$file_size = size_format( filesize( $file ) );
				WP_CLI::log( "  - {$file} ({$file_size})" );
				
				if ( ! $dry_run ) {
					if ( unlink( $file ) ) {
						WP_CLI::log( "    ✓ Deleted" );
					} else {
						WP_CLI::warning( "    ✗ Failed to delete" );
					}
				}
			}
		}
		
		if ( $dry_run ) {
			WP_CLI::log( 'Dry run mode - no files were deleted' );
		} else {
			WP_CLI::success( 'Cleanup completed!' );
		}
	}
	
	/**
	 * Show information about generated XML files
	 *
	 * ## EXAMPLES
	 *
	 *     wp xml-feed status
	 *
	 * @param array $args       Positional arguments
	 * @param array $assoc_args Associative arguments
	 */
	public function status( $args, $assoc_args ) {
		$upload_dir = wp_upload_dir();
		$upload_xml_dir = $upload_dir['basedir'] . '/xml/';
		$public_xml_dir = ABSPATH . 'xml/';
		
		WP_CLI::log( 'XML Feed Status:' );
		WP_CLI::log( '' );
		
		// Check upload directory
		WP_CLI::log( 'Upload Directory: ' . $upload_xml_dir );
		if ( is_dir( $upload_xml_dir ) ) {
			$upload_files = glob( $upload_xml_dir . '*.xml' );
			WP_CLI::log( sprintf( '  Files: %d', count( $upload_files ) ) );
			
			foreach ( $upload_files as $file ) {
				$filename = basename( $file );
				$file_size = size_format( filesize( $file ) );
				$modified = date( 'Y-m-d H:i:s', filemtime( $file ) );
				WP_CLI::log( sprintf( '    - %s (%s, modified: %s)', $filename, $file_size, $modified ) );
			}
		} else {
			WP_CLI::log( '  Directory does not exist' );
		}
		
		WP_CLI::log( '' );
		
		// Check public directory
		WP_CLI::log( 'Public Directory: ' . $public_xml_dir );
		if ( is_dir( $public_xml_dir ) ) {
			$public_files = glob( $public_xml_dir . '*.xml' );
			WP_CLI::log( sprintf( '  Files: %d', count( $public_files ) ) );
			
			foreach ( $public_files as $file ) {
				$filename = basename( $file );
				$file_size = size_format( filesize( $file ) );
				$modified = date( 'Y-m-d H:i:s', filemtime( $file ) );
				WP_CLI::log( sprintf( '    - %s (%s, modified: %s)', $filename, $file_size, $modified ) );
			}
		} else {
			WP_CLI::log( '  Directory does not exist' );
		}
	}
}

// Register WP-CLI command
WP_CLI::add_command( 'xml-feed', 'WP_CLI_XML_Feed_Command' );
