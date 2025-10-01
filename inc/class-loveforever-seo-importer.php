<?php
/**
 * SEO Importer
 *
 * @package LoveForever
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LoveForever_SEO_Importer
 */
class LoveForever_SEO_Importer {
	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get instance of this class.
	 *
	 * @return LoveForever_SEO_Importer
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
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_import_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_import_seo', array( $this, 'ajax_import_seo' ) );
		add_filter( 'upload_size_limit', array( $this, 'increase_upload_limit' ) );
		add_filter( 'wp_max_upload_size', array( $this, 'increase_upload_limit' ) );
	}

	/**
	 * Increase upload limit for CSV files.
	 *
	 * @param int $size Current upload size limit.
	 * @return int New upload size limit.
	 */
	public function increase_upload_limit( $size ) {
		// Check if we're on the SEO import page.
		$screen = get_current_screen();
		if ( $screen && 'dress_page_seo-import' === $screen->id ) {
			return 1024 * 1024; // 1MB.
		}
		return $size;
	}

	/**
	 * Add import page to admin menu.
	 */
	public function add_import_page() {
		add_submenu_page(
			'edit.php?post_type=dress',
			'Импорт SEO данных',
			'Импорт SEO',
			'manage_options',
			'seo-import',
			array( $this, 'render_import_page' )
		);
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( 'dress_page_seo-import' !== $screen->id ) {
			return;
		}

		wp_enqueue_style(
			'loveforever-seo-importer',
			get_template_directory_uri() . '/css/seo-importer.css',
			array(),
			filemtime( get_template_directory() . '/css/seo-importer.css' )
		);

		wp_enqueue_script(
			'loveforever-seo-importer',
			get_template_directory_uri() . '/js/seo-importer.js',
			array( 'jquery' ),
			filemtime( get_template_directory() . '/js/seo-importer.js' ),
			true
		);

		wp_localize_script(
			'loveforever-seo-importer',
			'loveforeverSeoImporter',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'import_seo_nonce' ),
				'importing' => 'Импорт SEO данных...',
				'complete'  => 'Импорт завершен!',
				'error'     => 'Произошла ошибка при импорте.',
			)
		);
	}

	/**
	 * Render import page.
	 */
	public function render_import_page() {
		?>
		<div class="wrap">
			<h1>Импорт SEO данных</h1>
			<div class="seo-importer-container">
				<div class="import-info">
					<h3>Формат CSV файла:</h3>
					<p>1 столбец - slug поста, 2 столбец - slug термина dress_category, 3 столбец - title, 4 столбец - description</p>
					<p><strong>Максимальный размер файла:</strong> 1 МБ</p>
				</div>
				<form id="seo-import-form" method="post" enctype="multipart/form-data">
					<div class="form-field">
						<label for="seo-csv">Выберите CSV файл:</label>
						<input type="file" name="seo-csv" id="seo-csv" accept=".csv" required>
					</div>
					<div class="form-field">
						<button type="submit" class="button button-primary">Запуск</button>
					</div>
				</form>
				<div id="import-progress" style="display: none;">
					<div class="progress-bar">
						<div class="progress-bar-fill"></div>
					</div>
					<div class="progress-status"></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle AJAX import request.
	 */
	public function ajax_import_seo() {
		check_ajax_referer( 'import_seo_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Недостаточно прав для выполнения операции.' );
		}

		// Enable error logging.
		error_log( 'Starting SEO import process' );

		// Получаем или создаем временный файл для хранения прогресса.
		$temp_file = get_temp_dir() . 'seo_import_' . get_current_user_id() . '.tmp';
		$cache_key = 'seo_import_cache_' . get_current_user_id();

		// Если это первый запрос.
		if ( ! isset( $_POST['current_row'] ) ) {
			if ( ! isset( $_FILES['file'] ) ) {
				error_log( 'No file uploaded' );
				wp_send_json_error( 'Файл не был загружен.' );
			}

			$file = array_map( 'sanitize_text_field', wp_unslash( $_FILES['file'] ) );
			error_log( 'File type: ' . $file['type'] );
			error_log( 'File size: ' . $file['size'] );
			
			// Check file size (1MB limit).
			$max_size = 1024 * 1024; // 1MB.
			if ( $file['size'] > $max_size ) {
				error_log( 'File too large: ' . $file['size'] . ' bytes' );
				wp_send_json_error( 'Файл слишком большой. Максимальный размер: 1 МБ.' );
			}
			
			// Check file type.
			if ( 'text/csv' !== $file['type'] && 'application/vnd.ms-excel' !== $file['type'] ) {
				error_log( 'Invalid file type: ' . $file['type'] );
				wp_send_json_error( 'Неверный формат файла. Пожалуйста, загрузите CSV файл.' );
			}

			// Сохраняем CSV файл во временный файл.
			if ( ! move_uploaded_file( $file['tmp_name'], $temp_file ) ) {
				error_log( 'Failed to move uploaded file to: ' . $temp_file );
				wp_send_json_error( 'Не удалось сохранить файл.' );
			}

			// Count actual number of rows in CSV with caching.
			$total_rows = wp_cache_get( $cache_key . '_total_rows' );
			if ( false === $total_rows ) {
				$total_rows = $this->count_csv_rows( $temp_file );
				wp_cache_set( $cache_key . '_total_rows', $total_rows, '', 3600 ); // Cache for 1 hour.
			}

			error_log( 'Total rows in file: ' . $total_rows );
			
			// Check if file is empty or has no data rows.
			if ( $total_rows === 0 ) {
				unlink( $temp_file );
				wp_send_json_error( 'CSV файл пустой или не содержит данных для обработки.' );
			}
			
			$current_row = 0;
			$updated     = 0;
			$skipped     = 0;
		} else {
			// Получаем сохраненный прогресс.
			$current_row = isset( $_POST['current_row'] ) ? intval( $_POST['current_row'] ) : 0;
			$updated     = isset( $_POST['updated'] ) ? intval( $_POST['updated'] ) : 0;
			$skipped     = isset( $_POST['skipped'] ) ? intval( $_POST['skipped'] ) : 0;
			$total_rows  = isset( $_POST['total_rows'] ) ? intval( $_POST['total_rows'] ) : 0;
			error_log( "Continuing import from row {$current_row}. Updated: {$updated}, Skipped: {$skipped}" );
		}

		// Parse CSV row.
		$data = $this->parse_csv_row( $temp_file, $current_row );
		if ( false === $data ) {
			error_log( 'Import completed. Final stats - Updated: ' . $updated . ', Skipped: ' . $skipped );
			// Импорт завершен.
			unlink( $temp_file ); // Удаляем временный файл.

			// Clear cache after import completion.
			$this->clear_import_cache( $cache_key );
			
			wp_send_json_success(
				array(
					'updated'  => $updated,
					'skipped'  => $skipped,
					'total'    => $total_rows,
					'complete' => true,
				)
			);
		}

		error_log( 'Processing row ' . ( $current_row + 1 ) . ': ' . print_r( $data, true ) );

		// Проверяем наличие обязательных полей.
		if ( count( $data ) < 4 ) {
			error_log( 'Skipping row ' . ( $current_row + 1 ) . ': Insufficient data columns' );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $updated, $skipped );
			return;
		}

		$post_slug = trim( $data[0], '"' );
		$term_slug = trim( $data[1], '"' );
		$seo_title = trim( $data[2], '"' );
		$seo_desc  = trim( $data[3], '"' );

		// Validate and clean data.
		$validation_result = $this->validate_seo_data( $post_slug, $term_slug, $seo_title, $seo_desc, $current_row );
		if ( ! $validation_result['valid'] ) {
			error_log( 'Skipping row ' . ( $current_row + 1 ) . ': ' . $validation_result['reason'] );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $updated, $skipped );
			return;
		}

		// Use cleaned data.
		$post_slug = $validation_result['post_slug'];
		$term_slug = $validation_result['term_slug'];
		$seo_title = $validation_result['seo_title'];
		$seo_desc  = $validation_result['seo_desc'];

		// Find post by slug.
		$post = get_page_by_path( $post_slug, OBJECT, 'dress' );
		if ( ! $post ) {
			error_log( 'Post not found with slug: ' . $post_slug );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $updated, $skipped );
			return;
		}

		// Find term by slug.
		$term = get_term_by( 'slug', $term_slug, 'dress_category' );
		if ( ! $term ) {
			error_log( 'Term not found with slug: ' . $term_slug );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $updated, $skipped );
			return;
		}

		// Update Yoast SEO fields.
		$title_updated = update_post_meta( $post->ID, '_yoast_wpseo_title', $seo_title );
		$desc_updated  = update_post_meta( $post->ID, '_yoast_wpseo_metadesc', $seo_desc );

		// Update ACF dress_category field - add term if not already present.
		$existing_categories = get_field( 'dress_category', $post->ID );
		$category_updated = false;
		
		if ( empty( $existing_categories ) ) {
			// No existing categories, set the new one.
			$category_updated = update_field( 'dress_category', $term->term_id, $post->ID );
			error_log( 'Set new category ' . $term->term_id . ' for post ' . $post->ID );
		} else {
			// Convert to array if it's not already.
			$existing_categories = is_array( $existing_categories ) ? $existing_categories : array( $existing_categories );
			
			// Check if term is already assigned.
			if ( ! in_array( $term->term_id, $existing_categories, true ) ) {
				// Add new term to existing categories.
				$existing_categories[] = $term->term_id;
				$category_updated = update_field( 'dress_category', $existing_categories, $post->ID );
				error_log( 'Added category ' . $term->term_id . ' to existing categories for post ' . $post->ID );
			} else {
				error_log( 'Category ' . $term->term_id . ' already exists for post ' . $post->ID );
			}
		}

		if ( $title_updated || $desc_updated || $category_updated ) {
			error_log( 'Successfully updated post ' . $post->ID . ' (slug: ' . $post_slug . ')' );
			++$updated;
		} else {
			error_log( 'No changes made to post ' . $post->ID . ' (slug: ' . $post_slug . ')' );
			++$skipped;
		}

		$this->send_progress_update( $current_row + 1, $total_rows, $updated, $skipped );
	}

	/**
	 * Send progress update via AJAX.
	 *
	 * @param int $current_row Current row number.
	 * @param int $total_rows Total number of rows.
	 * @param int $updated Number of updated posts.
	 * @param int $skipped Number of skipped posts.
	 */
	private function send_progress_update( $current_row, $total_rows, $updated, $skipped ) {
		$progress = $total_rows > 0 ? round( ( $current_row / $total_rows ) * 100 ) : 0;

		wp_send_json_success(
			array(
				'progress'    => $progress,
				'updated'     => $updated,
				'skipped'     => $skipped,
				'total'       => $total_rows,
				'current_row' => $current_row,
				'complete'    => false,
				'message'     => sprintf(
					'Обработано %d из %d записей (%d%%). Обновлено: %d, Пропущено: %d',
					$current_row,
					$total_rows,
					$progress,
					$updated,
					$skipped
				),
			)
		);
	}

	/**
	 * Parse CSV row with improved handling for multiline fields and caching.
	 *
	 * @param string $file_path Path to CSV file.
	 * @param int    $row_index Row index to parse (0-based).
	 * @return array|false Parsed row data or false on failure.
	 */
	private function parse_csv_row( $file_path, $row_index ) {
		$cache_key = 'seo_import_cache_' . get_current_user_id() . '_row_' . $row_index;
		
		// Try to get from cache first
		$data = wp_cache_get( $cache_key );
		if ( false !== $data ) {
			return $data;
		}

		$handle = fopen( $file_path, 'r' );
		if ( false === $handle ) {
			return false;
		}

		$current_row = 0;
		$data = false;

		// Skip header row if exists.
		if ( $row_index >= 0 ) {
			fgetcsv( $handle, 0, ',' );
		}

		// Skip to the target row.
		while ( $current_row <= $row_index ) {
			$data = fgetcsv( $handle, 0, ',' );
			if ( false === $data ) {
				break;
			}
			$current_row++;
		}

		fclose( $handle );

		// If we have multiline content, try to parse it more carefully.
		if ( $data && count( $data ) > 0 ) {
			$data = $this->clean_csv_data( $data );
			// Cache the parsed data for 30 minutes.
			wp_cache_set( $cache_key, $data, '', 1800 );
		}

		return $data;
	}

	/**
	 * Clean and validate CSV data.
	 *
	 * @param array $data Raw CSV data.
	 * @return array Cleaned data.
	 */
	private function clean_csv_data( $data ) {
		$cleaned = array();
		
		foreach ( $data as $index => $field ) {
			// Remove quotes and trim whitespace
			$field = trim( $field, '"' );
			$field = trim( $field );
			
			// Handle escaped quotes within the field
			$field = str_replace( '""', '"', $field );
			
			$cleaned[ $index ] = $field;
		}

		return $cleaned;
	}

	/**
	 * Count total number of data rows in CSV file.
	 *
	 * @param string $file_path Path to CSV file.
	 * @return int Number of data rows.
	 */
	private function count_csv_rows( $file_path ) {
		$handle = fopen( $file_path, 'r' );
		if ( false === $handle ) {
			return 0;
		}

		$total_rows = 0;
		
		// Skip header row if exists.
		fgetcsv( $handle, 0, ',' );
		
		// Count data rows.
		while ( fgetcsv( $handle, 0, ',' ) !== false ) {
			$total_rows++;
		}
		
		fclose( $handle );
		
		return $total_rows;
	}

	/**
	 * Validate and clean SEO data.
	 *
	 * @param string $post_slug Post slug.
	 * @param string $term_slug Term slug.
	 * @param string $seo_title SEO title.
	 * @param string $seo_desc SEO description.
	 * @param int    $row_index Row index for logging.
	 * @return array Validation result with cleaned data.
	 */
	private function validate_seo_data( $post_slug, $term_slug, $seo_title, $seo_desc, $row_index ) {
		$result = array(
			'valid'     => true,
			'reason'    => '',
			'post_slug' => $post_slug,
			'term_slug' => $term_slug,
			'seo_title' => $seo_title,
			'seo_desc'  => $seo_desc,
		);

		// Validate post slug
		if ( empty( $post_slug ) ) {
			$result['valid']  = false;
			$result['reason'] = 'Empty post slug';
			return $result;
		}

		$post_slug = sanitize_title( $post_slug );
		if ( strlen( $post_slug ) < 1 ) {
			$result['valid']  = false;
			$result['reason'] = 'Invalid post slug';
			return $result;
		}
		$result['post_slug'] = $post_slug;

		// Validate term slug
		if ( empty( $term_slug ) ) {
			$result['valid']  = false;
			$result['reason'] = 'Empty term slug';
			return $result;
		}

		$term_slug = sanitize_title( $term_slug );
		if ( strlen( $term_slug ) < 1 ) {
			$result['valid']  = false;
			$result['reason'] = 'Invalid term slug';
			return $result;
		}
		$result['term_slug'] = $term_slug;

		// Validate SEO title
		if ( empty( $seo_title ) ) {
			$result['valid']  = false;
			$result['reason'] = 'Empty SEO title';
			return $result;
		}

		$seo_title = sanitize_text_field( $seo_title );
		if ( strlen( $seo_title ) < 5 ) {
			$result['valid']  = false;
			$result['reason'] = 'SEO title too short (minimum 5 characters)';
			return $result;
		}
		$result['seo_title'] = $seo_title;

		// Validate SEO description
		if ( empty( $seo_desc ) ) {
			$result['valid']  = false;
			$result['reason'] = 'Empty SEO description';
			return $result;
		}

		$seo_desc = sanitize_textarea_field( $seo_desc );
		if ( strlen( $seo_desc ) < 10 ) {
			$result['valid']  = false;
			$result['reason'] = 'SEO description too short (minimum 10 characters)';
			return $result;
		}
		$result['seo_desc'] = $seo_desc;

		return $result;
	}

	/**
	 * Clear import cache for user.
	 *
	 * @param string $cache_key Base cache key.
	 */
	private function clear_import_cache( $cache_key ) {
		// Clear total rows cache.
		wp_cache_delete( $cache_key . '_total_rows' );
		
		// Clear individual row caches (we don't know how many rows there are, so we clear a reasonable range).
		for ( $i = 0; $i < 1000; $i++ ) {
			wp_cache_delete( $cache_key . '_row_' . $i );
		}
		
		error_log( 'Cleared SEO import cache for user ' . get_current_user_id() );
	}
}

// Initialize the importer.
LoveForever_SEO_Importer::get_instance();
