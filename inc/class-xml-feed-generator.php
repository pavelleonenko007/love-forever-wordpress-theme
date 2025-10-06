<?php
/**
 * XML Feed Generator
 *
 * Generates YML catalog XML feeds for dress products
 *
 * @package LoveForever
 */

defined( 'ABSPATH' ) || exit;

class XML_Feed_Generator {

	/**
	 * Singleton instance
	 *
	 * @var XML_Feed_Generator|null
	 */
	private static $instance = null;

	/**
	 * Shop configuration
	 *
	 * @var array
	 */
	private $shop_config = array(
		'name'     => 'Salon Love Forever',
		'company'  => 'Salon Love Forever',
		'url'      => 'https://salon-love.ru',
		'platform' => 'WordPress',
		'version'  => '1.0',
		'currency' => 'RUR',
		'rate'     => 1,
	);

	/**
	 * Allowed categories for feed generation
	 *
	 * @var array
	 */
	private $allowed_categories = array(
		'wedding',
		'evening',
		'prom',
		'wedding-sale',
	);

	/**
	 * Category name mappings for Russian language
	 *
	 * @var array
	 */
	private $category_names = array(
		'wedding'      => 'Свадебное платье',
		'evening'      => 'Вечернее платье',
		'prom'         => 'Выпускное платье',
		'wedding-sale' => 'Свадебное платье',
	);

	/**
	 * Get singleton instance
	 *
	 * @return XML_Feed_Generator
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor
	 */
	private function __construct() {
		// Constructor is private for singleton pattern
	}

	/**
	 * Get shop configuration
	 *
	 * @return array
	 */
	public function get_shop_config() {
		return $this->shop_config;
	}

	/**
	 * Generate XML feed for specific category
	 *
	 * @param string $category_slug Category slug to generate feed for
	 * @param string $output_path   Path where to save XML file
	 * @param int    $limit         Maximum number of products to include (0 = no limit)
	 * @return bool|WP_Error
	 */
	public function generate_feed( $category_slug, $output_path = null, $limit = 0 ) {
		if ( empty( $category_slug ) ) {
			return new WP_Error( 'invalid_category', 'Category slug is required' );
		}

		// Check if category is allowed
		if ( ! in_array( $category_slug, $this->allowed_categories, true ) ) {
			return new WP_Error( 'category_not_allowed', 'Category not allowed for feed generation: ' . $category_slug );
		}

		// Get category
		$category = get_term_by( 'slug', $category_slug, 'dress_category' );
		if ( ! $category || is_wp_error( $category ) ) {
			return new WP_Error( 'category_not_found', 'Category not found: ' . $category_slug );
		}

		// Set default output path if not provided
		if ( ! $output_path ) {
			$filename = $category_slug;
			if ( $limit > 0 ) {
				$filename .= '-' . $limit;
			}
			$output_path = ABSPATH . 'xml/yml_' . $filename . '.xml';
		}

		// Ensure directory exists
		$output_dir = dirname( $output_path );
		if ( ! wp_mkdir_p( $output_dir ) ) {
			return new WP_Error( 'directory_creation_failed', 'Failed to create directory: ' . $output_dir );
		}

		// Get products for this category
		$products = $this->get_products_for_category( $category->term_id, $limit );

		if ( empty( $products ) ) {
			return new WP_Error( 'no_products', 'No products found for category: ' . $category->name );
		}

		// Generate XML
		$xml_content = $this->build_xml_content( $category, $products );

		// Clear products array to free memory
		$products = null;

		// Save to file
		$result = file_put_contents( $output_path, $xml_content );

		if ( false === $result ) {
			return new WP_Error( 'file_write_failed', 'Failed to write XML file: ' . $output_path );
		}

		// Clear XML content to free memory
		$xml_content = null;

		return $output_path;
	}

