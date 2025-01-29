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
?>
				<section class="section section_100vh">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee' ); ?>
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
								<?php if ( ! empty( $reviews_hero_section['button'] ) ) : ?>
									<a 
										href="<?php echo esc_url( $reviews_hero_section['button']['url'] ); ?>" 
										class="btn in-slider-btn w-inline-block"
										target="<?php echo esc_attr( $reviews_hero_section['button']['target'] ); ?>"
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
				<section class="section">
					<div class="container">
						<div class="spleet">
							<h2 class="h-36-36">Прислать отзыв</h2>
						</div>
						<div class="spleet spleet-top">
							<ol role="list" class="otziv-left-ul p-16-20 w-list-unstyled">
								<li>
									<p>Начните с описания продукта или услуги, оцените его качество, удобство использования, внешний вид и другие характеристики.</p>
								</li>
								<li>
									<p>Опишите свой личный опыт использования продукта или услуги, поделитесь своими впечатлениями и эмоциями.</p>
								</li>
								<li>
									<p>Укажите, что вам понравилось и что не понравилось в данном продукте или услуге.</p>
								</li>
								<li>
									<p>Объясните, почему вы рекомендуете или не рекомендуете этот продукт или услугу другим пользователям.</p>
								</li>
								<li>
									<p>Предложите советы или рекомендации для улучшения продукта или услуги.</p>
								</li>
							</ol>
							<div class="otz-form-block">
								<form id="addReview" class="reviews-form" data-js-form data-js-review-form data-wf-page-id="67249dc1fa10238682ee40ae" data-wf-element-id="d744d5e4-a65e-3d82-0a54-f293122549ec" novalidate>
									<div class="field">
										<input 
											class="field__control" 
											maxlength="256" 
											name="name" 
											placeholder="Имя" 
											type="text" 
											id="addReviewNameField" 
											aria-errormessage="addReviewNameFieldErrors"
											required
										>
										<span class="field__errors" id="addReviewNameFieldErrors" data-js-form-field-errors></span>
									</div>
									<div class="field">
										<input 
											class="field__control" 
											maxlength="256" 
											name="date" 
											placeholder="Дата" 
											type="date" 
											id="addReviewDateField" 
											required
										>
										<span class="field__errors" id="addReviewDateFieldErrors" data-js-form-field-errors></span>
									</div>
									<div class="relaive">
										<div class="field">
											<textarea 
												placeholder="Ваш отзыв" 
												maxlength="5000" 
												id="addReviewReviewTextField" 
												name="review_text" 
												class="field__control"
												rows="10"
												required
											></textarea>
											<span class="field__errors" id="addReviewReviewTextFieldErrors" data-js-form-field-errors></span>
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
										<div class="p-12-12 uper op03 m-12-12">Нажимая отправить вы соглашаетесь с <a href="#">политикой конфиденциальности</a></div>
									</div>
								</form>
								<div class="w-form-done" data-js-review-form-success-message>
									<div>Thank you! Your submission has been received!</div>
								</div>
								<div class="w-form-fail" data-js-review-form-global-error>
									<div>Oops! Something went wrong while submitting the form.</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				<div class="succes-block">
					<div class="succes-div">
						<img src="<?php echo get_template_directory_uri(); ?>/images/672b6e5326479af83bdbf512_Group20441.svg" loading="lazy" alt class="image-4">
						<div class="p-21-21">Отзыв успешно отправлен</div>
						<p class="p-16-20">Ваш отзыв будет опубликован в течение 48 часов после нашей проверки. Спасибо за понимание!</p>
						<a href="#" class="btn in-single-btn close-sucess w-inline-block">
							<div>хорошо</div>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
		<?php get_footer(); ?>
