<?php
/**
 * Template name: Отзывы
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;
get_header(
	null,
	array(
		'data-wf-page'                  => '67249dc1fa10238682ee40ae',
		'barba-container-extra-classes' => array( 'otzivs-page' ),
		'namespace'                     => 'archive-review',
	)
);

$reviews_hero_section = get_field( 'reviews_hero_section', 'option' );

$infoline_id   = loveforever_get_current_infoline();
$infoline_data = loveforever_get_infoline_data( $infoline_id );
?>
				<section class="section section_100vh">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee', null, $infoline_data ); ?>
						<?php get_template_part( 'components/navbar' ); ?>
						<div class="slider_home-slider_slide-in">
							<div class="mom-abs">
								<?php if ( ! empty( $reviews_hero_section['thumbnail'] ) ) : ?>
									<img src="<?php echo esc_url( wp_get_attachment_image_url( $reviews_hero_section['thumbnail'], 'full' ) ); ?>" loading="eager" alt="<?php echo esc_attr( get_post_meta( $reviews_hero_section['thumbnail'], '_wp_attachment_image_alt', true ) ); ?>" class="img-cover">
								<?php endif; ?>
							</div>
							<div class="slider-bottom-content">
								<?php get_template_part( 'components/breadcrumb' ); ?>
								<h1 class="p-86-96"><?php echo ! empty( $reviews_hero_section['title'] ) ? esc_html( $reviews_hero_section['title'] ) : 'Отзывы'; ?></h1>
								<?php if ( ! empty( $reviews_hero_section['description'] ) ) : ?>
									<p class="p-16-20 mmax480"><?php echo wp_kses_post( $reviews_hero_section['description'] ); ?></p>
								<?php endif; ?>
								<?php
								if ( ! empty( $reviews_hero_section['button'] ) ) :
									$anchor_link_config = array(
										'duration' => 1000,
										'align'    => 'start',
									);
									?>
									<a 
										href="<?php echo esc_url( $reviews_hero_section['button']['url'] ); ?>" 
										class="btn in-slider-btn w-inline-block"
										target="<?php echo esc_attr( $reviews_hero_section['button']['target'] ); ?>"
										data-js-anchor-link="<?php echo esc_attr( wp_json_encode( $anchor_link_config ) ); ?>"
									>
										<div><?php echo esc_html( $reviews_hero_section['button']['title'] ); ?></div>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
		</section>
				<section class="section">
					<div class="container n-top">
						<?php if ( have_posts() ) : ?>
							<div class="otzivi-grid otzivi-flex">
								<?php
								while ( have_posts() ) :
									the_post();
									?>
									<?php get_template_part( 'components/review-card' ); ?>
								<?php endwhile; ?>
							</div>
							<div class="paginate">
								<?php
								global $wp_query;
								echo loveforever_get_pagination_html( $wp_query );
								?>
							</div>
						<?php else : ?>
							<div class="empty-content">
								<p>Отзывы еще не добавлены</p>
							</div>
						<?php endif; ?>
					</div>
				</section>
				<section id="addReview" class="section lf-review-section">
					<div class="container lf-review-section__container">
						<header class="spleet lf-review-section__header">
							<h1 class="h-36-36 lf-review-section__title">Прислать отзыв</h1>
						</header>
						<div class="spleet spleet-top lf-review-section__body">
							<div class="lf-review-section__content flow">
								<p>Нам будет очень ценно услышать ваши впечатления 💕</p>
								<p>Поделитесь отзывом о примерке или покупке платья и, если захотите, прикрепите фото со свадьбы — нам будет приятно сохранить частичку вашей истории👰‍♀️</p>
							</div>
							<div class="otz-form-block lf-review-section__form">
								<form 
									id="addReviewForm" 
									class="reviews-form" 
									data-js-form 
									data-js-review-form 
									data-js-input-zoom-prevention
									data-wf-page-id="67249dc1fa10238682ee40ae" 
									data-wf-element-id="d744d5e4-a65e-3d82-0a54-f293122549ec" 
									novalidate
								>
									<div class="reviews-form__grid">
										<div class="field">
											<fieldset class="field__control star-rating">
												<legend class="sr-only">Оценка</legend>
												<input 
													id="star5"
													type="radio" 
													name="rating" 
													value="5" 
													required
													aria-errormessage="rating-errors"
												/>
												<label for="star5" class="star-rating__label">
													<span aria-hidden="true" class="star-rating__span">
														<svg width="16" height="16" class="star-rating__icon">
															<use href="#star"></use>
														</svg>
													</span>
												</label>
												<input 
													id="star4"
													type="radio" 
													name="rating" 
													value="4" 
													required
													aria-errormessage="rating-errors"
												/>
												<label for="star4" class="star-rating__label">
													<span aria-hidden="true" class="star-rating__span">
														<svg width="16" height="16" class="star-rating__icon">
															<use href="#star"></use>
														</svg>
													</span>
												</label>
												<input 
													id="star3"
													type="radio" 
													name="rating" 
													value="3" 
													required
													aria-errormessage="rating-errors"
												/>
												<label for="star3" class="star-rating__label">
													<span aria-hidden="true" class="star-rating__span">
														<svg width="16" height="16" class="star-rating__icon">
															<use href="#star"></use>
														</svg>
													</span>
												</label>
												<input 
													id="star2"
													type="radio" 
													name="rating" 
													value="2" 
													required
													aria-errormessage="rating-errors"
												/>
												<label for="star2" class="star-rating__label">
													<span aria-hidden="true" class="star-rating__span">
														<svg width="16" height="16" class="star-rating__icon">
															<use href="#star"></use>
														</svg>
													</span>
												</label>
												<input 
													id="star1"
													type="radio" 
													name="rating" 
													value="1" 
													required
													aria-errormessage="rating-errors"
												/>
												<label for="star1" class="star-rating__label">
													<span aria-hidden="true" class="star-rating__span">
														<svg width="16" height="16" class="star-rating__icon">
															<use href="#star"></use>
														</svg>
													</span>
												</label>
												<span class="field__errors" id="rating-errors" data-js-form-field-errors></span>
											</fieldset>
										</div>
										<div class="field">
											<input 
												class="field__control" 
												maxlength="256" 
												name="name" 
												placeholder="Имя" 
												type="text" 
												id="addReviewFormNameField" 
												title="Пожалуйста, укажите ваше имя"
												aria-errormessage="addReviewFormNameFieldErrors"
												required
											>
											<span class="field__errors" id="addReviewFormNameFieldErrors" data-js-form-field-errors></span>
										</div>
										<div class="field" data-js-datepicker>
											<input 
												type="date" 
												name="date" 
												value="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>"
												id="singleDressFormDateField" 
												class="field__control"
												title="Пожалуйста, укажите дату"
												data-js-datepicker-original-control
											>
											<?php
											$today = wp_date( 'd.m.Y' );
											?>
											<input 
												type="text" 
												name="altdate" 
												id="singleDressFormCustomDateField" 
												class="field__control"
												value="<?php echo esc_attr( $today ); ?>"
												data-js-datepicker-custom-control
												data-js-datepicker-config="<?php echo esc_attr( wp_json_encode( array( 'dateFormat' => 'd.mm.yy' ) ) ); ?>"
											/>
											<span class="field__errors" id="addReviewFormDateFieldErrors" data-js-form-field-errors></span>
										</div>
									</div>
									<div class="relaive">
										<div class="field">
											<textarea 
												placeholder="Ваш отзыв" 
												maxlength="5000" 
												id="addReviewFormReviewTextField" 
												name="review_text" 
												class="field__control"
												rows="10"
												title="Оставьте ваши впечатления"
												required
											></textarea>
											<span class="field__errors" id="addReviewFormReviewTextFieldErrors" data-js-form-field-errors></span>
										</div>
										<div class="code-embed-6 w-embed">
											<div class="input-file-row" data-js-file-input>
												<label class="input-file">
													<svg width="18" height="14" viewbox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M17.8182 0L0 1.33514e-06V0.700001V13.3V14H0.742424H17.0758H17.8182L17.8182 13.3V0.7V0ZM0.742424 12.2695L0.742424 0.700001H17.0758L17.0758 12.8538L13.1515 9.15385L12.6266 9.64883L16.499 13.3L0.742424 13.3L6.11996 7.82186L10.342 11.8027L10.867 11.3077L13.1514 9.15386L12.6264 8.65889L10.342 10.8127L6.62463 7.30775L6.62468 7.3077L6.07982 6.83222L0.742424 12.2695ZM14.8485 3.5C14.8485 3.8866 14.5161 4.2 14.1061 4.2C13.696 4.2 13.3636 3.8866 13.3636 3.5C13.3636 3.1134 13.696 2.8 14.1061 2.8C14.5161 2.8 14.8485 3.1134 14.8485 3.5ZM15.5909 3.5C15.5909 4.2732 14.9261 4.9 14.1061 4.9C13.286 4.9 12.6212 4.2732 12.6212 3.5C12.6212 2.7268 13.286 2.1 14.1061 2.1C14.9261 2.1 15.5909 2.7268 15.5909 3.5Z" fill="black"></path>
													</svg>
													<input type="file" data-js-file-input-control name="file[]" multiple accept="image/*"> 
												</label>
												<div class="input-file-list" data-js-file-input-preview-container></div>
											</div>
										</div>
									</div>
									<div class="horiz otz-foriz">
										<input type="hidden" name="action" value="add_review">
										<?php wp_nonce_field( 'submit_review_form', '_submit_review_form_nonce', false ); ?>
										<input type="submit" data-wait="отправка..." class="btn send-btn w-button" value="отправить">
										<div class="p-12-12 uper m-12-12 reviews-form__legal">Нажимая отправить вы соглашаетесь с <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">политикой конфиденциальности</a></div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
		<div id="reviewDialog" role="dialog" class="review-dialog dialog" data-js-dialog>
			<div class="dialog__overlay" data-js-dialog-overlay>
				<div class="review-dialog__content dialog__content" data-js-dialog-content>
					<div class="review-dialog__card review-card">
						<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/review-popup-icon.svg' ); ?>" alt="" class="review-card__icon">
						<div class="review-card__title"></div>
						<div class="review-card__description"></div>
						<button type="button" class="review-card__button button button--pink" data-js-dialog-close-button>Хорошо</button>
					</div>
				</div>
			</div>
		</div>
		<svg style="display: none;" xmlns="http://www.w3.org/2000/svg">
			<symbol id="star" viewBox="-1 -1 18 17" >
				<path d="M3.06751 14.8876C3.33312 15.0818 3.66655 15.0208 4.0678 14.7323L8.00111 11.9034L11.9288 14.7323C12.3357 15.0208 12.6635 15.0818 12.9347 14.8876C13.1947 14.6935 13.2568 14.3718 13.0929 13.9059L11.5501 9.36855L15.5117 6.573C15.9187 6.29008 16.0712 5.99609 15.9695 5.68551C15.8678 5.38041 15.5683 5.2251 15.0653 5.23064L10.1995 5.25838L8.71881 0.698899C8.5606 0.232966 8.32889 0 8.00111 0C7.66767 0 7.43602 0.232966 7.28341 0.698899L5.79709 5.25838L0.931308 5.23064C0.428338 5.2251 0.134468 5.38041 0.0327444 5.68551C-0.074631 5.99609 0.0836066 6.29008 0.484851 6.573L4.44644 9.36855L2.90362 13.9059C2.74538 14.3718 2.80755 14.6935 3.06751 14.8876Z"/>
			</symbol>
		</svg>
		<?php get_template_part( 'components/footer' ); ?>
		<?php get_footer(); ?>
