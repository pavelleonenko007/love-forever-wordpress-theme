<?php
/**
 * Class Promo_Sorter
 *
 * Handles the sorting functionality for promo blocks in admin
 */

defined( 'ABSPATH' ) || exit;

class Promo_Sorter {
	private static $instance = null;

	private $post_type = 'promo_blocks';
	private $meta_key  = 'promo_order';

	/**
	 * Get singleton instance.
	 *
	 * @return Dress_Sorter
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// add_action( 'init', array( $this, 'register_promo_order_fields' ) );
		add_action( 'wp_ajax_update_promo_order', array( $this, 'update_promo_order_via_ajax' ) );
		add_filter( 'manage_promo_blocks_posts_columns', array( $this, 'add_order_column' ) );
		add_action( 'manage_promo_blocks_posts_custom_column', array( $this, 'show_order_column' ), 10, 2 );
		add_action( 'before_delete_post', array( $this, 'handle_dress_deletion' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'handle_dress_deletion' ), 10, 1 );
		add_action( 'set_object_terms', array( $this, 'add_promo_order_meta_on_term_set' ), 10, 6 ); // для массового добавления товаров в категории (добавляет порядок в категории)
		add_action( 'pre_get_posts', array( $this, 'sort_promo_blocks_by_order' ) );
	}
	private function __clone() {}
	public function __wakeup() {}

	public function register_promo_order_fields() {
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
				'key'           => 'field_promo_order_' . $categories_item->term_id,
				'label'         => sprintf( 'Порядок в категории: %s', $categories_item->name ),
				'name'          => 'promo_order_' . $categories_item->term_id,
				'type'          => 'number',
				'instructions'  => sprintf( 'Укажите порядковый номер в категории: <strong>%s</strong>', $categories_item->name ),
				'required'      => 0,
				'min'           => 0,
				'step'          => 1,
				'default_value' => 0,
			);

			acf_add_local_field_group(
				array(
					'key'                   => 'group_promo_order_' . $categories_item->term_id,
					'title'                 => 'Сортировка в категории "' . $categories_item->name . '"',
					'fields'                => array( $field ),
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'promo_blocks',
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

	function add_promo_order_meta_on_term_set( $post_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		if ( $taxonomy !== 'dress_category' || get_post_type( $post_id ) !== $this->post_type ) {
			return;
		}

		$tt_ids     = array_map( 'absint', $tt_ids );
		$old_tt_ids = array_map( 'absint', $old_tt_ids );

		// Получаем удаленные термины
		$removed_tt_ids = array_diff( $old_tt_ids, $tt_ids );

		// Удаляем метаполя для удаленных терминов
		foreach ( $removed_tt_ids as $term_id ) {
			$meta_key = $this->meta_key . '_' . $term_id;
			delete_post_meta( $post_id, $meta_key );
		}

		// Добавляем метаполя для новых терминов
		$new_tt_ids = array_diff( $tt_ids, $old_tt_ids );

		foreach ( $new_tt_ids as $term_id ) {
			$meta_key = $this->meta_key . '_' . $term_id;

			if ( ! metadata_exists( 'post', $post_id, $meta_key ) ) {
				update_post_meta( $post_id, $meta_key, 0 );
			}
		}
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

	public function update_promo_order_via_ajax() {
		check_ajax_referer( 'loveforever-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => 'Доступ запрещен!',
					'debug'   => 'Попытка обновить порядок платьев пользователем без соответствующего уровня доступа',
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
			'post_type'      => 'promo_blocks',
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
				$meta_key = $this->meta_key . '_' . $dress_category->term_id;
				update_post_meta( $post_id, $meta_key, $position );
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
				$meta_key = $this->meta_key . '_' . $dress_category->term_id;
				update_post_meta( $other_dress_id, $meta_key, $new_position );
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

	public function sort_promo_blocks_by_order( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || $this->post_type !== $query->get( 'post_type' ) ) {
			return;
		}

		$dress_category = isset( $_GET['dress_category'] ) ? get_term_by( 'slug', $_GET['dress_category'], 'dress_category' ) : null;

		if ( $dress_category ) {
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
			$query->set( 'meta_key', $this->meta_key . '_' . $dress_category->term_id );
		} else {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}
	}

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
					'promo_order_' . $category->term_id => array(
						'key'     => $this->meta_key . '_' . $category->term_id,
						'value'   => $current_order,
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				),
				'orderby'        => array(
					'promo_order_' . $category->term_id => 'ASC',
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

Promo_Sorter::get_instance();
