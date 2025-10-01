<?php
/**
 * Batch XML Feed Generator for Large Catalogs
 * 
 * This class handles XML feed generation in batches to avoid memory issues
 * 
 * @package LoveForever
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XML_Feed_Batch_Generator {
	
	/**
	 * Batch size for processing products
	 */
	const BATCH_SIZE = 50;
	
	/**
	 * Generate feed in batches to avoid memory issues
	 *
	 * @param string $category_slug Category slug
	 * @param int    $limit        Maximum number of products (0 = no limit)
	 * @return string|WP_Error File path or error
	 */
	public function generate_feed_batch( $category_slug, $limit = 0 ) {
		// Increase memory limit
		ini_set( 'memory_limit', '512M' );
		
		// Get category
		$category = get_term_by( 'slug', $category_slug, 'dress_category' );
		if ( ! $category || is_wp_error( $category ) ) {
			return new WP_Error( 'category_not_found', 'Category not found: ' . $category_slug );
		}
		
		// Set output path
		$filename = $category_slug;
		if ( $limit > 0 ) {
			$filename .= '-' . $limit;
		}
		$output_path = ABSPATH . 'xml/' . $filename . '.xml';
		
		// Ensure directory exists
		$output_dir = dirname( $output_path );
		if ( ! wp_mkdir_p( $output_dir ) ) {
			return new WP_Error( 'directory_creation_failed', 'Failed to create directory: ' . $output_dir );
		}
		
		// Start XML file
		$xml_header = $this->get_xml_header( $category );
		file_put_contents( $output_path, $xml_header );
		
		// Get total count
		$total_count = $this->get_products_count( $category->term_id );
		$processed_count = 0;
		$batch_count = 0;
		
		// Process in batches
		while ( $processed_count < $total_count && ( $limit == 0 || $processed_count < $limit ) ) {
			$batch_count++;
			$batch_limit = min( self::BATCH_SIZE, $limit > 0 ? $limit - $processed_count : self::BATCH_SIZE );
			
			// Get batch of products
			$products = $this->get_products_batch( $category->term_id, $batch_count, $batch_limit );
			
			if ( empty( $products ) ) {
				break;
			}
			
			// Generate XML for this batch
			$batch_xml = $this->generate_batch_xml( $products, $category_slug );
			
			// Append to file
			file_put_contents( $output_path, $batch_xml, FILE_APPEND );
			
			$processed_count += count( $products );
			
			// Clear memory
			$products = null;
			$batch_xml = null;
			wp_cache_flush();
			
			if ( function_exists( 'gc_collect_cycles' ) ) {
				gc_collect_cycles();
			}
		}
		
		// Close XML file
		$xml_footer = $this->get_xml_footer();
		file_put_contents( $output_path, $xml_footer, FILE_APPEND );
		
		return $output_path;
	}
	
	/**
	 * Get total count of products for category
	 */
	private function get_products_count( $category_id ) {
		$args = array(
			'post_type'      => 'dress',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids', // Only get IDs for better performance
			'tax_query'      => array(
				array(
					'taxonomy' => 'dress_category',
					'field'    => 'term_id',
					'terms'    => $category_id,
				),
			),
			'meta_query'     => array(
				array(
					'key'     => 'availability',
					'value'   => true,
					'compare' => '=',
				),
			),
		);
		
		$query = new WP_Query( $args );
		$count = $query->found_posts;
		
		// Clean up
		$query = null;
		
		return $count;
	}
	
	/**
	 * Get batch of products
	 */
	private function get_products_batch( $category_id, $batch_number, $batch_size ) {
		$offset = ( $batch_number - 1 ) * self::BATCH_SIZE;
		
		$args = array(
			'post_type'      => 'dress',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size,
			'offset'         => $offset,
			'no_found_rows'  => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'      => array(
				array(
					'taxonomy' => 'dress_category',
					'field'    => 'term_id',
					'terms'    => $category_id,
				),
			),
			'meta_query'     => array(
				array(
					'key'     => 'availability',
					'value'   => true,
					'compare' => '=',
				),
			),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		);
		
		$query = new WP_Query( $args );
		$posts = $query->posts;
		
		// Clean up
		$query = null;
		
		return $posts;
	}
	
	/**
	 * Generate XML header
	 */
	private function get_xml_header( $category ) {
		$generator = XML_Feed_Generator::get_instance();
		
		$xml = new DOMDocument( '1.0', 'UTF-8' );
		$xml->formatOutput = true;
		
		// Create root element
		$yml_catalog = $xml->createElement( 'yml_catalog' );
		$yml_catalog->setAttribute( 'date', date( 'Y-m-d H:i' ) );
		$xml->appendChild( $yml_catalog );
		
		// Create shop element
		$shop = $xml->createElement( 'shop' );
		$yml_catalog->appendChild( $shop );
		
		// Add shop info
		$shop_config = $generator->get_shop_config();
		$shop->appendChild( $xml->createElement( 'name', esc_html( $shop_config['name'] ) ) );
		$shop->appendChild( $xml->createElement( 'company', esc_html( $shop_config['company'] ) ) );
		$shop->appendChild( $xml->createElement( 'url', esc_url( $shop_config['url'] ) ) );
		
		// Add currencies
		$currencies = $xml->createElement( 'currencies' );
		$currency = $xml->createElement( 'currency' );
		$currency->setAttribute( 'id', $shop_config['currency'] );
		$currency->setAttribute( 'rate', $shop_config['rate'] );
		$currencies->appendChild( $currency );
		$shop->appendChild( $currencies );
		
		// Add categories
		$categories = $xml->createElement( 'categories' );
		$category_name = ( $category->slug === 'wedding-sale' ) ? 'Распродажа' : $category->name;
		$category_element = $xml->createElement( 'category', esc_html( $category_name ) );
		$category_element->setAttribute( 'id', $category->slug );
		$categories->appendChild( $category_element );
		$shop->appendChild( $categories );
		
		// Add offers opening tag
		$offers = $xml->createElement( 'offers' );
		$shop->appendChild( $offers );
		
		// Add collections
		$collections = $xml->createElement( 'collections' );
		$collection = $xml->createElement( 'collection' );
		$collection->setAttribute( 'id', $category->slug );
		$collection->appendChild( $xml->createElement( 'url', esc_url( get_term_link( $category ) ) ) );
		$collection_name = ( $category->slug === 'wedding-sale' ) ? 'Распродажа' : $category->name;
		$collection->appendChild( $xml->createElement( 'name', esc_html( $collection_name . ' в магазине Love Forever' ) ) );
		
		// Add category description
		$description = $category->description;
		if ( empty( $description ) ) {
			$description = sprintf( 'Купить %s в Санкт-Петербурге. В наличии › 1000 красивых платьев невесты. ✨Распродажа до -70%%✨ Запишись на примерку прямо сейчас — получи скидку и подарок!', strtolower( $category->name ) );
		} else {
			$description = wp_strip_all_tags( $description );
			$description = html_entity_decode( $description, ENT_QUOTES, 'UTF-8' );
			$description = preg_replace( '/\s+/', ' ', trim( $description ) );
		}
		$collection->appendChild( $xml->createElement( 'description', esc_html( $description ) ) );
		
		// Add category image
		$category_image = $this->get_category_image( $category );
		if ( $category_image ) {
			$collection->appendChild( $xml->createElement( 'picture', esc_url( $category_image ) ) );
		}
		
		$collections->appendChild( $collection );
		$shop->appendChild( $collections );
		
		// Return XML without closing tags (they will be added later)
		$xml_string = $xml->saveXML();
		$xml_string = str_replace( '</shop>', '', $xml_string );
		$xml_string = str_replace( '</yml_catalog>', '', $xml_string );
		
		return $xml_string;
	}
	
	/**
	 * Generate XML for batch of products
	 */
	private function generate_batch_xml( $products, $feed_category_slug ) {
		$generator = XML_Feed_Generator::get_instance();
		$xml_string = '';
		
		foreach ( $products as $product ) {
			// Create a temporary XML document for the offer element
			$temp_xml = new DOMDocument( '1.0', 'UTF-8' );
			$offer_element = $generator->create_offer_element( $temp_xml, $product, $feed_category_slug );
			$offer_xml = $temp_xml->saveXML( $offer_element );
			$xml_string .= $offer_xml;
		}
		
		return $xml_string;
	}
	
	/**
	 * Get XML footer
	 */
	private function get_xml_footer() {
		return '</offers></shop></yml_catalog>';
	}
	
	/**
	 * Get category image
	 */
	private function get_category_image( $category ) {
		// Get image from ACF field 'thumbnail' (returns ID)
		$image_id = get_field( 'thumbnail', 'dress_category_' . $category->term_id );
		
		if ( $image_id ) {
			// Handle different return formats from ACF
			if ( is_array( $image_id ) && isset( $image_id['url'] ) ) {
				// Return format: array
				return $image_id['url'];
			} elseif ( is_numeric( $image_id ) ) {
				// Return format: ID (most likely case based on JSON)
				return wp_get_attachment_image_url( $image_id, 'full' );
			} elseif ( is_string( $image_id ) ) {
				// Return format: URL
				return $image_id;
			}
		}
		
		return false;
	}
}
