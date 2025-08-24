<?php
/**
 * Seed скрипт для массового применения правил автокатегоризации
 * 
 * Запуск через браузер: https://your-site.com/wp-content/themes/loveforever/seed-rules.php
 * Запуск через командную строку: php seed-rules.php
 */

// Подключаем WordPress
require_once( dirname( __FILE__ ) . '/../../../wp-load.php' );

// Проверяем права доступа (только для администраторов)
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Недостаточно прав для выполнения этой операции.' );
}

// Получаем параметр действия
$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';

// Если запуск через командную строку
if ( php_sapi_name() === 'cli' ) {
	$action = isset( $argv[1] ) ? $argv[1] : '';
}

// Функция для вывода сообщений
function output_message( $message, $type = 'info' ) {
	if ( php_sapi_name() === 'cli' ) {
		echo $message . PHP_EOL;
	} else {
		$color = $type === 'success' ? 'green' : ( $type === 'error' ? 'red' : 'blue' );
		echo '<div style="color: ' . $color . '; margin: 5px 0;">' . esc_html( $message ) . '</div>';
	}
}

// Функция для создания прогресс-бара
function show_progress( $current, $total, $label = '' ) {
	$percentage = round( ( $current / $total ) * 100, 1 );
	
	if ( php_sapi_name() === 'cli' ) {
		echo "\r{$label}: {$current}/{$total} ({$percentage}%)";
		if ( $current >= $total ) {
			echo PHP_EOL;
		}
	} else {
		echo '<div style="margin: 5px 0;">';
		echo '<div style="background: #f0f0f0; border: 1px solid #ccc; height: 20px; position: relative;">';
		echo '<div style="background: #0073aa; height: 100%; width: ' . $percentage . '%;"></div>';
		echo '<div style="position: absolute; top: 0; left: 0; right: 0; text-align: center; line-height: 18px; font-size: 12px;">';
		echo esc_html( $label . ': ' . $current . '/' . $total . ' (' . $percentage . '%)' );
		echo '</div></div></div>';
	}
}

// Основная логика
switch ( $action ) {
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
		show_usage_instructions();
}

/**
 * Показывает инструкции по использованию
 */
function show_usage_instructions() {
	output_message( '🎯 Seed скрипт для применения правил автокатегоризации', 'info' );
	output_message( '', 'info' );
	output_message( 'Доступные действия:', 'info' );
	output_message( '- apply_auto_rules - Применить правила автокатегоризации (бейджи, фильтры)', 'info' );
	output_message( '- apply_price_rules - Применить ценовые правила', 'info' );
	output_message( '- apply_all_rules - Применить все правила', 'info' );
	output_message( '', 'info' );
	output_message( 'Использование:', 'info' );
	output_message( 'Через браузер: ?action=apply_all_rules', 'info' );
	output_message( 'Через командную строку: php seed-rules.php apply_all_rules', 'info' );
}

/**
 * Применяет правила автокатегоризации ко всем платьям
 */
