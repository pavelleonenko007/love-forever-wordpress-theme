<?php
/**
 * About us bullet
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $args['title'] ) && ( ! empty( $args['icon'] ) || ! empty( $args['description'] ) ) ) :
	?>
<div id="w-node-_7a1f9b32-ca32-356a-7188-ae3258fc571a-141460db" class="div-block-4">
	<?php if ( ! empty( $args['icon'] ) ) : ?>
		<img 
			src="<?php echo esc_url( wp_get_attachment_image_url( $args['icon'] ) ); ?>" 
			loading="lazy" 
			alt=""
			class="image-2"
		>
	<?php endif; ?>
	<div class="p-36-36 about-p lovercase">
		<?php echo wp_kses_post( $args['title'] ); ?>
	</div>
	<?php if ( ! empty( $args['description'] ) ) : ?>
		<p class="p-16-20 abput-p-small"><?php echo wp_kses_post( $args['description'] ); ?></p>
	<?php endif; ?>
</div>
<?php endif; ?>
