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
$images              = loveforever_get_product_images( get_the_ID() );
$video               = get_field( 'video' );
$colors              = get_field( 'colors' );
$price               = get_field( 'price' );
$has_discount        = get_field( 'has_discount' );
$price_with_discount = $has_discount ? get_field( 'price_with_discount' ) : null;
$brand               = ! empty( get_the_terms( get_the_ID(), 'brand' ) ) && ! is_wp_error( get_the_terms( get_the_ID(), 'brand' ) ) ? get_the_terms( get_the_ID(), 'brand' )[0]->name : null;
$tags                = wp_get_post_terms( get_the_ID(), array( 'brand', 'silhouette', 'style', 'color' ) );
$dress_category      = get_the_terms( get_the_ID(), 'dress_category' );
$related_products    = get_field( 'related_products' );

$has_change_fittings_capabilities = loveforever_is_user_has_manager_capability();

$date_with_nearest_available_slots = Fitting_Slots::get_nearest_available_date();
?>
				<section class="section">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee' ); ?>
						<?php get_template_part( 'components/navbar' ); ?>
					</div>
					<div class="container n-top single-product">
						<div class="container container-fw n-top">
							<div class="page-top single-p"></div>
						</div>
						<div class="spleet m-h-vert">
							<div class="code-embed-2 w-embed visible-mobile-s">
								<nav aria-label="Breadcrumb" class="breadcrumb">
									<?php get_template_part( 'components/breadcrumb', null, array( 'extra_classes' => array( 'breadcrumbs--single-dress' ) ) ); ?>
								</nav>
							</div>
						<?php
						if ( ! empty( $images ) ) :
							?>
							<div data-delay="4000" data-animation="slide" class="m-prod-slider lf-single-slider w-slider" data-autoplay="false" data-easing="ease" data-hide-arrows="false" data-disable-swipe="false" data-autoplay-limit="0" data-nav-spacing="3" data-duration="500" data-infinite="true">
								<div class="m-prod-slider_mask lf-single-slider__mask w-slider-mask">
									<?php foreach ( $images as $image_slide_index => $image_slide ) : ?>
										<div class="lf-single-slider__slide w-slide">
											<div class="mom-abs">
												<?php if ( ! empty( $image_slide['image'] ) ) : ?>
													<img src="<?php echo esc_url( $image_slide['image']['url'] ); ?>" loading="lazy" alt="<?php echo esc_attr( $image_slide['image']['alt'] ); ?>" class="img-cover">
												<?php endif; ?>
											</div>
										</div>
										<?php if ( 0 === $image_slide_index && ! empty( $video ) ) : ?>
											<div class="w-slide">
												<div class="mom-abs">
													<video 
														class="img-cover"
														muted
														playsinline
														loop
														autoplay
														data-js-play-if-visible-video
													>
														<source src="<?php echo esc_url( $video['url'] ); ?>" type="<?php echo esc_attr( loveforever_get_video_mime_type( $video ) ); ?>">
													</video>
												</div>
											</div>
										<?php endif; ?>
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
						</div>
						<div class="single-prod-grid single-product__columns">
							<div class="single-images single-product__left">
								<nav aria-label="Breadcrumb" class="breadcrumb">
									<?php get_template_part( 'components/breadcrumb', null, array( 'extra_classes' => array( 'breadcrumbs--single-dress' ) ) ); ?>
								</nav>
								<?php if ( ! empty( $images ) ) : ?>
									<div class="single-product__images">
										<?php foreach ( $images as $image_item_index => $image_item ) : ?>
											<div class="single-img-mom">
												<img src="<?php echo esc_url( $image_item['image']['url'] ); ?>" loading="lazy" alt="<?php echo esc_attr( $image_item['image']['alt'] ); ?>" class="img-fw">
											</div>
											<?php if ( 0 === $image_item_index && ! empty( $video ) ) : ?>
												<div class="single-img-mom">
													<video 
														class="img-fw"
														muted
														playsinline
														loop
														autoplay
														data-js-play-if-visible-video
													>
														<source src="<?php echo esc_url( $video['url'] ); ?>" type="<?php echo esc_attr( loveforever_get_video_mime_type( $video ) ); ?>">
													</video>
												</div>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
							<div class="single-content single-product__right">
								<div class="single-styk single-product__content">
									<div class="single-right-block single-product__content-badges">
										<div class="p-12-12 single-right-highl m-12-12"><?php echo ! empty( $availability ) ? 'Есть в наличии' : 'Нет в наличии'; ?></div>
										<?php if ( ! empty( $is_new ) ) : ?>
											<div class="_2px_cube"></div>
											<div class="p-12-12 uper m-12-12">Новинка</div>
										<?php endif; ?>
									</div>
									<div class="single-product__info">
										<?php if ( ! empty( $brand ) ) : ?>
											<div class="p-12-12 uper m-12-12 single-product__content-brand"><?php echo esc_html( $brand ); ?></div>
										<?php endif; ?>
										<h1 class="p-24-24 h-single single-product__content-heading"><?php echo wp_kses_post( loveforever_get_product_title( get_the_ID() ) ); ?></h1>
									</div>
									<?php if ( ! empty( $colors ) ) : ?>
										<div class="horiz single-product__content-colors">
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
									<?php if ( ! empty( $price ) ) : ?>
									<div class="horiz single-product__content-prices">
										<?php $first_price = loveforever_format_price( ! empty( $price_with_discount ) ? $price_with_discount : $price, 0 ); ?>
										<div class="p-24-24"><?php echo esc_html( $first_price ); ?></div>
										<?php if ( ! empty( $price_with_discount ) ) : ?>
											<div class="p-24-24 indirim-p-24-24"><?php echo esc_html( loveforever_format_price( $price, 0 ) ); ?></div>
											<?php
											$discount = loveforever_get_product_discount( get_the_ID() );
											?>
											<div class="indirim-single">
												<div class="p-12-12 uper m-12-12">-<?php echo esc_html( $discount ); ?>%</div>
											</div>
										<?php endif; ?>
									</div>
									<?php endif; ?>
									<?php if ( ! empty( get_the_content() ) ) : ?>
										<div class="p-16-20 odesc w-richtext single-product__content-text flow"><?php the_content(); ?></div>
									<?php endif; ?>
									<div class="vert form-keepre single-product__content-form">
										<form id="singleDressForm" class="single-dress-form" data-js-fitting-form>
											<fieldset class="single-dress-form__fieldset">
												<legend class="single-dress-form__legend">Запись на примерку</legend>
												<div class="single-dress-form__inner">
													<div class="field" data-js-datepicker>
														<input 
															type="date" 
															name="date" 
															value="<?php echo esc_attr( $date_with_nearest_available_slots ); ?>"
															min="<?php echo esc_attr( $date_with_nearest_available_slots ); ?>"
															id="singleDressFormDateField" 
															class="field__control"
															data-js-datepicker-original-control
														>
														<?php
														$min_date          = wp_date( 'd.m.Y', strtotime( $date_with_nearest_available_slots ) );
														$datepicker_config = array(
															'minDate' => $min_date,
														);
														?>
														<input 
															type="text" 
															name="altdate" 
															id="singleDressFormCustomDateField" 
															class="field__control"
															value="<?php echo esc_attr( $min_date ); ?>"
															data-js-datepicker-custom-control
															data-js-datepicker-config="<?php echo esc_attr( wp_json_encode( $datepicker_config ) ); ?>"
														/>
													</div>
													<?php
													$available_slots = Fitting_Slots::get_day_slots( $date_with_nearest_available_slots, current_time( 'timestamp' ) );
													// $available_slots = array_filter(
													// $slots,
													// function ( $slot ) {
													// return $slot['available'] > 0;
													// }
													// );
													?>
													<div class="single-dress-form__field-wrapper field ui-front">
														<?php
														$select_config = array(
															'type' => 'time',
														);
														?>
														<select 
															class="field__control"
															name="time" 
															id="singleDressFormTimeField" data-js-custom-select="<?php echo esc_attr( wp_json_encode( $select_config ) ); ?>">
															<?php foreach ( $available_slots as $time => $slot_data ) : ?>
																<option 
																	value="<?php echo esc_attr( $time ); ?>" 
																	<?php echo 0 === $slot_data['available'] ? 'disabled' : ''; ?>
																>
																	<?php echo esc_html( $time ); ?>
																</option>
															<?php endforeach; ?>
														</select>
													</div>
													<button 
														type="button" 
														class="single-dress-form__button button" data-js-fitting-form-dialog-button data-js-dialog-open-button="singleProductFittingDialog"
													>
														Записаться
													</button>
												</div>
												<?php
												$dress_category = loveforever_get_product_root_category( get_the_ID() );
												if ( ! empty( $dress_category ) ) :
													?>
													<input type="hidden" name="fitting_type" value="<?php echo esc_attr( $dress_category->slug ); ?>">		
												<?php endif; ?>							
											</fieldset>
										</form>
									</div>
									<div class="w-richtext single-product__content-fitting-text flow">
										<p class="p-16-20">Примерить и купить платье можно в нашем салоне: г. Санкт-Петербург, Вознесенский проспект 18 (м. Садовая) ежедневно с 10 до 22:00 по предварительной записи</p>
										<p class="p-16-20">Для доставки в регионы <a href="#" class="btn-call-reqest">закажите обратный звонок</a></p>
									</div>
									<div class="single-product__actions">
										<?php $is_in_favorites = loveforever_has_product_in_favorites( get_the_ID() ); ?>
										<button 
											type="button"
											id="singleDressPageAddToFavoriteButton" 
											class="button button--favorite<?php echo $is_in_favorites ? ' is-active' : ''; ?>"
											data-js-add-to-favorite-button="<?php the_ID(); ?>"
										>
											<svg class="button__icon" viewBox="0 0 24 24">
												<use href="#addToFavoritesIcon"></use>
											</svg>
											<span 
												class="button__text"
												data-js-add-to-favorite-button-text
											><?php echo $is_in_favorites ? 'Удалить из избранного' : 'Добавить в избранное'; ?></span>
										</button>
										<div class="button button--vk">
											<div id="vk_bookmarks" style="position: absolute; top: 50%; left: 50%; translate: -50% -50%; scale: 1.6; opacity: 0.001;"></div>
											<script type="text/javascript">
												VK.Widgets.Bookmarks("vk_bookmarks", {
													height: '50rem'
												});
											</script>
											<svg class="button__icon" viewBox="0 0 24 24">
												<use href="#vkButton"></use>
											</svg>
											<span class="button__text">Сохранить в vk</span>
										</div>
									</div>
									<!-- <div class="horiz m-vert single-product__content-actions">
										<?php $is_in_favorites = loveforever_has_product_in_favorites( get_the_ID() ); ?>
										<button type="button" id="singleDressPageAddToFavoriteButton" class="btn in-single-btn line w-inline-block <?php echo $is_in_favorites ? 'is-active' : ''; ?>" data-js-add-to-favorite-button="<?php the_ID(); ?>">
											<div class="svg-share lik w-embed">
												<svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<use href="#addToFavoritesIcon"></use>
												</svg>
											</div>
											<div data-js-add-to-favorite-button-text><?php echo $is_in_favorites ? 'Удалить из избранного' : 'Добавить в избранное'; ?></div>
										</button>
										<div class="vk-widget" style="overflow: hidden">
											<a href="#" class="btn in-single-btn _2 w-inline-block" onclick="document.getElementById('vk_bookmarks').click();">
												<div class="svg-share lik w-embed">
													<svg width="24" height="16" viewbox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M13.0718 16C4.87196 16 0.194878 9.99399 0 0H4.10743C4.24235 7.33533 7.27046 10.4424 9.66896 11.0831V0H13.5365V6.32633C15.9051 6.05405 18.3935 3.17117 19.233 0H23.1006C22.7842 1.64466 22.1537 3.20189 21.2483 4.57429C20.3429 5.94668 19.1823 7.10472 17.8389 7.97597C19.3384 8.77206 20.6629 9.89886 21.7249 11.282C22.7869 12.6651 23.5624 14.2732 24 16H19.7427C19.3498 14.5001 18.5513 13.1575 17.4473 12.1404C16.3433 11.1233 14.9829 10.4769 13.5365 10.2823V16H13.0718Z" fill="white"></path>
													</svg>
												</div>
												<div>сохранить в vk</div>
											</a>
										</div>
									</div> -->
									<svg style="display: none">
										<symbol id="addToFavoritesIcon" viewBox="0 0 24 24">
											<path fill-rule="evenodd" clip-rule="evenodd" d="M1.3118 8.62865C-0.437267 10.4011 -0.437267 13.2747 1.3118 15.0472L8.1727 21.9998L14.5066 15.5813L14.5043 15.579L15.029 15.0472C16.7781 13.2747 16.7781 10.4011 15.029 8.62865C13.2799 6.8562 10.4442 6.8562 8.69509 8.62865L8.1704 9.16036L7.64572 8.62865C5.89665 6.8562 3.06087 6.8562 1.3118 8.62865Z" fill="white"/>
											<path fill-rule="evenodd" clip-rule="evenodd" d="M11.5662 1.33233C11.129 1.77545 11.129 2.49386 11.5662 2.93697L13.2815 4.67513L14.8649 3.07049L14.8643 3.06991L14.9955 2.93697C15.4328 2.49386 15.4328 1.77545 14.9955 1.33233C14.5583 0.889222 13.8493 0.889222 13.4121 1.33233L13.2809 1.46526L13.1497 1.33233C12.7124 0.889222 12.0035 0.889222 11.5662 1.33233Z" fill="white"/>
											<path fill-rule="evenodd" clip-rule="evenodd" d="M17.8942 4.67214C17.1837 5.36846 17.1837 6.49739 17.8942 7.19371L20.6815 9.9251L23.2546 7.40353L23.2537 7.40262L23.4669 7.19371C24.1774 6.49739 24.1774 5.36846 23.4669 4.67214C22.7563 3.97582 21.6043 3.97582 20.8937 4.67214L20.6806 4.88103L20.4674 4.67214C19.7568 3.97582 18.6048 3.97582 17.8942 4.67214Z" fill="white"/>
										</symbol>
										<symbol id="vkButton" viewBox="0 0 24 24">
											<path d="M13.0718 20C4.87196 20 0.194878 13.994 0 4H4.10743C4.24235 11.3353 7.27046 14.4424 9.66896 15.0831V4H13.5365V10.3263C15.9051 10.0541 18.3935 7.17117 19.233 4H23.1006C22.7842 5.64466 22.1537 7.20189 21.2483 8.57429C20.3429 9.94668 19.1823 11.1047 17.8389 11.976C19.3384 12.7721 20.6629 13.8989 21.7249 15.282C22.7869 16.6651 23.5624 18.2732 24 20H19.7427C19.3498 18.5001 18.5513 17.1575 17.4473 16.1404C16.3433 15.1232 14.9829 14.4769 13.5365 14.2823V20H13.0718Z" fill="white"/>
										</symbol>
									</svg>
									<div class="horiz shere-line single-product__content-socials">
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
										<div class="cats-horiz single-product__content-tags">
											<?php
											foreach ( $tags as $tag ) :
												?>
												<a href="<?php echo esc_url( loveforever_format_filter_link_for_tag( $tag, get_the_ID() ) ); ?>" class="btn grey-border_btn in-single w-inline-block">
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
				<div id="singleProductFittingDialog" role="dialog" class="dialog" data-js-dialog>
					<div class="dialog__overlay" data-js-dialog-overlay>
						<div class="dialog__content" data-js-dialog-content>
							<div class="dialog-card">
								<div class="dialog-card__header">
									<h3 class="dialog-card__title italic ff-tt-norms-pro" data-js-dialog-title>Запись на примерку</h3>
									<?php if ( ! empty( ADDRESS ) ) : ?>
										<p class="dialog-card__subtitle"><?php echo esc_html( ADDRESS ); ?></p>
									<?php endif; ?>
									<?php if ( ! empty( MAP_LINK ) ) : ?>
										<a href="<?php echo esc_url( MAP_LINK['url'] ); ?>" class="dialog-card__link menu-link active">Маршрут от метро</a>
									<?php endif; ?>
								</div>
								<div class="dialog-card__body">
									<div class="fitting-form" data-js-fitting-form-wrapper="singleDressForm">
										<fieldset class="fitting-form__step" form="singleDressForm">
											<fieldset class="fitting-form__group" form="singleDressForm">
												<div class="fitting-form__group-header">
													<p class="fitting-form__group-heading" data-js-fitting-form-selected-date></p>
												</div>
												<div class="fitting-form__group-body">
													<div class="field">
														<input 
															type="text" 
															class="field__control" 
															name="name" 
															placeholder="Имя" 
															id="singleDressFormNameField"
															form="singleDressForm"
														>
													</div>
													<div class="field">
														<input 
															id="singleDressFittingFormPhoneField"
															type="text" 
															class="field__control" 
															name="phone" 
															placeholder="Телефон" 
															id="singleDressFormPhoneField" 
															data-js-input-mask="+{7} (000) 000-00-00"
															form="singleDressForm"
														>
													</div>
													<input 
														type="hidden" 
														name="target_dress" 
														value="<?php echo esc_attr( get_the_ID() ); ?>"
														form="singleDressForm"
													>
													<?php /** if ( ! $has_change_fittings_capabilities && ! empty( $_COOKIE['favorites'] ) ) : */ ?>
														<input 
															type="hidden" 
															name="client_favorite_dresses" 
															value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_COOKIE['favorites'] ?? '' ) ) ); ?>"
															form="singleDressForm"
														>
													<?php // endif; ?>
													<button type="submit" form="singleDressForm" class="button" data-js-fitting-form-submit-button>Записаться</button>
												</div>
												<div class="fitting-form__group-footer">
													<p>Нажимая записаться вы соглашаетесь с <a class="menu-link" href="<?php echo esc_url( PRIVACY_POLICY_LINK ); ?>">политикой конфиденциальности</a></p>
												</div>
											</fieldset>
										</fieldset>
										<div class="fitting-form__errors" data-js-fitting-form-errors hidden></div>
										<input type="hidden" name="submit_fitting_form_nonce" value="<?php echo esc_attr( wp_create_nonce( 'submit_fitting_form' ) ); ?>" form="singleDressForm">
									</div>
									<button type="button" class="dialog-card__body-button button" disabled hidden data-js-dialog-close-button>Хорошо</button>
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