function apply_auto_rules_to_all_dresses() {
	output_message( '🚀 Начинаем применение правил автокатегоризации ко всем платьям...', 'info' );
	
	// Получаем общее количество платьев
	$total_dresses = wp_count_posts( 'dress' )->publish;
	
	if ( $total_dresses === 0 ) {
		output_message( 'Платья не найдены.', 'error' );
		return;
	}
	
	output_message( '📋 Найдено ' . $total_dresses . ' платьев для обработки.', 'info' );
	
	$processed = 0;
	$updated = 0;
	$batch_size = 10; // Размер батча
	$offset = 0;
	
	// Обрабатываем платья батчами
	while ( $processed < $total_dresses ) {
		// Получаем батч платьев
		$dresses = get_posts( array(
			'post_type'      => 'dress',
			'numberposts'    => $batch_size,
			'offset'         => $offset,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		) );
		
		if ( empty( $dresses ) ) {
			break;
		}
		
		foreach ( $dresses as $dress_id ) {
			// Показываем прогресс
			show_progress( $processed + 1, $total_dresses, 'Применение правил автокатегоризации' );
			
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
		
		$offset += $batch_size;
		
		// Очищаем кэш для освобождения памяти
		wp_cache_flush();
		gc_collect_cycles();
	}
	
	output_message( '✅ Обработано ' . $processed . ' платьев. Обновлено: ' . $updated . ' платьев.', 'success' );
}

/**
 * Применяет ценовые правила ко всем платьям
 */
function apply_price_rules_to_all_dresses() {
	output_message( '💰 Начинаем применение ценовых правил ко всем платьям...', 'info' );
	
	// Получаем общее количество платьев
	$total_dresses = wp_count_posts( 'dress' )->publish;
	
	if ( $total_dresses === 0 ) {
		output_message( 'Платья не найдены.', 'error' );
		return;
	}
	
	output_message( '📋 Найдено ' . $total_dresses . ' платьев для обработки.', 'info' );
	
	$processed = 0;
	$updated = 0;
	$batch_size = 50; // Размер батча
	$offset = 0;
	
	// Обрабатываем платья батчами
	while ( $processed < $total_dresses ) {
		// Получаем батч платьев
		$dresses = get_posts( array(
			'post_type'      => 'dress',
			'numberposts'    => $batch_size,
			'offset'         => $offset,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		) );
		
		if ( empty( $dresses ) ) {
			break;
		}
		
		foreach ( $dresses as $dress_id ) {
			// Показываем прогресс
			show_progress( $processed + 1, $total_dresses, 'Применение ценовых правил' );
			
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
		
		$offset += $batch_size;
		
		// Очищаем кэш для освобождения памяти
		wp_cache_flush();
		gc_collect_cycles();
	}
	
	output_message( '✅ Обработано ' . $processed . ' платьев. Обновлено: ' . $updated . ' платьев.', 'success' );
}

/**
 * Применяет все правила ко всем платьям
 */
function apply_all_rules_to_all_dresses() {
	output_message( '🎯 Начинаем применение всех правил ко всем платьям...', 'info' );
	
	// Получаем общее количество платьев
	$total_dresses = wp_count_posts( 'dress' )->publish;
	
	if ( $total_dresses === 0 ) {
		output_message( 'Платья не найдены.', 'error' );
		return;
	}
	
	output_message( '📋 Найдено ' . $total_dresses . ' платьев для обработки.', 'info' );
	
	$processed = 0;
	$updated = 0;
	$batch_size = 50; // Размер батча
	$offset = 0;
	
	// Обрабатываем платья батчами
	while ( $processed < $total_dresses ) {
		// Получаем батч платьев
		$dresses = get_posts( array(
			'post_type'      => 'dress',
			'numberposts'    => $batch_size,
			'offset'         => $offset,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		) );
		
		if ( empty( $dresses ) ) {
			break;
		}
		
		foreach ( $dresses as $dress_id ) {
			// Показываем прогресс
			show_progress( $processed + 1, $total_dresses, 'Применение всех правил' );
			
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
		
		$offset += $batch_size;
		
		// Очищаем кэш для освобождения памяти
		wp_cache_flush();
		gc_collect_cycles();
	}
	
	output_message( '✅ Обработано ' . $processed . ' платьев. Обновлено: ' . $updated . ' платьев.', 'success' );
}

/**
 * Применяет правила автокатегоризации к одному платью
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
 */
function apply_price_rules_to_single_dress( $dress_id, $current_categories ) {
	// Получаем финальную цену платья
	$final_price = get_field( 'final_price', $dress_id );
	
	if ( empty( $final_price ) || ! is_numeric( $final_price ) ) {
		return $current_categories;
	}

	// Получаем текущие категории платья
	$dress_categories = $current_categories;

	if ( empty( $dress_categories ) ) {
		return $current_categories;
	}

	// Получаем родительские категории платьев (корневые)
	$dress_categories_objects  = array_map( 'get_term', $dress_categories );
	$parent_dress_categories   = array_filter( $dress_categories_objects, fn( $cat ) => 0 === $cat->parent );
	$parent_dress_category_ids = array_map( fn( $cat ) => $cat->term_id, $parent_dress_categories );

	if ( empty( $parent_dress_category_ids ) ) {
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
		$base_category = get_field( 'base_dress_category', $rule->ID ); // Базовая категория
		$target_category = get_field( 'target_category', $rule->ID );

		if ( empty( $target_category ) ) {
			continue;
		}

		// Проверяем, что платье принадлежит к базовой категории правила
		if ( ! empty( $base_category ) && ! in_array( $base_category, $parent_dress_category_ids, true ) ) {
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
		return array_unique( array_merge( $dress_categories, $matched_categories ) );
	}

	return $current_categories;
}
