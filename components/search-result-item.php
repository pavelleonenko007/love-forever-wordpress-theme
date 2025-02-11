<?php
/**
 * Search Result Item component
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;

$price = get_field( 'price' );
?>
<div class="search-ajaxed_item">
	<a href="<?php the_permalink(); ?>" class="search-ajaxed_item_a w-inline-block">
		<div><?php the_title(); ?></div>
	</a>
	<div class="search-ajaxed_item_0px">
		<div class="div-block-8">
			<div class="mom-abs">
				<?php if ( has_post_thumbnail() ) : ?>
					<img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>" loading="lazy" alt class="img-cover">
				<?php endif; ?>
			</div>
		</div>
	</div>
	<a href="<?php the_permalink(); ?>" class="search-ajaxed_item_a w-inline-block">
		<div class="itelic"><?php echo esc_html( loveforever_format_price( $price, 0 ) . ' ₽' ); ?></div>
	</a>
</div>