	/**
	 * Get products for specific category
	 *
	 * @param int $category_id Category ID
	 * @param int $limit       Maximum number of products (0 = no limit)
	 * @return array
	 */
	private function get_products_for_category( $category_id, $limit = 0 ) {
		$posts_per_page = ( $limit > 0 ) ? $limit : -1;

		$args = array(
			'post_type'              => 'dress',
			'post_status'            => 'publish',
			'posts_per_page'         => $posts_per_page,
			'no_found_rows'          => true, // Don't calculate total rows for better performance
			'update_post_meta_cache' => false, // Don't cache meta for better memory usage
			'update_post_term_cache' => false, // Don't cache terms for better memory usage
			'tax_query'              => array(
				array(
					'taxonomy' => 'dress_category',
					'field'    => 'term_id',
					'terms'    => $category_id,
				),
			),
			'meta_query'             => array(
				array(
					'key'     => 'availability',
					'value'   => true,
					'compare' => '=',
				),
			),
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
		);

		$query = new WP_Query( $args );
		$posts = $query->posts;

		// Clean up query object to free memory
		$query = null;

		return $posts;
	}

	/**
	 * Build XML content
	 *
	 * @param WP_Term $category Category term object
	 * @param array   $products Array of product posts
	 * @return string
	 */
	private function build_xml_content( $category, $products ) {
		$xml               = new DOMDocument( '1.0', 'UTF-8' );
		$xml->formatOutput = true;

		// Create root element
		$yml_catalog = $xml->createElement( 'yml_catalog' );
		$yml_catalog->setAttribute( 'date', wp_date( 'Y-m-d H:i' ) );
		$xml->appendChild( $yml_catalog );

		// Create shop element
		$shop = $xml->createElement( 'shop' );
		$yml_catalog->appendChild( $shop );

		// Add shop info
		$shop->appendChild( $xml->createElement( 'name', esc_html( $this->shop_config['name'] ) ) );
		$shop->appendChild( $xml->createElement( 'company', esc_html( $this->shop_config['company'] ) ) );
		$shop->appendChild( $xml->createElement( 'url', esc_url( $this->shop_config['url'] ) ) );
		$shop->appendChild( $xml->createElement( 'platform', esc_html( $this->shop_config['platform'] ) ) );
		$shop->appendChild( $xml->createElement( 'version', esc_html( $this->shop_config['version'] ) ) );

		// Add currencies
		$currencies = $xml->createElement( 'currencies' );
		$currency   = $xml->createElement( 'currency' );
		$currency->setAttribute( 'id', $this->shop_config['currency'] );
		$currency->setAttribute( 'rate', $this->shop_config['rate'] );
		$currencies->appendChild( $currency );
		$shop->appendChild( $currencies );

		// Add categories
		$categories       = $xml->createElement( 'categories' );
		$category_name    = ( $category->slug === 'wedding-sale' ) ? 'Распродажа' : $category->name;
		$category_element = $xml->createElement( 'category', esc_html( $category_name ) );
		$category_element->setAttribute( 'id', $category->term_id );
		$categories->appendChild( $category_element );
		$shop->appendChild( $categories );

		// Add offers
		$offers = $xml->createElement( 'offers' );
		$shop->appendChild( $offers );

		foreach ( $products as $product ) {
			$offer = $this->create_offer_element( $xml, $product, $category->term_id );
			if ( $offer ) {
				$offers->appendChild( $offer );
			}
		}

		// Add collections
		$collections = $xml->createElement( 'collections' );
		$collection  = $xml->createElement( 'collection' );
		$collection->setAttribute( 'id', $category->term_id );
		$collection->appendChild( $xml->createElement( 'url', esc_url( get_term_link( $category ) ) ) );
		$collection_name = ( $category->slug === 'wedding-sale' ) ? 'Распродажа' : $category->name;
		$collection->appendChild( $xml->createElement( 'name', esc_html( $collection_name . ' в магазине Love Forever' ) ) );
		$collection->appendChild( $xml->createElement( 'description', esc_html( $this->get_category_description( $category ) ) ) );

		// Add category image if available
		$category_image = $this->get_category_image( $category );
		if ( $category_image ) {
			$collection->appendChild( $xml->createElement( 'picture', esc_url( $category_image ) ) );
		}

		$collections->appendChild( $collection );
		$shop->appendChild( $collections );

		return $xml->saveXML();
	}

