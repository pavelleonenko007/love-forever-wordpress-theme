<?php
/**
 * Content Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$content = isset( $args['content'] ) ? $args['content'] : '';

if ( ! empty( $content ) ) :
	?>
<section class="section">
	<div class="container">
		<div class="lf-content flow">
			<?php echo wp_kses_post( $content ); ?>
		</div>
	</div>
</section>
<?php endif; ?>
