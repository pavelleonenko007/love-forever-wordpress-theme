<?php
/**
 * Functions
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/bootstrap.php';

require_once __DIR__ . '/inc/constants.php';
require_once __DIR__ . '/inc/utils.php';
require_once __DIR__ . '/inc/setup.php';
require_once __DIR__ . '/inc/scripts.php';
require_once __DIR__ . '/inc/post-types.php';
require_once __DIR__ . '/inc/options.php';
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/inc/hooks.php';
require_once __DIR__ . '/inc/schema-settings.php';

require_once __DIR__ . '/includes/class-dress-order.php';
require_once __DIR__ . '/includes/class-promo-order.php';
require_once __DIR__ . '/includes/class-story-order.php';
require_once __DIR__ . '/includes/class-fitting-slots.php';
require_once __DIR__ . '/includes/class-booking-manager-pavel.php';

require_once __DIR__ . '/inc/class-loveforever-dress-importer.php';
require_once __DIR__ . '/inc/cli/reupdate-posts.php';
require_once __DIR__ . '/inc/class-loveforever-review-importer.php';
// require_once __DIR__ . '/inc/dress-categories-importer.php'

// add_action('init', 'loveforever_collect_dress_categories_to_json', 100);

// add_action(
// 	'init',
// 	function () {
// 		$colors = array(
// 			'Nude'              => 'rgba(205, 178, 153, 1)',
// 			'Айвори'            => 'rgba(255, 255, 240, 1)',
// 			'Аква'              => 'rgba(127, 255, 212, 1)',
// 			'Бежевый'           => 'rgba(245, 245, 220, 1)',
// 			'Бело-розовый'      => 'rgba(255, 228, 225, 1)',
// 			'Белый'             => 'rgba(255, 255, 255, 1)',
// 			'Бирюзовый'         => 'rgba(64, 224, 208, 1)',
// 			'Бордо'             => 'rgba(128, 0, 32, 1)',
// 			'Бронза'            => 'rgba(205, 127, 50, 1)',
// 			'Брусничный'        => 'rgba(178, 34, 34, 1)',
// 			'Винный'            => 'rgba(114, 47, 55, 1)',
// 			'Голубой'           => 'rgba(173, 216, 230, 1)',
// 			'Желтый'            => 'rgba(255, 255, 0, 1)',
// 			'Желтый пастельный' => 'rgba(255, 253, 208, 1)',
// 			'Жемчуг'            => 'rgba(234, 230, 202, 1)',
// 			'Зеленый'           => 'rgba(0, 128, 0, 1)',
// 			'Золотой'           => 'rgba(255, 215, 0, 1)',
// 			'Изумруд'           => 'rgba(80, 200, 120, 1)',
// 			'Капучино'          => 'rgba(193, 154, 107, 1)',
// 			'Коралловый'        => 'rgba(255, 127, 80, 1)',
// 			'Коричневый'        => 'rgba(101, 67, 33, 1)',
// 			'Красный'           => 'rgba(255, 0, 0, 1)',
// 			'Крем'              => 'rgba(255, 253, 208, 1)',
// 			'Лавандовый'        => 'rgba(230, 230, 250, 1)',
// 			'Лиловый'           => 'rgba(200, 162, 200, 1)',
// 			'Малахит'           => 'rgba(11, 218, 81, 1)',
// 			'Малиновый'         => 'rgba(220, 20, 60, 1)',
// 			'Марсала'           => 'rgba(128, 0, 32, 1)',
// 			'Ментол'            => 'rgba(170, 240, 209, 1)',
// 			'Молочный'          => 'rgba(255, 250, 240, 1)',
// 			'Муссон'            => 'rgba(176, 196, 222, 1)',
// 			'Мята'              => 'rgba(189, 252, 201, 1)',
// 			'Оранжевый'         => 'rgba(255, 165, 0, 1)',
// 			'Пепел'             => 'rgba(178, 190, 181, 1)',
// 			'Персиковый'        => 'rgba(255, 218, 185, 1)',
// 			'Песочный'          => 'rgba(194, 178, 128, 1)',
// 			'Принт'             => 'rgba(128, 128, 128, 1)', // условно нейтральный
// 			'Пудра'             => 'rgba(220, 182, 193, 1)',
// 			'Розовое серебро'   => 'rgba(201, 192, 187, 1)',
// 			'Розовый'           => 'rgba(255, 182, 193, 1)',
// 			'Розовый кварц'     => 'rgba(247, 202, 201, 1)',
// 			'Салатовый'         => 'rgba(152, 251, 152, 1)',
// 			'Светло-розовый'    => 'rgba(255, 192, 203, 1)',
// 			'Серебро'           => 'rgba(192, 192, 192, 1)',
// 			'Серо-голубой'      => 'rgba(176, 196, 222, 1)',
// 			'Серо-коричневый'   => 'rgba(150, 111, 91, 1)',
// 			'Серый'             => 'rgba(128, 128, 128, 1)',
// 			'Синий'             => 'rgba(0, 0, 255, 1)',
// 			'Сиреневый'         => 'rgba(216, 191, 216, 1)',
// 			'Сталь'             => 'rgba(70, 130, 180, 1)',
// 			'Телесный'          => 'rgba(255, 224, 189, 1)',
// 			'Темно-синий'       => 'rgba(0, 0, 139, 1)',
// 			'Фиолетовый'        => 'rgba(138, 43, 226, 1)',
// 			'Фисташка'          => 'rgba(147, 197, 114, 1)',
// 			'Фраппе'            => 'rgba(209, 186, 152, 1)',
// 			'Фуксия'            => 'rgba(255, 0, 255, 1)',
// 			'Цветные'           => 'rgba(0, 0, 0, 1)', // условно placeholder
// 			'Черника'           => 'rgba(54, 48, 98, 1)',
// 			'Черный'            => 'rgba(0, 0, 0, 1)',
// 			'Шампань'           => 'rgba(250, 235, 215, 1)',
// 			'Шоколад'           => 'rgba(123, 63, 0, 1)',
// 			'Электрик'          => 'rgba(44, 117, 255, 1)',
// 			'Ясень'             => 'rgba(178, 190, 181, 1)',
// 		);

// 		$colors_terms = get_terms(
// 			array(
// 				'taxonomy'   => 'color',
// 				'hide_empty' => false,
// 			)
// 		);

// 		foreach ( $colors_terms as $color ) {
// 			$color_name = $color->name;
// 			$color_value = $colors[ $color_name ];

// 			update_field( 'color', $color_value, $color );
// 		}
// 	},
// 	100
// );
