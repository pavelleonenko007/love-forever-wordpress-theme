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
		<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'img-60w lf-story-circle__body' ) ); ?>
	</div>
	<div><?php the_title(); ?></div>
</a>
