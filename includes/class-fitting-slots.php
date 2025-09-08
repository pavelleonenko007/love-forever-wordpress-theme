<?php
/**
 * Class Fitting_Slots
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * TODO: Переписать это нечто на singleton
 */

class Fitting_Slots {
	private static $min_fitting_duration = 60;

	private static function get_slot_duration() {
		return get_field( 'fitting_slots_interval', 'option' ) ? absint( get_field( 'fitting_slots_interval', 'option' ) ) : self::DEFAULT_SLOT_DURATION;
	}

	public function get_slots_range( $start_date, $end_date ) {
		$slots        = array();
		$current_date = new DateTime( $start_date );
		$end          = new DateTime( $end_date );
		$current_time = current_time( 'timestamp' );

		while ( $current_date <= $end ) {
				$date           = $current_date->format( 'Y-m-d' );
				$slots[ $date ] = $this->get_day_slots( $date, $current_time );
				$current_date->modify( '+1 day' );
		}

		return $slots;
	}

	public function get_nearest_available_date( $max_days_ahead = 60 ) {
		$current_time = current_time( 'timestamp' );
		$start_date   = wp_date( 'Y-m-d', $current_time );
		$current_date = new DateTime( $start_date );
		$end_date     = ( new DateTime( $start_date ) )->modify( "+{$max_days_ahead} days" );

		while ( $current_date <= $end_date ) {
			$date      = $current_date->format( 'Y-m-d' );
			$day_slots = $this->get_day_slots( $date, $current_time );

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

	public function get_day_slots( $date, $exclude_fitting_id = null ) {
		$available_slots = $this->get_available_slots( $date, $current_time, $exclude_fitting_id );
		$all_slots       = self::generate_all_slots( $date );

		foreach ( $all_slots as $time => &$slot ) {
			if ( $current_time && ( new DateTime( $date . ' ' . $time, $this->timezone ) )->getTimestamp() <= $current_time ) {
				$slot['available'] = 0;
				$slot['is_booked'] = true;
			} else {
				$slot['available'] = isset( $available_slots[ $time ] ) ? $available_slots[ $time ] : 0;
				$slot['is_booked'] = $slot['available'] < $slot['max_fittings'];
			}
		}

		// echo '<pre>';
		// var_dump( $available_slots, $all_slots );
		// echo '</pre>';

		return $all_slots;
	}

	public static function generate_all_slots( $date ) {
		$timezone = wp_timezone();

		$slots         = array();
		$start_time    = ( new DateTime( '10:00', $timezone ) )->getTimestamp();
		$end_time      = ( new DateTime( '21:00', $timezone ) )->getTimestamp();
		$slot_duration = self::get_slot_duration();

		$interval = $slot_duration * 60;

		while ( $start_time <= $end_time ) {
				$time           = wp_date( 'H:i', $start_time );
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
		$slots                        = self::generate_slots( $date, $current_time );
		$bookings                     = self::get_bookings( $date, $exclude_fitting_id );
		$slots_with_bookings          = self::apply_bookings_to_slots( $slots, $bookings );
		$slots_after_minimum_duration = self::apply_minimum_duration_rule( $slots_with_bookings );

		// echo '<pre>';
		// var_dump( $slots, $bookings, $slots_with_bookings, $slots_after_minimum_duration );
		// echo '</pre>';

		return $slots_after_minimum_duration;
	}

	private static function generate_slots( $date, $current_time = null ) {
		$timezone = wp_timezone();

		$slots         = array();
		$start_time    = ( new DateTime( '10:00', $timezone ) )->getTimestamp();
		$end_time      = ( new DateTime( '21:00', $timezone ) )->getTimestamp();
		$slot_duration = self::get_slot_duration();

		$interval = $slot_duration * 60;

		// Если дата - сегодня и текущее время не указано, используем текущее время
		if ( $current_time === null && wp_date( 'Y-m-d' ) === $date ) {
			$current_time = ( new DateTime( 'now', $timezone ) )->getTimestamp();
		} elseif ( $current_time === null ) {
			$current_time = ( new DateTime( $date . '00:00', $timezone ) )->getTimestamp();
		}

		while ( $start_time <= $end_time ) {
			$time = wp_date( 'H:i', $start_time );
			// Проверяем, не прошло ли уже это время
			if ( ( new DateTime( $date . ' ' . $time, $timezone ) )->getTimestamp() > $current_time ) {
				$slots[ $time ] = self::get_max_fittings_for_slot( $date, $time );
			}
			$start_time += $interval;
		}

		return $slots;
	}

	private static function get_max_fittings_for_slot( $date, $time ) {
		$timezone = wp_timezone();

		$day_of_week   = wp_date( 'w', ( new DateTime( $date, $timezone ) )->getTimestamp() );
		$default_slots = 2; // Значение по умолчанию

		$forced_fittings_number_by_day = ! empty( get_field( 'forced_fittings_number_by_day', 'option' ) ) ? get_field( 'forced_fittings_number_by_day', 'option' ) : array();

		if ( ! empty( $forced_fittings_number_by_day ) ) {
			foreach ( $forced_fittings_number_by_day as $forced_fitting ) {
				$current_date        = ( new DateTime( $date . ' ' . $time, $timezone ) )->getTimestamp();
				$forced_fitting_date = ( new DateTime( $forced_fitting['date'], $timezone ) )->getTimestamp();

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
		$timezone = wp_timezone();

		$start_of_day = $date . ' 00:00:00';
		$end_of_day   = $date . ' 23:59:59';

		$args = array(
			'post_type'      => 'fitting',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'relation' => 'OR',
					array(
						'key'     => 'fitting_step',
						'value'   => 'delivery',
						'compare' => '!=',
					),
					array(
						'key'     => 'fitting_step',
						'compare' => 'NOT EXISTS',
					),
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

		foreach ( $query->posts as $fitting_id ) {
			$fitting_date = get_field( 'fitting_time', $fitting_id );
			$fitting_type = get_field( 'fitting_type', $fitting_id );
			$time         = wp_date( 'H:i', ( new DateTime( $fitting_date, $timezone ) )->getTimestamp() );
			$duration     = self::get_fitting_duration( $fitting_type );
			$bookings[]   = array(
				'time'     => $time,
				'duration' => $duration,
			);
		}

		return $bookings;
	}

	public static function check_slot_availability( $date, $time, $fitting_type, $exclude_fitting_id = null ) {
		$slots = self::get_available_slots( $date, null, $exclude_fitting_id );

		if ( ! isset( $slots[ $time ] ) || $slots[ $time ] <= 0 ) {
				return 'Выбранное время уже занято';
		}

		if ( '21:00' === $time && $slots[ $time ] > 0 ) {
			return true;
		}

		$timezone     = wp_timezone();
		$datetime     = new DateTime( $date . ' ' . $time, $timezone );
		$current_time = $datetime->getTimestamp();

		$duration = self::get_fitting_duration( $fitting_type );
		$datetime->modify( "+{$duration} minutes" );
		$end_time = $datetime->getTimestamp();

		$slot_duration = self::get_slot_duration();
		$interval      = $slot_duration * 60;

		while ( $current_time < $end_time ) {
			$check_time = wp_date( 'H:i', $current_time );

			if ( ! isset( $slots[ $check_time ] ) || $slots[ $check_time ] <= 0 ) {
					return 'Недостаточно свободного времени для выбранного типа примерки';
			}

			$current_time += $interval;
		}

		return true;
	}

	private static function get_fitting_duration( $fitting_type ) {
		$wedding_duration = 90;
		$default_duration = 60;
		$slot_duration    = self::get_slot_duration();

		if ( 60 === $slot_duration ) {
			return $default_duration;
		}

		return ( is_array( $fitting_type ) && in_array( 'wedding', $fitting_type ) ) || 'wedding' === $fitting_type
			? $wedding_duration
			: $default_duration;
	}

	private static function apply_bookings_to_slots( $slots, $bookings ) {
		$timezone      = wp_timezone();
		$slot_duration = self::get_slot_duration();

		$interval = $slot_duration * 60;

		foreach ( $bookings as $booking ) {
			$start_time   = ( new DateTime( $booking['time'], $timezone ) )->getTimestamp();
			$end_time     = $start_time + ( $booking['duration'] * 60 );
			$current_time = $start_time;

			while ( $current_time < $end_time ) {
				$time = wp_date( 'H:i', $current_time );

				if ( isset( $slots[ $time ] ) ) {
					--$slots[ $time ];
					$current_time += $interval;
					continue;
				}

				$slot_times       = array_keys( $slots );
				$count_slot_times = count( $slot_times );
				$time_index       = array_search( $time, $slot_times, true );
				$in_range         = false;
				$previous_slot    = null;
				$greater_slot     = null;

				if ( false === $time_index ) {
					// Check if $time is between any two slot times.
					for ( $i = 0; $i < $count_slot_times - 1; $i++ ) {
						if ( empty( $slot_times[ $i ] ) || empty( $slot_times[ $i + 1 ] ) ) {
							break;
						}

						$t1 = ( new DateTime( $slot_times[ $i ], $timezone ) )->getTimestamp();
						$t2 = ( new DateTime( $slot_times[ $i + 1 ], $timezone ) )->getTimestamp();
						$tt = ( new DateTime( $time, $timezone ) )->getTimestamp();

						if ( $tt > $t1 && $tt < $t2 ) {
							$previous_slot = $slot_times[ $i ];
							$greater_slot  = $slot_times[ $i + 1 ];
							$in_range      = true;
							break;
						}
					}
				}

				if ( $in_range ) {
					--$slots[ $previous_slot ];
					--$slots[ $greater_slot ];
				}

				$current_time += $interval; // Переход к следующему получасовому слоту
			}
		}

		return $slots;
	}

	private static function apply_minimum_duration_rule( $slots ) {
		$slot_duration    = self::get_slot_duration();
		$min_slots_needed = $slot_duration ? self::$min_fitting_duration / 60 : self::$min_fitting_duration / 30;
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
			$next_count = isset( $slot_times[ $index + 1 ] ) && isset( $slots[ $slot_times[ $index + 1 ] ] ) ? $slots[ $slot_times[ $index + 1 ] ] : 0;

			if ( '21:00' === $time && $next_count === 0 ) {
				$formatted_slots[ $time ] = $current_count;
				continue;
			}

			$formatted_slots[ $time ] = ( $current_count + $next_count >= $min_slots_needed ) ? $current_count : 0;
		}

		return $formatted_slots;
	}
}
