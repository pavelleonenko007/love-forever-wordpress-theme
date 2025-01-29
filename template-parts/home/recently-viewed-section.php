<?php
/**
 * Recently Viewed Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$recently_viewed = loveforever_get_viewed_products();

if ( ! empty( $recently_viewed ) ) :
	$query_args = array(
		'post__in'       => $recently_viewed,
		'posts_per_page' => 4,
		'post_type'      => 'dress',
	);
	$query      = new WP_Query( $query_args );

	if ( $query->have_posts() ) : ?>
<section class="section">
	<div class="container">
		<div class="spleet">
			<h2 class="h-36-36">Недавно смотрели</h2>
			<?php // TODO: Добавить кнопку "Смотреть все"! ?>
			<a id="w-node-a389feaf-b8e5-ed2e-7aa7-ad522ad62349-7ea1ac8d" href="#" class="btn btn-with-arrow w-inline-block">
				<div>Смотреть все</div>
				<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/6720d17cfb5622b535a21354_Arrow20Down.svg' ); ?>" loading="eager" alt class="img-arrow">
			</a>
		</div>
		<div class="splide no-pc">
			<div class="splide__track">
				<div class="splide__list search-grid">
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						?>
						<div id="w-node-_060f033b-d108-876e-d4aa-9a2eeb269f38-7ea1ac8d" class="splide__slide">
							<?php get_template_part( 'components/dress-card' ); ?>
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
