<?php
/**
 * Dress Importer
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Clean product name by removing common prefixes
 *
 * @param string $name Raw product name from XML.
 * @return string Cleaned product name.
 */
function loveforever_clean_product_name( $name ) {
	// List of prefixes to remove
	$prefixes_to_remove = array(
		'Свадебное платье ',
		'Вечернее платье ',
		'Платье на выпускной ',
		'Платье ',
		'Аксессуар ',
	);

	// Remove the prefixes
	foreach ( $prefixes_to_remove as $prefix ) {
		if ( strpos( $name, $prefix ) === 0 ) {
			return trim( str_replace( $prefix, '', $name ) );
		}
	}

	return $name;
}

/**
 * Extract slug from URL
 *
 * @param string $url URL from XML.
 * @return string Extracted slug.
 */
function loveforever_extract_slug_from_url( $url ) {
	// Remove trailing slash if exists
	$url = rtrim( $url, '/' );
	
	// Get the last part of the URL
	$parts = explode( '/', $url );
	$slug = end( $parts );
	
	return $slug;
}

/**
 * Initialize import progress tracking
 *
 * @param int $total_items Total number of items to import.
 * @return void
 */
function loveforever_init_import_progress( $total_items ) {
	$progress = array(
		'total'      => $total_items,
		'imported'   => 0,
		'percentage' => 0,
		'status'     => 'in_progress',
		'started_at' => time(),
	);
	
	set_transient( 'loveforever_import_progress', $progress, 24 * HOUR_IN_SECONDS );
}

/**
 * Update import progress
 *
 * @param int $imported_count Current number of imported items.
 * @param bool $is_complete Whether the import is complete.
 * @return array Updated progress data
 */
function loveforever_update_import_progress( $imported_count, $is_complete = false ) {
	$progress = get_transient( 'loveforever_import_progress' );
	
	if ( ! $progress ) {
		return false;
	}
	
	$progress['imported'] = $imported_count;
	$progress['percentage'] = round( ( $imported_count / $progress['total'] ) * 100 );
	
	if ( $is_complete ) {
		$progress['status'] = 'completed';
		$progress['completed_at'] = time();
	}
	
	set_transient( 'loveforever_import_progress', $progress, 24 * HOUR_IN_SECONDS );
	
	return $progress;
}

/**
 * Get current import progress
 *
 * @return array|bool Progress data or false if no import in progress
 */
function loveforever_get_import_progress() {
	return get_transient( 'loveforever_import_progress' );
}

/**
 * Import dresses from XML file
 *
 * @return int Number of imported dresses
 */
function loveforever_import_dresses() {
	$xml = simplexml_load_file( get_template_directory() . '/loveforever.xml' );
	$total_items = count( $xml->shop->offers->offer );
	
	// Initialize progress tracking
	loveforever_init_import_progress( $total_items );
	
	$imported_count = 0;

	foreach ( $xml->shop->offers->offer as $offer ) {
		// Clean the product name
		$product_name = loveforever_clean_product_name( (string) $offer->name );
		
		// Extract slug from URL
		$post_slug = '';
		if ( isset( $offer->url ) && ! empty( (string) $offer->url ) ) {
			$post_slug = loveforever_extract_slug_from_url( (string) $offer->url );
		}

		$post_args = array(
			'post_title'  => $product_name,
				'post_type'   => 'dress',
				'post_status' => 'publish',
		);

		if ( ! empty( $post_slug ) ) {
			$post_args['post_name'] = $post_slug;
		}
		
		$post_id = wp_insert_post( $post_args );

		if ( $post_id ) {
			$category_slug = (string) $offer->collectionId;
			$term          = get_term_by( 'slug', $category_slug, 'dress_category' );
			if ( $term ) {
				wp_set_object_terms( $post_id, $term->term_id, 'dress_category' );
			}

			// Check if oldprice exists
			if (isset($offer->oldprice) && !empty((string) $offer->oldprice)) {
				// Set has_discount to true
				update_field('has_discount', true, $post_id);
				
				// Set price_with_discount to the value of price from XML
				update_field('price_with_discount', (string) $offer->price, $post_id);
				
				// Set price to the value of oldprice from XML
				update_field('price', (string) $offer->oldprice, $post_id);
			} else {
				// If no oldprice, set has_discount to false and use regular price
				update_field('has_discount', false, $post_id);
				update_field('price', (string) $offer->price, $post_id);
			}
			
			update_post_meta( $post_id, 'final_price', (string) $offer->price );

			$images = $offer->picture;
			if ( count( $images ) > 0 ) {
				$featured_image_id = loveforever_download_and_add_image_to_library( (string) $images[0] );
				set_post_thumbnail( $post_id, $featured_image_id );

				if ( count( $images ) > 1 ) {
					$image_array = array();
					for ( $i = 1; $i < count( $images ); $i++ ) {
						$image_id      = loveforever_download_and_add_image_to_library( (string) $images[ $i ] );
						$image_array[] = array( 'image' => $image_id );
					}
					update_field( 'images', $image_array, $post_id );
				}
			}

			++$imported_count;
			
			// Update progress after each item
			loveforever_update_import_progress( $imported_count );
		}
	}
	
	// Mark import as complete
	loveforever_update_import_progress( $imported_count, true );

	return $imported_count;
}

/**
 * Registers a new REST API route for importing dresses.
 *
 * The route is `/dress-importer/v1/import` and accepts the `POST` method.
 * It calls the `loveforever_dress_importer_callback` function when invoked.
 *
 * @since 0.0.1
 */
function loveforever_register_dress_importer_route() {
	register_rest_route(
		'dress-importer/v1',
		'/import',
		array(
			'methods'             => 'POST',
			'callback'            => 'loveforever_dress_importer_callback',
			'permission_callback' => '__return_true',
		)
	);
	
	// Register progress check endpoint
	register_rest_route(
		'dress-importer/v1',
		'/progress',
		array(
			'methods'             => 'GET',
			'callback'            => 'loveforever_dress_importer_progress_callback',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'loveforever_register_dress_importer_route' );

/**
 * Callback функция для REST API endpoint.
 *
 * @since 0.0.1
 */
function loveforever_dress_importer_callback() {
	// Reset progress before starting a new import
	delete_transient( 'loveforever_import_progress' );
	
	$imported_count = loveforever_import_dresses();
	return new WP_REST_Response(
		array(
			'message' => "Импорт завершен. Импортировано платьев: $imported_count",
			'progress' => loveforever_get_import_progress(),
		),
		201,
		array( 'Content-Type' => 'application/json' )
	);
}

/**
 * Callback function for progress check endpoint.
 *
 * @return WP_REST_Response
 */
function loveforever_dress_importer_progress_callback() {
	$progress = loveforever_get_import_progress();
	
	if ( ! $progress ) {
		return new WP_REST_Response(
			array(
				'message' => 'Нет активного процесса импорта',
				'progress' => null,
			),
			200,
			array( 'Content-Type' => 'application/json' )
		);
	}
	
	return new WP_REST_Response(
		array(
			'message' => 'Прогресс импорта',
			'progress' => $progress,
		),
		200,
		array( 'Content-Type' => 'application/json' )
	);
}
