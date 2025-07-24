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

the_post();

$phone            = get_field( 'phone', 'option' );
$email            = get_field( 'email', 'option' );
$address          = get_field( 'address', 'option' );
$address_map_link = get_field( 'address_map_link', 'option' );
$working_hours    = get_field( 'working_hours', 'option' );
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
								<h1 class="p-86-96"><?php the_title(); ?></h1>
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
													<div class="metro-dot">м</div>
													<?php foreach ( $metro_stations as $metro_stations_index => $station ) : ?>
														<?php if ( $metro_stations_index > 0 ) : ?>
															<div class="_2px_cube black"></div>
														<?php endif; ?>
														<div class="p-12-12 uper m-12-12"><?php echo esc_html( $station ); ?></div>
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
								<?php if ( ! empty( VK_LINK ) || ! empty( TELEGRAM_LINK ) || ! empty( WHATSAPP_LINK ) || ! empty( INSTAGRAM_LINK ) || ! empty( YOUTUBE_LINK ) ) : ?>
									<div id="w-node-a3e9d446-0fc6-11f9-087f-e38dd88fa862-e1f817f5" class="div-block-4 cont-item">
										<div class="p-12-12 uper m-12-12">Соц. сети</div>
										<div class="soc-grid">
											<?php if ( ! empty( VK_LINK ) ) : ?>
												<a href="<?php echo esc_url( VK_LINK ); ?>" class="soc-btn w-inline-block" target="_blank" rel="noopener noreferrer nofollow">
													<div class="svg-share w-embed">
														<svg width="16" height="10" viewbox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M8.71455 10C3.24797 10 0.129919 6.24625 0 0H2.73829C2.82823 4.58458 4.84697 6.52653 6.44597 6.92693V0H9.02436V3.95395C10.6034 3.78378 12.2623 1.98198 12.822 0H15.4004C15.1895 1.02791 14.7691 2.00118 14.1655 2.85893C13.562 3.71668 12.7882 4.44045 11.8926 4.98498C12.8923 5.48254 13.7753 6.18678 14.4833 7.05125C15.1913 7.91571 15.7082 8.92073 16 10H13.1618C12.8999 9.06258 12.3676 8.22343 11.6316 7.58773C10.8956 6.95203 9.9886 6.54805 9.02436 6.42643V10H8.71455Z" fill="black"></path>
														</svg>
													</div>
												</a>
											<?php endif; ?>
											<?php if ( ! empty( TELEGRAM_LINK ) ) : ?>
												<a href="<?php echo esc_url( TELEGRAM_LINK ); ?>" class="soc-btn w-inline-block" target="_blank" rel="noopener noreferrer nofollow">
													<div class="svg-share w-embed">
														<svg width="14" height="13" viewbox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M14 0.460525L11.7855 12.4588C11.7855 12.4588 11.4756 13.2907 10.6244 12.8917L5.51495 8.68138L5.49126 8.66897C6.18143 8.00295 11.5333 2.83145 11.7673 2.59703C12.1294 2.23398 11.9046 2.01785 11.4841 2.2921L3.57869 7.68757L0.528786 6.5847C0.528786 6.5847 0.0488212 6.40122 0.00264736 6.00226C-0.044134 5.60264 0.544582 5.38651 0.544582 5.38651L12.9781 0.144489C12.9781 0.144489 14 -0.338054 14 0.460525Z" fill="black"></path>
														</svg>
													</div>
												</a>
											<?php endif; ?>
											<?php if ( ! empty( WHATSAPP_LINK ) ) : ?>
												<a href="<?php echo esc_url( WHATSAPP_LINK ); ?>" class="soc-btn w-inline-block" target="_blank" rel="noopener noreferrer nofollow">
													<div class="svg-share w-embed">
														<svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M13.6585 2.33333C12.1533 0.833333 10.1463 0 8.02787 0C3.62369 0 0.0557483 3.55556 0.0557483 7.94444C0.0557483 9.33333 0.445993 10.7222 1.11498 11.8889L0 16L4.23694 14.8889C5.40767 15.5 6.68989 15.8333 8.02787 15.8333C12.4321 15.8333 16 12.2778 16 7.88889C15.9443 5.83333 15.1638 3.83333 13.6585 2.33333ZM11.8746 10.7778C11.7073 11.2222 10.9268 11.6667 10.5366 11.7222C10.2021 11.7778 9.7561 11.7778 9.31011 11.6667C9.03136 11.5556 8.64112 11.4444 8.19512 11.2222C6.18815 10.3889 4.90592 8.38889 4.79443 8.22222C4.68293 8.11111 3.95819 7.16667 3.95819 6.16667C3.95819 5.16667 4.45993 4.72222 4.62718 4.5C4.79443 4.27778 5.01742 4.27778 5.18467 4.27778C5.29617 4.27778 5.46341 4.27778 5.57491 4.27778C5.68641 4.27778 5.85366 4.22222 6.02091 4.61111C6.18815 5 6.5784 6 6.63415 6.05556C6.68989 6.16667 6.68989 6.27778 6.63415 6.38889C6.5784 6.5 6.52265 6.61111 6.41115 6.72222C6.29965 6.83333 6.18815 7 6.1324 7.05556C6.0209 7.16667 5.90941 7.27778 6.02091 7.44444C6.1324 7.66667 6.52265 8.27778 7.13589 8.83333C7.91638 9.5 8.52962 9.72222 8.75261 9.83333C8.97561 9.94445 9.08711 9.88889 9.1986 9.77778C9.3101 9.66667 9.70035 9.22222 9.81185 9C9.92335 8.77778 10.0906 8.83333 10.2578 8.88889C10.4251 8.94444 11.4286 9.44445 11.5958 9.55556C11.8188 9.66667 11.9303 9.72222 11.9861 9.77778C12.0418 9.94444 12.0418 10.3333 11.8746 10.7778Z" fill="black"></path>
														</svg>
													</div>
												</a>
											<?php endif; ?>
											<?php if ( ! empty( INSTAGRAM_LINK ) ) : ?>
												<a href="<?php echo esc_url( INSTAGRAM_LINK ); ?>" class="soc-btn w-inline-block" target="_blank" rel="noopener noreferrer nofollow">
													<div class="svg-share w-embed">
														<svg width="100%" height="100%" viewbox="0 0 40 60" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M23.53 22H16.4282C14.0052 22 12 24.0052 12 26.4282V33.5718C12 35.9948 14.0052 38 16.4282 38H23.53C25.953 38 27.9582 35.9948 28 33.5718V26.47C28 24.0052 25.9948 22 23.53 22ZM19.9791 33.7807C17.8903 33.7807 16.2193 32.1097 16.2193 30.0209C16.2193 27.9321 17.8903 26.3029 19.9373 26.3029C21.9843 26.3029 23.6554 27.9739 23.6554 30.0209C23.6554 32.0679 22.0261 33.7807 19.9791 33.7807ZM25.8277 25.9269C25.4935 26.47 24.7833 26.6789 24.282 26.3446C23.7389 26.0104 23.53 25.3003 23.8642 24.799C24.1984 24.2559 24.9086 24.047 25.4099 24.3812C25.953 24.6736 26.1201 25.3838 25.8277 25.9269Z" fill="black"></path>
														</svg>
													</div>
												</a>
											<?php endif; ?>
											<?php if ( ! empty( YOUTUBE_LINK ) ) : ?>
												<a href="<?php echo esc_url( YOUTUBE_LINK ); ?>" class="soc-btn w-inline-block" target="_blank" rel="noopener noreferrer nofollow">
													<div class="svg-share w-embed">
														<svg width="100%" height="100%" viewbox="0 0 40 60" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path fill-rule="evenodd" clip-rule="evenodd" d="M12.1596 36.5864C11.7852 36.478 11.4436 36.2853 11.1635 36.0245C10.8835 35.7637 10.6733 35.4424 10.5507 35.0878C9.85006 33.2549 9.64246 25.6011 10.9919 24.0902C11.441 23.5987 12.0751 23.2961 12.7565 23.248C16.3766 22.8765 27.5612 22.926 28.8458 23.3719C29.2071 23.4838 29.5372 23.6722 29.8113 23.9232C30.0854 24.1741 30.2965 24.481 30.4287 24.8209C31.1943 26.7158 31.2202 33.6016 30.3249 35.4222C30.0875 35.8961 29.6899 36.28 29.1961 36.5121C27.8467 37.1561 13.9502 37.1437 12.1596 36.5864ZM17.9077 32.9948L24.3953 29.7748L17.9077 26.53V32.9948Z" fill="black"></path>
														</svg>
													</div>
												</a>
											<?php endif; ?>
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
				$video = get_field( 'video' );
				if ( ! empty( $video['mp4'] ) || ! empty( $video['webm'] ) ) :
					?>
					<section class="section">
						<div class="container">
							<div class="w-embed">
								<?php
								$data_video_urls_attribute = '';
								if ( ! empty( $video['mp4'] ) ) {
									$data_video_urls_attribute .= esc_url( $video['mp4']['url'] );
								}
								if ( ! empty( $video['webm'] ) ) {
									if ( ! empty( $data_video_urls_attribute ) ) {
										$data_video_urls_attribute .= ',';
									}
									$data_video_urls_attribute .= esc_url( $video['webm']['url'] );
								}
								?>
								<div 
									data-poster-url="<?php echo esc_url( $video['poster']['url'] ); ?>" 
									data-video-urls="<?php echo esc_attr( $data_video_urls_attribute ); ?>" 
									data-autoplay="false" 
									data-loop="false" 
									data-wf-ignore="true" 
									class="video-block w-background-video w-background-video-atom"
								>
									<video id="db3812d2-ae01-6df8-78f1-ea8f8b9691de-video" style='background-image:url("<?php echo esc_url( $video['poster']['url'] ); ?>")' playsinline data-wf-ignore="true" data-object-fit="cover">
										<?php if ( ! empty( $video['mp4'] ) ) : ?>
											<source src="<?php echo esc_url( $video['mp4']['url'] ); ?>" type="<?php echo esc_attr( $video['mp4']['mime_type'] ); ?>" data-wf-ignore="true">
										<?php endif; ?>
										<?php if ( ! empty( $video['webm'] ) ) : ?>
											<source src="<?php echo esc_url( $video['webm']['url'] ); ?>" type="<?php echo esc_attr( $video['webm']['mime_type'] ); ?>" data-wf-ignore="true">
										<?php endif; ?>
										</source>
										</source>
									</video>
									<div class="playvideobtn"></div>
								</div>
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
