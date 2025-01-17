<?php
/**
 * Dress Card
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;
$price                = get_field( 'price' );
$has_discount         = get_field( 'has_discount' );
$price_with_discount  = get_field( 'price_with_discount' );
$slider_in_dress_card = get_field( 'slider_in_dress_card' );
?>

<div id="w-node-_6e88719d-fe8f-116f-4337-b580b5a0b461-b5a0b461" class="prod-item">
	<div class="prod-item_top">
		<?php if ( empty( $slider_in_dress_card ) ) : ?>
			<a href="<?php the_permalink(); ?>" class="link w-inline-block">
				<div class="prod-item_img-mom">
					<div class="mom-abs">
						<?php if ( has_post_thumbnail() ) : ?>
							<img src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" loading="lazy" alt class="img-cover">
						<?php endif; ?>
					</div>
				</div>
			</a>
		<?php else : ?>
			<div data-delay="4000" data-animation="slide" class="slider w-slider" data-autoplay="false" data-easing="ease" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-nav-spacing="3" data-duration="500" data-infinite="true">
				<div class="w-slider-mask">
					<?php
					foreach ( $slider_in_dress_card as $slide ) :
						$image = $slide['image'];
						?>
						<div class="w-slide">
							<a href="<?php the_permalink(); ?>" class="link w-inline-block">
								<div class="prod-item_img-mom">
									<div class="mom-abs">
										<?php if ( ! empty( $image ) ) : ?>
											<img alt src="<?php echo esc_url( $image['url'] ); ?>" loading="lazy" class="img-cover">
										<?php endif; ?>
									</div>
								</div>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="left-arrow w-slider-arrow-left">
					<div class="svg w-embed">
						<svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewbox="0 0 6 10" fill="none">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M0.750232 4.28598L5.25007 0L6 0.714289L1.50016 5.00027L5.99944 9.28571L5.24951 10L0 4.99998L0.74993 4.28569L0.750232 4.28598Z" fill="white"></path>
						</svg>
					</div>
				</div>
				<div class="right-arrow w-slider-arrow-right">
					<div class="svg w-embed">
						<svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewbox="0 0 6 10" fill="none">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M5.24977 4.28598L0.74993 0L0 0.714289L4.49984 5.00027L0.000560648 9.28571L0.750491 10L6 4.99998L5.25007 4.28569L5.24977 4.28598Z" fill="white"></path>
						</svg>
					</div>
				</div>
				<div class="none w-slider-nav w-round w-num"></div>
			</div>
		<?php endif; ?>
		<a href="#" class="btn-like w-inline-block">
			<div class="svg w-embed">
				<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
					<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
					<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
					<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
				</svg>
			</div>
		</a>
		<?php if ( $has_discount ) : ?>
			<div class="badge">
				<div class="bagde__text">Скидка</div>
			</div>
		<?php endif; ?>
	</div>
	<a href="<?php the_permalink(); ?>" class="prod-item_bottom w-inline-block">
		<div data-wp="post_title" class="p-12-12 uper m-12-12"><?php the_title(); ?></div>
		<div class="horiz indirim-horiz">
			<?php if ( ! empty( $price ) ) : ?>
				<div class="p-12-12 italic letter-5"><span><?php echo esc_html( loveforever_format_price( $price ) ); ?></span> ₽</div>
			<?php endif; ?>
			<?php if ( ! empty( $price_with_discount ) ) : ?>
				<div class="p-12-12 italic letter-5 oldprice"><span><?php echo esc_html( loveforever_format_price( $price_with_discount ) ); ?></span> ₽</div>
			<?php endif; ?>
		</div>
	</a>
</div>
