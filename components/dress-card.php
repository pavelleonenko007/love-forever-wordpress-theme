<?php
/**
 * Dress Card
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'size'          => 'large',
		'is_paged'      => false,
		'post_id'       => 0,
		'post_object'   => null,
		'image_loading' => 'lazy',
	)
);

// Получаем пост по ID или используем объект
if ( $args['post_id'] ) {
	$post = get_post( $args['post_id'] );
} elseif ( $args['post_object'] ) {
	$post = $args['post_object'];
} else {
	// Фолбэк на глобальную переменную (не рекомендуется для AJAX)
	global $post;
}

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

<article class="lf-product-card">
	<div class="lf-product-card__body">
		<a href="<?php the_permalink(); ?>" class="lf-product-card__image" aria-label="<?php echo esc_attr( 'Перейти на страницу товара ' . get_the_title() ); ?>" title="<?php echo esc_attr( 'Перейти на страницу товара ' . get_the_title() ); ?>">
			<?php if ( $show_video_in_product_card && ! empty( $video ) ) : ?>
				<video 
					class="card-video"
					loop
					muted
					playsinline 
					data-js-play-if-visible-video
					preload="metadata"
					poster="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'fullhd' ) ); ?>"
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
					// 'lazyLoad'     => 'nearby',
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
									$image_loading    = ( 0 === $index && 'eager' === $args['image_loading'] ) ? 'eager' : 'lazy';
									$image_attributes = array(
										'loading' => $image_loading,
										'class'   => 'img-cover',
									);

									if ( 'eager' === $image_loading ) {
										$image_attributes['fetchpriority'] = 'high';
									}

									echo wp_get_attachment_image(
										$image['image']['ID'],
										'fullhd',
										false,
										$image_attributes
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
						'class'   => 'img-cover',
						'loading' => $args['image_loading'],
					)
				);
				?>
			<?php endif; ?>
		</a>
		<?php
		$is_in_favorites = loveforever_has_product_in_favorites( get_the_ID() );
		$aria_label      = $is_in_favorites ? 'Удалить из избранного товар ' . get_the_title() : 'Добавить в избранное товар ' . get_the_title();
		?>
		<button 
			type="button" 
			class="btn-like lf-like-button w-inline-block <?php echo $is_in_favorites ? 'is-active' : ''; ?>" 
			data-js-add-to-favorite-button="<?php the_ID(); ?>"
			aria-label="<?php echo esc_attr( $aria_label ); ?>"
			title="<?php echo esc_attr( $aria_label ); ?>"
			data-product-name="<?php echo esc_attr( get_the_title() ); ?>"
		>
			<svg class="lf-like-button__icon" xmlns="http://www.w3.org/2000/svg">
				<use href="#heartIcon"></use>
			</svg>
		</button>
		<?php if ( $badge_text ) : ?>
			<div class="badge lf-badge">
				<div class="bagde__text lf-badge__text"><?php echo esc_html( $badge_text ); ?></div>
			</div>
		<?php endif; ?>
	</div>
	<div class="lf-product-card__footer">
		<h3 class="lf-product-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<div class="lf-product-card__prices">
			<?php if ( ! empty( $price ) ) : ?>
				<div class="lf-product-card__price"><?php echo $price_with_discount ? esc_html( loveforever_format_price( $price_with_discount, 0 ) ) : esc_html( loveforever_format_price( $price, 0 ) ); ?></div>
			<?php endif; ?>
			<?php if ( ! empty( $price_with_discount ) ) : ?>
				<div class="lf-product-card__price lf-product-card__price--with-discount"><?php echo esc_html( loveforever_format_price( $price, 0 ) ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</article>
