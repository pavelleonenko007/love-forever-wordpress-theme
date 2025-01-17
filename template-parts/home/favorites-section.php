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
		<section class="section">
			<div class="container">
				<div class="spleet">
					<h2 class="h-36-36">Избранное</h2>
					<?php
					// TODO: Добавить кнопку "Смотреть все"!
					?>
					<a id="w-node-_0ae708a4-2631-fce5-071c-c19af11d248f-7ea1ac8d" href="#" class="btn btn-with-arrow w-inline-block">
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
								<div id="w-node-b3928613-0859-fa0e-0b82-3fb78df5fb76-7ea1ac8d" class="splide__slide">
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
<?php endif;
endif; ?>
