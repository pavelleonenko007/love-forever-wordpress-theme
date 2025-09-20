<?php
/**
 * Reviews Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$reviews_query_args = array(
	'post_type'      => 'review',
	'posts_per_page' => 3,
);

$reviews_query = new WP_Query( $reviews_query_args );

if ( $reviews_query->have_posts() ) :
	?>
<section class="lf-products-section section">
	<div class="lf-products-section__container container">
		<header class="lf-products-section__header spleet">
			<h2 class="h-36-36">Отзывы</h2>
			<a id="w-node-_42e31bf8-b85c-a9ca-923c-af59232d595e-7ea1ac8d" href="<?php echo esc_url( get_post_type_archive_link( 'review' ) ); ?>" class="btn btn-with-arrow w-inline-block">
				<div>Смотреть все</div>
				<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/6720d17cfb5622b535a21354_Arrow20Down.svg' ); ?>" loading="eager" alt class="img-arrow">
			</a>
		</header>
		<?php
		$splide_config = array(
			'perMove'     => 1,
			'perPage'     => 1,
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
			class="lf-products-section__catalog reviews-slider splide" 
			data-splide="<?php echo esc_attr( wp_json_encode( $splide_config ) ); ?>" 
			data-js-catalog-splide
		>
			<div class="reviews-slider__track splide__track">
				<div class="reviews-slider__list splide__list otzivi-grid">
					<?php
					while ( $reviews_query->have_posts() ) :
						$reviews_query->the_post();
						?>
						<div class="reviews-slider__slide splide__slide">
							<?php get_template_part( 'components/review-card' ); ?>
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
<?php endif; ?>
