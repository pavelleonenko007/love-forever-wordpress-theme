<?php
/**
 * Blog Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$blog_query_args = array(
	'post_type'      => 'post',
	'posts_per_page' => 3,
);

$blog_query = new WP_Query( $blog_query_args );

if ( $blog_query->have_posts() ) :
	?>
<section class="section">
	<div class="container">
		<div class="spleet">
			<h2 class="h-36-36">Блог</h2>
			<?php // TODO: Добавить кнопку "Смотреть все"! ?>
			<a id="w-node-b902059f-b71b-0755-15ad-407d247cc3d2-7ea1ac8d" href="#" class="btn btn-with-arrow w-inline-block">
				<div>Смотреть все</div>
				<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/6720d17cfb5622b535a21354_Arrow20Down.svg' ); ?>" loading="eager" alt class="img-arrow">
			</a>
		</div>
		<div class="splide no-pc blog">
			<div class="splide__track">
				<div class="splide__list search-grid">
					<?php
					while ( $blog_query->have_posts() ) :
						$blog_query->the_post();
						?>
						<div id="w-node-_2b680d64-d882-00a4-ac7a-e5a08c854948-7ea1ac8d" class="splide__slide">
							<?php get_template_part( 'components/blog-card' ); ?>
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
