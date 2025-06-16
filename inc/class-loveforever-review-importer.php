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

			// Count actual number of reviews in CSV
			$total_rows = 0;
			$handle = fopen( $temp_file, 'r' );
			if ( $handle ) {
				// Skip header row if exists
				fgetcsv( $handle, 0, ',' );
				
				while ( ( $data = fgetcsv( $handle, 0, ',' ) ) !== false ) {
					$total_rows++;
				}
				fclose( $handle );
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

		$handle = fopen( $temp_file, 'r' );
		if ( false === $handle ) {
			error_log( 'Failed to open file: ' . $temp_file );
			wp_send_json_error( 'Не удалось открыть файл.' );
		}

		// Skip header row if exists and already processed rows
		if ( $current_row === 0 ) {
			fgetcsv( $handle, 0, ',' );
		} else {
			// Skip header
			fgetcsv( $handle, 0, ',' );
			// Skip already processed rows
			for ( $i = 0; $i < $current_row; $i++ ) {
				fgetcsv( $handle, 0, ',' );
			}
		}

		// Обрабатываем следующую строку
		$data = fgetcsv( $handle, 0, ',' );
		fclose( $handle );

		if ( false === $data ) {
			error_log( 'Import completed. Final stats - Imported: ' . $imported . ', Skipped: ' . $skipped );
			// Импорт завершен
			unlink( $temp_file ); // Удаляем временный файл
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

		// Проверяем наличие обязательных полей (автор и текст отзыва)
		if ( count( $data ) < 2 ) {
			error_log( 'Skipping row ' . ($current_row + 1) . ': Insufficient data columns' );
			$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
			return;
		}

		$author    = trim( $data[0], '"' );
		$text      = trim( $data[1], '"' );
		$image_url = isset( $data[2] ) ? trim( $data[2] ) : '';

		// Validate data
		if ( empty( $author ) ) {
			error_log( 'Skipping row ' . ($current_row + 1) . ': Empty author' );
			++$skipped;
			$this->send_progress_update( $current_row + 1, $total_rows, $imported, $skipped );
			return;
		}

		// Get filename from the image URL in CSV
		$csv_image_filename = '';
		if ( ! empty( $image_url ) ) {
			$csv_image_filename = basename( parse_url( $image_url, PHP_URL_PATH ) );
		}

		global $wpdb;

		// Prepare the query to check for duplicates
		$query = $wpdb->prepare(
			"SELECT p.ID 
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm_author ON p.ID = pm_author.post_id AND pm_author.meta_key = 'author' AND pm_author.meta_value = %s
			INNER JOIN {$wpdb->postmeta} pm_text ON p.ID = pm_text.post_id AND pm_text.meta_key = 'review_text' AND pm_text.meta_value = %s
			LEFT JOIN {$wpdb->postmeta} pm_thumbnail ON p.ID = pm_thumbnail.post_id AND pm_thumbnail.meta_key = '_thumbnail_id'
			LEFT JOIN {$wpdb->posts} attachment ON pm_thumbnail.meta_value = attachment.ID
			WHERE p.post_type = 'review' 
			AND p.post_status = 'publish'
			AND (
				(%s = '' AND pm_thumbnail.meta_value IS NULL)
				OR 
				(%s != '' AND attachment.post_title = %s)
			)
			LIMIT 1",
			$author,
			$text,
			$csv_image_filename,
			$csv_image_filename,
			$csv_image_filename
		);

		$duplicate = $wpdb->get_var( $query );

		if ( $duplicate ) {
			error_log( 'Skipping duplicate review - Author: ' . $author . ', Image filename: ' . $csv_image_filename );
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

		// Import and attach image if URL is provided.
		if ( ! empty( $image_url ) ) {
			$image_id = $this->import_image( $image_url, $post_id );
			if ( $image_id ) {
				set_post_thumbnail( $post_id, $image_id );
				error_log( 'Successfully attached image ' . $image_id . ' to post ' . $post_id );
			} else {
				error_log( 'Failed to import image from URL: ' . $image_url );
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
}

// Initialize the importer.
LoveForever_Review_Importer::get_instance();
