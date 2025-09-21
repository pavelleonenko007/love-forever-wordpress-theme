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

$infoline_id   = loveforever_get_current_infoline();
$infoline_data = loveforever_get_infoline_data( $infoline_id );
?>
				<section class="section section_100vh homepagesection hero-section">
					<div class="container container-fw n-top">
						<?php
						get_template_part(
							'components/marquee',
							null,
							$infoline_data
						);
						?>
						<?php get_template_part( 'components/navbar' ); ?>
						<?php
						$hero_slider = get_field( 'hero_slider' );
						if ( ! empty( $hero_slider ) ) :
							?>
							<div data-delay="4000" data-animation="cross" class="slider_home-slider w-slider hero-slider" data-target-element=".w-slider-mask" data-autoplay="true" data-easing="ease" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-acf-repeater="hero_slider" data-nav-spacing="0" data-duration="500" data-infinite="true">
								<div class="hero-slider__mask w-slider-mask">
									<?php
									foreach ( $hero_slider as $slide ) :
										$image       = $slide['image'];
										$subheading  = $slide['subheading'];
										$heading     = $slide['heading'];
										$description = $slide['description'];
										$button      = $slide['button'];
										?>
										<div class="hero-slider__slide w-slide">
											<div class="slider_home-slider_slide-in hero-slider__slide-in">
												<div class="mom-abs hero-slider__image">
													<?php
													if ( ! empty( $image ) ) :
														?>
														<img src="<?php echo esc_url( wp_get_attachment_image_url( $image, 'full' ) ); ?>" loading="eager" alt class="img-cover">
													<?php endif; ?>
												</div>
												<div class="slider-bottom-content hero-slider__bottom-content">
													<?php
													if ( ! empty( $subheading ) ) :
														?>
														<div class="p-12-12"><?php echo wp_kses_post( mb_strtoupper( $subheading ) ); ?></div>
													<?php endif; ?>
													<?php if ( ! empty( $heading ) ) : ?>
														<p class="p-86-96"><?php echo wp_kses_post( mb_strtolower( $heading ) ); ?></p>
													<?php endif; ?>
													<?php if ( ! empty( $description ) ) : ?>
														<p class="p-16-20"><?php echo wp_kses_post( $description ); ?></p>
													<?php endif; ?>
													<?php
													if ( ! empty( $button ) ) :
														$url    = $button['url'];
														$target = ! empty( $button['target'] ) ? $button['target'] : 'self';
														$text   = $button['title'];
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
								<div class="slider_home-slider_l-btn w-slider-arrow-left hero-slider__arrow">
									<div class="w-icon-slider-left"></div>
								</div>
								<div class="slider_home-slider_r-btn w-slider-arrow-right hero-slider__arrow">
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
					<div class="flexible-content">
						<?php
						foreach ( $sections as $section ) {
							get_template_part( 'template-parts/home/' . $section['acf_fc_layout'], null, $section );
						}
						?>
					</div>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/global/personal-choice-section' ); ?>
				<?php get_template_part( 'template-parts/global/map-section' ); ?>
				<?php get_template_part( 'template-parts/global/content-section', null, array( 'content' => get_the_content() ) ); ?>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
		<?php wp_footer(); ?>
		<?php
			$stories_sections = array_filter(
				$sections,
				function ( $section ) {
					return 'stories-section' === $section['acf_fc_layout'];
				}
			);
			$stories          = array_reduce(
				$stories_sections,
				function ( $acc, $section ) {
					return array_merge( $acc, ! empty( $section['stories'] ) ? $section['stories'] : array() );
				},
				array()
			);
			?>
		<?php get_template_part( 'template-parts/global/stories-dialog', null, array( 'stories' => $stories ) ); ?>
		<?php get_template_part( 'components/global-fitting-dialog-simpler' ); ?>
		<?php get_template_part( 'components/callback-dialog' ); ?>
	</body>
</html>
