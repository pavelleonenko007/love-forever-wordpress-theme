<?php
/**
 * Class Fitting_Slots
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

class Fitting_Slots {
	private static $min_fitting_duration = 60;

	public static function get_slots_range( $start_date, $end_date ) {
		$slots        = array();
		$current_date = new DateTime( $start_date );
		$end          = new DateTime( $end_date );
		$current_time = current_time( 'timestamp' );

		while ( $current_date <= $end ) {
				$date           = $current_date->format( 'Y-m-d' );
				$slots[ $date ] = self::get_day_slots( $date, $current_time );
				$current_date->modify( '+1 day' );
		}

		return $slots;
	}

	public static function get_nearest_available_date( $max_days_ahead = 60 ) {
		$current_time = current_time( 'timestamp' );
		$start_date   = date( 'Y-m-d', $current_time );
		$current_date = new DateTime( $start_date );
		$end_date     = ( new DateTime( $start_date ) )->modify( "+{$max_days_ahead} days" );

		while ( $current_date <= $end_date ) {
			$date      = $current_date->format( 'Y-m-d' );
			$day_slots = self::get_day_slots( $date, $current_time );

			// Check if there are any available slots for this day
			foreach ( $day_slots as $time => $slot ) {
				if ( $slot['available'] > 0 && ! $slot['is_booked'] ) {
					// Check if the slot is not in the past for today
					if ( $date === $start_date ) {
						$slot_time = strtotime( $date . ' ' . $time );
						if ( $slot_time > $current_time ) {
							return $date;
						}
					} else {
						return $date;
					}
				}
			}

			$current_date->modify( '+1 day' );
		}

		return null;
	}

	public static function get_day_slots( $date, $current_time = null, $exclude_fitting_id = null ) {
		$available_slots = self::get_available_slots( $date, $current_time, $exclude_fitting_id );
		$all_slots       = self::generate_all_slots( $date );

		foreach ( $all_slots as $time => &$slot ) {
			if ( $current_time && strtotime( $date . ' ' . $time ) <= $current_time ) {
				$slot['available'] = 0;
				$slot['is_booked'] = true;
			} else {
				$slot['available'] = isset( $available_slots[ $time ] ) ? $available_slots[ $time ] : 0;
				$slot['is_booked'] = $slot['available'] < $slot['max_fittings'];
			}
		}

		return $all_slots;
	}

	public static function generate_all_slots( $date ) {
		$slots      = array();
		$start_time = strtotime( '10:00' );
		$end_time   = strtotime( '21:00' );
        $use_one_hour_interval = get_field( 'fitting_slots_interval', 'option' );

        $interval = $use_one_hour_interval ? 60 * 60 : 30 * 60; // 60 or 30 minutes

		while ( $start_time <= $end_time ) {
				$time           = gmdate( 'H:i', $start_time );
				$slots[ $time ] = array(
					'max_fittings' => self::get_max_fittings_for_slot( $date, $time ),
					'available'    => 0,
					'is_booked'    => false,
				);
				$start_time    += $interval;
		}

		return $slots;
	}

	public static function get_available_slots( $date, $current_time = null, $exclude_fitting_id = null ) {
		$slots               = self::generate_slots( $date, $current_time );
		$bookings            = self::get_bookings( $date, $exclude_fitting_id );
		$slots_with_bookings = self::apply_bookings_to_slots( $slots, $bookings );
		return self::apply_minimum_duration_rule( $slots_with_bookings );
	}

	private static function generate_slots( $date, $current_time = null ) {
		$slots      = array();
		$start_time = strtotime( '10:00' );
		$end_time   = strtotime( '21:00' );
        $use_one_hour_interval = get_field( 'fitting_slots_interval', 'option' );

        $interval = $use_one_hour_interval ? 60 * 60 : 30 * 60; // 60 or 30 minutes

		// Если дата - сегодня и текущее время не указано, используем текущее время
		if ( $current_time === null && gmdate( 'Y-m-d' ) === $date ) {
			$current_time = time();
		} elseif ( $current_time === null ) {
			$current_time = strtotime( '00:00', strtotime( $date ) );
		}

		while ( $start_time <= $end_time ) {
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

		$forced_fittings_number_by_day = ! empty( get_field( 'forced_fittings_number_by_day', 'option' ) ) ? get_field( 'forced_fittings_number_by_day', 'option' ) : array();

		if ( ! empty( $forced_fittings_number_by_day ) ) {
			foreach ( $forced_fittings_number_by_day as $forced_fitting ) {
				$current_date        = strtotime( $date . ' ' . $time );
				$forced_fitting_date = strtotime( $forced_fitting['date'] );

				if ( $current_date === $forced_fitting_date ) {
					return $forced_fitting['fittings_number'];
				}
			}
		}

		$fittings_number_by_day_of_week = ! empty( get_field( 'fittings_number_by_day_of_week', 'option' ) ) ? get_field( 'fittings_number_by_day_of_week', 'option' ) : array();

		$formatted_fittings = array();
		if ( ! empty( $fittings_number_by_day_of_week ) ) {
			foreach ( $fittings_number_by_day_of_week as $fitting ) {
				if ( ! empty( $fitting['special_hours_capacity'] ) ) {
					$special_hours_slots                       = loveforever_format_special_hours( $fitting['special_hours_capacity'] );
					$formatted_fittings[ $fitting['weekday'] ] = isset( $special_hours_slots[ $time ] ) ? (int) $special_hours_slots[ $time ] : (int) $fitting['fittings_number'];
				} else {
					$formatted_fittings[ $fitting['weekday'] ] = (int) $fitting['fittings_number'];
				}
			}
		}

		return isset( $formatted_fittings[ $day_of_week ] ) ? $formatted_fittings[ $day_of_week ] : $default_slots;
	}

	private static function get_bookings( $date, $exclude_fitting_id = null ) {
		$start_of_day = $date . ' 00:00:00';
		$end_of_day   = $date . ' 23:59:59';

		$args = array(
			'post_type'      => 'fitting',
			'posts_per_page' => -1,
			// 'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'     => 'fitting_step',
					'value'   => 'delivery',
					'compare' => '!=',
				),
				array(
					'key'     => 'fitting_time',
					'value'   => array( $start_of_day, $end_of_day ),
					'compare' => 'BETWEEN',
					'type'    => 'DATETIME',
				),
			),
		);

		if ( ! empty( $exclude_fitting_id ) ) {
			$args['post__not_in'] = array( $exclude_fitting_id );
		}

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

	public static function check_slot_availability( $date, $time, $fitting_type, $exclude_fitting_id = null ) {
		$slots = self::get_available_slots( $date, null, $exclude_fitting_id );

		if ( ! isset( $slots[ $time ] ) || $slots[ $time ] <= 0 ) {
				return 'Выбранное время уже занято';
		}

		$duration     = self::get_fitting_duration( $fitting_type );
		$end_time     = strtotime( "+{$duration} minutes", strtotime( $date . ' ' . $time ) );
		$current_time = strtotime( $date . ' ' . $time );

		while ( $current_time < $end_time ) {
			$check_time = gmdate( 'H:i', $current_time );
			if ( ! isset( $slots[ $check_time ] ) || $slots[ $check_time ] <= 0 ) {
					return 'Недостаточно свободного времени для выбранного типа примерки' . $exclude_fitting_id;
			}
			$current_time = strtotime( '+30 minutes', $current_time );
		}

		return true;
	}

	private static function get_fitting_duration( $fitting_type ) {
		$wedding_duration = 90;
		$default_duration = 60;

		return ( is_array( $fitting_type ) && in_array( 'wedding', $fitting_type ) ) || 'wedding' === $fitting_type
			? $wedding_duration
			: $default_duration;
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

	private static function apply_minimum_duration_rule( $slots ) {
		$min_slots_needed = self::$min_fitting_duration / 30;
		$formatted_slots  = array();
		$slot_times       = array_keys( $slots );

		foreach ( $slot_times as $index => $time ) {
			$current_count = $slots[ $time ];

			if ( $current_count <= 0 ) {
				$formatted_slots[ $time ] = 0;
				continue;
			}

			if ( $current_count >= $min_slots_needed ) {
				$formatted_slots[ $time ] = $current_count;
				continue;
			}

			// Check next slot availability
			$next_count               = isset( $slot_times[ $index + 1 ] ) && isset( $slots[ $slot_times[ $index + 1 ] ] ) ? $slots[ $slot_times[ $index + 1 ] ] : 0;
			$formatted_slots[ $time ] = ( $current_count + $next_count >= $min_slots_needed ) ? $current_count : 0;
		}

		return $formatted_slots;
	}
}
