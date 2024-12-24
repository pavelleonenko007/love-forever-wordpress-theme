<?php
/**
 * Class Fitting_Slots
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

class Fitting_Slots {
	public static function get_available_slots( $date, $current_time = null ) {
		$slots    = self::generate_slots( $date, $current_time );
		$bookings = self::get_bookings( $date );
		return self::apply_bookings_to_slots( $slots, $bookings );
	}

	private static function generate_slots( $date, $current_time = null ) {
		$slots      = array();
		$start_time = strtotime( '10:00' );
		$end_time   = strtotime( '21:00' );
		$interval   = 30 * 60; // 30 minutes

		// Если дата - сегодня и текущее время не указано, используем текущее время
		if ( $current_time === null && gmdate( 'Y-m-d' ) === $date ) {
			$current_time = time();
		} elseif ( $current_time === null ) {
			$current_time = strtotime( '00:00', strtotime( $date ) );
		}

		while ( $start_time < $end_time ) {
			$time = gmdate( 'H:i', $start_time );
			// Проверяем, не прошло ли уже это время
			if ( strtotime( $date . ' ' . $time ) > $current_time ) {
				$slots[ $time ] = self::get_max_fittings_for_slot( $date, $time );
			}
			$start_time += $interval;
		}

		return $slots;
	}

	private static function get_max_fittings_for_slot( $date, $time ) {
		$day_of_week   = gmdate( 'w', strtotime( $date ) );
		$default_slots = 2; // Значение по умолчанию

		// Здесь можно добавить логику для получения настроек количества слотов из базы данных
		// Например:
		// $settings = get_option('salon_love_fitting_settings');
		// return isset($settings[$day_of_week][$time]) ? $settings[$day_of_week][$time] : $default_slots;

		return $default_slots;
	}

	private static function get_bookings( $date ) {
		$args = array(
			'post_type'      => 'fitting',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'fitting_time',
					'value'   => $date,
					'compare' => 'LIKE',
				),
			),
		);

		$query    = new WP_Query( $args );
		$bookings = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$fitting_date = get_field( 'fitting_time' );
				$fitting_type = get_field( 'fitting_type' );
				$time         = gmdate( 'H:i', strtotime( $fitting_date ) );
				$duration     = self::get_fitting_duration( $fitting_type );
				$bookings[]   = array(
					'time'     => $time,
					'duration' => $duration,
				);
			}
		}
		wp_reset_postdata();

		return $bookings;
	}

	public static function check_slot_availability( $date, $time, $fitting_type ) {
		$slots = self::get_available_slots( $date );

		if ( ! isset( $slots[ $time ] ) || $slots[ $time ] <= 0 ) {
				return 'Выбранное время уже занято';
		}

		$duration     = self::get_fitting_duration( $fitting_type );
		$end_time     = strtotime( "+{$duration} minutes", strtotime( $date . ' ' . $time ) );
		$current_time = strtotime( $date . ' ' . $time );

		while ( $current_time < $end_time ) {
				$check_time = gmdate( 'H:i', $current_time );
			if ( ! isset( $slots[ $check_time ] ) || $slots[ $check_time ] <= 0 ) {
					return 'Недостаточно свободного времени для выбранного типа примерки';
			}
				$current_time = strtotime( '+30 minutes', $current_time );
		}

		return true;
	}

	private static function get_fitting_duration( $fitting_type ) {
		if ( in_array( 'wedding', $fitting_type ) ) {
			return 90; // 1.5 часа для свадебных платьев
		} elseif ( in_array( 'evening', $fitting_type ) || in_array( 'prom', $fitting_type ) ) {
			return 60; // 1 час для вечерних и выпускных платьев
		} else {
			return 60; // По умолчанию 1 час
		}
	}

	private static function apply_bookings_to_slots( $slots, $bookings ) {
		foreach ( $bookings as $booking ) {
			$start_time   = strtotime( $booking['time'] );
			$end_time     = $start_time + ( $booking['duration'] * 60 );
			$current_time = $start_time;

			while ( $current_time < $end_time ) {
				$time = gmdate( 'H:i', $current_time );
				if ( isset( $slots[ $time ] ) ) {
					--$slots[ $time ];
				}
				$current_time += 30 * 60; // Переход к следующему получасовому слоту
			}
		}

		return $slots;
	}
}
