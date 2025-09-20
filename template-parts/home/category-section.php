<?php
/**
 * Category Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$section          = $args;
$category         = $section['category'];
$section_name     = ! empty( trim( $section['alt_name'] ) ) ? $section['alt_name'] : $category->name;
$dress_query_args = array(
	'post_type'      => 'dress',
	'posts_per_page' => 4,
	'tax_query'      => array(
		array(
			'taxonomy' => $category->taxonomy,
			'field'    => 'term_id',
			'terms'    => array( $category->term_id ),
		),
	),
);
$dress_query      = new WP_Query( $dress_query_args );
if ( $dress_query->have_posts() ) :
	?>
	<section class="lf-products-section section">
		<div class="lf-products-section__container container">
			<header class="lf-products-section__header spleet">
				<h2 class="h-36-36"><?php echo esc_html( $section_name ); ?></h2>
				<a id="w-node-db76596e-4fc1-70c6-bdf8-c5a48b11e020-7ea1ac8d" href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="btn btn-with-arrow w-inline-block">
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
				class="lf-products-section__catalog category-products splide" 
				data-splide="<?php echo esc_attr( wp_json_encode( $splide_config ) ); ?>" 
				data-js-catalog-splide
			>
				<div class="category-products__track splide__track">
					<div class="category-products__list splide__list">
						<?php
						while ( $dress_query->have_posts() ) :
							$dress_query->the_post();
							?>
							<div id="w-node-cd7ab443-534b-d5a8-285a-97ee704f3d95-7ea1ac8d" class="category-products__slide splide__slide">
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
