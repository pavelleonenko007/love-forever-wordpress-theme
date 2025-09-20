<?php
/**
 * Favorites Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $_COOKIE['favorites'] ) ) :
	$query_args = array(
		'post_type'      => 'dress',
		'post__in'       => explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['favorites'] ) ) ),
		'posts_per_page' => 4,
	);

	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) : ?>
		<section class="lf-products-section section">
			<div class="lf-products-section__container container">
				<header class="lf-products-section__header spleet">
					<h2 class="h-36-36">Избранное</h2>
					<?php
					// TODO: Добавить кнопку "Смотреть все"!
					?>
					<a id="w-node-_0ae708a4-2631-fce5-071c-c19af11d248f-7ea1ac8d" href="<?php echo esc_url( home_url( '/' ) . 'favorites/' ); ?>" class="btn btn-with-arrow w-inline-block">
						<div>Смотреть все</div>
						<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/6720d17cfb5622b535a21354_Arrow20Down.svg' ); ?>" loading="eager" alt class="img-arrow">
					</a>
				</header>
				<?php
				$splide_config = array(
					'perMove'     => 1,
					'perPage'     => 2,
					'arrows'      => false,
					'focus'       => 0,
					'speed'       => 500,
					'gap'         => '10rem',
					'autoplay'    => false,
					'interval'    => 2000,
					'pagination'  => true,
					'omitEnd'     => true,
					'rewind'      => true,
					'mediaQuery'  => 'min',
					'breakpoints' => array(
						993 => array(
							'destroy' => true,
						),
					),
					'classes'     => array(
						'pagination' => 'lf-products-section__pagination splide__pagination',
						'page'       => 'lf-products-section__page splide__pagination__page',
					),
				);
				?>
				<div 
					class="lf-products-section__catalog favorites-products splide" 
					data-splide="<?php echo esc_attr( wp_json_encode( $splide_config ) ); ?>" 
					data-js-catalog-splide
				>
					<div class="favorites-products__track splide__track">
						<div class="favorites-products__list splide__list">
							<?php
							while ( $query->have_posts() ) :
								$query->the_post();
								?>
								<div id="w-node-b3928613-0859-fa0e-0b82-3fb78df5fb76-7ea1ac8d" class="splide__slide">
									<?php get_template_part( 'components/dress-card', null, array( 'show_carousel' => false ) ); ?>
								</div>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</div>
				</div>
			</div>
		</section>
<?php endif;
endif; ?>
