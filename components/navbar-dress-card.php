<?php
/**
 * Navbar Dress Card component
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;

$price = get_field( 'price' );
?>
<a href="<?php the_permalink(); ?>" class="choosed-item w-inline-block">
	<div class="mom-abs">
		<?php if ( has_post_thumbnail() ) : ?>
			<img 
				src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" 
				loading="eager" 
				alt 
				class="img-cover"
			>
		<?php endif; ?>
	</div>
	<div class="choosed-item_bottom">
		<div><?php the_title(); ?></div>
		<?php
		if ( ! empty( $price ) ) :
			?>
			<div class="text-block"><?php echo esc_html( loveforever_format_price( $price, 0 ) . ' ₽' ); ?></div>
		<?php endif; ?>
	</div>
</a>
