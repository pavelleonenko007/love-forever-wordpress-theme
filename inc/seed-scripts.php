<?php
/**
 * Seed скрипты для массового применения правил автокатегоризации
 * 
 * Использование через WP-CLI:
 * wp eval-file wp-content/themes/loveforever/inc/seed-scripts.php apply_auto_rules
 * wp eval-file wp-content/themes/loveforever/inc/seed-scripts.php apply_price_rules
 * wp eval-file wp-content/themes/loveforever/inc/seed-scripts.php apply_all_rules
 */

// Проверяем, что скрипт запущен через WP-CLI
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	wp_die( 'Этот скрипт должен быть запущен через WP-CLI' );
}

// Получаем аргументы командной строки
$args = $argv;
$script_name = isset( $args[1] ) ? $args[1] : '';

switch ( $script_name ) {
	case 'apply_auto_rules':
		apply_auto_rules_to_all_dresses();
		break;
	case 'apply_price_rules':
		apply_price_rules_to_all_dresses();
		break;
	case 'apply_all_rules':
		apply_all_rules_to_all_dresses();
		break;
	default:
		WP_CLI::error( 'Неизвестная команда. Доступные команды: apply_auto_rules, apply_price_rules, apply_all_rules' );
}

/**
 * Применяет правила автокатегоризации ко всем платьям
 */
function apply_auto_rules_to_all_dresses() {
	WP_CLI::log( '🚀 Начинаем применение правил автокатегоризации ко всем платьям...' );
	
	// Получаем все платья
	$dresses = get_posts( array(
		'post_type'      => 'dress',
		'numberposts'    => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
	) );
	
	if ( empty( $dresses ) ) {
		WP_CLI::warning( 'Платья не найдены.' );
		return;
	}
	
	WP_CLI::log( sprintf( '📋 Найдено %d платьев для обработки.', count( $dresses ) ) );
	
	$processed = 0;
	$updated = 0;
	
	// Создаем прогресс-бар
	$progress = \WP_CLI\Utils\make_progress_bar( 'Применение правил автокатегоризации', count( $dresses ) );
	
	foreach ( $dresses as $dress_id ) {
		$progress->tick();
		
		// Получаем текущие категории
		$current_categories = get_field( 'dress_category', $dress_id );
		$current_categories = is_array( $current_categories ) ? $current_categories : array();
		
		// Применяем правила автокатегоризации
		$new_categories = apply_auto_rules_to_single_dress( $dress_id, $current_categories );
		
		// Если категории изменились, обновляем
		if ( $new_categories !== $current_categories ) {
			update_field( 'dress_category', $new_categories, $dress_id );
			wp_set_post_terms( $dress_id, $new_categories, 'dress_category' );
			$updated++;
		}
		
		$processed++;
	}
	
	$progress->finish();
	
	WP_CLI::success( sprintf( 
		'✅ Обработано %d платьев. Обновлено: %d платьев.', 
		$processed, 
		$updated 
	) );
}

/**
 * Применяет ценовые правила ко всем платьям
 */
function apply_price_rules_to_all_dresses() {
	WP_CLI::log( '💰 Начинаем применение ценовых правил ко всем платьям...' );
	
	// Получаем все платья
	$dresses = get_posts( array(
		'post_type'      => 'dress',
		'numberposts'    => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
	) );
	
	if ( empty( $dresses ) ) {
		WP_CLI::warning( 'Платья не найдены.' );
		return;
	}
	
	WP_CLI::log( sprintf( '📋 Найдено %d платьев для обработки.', count( $dresses ) ) );
	
	$processed = 0;
	$updated = 0;
	
	// Создаем прогресс-бар
	$progress = \WP_CLI\Utils\make_progress_bar( 'Применение ценовых правил', count( $dresses ) );
	
	foreach ( $dresses as $dress_id ) {
		$progress->tick();
		
		// Получаем текущие категории
		$current_categories = get_field( 'dress_category', $dress_id );
		$current_categories = is_array( $current_categories ) ? $current_categories : array();
		
		// Применяем ценовые правила
		$new_categories = apply_price_rules_to_single_dress( $dress_id, $current_categories );
		
		// Если категории изменились, обновляем
		if ( $new_categories !== $current_categories ) {
			update_field( 'dress_category', $new_categories, $dress_id );
			wp_set_post_terms( $dress_id, $new_categories, 'dress_category' );
			$updated++;
		}
		
		$processed++;
	}
	
	$progress->finish();
	
	WP_CLI::success( sprintf( 
		'✅ Обработано %d платьев. Обновлено: %d платьев.', 
		$processed, 
		$updated 
	) );
}

/**
 * Применяет все правила ко всем платьям
 */
