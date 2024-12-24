<?php
/**
 * Custom Post Types
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'loveforever_register_post_types' );
function loveforever_register_post_types() {
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
			'has_archive'         => true,
			'rewrite'             => true,
			'query_var'           => true,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);
}

