<?php
/**
 * Dress Importer
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Import dresses from XML file
 *
 * @return int Number of imported dresses
 */
function loveforever_import_dresses() {
	$xml            = simplexml_load_file( get_template_directory() . '/loveforever.xml' );
	$imported_count = 0;

	foreach ( $xml->shop->offers->offer as $offer ) {
		$post_id = wp_insert_post(
			array(
				'post_title'  => (string) $offer->name,
				'post_type'   => 'dress',
				'post_status' => 'publish',
			)
		);

		if ( $post_id ) {
			$category_slug = (string) $offer->collectionId;
			$term          = get_term_by( 'slug', $category_slug, 'dress_category' );
			if ( $term ) {
				wp_set_object_terms( $post_id, $term->term_id, 'dress_category' );
			}

			update_field( 'price', (string) $offer->price, $post_id );

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
		}
	}

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
}
add_action( 'rest_api_init', 'loveforever_register_dress_importer_route' );

/**
 * Callback функция для REST API endpoint.
 *
 * @since 0.0.1
 */
function loveforever_dress_importer_callback() {
	$imported_count = loveforever_import_dresses();
	return new WP_REST_Response(
		array(
			'message' => "Импорт завершен. Импортировано платьев: $imported_count",
		),
		201,
		array( 'Content-Type' => 'application/json' )
	);
}
