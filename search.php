<?php
/**
 * Template name: Search
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

get_header(
	null,
	array(
		'data-wf-page'                  => '6720c60d7d5faf3e7ea1ac86',
		'barba-container-extra-classes' => array(
			'white-top',
		),
		'namespace'                     => 'search',
	)
);

$infoline_id   = loveforever_get_current_infoline();
$infoline_data = loveforever_get_infoline_data( $infoline_id );
?>
			<section class="section">
				<div class="container container-fw n-top">
					<?php get_template_part( 'components/marquee', null, $infoline_data ); ?>
					<?php get_template_part( 'components/navbar' ); ?>
					<div class="page-top">
						<div class="horiz">
							<div class="p-12-12 uper"> 
							<?php
							$count = $wp_query->post_count;
							$word1 = 'Найден';
							$word2 = 'товар';
							if ( 1 === $count % 10 && 11 !== $count % 100 ) {
								$word1 .= '';
							} else {
								$word1 .= 'о';
							}

							if ( 1 === $count % 10 && 11 !== $count % 100 ) {
								$word2 .= '';
							} elseif ( $count % 10 >= 2 && $count % 10 <= 4 && ( $count % 100 < 10 || $count % 100 >= 20 ) ) {
								$word2 .= 'а';
							} else {
								$word2 .= 'ов';
							}
							echo esc_html( $word1 . ' ' . $count . ' ' . $word2 . ' по вашему запросу' );
							?>
							</div>
						</div>
						<h1 class="p-86-96"><?php echo esc_html( $_GET['s'] ); ?></h1>
					</div>
				</div>
			</section>
			<section class="section">
				<div class="container n-top">
					<div class="catalog-grid search-page">
						<?php
						if ( have_posts() ) :
							while ( have_posts() ) :
								the_post();
								?>
								<?php
								get_template_part( 'components/dress-card', null );
								?>
							<?php endwhile; ?>
						<?php else : ?>
							<div class="empty-content">
								<p>Товары не найдены</p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
		</div>
	</div>
	<?php get_template_part( 'components/footer' ); ?>
<?php get_footer(); ?>
