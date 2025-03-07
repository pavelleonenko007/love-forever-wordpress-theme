<?php
/**
 * Dress Card
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;
$price                      = get_field( 'price' );
$has_discount               = get_field( 'has_discount' );
$price_with_discount        = get_field( 'price_with_discount' );
$images                     = get_field( 'images' );
$video                      = get_field( 'video' );
$show_video_in_product_card = get_field( 'show_video_in_product_card' );
?>

<div id="w-node-_6e88719d-fe8f-116f-4337-b580b5a0b461-b5a0b461" class="prod-item">
	<div class="prod-item_top">
		<a href="<?php the_permalink(); ?>" class="link w-inline-block">
			<div class="prod-item_img-mom">
				<div class="mom-abs">
					<?php if ( $show_video_in_product_card && ! empty( $video ) ) : ?>
						<video 
							class="card-video"
							loop
							muted
							playsinline 
							data-js-play-if-visible-video
						>
							<source src="<?php echo esc_url( $video['url'] ); ?>" type="<?php echo esc_attr( $video['mime_type'] ); ?>">
						</video>
					<?php elseif ( ! empty( $images ) ) : ?>
						<div class="card-slider" data-js-card-slider>
							<ul class="card-slider__list">
								<?php foreach ( $images as $index => $image ) : ?>
									<li class="card-slider__list-item<?php echo ( 0 === $index ) ? ' is-active' : ''; ?>" data-js-card-slider-slide-item="<?php echo esc_attr( $index ); ?>">
										<img src="<?php echo esc_url( wp_get_attachment_image_url( $image['image']['ID'], 'large' ) ); ?>" loading="lazy" alt class="img-cover">
									</li>
								<?php endforeach; ?>
							</ul>
							<ul class="card-slider__nav">
								<?php
								$count_images = count( $images );
								for ( $i = 0; $i < $count_images; $i++ ) :
									?>
									<li class="card-slider__nav-item<?php echo ( 0 === $i ) ? ' is-active' : ''; ?>" data-js-card-slider-nav-item="<?php echo esc_attr( $i ); ?>"></li>
								<?php endfor; ?>
							</ul>
						</div>
					<?php elseif ( has_post_thumbnail() ) : ?>
						<img src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" loading="lazy" alt class="img-cover">
					<?php endif; ?>
				</div>
			</div>
		</a>
		<?php $is_in_favorites = loveforever_has_product_in_favorites( get_the_ID() ); ?>
		<button type="button" class="btn-like like-button w-inline-block <?php echo $is_in_favorites ? 'is-active' : ''; ?>" data-js-add-to-favorite-button="<?php the_ID(); ?>">
			<div class="svg w-embed">
				<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
					<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
					<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
					<path class="like-button__heart" d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
				</svg>
			</div>
		</button>
		<?php if ( $has_discount ) : ?>
			<div class="badge">
				<div class="bagde__text">Скидка</div>
			</div>
		<?php endif; ?>
	</div>
	<a href="<?php the_permalink(); ?>" class="prod-item_bottom w-inline-block">
		<div data-wp="post_title" class="p-12-12 uper m-12-12"><?php the_title(); ?></div>
		<?php
		if ( ! empty( $price ) ) :
			?>
			<div class="horiz indirim-horiz">
				<?php $first_price = loveforever_format_price( ! empty( $price_with_discount ) ? $price_with_discount : $price, 0 ); ?>
				<div class="p-12-12 italic letter-5"><span><?php echo esc_html( $first_price ); ?></span> ₽</div>
				<?php if ( ! empty( $price_with_discount ) ) : ?>
					<div class="p-12-12 italic letter-5 oldprice"><span><?php echo esc_html( loveforever_format_price( $price ) ); ?></span> ₽</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</a>
</div>
