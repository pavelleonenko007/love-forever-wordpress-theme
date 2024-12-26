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
