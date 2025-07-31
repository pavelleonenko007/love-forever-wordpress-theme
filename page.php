<?php
/**
 * Template name: Избранное
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

get_header(
	null,
	array(
		'data-wf-page'                  => '672b5edfcfeb9652455dadc7',
		'barba-container-extra-classes' => array( 'white-top' ),
		'namespace'                     => 'favorite-products',
	)
);

$is_from_link = ! empty( $_GET['favorites'] );
$favorites    = '';

if ( $is_from_link ) {
	$favorites = sanitize_text_field( wp_unslash( $_GET['favorites'] ) );
} else {
	$favorites = ! empty( $_COOKIE['favorites'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['favorites'] ) ) : '';
}

$favorites_link = esc_attr( get_the_permalink() . '?favorites=' . $favorites );
?>
				<div>
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee' ); ?>
						<?php get_template_part( 'components/navbar' ); ?>
					</div>
				</div>
				<section class="section" style="padding-top: 100rem;">
					<div class="container">
						<div class="lf-content flow">
							<h1 class="lf-h1"><?php the_title(); ?></h1>
							<?php the_content(); ?>
						</div>
					</div>
				</section>
		<?php get_template_part( 'components/footer' ); ?>
		<?php get_footer(); ?>
