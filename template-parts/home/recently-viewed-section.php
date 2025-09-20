<?php
/**
 * Recently Viewed Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$view_all_link   = isset( $args['view_all_link'] ) ? $args['view_all_link'] : true;
$id              = ! empty( $args['id'] ) ? $args['id'] : 'recentlyViewed';
$recently_viewed = loveforever_get_viewed_products();

if ( ! empty( $recently_viewed ) ) :
	$query_args = array(
		'post__in'       => $recently_viewed,
		'posts_per_page' => 4,
		'post_type'      => 'dress',
	);
	$query      = new WP_Query( $query_args );

	if ( $query->have_posts() ) : ?>
<section id="<?php echo esc_attr( $id ); ?>" class="lf-products-section section">
	<div class="lf-products-section__container container">
		<header class="lf-products-section__header spleet">
			<h2 class="h-36-36">Недавно смотрели</h2>
			<?php
			if ( $view_all_link ) :
				?>
				<a 
					id="w-node-a389feaf-b8e5-ed2e-7aa7-ad522ad62349-7ea1ac8d" 
					href="<?php echo esc_url( get_the_permalink( FAVORITES_PAGE_ID ) . '#recentlyViewed' ); ?>" 
					class="btn btn-with-arrow w-inline-block"
				>
					<div>Смотреть все</div>
					<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/6720d17cfb5622b535a21354_Arrow20Down.svg' ); ?>" loading="eager" alt class="img-arrow">
				</a>
			<?php endif; ?>
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
			class="lf-products-section__catalog recently-viewed-products splide" 
			data-splide="<?php echo esc_attr( wp_json_encode( $splide_config ) ); ?>"
			data-js-catalog-splide
		>
			<div class="recently-viewed-products__track splide__track">
				<div class="recently-viewed-products__list splide__list">
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						?>
						<div class="recently-viewed-products__slide splide__slide">
							<?php
							get_template_part(
								'components/dress-card',
								null,
								array(
									'show_carousel' => false,
								)
							);
							?>
						</div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif;
endif; ?>
