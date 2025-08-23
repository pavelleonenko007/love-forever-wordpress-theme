<?php
/**
 * Stories section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $args['stories'] ) ) : ?>
<section class="section stories-section">
	<div class="container n-top m-scrll stories-section__container">
		<div class="fast-links stories-section__list">
			<?php
			foreach ( $args['stories'] as $i => $post ) :
				setup_postdata( $post );
				get_template_part(
					'components/story',
					null,
					array(
						'index' => $i,
					)
				);
			endforeach;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
<?php endif; ?>
