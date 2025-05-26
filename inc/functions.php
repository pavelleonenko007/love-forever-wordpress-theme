<?php
/**
 * Functions
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

function loveforever_remove_image_dimensions_attributes( $image_html ) {
	$image_html = preg_replace( '/(width|height)="\d*"\s/', '', $image_html );
	return $image_html;
}

function loveforever_format_phone_to_link( $phone ) {
	$phone = preg_replace( '/[^0-9]/', '', $phone );
	return 'tel:' . $phone;
}

function loveforever_format_email_to_link( $email, $subject = '' ) {
	$email_url = 'mailto:' . $email;
	if ( ! empty( $subject ) ) {
		$email_url .= '?subject=' . rawurlencode( $subject );
	}
	return $email_url;
}

function loveforever_is_valid_phone( $phone ) {
	// Удаляем все, кроме цифр
	$phone = preg_replace( '/[^0-9]/', '', $phone );

	// Проверяем, что номер начинается с 7 или 8 и содержит 11 цифр
	return ( preg_match( '/^[78]\d{10}$/', $phone ) === 1 );
}

function loveforever_is_valid_fitting_datetime( $datetime, $fitting_type, $exclude_fitting_id = null ) {
	$timestamp = strtotime( $datetime );
	if ( $timestamp === false ) {
			return 'Неверный формат даты и времени';
	}

	$current_time = current_time( 'timestamp' );
	if ( $timestamp <= $current_time ) {
			return 'Время примерки не может быть в прошлом';
	}

	$hour = gmdate( 'G', $timestamp );
	if ( $hour < 10 || $hour >= 21 ) {
			return 'Время примерки должно быть между 10:00 и 21:00';
	}

	// Проверка доступности слота
	$date              = gmdate( 'Y-m-d', $timestamp );
	$time              = gmdate( 'H:i', $timestamp );
	$slot_availability = Fitting_Slots::check_slot_availability( $date, $time, $fitting_type, $exclude_fitting_id );

	if ( $slot_availability !== true ) {
			return $slot_availability;
	}

	return true;
}

function loveforever_get_head_code() {
	if ( function_exists( 'get_field' ) ) {
		echo get_field( 'body_code', 'option' );
	}
}

function loveforever_get_viewed_products() {
	if ( empty( $_COOKIE['viewed_products'] ) ) {
		return array();
	}

	return array_reverse( explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['viewed_products'] ) ) ) );
}

function loveforever_update_viewed_products( $product_id ) {
	$views           = (int) get_post_meta( $product_id, 'product_views_count', true );
	$viewed_products = ! empty( $_COOKIE['viewed_products'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['viewed_products'] ) ) ) : array();

	if ( empty( $viewed_products ) ) {
		$viewed_products[] = $product_id;
		setcookie( 'viewed_products', implode( ',', $viewed_products ), time() + DAY_IN_SECONDS * 30, '/' );
		update_post_meta( $product_id, 'product_views_count', $views + 1 );
	} else {
		if ( ! in_array( $product_id, $viewed_products ) ) {
			update_post_meta( $product_id, 'product_views_count', $views + 1 );
		}

		$viewed_products = array_filter(
			$viewed_products,
			function ( $id ) use ( $product_id ) {
					return (int) $id !== $product_id;
			}
		);

		$viewed_products[] = $product_id;
		setcookie( 'viewed_products', implode( ',', $viewed_products ), time() + DAY_IN_SECONDS * 30, '/' );
	}
}

/**
 * Generates array of pagination links.
 *
 * @param array $args {
 *
 *     @type int    $total        Maximum allowable pagination page.
 *     @type int    $current      Current page number.
 *     @type string $url_base     URL pattern. Use `{pagenum}` placeholder.
 *     @type string $first_url    URL to first page. Default: '' - taken automatically from $url_base.
 *     @type int    $mid_size     Number of links before/after current: 1 ... 1 2 [3] 4 5 ... 99. Default: 2.
 *     @type int    $end_size     Number of links at the edges: 1 2 ... 3 4 [5] 6 7 ... 98 99. Default: 1.
 *     @type bool   $show_all     true - Show all links. Default: false.
 *     @type string $a_text_patt  `%s` will be replaced with number of pagination page. Default: `'%s'`.
 *     @type bool   $is_prev_next Whether to show prev/next links. « Previous 1 2 [3] 4 ... 99 Next ». Default: false.
 *     @type string $prev_text    Default: `« Previous`.
 *     @type string $next_text    Default: `Next »`.
 * }
 *
 * @return array
 */
