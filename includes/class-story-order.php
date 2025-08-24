<?php
/**
 * Class Story_Order
 *
 * Handles the sorting functionality for story posts in dress_category taxonomy
 *
 * @package LoveForever
 */

defined( 'ABSPATH' ) || exit;

/**
 * Story_Order class.
 *
 * Manages custom ordering for story posts within dress_category taxonomy.
 */
class Story_Order {
	/**
	 * Singleton instance.
	 *
	 * @var Story_Order|null
	 */
	private static $instance = null;

	/**
	 * Post type to handle.
	 *
	 * @var string
	 */
	private $post_type = 'story';

	/**
	 * Meta key prefix for ordering.
	 *
	 * @var string
	 */
	private $meta_key = 'story_order';

	/**
	 * Get singleton instance.
	 *
	 * @return Story_Order
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'register_story_order_fields' ) );
		add_action( 'wp_ajax_update_story_order', array( $this, 'update_story_order_via_ajax' ) );
		add_filter( 'manage_story_posts_columns', array( $this, 'add_order_column' ) );
		add_filter( 'manage_edit-story_columns', array( $this, 'add_order_column' ) );
		add_action( 'manage_story_posts_custom_column', array( $this, 'show_order_column' ), 10, 2 );
		add_action( 'manage_story_custom_column', array( $this, 'show_order_column' ), 10, 2 );
		add_action( 'before_delete_post', array( $this, 'handle_story_deletion' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'handle_story_deletion' ), 10, 1 );
		add_action( 'set_object_terms', array( $this, 'add_story_order_meta_on_term_set' ), 10, 6 );
		add_action( 'pre_get_posts', array( $this, 'sort_stories_by_order' ) );
		add_action( 'save_post', array( $this, 'initialize_story_order_on_save' ), 10, 2 );
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {}

	/**
	 * Register ACF fields for story ordering in categories.
	 */
	public function register_story_order_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$categories = get_terms(
			array(
				'taxonomy'   => 'dress_category',
				'hide_empty' => false,
			)
		);

