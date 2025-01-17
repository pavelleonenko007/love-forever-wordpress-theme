<?php
/**
 * Category Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$section          = $args;
$category         = $section['category'];
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
	<section class="section">
		<div class="container">
			<div class="spleet">
				<h2 class="h-36-36"><?php echo esc_html( $category->name ); ?></h2>
				<a id="w-node-db76596e-4fc1-70c6-bdf8-c5a48b11e020-7ea1ac8d" href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="btn btn-with-arrow w-inline-block">
					<div>Смотреть все</div>
					<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/6720d17cfb5622b535a21354_Arrow20Down.svg' ); ?>" loading="eager" alt class="img-arrow">
				</a>
			</div>
			<div class="splide no-pc">
				<div class="splide__track">
					<div class="splide__list search-grid">
						<?php
						while ( $dress_query->have_posts() ) :
							$dress_query->the_post();
							?>
							<div id="w-node-cd7ab443-534b-d5a8-285a-97ee704f3d95-7ea1ac8d" class="splide__slide">
								<?php get_template_part( 'components/dress-card' ); ?>
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