function loveforever_paginate_links_data( array $args ): array {
	global $wp_query;

	$args += array(
		'total'        => 1,
		'current'      => 0,
		'url_base'     => '/{pagenum}',
		'first_url'    => '',
		'mid_size'     => 2,
		'end_size'     => 1,
		'show_all'     => false,
		'a_text_patt'  => '%s',
		'is_prev_next' => false,
		'prev_text'    => '« Previous',
		'next_text'    => 'Next »',
	);

	$rg = (object) $args;

	$total_pages = max( 1, (int) ( $rg->total ?: $wp_query->max_num_pages ) );

	if ( $total_pages === 1 ) {
		return array();
	}

	// fix working parameters

	$rg->total   = $total_pages;
	$rg->current = max( 1, abs( $rg->current ?: get_query_var( 'paged', 1 ) ) );

	$rg->url_base = $rg->url_base ?: str_replace( PHP_INT_MAX, '{pagenum}', get_pagenum_link( PHP_INT_MAX ) );
	$rg->url_base = wp_normalize_path( $rg->url_base );

	if ( ! $rg->first_url ) {
		// /foo/page(d)/2 >>> /foo/ /foo?page(d)=2 >>> /foo/
		$rg->first_url = preg_replace( '~/paged?/{pagenum}/?|[?]paged?={pagenum}|/{pagenum}/?~', '', $rg->url_base );
		$rg->first_url = user_trailingslashit( $rg->first_url );
	}

	// core array

	if ( $rg->show_all ) {
		$active_nums = range( 1, $rg->total );
	} else {

		if ( $rg->end_size > 1 ) {
			$start_nums = range( 1, $rg->end_size );
			$end_nums   = range( $rg->total - ( $rg->end_size - 1 ), $rg->total );
		} else {
			$start_nums = array( 1 );
			$end_nums   = array( $rg->total );
		}

		$from = $rg->current - $rg->mid_size;
		$to   = $rg->current + $rg->mid_size;

		if ( $from < 1 ) {
			$to   = min( $rg->total, $to + absint( $from ) );
			$from = 1;

		}
		if ( $to > $rg->total ) {
			$from = max( 1, $from - ( $to - $rg->total ) );
			$to   = $rg->total;
		}

		$active_nums = array_merge( $start_nums, range( $from, $to ), $end_nums );
		$active_nums = array_unique( $active_nums );
		$active_nums = array_values( $active_nums ); // reset keys
	}

	// fill by core array

	$pages = array();

	if ( 1 === count( $active_nums ) ) {
		return $pages;
	}

	$item_data = static function ( $num ) use ( $rg ) {

		$data = array(
			'is_current'   => false,
			'page_num'     => null,
			'url'          => null,
			'link_text'    => null,
			'is_prev_next' => false,
			'is_dots'      => false,
		);

		if ( 'dots' === $num ) {

			return (object) ( array(
				'is_dots'   => true,
				'link_text' => '…',
			) + $data );
		}

		$is_prev = 'prev' === $num && ( $num = max( 1, $rg->current - 1 ) );
		$is_next = 'next' === $num && ( $num = min( $rg->total, $rg->current + 1 ) );

		$data = array(
			'is_current'   => ! ( $is_prev || $is_next ) && $num === $rg->current,
			'page_num'     => $num,
			'url'          => 1 === $num ? $rg->first_url : str_replace( '{pagenum}', $num, $rg->url_base ),
			'is_prev_next' => $is_prev || $is_next,
		) + $data;

		if ( $is_prev ) {
			$data['link_text'] = $rg->prev_text;
		} elseif ( $is_next ) {
			$data['link_text'] = $rg->next_text;
		} else {
			$data['link_text'] = sprintf( $rg->a_text_patt, $num );
		}

		return (object) $data;
	};

	foreach ( $active_nums as $indx => $num ) {

		$pages[] = $item_data( $num );

		// set dots
		$next = $active_nums[ $indx + 1 ] ?? null;
		if ( $next && ( $num + 1 ) !== $next ) {
			$pages[] = $item_data( 'dots' );
		}
	}

	if ( $rg->is_prev_next ) {
		$rg->current !== 1 && array_unshift( $pages, $item_data( 'prev' ) );
		$rg->current !== $rg->total && $pages[] = $item_data( 'next' );
	}

	return $pages;
}

