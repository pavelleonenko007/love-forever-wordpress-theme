<?php
/**
 * Review Importer
 *
 * @package LoveForever
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LoveForever_Review_Importer
 */
class LoveForever_Review_Importer {
	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Get instance of this class.
	 *
	 * @return LoveForever_Review_Importer
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
		add_action( 'wp_ajax_import_reviews', array( $this, 'ajax_import_reviews' ) );
	}

	/**
	 * Add import page to admin menu.
	 */
	public function add_import_page() {
		add_submenu_page(
			'edit.php?post_type=review',
			'Импорт отзывов',
			'Импорт отзывов',
			'manage_options',
			'review-import',
			array( $this, 'render_import_page' )
		);
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( 'review_page_review-import' !== $screen->id ) {
			return;
		}

		wp_enqueue_style(
			'loveforever-review-importer',
			get_template_directory_uri() . '/css/review-importer.css',
			array(),
			filemtime( get_template_directory() . '/css/review-importer.css' )
		);

		wp_enqueue_script(
			'loveforever-review-importer',
			get_template_directory_uri() . '/js/review-importer.js',
			array( 'jquery' ),
			filemtime( get_template_directory() . '/js/review-importer.js' ),
			true
		);

		wp_localize_script(
			'loveforever-review-importer',
			'loveforeverReviewImporter',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'import_reviews_nonce' ),
				'importing' => 'Импорт отзывов...',
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
			<h1>Импорт отзывов</h1>
			<div class="review-importer-container">
				<form id="review-import-form" method="post" enctype="multipart/form-data">
					<div class="form-field">
						<label for="review-csv">Выберите CSV файл:</label>
						<input type="file" name="review-csv" id="review-csv" accept=".csv" required>
					</div>
					<div class="form-field">
						<button type="submit" class="button button-primary">Импортировать</button>
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
	public function ajax_import_reviews() {
		check_ajax_referer( 'import_reviews_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Недостаточно прав для выполнения операции.' );
		}

		// Enable error logging
		error_log( 'Starting review import process' );

		// Получаем или создаем временный файл для хранения прогресса
		$temp_file = get_temp_dir() . 'review_import_' . get_current_user_id() . '.tmp';
		$cache_key = 'review_import_cache_' . get_current_user_id();

		// Если это первый запрос
		if ( ! isset( $_POST['current_row'] ) ) {
			if ( ! isset( $_FILES['file'] ) ) {
				error_log( 'No file uploaded' );
				wp_send_json_error( 'Файл не был загружен.' );
			}

			$file = array_map( 'sanitize_text_field', wp_unslash( $_FILES['file'] ) );
			error_log( 'File type: ' . $file['type'] );
			
			if ( 'text/csv' !== $file['type'] && 'application/vnd.ms-excel' !== $file['type'] ) {
				error_log( 'Invalid file type: ' . $file['type'] );
				wp_send_json_error( 'Неверный формат файла. Пожалуйста, загрузите CSV файл.' );
			}

			// Сохраняем CSV файл во временный файл
			if ( ! move_uploaded_file( $file['tmp_name'], $temp_file ) ) {
				error_log( 'Failed to move uploaded file to: ' . $temp_file );
				wp_send_json_error( 'Не удалось сохранить файл.' );
			}

			// Count actual number of reviews in CSV with caching
			$total_rows = wp_cache_get( $cache_key . '_total_rows' );
			if ( false === $total_rows ) {
				$total_rows = $this->count_csv_rows( $temp_file );
				wp_cache_set( $cache_key . '_total_rows', $total_rows, '', 3600 ); // Cache for 1 hour
			}

			error_log( 'Total reviews in file: ' . $total_rows );
			$current_row = 0;
			$imported    = 0;
			$skipped     = 0;
		} else {
			// Получаем сохраненный прогресс
			$current_row = isset( $_POST['current_row'] ) ? intval( $_POST['current_row'] ) : 0;
			$imported    = isset( $_POST['imported'] ) ? intval( $_POST['imported'] ) : 0;
			$skipped     = isset( $_POST['skipped'] ) ? intval( $_POST['skipped'] ) : 0;
			$total_rows  = isset( $_POST['total_rows'] ) ? intval( $_POST['total_rows'] ) : 0;
			error_log( "Continuing import from row {$current_row}. Imported: {$imported}, Skipped: {$skipped}" );
		}

		// Parse CSV with improved handling for multiline fields.
		$data = $this->parse_csv_row( $temp_file, $current_row );
		if ( false === $data ) {
			error_log( 'Import completed. Final stats - Imported: ' . $imported . ', Skipped: ' . $skipped );
			// Импорт завершен
			unlink( $temp_file ); // Удаляем временный файл
			
			// Clear cache after import completion
			$this->clear_import_cache( $cache_key );
			
			wp_send_json_success(
				array(
					'imported' => $imported,
					'skipped'  => $skipped,
					'total'    => $total_rows,
					'complete' => true,
				)
			);
		}

		error_log( 'Processing row ' . ($current_row + 1) . ': ' . print_r($data, true) );

		// Проверяем наличие обязательных полей (автор, дата и текст отзыва)
		if ( count( $data ) < 3 ) {
			error_log( 'Skipping row ' . ($current_row + 1) . ': Insufficient data columns' );
			$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
			return;
		}

		$author    = trim( $data[0], '"' );
		$date      = isset( $data[1] ) ? trim( $data[1], '"' ) : '';
		$text      = trim( $data[2], '"' );
		$image_url = isset( $data[3] ) ? trim( $data[3] ) : '';

		// Validate and clean data.
		$validation_result = $this->validate_review_data( $author, $date, $text, $data, $current_row );
		if ( ! $validation_result['valid'] ) {
			error_log( 'Skipping row ' . ( $current_row + 1 ) . ': ' . $validation_result['reason'] );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
			return;
		}

		// Use cleaned data.
		$author = $validation_result['author'];
		$date   = $validation_result['date'];
		$text   = $validation_result['text'];

		// Check for duplicates with caching
		$duplicate_hash = md5( $author . $text );
		$duplicate = wp_cache_get( 'review_duplicate_' . $duplicate_hash );
		
		if ( false === $duplicate ) {
			global $wpdb;

			// Check for duplicates based on author and review text (more reliable than image comparison).
			$query = $wpdb->prepare(
				"SELECT p.ID 
				FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm_author ON p.ID = pm_author.post_id AND pm_author.meta_key = 'author' AND pm_author.meta_value = %s
				INNER JOIN {$wpdb->postmeta} pm_text ON p.ID = pm_text.post_id AND pm_text.meta_key = 'review_text' AND pm_text.meta_value = %s
				WHERE p.post_type = 'review' 
				AND p.post_status = 'publish'
				LIMIT 1",
				$author,
				$text
			);

			$duplicate = $wpdb->get_var( $query );
			
			// Cache the result for 1 hour
			wp_cache_set( 'review_duplicate_' . $duplicate_hash, $duplicate, '', 3600 );
		}

		if ( $duplicate ) {
			error_log( 'Skipping duplicate review - Author: ' . $author . ', Text: ' . substr( $text, 0, 50 ) . '...' );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
			return;
		}

		// Create review post.
		$post_id = wp_insert_post(
			array(
				'post_title'  => $author,
				'post_type'   => 'review',
				'post_status' => 'publish',
			)
		);

		if ( is_wp_error( $post_id ) ) {
			error_log( 'Failed to create review post: ' . $post_id->get_error_message() );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
			return;
		}

		// Set ACF fields.
		$author_update = update_field( 'author', $author, $post_id );
		$text_update = update_field( 'review_text', $text, $post_id );
		$rating_update = update_field( 'rating', 5, $post_id );

		if ( ! $author_update || ! $text_update || ! $rating_update ) {
			error_log( 'Failed to update ACF fields for post ' . $post_id );
		}

		// Import and process all images (hybrid approach: first as thumbnail, rest in carousel).
		$image_urls = array_slice( $data, 3 ); // Get all image URLs starting from column 3.
		$carousel_data = array();
		$valid_image_count = 0;

		foreach ( $image_urls as $index => $url ) {
			$url = trim( $url );
			if ( empty( $url ) || ! $this->is_valid_image_url( $url ) ) {
				continue;
			}

			$image_id = $this->import_image( $url, $post_id );
			if ( $image_id ) {
				if ( 0 === $valid_image_count ) {
					// First valid image as post thumbnail.
					set_post_thumbnail( $post_id, $image_id );
					error_log( 'Successfully set thumbnail image ' . $image_id . ' for post ' . $post_id );
				} else {
					// Additional images in carousel.
					$carousel_data[] = array( 'image' => $image_id );
					error_log( 'Successfully added image ' . $image_id . ' to carousel for post ' . $post_id );
				}
				$valid_image_count++;
			} else {
				error_log( 'Failed to import image from URL: ' . $url );
			}
		}

		// Update ACF carousel field if we have additional images.
		if ( ! empty( $carousel_data ) ) {
			$carousel_update = update_field( 'image_carousel', $carousel_data, $post_id );
			if ( ! $carousel_update ) {
				error_log( 'Failed to update image_carousel field for post ' . $post_id );
			} else {
				error_log( 'Successfully updated image_carousel with ' . count( $carousel_data ) . ' images for post ' . $post_id );
			}
		}

		error_log( 'Successfully imported review - Author: ' . $author );
		++$imported;

		$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
	}

	/**
	 * Send progress update via AJAX.
	 *
	 * @param int $current_row Current row number.
	 * @param int $total_rows Total number of rows.
	 * @param int $imported Number of imported reviews.
	 * @param int $skipped Number of skipped reviews.
	 */
	private function send_progress_update( $current_row, $total_rows, $imported, $skipped ) {
		$progress = round( ( $current_row / $total_rows ) * 100 );

		wp_send_json_success(
			array(
				'progress'    => $progress,
				'imported'    => $imported,
				'skipped'     => $skipped,
				'total'       => $total_rows,
				'current_row' => $current_row,
				'complete'    => false,
				'message'     => sprintf(
					'Обработано %d из %d отзывов (%d%%). Импортировано: %d, Пропущено: %d',
					$current_row,
					$total_rows,
					$progress,
					$imported,
					$skipped
				),
			)
		);
	}

	/**
	 * Import image from URL.
	 *
	 * @param string $url Image URL.
	 * @param int    $post_id Post ID to attach image to.
	 * @return int|false Attachment ID or false on failure.
	 */
	private function import_image( $url, $post_id ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			return false;
		}

		$file_array = array(
			'name'     => basename( $url ),
			'tmp_name' => $tmp,
		);

		$attachment_id = media_handle_sideload( $file_array, $post_id );
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp );
			return false;
		}

		return $attachment_id;
	}

	/**
	 * Parse CSV row with improved handling for multiline fields and caching.
	 *
	 * @param string $file_path Path to CSV file.
	 * @param int    $row_index Row index to parse (0-based).
	 * @return array|false Parsed row data or false on failure.
	 */
	private function parse_csv_row( $file_path, $row_index ) {
		$cache_key = 'review_import_cache_' . get_current_user_id() . '_row_' . $row_index;
		
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
			// Cache the parsed data for 30 minutes
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
			// Remove quotes and trim whitespace.
			$field = trim( $field, '"' );
			$field = trim( $field );
			
			// Handle escaped quotes within the field.
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
	 * Validate and clean review data.
	 *
	 * @param string $author Author name.
	 * @param string $date Review date.
	 * @param string $text Review text.
	 * @param array  $data Full CSV row data.
	 * @param int    $row_index Row index for logging.
	 * @return array Validation result with cleaned data.
	 */
	private function validate_review_data( $author, $date, $text, $data, $row_index ) {
		$result = array(
			'valid'  => true,
			'reason' => '',
			'author' => $author,
			'date'   => $date,
			'text'   => $text,
		);

		// Validate author.
		if ( empty( $author ) ) {
			$result['valid']  = false;
			$result['reason'] = 'Empty author name';
			return $result;
		}

		// Clean and validate author name.
		$author = sanitize_text_field( $author );
		if ( strlen( $author ) < 2 ) {
			$result['valid']  = false;
			$result['reason'] = 'Author name too short (minimum 2 characters)';
			return $result;
		}
		$result['author'] = $author;

		// Validate and clean review text.
		if ( empty( $text ) ) {
			$result['valid']  = false;
			$result['reason'] = 'Empty review text';
			return $result;
		}

		// Clean review text but preserve formatting.
		$text = wp_kses_post( $text );
		if ( strlen( $text ) < 5 ) {
			$result['valid']  = false;
			$result['reason'] = 'Review text too short (minimum 5 characters)';
			return $result;
		}
		$result['text'] = $text;

		// Validate and clean date.
		if ( ! empty( $date ) ) {
			// Try to parse the date and convert to standard format.
			$parsed_date = $this->parse_review_date( $date );
			if ( $parsed_date ) {
				$result['date'] = $parsed_date;
			} else {
				// If date parsing fails, use current date.
				$result['date'] = current_time( 'Y-m-d' );
				error_log( 'Invalid date format in row ' . ( $row_index + 1 ) . ': ' . $date . ', using current date' );
			}
		} else {
			// If no date provided, use current date.
			$result['date'] = current_time( 'Y-m-d' );
		}

		// Validate image URLs if present.
		$image_urls = array_slice( $data, 3 );
		$valid_urls = array();
		foreach ( $image_urls as $url ) {
			$url = trim( $url );
			if ( ! empty( $url ) && $this->is_valid_image_url( $url ) ) {
				$valid_urls[] = $url;
			}
		}

		// Log if some image URLs are invalid.
		if ( count( $image_urls ) > count( $valid_urls ) ) {
			error_log( 'Row ' . ( $row_index + 1 ) . ': ' . ( count( $image_urls ) - count( $valid_urls ) ) . ' invalid image URLs skipped' );
		}

		return $result;
	}

	/**
	 * Parse review date from various formats.
	 *
	 * @param string $date_string Date string to parse.
	 * @return string|false Parsed date in Y-m-d format or false on failure.
	 */
	private function parse_review_date( $date_string ) {
		// Common date formats in the CSV.
		$formats = array(
			'd.m.Y',     // 27.02.2013
			'd/m/Y',     // 27/02/2013
			'Y-m-d',     // 2013-02-27
			'm/d/Y',     // 02/27/2013
		);

		foreach ( $formats as $format ) {
			$date = DateTime::createFromFormat( $format, $date_string );
			if ( $date && $date->format( $format ) === $date_string ) {
				return $date->format( 'Y-m-d' );
			}
		}

		// Try strtotime as fallback.
		$timestamp = strtotime( $date_string );
		if ( $timestamp !== false ) {
			return date( 'Y-m-d', $timestamp );
		}

		return false;
	}

	/**
	 * Validate image URL.
	 *
	 * @param string $url URL to validate.
	 * @return bool True if valid image URL.
	 */
	private function is_valid_image_url( $url ) {
		// Basic URL validation.
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		// Check if URL points to an image file.
		$path = parse_url( $url, PHP_URL_PATH );
		$extension = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
		$valid_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp' );

		return in_array( $extension, $valid_extensions, true );
	}

	/**
	 * Clear import cache for user.
	 *
	 * @param string $cache_key Base cache key.
	 */
	private function clear_import_cache( $cache_key ) {
		// Clear total rows cache
		wp_cache_delete( $cache_key . '_total_rows' );
		
		// Clear individual row caches (we don't know how many rows there are, so we clear a reasonable range)
		for ( $i = 0; $i < 1000; $i++ ) {
			wp_cache_delete( $cache_key . '_row_' . $i );
		}
		
		error_log( 'Cleared import cache for user ' . get_current_user_id() );
	}
}

// Initialize the importer.
LoveForever_Review_Importer::get_instance();