function apply_all_rules_to_all_dresses() {
	WP_CLI::log( '🎯 Начинаем применение всех правил ко всем платьям...' );
	
	// Получаем все платья
	$dresses = get_posts( array(
		'post_type'      => 'dress',
		'numberposts'    => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
	) );
	
	if ( empty( $dresses ) ) {
		WP_CLI::warning( 'Платья не найдены.' );
		return;
	}
	
	WP_CLI::log( sprintf( '📋 Найдено %d платьев для обработки.', count( $dresses ) ) );
	
	$processed = 0;
	$updated = 0;
	
	// Создаем прогресс-бар
	$progress = \WP_CLI\Utils\make_progress_bar( 'Применение всех правил', count( $dresses ) );
	
	foreach ( $dresses as $dress_id ) {
		$progress->tick();
		
		// Получаем текущие категории
		$current_categories = get_field( 'dress_category', $dress_id );
		$current_categories = is_array( $current_categories ) ? $current_categories : array();
		
		// Применяем правила автокатегоризации
		$new_categories = apply_auto_rules_to_single_dress( $dress_id, $current_categories );
		
		// Применяем ценовые правила
		$new_categories = apply_price_rules_to_single_dress( $dress_id, $new_categories );
		
		// Если категории изменились, обновляем
		if ( $new_categories !== $current_categories ) {
			update_field( 'dress_category', $new_categories, $dress_id );
			wp_set_post_terms( $dress_id, $new_categories, 'dress_category' );
			$updated++;
		}
		
		$processed++;
	}
	
	$progress->finish();
	
	WP_CLI::success( sprintf( 
		'✅ Обработано %d платьев. Обновлено: %d платьев.', 
		$processed, 
		$updated 
	) );
}

/**
 * Применяет правила автокатегоризации к одному платью
 * 
 * @param int   $dress_id ID платья
 * @param array $current_categories Текущие категории
 * @return array Новые категории
 */
function apply_auto_rules_to_single_dress( $dress_id, $current_categories ) {
	$dress_categories = $current_categories;

	if ( empty( $dress_categories ) ) {
		return $current_categories;
	}

	$dress_categories_objects  = array_map( 'get_term', $dress_categories );
	$parent_dress_categories   = array_filter( $dress_categories_objects, fn( $cat ) => 0 === $cat->parent );
	$parent_dress_category_ids = array_map( fn( $cat ) => $cat->term_id, $parent_dress_categories );

	if ( empty( $parent_dress_category_ids ) ) {
		return $current_categories;
	}

	$filters = array(
		'brand'      => get_field( 'brand', $dress_id ),
		'style'      => get_field( 'style', $dress_id ),
		'silhouette' => get_field( 'silhouette', $dress_id ),
		'color'      => get_field( 'color', $dress_id ),
		'fabric'     => get_field( 'fabric', $dress_id ),
		'badge'      => get_field( 'badge', $dress_id ),
	);

	if ( empty( array_filter( array_values( $filters ) ) ) ) {
		return $current_categories;
	}

	$rules = get_posts( array(
		'post_type'   => 'auto_rule',
		'numberposts' => -1,
		'post_status' => 'publish',
	) );

	$matched_terms = array();

	foreach ( $rules as $rule ) {
		$base_category_id   = get_field( 'base_dress_category', $rule->ID );
		$result_category_id = get_field( 'result_dress_category', $rule->ID );
		$rule_filters       = get_field( 'filters', $rule->ID );

		if ( ! $base_category_id || ! $result_category_id ) {
			continue;
		}

		if ( ! in_array( $base_category_id, $parent_dress_category_ids, true ) ) {
			continue;
		}

		$matched = false;

		foreach ( $rule_filters as $taxonomy => $rule_terms ) {
			if ( empty( $rule_terms ) || empty( $filters[ $taxonomy ] ) ) {
				continue;
			}

			// Специальная обработка для поля badge (не таксономия)
			if ( 'badge' === $taxonomy ) {
				if ( $filters[ $taxonomy ] === $rule_terms ) {
					$matched = true;
				} else {
					$matched = false;
					break;
				}
			} else {
				// Обычная обработка для таксономий
				$common = array_intersect( $filters[ $taxonomy ], $rule_terms );

				if ( empty( $common ) ) {
					$matched = false;
					break;
				}

				$matched = true;
			}
		}

		if ( $matched ) {
			$matched_terms[] = $result_category_id;
		}
	}

	// Возвращаем объединенные категории
	if ( ! empty( $matched_terms ) ) {
		return array_unique( array_merge( $dress_categories, $matched_terms ) );
	}

	return $current_categories;
}

/**
 * Применяет ценовые правила к одному платью
 * 
 * @param int   $dress_id ID платья
 * @param array $current_categories Текущие категории
 * @return array Новые категории
 */
function apply_price_rules_to_single_dress( $dress_id, $current_categories ) {
	// Получаем финальную цену платья
	$final_price = get_field( 'final_price', $dress_id );
	
	if ( empty( $final_price ) || ! is_numeric( $final_price ) ) {
		return $current_categories;
	}

	// Получаем все активные ценовые правила
	$price_rules = get_posts( array(
		'post_type'   => 'price_rule',
		'numberposts' => -1,
		'post_status' => 'publish',
	) );

	$matched_categories = array();

	foreach ( $price_rules as $rule ) {
		$min_price = get_field( 'min_price', $rule->ID );
		$max_price = get_field( 'max_price', $rule->ID );
		$target_category = get_field( 'target_category', $rule->ID );

		if ( empty( $target_category ) ) {
			continue;
		}

		// Проверяем соответствие цены правилу
		$matches = false;

		if ( ! empty( $min_price ) && ! empty( $max_price ) ) {
			// Диапазон цен
			$matches = ( $final_price >= $min_price && $final_price <= $max_price );
		} elseif ( ! empty( $min_price ) ) {
			// Минимальная цена
			$matches = ( $final_price >= $min_price );
		} elseif ( ! empty( $max_price ) ) {
			// Максимальная цена
			$matches = ( $final_price <= $max_price );
		}

		if ( $matches ) {
			$matched_categories[] = $target_category;
		}
	}

	// Возвращаем объединенные категории
	if ( ! empty( $matched_categories ) ) {
		return array_unique( array_merge( $current_categories, $matched_categories ) );
	}

	return $current_categories;
}
