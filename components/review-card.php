<?php
/**
 * Review Card
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;

$name        = get_field( 'name' );
$review_text = get_field( 'review_text' );
?>
<div class="<?php echo has_post_thumbnail() ? 'oyziv-item' : 'oyziv-item no-image'; ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<img src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" loading="lazy" alt class="img-fw">
	<?php endif; ?>
	<div class="vert">
		<div class="<?php echo has_post_thumbnail() ? 'otziv-horiz' : 'otziv-horiz no-image'; ?>">
			<?php if ( ! empty( $name ) ) : ?>
				<div class="p-12-12 uper"><?php echo esc_html( $name ); ?></div>
				<div class="<?php echo has_post_thumbnail() ? '_2px_romb purp' : '_2px_romb purp _2'; ?>"></div>
			<?php endif; ?>
			<div class="p-12-12 uper"><?php echo esc_html( get_the_date( 'd.m.y' ) ); ?></div>
		</div>
		<p class="p-16-20 italic"><?php echo esc_html( $review_text ); ?></p>
	</div>
</div>
