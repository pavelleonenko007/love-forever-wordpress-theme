<?php
/**
 * Review Card
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;

$author         = get_field( 'author' );
$rating         = min( intval( get_field( 'rating' ) ), 5 );
$review_text    = get_field( 'review_text' );
$image_carousel = ! empty( get_field( 'image_carousel' ) ) ? array_filter( get_field( 'image_carousel' ) ) : array();

$card_classes = array(
	'lf-rewiew-card',
	'oyziv-item',
);

if ( empty( $image_carousel ) && ! has_post_thumbnail() ) {
	$card_classes[] = 'no-image';
}

?>
<div class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>">
	<?php if ( ! empty( $image_carousel ) ) : ?>
		<div class="lf-review-splide splide" data-js-review-splide>
			<div class="lf-review-splide__track splide__track">
				<div class="lf-review-splide__list splide__list">
					<?php foreach ( $image_carousel as $image_carousel_item ) : ?>
						<div class="lf-review-splide__slide splide__slide">
							<?php echo wp_get_attachment_image( $image_carousel_item['image'], 'fullhd', false, array( 'class' => 'img-cover' ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<!-- <div data-delay="4000" data-animation="slide" class="slider-oyziv w-slider" data-autoplay="false" data-easing="ease" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-nav-spacing="3" data-duration="500" data-infinite="true">
			<div class="slider-oyziv_mask w-slider-mask">
				<?php
				if ( has_post_thumbnail() ) :
					?>
					<div class="w-slide">
						<div class="slider-oyziv_img-mom">
							<?php the_post_thumbnail( 'fullhd', array( 'class' => 'img-cover' ) ); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php
				foreach ( $image_carousel as $image_carousel_item ) :
					?>
					<div class="w-slide">
						<div class="slider-oyziv_img-mom">
							<?php echo wp_get_attachment_image( $image_carousel_item['image'], 'fullhd', false, array( 'class' => 'img-cover' ) ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="slider-oyziv_l w-slider-arrow-left">
				<div class="pag-svg w-embed">
					<svg width="6" height="10" viewbox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M0.750232 4.28598L5.25007 0L6 0.714289L1.50016 5.00027L5.99944 9.28571L5.24951 10L0 4.99998L0.74993 4.28569L0.750232 4.28598Z" fill="white"></path>
					</svg>
				</div>
			</div>
			<div class="slider-oyziv_r w-slider-arrow-right">
				<div class="pag-svg w-embed">
					<svg width="6" height="10" viewbox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M5.24977 4.28598L0.74993 0L0 0.714289L4.49984 5.00027L0.000560648 9.28571L0.750491 10L6 4.99998L5.25007 4.28569L5.24977 4.28598Z" fill="white"></path>
					</svg>
				</div>
			</div>
			<div class="slider-oyziv_nav w-slider-nav w-round w-num"></div>
		</div> -->
	<?php elseif ( has_post_thumbnail() ) : ?>
		<img src="<?php echo esc_url( get_the_post_thumbnail_url( $post, 'fullhd' ) ); ?>" loading="lazy" alt class="img-fw">
	<?php endif; ?>
	<div class="vert">
		<div class="<?php echo has_post_thumbnail() ? 'otziv-horiz' : 'otziv-horiz no-image'; ?>">
			<?php if ( ! empty( $author ) ) : ?>
				<div class="p-12-12 uper"><?php echo esc_html( $author ); ?></div>
			<?php endif; ?>
			<div class="<?php echo has_post_thumbnail() ? '_2px_romb purp' : '_2px_romb purp _2'; ?>"></div>
			<div class="lf-review-card__rating">
				<?php echo loveforever_get_rating_html( $rating ); ?>
			</div>
		</div>
		<p class="p-16-20 italic"><?php echo wp_kses_post( $review_text ); ?></p>
	</div>
</div>
