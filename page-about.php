<?php
/**
 * Template name: О салоне
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

get_header(
	null,
	array(
		'data-wf-page'                  => '672b69295391593e141460db',
		'barba-container-extra-classes' => array(),
		'namespace'                     => 'about',
	)
);

?>
				<section class="section section_100vh">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee' ); ?>
						<?php get_template_part( 'components/navbar' ); ?>
						<div class="slider_home-slider_slide-in">
							<div class="mom-abs">
								<?php if ( has_post_thumbnail() ) : ?>
									<img 
										src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" 
										loading="eager" 
										alt="<?php echo esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ); ?>"
										class="img-cover"
									>
								<?php endif; ?>
							</div>
							<div class="slider-bottom-content inner-pages">
							<?php get_template_part( 'components/breadcrumb' ); ?>
							<h1 class="p-86-96"><?php the_title(); ?></h1>
							<?php if ( ! empty( get_field( 'address', 'option' ) ) ) : ?>
								<p class="p-16-20 mmax695"><?php echo esc_html( get_field( 'address', 'option' ) ); ?></p>
							<?php endif; ?>
							</div>
						</div>
					</div>
				</section>
				<?php
				$about_us_section = get_field( 'about_us_section' );
				?>
				<section class="section">
					<div class="container">
						<div class="flex-about">
							<?php if ( ! empty( $about_us_section['image'] ) ) : ?>
								<img 
									src="<?php echo esc_url( wp_get_attachment_image_url( $about_us_section['image'], 'full' ) ); ?>" 
									loading="lazy" 
									alt="<?php echo esc_attr( get_post_meta( $about_us_section['image'], '_wp_attachment_image_alt', true ) ); ?>" 
									class="image"
								>
							<?php endif; ?>
							<?php
							$bullets = $about_us_section['bullets'];
							if ( ! empty( $bullets ) ) :
								?>
								<div class="div-block-3">
									<?php foreach ( $bullets as $bullet ) : ?>
										<?php get_template_part( 'components/about-us-bullet', null, $bullet ); ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</section>
				<?php
				$video_section = get_field( 'video_section' );
				?>
				<section class="section">
					<div class="container">
						<?php if ( ! empty( $video_section['heading'] ) ) : ?>
							<h2 class="p-64-64 center"><?php echo $video_section['heading']; ?></h2>
						<?php endif; ?>
						<?php
						$video_player = $video_section['video_player'];
						if ( ! empty( $video_player['mp4'] ) || ! empty( $video_player['webm'] ) ) :
							$poster_url = ! empty( $video_player['poster'] ) ? wp_get_attachment_image_url( $video_player['poster'], 'full' ) : '';
							$videos     = array();
							if ( ! empty( $video_player['mp4'] ) ) {
								$videos[] = $video_player['mp4'];
							}

							if ( ! empty( $video_player['webm'] ) ) {
								$videos[] = $video_player['webm'];
							}

							$video_urls_attr = esc_attr(
								implode(
									',',
									array_map(
										function ( $video ) {
											return $video['url'];
										},
										$videos
									)
								)
							);
							?>
							<div class="_2 w-embed">
								<div 
									data-poster-url="<?php echo esc_url( $poster_url ); ?>" 
									data-video-urls="<?php echo $video_urls_attr; ?>" 
									data-autoplay="false" 
									data-loop="false" 
									data-wf-ignore="true" 
									class="video-block video-player w-background-video w-background-video-atom"
									data-js-video-player
								>
									<video 
										id="db3812d2-ae01-6df8-78f1-ea8f8b9691de-video" 
										poster="<?php echo ! empty( $poster_url ) ? $poster_url : ''; ?>"
										style="<?php echo ! empty( $poster_url ) ? 'background-image:url(' . $poster_url . ')' : ''; ?>" 
										playsinline 
										data-wf-ignore="true" 
										data-object-fit="cover"
										data-js-video-player-video
										class="video-player__video"
									>
										<?php foreach ( $videos as $video ) : ?>
											<source src="<?php echo esc_url( $video['url'] ); ?>" type="<?php echo esc_attr( $video['mime_type'] ); ?>" data-wf-ignore="true">
										<?php endforeach; ?>
									</video>
									<button type="button" class="playvideobtn video-player__button" data-js-video-player-button aria-label="Включить видео"></button>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</section>
				<?php get_template_part( 'template-parts/global/map-section' ); ?>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
<?php get_footer(); ?>
