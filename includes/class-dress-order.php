<?php
/**
 * Class Dress_Sorter
 *
 * Handles the sorting functionality for dresses in admin
 */

defined( 'ABSPATH' ) || exit;

class Dress_Sorter {
	private $post_type = 'dress'; // Assuming your post type is 'dress'
	private $meta_key  = 'dress_order';

	public function __construct() {
		add_action( 'wp_ajax_update_dress_order', array( $this, 'update_dress_order_via_ajax' ) );
		add_action( 'init', array( $this, 'register_dress_order_fields' ) );
		add_action( 'current_screen', array( $this, 'maybe_setup_dress_order_fields_value' ), 10, 1 );
		// add_action( 'init', array( $this, 'setup_dress_order_fields_value' ), 20 );
		add_action( 'pre_get_posts', array( $this, 'sort_dresses_by_order' ) );
		add_filter( 'manage_dress_posts_columns', array( $this, 'add_order_column' ) );
		add_action( 'manage_dress_posts_custom_column', array( $this, 'show_order_column' ), 10, 2 );
		add_action( 'wp_insert_post_data', array( $this, 'reorder_dresses_after_save' ), 10, 2 );
		add_action( 'acf/save_post', array( $this, 'reorder_dresses_in_categories_after_save' ), 5 );
		// add_action( 'wp_insert_post', array( $this, 'handle_new_dress' ), 20, 3 );
		// add_action( 'wp_insert_post', array( $this, 'handle_existing_dress' ), 20, 3 );
		// add_action( 'draft_to_publish', array( $this, 'handle_dress_publication' ), 10, 1 );
		// add_action( 'pending_to_publish', array( $this, 'handle_dress_publication' ), 10, 1 );
		add_action( 'before_delete_post', array( $this, 'handle_dress_deletion' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'handle_dress_deletion' ), 10, 1 );
	}

	public function register_dress_order_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$categories = get_terms(
			array(
				'taxonomy'   => 'dress_category',
				'hide_empty' => false,
			)
		);