function loveforever_get_pagination_html( WP_Query $query, array $pagination_args = array() ): string {
	$total_pages  = $query->max_num_pages;
	$current_page = max( 1, $query->get( 'paged' ) );
	$url_base     = ! empty( $pagination_args['base_url'] )
		? $pagination_args['base_url'] . '?paged={pagenum}'
		: get_pagenum_link( 1 ) . '?paged={pagenum}';
	$args         = array(
		'total'        => $total_pages,
		'current'      => $current_page,
		'url_base'     => $url_base,
		'mid_size'     => 2,
		'is_prev_next' => true,
		'prev_text'    => '<svg width="6" height="10" viewbox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" clip-rule="evenodd" d="M0.750232 4.28598L5.25007 0L6 0.714289L1.50016 5.00027L5.99944 9.28571L5.24951 10L0 4.99998L0.74993 4.28569L0.750232 4.28598Z" fill="black"></path>
		</svg>',
		'next_text'    => '<svg width="6" height="10" viewbox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" clip-rule="evenodd" d="M5.24977 4.28598L0.74993 0L0 0.714289L4.49984 5.00027L0.000560648 9.28571L0.750491 10L6 4.99998L5.25007 4.28569L5.24977 4.28598Z" fill="black"></path>
		</svg>',
	);
	$pages        = loveforever_paginate_links_data( $args );

	if ( empty( $pages ) ) {
		return '';
	}

	$output  = '<nav class="pagination" role="navigation" aria-label="Постраничная навигация">';
	$output .= '<ul class="pagination__list">';

	foreach ( $pages as $page ) {
		$classes      = array( 'pagination__item' );
		$link_classes = array( 'pagination__link', 'no-barba' );

		if ( $page->is_current ) {
			$classes[]      = 'pagination__item--active';
			$link_classes[] = 'pagination__link--active';
		}

		if ( $page->is_dots ) {
			$classes[] = 'pagination__item--dots';
			$output   .= sprintf(
				'<li class="%s"><span class="pagination__dots">%s</span></li>',
				implode( ' ', $classes ),
				$page->link_text
			);
			continue;
		}

		if ( $page->is_prev_next ) {
			$classes[]      = 'pagination__item--' . ( $page->link_text === $args['prev_text'] ? 'prev' : 'next' );
			$link_classes[] = 'pagination__link--' . ( $page->link_text === $args['prev_text'] ? 'prev' : 'next' );
		}

		if ( isset( $pagination_args['is_catalog_page'] ) && $pagination_args['is_catalog_page'] ) {
			$output .= sprintf(
				'<li class="%s"><a href="%s" class="%s" data-js-product-filter-form-paginate-link="%d" aria-label="Go to page %d"%s>%s</a></li>',
				implode( ' ', $classes ),
				esc_url( $page->url ),
				implode( ' ', $link_classes ),
				$page->page_num,
				$page->page_num,
				$page->is_current ? ' aria-current="page"' : '',
				$page->link_text
			);
		} else {
			$output .= sprintf(
				'<li class="%s"><a href="%s" class="%s" aria-label="Go to page %d"%s>%s</a></li>',
				implode( ' ', $classes ),
				esc_url( $page->url ),
				implode( ' ', $link_classes ),
				$page->page_num,
				$page->is_current ? ' aria-current="page"' : '',
				$page->link_text
			);
		}
	}

	$output .= '</ul></nav>';

	return $output;
}

function loveforever_get_product_price_range( $category_term_id = null ) {
	global $wpdb;

	if ( empty( $category_term_id ) ) {
		return loveforever_get_product_price_range_without_category();
	}

	$query = $wpdb->prepare(
		"SELECT 
				MIN(CAST(final_price.meta_value AS UNSIGNED INTEGER)) as min_price,
				MAX(CAST(final_price.meta_value AS UNSIGNED INTEGER)) as max_price
		 FROM {$wpdb->posts} p
		 JOIN {$wpdb->postmeta} final_price ON p.ID = final_price.post_id
		 JOIN {$wpdb->term_relationships} term_relationships ON p.ID = term_relationships.object_id
		 JOIN {$wpdb->term_taxonomy} term_taxonomy ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id
		 WHERE p.post_type = %s
		 AND p.post_status = 'publish'
		 AND final_price.meta_key = 'final_price'
		 AND final_price.meta_value > 0
		 AND term_taxonomy.term_id = %d
		 ORDER BY final_price.meta_value ASC",
		'dress',
		$category_term_id
	);

	// Выполнение запроса
	$result = $wpdb->get_row( $query );

	if ( $result && null !== $result->min_price && null !== $result->max_price ) {
			return array(
				'min_price' => (int) $result->min_price,
				'max_price' => (int) $result->max_price,
			);
	}

	return false;
}

