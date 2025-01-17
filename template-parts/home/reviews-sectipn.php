<?php
/**
 * Reviews Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$reviews_query_args = array(
	'post_type'      => 'reviews',
	'posts_per_page' => 3,
);

$reviews_query = new WP_Query( $reviews_query_args );

if ( $reviews_query->have_posts() ) :
	?>
<section class="section">
	<div class="container">
		<div class="spleet">
			<h2 class="h-36-36">Отзывы</h2>
			<a id="w-node-_42e31bf8-b85c-a9ca-923c-af59232d595e-7ea1ac8d" href="<?php echo esc_url( get_post_type_archive_link( 'reviews' ) ); ?>" class="btn btn-with-arrow w-inline-block">
				<div>Смотреть все</div>
				<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/6720d17cfb5622b535a21354_Arrow20Down.svg' ); ?>" loading="eager" alt class="img-arrow">
			</a>
		</div>
		<div class="splide no-pc">
			<div class="splide__track">
				<div class="splide__list otzivi-grid">
					<?php
					while ( $reviews_query->have_posts() ) :
						$reviews_query->the_post();
						?>
						<div id="w-node-_3d0d88a6-0e54-32cf-18c5-8b1680228c84-7ea1ac8d" class="splide__slide">
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
