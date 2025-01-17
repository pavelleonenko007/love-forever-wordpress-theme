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
<section data-acf-layout="stories-section" class="section">
	<div class="container n-top m-scrll">
		<div class="fast-links">
			<?php
			while ( $stories_query->have_posts() ) :
				$stories_query->the_post();
				get_template_part( 'components/story' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php endif; ?>
