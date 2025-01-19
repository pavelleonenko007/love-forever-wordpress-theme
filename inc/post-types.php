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
			'hierarchical'       => false,

			'rewrite'            => true,
			// 'query_var'             => taxonomy, // название параметра запроса
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => null, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	register_taxonomy(
		'dress_brand',
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
			// 'query_var'             => taxonomy, // название параметра запроса
			'capabilities'       => array(),
			'meta_box_cb'        => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
			'show_admin_column'  => true, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'       => null, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	register_taxonomy(
		'dress_tag',
		null,
		array(
			'label'              => '',
			'labels'             => array(
				'name'              => 'Теги',
				'singular_name'     => 'Тег',
				'search_items'      => 'Поиск тегов',
				'all_items'         => 'Все теги',
				'view_item '        => 'Просмотр тега',
				'parent_item'       => 'Родитель тега',
				'parent_item_colon' => 'Родитель тега:',
				'edit_item'         => 'Редактировать тег',
				'update_item'       => 'Обновить тег',
				'add_new_item'      => 'Добавить новый тег',
				'new_item_name'     => 'Название нового тега',
				'menu_name'         => 'Теги',
				'back_to_items'     => '← Назад к тегам',
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
			'show_in_rest'       => null, // добавить в REST API
			'rest_base'          => null, // taxonomy
		)
	);

	register_post_type(
		'fitting',
		array(
			'label'         => null,
			'labels'        => array(
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
			'description'   => '',
			'public'        => false,
			// 'publicly_queryable'  => null,
			// 'exclude_from_search' => null,
			'show_ui'       => true,
			// 'show_in_nav_menus'   => null,
			'show_in_menu'  => null,
			// 'show_in_admin_bar'   => null,
			'show_in_rest'  => true,
			'rest_base'     => null,
			'menu_position' => null,
			// 'capability_type'   => 'post',
			// 'capabilities'      => 'post',
			// 'map_meta_cap'      => null,
			'hierarchical'  => false,
			'supports'      => array( 'title' ), // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
			'taxonomies'    => array(),
			'has_archive'   => false,
			'rewrite'       => true,
			'query_var'     => true,
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
			'taxonomies'          => array( 'dress_category', 'dress_brand', 'dress_tag' ),
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
			'taxonomies'    => array(),
			'has_archive'   => false,
			'rewrite'       => true,
			'query_var'     => true,
		)
	);

	register_post_type(
		'story',
		array(
			'label'         => null,
			'labels'        => array(
				'name'               => 'Отзывы',
				'singular_name'      => 'Отзыв',
				'add_new'            => 'Добавить новый',
				'add_new_item'       => 'Добавить новый отзыв',
				'edit_item'          => 'Редактировать отзыв',
				'new_item'           => 'Новый отзыв',
				'view_item'          => 'Посмотреть отзыв',
				'search_items'       => 'Найти отзыв',
				'not_found'          => 'Не найдено',
				'not_found_in_trash' => 'Не найдено в корзине',
				'parent_item_colon'  => '',
				'menu_name'          => 'Отзывы',
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
			'taxonomies'    => array(),
			'has_archive'   => false,
			'rewrite'       => true,
			'menu_icon'     => 'dashicons-testimonial',
			'query_var'     => true,
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
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => null,
			'show_in_rest'  => true,
			'rest_base'     => null,
			'menu_position' => null,
			'hierarchical'  => false,
			'supports'      => array( 'title', 'editor' ), // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
			'taxonomies'    => array(),
			'has_archive'   => false,
			'rewrite'       => true,
			'menu_icon'     => 'dashicons-format-status',
			'query_var'     => true,
		)
	);
}

// Изменение структуры ссылок для типа записи 'dress'
function custom_dress_post_link( $post_link, $post ) {
	if ( is_object( $post ) && $post->post_type == 'dress' ) {
		$terms  = wp_get_object_terms( $post->ID, 'dress_category' );
		$brands = wp_get_object_terms( $post->ID, 'dress_brand' );

		if ( $terms ) {
			// Выбираем первую категорию как основную
			$category_slug = $terms[0]->slug;
			if ( $brands ) {
				$brand_slug = $brands[0]->slug;
				return home_url( "dress/$category_slug/$brand_slug/" . $post->post_name );
			}
			return home_url( "dress/$category_slug/" . $post->post_name );
		}
	}
	return $post_link;
}
// add_filter( 'post_type_link', 'custom_dress_post_link', 10, 2 );

// Регистрация новой структуры URL для типа записи 'dress'
function custom_dress_permalinks( $rules ) {
	$new_rules = array(
		'dress/([^/]+)/([^/]+)/([^/]+)/?$' => 'index.php?dress=$matches[3]&dress_category=$matches[1]&dress_brand=$matches[2]',
		'dress/([^/]+)/([^/]+)/?$'         => 'index.php?dress_category=$matches[1]&dress_brand=$matches[2]',
		'dress/([^/]+)/?$'                 => 'index.php?dress_category=$matches[1]',
	);
	return $new_rules + $rules;
}
// add_filter( 'rewrite_rules_array', 'custom_dress_permalinks' );

// Обновление структуры ссылок для типа записи 'dress'
function custom_dress_permalink_structure() {
	global $wp_rewrite;
	$wp_rewrite->add_rewrite_tag( '%dress_category%', '([^/]+)', 'dress_category=' );
	$wp_rewrite->add_rewrite_tag( '%dress_brand%', '([^/]+)', 'dress_brand=' );
	$wp_rewrite->add_permastruct( 'dress', 'dress/%dress_category%/%dress_brand%/%dress%', false );
}
// add_action( 'init', 'custom_dress_permalink_structure', 10, 0 );

// Обеспечение правильной загрузки категорий и брендов
function custom_dress_query_vars( $query_vars ) {
	$query_vars[] = 'dress_category';
	$query_vars[] = 'dress_brand';
	return $query_vars;
}
// add_filter( 'query_vars', 'custom_dress_query_vars' );

// Изменение запроса для правильной загрузки категорий и брендов
function custom_dress_request( $query_vars ) {
	if ( isset( $query_vars['dress_category'] ) ) {
		if ( isset( $query_vars['dress_brand'] ) ) {
			$query_vars['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'dress_category',
					'field'    => 'slug',
					'terms'    => $query_vars['dress_category'],
				),
				array(
					'taxonomy' => 'dress_brand',
					'field'    => 'slug',
					'terms'    => $query_vars['dress_brand'],
				),
			);
		} else {
			$query_vars['tax_query'] = array(
				array(
					'taxonomy' => 'dress_category',
					'field'    => 'slug',
					'terms'    => $query_vars['dress_category'],
				),
			);
		}
		$query_vars['post_type'] = 'dress';
	}
	return $query_vars;
}
// add_filter( 'request', 'custom_dress_request' );

// Перенаправление старых URL на новые
function custom_dress_redirect() {
	if ( is_tax( 'dress_category' ) || is_tax( 'dress_brand' ) ) {
		$queried_object = get_queried_object();
		if ( is_tax( 'dress_category' ) ) {
			wp_redirect( home_url( "dress/{$queried_object->slug}" ), 301 );
			exit;
		} elseif ( is_tax( 'dress_brand' ) ) {
			$category = get_term_by( 'slug', get_query_var( 'dress_category' ), 'dress_category' );
			if ( $category ) {
				wp_redirect( home_url( "dress/{$category->slug}/{$queried_object->slug}" ), 301 );
				exit;
			}
		}
	}
}
/*add_action( 'template_redirect', 'custom_dress_redirect' );*/

// Добавляем функцию для установки канонического URL
function custom_dress_canonical() {
	if ( is_singular( 'dress' ) ) {
		global $post;
		$link = custom_dress_post_link( '', $post );
		echo '<link rel="canonical" href="' . esc_url( $link ) . '" />' . "\n";
	}
}
add_action( 'wp_head', 'custom_dress_canonical' );

// Добавляем функцию для перенаправления на канонический URL
function custom_dress_redirect_canonical() {
	if ( is_singular( 'dress' ) ) {
		global $post;
		$canonical_link = custom_dress_post_link( '', $post );
		$current_link   = home_url( $_SERVER['REQUEST_URI'] );

		if ( $canonical_link !== $current_link ) {
			wp_redirect( $canonical_link, 301 );
			exit;
		}
	}
}
/*add_action( 'template_redirect', 'custom_dress_redirect_canonical' );*/
