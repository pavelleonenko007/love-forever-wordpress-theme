<?php
/**
 * Stories Modal Component
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $args['stories'] ) ) :
	?>
<div
	id="storiesDialog"
	role="dialog"
	class="stories-dialog dialog"
	data-js-dialog
>
	<div
		class="stories-dialog__overlay dialog__overlay"
		data-js-dialog-overlay
	>
		<div
			class="stories-dialog__content dialog__content"
			data-js-dialog-content
		>
			<div
				class="stories-slider splide"
				role="group"
				aria-label="Splide Basic HTML Example"
				data-js-stories
			>
				<div class="stories-slider__track splide__track">
					<ul class="stories-slider__list splide__list">
						<?php foreach ( $args['stories'] as $story ) : ?>
							<li class="stories-slider__slide splide__slide">
								<div
									class="story-slider splide"
									role="group"
									aria-label="Splide Basic HTML Example"
									data-js-story
								>
									<div class="story-slider__track splide__track">
										<ul class="story-slider__list splide__list">
											<?php
											$slides = get_field( 'slides', $story->ID );
											foreach ( $slides as $slide ) :
												$image_or_video = $slide['image_or_video'];
												$interval = 5000;

												if ( 'video' === $slide['image_or_video']['type'] ) {
													$interval = absint( wp_get_attachment_metadata( $image_or_video['ID'] )['length'] ) * 1000;
												}

												?>
												<li class="story-slider__slide splide__slide" data-splide-interval="<?php echo esc_attr( $interval ); ?>">
													<div class="story">
														<?php
														if ( 'video' === $image_or_video['type'] ) :
															?>
															<video class="story__bg" playsinline>
																<source src="<?php echo esc_url( $image_or_video['url'] ); ?>" type="video/mp4">
															</video>
															<div class="story__loader" data-js-video-loader>
																<div class="story__loader-spinner"></div>
																<div class="story__loader-text">Загрузка...</div>
															</div>
														<?php else : ?>
															<?php
															echo wp_get_attachment_image(
																$image_or_video['ID'],
																'large',
																false,
																array(
																	'class' => 'story__bg',
																)
															);
															?>
														<?php endif; ?>
														<div class="story__body">
															<div class="story__content">
																<div class="story__title h3"><?php echo $slide['story_title']; ?></div>
																<div class="story__description">
																	<p><?php echo $slide['story_description']; ?></p>
																</div>
																<?php
																if ( ! empty( $slide['cta'] ) ) :
																	$cta = $slide['cta'];
																	?>
																	<a
																		href="<?php echo esc_url( $cta['url'] ); ?>" 
																		class="story__cta button button--pink" 
																		data-js-story-cta
																	><?php echo esc_html( $cta['title'] ); ?></a>
																<?php endif; ?>
															</div>
														</div>
													</div>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<button type="button" class="stories-dialog__close dialog__close" data-js-dialog-close-button>
		<svg
			width="18"
			height="18"
			viewBox="0 0 18 18"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
		>
			<mask
				id="mask0_451_2489"
				style="mask-type: alpha"
				maskUnits="userSpaceOnUse"
				x="0"
				y="0"
				width="18"
				height="18"
			>
				<rect width="18" height="18" fill="#D9D9D9" />
			</mask>
			<g mask="url(#mask0_451_2489)">
				<path
					fill-rule="evenodd"
					clip-rule="evenodd"
					d="M8.84924 8.14201L1.77818 1.07095L1.07107 1.77805L8.14214 8.84912L1.07107 15.9202L1.77817 16.6273L8.84924 9.55623L15.9203 16.6273L16.6274 15.9202L9.55635 8.84912L16.6274 1.77805L15.9203 1.07095L8.84924 8.14201Z"
					fill="currentColor"
				/>
			</g>
		</svg>
	</button>
</div>
<?php endif; ?>
