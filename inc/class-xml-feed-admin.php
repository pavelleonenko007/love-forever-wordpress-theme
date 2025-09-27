<?php
/**
 * XML Feed Admin Interface
 * 
 * @package LoveForever
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class XML_Feed_Admin {
	
	/**
	 * Initialize admin interface
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_xml_feed_manual_generation', array( $this, 'handle_manual_generation' ) );
		add_action( 'wp_ajax_xml_feed_batch_generation', array( $this, 'handle_batch_generation' ) );
		add_action( 'wp_ajax_xml_feed_cron_status', array( $this, 'handle_cron_status' ) );
		add_action( 'wp_ajax_xml_feed_reschedule', array( $this, 'handle_reschedule' ) );
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_management_page(
			'Генератор XML фидов',
			'Генератор XML фидов',
			'manage_options',
			'xml-feed-generator',
			array( $this, 'admin_page' )
		);
	}
	
	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_scripts( $hook ) {
		if ( $hook !== 'tools_page_xml-feed-generator' ) {
			return;
		}
		
		wp_enqueue_script( 'jquery' );
		wp_add_inline_script( 'jquery', $this->get_admin_js() );
		wp_add_inline_style( 'wp-admin', $this->get_admin_css() );
	}
	
	/**
	 * Admin page content
	 */
	public function admin_page() {
		$cron_status = $this->get_cron_status();
		$last_run = get_option( 'xml_feed_last_run', array() );
		$generator = XML_Feed_Generator::get_instance();
		$categories = $generator->get_available_categories();
		
		// Fallback: If no categories found, try to get them directly
		if ( empty( $categories ) ) {
			$allowed_categories = array( 'wedding', 'evening', 'prom', 'wedding-sale' );
			$categories = array();
			
			foreach ( $allowed_categories as $category_slug ) {
				$category = get_term_by( 'slug', $category_slug, 'dress_category' );
				if ( $category && ! is_wp_error( $category ) ) {
					$categories[] = $category;
				}
			}
		}
		
		
		?>
		<div class="wrap">
			<h1>Генератор XML фидов</h1>
			
			<div class="xml-feed-dashboard">
				
				<!-- Status Cards -->
				<div class="xml-feed-cards">
					<div class="xml-feed-card">
						<h3>Статус Cron</h3>
						<div class="status-info">
							<?php if ( $cron_status['is_scheduled'] ): ?>
								<span class="status-badge success">Активен</span>
								<p>Следующий запуск: через <?php echo esc_html( $cron_status['next_run_relative'] ); ?></p>
								<p>Точное время: <?php echo esc_html( $cron_status['next_run'] ); ?></p>
								<p>Интервал: <?php echo esc_html( $cron_status['current_interval_name'] ); ?></p>
							<?php else: ?>
								<span class="status-badge error">Неактивен</span>
								<p>Cron не запланирован</p>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="xml-feed-card">
						<h3>Последняя генерация</h3>
						<div class="status-info">
							<?php if ( ! empty( $last_run ) ): ?>
								<span class="status-badge <?php echo esc_attr( $last_run['status'] ); ?>">
									<?php 
									$status_names = array(
										'success' => 'Успешно',
										'partial' => 'Частично',
										'error' => 'Ошибка'
									);
									echo esc_html( $status_names[ $last_run['status'] ] ?? ucfirst( $last_run['status'] ) );
									?>
								</span>
								<p><?php echo esc_html( $last_run['timestamp'] ); ?></p>
								<p>Успешно: <?php echo intval( $last_run['success_count'] ); ?>, 
								   Ошибок: <?php echo intval( $last_run['error_count'] ); ?></p>
							<?php else: ?>
								<span class="status-badge warning">Никогда не запускался</span>
								<p>Предыдущих генераций не найдено</p>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="xml-feed-card">
						<h3>Созданные файлы</h3>
						<div class="status-info">
							<?php
							$xml_dir = ABSPATH . 'xml/';
							$files = array(
								'wedding.xml', 'evening.xml', 'prom.xml', 'wedding-sale.xml',
								'wedding-360.xml', 'evening-72.xml', 'prom-96.xml', 'combined.xml'
							);
							$existing_files = 0;
							foreach ( $files as $file ) {
								if ( file_exists( $xml_dir . $file ) ) {
									$existing_files++;
								}
							}
							?>
							<span class="status-badge <?php echo $existing_files > 0 ? 'success' : 'warning'; ?>">
								<?php echo $existing_files; ?>/<?php echo count( $files ); ?>
							</span>
							<p>XML файлов существует</p>
						</div>
					</div>
				</div>
				
				<!-- Actions -->
				<div class="xml-feed-actions">
					<h2>Действия</h2>
					
					<div class="action-buttons">
						<button type="button" id="manual-generation" class="button button-primary">
							Сгенерировать все фиды сейчас
						</button>
						
						<button type="button" id="batch-generation" class="button button-secondary">
							Сгенерировать с батчевой обработкой
						</button>
						
						<button type="button" id="refresh-status" class="button">
							Обновить статус
						</button>
						
						<button type="button" id="reschedule-cron" class="button">
							Перепланировать Cron
						</button>
					</div>
					
					<div class="cron-settings">
						<h3>Настройки Cron</h3>
						<div class="notice notice-warning inline">
							<p><strong>Внимание:</strong> Частая генерация (каждые 5-15 минут) может увеличить нагрузку на сервер. Используйте с осторожностью на продакшн сайтах.</p>
						</div>
						<form id="cron-settings-form">
							<label for="cron-interval">Интервал:</label>
							<select id="cron-interval" name="interval">
								<?php
								$current_interval = get_option( 'xml_feed_cron_interval', 'xml_feed_interval' );
								$intervals = array(
									'xml_feed_5min' => 'Каждые 5 минут',
									'xml_feed_15min' => 'Каждые 15 минут',
									'xml_feed_30min' => 'Каждые 30 минут',
									'xml_feed_hourly' => 'Каждый час',
									'hourly' => 'Каждый час (WordPress по умолчанию)',
									'xml_feed_interval' => 'Каждые 6 часов',
									'twicedaily' => 'Дважды в день',
									'daily' => 'Один раз в день'
								);
								
								foreach ( $intervals as $value => $label ) {
									$selected = selected( $current_interval, $value, false );
									echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
								}
								?>
							</select>
							<button type="submit" class="button button-secondary">Сохранить настройки</button>
						</form>
					</div>
				</div>
				
				<!-- Categories -->
				<div class="xml-feed-categories">
					<h2>Доступные категории</h2>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th>Категория</th>
								<th>Slug</th>
								<th>Товары</th>
								<th>Действия</th>
							</tr>
						</thead>
						<tbody>
							<?php if ( empty( $categories ) ): ?>
								<tr>
									<td colspan="4" style="text-align: center; color: #666;">
										Категории не найдены. Проверьте, что категории wedding, evening, prom, wedding-sale существуют в таксономии dress_category.
									</td>
								</tr>
							<?php else: ?>
								<?php foreach ( $categories as $category ): ?>
									<tr>
										<td><?php echo esc_html( is_object( $category ) ? $category->name : 'Ошибка объекта' ); ?></td>
										<td><code><?php echo esc_html( is_object( $category ) ? $category->slug : 'Ошибка объекта' ); ?></code></td>
										<td><?php echo intval( is_object( $category ) ? $category->count : 0 ); ?></td>
										<td>
											<button type="button" class="button button-small generate-single" 
													data-category="<?php echo esc_attr( is_object( $category ) ? $category->slug : '' ); ?>">
												Сгенерировать
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				
				<!-- Log -->
				<div class="xml-feed-log">
					<h2>Лог генерации</h2>
					<div id="generation-log" class="log-container">
						<p>Нажмите "Сгенерировать все фиды сейчас" чтобы увидеть лог генерации.</p>
					</div>
				</div>
				
			</div>
		</div>
		<?php
	}
	
	/**
	 * Handle manual generation AJAX
	 */
	public function handle_manual_generation() {
		// Check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'xml_feed_manual' ) ) {
			wp_send_json_error( 'Ошибка проверки безопасности' );
		}
		
		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Недостаточно прав доступа' );
		}
		
		// Increase memory limit for this operation
		ini_set( 'memory_limit', '512M' );
		
		try {
			// Run generation
			$cron = new WP_Cron_XML_Feed();
			$cron->generate_feeds();
			
			// Get updated status
			$last_run = get_option( 'xml_feed_last_run', array() );
			
			wp_send_json_success( array(
				'message' => 'XML фиды успешно сгенерированы',
				'last_run' => $last_run
			) );
		} catch ( Exception $e ) {
			wp_send_json_error( 'Ошибка: ' . $e->getMessage() );
		}
	}
	
	/**
	 * Handle batch generation AJAX
	 */
	public function handle_batch_generation() {
		// Check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'xml_feed_batch' ) ) {
			wp_send_json_error( 'Ошибка проверки безопасности' );
		}
		
		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Недостаточно прав доступа' );
		}
		
		// Increase memory limit for this operation
		ini_set( 'memory_limit', '512M' );
		
		try {
			// Load batch generator
			require_once __DIR__ . '/class-xml-feed-batch.php';
			$batch_generator = new XML_Feed_Batch_Generator();
			
			// Generate feeds one by one with batch processing
			$feeds_to_generate = array(
				// Full feeds
				array( 'category' => 'wedding', 'limit' => null ),
				array( 'category' => 'evening', 'limit' => null ),
				array( 'category' => 'prom', 'limit' => null ),
				array( 'category' => 'wedding-sale', 'limit' => null ),
				// Limited feeds
				array( 'category' => 'wedding', 'limit' => 360 ),
				array( 'category' => 'evening', 'limit' => 72 ),
				array( 'category' => 'prom', 'limit' => 96 )
			);
			
			$success_count = 0;
			$error_count = 0;
			$errors = array();
			
			foreach ( $feeds_to_generate as $feed_config ) {
				$category = $feed_config['category'];
				$limit = $feed_config['limit'];
				$feed_name = $category . ( $limit ? "-{$limit}" : "" );
				
				$result = $batch_generator->generate_feed_batch( $category, $limit );
				
				if ( is_wp_error( $result ) ) {
					$error_message = $result->get_error_message();
					$errors[] = "{$feed_name}: {$error_message}";
					$error_count++;
				} else {
					$success_count++;
				}
			}
			
			// Generate combined feed using regular method
			$generator = XML_Feed_Generator::get_instance();
			$combined_result = $generator->generate_combined_feed();
			
			if ( is_wp_error( $combined_result ) ) {
				$error_message = $combined_result->get_error_message();
				$errors[] = "combined: {$error_message}";
				$error_count++;
			} else {
				$success_count++;
			}
			
			// Store results
			update_option( 'xml_feed_last_run', array(
				'timestamp' => current_time( 'mysql' ),
				'success_count' => $success_count,
				'error_count' => $error_count,
				'errors' => $errors,
				'status' => $error_count > 0 ? 'partial' : 'success'
			) );
			
			wp_send_json_success( array(
				'message' => 'XML фиды успешно сгенерированы с использованием батчевой обработки',
				'success_count' => $success_count,
				'error_count' => $error_count
			) );
			
		} catch ( Exception $e ) {
			wp_send_json_error( 'Ошибка: ' . $e->getMessage() );
		}
	}
	
	/**
	 * Handle cron status AJAX
	 */
	public function handle_cron_status() {
		// Check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'xml_feed_status' ) ) {
			wp_send_json_error( 'Ошибка проверки безопасности' );
		}
		
		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Недостаточно прав доступа' );
		}
		
		$status = $this->get_cron_status();
		wp_send_json_success( $status );
	}
	
	/**
	 * Handle reschedule AJAX
	 */
	public function handle_reschedule() {
		// Check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'xml_feed_reschedule' ) ) {
			wp_send_json_error( 'Ошибка проверки безопасности' );
		}
		
		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Недостаточно прав доступа' );
		}
		
		$interval = sanitize_text_field( $_POST['interval'] );
		
		// Validate interval
		$valid_intervals = array( 
			'xml_feed_5min', 
			'xml_feed_15min', 
			'xml_feed_30min', 
			'xml_feed_hourly', 
			'xml_feed_interval', 
			'hourly', 
			'twicedaily', 
			'daily' 
		);
		if ( ! in_array( $interval, $valid_intervals ) ) {
			wp_send_json_error( 'Выбран недопустимый интервал' );
		}
		
		try {
			// Save interval setting
			update_option( 'xml_feed_cron_interval', $interval );
			
			// Reschedule cron with new interval
			$cron = new WP_Cron_XML_Feed();
			$cron->reschedule_cron( $interval );
			
			wp_send_json_success( array(
				'message' => 'Настройки Cron успешно сохранены и перепланированы'
			) );
		} catch ( Exception $e ) {
			wp_send_json_error( 'Ошибка: ' . $e->getMessage() );
		}
	}
	
	/**
	 * Get cron status
	 */
	private function get_cron_status() {
		$next_run = wp_next_scheduled( 'xml_feed_generation' );
		$last_run = get_option( 'xml_feed_last_run', array() );
		$current_interval = get_option( 'xml_feed_cron_interval', 'xml_feed_interval' );
		
		// Get interval display name
		$interval_names = array(
			'xml_feed_5min' => 'Каждые 5 минут',
			'xml_feed_15min' => 'Каждые 15 минут',
			'xml_feed_30min' => 'Каждые 30 минут',
			'xml_feed_hourly' => 'Каждый час',
			'hourly' => 'Каждый час (WordPress по умолчанию)',
			'xml_feed_interval' => 'Каждые 6 часов',
			'twicedaily' => 'Дважды в день',
			'daily' => 'Один раз в день'
		);
		
		return array(
			'is_scheduled' => $next_run !== false,
			'next_run' => $next_run ? date( 'Y-m-d H:i:s', $next_run ) : null,
			'next_run_relative' => $next_run ? human_time_diff( time(), $next_run ) : null,
			'last_run' => $last_run,
			'current_interval' => $current_interval,
			'current_interval_name' => isset( $interval_names[ $current_interval ] ) ? $interval_names[ $current_interval ] : $current_interval,
			'cron_disabled' => defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON
		);
	}
	
	/**
	 * Get admin JavaScript
	 */
	private function get_admin_js() {
		return "
		jQuery(document).ready(function($) {
			// Manual generation
			$('#manual-generation').on('click', function() {
				var button = $(this);
				button.prop('disabled', true).text('Генерирую...');
				$('#generation-log').html('<p>Начинаю генерацию...</p>');
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'xml_feed_manual_generation',
						nonce: '" . wp_create_nonce( 'xml_feed_manual' ) . "'
					},
					success: function(response) {
						if (response.success) {
							$('#generation-log').html('<p class=\"success\">' + response.data.message + '</p>');
							setTimeout(function() {
								location.reload();
							}, 2000);
						} else {
							$('#generation-log').html('<p class=\"error\">Ошибка: ' + response.data + '</p>');
						}
					},
					error: function(xhr, status, error) {
						$('#generation-log').html('<p class=\"error\">AJAX Ошибка: ' + error + '</p>');
					},
					complete: function() {
						button.prop('disabled', false).text('Сгенерировать все фиды сейчас');
					}
				});
			});
			
			// Batch generation
			$('#batch-generation').on('click', function() {
				var button = $(this);
				button.prop('disabled', true).text('Генерирую с батчевой обработкой...');
				$('#generation-log').html('<p>Начинаю батчевую генерацию...</p>');
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'xml_feed_batch_generation',
						nonce: '" . wp_create_nonce( 'xml_feed_batch' ) . "'
					},
					success: function(response) {
						if (response.success) {
							$('#generation-log').html('<p class=\"success\">' + response.data.message + '</p>');
							setTimeout(function() {
								location.reload();
							}, 2000);
						} else {
							$('#generation-log').html('<p class=\"error\">Ошибка: ' + response.data + '</p>');
						}
					},
					error: function(xhr, status, error) {
						$('#generation-log').html('<p class=\"error\">AJAX Ошибка: ' + error + '</p>');
					},
					complete: function() {
						button.prop('disabled', false).text('Сгенерировать с батчевой обработкой');
					}
				});
			});
			
			// Refresh status
			$('#refresh-status').on('click', function() {
				location.reload();
			});
			
			// Cron settings form
			$('#cron-settings-form').on('submit', function(e) {
				e.preventDefault();
				var interval = $('#cron-interval').val();
				var button = $(this).find('button[type=\"submit\"]');
				
				button.prop('disabled', true).text('Сохраняю...');
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'xml_feed_reschedule',
						nonce: '" . wp_create_nonce( 'xml_feed_reschedule' ) . "',
						interval: interval
					},
					success: function(response) {
						if (response.success) {
							alert('Настройки Cron успешно сохранены!');
							location.reload();
						} else {
							alert('Ошибка: ' + response.data);
						}
					},
					error: function(xhr, status, error) {
						alert('AJAX Ошибка: ' + error);
					},
					complete: function() {
						button.prop('disabled', false).text('Сохранить настройки');
					}
				});
			});
			
			// Reschedule cron (legacy button)
			$('#reschedule-cron').on('click', function() {
				var interval = $('#cron-interval').val();
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'xml_feed_reschedule',
						nonce: '" . wp_create_nonce( 'xml_feed_reschedule' ) . "',
						interval: interval
					},
					success: function(response) {
						if (response.success) {
							alert('Cron успешно перепланирован');
							location.reload();
						} else {
							alert('Ошибка: ' + response.data);
						}
					},
					error: function(xhr, status, error) {
						alert('AJAX Ошибка: ' + error);
					}
				});
			});
			
			// Generate single category
			$('.generate-single').on('click', function() {
				var button = $(this);
				var category = button.data('category');
				
				button.prop('disabled', true).text('Генерирую...');
				
				// This would need a separate AJAX handler for single category generation
				alert('Генерация отдельных категорий пока не реализована');
				button.prop('disabled', false).text('Сгенерировать');
			});
			
			// Function to update time display
			function updateTimeDisplay() {
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'xml_feed_cron_status',
						nonce: '" . wp_create_nonce( 'xml_feed_status' ) . "'
					},
					success: function(response) {
						if (response.success && response.data.next_run_relative) {
							$('.status-info p:contains(\"Следующий запуск\")').html('Следующий запуск: через ' + response.data.next_run_relative);
						}
					}
				});
			}
			
			// Update time display every minute
			setInterval(updateTimeDisplay, 60000);
		});
		";
	}
	
	/**
	 * Get admin CSS
	 */
	private function get_admin_css() {
		return "
		.xml-feed-dashboard {
			margin-top: 20px;
		}
		
		.xml-feed-cards {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			gap: 20px;
			margin-bottom: 30px;
		}
		
		.xml-feed-card {
			background: #fff;
			border: 1px solid #ccd0d4;
			border-radius: 4px;
			padding: 20px;
		}
		
		.xml-feed-card h3 {
			margin-top: 0;
			margin-bottom: 15px;
		}
		
		.status-badge {
			display: inline-block;
			padding: 4px 8px;
			border-radius: 3px;
			font-size: 12px;
			font-weight: bold;
			text-transform: uppercase;
		}
		
		.status-badge.success {
			background: #d4edda;
			color: #155724;
		}
		
		.status-badge.error {
			background: #f8d7da;
			color: #721c24;
		}
		
		.status-badge.warning {
			background: #fff3cd;
			color: #856404;
		}
		
		.xml-feed-actions {
			background: #fff;
			border: 1px solid #ccd0d4;
			border-radius: 4px;
			padding: 20px;
			margin-bottom: 30px;
		}
		
		.action-buttons {
			margin-bottom: 20px;
		}
		
		.action-buttons .button {
			margin-right: 10px;
		}
		
		.cron-settings {
			border-top: 1px solid #eee;
			padding-top: 20px;
		}
		
		.cron-settings .notice {
			margin: 10px 0;
			padding: 10px;
			border-left: 4px solid #ffb900;
			background: #fff8e5;
		}
		
		.cron-settings label {
			display: inline-block;
			width: 100px;
			font-weight: bold;
		}
		
		.xml-feed-categories,
		.xml-feed-log {
			background: #fff;
			border: 1px solid #ccd0d4;
			border-radius: 4px;
			padding: 20px;
			margin-bottom: 30px;
		}
		
		.log-container {
			background: #f1f1f1;
			border: 1px solid #ddd;
			padding: 15px;
			min-height: 100px;
			font-family: monospace;
		}
		
		.log-container .success {
			color: #155724;
		}
		
		.log-container .error {
			color: #721c24;
		}
		";
	}
}

// Initialize admin interface
new XML_Feed_Admin();