	/**
	 * Create offer element for product
	 *
	 * @param DOMDocument $xml     XML document
	 * @param WP_Post     $product Product post object
	 * @param string      $category_id Category slug for which feed is being generated
	 * @return DOMElement|false
	 */
	private function create_offer_element( $xml, $product, $category_id ) {
		$offer = $xml->createElement( 'offer' );
		$offer->setAttribute( 'id', $product->ID );
		$offer->setAttribute( 'available', 'true' );

		// Product name with category prefix
		$product_name = $this->get_product_name_with_category( $product, $category_id );
		$offer->appendChild( $xml->createElement( 'name', esc_html( $product_name ) ) );

		// Collection ID (feed category slug)
		$offer->appendChild( $xml->createElement( 'collectionId', esc_html( $category_id ) ) );

		// Product URL
		$offer->appendChild( $xml->createElement( 'url', esc_url( get_permalink( $product->ID ) ) ) );

		// Price
		$final_price = get_post_meta( $product->ID, 'final_price', true );
		if ( ! $final_price ) {
			$final_price = get_field( 'price', $product->ID );
		}

		if ( $final_price ) {
			$offer->appendChild( $xml->createElement( 'price', intval( $final_price ) ) );
		}

		// Currency
		$offer->appendChild( $xml->createElement( 'currencyId', $this->shop_config['currency'] ) );

		// Category ID
		$offer->appendChild( $xml->createElement( 'categoryId', $category_id ) );

		// Images
		$images = $this->get_product_images( $product->ID );
		foreach ( $images as $image_url ) {
			$offer->appendChild( $xml->createElement( 'picture', esc_url( $image_url ) ) );
		}

		// Store availability
		$availability = get_field( 'availability', $product->ID );
		$offer->appendChild( $xml->createElement( 'store', $availability ? 'true' : 'false' ) );

		// Description if available
		$description = get_field( 'description', $product->ID );
		if ( $description ) {
			$offer->appendChild( $xml->createElement( 'description', esc_html( $description ) ) );
		}

		return $offer;
	}

	/**
	 * Get product images
	 *
	 * @param int $product_id Product ID
	 * @return array
	 */
	private function get_product_images( $product_id ) {
		$images = array();

		// Featured image (first picture)
		$featured_image_id = get_post_thumbnail_id( $product_id );
		if ( $featured_image_id ) {
			$featured_image_url = wp_get_attachment_image_url( $featured_image_id, 'full' );
			if ( $featured_image_url ) {
				$images[] = $featured_image_url;
			}
		}

		// Gallery images from ACF repeater field
		$gallery_images = get_field( 'images', $product_id );
		if ( $gallery_images && is_array( $gallery_images ) ) {
			foreach ( $gallery_images as $gallery_item ) {
				// Check if image field exists and has value
				if ( isset( $gallery_item['image'] ) ) {
					$image_data = $gallery_item['image'];

					// Handle different return formats from ACF
					if ( is_array( $image_data ) && isset( $image_data['url'] ) ) {
						// Return format: array
						$image_url = $image_data['url'];
					} elseif ( is_numeric( $image_data ) ) {
						// Return format: ID
						$image_url = wp_get_attachment_image_url( $image_data, 'full' );
					} elseif ( is_string( $image_data ) ) {
						// Return format: URL
						$image_url = $image_data;
					} else {
						continue;
					}

					// Add image if URL is valid and not already in array
					if ( $image_url && ! in_array( $image_url, $images ) ) {
						$images[] = $image_url;
					}
				}
			}
		}

		return $images;
	}

	/**
	 * Get category description
	 *
	 * @param WP_Term $category Category term
	 * @return string
	 */
	private function get_category_description( $category ) {
		$description = $category->description;

		if ( empty( $description ) ) {
			$description = sprintf(
				'Купить %s в Санкт-Петербурге. В наличии › 1000 красивых платьев невесты. ✨Распродажа до -70%%✨ Запишись на примерку прямо сейчас — получи скидку и подарок!',
				strtolower( $category->name )
			);
		} else {
			// Remove HTML tags and decode HTML entities
			$description = wp_strip_all_tags( $description );
			$description = html_entity_decode( $description, ENT_QUOTES, 'UTF-8' );
			// Clean up extra whitespace
			$description = preg_replace( '/\s+/', ' ', trim( $description ) );
		}

		return $description;
	}

	/**
	 * Get category image
	 *
	 * @param WP_Term $category Category term
	 * @return string|false
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

		// Fallback: get first product image from this category
		$products = $this->get_products_for_category( $category->term_id, 1 );
		if ( ! empty( $products ) ) {
			$first_product_images = $this->get_product_images( $products[0]->ID );
			if ( ! empty( $first_product_images ) ) {
				return $first_product_images[0];
			}
		}

		return false;
	}

	/**
	 * Generate feed for all allowed categories
	 *
	 * @param int $limit Maximum number of products per category (0 = no limit)
	 * @return array Results for each category
	 */
	public function generate_all_feeds( $limit = 0 ) {
		$results = array();

		foreach ( $this->allowed_categories as $category_slug ) {
			$result                    = $this->generate_feed( $category_slug, null, $limit );
			$results[ $category_slug ] = $result;
		}

		return $results;
	}