function loveforever_get_product_price_range_without_category() {
	global $wpdb;

	$query = $wpdb->prepare(
		"SELECT 
				MIN(CAST(final_price.meta_value AS UNSIGNED INTEGER)) as min_price,
				MAX(CAST(final_price.meta_value AS UNSIGNED INTEGER)) as max_price
		 FROM {$wpdb->posts} p
		 JOIN {$wpdb->postmeta} final_price ON p.ID = final_price.post_id
		 WHERE p.post_type = %s
		 AND p.post_status = 'publish'
		 AND final_price.meta_key = 'final_price'
		 AND final_price.meta_value > 0
		 ORDER BY final_price.meta_value ASC",
		'dress'
	);

	// Выполнение запроса
	$result = $wpdb->get_row( $query );

	if ( $result && null !== $result->min_price && null !== $result->max_price ) {
			return array(
				'min_price' => (float) $result->min_price,
				'max_price' => (float) $result->max_price,
			);
	}

	return false;
}

function loveforever_get_dress_tags_by_category( $category_term_id ) {
	global $wpdb;

	// Ensure term_id is integer
	$category_term_id = absint( $category_term_id );

	if ( ! $category_term_id ) {
			return array();
	}

	$query = $wpdb->prepare(
		"SELECT DISTINCT terms.term_id, terms.name, terms.slug
			FROM {$wpdb->terms} AS terms
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy 
					ON terms.term_id = term_taxonomy.term_id
			INNER JOIN {$wpdb->term_relationships} AS term_relationships 
					ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
			INNER JOIN {$wpdb->posts} AS posts 
					ON term_relationships.object_id = posts.ID
			INNER JOIN {$wpdb->term_relationships} AS category_relationships 
					ON posts.ID = category_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS category_taxonomy 
					ON category_relationships.term_taxonomy_id = category_taxonomy.term_taxonomy_id
			WHERE term_taxonomy.taxonomy = 'dress_tag'
			AND category_taxonomy.taxonomy = 'dress_category'
			AND category_taxonomy.term_id = %d
			AND posts.post_type = 'dress'
			AND posts.post_status = 'publish'
			ORDER BY terms.name ASC",
		$category_term_id
	);

	$results = $wpdb->get_results( $query );

	if ( empty( $results ) ) {
			return array();
	}

	return array_map(
		function ( $result ) {
			return new WP_Term(
				(object) array(
					'term_id'  => $result->term_id,
					'name'     => $result->name,
					'slug'     => $result->slug,
					'taxonomy' => 'dress_tag',
				)
			);
		},
		$results
	);
}

function loveforever_get_product_title( $product_id ) {
	$catalog_titles = array(
		'wedding' => 'Свадебное платье',
		'evening' => 'Вечернее платье',
		'prom'    => 'Выпускное платье',
	);
	$categories     = get_the_terms( $product_id, 'dress_category' );

	if ( is_wp_error( $categories ) || empty( $categories ) ) {
		return get_the_title( $product_id );
	}

	$categories = array_values(
		array_filter(
			$categories,
			function ( $category ) use ( $catalog_titles ) {
				return in_array( $category->slug, array_keys( $catalog_titles ) );
			}
		)
	);

	if ( count( $categories ) > 1 ) {
		foreach ( $categories as $category ) {
			if ( 'evening' === $category->slug ) {
				return $catalog_titles['evening'] . ' ' . get_the_title( $product_id );
			}
		}
	}

	return $catalog_titles[ $categories[0]->slug ] . ' ' . get_the_title( $product_id );
}

function loveforever_get_product_images( $product_id ) {
	$images = get_field( 'images', $product_id );

	if ( ! empty( $images ) && has_post_thumbnail( $product_id ) ) {
		array_unshift(
			$images,
			array(
				'image' => array(
					'ID'  => get_post_thumbnail_id( $product_id ),
					'url' => get_the_post_thumbnail_url( $product_id, 'full' ),
					'alt' => '',
				),
			)
		);

		return array_filter( $images, fn( $item ) => ! empty( $item['image'] ) );
	}

	return $images;
}

