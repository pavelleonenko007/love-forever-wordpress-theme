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

	$infoline_id   = loveforever_get_current_infoline();
	$infoline_data = loveforever_get_infoline_data( $infoline_id );

	$availability        = get_field( 'availability' );
	$badge               = loveforever_get_product_badge_text( get_the_ID() );
	$images              = loveforever_get_product_images( get_the_ID() );
	$video               = get_field( 'video' );
	$colors              = get_the_terms( get_the_ID(), 'color' );
	$price               = get_field( 'price' );
	$has_discount        = get_field( 'has_discount' );
	$price_with_discount = $has_discount ? get_field( 'price_with_discount' ) : null;
	$brand               = get_the_terms( get_the_ID(), 'brand' );
	$tags                = wp_get_post_terms( get_the_ID(), array( 'brand', 'silhouette', 'style', 'color' ) );
	$dress_category      = loveforever_get_product_root_category( get_the_ID() );
	$related_products    = get_field( 'related_products' );

	$has_change_fittings_capabilities  = loveforever_is_user_has_manager_capability();
	$booking_manager                   = Fitting_Slots_Manager::get_instance();
	$date_with_nearest_available_slots = $booking_manager->get_nearest_available_date();
	?>
<section class="section">
	<div class="container container-fw n-top">
		<?php get_template_part( 'components/marquee', null, $infoline_data ); ?>
		<?php get_template_part( 'components/navbar' ); ?>
	</div>
	<div class="container n-top single-product">
		<div class="container container-fw n-top">
			<div class="page-top single-p"></div>
		</div>
		<div class="spleet m-h-vert">
			<div class="code-embed-2 w-embed visible-mobile-s">
				<nav aria-label="Breadcrumb" class="breadcrumb">
					<?php
					ob_start();
					get_template_part( 'components/breadcrumb', null, array( 'extra_classes' => array( 'breadcrumbs--single-dress' ) ) );
					$breadcrumb_html = ob_get_clean();
					$breadcrumb_html = loveforever_remove_attributes_from_html( $breadcrumb_html, array( 'itemprop', 'itemtype', 'itemscope' ) );

					echo $breadcrumb_html;
					?>
				</nav>
			</div>
			<?php
			if ( ! empty( $images ) ) :
				$slider_config = array(
					'type'        => 'loop',
					'perPage'     => 1,
					'perMove'     => 1,
					'speed'       => 500,
					'arrows'      => false,
					'pagination'  => true,
					'easing'      => 'ease',
					'classes'     => array(
						'pagination' => 'lf-single-slider__pagination splide__pagination',
						'page'       => 'lf-single-slider__page splide__pagination__page',
					),
					'mediaQuery'  => 'min',
					'breakpoints' => array(
						768 => array(
							'destroy' => true,
						),
					),
				);
				?>
			<div 
				class="splide lf-single-slider visible-mobile" 
				role="group" 
				aria-hidden="true" 
				data-splide="<?php echo esc_attr( wp_json_encode( $slider_config ) ); ?>"
				data-js-product-slider
			>
				<div class="splide__track lf-single-slider__track">
					<ul class="splide__list lf-single-slider__list">
						<?php foreach ( $images as $image_slide_index => $image_slide ) : ?>
						<li class="splide__slide lf-single-slider__slide">
							<?php
							echo wp_get_attachment_image(
								$image_slide,
								'fullhd',
								false,
								array(
									'class' => 'lf-single-slider__image',
								)
							);
							?>
						</li>
							<?php if ( 0 === $image_slide_index && ! empty( $video ) ) : ?>
						<li class="splide__slide lf-single-slider__slide">
							<video 
								class="lf-single-slider__video" 
								preload="metadata"
								muted 
								playsinline 
								loop 
								autoplay
								data-js-play-if-visible-video
							>
								<source src="<?php echo esc_url( $video['url'] ); ?>" type="<?php echo esc_attr( loveforever_get_video_mime_type( $video ) ); ?>">
							</video>
						</li>
						<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php
				$is_in_favorites = loveforever_has_product_in_favorites( get_the_ID() );
				$aria_label      = $is_in_favorites ? 'Удалить из избранного товар ' . get_the_title() : 'Добавить в избранное товар ' . get_the_title();
				?>
				<button 
					type="button" class="btn-like lf-like-button w-inline-block <?php echo $is_in_favorites ? 'is-active' : ''; ?>" 
					data-js-add-to-favorite-button="<?php the_ID(); ?>"
					aria-label="<?php echo esc_attr( $aria_label ); ?>"
					title="<?php echo esc_attr( $aria_label ); ?>"
					data-product-name="<?php echo esc_attr( get_the_title() ); ?>"
				>
					<svg class="lf-like-button__icon" xmlns="http://www.w3.org/2000/svg">
						<use href="#heartIcon"></use>
					</svg>
				</button>
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
						<?php
						echo wp_get_attachment_image(
							$image_item,
							'full',
							false,
							array(
								'class' => 'img-fw',
								'style' => 'height: auto;',
							)
						);
						?>
					</div>
						<?php if ( 0 === $image_item_index && ! empty( $video ) ) : ?>
					<div class="single-img-mom">
						<video 
							class="img-fw"
							muted
							playsinline
							loop
							autoplay
							preload="metadata"
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
						<?php if ( ! empty( $badge ) ) : ?>
						<div class="_2px_cube" style="margin: 0; width: 2rem; height: 2rem;"></div>
						<div class="p-12-12 uper m-12-12"><?php echo esc_html( $badge ); ?></div>
						<?php endif; ?>
					</div>
					<div class="single-product__info">
						<?php if ( ! is_wp_error( $brand ) && ! empty( $brand ) ) : ?>
							<a href="<?php echo esc_url( loveforever_format_filter_link_for_tag( $brand[0], get_the_ID() ) ); ?>" class="p-12-12 uper m-12-12 single-product__content-brand"><?php echo esc_html( $brand[0]->name ); ?></a>
						<?php endif; ?>
						<h1 class="p-24-24 h-single single-product__content-heading"><?php echo wp_kses_post( loveforever_get_product_title( get_the_ID() ) ); ?></h1>
					</div>
					<?php
					$is_wedding_dress = 'wedding' === $dress_category->slug;
					if ( ! is_wp_error( $colors ) && ! empty( $colors ) && ( ! $is_wedding_dress || 1 < count( $colors ) ) ) :
						$colors = array_map(
							function ( $color ) {
								return array(
									'hex'  => get_field( 'color', $color ),
									'name' => $color->name,
								);
							},
							$colors
						);
						?>
					<div class="single-product__colors lf-product-colors">
						<?php
						foreach ( $colors as $color ) :
							if ( ! empty( $color['hex'] ) && ! empty( $color['name'] ) ) :
								?>
								<div class="lf-product-color">
									<div class="lf-product-color__circle" style="background-color: <?php echo esc_attr( $color['hex'] ); ?>;"></div>
									<div class="lf-product-color__name"><?php echo esc_html( $color['name'] ); ?></div>
								</div>
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
											$min_date          = wp_date( 'd F (D)', strtotime( $date_with_nearest_available_slots ) );
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
										$available_slots = $booking_manager->get_available_slots( $date_with_nearest_available_slots, $dress_category->slug );
										$available_slots = array_filter(
											$available_slots,
											function ( $slot ) {
												return $slot['available_for_booking'] > 0;
											}
										);
										?>
									<div class="single-dress-form__field-wrapper field ui-front">
										<?php
											$select_config = array(
												'type' => 'time',
												'hasBorder' => true,
											);
											?>
										<select 
											class="field__control"
											name="time" 
											id="singleDressFormTimeField" data-js-custom-select="<?php echo esc_attr( wp_json_encode( $select_config ) ); ?>">
											<?php foreach ( $available_slots as $time => $slot_data ) : ?>
											<option value="<?php echo esc_attr( $time ); ?>"><?php echo esc_html( $time ); ?></option>
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
								if ( ! empty( $dress_category ) ) :
									?>
								<input type="hidden" name="fitting_type" value="<?php echo esc_attr( $dress_category->slug ); ?>">		
								<?php endif; ?>							
							</fieldset>
						</form>
					</div>
					<div class="w-richtext single-product__content-fitting-text flow">
						<p class="p-16-20">Примерить и купить платье можно в нашем салоне: г. Санкт-Петербург, Вознесенский проспект 18 (м. Садовая) ежедневно с 10 до 22:00 по предварительной записи</p>
						<p class="p-16-20">Для доставки в регионы <a href="#" class="btn-call-reqest" data-js-dialog-open-button="globalCallbackDialog">закажите обратный звонок</a></p>
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
						<div class="button button--vk" data-js-add-to-vk-button>
							<div style="position: absolute; top: 50%; left: 50%; translate: -50% -50%; scale: 5; opacity: 0.01;">
								<div id="vk_bookmarks"></div>
								<script type="text/javascript">
									VK.Widgets.Bookmarks("vk_bookmarks");
								</script>
							</div>
							<svg class="button__icon" viewBox="0 0 24 24">
								<use href="#vkButton"></use>
							</svg>
							<span class="button__text">Сохранить в vk</span>
						</div>
					</div>
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
					<?php $share_buttons = loveforever_get_share_buttons( get_the_permalink() ); ?>
					<div class="horiz shere-line single-product__content-socials">
						<div class="p-12-12 uper m-12-12">Поделиться</div>
						<div class="lf-share-buttons">
							<?php foreach ( $share_buttons as $share_button ) : ?>
							<a class="lf-share-button lf-share-button--dark" href="<?php echo esc_url( $share_button['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<svg class="lf-share-button__icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<use href="#<?php echo esc_attr( $share_button['icon'] ); ?>"></use>
								</svg>
							</a>
							<?php endforeach; ?>
						</div>
					</div>
					<?php if ( ! empty( $tags ) ) : ?>
					<div class="lf-tags">
						<?php foreach ( $tags as $tag ) : ?>
						<a href="<?php echo esc_url( loveforever_format_filter_link_for_tag( $tag, get_the_ID() ) ); ?>" class="lf-tag">
							<div class="lf-tag__text"><?php echo esc_html( $tag->name ); ?></div>
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
<?php get_template_part( 'template-parts/home/recently-viewed-section', null, array( 'view_all_link' => true ) ); ?>
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
					<p class="fitting-form__group-heading" data-js-fitting-form-selected-date></p>
					<div class="fitting-form" data-js-fitting-form-wrapper="singleDressForm">
						<fieldset class="fitting-form__step" form="singleDressForm">
							<fieldset class="fitting-form__group" form="singleDressForm">
								<div class="fitting-form__group-body">
									<div class="field">
										<label for="singleDressFormNameField" class="field__label sr-only">Имя</label>
										<input 
											type="text" 
											class="field__control" 
											name="name" 
											placeholder="Имя" 
											id="singleDressFormNameField"
											form="singleDressForm"
											autocomplete="name"
											title="Укажите ваше имя"
										/>
									</div>
									<div class="field">
										<label for="singleDressFormPhoneField" class="field__label sr-only">Телефон</label>
										<input 
											id="singleDressFormPhoneField"
											type="text" 
											class="field__control" 
											name="phone" 
											placeholder="Телефон" 
											id="singleDressFormPhoneField" 
											data-js-input-mask="phone"
											form="singleDressForm"
											autocomplete="tel"
											inputmode="tel"
											required
											title="Укажите ваш телефон"
										/>
									</div>
									<input 
										type="hidden" 
										name="target_dress" 
										value="<?php echo esc_attr( get_the_ID() ); ?>"
										form="singleDressForm"
									/>
									<?php /** if ( ! $has_change_fittings_capabilities && ! empty( $_COOKIE['favorites'] ) ) : */ ?>
									<input 
										type="hidden" 
										name="client_favorite_dresses" 
										value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_COOKIE['favorites'] ?? '' ) ) ); ?>"
										form="singleDressForm"
									/>
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
