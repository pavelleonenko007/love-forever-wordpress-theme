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
$images                     = loveforever_get_product_images( get_the_ID() );
$video                      = get_field( 'video' );
$show_video_in_product_card = get_field( 'show_video_in_product_card' );
$badge_text                 = loveforever_get_product_badge_text( get_the_ID() );

$size          = ! empty( $args['size'] ) ? $args['size'] : 'large';
$show_carousel = isset( $args['show_carousel'] ) ? (bool) $args['show_carousel'] : true;
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
							<source src="<?php echo esc_url( $video['url'] ); ?>" type="<?php echo esc_attr( loveforever_get_video_mime_type( $video ) ); ?>">
						</video>
					<?php elseif ( ! empty( $images ) && $show_carousel ) : ?>
						<?php
						$slider_config = array(
							'type'         => 'loop',
							'perPage'      => 1,
							'perMove'      => 1,
							'speed'        => 0,
							'arrows'       => false,
							'lazyLoad'     => 'nearby',
							'preloadPages' => 1,
							'classes'      => array(
								'pagination' => 'card-slider__pagination splide__pagination your-class-pagination',
								'page'       => 'card-slider__page splide__pagination__page',
							),
							'breakpoints'  => array(
								991 => array(
									'speed'             => 600,
									'rewindSpeed'       => 600,
									'waitForTransition' => true,
									'easing'            => 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
									'flickPower'        => 400,
									'flickMaxPages'     => 1,
									'snap'              => true,
									'slideFocus'        => false,
								),
							),
						);
						?>
						<div class="card-slider splide" aria-label="<?php echo esc_attr( 'Карусель изображений платья ' . get_the_title() ); ?>" data-splide="<?php echo esc_attr( wp_json_encode( $slider_config ) ); ?>" data-js-card-splide="">
							<div class="card-slider__track splide__track">
								<ul class="card-slider__list splide__list">
									<?php foreach ( $images as $index => $image ) : ?>
										<li class="card-slider__list-item splide__slide">
											<?php
											echo wp_get_attachment_image(
												$image['image']['ID'],
												'fullhd',
												false,
												array(
													'loading' => 'lazy',
													'class'   => 'img-cover',
												)
											);
											?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					<?php elseif ( has_post_thumbnail() ) : ?>
						<?php
						the_post_thumbnail(
							'fullhd',
							array(
								'loading' => 'lazy',
								'class'   => 'img-cover',
							)
						);
						?>
					<?php endif; ?>
				</div>
			</div>
		</a>
		<?php $is_in_favorites = loveforever_has_product_in_favorites( get_the_ID() ); ?>
		<button type="button" class="btn-like lf-like-button w-inline-block <?php echo $is_in_favorites ? 'is-active' : ''; ?>" data-js-add-to-favorite-button="<?php the_ID(); ?>">
			<svg class="lf-like-button__icon" viewbox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
				<use href="#heartIcon"></use>
			</svg>
		</button>
		<?php if ( $badge_text ) : ?>
			<div class="badge lf-badge">
				<div class="bagde__text lf-badge__text"><?php echo esc_html( $badge_text ); ?></div>
			</div>
		<?php endif; ?>
	</div>
	<a href="<?php the_permalink(); ?>" class="prod-item_bottom w-inline-block">
		<div class="p-12-12 uper m-12-12"><?php the_title(); ?></div>
		<?php
		if ( ! empty( $price ) ) :
			?>
			<div class="horiz indirim-horiz">
				<?php $first_price = loveforever_format_price( ! empty( $price_with_discount ) ? $price_with_discount : $price, 0 ); ?>
				<div class="p-12-12 italic letter-5"><span><?php echo esc_html( $first_price ); ?></span></div>
				<?php if ( ! empty( $price_with_discount ) ) : ?>
					<div class="p-12-12 italic letter-5 oldprice"><span><?php echo esc_html( loveforever_format_price( $price ) ); ?></span></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</a>
</div>
