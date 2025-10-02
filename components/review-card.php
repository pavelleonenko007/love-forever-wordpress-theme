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
$image_carousel = array();

if ( ! empty( get_field( 'image_carousel' ) ) ) {
	$image_carousel = array_map( fn( $item ) => $item['image'], array_filter( get_field( 'image_carousel' ) ) );
}

if ( ! empty( $image_carousel ) && has_post_thumbnail() ) {
	array_unshift( $image_carousel, get_post_thumbnail_id() );
}

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
							<?php echo wp_get_attachment_image( $image_carousel_item, 'fullhd', false, array( 'class' => 'img-cover' ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
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