function loveforever_get_filter_terms_for_dress_category( $taxonomy = '', $category_id = null ) {
	if ( '' === $taxonomy ) {
		return array();
	}

	if ( ! $category_id ) {
		$current_category = get_queried_object();

		if ( $current_category && $current_category instanceof WP_Term ) {
			$category_id = $current_category->term_id;
		}
	}

	if ( ! $category_id ) {
		return array();
	}

	$posts = get_posts(
		array(
			'post_type'   => 'dress',
			'numberposts' => -1,
			'fields'      => 'ids',
			'tax_query'   => array(
				array(
					'taxonomy' => 'dress_category',
					'field'    => 'term_id',
					'terms'    => $category_id,
				),
			),
		)
	);

	if ( empty( $posts ) ) {
		return array();
	}

	$silhouettes = wp_get_object_terms(
		$posts,
		$taxonomy,
		array(
			'orderby' => 'name',
			'order'   => 'ASC',
		)
	);

	return $silhouettes;
}

/**
 * Get silhouette terms associated with posts in the dress_category
 *
 * @param int|null $category_id Optional category ID to check against. If null, uses current term.
 * @return array Array of WP_Term objects for silhouette taxonomy
 */
function loveforever_get_silhouettes_for_dress_category( $category_id = null ) {
	return loveforever_get_filter_terms_for_dress_category( 'silhouette', $category_id );
}

/**
 * Get brand terms associated with posts in the dress_category
 *
 * @param int|null $category_id Optional category ID to check against. If null, uses current term.
 * @return array Array of WP_Term objects for silhouette taxonomy
 */
function loveforever_get_brands_for_dress_category( $category_id = null ) {
	return loveforever_get_filter_terms_for_dress_category( 'brand', $category_id );
}

/**
 * Get style terms associated with posts in the dress_category
 *
 * @param int|null $category_id Optional category ID to check against. If null, uses current term.
 * @return array Array of WP_Term objects for silhouette taxonomy
 */
function loveforever_get_styles_for_dress_category( $category_id = null ) {
	return loveforever_get_filter_terms_for_dress_category( 'style', $category_id );
}

/**
 * Get color terms associated with posts in the dress_category
 *
 * @param int|null $category_id Optional category ID to check against. If null, uses current term.
 * @return array Array of WP_Term objects for silhouette taxonomy
 */
function loveforever_get_colors_for_dress_category( $category_id = null ) {
	return loveforever_get_filter_terms_for_dress_category( 'color', $category_id );
}

function loveforever_get_product_badge_text( $product_id ) {
	$badge_values     = array(
		'new'     => 'Новинка',
		'popular' => 'Популярное',
	);
	$badge_text_value = get_field( 'badge', $product_id );

	if ( $badge_text_value ) {
		return $badge_values[ $badge_text_value ];
	}

	$has_discount = get_field( 'has_discount', $product_id );

	if ( $has_discount ) {
		return 'Скидка';
	}

	return '';
}

function loveforever_get_product_discount( $product_id ) {
	$has_discount = get_field( 'has_discount', $product_id );

	if ( ! $has_discount ) {
		return 0;
	}

	return absint( get_field( 'discount_percent', $product_id ) );
}

function loveforever_get_product_root_category( int $product_id ) {
	$categories_names = array(
		'wedding',
		'evening',
		'prom',
	);
	$dress_categories = get_the_terms( $product_id, 'dress_category' );

	if ( is_wp_error( $dress_categories ) || ! $dress_categories ) {
		return get_term_by( 'slug', 'wedding', 'dress_category' );
	}

	foreach ( $dress_categories as $term ) {
		if ( 0 === $term->parent && in_array( $term->slug, $categories_names, true ) ) {
			return $term;
		}
	}

	return get_term_by( 'slug', 'wedding', 'dress_category' );
}

function loveforever_format_filter_link_for_tag( WP_Term $tag, int $product_id ) {
	$root_dress_category = loveforever_get_product_root_category( $product_id );

	$postfix = '';

	if ( $tag->taxonomy !== 'silhouette' ) {
		$postfix = '[]';
	}

	return get_term_link( $root_dress_category ) . '?' . $tag->taxonomy . $postfix . '=' . $tag->term_id;
}
