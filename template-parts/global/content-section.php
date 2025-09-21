<?php
/**
 * Content Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$content      = isset( $args['content'] ) ? $args['content'] : '';
$display_type = isset( $args['display_type'] ) ? $args['display_type'] : 'standard';

if ( ! empty( $content ) ) :
	$content = 'wrapped' === $display_type ? loveforever_convert_content_to_accordion( $content ) : $content;
	?>
<section class="section">
	<div class="container">
		<div class="lf-content flow">
			<?php echo $content; ?>
		</div>
	</div>
</section>
<?php endif; ?>
