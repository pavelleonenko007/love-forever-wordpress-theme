<?php
/**
 * Template name: Product
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

get_header(
	null,
	array(
		'data-wf-page'                  => '67239bd83c4331a3450cc872',
		'barba-container-extra-classes' => array( 'white-top' ),
		'namespace'                     => 'single-dress',
		'product-id'                    => get_the_ID(),
	)
);

$availability        = get_field( 'availability' );
$is_new              = get_field( 'is_new' );
$images              = get_field( 'images' );
$colors              = get_field( 'colors' );
$price               = get_field( 'price' );
$has_discount        = get_field( 'has_discount' );
$price_with_discount = $has_discount ? get_field( 'price_with_discount' ) : null;
$brand               = ! empty( get_the_terms( get_the_ID(), 'brand' ) ) && ! is_wp_error( get_the_terms( get_the_ID(), 'brand' ) ) ? get_the_terms( get_the_ID(), 'brand' )[0]->name : null;
$tags                = get_the_terms( get_the_ID(), 'dress_tag' );
$dress_category      = get_the_terms( get_the_ID(), 'dress_category' );
$related_products    = get_field( 'related_products' );
?>
				<section class="section">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee' ); ?>
						<?php get_template_part( 'components/navbar' ); ?>
					</div>
					<div class="container n-top">
						<div class="container container-fw n-top">
							<div class="page-top single-p"></div>
						</div>
						<div class="spleet m-h-vert">
							<div class="code-embed-2 w-embed">
								<nav aria-label="Breadcrumb" class="breadcrumb">
									<?php get_template_part( 'components/breadcrumb', null, array( 'extra_classes' => array( 'breadcrumbs--single-dress' ) ) ); ?>
								</nav>
							</div>
							<?php if ( ! empty( $images ) ) : ?>
							<div data-delay="4000" data-animation="slide" class="m-prod-slider w-slider" data-autoplay="false" data-easing="ease" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-nav-spacing="3" data-duration="500" data-infinite="true">
								<div class="m-prod-slider_mask w-slider-mask">
									<?php foreach ( $images as $image_slide ) : ?>
										<div class="w-slide">
											<div class="mom-abs">
												<?php if ( ! empty( $image_slide['image'] ) ) : ?>
													<img src="<?php echo esc_url( $image_slide['image']['url'] ); ?>" loading="lazy" alt="<?php echo esc_attr( $image_slide['image']['alt'] ); ?>" class="img-cover">
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
								<div class="none w-slider-arrow-left">
									<div class="w-icon-slider-left"></div>
								</div>
								<div class="none w-slider-arrow-right">
									<div class="w-icon-slider-right"></div>
								</div>
								<div class="m-prod-slider_nav w-slider-nav w-round"></div>
							</div>
							<?php endif; ?>
							<div class="single-right-block">
								<div class="p-12-12 single-right-highl m-12-12"><?php echo ! empty( $availability ) ? 'Есть в наличии' : 'Нет в наличии'; ?></div>
								<?php if ( ! empty( $is_new ) ) : ?>
									<div class="_2px_cube"></div>
									<div class="p-12-12 uper m-12-12">Новинка</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="single-prod-grid">
							<?php if ( ! empty( $images ) ) : ?>
							<div class="single-images">
								<?php foreach ( $images as $image_item ) : ?>
									<div class="single-img-mom">
										<img src="<?php echo esc_url( $image_item['image']['url'] ); ?>" loading="lazy" alt="<?php echo esc_attr( $image_item['image']['alt'] ); ?>" class="img-fw">
									</div>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
							<div class="single-content">
								<div class="single-styk">
									<?php if ( ! empty( $brand ) ) : ?>
										<div class="p-12-12 uper m-12-12"><?php echo esc_html( $brand ); ?></div>
									<?php endif; ?>
									<h1 class="p-24-24 h-single"><?php the_title(); ?></h1>
									<?php if ( ! empty( $colors ) ) : ?>
										<div class="horiz">
											<?php
											foreach ( $colors as $color ) :
												if ( ! empty( $color['hex'] ) && ! empty( $color['name'] ) ) :
													?>
													<a href="#" class="btn-color w-inline-block">
														<div class="color-mom">
															<div class="color-dot" style="background-color: <?php echo esc_attr( $color['hex'] ); ?>;">
																<div class="color-dot_in"></div>
															</div>
														</div>
														<div class="p-12-12 uper m-12-12"><?php echo esc_html( $color['name'] ); ?></div>
													</a>
													<?php
												endif;
											endforeach;
											?>
										</div>
									<?php endif; ?>
									<div class="horiz">
										<?php if ( ! empty( $price ) ) : ?>
											<div class="p-24-24"><?php echo esc_html( loveforever_format_price( $price ) ); ?> ₽</div>
										<?php endif; ?>
										<?php if ( ! empty( $price_with_discount ) ) : ?>
											<div class="p-24-24 indirim-p-24-24"><?php echo esc_html( loveforever_format_price( $price_with_discount ) ); ?> ₽</div>
											<?php
											$discount = round( ( $price / $price_with_discount * 100 ) - 100 );
											?>
											<div class="indirim-single">
												<div class="p-12-12 uper m-12-12">-<?php echo esc_html( $discount ); ?>%</div>
											</div>
										<?php endif; ?>
									</div>
									<div class="p-16-20 odesc w-richtext"><?php the_content(); ?></div>
									<div class="vert form-keepre">
										<div class="p-12-12 uper m-12-12">Запись на примерку</div>
										<button class="btn in-single-btn zapis w-inline-block" data-js-dialog-open-button="fittingDialog">
											<div>Выбрать дату и время</div>
										</button>
										<div class="form-block w-form">
											<form id="email-form" name="email-form" data-name="Email Form" method="get" class="form" data-wf-page-id="67239bd83c4331a3450cc872" data-wf-element-id="919e8683-0ddb-4c9b-4c3b-9ef17e8ca1d0"><a href="#" class="select w-inline-block"></a>
												<input class="w-input" maxlength="256" name="email-2" data-name="Email 2" placeholder type="email" id="email-2" required>
												<input type="submit" data-wait="Please wait..." class="w-button" value="Submit">
											</form>
											<div class="w-form-done">
												<div>Thank you! Your submission has been received!</div>
											</div>
											<div class="w-form-fail">
												<div>Oops! Something went wrong while submitting the form.</div>
											</div>
										</div>
									</div>
									<p class="p-16-20 odescr">Примерить и купить платье можно в нашем салоне:г. Санкт-Петербург, Вознесенский проспект 18 (м. Садовая) ежедневно с 10 до 22:00 по предварительной записи<br>‍</p>
									<div class="p-16-20 n-top single-p">Для доставки в регионы <a href="#" class="btn-call-reqest">закажите обратный звонок</a></div>
									<div class="horiz m-vert">
										<?php $is_in_favorites = loveforever_has_product_in_favorites( get_the_ID() ); ?>
										<button type="button" id="singleDressPageAddToFavoriteButton" class="btn in-single-btn line w-inline-block <?php echo $is_in_favorites ? 'is-active' : ''; ?>" data-js-add-to-favorite-button="<?php the_ID(); ?>">
											<div class="svg-share lik w-embed">
												<svg width="100%" height="100%" viewbox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" clip-rule="evenodd" d="M1.28448 7.26555C-0.428161 8.95359 -0.428161 11.6904 1.28448 13.3784L8.00251 20L14.2045 13.8871L14.2022 13.8849L14.716 13.3784C16.4287 11.6904 16.4287 8.95359 14.716 7.26555C13.0034 5.5775 10.2267 5.5775 8.51402 7.26555L8.00026 7.77193L7.4865 7.26555C5.77386 5.5775 2.99713 5.5775 1.28448 7.26555Z" fill="white"></path>
													<path fill-rule="evenodd" clip-rule="evenodd" d="M11.325 0.316509C10.8969 0.73852 10.8969 1.42272 11.325 1.84473L13.0045 3.50011L14.555 1.97189L14.5545 1.97134L14.6829 1.84473C15.1111 1.42272 15.1111 0.73852 14.6829 0.316509C14.2548 -0.105503 13.5606 -0.105503 13.1324 0.316509L13.004 0.443104L12.8755 0.316509C12.4474 -0.105503 11.7532 -0.105503 11.325 0.316509Z" fill="white"></path>
													<path fill-rule="evenodd" clip-rule="evenodd" d="M17.5218 3.49713C16.8261 4.16029 16.8261 5.23546 17.5218 5.89862L20.251 8.49994L22.7706 6.09844L22.7697 6.09757L22.9784 5.89862C23.6742 5.23546 23.6742 4.16029 22.9784 3.49713C22.2826 2.83397 21.1546 2.83397 20.4588 3.49713L20.2501 3.69606L20.0414 3.49713C19.3456 2.83397 18.2176 2.83397 17.5218 3.49713Z" fill="white"></path>
												</svg>
											</div>
											<div data-js-add-to-favorite-button-text><?php echo $is_in_favorites ? 'Удалить из избранного' : 'Добавить в избранное'; ?></div>
										</button>
										<a href="#" class="btn in-single-btn _2 w-inline-block">
											<div class="svg-share lik w-embed">
												<svg width="24" height="16" viewbox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M13.0718 16C4.87196 16 0.194878 9.99399 0 0H4.10743C4.24235 7.33533 7.27046 10.4424 9.66896 11.0831V0H13.5365V6.32633C15.9051 6.05405 18.3935 3.17117 19.233 0H23.1006C22.7842 1.64466 22.1537 3.20189 21.2483 4.57429C20.3429 5.94668 19.1823 7.10472 17.8389 7.97597C19.3384 8.77206 20.6629 9.89886 21.7249 11.282C22.7869 12.6651 23.5624 14.2732 24 16H19.7427C19.3498 14.5001 18.5513 13.1575 17.4473 12.1404C16.3433 11.1233 14.9829 10.4769 13.5365 10.2823V16H13.0718Z" fill="white"></path>
												</svg>
											</div>
											<div>сохранить в vk</div>
										</a>
									</div>
									<div class="horiz shere-line">
										<div class="p-12-12 uper m-12-12">Поделиться</div>
										<div class="share-block">
											<a href="https://vk.com/share.php?url=<?php the_permalink(); ?>" class="share-btn no-barba w-inline-block" target="_blank">
												<div class="svg-share w-embed">
													<svg width="16" height="10" viewbox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M8.71455 10C3.24797 10 0.129919 6.24625 0 0H2.73829C2.82823 4.58458 4.84697 6.52653 6.44597 6.92693V0H9.02436V3.95395C10.6034 3.78378 12.2623 1.98198 12.822 0H15.4004C15.1895 1.02791 14.7691 2.00118 14.1655 2.85893C13.562 3.71668 12.7882 4.44045 11.8926 4.98498C12.8923 5.48254 13.7753 6.18678 14.4833 7.05125C15.1913 7.91571 15.7082 8.92073 16 10H13.1618C12.8999 9.06258 12.3676 8.22343 11.6316 7.58773C10.8956 6.95203 9.9886 6.54805 9.02436 6.42643V10H8.71455Z" fill="black"></path>
													</svg>
												</div>
											</a>
											<a href="https://t.me/share/url?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" class="share-btn no-barba w-inline-block" target="_blank">
												<div class="svg-share w-embed">
													<svg width="14" height="13" viewbox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M14 0.460525L11.7855 12.4588C11.7855 12.4588 11.4756 13.2907 10.6244 12.8917L5.51495 8.68138L5.49126 8.66897C6.18143 8.00295 11.5333 2.83145 11.7673 2.59703C12.1294 2.23398 11.9046 2.01785 11.4841 2.2921L3.57869 7.68757L0.528786 6.5847C0.528786 6.5847 0.0488212 6.40122 0.00264736 6.00226C-0.044134 5.60264 0.544582 5.38651 0.544582 5.38651L12.9781 0.144489C12.9781 0.144489 14 -0.338054 14 0.460525Z" fill="black"></path>
													</svg>
												</div>
											</a>
											<a href="https://api.whatsapp.com/send?text=<?php the_permalink(); ?>" class="share-btn w-inline-block" data-action="share/whatsapp/share" target="_blank">
												<div class="svg-share w-embed">
													<svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M13.6585 2.33333C12.1533 0.833333 10.1463 0 8.02787 0C3.62369 0 0.0557483 3.55556 0.0557483 7.94444C0.0557483 9.33333 0.445993 10.7222 1.11498 11.8889L0 16L4.23694 14.8889C5.40767 15.5 6.68989 15.8333 8.02787 15.8333C12.4321 15.8333 16 12.2778 16 7.88889C15.9443 5.83333 15.1638 3.83333 13.6585 2.33333ZM11.8746 10.7778C11.7073 11.2222 10.9268 11.6667 10.5366 11.7222C10.2021 11.7778 9.7561 11.7778 9.31011 11.6667C9.03136 11.5556 8.64112 11.4444 8.19512 11.2222C6.18815 10.3889 4.90592 8.38889 4.79443 8.22222C4.68293 8.11111 3.95819 7.16667 3.95819 6.16667C3.95819 5.16667 4.45993 4.72222 4.62718 4.5C4.79443 4.27778 5.01742 4.27778 5.18467 4.27778C5.29617 4.27778 5.46341 4.27778 5.57491 4.27778C5.68641 4.27778 5.85366 4.22222 6.02091 4.61111C6.18815 5 6.5784 6 6.63415 6.05556C6.68989 6.16667 6.68989 6.27778 6.63415 6.38889C6.5784 6.5 6.52265 6.61111 6.41115 6.72222C6.29965 6.83333 6.18815 7 6.1324 7.05556C6.0209 7.16667 5.90941 7.27778 6.02091 7.44444C6.1324 7.66667 6.52265 8.27778 7.13589 8.83333C7.91638 9.5 8.52962 9.72222 8.75261 9.83333C8.97561 9.94445 9.08711 9.88889 9.1986 9.77778C9.3101 9.66667 9.70035 9.22222 9.81185 9C9.92335 8.77778 10.0906 8.83333 10.2578 8.88889C10.4251 8.94444 11.4286 9.44445 11.5958 9.55556C11.8188 9.66667 11.9303 9.72222 11.9861 9.77778C12.0418 9.94444 12.0418 10.3333 11.8746 10.7778Z" fill="black"></path>
													</svg>
												</div>
											</a>
										</div>
									</div>
									<?php if ( ! empty( $tags ) ) : ?>
										<div class="cats-horiz">
											<?php foreach ( $tags as $tag ) : ?>
												<a href="#" class="btn grey-border_btn in-single w-inline-block">
													<div>#<?php echo esc_html( $tag->name ); ?></div>
												</a>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</section>
				<?php
				if ( ! empty( $related_products ) ) :
					$related_products_query_args = array(
						'post_type' => 'dress',
						'post__in'  => $related_products,
					);
					$related_products_query      = new WP_Query( $related_products_query_args );
					?>
					<section class="section">
						<div class="container">
							<div class="spleet">
								<h2 class="h-36-36">Так же выбирают</h2>
								<div class="horiz">
									<a href="#" class="splide-arrow w-inline-block">
										<div class="svg rev w-embed">
											<svg width="4" height="6" viewbox="0 0 4 6" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" clip-rule="evenodd" d="M3.41604 3.51284L0.583958 6L0 5.48716L2.83208 3L0 0.512837L0.583958 0L3.41604 2.48716L4 3L3.41604 3.51284Z" fill="black"></path>
											</svg>
										</div>
									</a>
									<a href="#" class="splide-arrow w-inline-block">
										<div class="svg w-embed">
											<svg width="4" height="6" viewbox="0 0 4 6" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" clip-rule="evenodd" d="M3.41604 3.51284L0.583958 6L0 5.48716L2.83208 3L0 0.512837L0.583958 0L3.41604 2.48716L4 3L3.41604 3.51284Z" fill="black"></path>
											</svg>
										</div>
									</a>
								</div>
							</div>
							<div class="splide y-pc">
								<div class="splide__track">
									<div class="splide__list search-grid">
										<?php
										while ( $related_products_query->have_posts() ) :
											$related_products_query->the_post();
											?>
											<div id="w-node-f2a03f55-cf72-e124-8648-e74d9b1c1778-450cc872" class="splide__slide">
												<?php get_template_part( 'components/dress-card' ); ?>
											</div>
											<?php
										endwhile;
										wp_reset_postdata();
										?>
									</div>
								</div>
							</div>
						</div>
					</section>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/home/recently-viewed-section' ); ?>
				<?php get_template_part( 'template-parts/global/map-section' ); ?>
				<div id="fittingDialog" role="dialog" class="dialog" data-js-dialog>
					<div class="dialog__overlay" data-js-dialog-overlay>
						<div class="dialog__content" data-js-dialog-content>
							<div class="dialog-card">
								<div class="dialog-card__header">
									<h3 class="dialog-card__title italic ff-tt-norms-pro" data-js-dialog-title>Запись на примерку</h3>
									<p class="dialog-card__subtitle">м. Садовая, Вознесенский пр-кт, 18</p>
									<a href="#" class="dialog-card__link menu-link active">Маршрут от метро</a>
								</div>
								<div class="dialog-card__body">
									<form id="singleDressFittingForm" class="fitting-form" data-js-fitting-form>
										<button type="button" class="fitting-form__back" data-js-fitting-form-back-button disabled>
											<svg width="9" height="16" viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" clip-rule="evenodd" d="M0.707107 7.14201L0.00075584 7.84914L0.707862 8.55621L7.77817 15.6273L8.48528 14.9202L1.41421 7.84912L8.48528 0.778053L7.77818 0.070946L0.707107 7.14201Z" fill="black"/>
											</svg>
										</button>
										<fieldset class="fitting-form__step" data-js-fitting-form-step>
											<?php
											$dress_categories = get_terms(
												array(
													'taxonomy'   => 'dress_category',
													'hide_empty' => false,
												)
											);

											if ( ! empty( $dress_categories ) && ! is_wp_error( $dress_categories ) ) :
												?>
												<fieldset class="fitting-form__group">
													<div class="fitting-form__group-header">
														<p class="fitting-form__group-heading">Какие платья желаете примерить?</p>
													</div>
													<div class="fitting-form__columns">
														<?php
														foreach ( $dress_categories as $dress_categories_item ) :
															$dress_category_name = str_replace( ' платья', '', $dress_categories_item->name );
															?>
															<label class="radio">
																<input 
																	class="radio__input" 
																	type="radio" 
																	name="<?php echo esc_attr( $dress_categories_item->taxonomy ); ?>" 
																	value="<?php echo esc_attr( $dress_categories_item->slug ); ?>"
																	<?php echo ( ! empty( $dress_category ) && ! is_wp_error( $dress_category ) && $dress_category[0]->slug === $dress_categories_item->slug ) ? 'checked' : ''; ?>
																>
																<span class="radio__label"><?php echo esc_html( $dress_category_name ); ?></span>
															</label>
														<?php endforeach; ?>
													</div>
												</fieldset>
											<?php endif; ?>
											<fieldset class="fitting-form__group">
												<div class="fitting-form__group-header">
													<p class="fitting-form__group-heading">Выберите день и время</p>
													<div class="fitting-form__actions">
														<button type="button" class="fitting-form__actions-button fitting-form__actions-button--prev" disabled data-js-fitting-form-prev-slots-button>
															<svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path fill-rule="evenodd" clip-rule="evenodd" d="M5.24977 4.28598L0.74993 0L0 0.714289L4.49984 5.00027L0.000560648 9.28571L0.750491 10L6 4.99998L5.25007 4.28569L5.24977 4.28598Z" fill="black"/>
															</svg>
														</button>
														<button type="button" class="fitting-form__actions-button fitting-form__actions-button--next" data-js-fitting-form-next-slots-button>
															<svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path fill-rule="evenodd" clip-rule="evenodd" d="M5.24977 4.28598L0.74993 0L0 0.714289L4.49984 5.00027L0.000560648 9.28571L0.750491 10L6 4.99998L5.25007 4.28569L5.24977 4.28598Z" fill="black"/>
															</svg>
														</button>
													</div>
												</div>
												<?php
												$current_date = gmdate( 'd.m.Y', current_time( 'timestamp' ) );
												$end_date     = gmdate( 'd.m.Y', strtotime( '+2 days', current_time( 'timestamp' ) ) );
												$slots_range  = Fitting_Slots::get_slots_range( $current_date, $end_date );
												?>
												<div class="fitting-form__columns" data-js-fitting-form-slots-container>
													<?php
													/*
													foreach ( $slots_range as $slots_range_date => $slots ) : ?>
														<div class="fitting-form__day-column">
															<div class="fitting-form__day-column-head">
																<label class="fitting-form__day-input radio">
																	<!-- <input class="radio__input" type="radio" name="date" id="" value="01.02"> -->
																	<span class="radio__label"><?php echo esc_html( gmdate( 'd.m (D)', strtotime( $slots_range_date ) ) ); ?></span>
																</label>
															</div>
															<ol class="fitting-form__day-column-list">
																<?php foreach ( $slots as $time => $slot ) : ?>
																<li class="fitting-form__day-column-list-item">
																	<label class="radio">
																		<input
																			class="radio__input"
																			type="radio"
																			name="time"
																			id=""
																			value="<?php echo esc_attr( $time ); ?>"
																			<?php echo 0 === $slot['available'] ? 'disabled' : ''; ?>
																			data-js-fitting-form-date-value="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $slots_range_date ) ) ); ?>"
																		>
																		<span class="radio__label"><?php echo esc_html( $time ); ?></span>
																	</label>
																</li>
																<?php endforeach; ?>
															</ol>
														</div>
													<?php endforeach; */
													?>
												</div>
												<input type="hidden" name="date" value="<?php echo esc_attr( gmdate( 'd.m' ) ); ?>" data-js-fitting-form-date-control>
											</fieldset>
										</fieldset>
										<fieldset class="fitting-form__step" data-js-fitting-form-step hidden>
											<fieldset class="fitting-form__group">
												<div class="fitting-form__group-header">
													<p class="fitting-form__group-heading">2 октября в среду в 14:00</p>
												</div>
												<div class="fitting-form__group-body">
													<div class="field">
														<input type="text" class="field__control" name="name" placeholder="Имя" id="fittingFormNameField">
													</div>
													<div class="field">
														<input 
															id="singleDressFittingFormPhoneField"
															type="text" 
															class="field__control" 
															name="phone" 
															placeholder="Телефон" 
															id="fittingFormPhoneField" 
															data-js-input-mask="+{7} (000) 000-00-00">
													</div>
													<input type="hidden" name="target_dress" value="<?php echo esc_attr( get_the_ID() ); ?>">
													<?php if ( ! empty( $_COOKIE['favorites'] ) ) : ?>
														<input type="hidden" name="client_favorite_dresses" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_COOKIE['favorites'] ) ) ); ?>">
													<?php endif; ?>
													<button type="submit" class="button" data-js-fitting-form-submit-button>Записаться</button>
												</div>
												<div class="fitting-form__group-footer">
													<p>Нажимая записаться вы соглашаетесь с <a href="#">политикой конфиденциальности</a></p>
												</div>
											</fieldset>
										</fieldset>
										<div class="fitting-form__errors" data-js-fitting-form-errors hidden></div>
										<?php wp_nonce_field( 'submit_fitting_form', 'submit_fitting_form_nonce' ); ?>
									</form>
									<button type="dialog-card__body-button button" class="button" disabled hidden data-js-dialog-close-button>Хорошо</button>
								</div>
							</div>
						</div>
					</div>
					<button type="button" class="dialog__close" data-js-dialog-close-button>
						<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
							<mask id="mask0_451_2489" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="18" height="18">
								<rect width="18" height="18" fill="#D9D9D9"/>
							</mask>
							<g mask="url(#mask0_451_2489)">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M8.84924 8.14201L1.77818 1.07095L1.07107 1.77805L8.14214 8.84912L1.07107 15.9202L1.77817 16.6273L8.84924 9.55623L15.9203 16.6273L16.6274 15.9202L9.55635 8.84912L16.6274 1.77805L15.9203 1.07095L8.84924 8.14201Z" fill="black"/>
							</g>
						</svg>
					</button>
				</div>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
<?php get_footer(); ?>
