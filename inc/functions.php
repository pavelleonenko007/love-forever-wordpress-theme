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

function loveforever_is_valid_fitting_datetime( $datetime, $fitting_type ) {
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
	$slot_availability = Fitting_Slots::check_slot_availability( $date, $time, $fitting_type );

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

function loveforever_get_recently_viewed_products() {
	if ( empty( $_COOKIE['recently_viewed'] ) ) {
		return array();
	}

	return array_reverse( explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['recently_viewed'] ) ) ) );
}

function loveforever_update_recently_viewed_products( $product_id ) {
	if ( empty( $_COOKIE['recently_viewed'] ) ) {
		setcookie( 'recently_viewed', $product_id, time() + 60 * 60 * 24 * 30, '/' );
	} else {
		$recently_viewed_ids = explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['recently_viewed'] ) ) );
		$recently_viewed_ids = array_filter(
			$recently_viewed_ids,
			function ( $id ) use ( $product_id ) {
				return $id !== $product_id;
			}
		);

		$recently_viewed_ids[] = $product_id;
		setcookie( 'recently_viewed', implode( ',', $recently_viewed_ids ), time() + 60 * 60 * 24 * 30, '/' );
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