	/**
	 * Get available categories for feed generation
	 *
	 * @return array
	 */
	public function get_available_categories() {
		$result = array();

		foreach ( $this->allowed_categories as $category_slug ) {
			$category = get_term_by( 'slug', $category_slug, 'dress_category' );

			if ( $category && ! is_wp_error( $category ) ) {
				$result[] = $category;
			}
		}
		return $result;
	}

	/**
	 * Get product name with category prefix
	 *
	 * @param WP_Post $product Product post object
	 * @param string  $category_id Category slug for which feed is being generated
	 * @return string
	 */
	private function get_product_name_with_category( $product, $category_id ) {
		$product_name = $product->post_title;

		// Get all categories for this product
		$product_categories = get_the_terms( $product->ID, 'dress_category' );

		if ( ! $product_categories || is_wp_error( $product_categories ) ) {
			// If no categories, use the feed category
			$category_name = $this->category_names[ $category_id ] ?? 'Платье';
			return $category_name . ' ' . $product_name;
		}

		// Convert to array of slugs
		$category_slugs = array_map(
			function ( $term ) {
				return $term->slug;
			},
			$product_categories
		);

		// Determine priority category
		$priority_category = $this->get_priority_category( $category_slugs );

		// Get category name
		$category_name = $this->category_names[ $priority_category ] ?? 'Платье';

		return $category_name . ' ' . $product_name;
	}

	/**
	 * Get priority category based on business rules
	 *
	 * @param array $category_slugs Array of category slugs
	 * @return string Priority category slug
	 */
	private function get_priority_category( $category_slugs ) {
		// Priority order: evening > prom > wedding > wedding-sale
		$priority_order = array( 'evening', 'prom', 'wedding', 'wedding-sale' );

		foreach ( $priority_order as $category ) {
			if ( in_array( $category, $category_slugs, true ) ) {
				return $category;
			}
		}

		// Fallback to first allowed category found
		foreach ( $category_slugs as $slug ) {
			if ( in_array( $slug, $this->allowed_categories, true ) ) {
				return $slug;
			}
		}

		// Ultimate fallback
		return 'wedding';
	}

	/**
	 * Generate all required feeds (full + limited + combined)
	 *
	 * @return array Results for each feed
	 */
	public function generate_all_required_feeds() {
		$results = array();

		// Full feeds for all categories
		$full_categories = array( 'wedding', 'evening', 'prom', 'wedding-sale' );
		foreach ( $full_categories as $category_slug ) {
			$result                              = $this->generate_feed( $category_slug );
			$results[ $category_slug . '_full' ] = $result;
		}

		// Limited feeds
		$limited_feeds = array(
			'wedding' => 360,
			'evening' => 72,
			'prom'    => 96,
		);

		foreach ( $limited_feeds as $category_slug => $limit ) {
			$result                                   = $this->generate_feed( $category_slug, null, $limit );
			$results[ $category_slug . '_' . $limit ] = $result;
		}

		// Combined feed (superview)
		$combined_result     = $this->generate_combined_feed();
		$results['combined'] = $combined_result;

		return $results;
	}

	/**
	 * Generate combined feed with multiple categories
	 *
	 * @return bool|WP_Error
	 */
	public function generate_combined_feed() {
		$categories     = array( 'wedding', 'evening', 'prom' );
		$limited_counts = array(
			'wedding' => 360,
			'evening' => 72,
			'prom'    => 96,
		);

		// Get all products from limited categories
		$all_products   = array();
		$category_terms = array();

		foreach ( $categories as $category_slug ) {
			$category = get_term_by( 'slug', $category_slug, 'dress_category' );
			if ( ! $category || is_wp_error( $category ) ) {
				continue;
			}

			$category_terms[] = $category;
			$products         = $this->get_products_for_category( $category->term_id, $limited_counts[ $category_slug ] );
			$all_products     = array_merge( $all_products, $products );
		}

		if ( empty( $all_products ) ) {
			return new WP_Error( 'no_products', 'No products found for combined feed' );
		}

		// Generate XML content for combined feed
		$xml_content = $this->build_combined_xml_content( $category_terms, $all_products );

		// Save to public XML directory only
		$public_path = ABSPATH . 'xml/yml_combined.xml';

		// Ensure directory exists
		wp_mkdir_p( dirname( $public_path ) );

		// Save file
		$result = file_put_contents( $public_path, $xml_content );

		if ( false === $result ) {
			return new WP_Error( 'file_write_failed', 'Failed to write combined XML file' );
		}

		return $public_path;
	}

