<?php
/**
 * Custom Post Types
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'loveforever_register_post_types' );
function loveforever_register_post_types() {
	register_taxonomy(
		'dress_category',
		null,
		array(
			'label'              => '',
			'labels'             => array(
				'name'              => 'Категории',
				'singular_name'     => 'Категория',
				'search_items'      => 'Поиск категорий',
				'all_items'         => 'Все категории',
				'view_item '        => 'Просмотр категории',
				'parent_item'       => 'Родитель категории',
				'parent_item_colon' => 'Родитель категории:',
				'edit_item'         => 'Редактировать категорию',
				'update_item'       => 'Обновить категорию',
				'add_new_item'      => 'Добавить новую категорию',
				'new_item_name'     => 'Название новой категории',
				'menu_name'         => 'Категории',
				'back_to_items'     => '← Назад к категориям',
			),
			'description'        => '',
			'public'             => true,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'hierarchical'       => true,
			'rewrite'            => array(
				'slug'         => 'dresses',
				'hierarchical' => true,
				'with_front'   => false,
			),
			// 'query_var'             => taxonomy, // название параметра запроса
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => null, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	register_taxonomy(
		'brand',
		null,
		array(
			'label'              => '',
			'labels'             => array(
				'name'              => 'Бренды',
				'singular_name'     => 'Бренд',
				'search_items'      => 'Поиск брендов',
				'all_items'         => 'Все бренды',
				'view_item '        => 'Просмотр бренда',
				'parent_item'       => 'Родитель бренда',
				'parent_item_colon' => 'Родитель бренда:',
				'edit_item'         => 'Редактировать бренд',
				'update_item'       => 'Обновить бренд',
				'add_new_item'      => 'Добавить новый бренд',
				'new_item_name'     => 'Название нового бренда',
				'menu_name'         => 'Бренды',
				'back_to_items'     => '← Назад к брендам',
			),
			'description'        => '',
			'public'             => true,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'hierarchical'       => false,
			'rewrite'            => true,
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => null, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	// register_taxonomy(
	// 	'dress_tag',
	// 	null,
	// 	array(
	// 		'label'              => '',
	// 		'labels'             => array(
	// 			'name'              => 'Теги',
	// 			'singular_name'     => 'Тег',
	// 			'search_items'      => 'Поиск тегов',
	// 			'all_items'         => 'Все теги',
	// 			'view_item '        => 'Просмотр тега',
	// 			'parent_item'       => 'Родитель тега',
	// 			'parent_item_colon' => 'Родитель тега:',
	// 			'edit_item'         => 'Редактировать тег',
	// 			'update_item'       => 'Обновить тег',
	// 			'add_new_item'      => 'Добавить новый тег',
	// 			'new_item_name'     => 'Название нового тега',
	// 			'menu_name'         => 'Теги',
	// 			'back_to_items'     => '← Назад к тегам',
	// 		),
	// 		'description'        => '',
	// 		'public'             => true,
	// 		'show_ui'            => true,
	// 		'show_in_quick_edit' => true,
	// 		'hierarchical'       => false,

	// 		'rewrite'            => true,
	// 		// 'query_var'             => taxonomy, // название параметра запроса
	// 		'capabilities'       => array(),
	// 		'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
	// 		'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
	// 		'show_in_rest'       => null, // добавить в REST API
	// 		'rest_base'          => null, // taxonomy
	// 	)
	// );

	register_taxonomy(
		'silhouette',
		null,
		array(
			'label'              => '',
			'labels'             => array(
				'name'              => 'Силуэты',
				'singular_name'     => 'Силуэт',
				'search_items'      => 'Поиск силуэтов',
				'all_items'         => 'Все силуэты',
				'view_item '        => 'Просмотр силуэта',
				'parent_item'       => 'Родитель силуэта',
				'parent_item_colon' => 'Родитель силуэта:',
				'edit_item'         => 'Редактировать силуэт',
				'update_item'       => 'Обновить силуэт',
				'add_new_item'      => 'Добавить новый силуэт',
				'new_item_name'     => 'Название нового силуэта',
				'menu_name'         => 'Силуэты',
				'back_to_items'     => '← Назад к силуэтам',
			),
			'description'        => '',
			'public'             => true,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'hierarchical'       => false,
			'rewrite'            => true,
			// 'query_var'             => taxonomy, // название параметра запроса
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => true, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	register_taxonomy(
		'style',
		null,
		array(
			'label'              => '',
			'labels'             => array(
				'name'              => 'Стили',
				'singular_name'     => 'Стиль',
				'search_items'      => 'Поиск силуэтов',
				'all_items'         => 'Все стили',
				'view_item '        => 'Просмотр стиля',
				'parent_item'       => 'Родитель стиля',
				'parent_item_colon' => 'Родитель стиля:',
				'edit_item'         => 'Редактировать стиль',
				'update_item'       => 'Обновить стиль',
				'add_new_item'      => 'Добавить новый стиль',
				'new_item_name'     => 'Название нового стиля',
				'menu_name'         => 'Стили',
				'back_to_items'     => '← Назад к стилям',
			),
			'description'        => '',
			'public'             => true,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'hierarchical'       => false,
			'rewrite'            => true,
			// 'query_var'             => taxonomy, // название параметра запроса
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => true, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	register_taxonomy(
		'fabric',
		null,
		array(
			'label'              => '',
			'labels'             => array(
				'name'              => 'Ткани',
				'singular_name'     => 'Ткань',
				'search_items'      => 'Поиск тканей',
				'all_items'         => 'Все ткани',
				'view_item '        => 'Просмотр тканей',
				'parent_item'       => 'Родитель ткани',
				'parent_item_colon' => 'Родитель ткани:',
				'edit_item'         => 'Редактировать ткань',
				'update_item'       => 'Обновить ткань',
				'add_new_item'      => 'Добавить новую ткань',
				'new_item_name'     => 'Название новой ткани',
				'menu_name'         => 'Ткани',
				'back_to_items'     => '← Назад к тканям',
			),
			'description'        => '',
			'public'             => false,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'hierarchical'       => false,
			'rewrite'            => true,
			// 'query_var'             => taxonomy, // название параметра запроса
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => true, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	register_taxonomy(
		'color',
		null,
		array(
			'label'              => '',
			'labels'             => array(
				'name'              => 'Цвета',
				'singular_name'     => 'Цвет',
				'search_items'      => 'Поиск цветов',
				'all_items'         => 'Все цвета',
				'view_item '        => 'Просмотр цветов',
				'parent_item'       => 'Родитель цвета',
				'parent_item_colon' => 'Родитель цвета:',
				'edit_item'         => 'Редактировать цвет',
				'update_item'       => 'Обновить цвет',
				'add_new_item'      => 'Добавить новый цвет',
				'new_item_name'     => 'Название нового цвета',
				'menu_name'         => 'Цвета',
				'back_to_items'     => '← Назад к цветам',
			),
			'description'        => '',
			'public'             => false,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'hierarchical'       => false,
			'rewrite'            => true,
			// 'query_var'             => taxonomy, // название параметра запроса
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => true, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	register_taxonomy(
		'faq_category',
		null,
		array(
			'label'              => '',
			'labels'             => array(
				'name'              => 'Категории вопросов',
				'singular_name'     => 'Категория вопросов',
				'search_items'      => 'Поиск категории',
				'all_items'         => 'Все категории',
				'view_item '        => 'Просмотр категории',
				'parent_item'       => 'Родитель категории',
				'parent_item_colon' => 'Родитель категории:',
				'edit_item'         => 'Редактировать категорию',
				'update_item'       => 'Обновить категорию',
				'add_new_item'      => 'Добавить новую категорию',
				'new_item_name'     => 'Название новой категории',
				'menu_name'         => 'Категории',
				'back_to_items'     => '← Назад к категориям',
			),
			'description'        => '',
			'public'             => false,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'hierarchical'       => false,
			'rewrite'            => true,
			// 'query_var'             => taxonomy, // название параметра запроса
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => true, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);
	register_taxonomy(
		'zodiac_sign',
		null,
		array(
			'label'              => '',
			'labels'             => array(
				'name'          => 'Знаки зодиака',
				'singular_name' => 'Знак зодиака',
				'search_items'  => 'Поиск знаков зодиака',
				'all_items'     => 'Все знаки зодиака',
				'view_item '    => 'Просмотр знака зодиака',
				'edit_item'     => 'Редактировать знак зодиака',
				'update_item'   => 'Обновить знак зодиака',
				'add_new_item'  => 'Добавить новый знак зодиака',
				'new_item_name' => 'Название нового знака зодиака',
				'menu_name'     => 'Знаки зодиака',
				'back_to_items' => '← Назад к знакам зодиака',
			),
			'description'        => '',
			'public'             => true,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'hierarchical'       => true,
			'rewrite'            => true,
			// 'query_var'             => taxonomy, // название параметра запроса
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => null, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	register_post_type(
		'fitting',
		array(
			'label'           => null,
			'labels'          => array(
				'name'               => 'Примерки',
				'singular_name'      => 'Примерка',
				'add_new'            => 'Добавить новую',
				'add_new_item'       => 'Добавить новую примерку',
				'edit_item'          => 'Редактировать примерку',
				'new_item'           => 'Новая примерка',
				'view_item'          => 'Посмотреть примерку',
				'search_items'       => 'Найти примерку',
				'not_found'          => 'Не найдено',
				'not_found_in_trash' => 'Не найдено в корзине',
				'parent_item_colon'  => '',
				'menu_name'          => 'Примерки',
			),
			'description'     => '',
			'public'          => true,
			// 'publicly_queryable'  => null,
			// 'exclude_from_search' => null,
			'show_ui'         => true,
			// 'show_in_nav_menus'   => null,
			'show_in_menu'    => null,
			// 'show_in_admin_bar'   => null,
			'show_in_rest'    => true,
			'rest_base'       => null,
			'menu_position'   => null,
			'capability_type' => array( 'fitting', 'fittings' ),
			'map_meta_cap'    => true,
			'hierarchical'    => false,
			'supports'        => array( 'title' ), // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
			'taxonomies'      => array(),
			'has_archive'     => false,
			'rewrite'         => true,
			'query_var'       => true,
		)
	);

	register_post_type(
		'dress',
		array(
			'label'               => null,
			'labels'              => array(
				'name'               => 'Платья',
				'singular_name'      => 'Платье',
				'add_new'            => 'Добавить новое',
				'add_new_item'       => 'Добавить новое платье',
				'edit_item'          => 'Редактировать платье',
				'new_item'           => 'Новое платье',
				'view_item'          => 'Посмотреть платье',
				'search_items'       => 'Найти платье',
				'not_found'          => 'Не найдено',
				'not_found_in_trash' => 'Не найдено в корзине',
				'parent_item_colon'  => '',
				'menu_name'          => 'Платья',
			),
			'description'         => '',
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'rest_base'           => '',
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-portfolio',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'has_archive'         => false,
			'rewrite'             => true,
			'query_var'           => true,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'taxonomies'          => array( 'dress_category', 'silhouette', 'style', 'brand', 'fabric', 'color', 'zodiac_sign' ),
		)
	);

	register_post_type(
		'story',
		array(
			'label'         => null,
			'labels'        => array(
				'name'               => 'Истории',
				'singular_name'      => 'История',
				'add_new'            => 'Добавить новую',
				'add_new_item'       => 'Добавить новую историю',
				'edit_item'          => 'Редактировать историю',
				'new_item'           => 'Новая история',
				'view_item'          => 'Посмотреть историю',
				'search_items'       => 'Найти историю',
				'not_found'          => 'Не найдено',
				'not_found_in_trash' => 'Не найдено в корзине',
				'parent_item_colon'  => '',
				'menu_name'          => 'Истории',
			),
			'description'   => '',
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => null,
			'show_in_rest'  => true,
			'rest_base'     => null,
			'menu_position' => null,
			'hierarchical'  => false,
			'supports'      => array( 'title', 'thumbnail' ), // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
			'taxonomies'    => array( 'dress_category' ),
			'has_archive'   => false,
			'rewrite'       => true,
			'query_var'     => true,
		)
	);

	register_post_type(
		'review',
		array(
			'labels'             => array(
				'name'               => 'Отзывы',
				'singular_name'      => 'Отзыв',
				'add_new'            => 'Добавить отзыв',
				'add_new_item'       => 'Добавить новый отзыв',
				'edit_item'          => 'Редактировать отзыв',
				'new_item'           => 'Новый отзыв',
				'view_item'          => 'Просмотреть отзыв',
				'search_items'       => 'Найти отзыв',
				'not_found'          => 'Отзывов не найдено',
				'not_found_in_trash' => 'В корзине отзывов не найдено',
				'menu_name'          => 'Отзывы',
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'reviews' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'thumbnail' ),
			'show_in_rest'       => true,
		)
	);

	register_post_type(
		'faq',
		array(
			'label'         => null,
			'labels'        => array(
				'name'               => 'FAQs',
				'singular_name'      => 'FAQ',
				'add_new'            => 'Добавить новый',
				'add_new_item'       => 'Добавить новый FAQ',
				'edit_item'          => 'Редактировать FAQ',
				'new_item'           => 'Новый FAQ',
				'view_item'          => 'Посмотреть FAQ',
				'search_items'       => 'Найти FAQ',
				'not_found'          => 'Не найдено',
				'not_found_in_trash' => 'Не найдено в корзине',
				'parent_item_colon'  => '',
				'menu_name'          => 'FAQs',
			),
			'description'   => '',
			'public'        => true,
			'show_ui'       => true,
			'show_in_menu'  => null,
			'show_in_rest'  => true,
			'rest_base'     => null,
			'menu_position' => null,
			'hierarchical'  => false,
			'supports'      => array( 'title', 'editor' ), // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
			'taxonomies'    => array( 'faq_category' ),
			'has_archive'   => true,
			'rewrite'       => array(
				'slug' => 'faqs',
			),
			'menu_icon'     => 'dashicons-format-status',
			'query_var'     => true,
		)
	);

	register_post_type(
		'auto_rule',
		array(
			'label'        => '',
			'labels'       => array(
				'name'               => 'Правила автокатегорий',
				'singular_name'      => 'Правило автокатегоризации',
				'add_new'            => 'Добавить новое',
				'add_new_item'       => 'Добавить новое правило',
				'edit_item'          => 'Редактировать правило',
				'new_item'           => 'Новое правило',
				'view_item'          => 'Посмотреть правило',
				'search_items'       => 'Найти правило',
				'not_found'          => 'Правила не найдены',
				'not_found_in_trash' => 'Правила не найдены в корзине',
				'parent_item_colon'  => '',
				'menu_name'          => 'Правила автокатегоризации',
			),
			'public'       => false,
			'show_ui'      => true,
			'menu_icon'    => 'dashicons-randomize',
			'supports'     => array( 'title' ),
			'show_in_menu' => 'edit.php?post_type=dress',
		)
	);

	register_post_type(
		'promo_blocks',
		array(
			'label'               => 'Промо-блоки',
			'labels'              => array(
				'name'               => 'Промо блоки',
				'singular_name'      => 'Промо блок',
				'add_new'            => 'Добавить новый',
				'add_new_item'       => 'Добавить новый блок',
				'edit_item'          => 'Редактировать блок',
				'new_item'           => 'Новый блок',
				'view_item'          => 'Посмотреть блок',
				'search_items'       => 'Найти блок',
				'not_found'          => 'Блоки не найдены',
				'not_found_in_trash' => 'Блоки не найдены в корзине',
				'parent_item_colon'  => '',
				'menu_name'          => 'Промо блоки',
			),
			'public'              => true,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'menu_icon'           => 'dashicons-layout',
			'supports'            => array( 'title' ), // Отключаем редактор
			'has_archive'         => false,
			'taxonomies'          => array( 'dress_category' ),
		)
	);

	register_post_type(
		'infoline',
		array(
			'label'               => 'Инфолиния',
			'labels'              => array(
				'name'               => 'Инфолиния',
				'singular_name'      => 'Инфолиния',
				'add_new'            => 'Добавить новую',
				'add_new_item'       => 'Добавить новую инфолинию',
				'edit_item'          => 'Редактировать инфолинию',
				'new_item'           => 'Новая инфолиния',
				'view_item'          => 'Посмотреть инфолинию',
				'search_items'       => 'Найти инфолинию',
				'not_found'          => 'Инфолинии не найдены',
				'not_found_in_trash' => 'Инфолинии не найдены в корзине',
				'parent_item_colon'  => '',
				'menu_name'          => 'Инфолиния',
			),
			'description'         => ' Используется как бегущая строка либо информационная строка.',
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => false,
			'rest_base'           => null,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-megaphone',
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor' ),
			'taxonomies'          => array(),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
		)
	);
}