		foreach ( $categories as $categories_item ) {
			$field = array(
				'key'           => 'field_dress_order_' . $categories_item->term_id,
				'label'         => sprintf( 'Порядок в категории: %s', $categories_item->name ),
				'name'          => 'dress_order_' . $categories_item->term_id,
				'type'          => 'number',
				'instructions'  => sprintf( 'Укажите порядковый номер в категории: <strong>%s</strong>', $categories_item->name ),
				'required'      => 0,
				'min'           => 0,
				'step'          => 1,
				'default_value' => 0,
			);

			acf_add_local_field_group(
				array(
					'key'                   => 'group_dress_order_' . $categories_item->term_id,
					'title'                 => 'Сортировка в категории "' . $categories_item->name . '"',
					'fields'                => array( $field ),
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'dress',
							),
							array(
								'param'    => 'post_taxonomy',
								'operator' => '==',
								'value'    => 'dress_category:' . $categories_item->slug,
							),
						),
					),
					'position'              => 'side',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
				)
			);
		}
	}

	public function setup_dress_order_fields_value() {
		$dress_taxonomy_names = array( 'dress_category' );
		$dress_taxonomies     = get_terms(
			array(
				'taxonomy'   => $dress_taxonomy_names,
				'hide_empty' => false,
			)
		);

		$dresses = get_posts(
			array(
				'post_type'   => $this->post_type,
				'numberposts' => -1,
			)
		);

		foreach ( $dresses as $dress ) {
			foreach ( $dress_taxonomies as $dress_tax ) {
				$dress_tax_order = get_field( $this->meta_key . '_' . $dress_tax->term_id, $dress->ID );

				if ( empty( $dress_tax_order ) && 0 !== $dress_tax_order ) {
					update_field( $this->meta_key . '_' . $dress_tax->term_id, 0, $dress->ID );
				}
			}
		}
	}

	public function maybe_setup_dress_order_fields_value() {
		// Only run on specific admin pages where it's needed
		$screen = get_current_screen();

		// Only run on the dress post type edit screen or when explicitly requested
		if ( isset( $_GET['setup_dress_order'] ) ||
		( $screen && 'dress' === $screen->post_type && 'edit' === $screen->base ) ) {
			$this->setup_dress_order_fields_value();
		}
	}

	public function update_dress_order_via_ajax() {
		check_ajax_referer( 'loveforever-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => 'Доступ запрещен!',
					'debug'   => 'Попытка обновить порядок платьев пользователем без соотвествующего уровня доступа',
				)
			);
		}

		global $wpdb;

		$order               = ! empty( $_POST['order'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['order'] ) ) : array();
		$order               = ! empty( $_POST['order'] ) ? array_map( 'intval', $order ) : array();
		$page                = ! empty( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$dress_category_slug = ! empty( $_POST['dress_category'] ) ? sanitize_text_field( wp_unslash( $_POST['dress_category'] ) ) : '';
		$posts_per_page      = ! empty( $_POST['posts_per_page'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['posts_per_page'] ) ) : 10;
		$dress_category      = ! empty( $dress_category_slug ) ? get_term_by( 'slug', $dress_category_slug, 'dress_category' ) : null;

		$result  = array();
		$result2 = array();

		$other_dresses_args = array(
			'post_type'      => 'dress',
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post__not_in'   => $order,
		);

		if ( $dress_category ) {
			$other_dresses_args['tax_query'][] = array(
				'taxonomy' => 'dress_category',
				'field'    => 'term_id',
				'terms'    => array( $dress_category->term_id ),
			);

			$other_dresses_args['meta_key'] = $this->meta_key . '_' . $dress_category->term_id;
			$other_dresses_args['orderby']  = 'meta_value_num';
		}

		$other_dresses = ( new WP_Query( $other_dresses_args ) )->posts;

		foreach ( $order as $index => $post_id ) {
			// $position = $index + 1;
			$position = ( $page - 1 ) * $posts_per_page + $index + 1;
			if ( $dress_category ) {
				update_field( $this->meta_key . '_' . $dress_category->term_id, $position, $post_id );
			} else {
				$wpdb->update(
					$wpdb->posts,
					array( 'menu_order' => $position ),
					array( 'ID' => $post_id )
				);
			}

			$result[ $position ] = $post_id;
		}

		foreach ( $other_dresses as $index => $other_dress_id ) {
			// For posts before current page
			if ( $index < ( $page - 1 ) * $posts_per_page ) {
				$new_position = $index + 1;
			}
			// For posts after current page (including the remaining posts on current page)
			else {
				$new_position = $posts_per_page * ( $page - 1 ) + count( $order ) + ( $index - ( $page - 1 ) * $posts_per_page ) + 1;
			}

			if ( $dress_category ) {
				update_field( $this->meta_key . '_' . $dress_category->term_id, $new_position, $other_dress_id );
			} else {
				$wpdb->update(
					$wpdb->posts,
					array( 'menu_order' => $new_position ),
					array( 'ID' => $other_dress_id )
				);
			}

			$result2[ $new_position ] = $other_dress_id;
		}

		wp_send_json_success(
			array(
				'result'  => $result,
				'result2' => $result2,
				'offset'  => $posts_per_page * $page,
				'message' => 'Порядок успешно обновлен',
			)
		);
	}

	public function sort_dresses_by_order( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || $this->post_type !== $query->get( 'post_type' ) ) {
			return;
		}

		$dress_category = isset( $_GET['dress_category'] ) ? get_term_by( 'slug', $_GET['dress_category'], 'dress_category' ) : null;

		if ( $dress_category ) {
			$query->set( 'meta_key', $this->meta_key . '_' . $dress_category->term_id );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
		} else {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}

		// var_dump( $query );
	}

	public function add_order_column( $columns ) {
		if ( ! empty( $_GET['dress_category'] ) ) {
			$dress_category        = get_term_by( 'slug', $_GET['dress_category'], 'dress_category' );
			$columns['menu_order'] = 'Порядок (' . $dress_category->name . ')';
		} else {
			$columns['menu_order'] = 'Порядок';
		}
		return $columns;
	}

	public function show_order_column( $column, $post_id ) {
		if ( 'menu_order' === $column ) {
			if ( ! empty( $_GET['dress_category'] ) ) {
				$dress_category = get_term_by( 'slug', $_GET['dress_category'], 'dress_category' );
				$order          = get_field( $this->meta_key . '_' . $dress_category->term_id, $post_id );
			} else {
				$order = get_post( $post_id )->menu_order;
			}
			echo ! empty( $order ) ? esc_html( $order ) : '0';
		}
	}

	public function reorder_dresses_after_save( $data, $postarr ) {
		global $wpdb;

		if (
			empty( $_POST['post_type'] ) ||
			$this->post_type !== $_POST['post_type'] ||
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
			! current_user_can( 'edit_post', $postarr['ID'] )
		) {
			return $data;
		}

		$allowed_statuses = array( 'publish', 'draft', 'pending' );

		// Обрабатываем только опубликованные посты
		if ( ! in_array( $data['post_status'], $allowed_statuses ) ) {
			return $data;
		}

		$current_dress = get_post( $postarr['ID'] );
		if ( ! $current_dress ) {
			return $data;
		}

		$new_order         = isset( $data['menu_order'] ) ? (int) $data['menu_order'] : 0;
		$previous_order    = (int) $current_dress->menu_order;
		$is_status_changed = $current_dress->post_status !== $data['post_status'];

		// Получаем количество опубликованных постов
		global $wpdb;
		$total_posts = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} 
				WHERE post_type = %s 
				AND post_status IN ('publish', 'draft', 'pending')",
				$this->post_type
			)
		);

		// Валидация menu_order
		if ( 0 === $new_order ) {
			$new_order = 1;
		} elseif ( 0 > $new_order ) {
			$new_order = $total_posts;
		} elseif ( $new_order > $total_posts ) {
			$new_order = $total_posts;
		}

		$data['menu_order'] = $new_order;

		if ( $new_order === $previous_order && ! $is_status_changed ) {
			return $data;
		}

		// Временно отключаем текущий хук
		remove_action( 'wp_insert_post_data', array( $this, 'reorder_dresses_after_save' ), 10 );

		// Обновляем только опубликованные посты
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts} 
				SET menu_order = menu_order - 1 
				WHERE post_type = %s 
				AND post_status = 'publish'
				AND menu_order > %d 
				AND menu_order <= %d",
				$this->post_type,
				$previous_order,
				$new_order
			)
		);

		// Восстанавливаем хук
		add_action( 'wp_insert_post_data', array( $this, 'reorder_dresses_after_save' ), 10, 2 );

		return $data;
	}

	public function reorder_dresses_in_categories_after_save( $post_id ) {
		// Check if this is a dress post type
		if ( get_post_type( $post_id ) !== $this->post_type ) {
			return;
		}

		$previous_field_values = get_fields( $post_id );

		// Get all dress categories for this post
		$dress_categories = wp_get_post_terms( $post_id, 'dress_category' );

		// If no categories are found, exit early to prevent data loss
		if ( empty( $dress_categories ) || is_wp_error( $dress_categories ) ) {
			return;
		}

		foreach ( $dress_categories as $category ) {
			if ( ! isset( $_POST['acf'][ 'field_' . $this->meta_key . '_' . $category->term_id ] ) ) {
				continue;
			}

			$previous_order = (int) $previous_field_values[ $this->meta_key . '_' . $category->term_id ];
			$new_order      = (int) $_POST['acf'][ 'field_' . $this->meta_key . '_' . $category->term_id ];

			if ( 0 === $new_order ) {
				$new_order = 1;
				$_POST['acf'][ 'field_' . $this->meta_key . '_' . $category->term_id ] = $new_order;
			}

			if ( $new_order > $category->count ) {
				$new_order = $category->count;
				$_POST['acf'][ 'field_' . $this->meta_key . '_' . $category->term_id ] = $new_order;
			}

			if ( $new_order === $previous_order ) {
				continue;
			}

			// Get all other dresses in this category
			$other_dresses = get_posts(
				array(
					'post_type'      => $this->post_type,
					'posts_per_page' => -1,
					'post__not_in'   => array( $post_id ),
					'tax_query'      => array(
						array(
							'taxonomy' => 'dress_category',
							'field'    => 'term_id',
							'terms'    => array( $category->term_id ),
						),
					),
					'meta_key'       => $this->meta_key . '_' . $category->term_id,
					'orderby'        => 'meta_value_num',
					'order'          => 'ASC',
				)
			);

			$position = 1;
			foreach ( $other_dresses as $dress ) {
				$dress_order = get_field( $this->meta_key . '_' . $category->term_id, $dress->ID );

				// Skip if this position is taken by the updated post
				if ( $position === $new_order ) {
					++$position;
				}

				// Only update if the order needs to change
				if ( $dress_order !== $position ) {
					update_field( $this->meta_key . '_' . $category->term_id, $position, $dress->ID );
				}

				++$position;
			}
		}
	}

	/**
	 * Обрабатывает удаление платья и перемещение в корзину.
	 * Обновляет порядковые номера в каждой категории.
	 *
	 * @param int $post_id ID удаляемого поста.
	 */
	public function handle_dress_deletion( $post_id ) {
		// Проверяем тип поста
		if ( get_post_type( $post_id ) !== $this->post_type ) {
			return;
		}

		global $wpdb;

		// Получаем текущий menu_order удаляемого поста
		$current_post = get_post( $post_id );
		if ( ! $current_post ) {
			return;
		}

		$current_menu_order = (int) $current_post->menu_order;

		// Обновляем menu_order для всех постов с большим порядковым номером
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->posts} 
				SET menu_order = menu_order - 1 
				WHERE post_type = %s 
				AND post_status IN ('publish', 'draft', 'pending')
				AND menu_order > %d",
				$this->post_type,
				$current_menu_order
			)
		);

		// Обработка категорий
		$dress_categories = wp_get_post_terms( $post_id, 'dress_category' );

		foreach ( $dress_categories as $category ) {
			$current_order = (int) get_field( $this->meta_key . '_' . $category->term_id, $post_id );

			// Получаем все платья в этой категории с большим порядковым номером
			$args = array(
				'post_type'      => $this->post_type,
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'post__not_in'   => array( $post_id ),
				'tax_query'      => array(
					array(
						'taxonomy' => 'dress_category',
						'field'    => 'term_id',
						'terms'    => $category->term_id,
					),
				),
				'meta_query'     => array(
					'dress_order_' . $category->term_id => array(
						'key'     => $this->meta_key . '_' . $category->term_id,
						'value'   => $current_order,
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				),
				'orderby'        => array(
					'dress_order_' . $category->term_id => 'ASC',
				),
			);

			$posts_to_update = ( new WP_Query( $args ) )->posts;

			foreach ( $posts_to_update as $post_id_to_update ) {
				$old_order = (int) get_field( $this->meta_key . '_' . $category->term_id, $post_id_to_update );
				update_field( $this->meta_key . '_' . $category->term_id, $old_order - 1, $post_id_to_update );
			}
		}
	}
}

new Dress_Sorter();

add_action(
	'init',
	function () {
		$other_dresses_args = array(
			'post_type'      => 'dress',
			'fields'         => 'ids',
			'posts_per_page' => -1,
			// 'meta_key'       => 'dress_order_4',
			// 'orderby'        => 'meta_value_num',
			// 'post__not_in'   => array( 101, 27, 177, 45, 105, 193, 55, 123, 204, 63 ),
			// 'paged'          => 2,
			// 'order'          => 'ASC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'dress_category',
					'field'    => 'term_id',
					'terms'    => array( 4 ),
				),
			),
		);

		// $posts = ( new WP_Query( $other_dresses_args ) )->posts;

		// foreach ( $posts as $posts_item ) {
		// update_field( 'dress_order_4', 0, $posts_item );
		// }

		// var_dump( $other_dresses_args );
		// var_dump( ( new WP_Query( $other_dresses_args ) )->posts );
	}
);