		foreach ( $categories as $category ) {
			$field = array(
				'key'           => 'field_story_order_' . $category->term_id,
				'label'         => sprintf( 'Порядок в категории: %s', $category->name ),
				'name'          => 'story_order_' . $category->term_id,
				'type'          => 'number',
				'instructions'  => sprintf( 'Укажите порядковый номер в категории: <strong>%s</strong>', $category->name ),
				'required'      => 0,
				'min'           => 0,
				'step'          => 1,
				'default_value' => 0,
			);

			acf_add_local_field_group(
				array(
					'key'                   => 'group_story_order_' . $category->term_id,
					'title'                 => 'Сортировка в категории "' . $category->name . '"',
					'fields'                => array( $field ),
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'story',
							),
							array(
								'param'    => 'post_taxonomy',
								'operator' => '==',
								'value'    => 'dress_category:' . $category->slug,
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

	/**
	 * Add story order meta when terms are set.
	 *
	 * @param int    $post_id    Post ID.
	 * @param array  $terms      Terms array.
	 * @param array  $tt_ids     Term taxonomy IDs.
	 * @param string $taxonomy   Taxonomy name.
	 * @param bool   $append     Whether to append terms.
	 * @param array  $old_tt_ids Old term taxonomy IDs.
	 */
	public function add_story_order_meta_on_term_set( $post_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		if ( 'dress_category' !== $taxonomy || get_post_type( $post_id ) !== $this->post_type ) {
			return;
		}

		$tt_ids     = array_map( 'absint', $tt_ids );
		$old_tt_ids = array_map( 'absint', $old_tt_ids );

		// Get removed terms.
		$removed_tt_ids = array_diff( $old_tt_ids, $tt_ids );

		// Remove meta fields for removed terms.
		foreach ( $removed_tt_ids as $term_id ) {
			$meta_key = $this->meta_key . '_' . $term_id;
			delete_post_meta( $post_id, $meta_key );
		}

		// Add meta fields for new terms.
		$new_tt_ids = array_diff( $tt_ids, $old_tt_ids );

		foreach ( $new_tt_ids as $term_id ) {
			$meta_key = $this->meta_key . '_' . $term_id;

			if ( ! metadata_exists( 'post', $post_id, $meta_key ) ) {
				update_post_meta( $post_id, $meta_key, 0 );
			}
		}
	}

	/**
	 * Add order column to admin list.
	 *
	 * @param array $columns Columns array.
	 * @return array
	 */
	public function add_order_column( $columns ) {
		// Always add the column, but show category name if filtered.
		if ( ! empty( $_GET['dress_category'] ) ) {
			$dress_category_slug = sanitize_text_field( wp_unslash( $_GET['dress_category'] ) );
			$dress_category      = get_term_by( 'slug', $dress_category_slug, 'dress_category' );

			if ( $dress_category ) {
				$columns['menu_order'] = 'Порядок (' . $dress_category->name . ')';
			} else {
				$columns['menu_order'] = 'Порядок';
			}
		} else {
			$columns['menu_order'] = 'Порядок';
		}

		return $columns;
	}

	/**
	 * Show order column content.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 */
	public function show_order_column( $column, $post_id ) {
		if ( 'menu_order' === $column ) {
			$order = 0;

			if ( ! empty( $_GET['dress_category'] ) ) {
				$dress_category_slug = sanitize_text_field( wp_unslash( $_GET['dress_category'] ) );
				$dress_category      = get_term_by( 'slug', $dress_category_slug, 'dress_category' );

				if ( $dress_category ) {
					$order = get_field( $this->meta_key . '_' . $dress_category->term_id, $post_id );

					// If order is not set, initialize it to 0.
					if ( '' === $order || null === $order ) {
						$order = 0;
						update_post_meta( $post_id, $this->meta_key . '_' . $dress_category->term_id, $order );
					}
				}
			}

			echo esc_html( $order );
		}
	}

	/**
	 * Update story order via AJAX.
	 */
	public function update_story_order_via_ajax() {
		check_ajax_referer( 'loveforever-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => 'Доступ запрещен!',
					'debug'   => 'Попытка обновить порядок историй пользователем без соответствующего уровня доступа',
				)
			);
		}

		$order               = ! empty( $_POST['order'] ) ? array_map( 'absint', wp_unslash( $_POST['order'] ) ) : array();
		$page                = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$dress_category_slug = ! empty( $_POST['dress_category'] ) ? sanitize_text_field( wp_unslash( $_POST['dress_category'] ) ) : '';
		$posts_per_page      = ! empty( $_POST['posts_per_page'] ) ? absint( wp_unslash( $_POST['posts_per_page'] ) ) : 10;
		$dress_category      = ! empty( $dress_category_slug ) ? get_term_by( 'slug', $dress_category_slug, 'dress_category' ) : null;

		if ( ! $dress_category ) {
			wp_send_json_error(
				array(
					'message' => 'Категория не найдена!',
				)
			);
		}

		$result  = array();
		$result2 = array();

		$other_stories_args = array(
			'post_type'      => $this->post_type,
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'post__not_in'   => $order,
			'tax_query'      => array(
				array(
					'taxonomy' => 'dress_category',
					'field'    => 'term_id',
					'terms'    => array( $dress_category->term_id ),
				),
			),
			'meta_key'       => $this->meta_key . '_' . $dress_category->term_id,
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
		);

		$other_stories = ( new WP_Query( $other_stories_args ) )->posts;

		// Update order for dragged stories.
		foreach ( $order as $index => $post_id ) {
			$position = ( $page - 1 ) * $posts_per_page + $index + 1;
			$meta_key = $this->meta_key . '_' . $dress_category->term_id;
			update_post_meta( $post_id, $meta_key, $position );

			$result[ $position ] = $post_id;
		}

		// Update order for other stories.
		foreach ( $other_stories as $index => $other_story_id ) {
			// For posts before current page.
			if ( $index < ( $page - 1 ) * $posts_per_page ) {
				$new_position = $index + 1;
			} else {
				// For posts after current page (including the remaining posts on current page).
				$new_position = $posts_per_page * ( $page - 1 ) + count( $order ) + ( $index - ( $page - 1 ) * $posts_per_page ) + 1;
			}

			$meta_key = $this->meta_key . '_' . $dress_category->term_id;
			update_post_meta( $other_story_id, $meta_key, $new_position );

			$result2[ $new_position ] = $other_story_id;
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

	/**
	 * Sort stories by order in admin.
	 *
	 * @param WP_Query $query Query object.
	 */
	public function sort_stories_by_order( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || $this->post_type !== $query->get( 'post_type' ) ) {
			return;
		}

		$dress_category_slug = isset( $_GET['dress_category'] ) ? sanitize_text_field( wp_unslash( $_GET['dress_category'] ) ) : '';
		$dress_category      = ! empty( $dress_category_slug ) ? get_term_by( 'slug', $dress_category_slug, 'dress_category' ) : null;

		if ( $dress_category ) {
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
			$query->set( 'meta_key', $this->meta_key . '_' . $dress_category->term_id );
		}
	}

	/**
	 * Initialize story order on post save.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function initialize_story_order_on_save( $post_id, $post ) {
		// Check if this is a story post.
		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		// Skip autosaves and revisions.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Get all dress categories.
		$categories = get_terms(
			array(
				'taxonomy'   => 'dress_category',
				'hide_empty' => false,
			)
		);

		// Initialize order for each category if not exists.
		foreach ( $categories as $category ) {
			$meta_key = $this->meta_key . '_' . $category->term_id;

			if ( ! metadata_exists( 'post', $post_id, $meta_key ) ) {
				update_post_meta( $post_id, $meta_key, 0 );
			}
		}
	}

	/**
	 * Handle story deletion and reorder remaining stories.
	 *
	 * @param int $post_id Post ID.
	 */
	public function handle_story_deletion( $post_id ) {
		// Check post type.
		if ( get_post_type( $post_id ) !== $this->post_type ) {
			return;
		}

		// Handle categories.
		$dress_categories = wp_get_post_terms( $post_id, 'dress_category' );

		foreach ( $dress_categories as $category ) {
			$current_order = (int) get_field( $this->meta_key . '_' . $category->term_id, $post_id );

			// Get all stories in this category with higher order number.
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
					'story_order_' . $category->term_id => array(
						'key'     => $this->meta_key . '_' . $category->term_id,
						'value'   => $current_order,
						'compare' => '>',
						'type'    => 'NUMERIC',
					),
				),
				'orderby'        => array(
					'story_order_' . $category->term_id => 'ASC',
				),
			);

			$stories_to_update = ( new WP_Query( $args ) )->posts;

			foreach ( $stories_to_update as $story_id_to_update ) {
				$old_order = (int) get_field( $this->meta_key . '_' . $category->term_id, $story_id_to_update );
				update_field( $this->meta_key . '_' . $category->term_id, $old_order - 1, $story_id_to_update );
			}
		}
	}

	/**
	 * Initialize order for all existing stories (for first activation).
	 * This method can be called manually or via WP-CLI.
	 */
	public function initialize_all_stories_order() {
		$stories = get_posts(
			array(
				'post_type'      => $this->post_type,
				'posts_per_page' => -1,
				'post_status'    => 'any',
			)
		);

		$categories = get_terms(
			array(
				'taxonomy'   => 'dress_category',
				'hide_empty' => false,
			)
		);

		$updated = 0;

		foreach ( $stories as $story ) {
			foreach ( $categories as $category ) {
				$meta_key = $this->meta_key . '_' . $category->term_id;

				if ( ! metadata_exists( 'post', $story->ID, $meta_key ) ) {
					update_post_meta( $story->ID, $meta_key, 0 );
					$updated++;
				}
			}
		}

		return $updated;
	}
}

Story_Order::get_instance();
