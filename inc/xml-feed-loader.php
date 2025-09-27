<?php
/**
 * XML Feed Loader
 *
 * Loads all XML feed related classes and functionality
 *
 * @package LoveForever
 */

defined( 'ABSPATH' ) || exit;

// Load XML Feed Generator class
require_once __DIR__ . '/class-xml-feed-generator.php';

// Load WP-Cron system
require_once __DIR__ . '/class-wp-cron-xml-feed.php';

// Load Admin interface
if ( is_admin() ) {
	require_once __DIR__ . '/class-xml-feed-admin.php';
}

// Load WP-CLI commands only if WP-CLI is available
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/class-wp-cli-xml-feed.php';
}

/**
 * Initialize XML Feed functionality
 */
function loveforever_init_xml_feed() {
	// Register activation hook to create necessary directories
	register_activation_hook( __FILE__, 'loveforever_xml_feed_activation' );
	
	// Add admin menu for XML feed management
	add_action( 'admin_menu', 'loveforever_add_xml_feed_admin_menu' );
	
	// Add AJAX handlers for admin interface
	add_action( 'wp_ajax_generate_xml_feed', 'loveforever_ajax_generate_xml_feed' );
	add_action( 'wp_ajax_clean_xml_feeds', 'loveforever_ajax_clean_xml_feeds' );
}

/**
 * Activation hook - create necessary directories
 */
function loveforever_xml_feed_activation() {
	$upload_dir = wp_upload_dir();
	$xml_dir = $upload_dir['basedir'] . '/xml/';
	$public_xml_dir = ABSPATH . 'xml/';
	
	wp_mkdir_p( $xml_dir );
	wp_mkdir_p( $public_xml_dir );
}

/**
 * Add admin menu for XML feed management
 */
function loveforever_add_xml_feed_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=dress',
		'XML Feeds',
		'XML Feeds',
		'manage_options',
		'xml-feeds',
		'loveforever_xml_feed_admin_page'
	);
}

/**
 * Admin page for XML feed management
 */
function loveforever_xml_feed_admin_page() {
	$generator = XML_Feed_Generator::get_instance();
	$categories = $generator->get_available_categories();
	
	// Get status of existing files
	$upload_dir = wp_upload_dir();
	$upload_xml_dir = $upload_dir['basedir'] . '/xml/';
	$public_xml_dir = ABSPATH . 'xml/';
	
	$upload_files = is_dir( $upload_xml_dir ) ? glob( $upload_xml_dir . '*.xml' ) : array();
	$public_files = is_dir( $public_xml_dir ) ? glob( $public_xml_dir . '*.xml' ) : array();
	
	?>
	<div class="wrap">
		<h1>XML Feed Management</h1>
		
		<div class="card">
			<h2>Allowed Categories for Feed Generation</h2>
			<?php if ( empty( $categories ) ) : ?>
				<p>No allowed categories found.</p>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th>Category</th>
							<th>Slug</th>
							<th>Products</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $categories as $category ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $category['name'] ); ?></strong></td>
								<td><code><?php echo esc_html( $category['slug'] ); ?></code></td>
								<td><?php echo intval( $category['count'] ); ?></td>
								<td>
									<button type="button" class="button button-primary generate-feed-btn" 
											data-category="<?php echo esc_attr( $category['slug'] ); ?>">
										Generate Feed
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		
		<div class="card">
			<h2>Bulk Actions</h2>
			<p>
				<button type="button" class="button button-secondary" id="generate-all-feeds">
					Generate All Allowed Feeds
				</button>
				<button type="button" class="button button-secondary" id="clean-all-feeds">
					Clean All Feeds
				</button>
			</p>
			<p><em>Note: Only categories "wedding", "evening", "prom", and "wedding-sale" are allowed for feed generation.</em></p>
		</div>
		
		<div class="card">
			<h2>Generated Files</h2>
			<div class="xml-files-status">
				<h3>Upload Directory (<?php echo esc_html( $upload_xml_dir ); ?>)</h3>
				<?php if ( empty( $upload_files ) ) : ?>
					<p>No files found.</p>
				<?php else : ?>
					<ul>
						<?php foreach ( $upload_files as $file ) : ?>
							<li>
								<strong><?php echo esc_html( basename( $file ) ); ?></strong>
								(<?php echo size_format( filesize( $file ) ); ?>, 
								modified: <?php echo date( 'Y-m-d H:i:s', filemtime( $file ) ); ?>)
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				
				<h3>Public Directory (<?php echo esc_html( $public_xml_dir ); ?>)</h3>
				<?php if ( empty( $public_files ) ) : ?>
					<p>No files found.</p>
				<?php else : ?>
					<ul>
						<?php foreach ( $public_files as $file ) : ?>
							<li>
								<strong><?php echo esc_html( basename( $file ) ); ?></strong>
								(<?php echo size_format( filesize( $file ) ); ?>, 
								modified: <?php echo date( 'Y-m-d H:i:s', filemtime( $file ) ); ?>)
								<a href="<?php echo esc_url( home_url( '/xml/' . basename( $file ) ) ); ?>" target="_blank">View</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="card">
			<h2>WP-CLI Commands</h2>
			<p>You can also manage XML feeds via WP-CLI:</p>
			<pre><code># Generate feed for specific allowed category
