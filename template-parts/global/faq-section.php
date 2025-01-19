<?php
/**
 * Faq Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$faq_query_args = array(
	'post_type'      => 'faq',
	'posts_per_page' => ! empty( $args['faqs'] ) ? count( $args['faqs'] ) : 5,
);

if ( ! empty( $args['faqs'] ) ) {
	$faq_query_args['post__in'] = $args['faqs'];
}

$faq_query = new WP_Query( $faq_query_args );

if ( $faq_query->have_posts() ) :
	?>
	<section class="section">
		<div class="container">
			<div class="spleet">
				<h2 class="h-36-36">Вопрос - ответ</h2>
			</div>
			<div class="faq-block">
				<?php
				while ( $faq_query->have_posts() ) :
					$faq_query->the_post();
					?>
					<?php get_template_part( 'components/faq-dropdown' ); ?>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>