	/**
	 * Build XML content for combined feed
	 *
	 * @param array $categories Array of category term objects
	 * @param array $products   Array of product posts
	 * @return string
	 */
	private function build_combined_xml_content( $categories, $products ) {
		$xml               = new DOMDocument( '1.0', 'UTF-8' );
		$xml->formatOutput = true;

		// Create root element
		$yml_catalog = $xml->createElement( 'yml_catalog' );
		$yml_catalog->setAttribute( 'date', gmdate( 'Y-m-d H:i' ) );
		$xml->appendChild( $yml_catalog );

		// Create shop element
		$shop = $xml->createElement( 'shop' );
		$yml_catalog->appendChild( $shop );

		// Add shop info
		$shop->appendChild( $xml->createElement( 'name', esc_html( $this->shop_config['name'] ) ) );
		$shop->appendChild( $xml->createElement( 'company', esc_html( $this->shop_config['company'] ) ) );
		$shop->appendChild( $xml->createElement( 'url', esc_url( $this->shop_config['url'] ) ) );
		$shop->appendChild( $xml->createElement( 'platform', esc_html( $this->shop_config['platform'] ) ) );
		$shop->appendChild( $xml->createElement( 'version', esc_html( $this->shop_config['version'] ) ) );

		// Add currencies
		$currencies = $xml->createElement( 'currencies' );
		$currency   = $xml->createElement( 'currency' );
		$currency->setAttribute( 'id', $this->shop_config['currency'] );
		$currency->setAttribute( 'rate', $this->shop_config['rate'] );
		$currencies->appendChild( $currency );
		$shop->appendChild( $currencies );

		// Add categories
		$categories_element = $xml->createElement( 'categories' );
		foreach ( $categories as $category ) {
			$category_name    = ( $category->slug === 'wedding-sale' ) ? 'Распродажа' : $category->name;
			$category_element = $xml->createElement( 'category', esc_html( $category_name ) );
			$category_element->setAttribute( 'id', $category->slug );
			$categories_element->appendChild( $category_element );
		}
		$shop->appendChild( $categories_element );

		// Add offers
		$offers = $xml->createElement( 'offers' );
		$shop->appendChild( $offers );

		foreach ( $products as $product ) {
			// Determine which category this product belongs to for collectionId
			$product_categories = get_the_terms( $product->ID, 'dress_category' );
			$category_id        = 'wedding'; // default

			if ( $product_categories && ! is_wp_error( $product_categories ) ) {
				$category_slugs = array_map(
					function ( $term ) {
						return $term->slug;
					},
					$product_categories
				);
				$category_id    = $this->get_priority_category( $category_slugs );
			}

			$offer = $this->create_offer_element( $xml, $product, $category_id );
			if ( $offer ) {
				$offers->appendChild( $offer );
			}
		}

		// Add collections
		$collections = $xml->createElement( 'collections' );
		$shop->appendChild( $collections );

		foreach ( $categories as $category ) {
			$collection = $xml->createElement( 'collection' );
			$collection->setAttribute( 'id', $category->slug );
			$collection->appendChild( $xml->createElement( 'url', esc_url( get_term_link( $category ) ) ) );
			$collection_name = ( $category->slug === 'wedding-sale' ) ? 'Распродажа' : $category->name;
			$collection->appendChild( $xml->createElement( 'name', esc_html( $collection_name . ' в магазине Love Forever' ) ) );
			$collection->appendChild( $xml->createElement( 'description', esc_html( $this->get_category_description( $category ) ) ) );

			// Add category image if available
			$category_image = $this->get_category_image( $category );
			if ( $category_image ) {
				$collection->appendChild( $xml->createElement( 'picture', esc_url( $category_image ) ) );
			}

			$collections->appendChild( $collection );
		}

		return $xml->saveXML();
	}
}
