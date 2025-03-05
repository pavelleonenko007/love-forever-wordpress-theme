<?php
/**
 * Dress Categories Importer
 *
 * @package Love Forever
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'loveforever/v1',
			'/import-dress-categories',
			array(
				'methods'             => 'GET',
				// 'permission_callback' => function () {
				// 	return current_user_can( 'manage_options' );
				// },
				'callback'            => 'loveforever_import_dress_categories',
				// 'callback'            => '__return_false',
			)
		);
	}
);

function loveforever_import_dress_categories() {
	$csv_file = get_template_directory() . '/loveforever-dress-categories.csv';

	if ( ! file_exists( $csv_file ) ) {
		return new WP_Error( 'csv_missing', 'CSV file not found', array( 'status' => 404 ) );
	}

	$file     = fopen( $csv_file, 'r' );
	$headers  = fgetcsv( $file ); // Skip headers
	$imported = array();
	$errors   = array();

	while ( ( $data = fgetcsv( $file ) ) !== false ) {
		$url             = $data[0];
		$name            = $data[1];
		$seo_title       = $data[2];
		$seo_description = $data[3];

		// Extract slug from URL
		$path  = parse_url( $url, PHP_URL_PATH );
		$parts = array_values( array_filter( explode( '/', $path ) ) );

		// Remove 'dresses' from the beginning
		if ( $parts[0] === 'dresses' ) {
			array_shift( $parts );
		}

		// Process hierarchical categories
		$parent_id = 0;
		foreach ( $parts as $part ) {
			$term = get_term_by( 'slug', $part, 'dress_category' );

			if ( ! $term ) {
				$result = wp_insert_term(
					$name, // Use the H1 as the name for the last part, otherwise use slug
					'dress_category',
					array(
						'slug'        => $part,
						'parent'      => $parent_id,
						'description' => $seo_description, // description from CSV
					)
				);

				if ( is_wp_error( $result ) ) {
					$errors[] = array(
						'slug'  => $part,
						'error' => $result->get_error_message(),
					);
					continue;
				}

				$term_id = $result['term_id'];

				// Add Yoast SEO metadata only for the final term in hierarchy
				if ( $part === end( $parts ) ) {
					// Set Yoast SEO title
					// update_term_meta( $term_id, '_yoast_wpseo_title', $seo_title );
					// // Set Yoast SEO description
					// update_term_meta( $term_id, '_yoast_wpseo_metadesc', $seo_description );
					// // Enable Yoast SEO for this term
					// update_term_meta( $term_id, '_yoast_wpseo_opengraph-title', $seo_title );
					// update_term_meta( $term_id, '_yoast_wpseo_opengraph-description', $seo_description );

					WPSEO_Taxonomy_Meta::set_values(
						$term_id,
						'dress_category',
						array(
							'wpseo_title'                 => $seo_title,
							'wpseo_desc'                  => $seo_description,
							'wpseo_opengraph-title'       => $seo_title,
							'wpseo_opengraph-description' => $seo_description,
							'wpseo_twitter-title'         => $seo_title,
							'wpseo_twitter-description'   => $seo_description,
						)
					);

					WPSEO_Options::clear_cache();
				}

				$imported[] = array(
					'slug'            => $part,
					'name'            => ( $part === end( $parts ) ) ? $name : $part,
					'parent'          => $parent_id,
					'seo_title'       => ( $part === end( $parts ) ) ? $seo_title : '',
					'seo_description' => ( $part === end( $parts ) ) ? $seo_description : '',
				);
			} else {
				$term_id = $term->term_id;

				// Update Yoast SEO metadata for existing terms if it's the final term
				if ( $part === end( $parts ) ) {
					// update_term_meta( $term_id, '_yoast_wpseo_title', $seo_title );
					// update_term_meta( $term_id, '_yoast_wpseo_metadesc', $seo_description );
					// update_term_meta( $term_id, '_yoast_wpseo_opengraph-title', $seo_title );
					// update_term_meta( $term_id, '_yoast_wpseo_opengraph-description', $seo_description );

					WPSEO_Taxonomy_Meta::set_values(
						$term_id,
						'dress_category',
						array(
							'wpseo_title'                 => $seo_title,
							'wpseo_desc'                  => $seo_description,
							'wpseo_opengraph-title'       => $seo_title,
							'wpseo_opengraph-description' => $seo_description,
							'wpseo_twitter-title'         => $seo_title,
							'wpseo_twitter-description'   => $seo_description,
						)
					);

					WPSEO_Options::clear_cache();
				}
			}

			$parent_id = $term_id;
		}
	}

	fclose( $file );

	return array(
		'success'  => true,
		'imported' => $imported,
		'errors'   => $errors,
	);
}
