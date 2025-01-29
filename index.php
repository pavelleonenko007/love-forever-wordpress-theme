<?php
/**
 * Template Name: Главная
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

get_header(
	null,
	array(
		'data-wf-page'                  => '6720c60d7d5faf3e7ea1ac8d',
		'barba-container-extra-classes' => array(
			'home-page',
		),
		'namespace'                     => 'home',
	)
);
?>
				<section class="section section_100vh homepagesection">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee' ); ?>
						<?php get_template_part( 'components/header' ); ?>
						<?php
						$hero_slider = get_field( 'hero_slider' );
						if ( ! empty( $hero_slider ) ) :
							?>
							<div data-delay="4000" data-animation="cross" class="slider_home-slider w-slider" data-target-element=".w-slider-mask" data-autoplay="false" data-easing="ease" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-acf-repeater="hero_slider" data-nav-spacing="0" data-duration="500" data-infinite="true">
								<div class="w-slider-mask">
									<?php
									foreach ( $hero_slider as $slide ) :
										$image       = $slide['image'];
										$subheading  = $slide['subheading'];
										$heading     = $slide['heading'];
										$description = $slide['description'];
										$button      = $slide['button'];
										?>
										<div class="w-slide">
											<div class="slider_home-slider_slide-in">
												<div class="mom-abs">
													<?php
													if ( ! empty( $image ) ) :
														?>
														<img src="<?php echo esc_url( $image['url'] ); ?>" loading="eager" alt class="img-cover">
													<?php endif; ?>
												</div>
												<div class="slider-bottom-content">
													<?php
													if ( ! empty( $subheading ) ) :
														?>
														<div class="p-12-12"><?php echo wp_kses_post( $subheading ); ?></div>
													<?php endif; ?>
													<?php if ( ! empty( $heading ) ) : ?>
														<p class="p-86-96"><?php echo wp_kses_post( $heading ); ?></p>
													<?php endif; ?>
													<?php if ( ! empty( $description ) ) : ?>
														<p class="p-16-20"><?php echo wp_kses_post( $description ); ?></p>
													<?php endif; ?>
													<?php
													if ( ! empty( $button ) ) :
														$url    = $button['url'];
														$target = ! empty( $button['target'] ) ? $button['target'] : 'self';
														$text   = $button['label'];
														?>
														<a href="<?php echo esc_url( $url ); ?>" class="btn in-slider-btn w-inline-block" target="<?php echo esc_attr( $target ); ?>">
															<div><?php echo esc_html( $text ); ?></div>
														</a>
													<?php endif; ?>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
								<div class="slider_home-slider_l-btn w-slider-arrow-left">
									<div class="w-icon-slider-left"></div>
								</div>
								<div class="slider_home-slider_r-btn w-slider-arrow-right">
									<div class="w-icon-slider-right"></div>
								</div>
								<div class="slider_home-slider_nav w-slider-nav w-round"></div>
							</div>
						<?php endif; ?>
					</div>
				</section>
				<?php
				$sections = get_field( 'sections' );

				if ( ! empty( $sections ) ) :
					?>
					<div data-acf-flexible="sections" class="flexible-content">
						<?php
						foreach ( $sections as $section ) {
							get_template_part( 'template-parts/home/' . $section['acf_fc_layout'], null, $section );
						}
						?>
					</div>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/global/personal-choice-section' ); ?>
				<?php get_template_part( 'template-parts/global/map-section' ); ?>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
		<?php wp_footer(); ?>
	</body>
</html>
