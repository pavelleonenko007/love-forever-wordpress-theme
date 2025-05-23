<?php
/**
 * Stories section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$stories_query_args = array(
	'post_type'      => 'story',
	'posts_per_page' => -1,
);
$stories_query      = new WP_Query( $stories_query_args );

if ( $stories_query->have_posts() ) : ?>
<section class="section stories-section">
	<div class="container n-top m-scrll stories-section__container">
		<div class="fast-links stories-section__list">
			<?php
			$i = 0;
			while ( $stories_query->have_posts() ) :
				$stories_query->the_post();
				get_template_part(
					'components/story',
					null,
					array(
						'index' => $i,
					)
				);
				++$i;
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php endif; ?>