wp xml-feed generate wedding

# Generate feeds for all allowed categories
wp xml-feed generate-all

# List allowed categories
wp xml-feed list-categories

# Clean generated files
wp xml-feed clean

# Show status
wp xml-feed status</code></pre>
		</div>
		
		<div class="card">
			<h2>Cron Setup</h2>
			<p>To set up automatic generation via system cron, add this to your crontab:</p>
			<pre><code># Generate all allowed feeds daily at 2 AM
0 2 * * * php <?php echo esc_html( __DIR__ . '/xml-feed-cron.php' ); ?> --all

# Generate specific allowed category feed every 6 hours
0 */6 * * * php <?php echo esc_html( __DIR__ . '/xml-feed-cron.php' ); ?> wedding</code></pre>
			<p><em>Note: Only categories "wedding", "evening", "prom", and "wedding-sale" are allowed for feed generation.</em></p>
		</div>
	</div>
	
	<script>
	jQuery(document).ready(function($) {
		$('.generate-feed-btn').on('click', function() {
			var category = $(this).data('category');
			var button = $(this);
			
			button.prop('disabled', true).text('Generating...');
			
			$.post(ajaxurl, {
				action: 'generate_xml_feed',
				category: category,
				nonce: '<?php echo wp_create_nonce( 'xml_feed_nonce' ); ?>'
			}, function(response) {
				if (response.success) {
					alert('Feed generated successfully!');
					location.reload();
				} else {
					alert('Error: ' + response.data);
				}
			}).always(function() {
				button.prop('disabled', false).text('Generate Feed');
			});
		});
		
		$('#generate-all-feeds').on('click', function() {
			if (!confirm('Generate feeds for all categories?')) return;
			
			var button = $(this);
			button.prop('disabled', true).text('Generating...');
			
			$.post(ajaxurl, {
				action: 'generate_xml_feed',
				category: 'all',
				nonce: '<?php echo wp_create_nonce( 'xml_feed_nonce' ); ?>'
			}, function(response) {
				if (response.success) {
					alert('All feeds generated successfully!');
					location.reload();
				} else {
					alert('Error: ' + response.data);
				}
			}).always(function() {
				button.prop('disabled', false).text('Generate All Feeds');
			});
		});
		
		$('#clean-all-feeds').on('click', function() {
			if (!confirm('Delete all generated XML files?')) return;
			
			var button = $(this);
			button.prop('disabled', true).text('Cleaning...');
			
			$.post(ajaxurl, {
				action: 'clean_xml_feeds',
				nonce: '<?php echo wp_create_nonce( 'xml_feed_nonce' ); ?>'
			}, function(response) {
				if (response.success) {
					alert('All feeds cleaned successfully!');
					location.reload();
				} else {
					alert('Error: ' + response.data);
				}
			}).always(function() {
				button.prop('disabled', false).text('Clean All Feeds');
			});
		});
	});
	</script>
	<?php
}

/**
 * AJAX handler for generating XML feed
 */
function loveforever_ajax_generate_xml_feed() {
	check_ajax_referer( 'xml_feed_nonce', 'nonce' );
	
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Insufficient permissions' );
	}
	
	$category = sanitize_text_field( $_POST['category'] ?? '' );
	
	if ( empty( $category ) ) {
		wp_send_json_error( 'Category is required' );
	}
	
	$generator = XML_Feed_Generator::get_instance();
	
	if ( $category === 'all' ) {
		$results = $generator->generate_all_feeds();
		$success_count = 0;
		$error_count = 0;
		
		foreach ( $results as $cat_slug => $result ) {
			if ( is_wp_error( $result ) ) {
				$error_count++;
			} else {
				$success_count++;
			}
		}
		
		wp_send_json_success( sprintf( 'Generated %d feeds successfully, %d errors', $success_count, $error_count ) );
	} else {
		$result = $generator->generate_feed( $category );
		
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}
		
		wp_send_json_success( 'Feed generated successfully' );
	}
}

/**
 * AJAX handler for cleaning XML feeds
 */
function loveforever_ajax_clean_xml_feeds() {
	check_ajax_referer( 'xml_feed_nonce', 'nonce' );
	
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Insufficient permissions' );
	}
	
	$upload_dir = wp_upload_dir();
	$upload_xml_dir = $upload_dir['basedir'] . '/xml/';
	$public_xml_dir = ABSPATH . 'xml/';
	
	$deleted_count = 0;
	
	// Delete upload directory files
	if ( is_dir( $upload_xml_dir ) ) {
		$upload_files = glob( $upload_xml_dir . '*.xml' );
		foreach ( $upload_files as $file ) {
			if ( unlink( $file ) ) {
				$deleted_count++;
			}
		}
	}
	
	// Delete public directory files
	if ( is_dir( $public_xml_dir ) ) {
		$public_files = glob( $public_xml_dir . '*.xml' );
		foreach ( $public_files as $file ) {
			if ( unlink( $file ) ) {
				$deleted_count++;
			}
		}
	}
	
	wp_send_json_success( sprintf( 'Deleted %d files', $deleted_count ) );
}

// Initialize XML Feed functionality
loveforever_init_xml_feed();
