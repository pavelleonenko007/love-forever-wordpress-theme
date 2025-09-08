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

class Fitting_Slots_Manager {
	const DEFAULT_SLOT_DURATION = 60;
	const MAX_FITTING_ROOMS     = 2;
	const MIN_FITTING_DURATION  = 60;
	const SALON_OPEN_HOUR       = 10;
	const SALON_CLOSE_HOUR      = 21;

	private static $instance = null;
	private $timezone;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->timezone = wp_timezone();
	}

	private function get_slot_duration() {
		return get_field( 'fitting_slots_interval', 'option' ) ? absint( get_field( 'fitting_slots_interval', 'option' ) ) : self::DEFAULT_SLOT_DURATION;
	}

	public static function get_slots_range( $start_date, $end_date ) {
		$slots        = array();
		$current_date = new DateTime( $start_date );
		$end          = new DateTime( $end_date );
		$current_time = current_time( 'timestamp' );

		while ( $current_date <= $end ) {
				$date           = $current_date->format( 'Y-m-d' );
				$slots[ $date ] = self::get_slots_for_date( $date );
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
			$day_slots = $this->get_slots_for_date( $date );

			// Check if there are any available slots for this day
			foreach ( $day_slots as $time => $slot ) {
				if ( $slot['available_for_booking'] > 0 ) {
					// Check if the slot is not in the past for today
					if ( $date === $start_date ) {
						$slot_time = ( new DateTime( $date . ' ' . $time, $this->timezone ) )->getTimestamp();
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

	public function get_slots_for_date( $date, $fitting_type = 'evening', $exclude_booking_id = null ) {
		$slots                        = $this->generate_slots_for_date( $date );
		$bookings                     = $this->get_bookings_for_date( $date, $exclude_booking_id );
		$slots_with_bookings          = $this->apply_bookings_to_slots( $slots, $bookings );
		$slots_after_fitting_duration = $this->apply_fitting_duration_rule( $slots_with_bookings, $fitting_type );

		return $slots_after_fitting_duration;
	}

	public function get_available_slots( $date, $fitting_type = 'evening', $exclude_booking_id = null ) {
		$slots            = $this->get_slots_for_date( $date, $fitting_type, $exclude_booking_id );
		$current_datetime = new DateTime( 'now', $this->timezone );

		return array_map(
			function ( $slot ) use ( $current_datetime ) {
				if ( $slot['timestamp'] <= $current_datetime->getTimestamp() ) {
					$slot['available']             = 0;
					$slot['available_for_booking'] = 0;
				}
				return $slot;
			},
			$slots
		);
	}

	private function generate_slots_for_date( $date ) {
		$slots         = array();
		$start_time    = ( new DateTime( $date . ' ' . self::SALON_OPEN_HOUR . ':00', $this->timezone ) )->getTimestamp();
		$end_time      = ( new DateTime( $date . ' ' . self::SALON_CLOSE_HOUR . ':00', $this->timezone ) )->getTimestamp();
		$slot_duration = $this->get_slot_duration();

		$interval = $slot_duration * 60;

		$loop_count = 0;
		while ( $start_time <= $end_time ) {
			++$loop_count;
			$time           = wp_date( 'H:i', $start_time );
			$slots[ $time ] = array(
				'time'                  => $time,
				'datetime'              => $date . ' ' . $time,
				'timestamp'             => ( new DateTime( $date . ' ' . $time, $this->timezone ) )->getTimestamp(),
				'max_fittings'          => $this->get_max_fittings_for_slot( $date, $time ),
				'available_for_booking' => $this->get_max_fittings_for_slot( $date, $time ),
				'available'             => $this->get_max_fittings_for_slot( $date, $time ),
				'is_booked'             => false,
			);
			$start_time    += $interval;

			// Защита от бесконечного цикла
			if ( $loop_count > 50 ) {
				error_log( "Loop count exceeded 50, breaking. Current start_time = $start_time, end_time = $end_time" );
				break;
			}
		}

		return $slots;
	}

	private function get_max_fittings_for_slot( $date, $time ) {
		$day_of_week   = wp_date( 'w', ( new DateTime( $date, $this->timezone ) )->getTimestamp() );
		$default_slots = 2; // Значение по умолчанию

		$forced_fittings_number_by_day = ! empty( get_field( 'forced_fittings_number_by_day', 'option' ) ) ? get_field( 'forced_fittings_number_by_day', 'option' ) : array();

		if ( ! empty( $forced_fittings_number_by_day ) ) {
			foreach ( $forced_fittings_number_by_day as $forced_fitting ) {
				$current_date        = ( new DateTime( $date . ' ' . $time, $this->timezone ) )->getTimestamp();
				$forced_fitting_date = ( new DateTime( $forced_fitting['date'], $this->timezone ) )->getTimestamp();

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

	private function get_bookings_for_date( $date, $exclude_booking_id = null ) {
		$start_of_day = $date . ' 00:00:00';
		$end_of_day   = $date . ' 23:59:59';

		$args = array(
			'post_type'      => 'fitting',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => 'fitting_time',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
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

		if ( ! empty( $exclude_booking_id ) ) {
			$args['exclude'] = array( $exclude_booking_id );
		}

		$booking_ids = get_posts( $args );
		$bookings    = array_map(
			function ( $id ) {
				$fitting_date     = get_field( 'fitting_time', $id );
				$fitting_type     = get_field( 'fitting_type', $id );
				$fitting_datetime = new DateTime( $fitting_date, $this->timezone );
				$time             = $fitting_datetime->format( 'H:i' );
				$duration         = $this->get_fitting_duration( $fitting_type );

				return array(
					'id'        => $id,
					'time'      => $time,
					'datetime'  => $fitting_date,
					'timestamp' => $fitting_datetime->getTimestamp(),
					'duration'  => $duration,
				);
			},
			$booking_ids
		);

		return $bookings;
	}

	public function is_slot_available( $date, $time, $fitting_type, $exclude_booking_id = null ) {
		$slots = $this->get_slots_for_date( $date, $fitting_type, $exclude_booking_id );

		if ( ! isset( $slots[ $time ] ) || $slots[ $time ]['available_for_booking'] < 1 ) {
				return new WP_Error( 'slot_not_available', 'Выбранное время недоступно для записи' );
		}

		if ( '21:00' === $time && $slots[ $time ]['available_for_booking'] > 0 ) {
			return true;
		}

		$datetime     = new DateTime( $date . ' ' . $time, $this->timezone );
		$current_time = $datetime->getTimestamp();

		$duration = $this->get_fitting_duration( $fitting_type );
		$datetime->modify( "+{$duration} minutes" );

		$end_time = $datetime->getTimestamp();

		$slot_duration = $this->get_slot_duration();
		$interval      = $slot_duration * 60;

		while ( $current_time < $end_time ) {
			$check_time = wp_date( 'H:i', $current_time );

			if ( ! isset( $slots[ $check_time ] ) || $slots[ $check_time ]['available'] < 1 ) {
					return new WP_Error( 'not_enough_time', 'Недостаточно свободного времени для выбранного типа примерки' );
			}

			$current_time += $interval;
		}

		return true;
	}

	private function get_fitting_duration( $fitting_type ) {
		$wedding_duration = 90;
		$slot_duration    = $this->get_slot_duration();

		if ( self::DEFAULT_SLOT_DURATION === $slot_duration ) {
			return self::DEFAULT_SLOT_DURATION;
		}

		return ( is_array( $fitting_type ) && in_array( 'wedding', $fitting_type ) ) || 'wedding' === $fitting_type
			? $wedding_duration
			: self::DEFAULT_SLOT_DURATION;
	}

	private function apply_bookings_to_slots( $slots, $bookings ) {
		$slot_duration = $this->get_slot_duration();
		$interval      = $slot_duration * 60;

		foreach ( $bookings as $booking ) {
			$start_time   = $booking['timestamp'];
			$end_time     = $start_time + ( $booking['duration'] * 60 );
			$current_time = $start_time;

			while ( $current_time < $end_time ) {
				$time = wp_date( 'H:i', $current_time );

				if ( isset( $slots[ $time ] ) ) {
					if ( $slots[ $time ]['available'] > 0 ) {
						--$slots[ $time ]['available'];
						--$slots[ $time ]['available_for_booking'];
					}
					$slots[ $time ]['is_booked'] = true;
					$current_time               += $interval;
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

						$t1 = ( new DateTime( $slot_times[ $i ], $this->timezone ) )->getTimestamp();
						$t2 = ( new DateTime( $slot_times[ $i + 1 ], $this->timezone ) )->getTimestamp();
						$tt = ( new DateTime( $time, $this->timezone ) )->getTimestamp();

						if ( $tt > $t1 && $tt < $t2 ) {
							$previous_slot = $slot_times[ $i ];
							$greater_slot  = $slot_times[ $i + 1 ];
							$in_range      = true;
							break;
						}
					}
				}

				if ( $in_range ) {
					if ( $slots[ $previous_slot ]['available'] > 0 ) {
						--$slots[ $previous_slot ]['available'];
					}

					if ( $slots[ $previous_slot ]['available_for_booking'] > 0 ) {
						--$slots[ $previous_slot ]['available_for_booking'];
					}

					if ( $slots[ $greater_slot ]['available'] > 0 ) {
						--$slots[ $greater_slot ]['available'];
					}

					if ( $slots[ $greater_slot ]['available_for_booking'] > 0 ) {
						--$slots[ $greater_slot ]['available_for_booking'];
					}

					$slots[ $previous_slot ]['is_booked'] = true;
					$slots[ $greater_slot ]['is_booked']  = true;
				}

				$current_time += $interval;
			}
		}

		return $slots;
	}

	// private function apply_minimum_duration_rule( $slots ) {
	// $slot_duration    = $this->get_slot_duration();
	// $min_slots_needed = self::MIN_FITTING_DURATION / $slot_duration;
	// $formatted_slots  = array();
	// $slot_times       = array_keys( $slots );

	// foreach ( $slot_times as $index => $time ) {
	// if ( $slots[ $time ]['available'] < 1 ) {
	// $formatted_slots[ $time ] = $slots[ $time ];
	// continue;
	// }

	// $can_accommodate_min_duration = true;

	// for ( $i = 1; $i < $min_slots_needed; $i++ ) {
	// $check_index = $index + $i;
	// if ( $check_index >= count( $slot_times ) ) {
	// break;
	// }

	// $check_time = $slot_times[ $check_index ];

	// if ( $slots[ $check_time ]['available'] < 1 ) {
	// $can_accommodate_min_duration = false;
	// break;
	// }

	// if ( $slots[ $check_time ]['available'] < $slots[ $time ]['available'] ) {
	// $slots[ $time ]['available'] = $slots[ $check_time ]['available'];
	// }
	// }

	// if ( ! $can_accommodate_min_duration ) {
	// $slots[ $time ]['available'] = 0;
	// }

	// $formatted_slots[ $time ] = $slots[ $time ];
	// }

	// return $formatted_slots;
	// }

	private function apply_fitting_duration_rule( $slots, $fitting_type ) {
		$slot_duration    = $this->get_slot_duration();
		$min_slots_needed = $this->get_fitting_duration( $fitting_type ) / $slot_duration;
		$formatted_slots  = array();
		$slot_times       = array_keys( $slots );

		foreach ( $slot_times as $index => $time ) {
			if ( $slots[ $time ]['available'] < 1 ) {
				$formatted_slots[ $time ] = $slots[ $time ];
				continue;
			}

			$can_accommodate_min_duration = true;

			for ( $i = 1; $i < $min_slots_needed; $i++ ) {
				$check_index = $index + $i;
				if ( $check_index >= count( $slot_times ) ) {
					break;
				}

				$check_time = $slot_times[ $check_index ];

				if ( $slots[ $check_time ]['available'] < 1 ) {
					$can_accommodate_min_duration = false;
					break;
				}

				if ( $slots[ $check_time ]['available'] < $slots[ $time ]['available'] ) {
					$slots[ $time ]['available_for_booking'] = $slots[ $check_time ]['available'];
				}
			}

			if ( ! $can_accommodate_min_duration ) {
				$slots[ $time ]['available_for_booking'] = 0;
			}

			$formatted_slots[ $time ] = $slots[ $time ];
		}

		return $formatted_slots;
	}

	/**
	 * Validates fitting datetime with comprehensive checks.
	 *
	 * @param string       $datetime           The datetime string to validate.
	 * @param string|array $fitting_type       The type of fitting.
	 * @param int|null     $exclude_booking_id ID of booking to exclude from validation.
	 * @return true|WP_Error Returns true if valid, WP_Error if invalid.
	 */
	public function validate_fitting_datetime( $datetime, $fitting_type, $exclude_booking_id = null ) {
		// Validate input parameters.
		if ( empty( $datetime ) ) {
			return new WP_Error( 'empty_datetime', 'Дата и время не могут быть пустыми' );
		}

		if ( empty( $fitting_type ) ) {
			return new WP_Error( 'empty_fitting_type', 'Тип примерки не может быть пустым' );
		}

		// Parse datetime with timezone.
		try {
			$datetime_obj = new DateTime( $datetime, $this->timezone );
		} catch ( Exception $e ) {
			return new WP_Error( 'invalid_datetime_format', 'Неверный формат даты и времени' );
		}

		$timestamp         = $datetime_obj->getTimestamp();
		$current_timestamp = ( new DateTime( 'now', $this->timezone ) )->getTimestamp();

		// Check if datetime is in the past.
		if ( $timestamp <= $current_timestamp ) {
			return new WP_Error( 'past_datetime', 'Время примерки не может быть в прошлом' );
		}

		// Check if datetime is within salon working hours.
		$hour = absint( $datetime_obj->format( 'G' ) );
		if ( $hour < self::SALON_OPEN_HOUR || $hour > self::SALON_CLOSE_HOUR ) {
			return new WP_Error(
				'outside_working_hours',
				sprintf( 'Время примерки должно быть между %s и %s', self::SALON_OPEN_HOUR . ':00', self::SALON_CLOSE_HOUR . ':00' )
			);
		}

		// Check slot availability using existing method.
		$date = $datetime_obj->format( 'Y-m-d' );
		$time = $datetime_obj->format( 'H:i' );

		$slot_availability = $this->is_slot_available( $date, $time, $fitting_type, $exclude_booking_id );

		if ( is_wp_error( $slot_availability ) ) {
			return $slot_availability;
		}

		return true;
	}

	/**
	 * Static wrapper for validate_fitting_datetime method.
	 * Maintains backward compatibility with existing code.
	 *
	 * @param string       $datetime           The datetime string to validate.
	 * @param string|array $fitting_type       The type of fitting.
	 * @param int|null     $exclude_booking_id ID of fitting to exclude from validation.
	 * @return true|WP_Error Returns true if valid, WP_Error if invalid.
	 */
	public static function validate_fitting_datetime_static( $datetime, $fitting_type, $exclude_booking_id = null ) {
		$instance = self::get_instance();
		return $instance->validate_fitting_datetime( $datetime, $fitting_type, $exclude_booking_id );
	}
}

$booking_manager = Fitting_Slots_Manager::get_instance();
