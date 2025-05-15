<?php
/**
 * Story
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;
$index = $args['index'] ?? 0;
?>
<a href="#" data-js-story-item="<?php echo esc_attr( $index ); ?>" data-js-href="<?php echo esc_url( '/?story=' . get_the_ID() ); ?>" data-js-dialog-open-button="storiesDialog" class="btn fast-link-btn lf-story-circle w-inline-block">
	<div class="fast-link-omg-mom lf-story-circle__body">
		<?php if ( has_post_thumbnail() ) : ?>
			<img src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" loading="lazy" data-wp="post_image" alt class="img-60w lf-story-circle__body">
		<?php endif; ?>
	</div>
	<div><?php the_title(); ?></div>
</a>
