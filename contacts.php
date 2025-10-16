<?php
/**
 * Template Name: Контакты
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

get_header(
	null,
	array(
		'data-wf-page'                  => '672b6e839ec11c45e1f817f5',
		'barba-container-extra-classes' => array(),
	)
);

$infoline_id   = loveforever_get_current_infoline();
$infoline_data = loveforever_get_infoline_data( $infoline_id );
$socials       = array_filter(
	loveforever_get_socials(),
	function ( $social ) {
		return loveforever_get_telegram_link_2() !== $social['url'];
	}
);

the_post();

$phone            = get_field( 'phone', 'option' );
$email            = get_field( 'email', 'option' );
$address          = get_field( 'address' ) ?: get_field( 'address', 'option' );
$address_map_link = get_field( 'address_map_link', 'option' );
$working_hours    = get_field( 'working_hours' ) ?: get_field( 'working_hours', 'option' );
$requisites       = get_field( 'requisites', 'option' );
$metro_stations   = get_field( 'metro_stations', 'option' );
?>
				<section class="section section_100vh">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee', null, $infoline_data ); ?>
						<?php get_template_part( 'components/navbar' ); ?>
						<div class="slider_home-slider_slide-in">
							<div class="mom-abs">
								<?php if ( has_post_thumbnail() ) : ?>
									<img src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" loading="eager" alt class="img-cover">
								<?php endif; ?>
							</div>
							<div class="slider-bottom-content inner-pages">
								<?php get_template_part( 'components/breadcrumb' ); ?>
								<h1 class="p-86-96 lovercase"><?php the_title(); ?></h1>
							</div>
						</div>
					</div>
				</section>
				<section id="contacts" class="section">
					<div class="container">
						<div class="flex-about n-rev">
							<div class="div-block-3 cont-grid">
								<?php if ( ! empty( $address ) ) : ?>
									<div id="w-node-c005dd64-42af-02aa-2c87-fd11761efe0b-e1f817f5" class="div-block-4 cont-item">
										<div class="p-12-12 uper m-12-12">Адрес</div>
										<div class="vert cont-vert">
											<?php if ( ! empty( $address_map_link ) ) : ?>
												<a href="<?php echo esc_url( $address_map_link ); ?>" class="p-36-36 cont-p"><?php echo wp_kses_post( $address ); ?></a>
											<?php else : ?>
												<div class="p-36-36 cont-p"><?php echo wp_kses_post( $address ); ?></div>
											<?php endif; ?>
											<?php if ( ! empty( $metro_stations ) ) : ?>
												<div class="horiz">
													<div class="metro-dot" aria-label="Станции метро">м</div>
													<?php foreach ( $metro_stations as $metro_stations_index => $station ) : ?>
														<?php if ( $metro_stations_index > 0 ) : ?>
															<div class="_2px_cube black"></div>
														<?php endif; ?>
														<div class="p-12-12 uper m-12-12"><?php echo esc_html( $station['metro_station'] ); ?></div>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<div class="div-block-3 cont-grid">
								<?php if ( ! empty( $phone ) || ! empty( $email ) ) : ?>
									<div id="w-node-c9006335-3f7e-a7da-f9d9-ef62baf91847-e1f817f5" class="div-block-4 cont-item">
										<div class="p-12-12 uper m-12-12">Номер и почта</div>
										<div class="vert">
											<?php if ( ! empty( $phone ) ) : ?>
												<a href="<?php echo esc_url( loveforever_format_phone_to_link( $phone ) ); ?>" class="a-32-40"><?php echo esc_html( $phone ); ?></a>
											<?php endif; ?>
											<?php if ( ! empty( $email ) ) : ?>
												<a href="<?php echo esc_url( loveforever_format_email_to_link( $email ) ); ?>" class="a-32-40"><?php echo esc_html( $email ); ?></a>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $working_hours ) ) : ?>
									<div id="w-node-_12072c89-a372-3919-6922-5b80aacd4cc3-e1f817f5" class="div-block-4 cont-item">
										<div class="p-12-12 uper m-12-12">Режим работы</div>
										<div class="a-32-40"><?php echo wp_kses_post( $working_hours ); ?></div>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $socials ) ) : ?>
									<div id="w-node-a3e9d446-0fc6-11f9-087f-e38dd88fa862-e1f817f5" class="div-block-4 cont-item">
										<div class="p-12-12 uper m-12-12">Соц. сети</div>
										<div class="soc-grid lf-share-buttons lf-share-buttons--no-margin">
										<?php foreach ( $socials as $social ) : ?>
											<a class="lf-share-button lf-share-button--dark" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer">
												<svg class="lf-share-button__icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<use href="#<?php echo esc_attr( $social['icon'] ); ?>"></use>
												</svg>
											</a>
										<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $requisites ) ) : ?>
									<div id="w-node-_98436ef0-1da5-fdcb-8dc9-c5f8768d1098-e1f817f5" class="div-block-4 cont-item">
										<div class="p-12-12 uper m-12-12">Реквизиты</div>
										<div class="p-16-20 n-top m-16-20"><?php echo wp_kses_post( $requisites ); ?></div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</section>
				<?php
				$video        = get_field( 'video' );
				$mobile_video = get_field( 'mobile_video' );
				if ( ! empty( $video['mp4'] ) || ! empty( $video['webm'] ) ) :
					?>
					<section class="section">
						<div class="container">
							<div class="lf-video-player hidden-mobile" data-js-video-player>
								<?php
								$video_data_attributes = array(
									'class'       => 'lf-video-player__video',
									'playsinline' => 'true',
									'preload'     => 'metadata',
									'data-js-video-player-video' => '',
								);

								if ( ! empty( $video['poster'] ) ) {
									$video_data_attributes['poster'] = esc_url( $video['poster']['url'] );
								}

								$video_data_attributes_string = loveforever_prepare_tag_attributes_as_string( $video_data_attributes );
								?>
								<video <?php echo $video_data_attributes_string; ?>>
									<?php if ( ! empty( $video['mp4'] ) ) : ?>
										<source src="<?php echo esc_url( $video['mp4']['url'] ); ?>" type="<?php echo esc_attr( $video['mp4']['mime_type'] ); ?>">
									<?php endif; ?>
									<?php if ( ! empty( $video['webm'] ) ) : ?>
										<source src="<?php echo esc_url( $video['webm']['url'] ); ?>" type="<?php echo esc_attr( $video['webm']['mime_type'] ); ?>">
									<?php endif; ?>
									</source>
								</video>
								<div role="button" class="lf-video-player__button playvideobtn" data-js-video-player-button></div>
							</div>
							<div class="lf-video-player visible-mobile" data-js-video-player>
								<?php
								$video_data_attributes = array(
									'class'       => 'lf-video-player__video',
									'playsinline' => 'true',
									'preload'     => 'metadata',
									'data-js-video-player-video' => '',
								);

								if ( ! empty( $mobile_video['poster'] ) ) {
									$video_data_attributes['poster'] = esc_url( $mobile_video['poster']['url'] );
								}

								$video_data_attributes_string = loveforever_prepare_tag_attributes_as_string( $video_data_attributes );
								?>
								<video <?php echo $video_data_attributes_string; ?>>
									<?php if ( ! empty( $mobile_video['mp4'] ) ) : ?>
										<source src="<?php echo esc_url( $mobile_video['mp4']['url'] ); ?>" type="<?php echo esc_attr( $mobile_video['mp4']['mime_type'] ); ?>">
									<?php endif; ?>
									<?php if ( ! empty( $mobile_video['webm'] ) ) : ?>
										<source src="<?php echo esc_url( $mobile_video['webm']['url'] ); ?>" type="<?php echo esc_attr( $mobile_video['webm']['mime_type'] ); ?>">
									<?php endif; ?>
									</source>
								</video>
								<div role="button" class="lf-video-player__button playvideobtn" data-js-video-player-button></div>
							</div>
						</div>
					</section>
				<?php endif; ?>
				<?php
				get_template_part(
					'template-parts/global/map-section',
					null,
					array(
						'is-contact-page' => true,
					)
				);
				?>
				<?php
				$faq = get_field( 'faq' );
				if ( ! empty( $faq ) ) :
					$faq_query_args = array(
						'post_type' => 'faq',
						'post__in'  => $faq,
					);
					$faq_query      = new WP_Query( $faq_query_args );
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
									<?php get_template_part( 'components/faq-item' ); ?>
								<?php endwhile; ?>
								<?php wp_reset_postdata(); ?>
							</div>
						</div>
					</section>
				<?php endif; ?>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
<?php get_footer(); ?>
