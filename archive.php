<?php
/**
 * Template Name: Archive
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$catalog_filter_form_id = 'catalogFilterForm';

get_header(
	null,
	array(
		'data-wf-page'                  => '672202863105b8a4d315ac0c',
		'barba-container-extra-classes' => array(
			'catalog-page',
		),
		'namespace'                     => 'catalog',
	)
);

$infoline_id   = loveforever_get_current_infoline();
$infoline_data = loveforever_get_infoline_data( $infoline_id );

$queried_object           = get_queried_object();
$main_catalog_categories  = array(
	'wedding',
	'evening',
	'prom',
);
$is_main_catalog_category = in_array( $queried_object->slug, $main_catalog_categories, true );

$stories = array();

$thumbnail = get_field( 'thumbnail', $queried_object );

$hero_section_classes = array( 'section', 'section_100vh', 'hero-section' );

if ( empty( $thumbnail ) ) {
	$hero_section_classes[] = 'hero-section--no-image';
}

$seo_text              = get_field( 'seo_text', $queried_object );
$seo_text_display_type = ! empty( get_field( 'seo_text_display_type', $queried_object ) ) ? get_field( 'seo_text_display_type', $queried_object ) : 'standard';

$dresses_without_order = get_posts(
	array(
		'post_type'   => 'dress',
		'numberposts' => -1,
		'fields'      => 'ids',
		'meta_query'  => array(
			array(
				'key'     => 'dress_order_' . $queried_object->term_id,
				'compare' => 'NOT EXISTS',
			),
		),
		'tax_query'   => array(
			array(
				'taxonomy' => 'dress_category',
				'field'    => 'term_id',
				'terms'    => array( $queried_object->term_id ),
			),
		),
	)
);

if ( ! empty( $dresses_without_order ) ) {
	foreach ( $dresses_without_order as $dress ) {
		update_field( 'dress_order_' . $queried_object->term_id, 0, $dress );
	}
}
?>
				<section class="<?php echo esc_attr( implode( ' ', $hero_section_classes ) ); ?>">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee', null, $infoline_data ); ?>
						<?php get_template_part( 'components/navbar' ); ?>
						<div class="slider_home-slider_slide-in">
							<?php if ( ! empty( $thumbnail ) ) : ?>
								<div class="mom-abs">
									<?php
									echo wp_get_attachment_image(
										$thumbnail,
										'full',
										false,
										array(
											'class'   => 'img-cover',
											'loading' => 'eager',
											'fetchpriority' => 'high',
										)
									);
									?>
								</div>
							<?php else : ?>
								<div class="hero-section__blur">
									<div class="hero-section__blur-color"></div>
									<div class="hero-section__blur-tone"></div>
								</div>
							<?php endif; ?>
							<div class="slider-bottom-content inner-pages">
								<?php get_template_part( 'components/breadcrumb' ); ?>
								<h1 class="p-86-96 lowercase"><?php echo wp_kses_post( loveforever_to_capitalize_brand_name_in_string( $queried_object->name ) ); ?></h1>
								<?php if ( ! empty( $queried_object->description ) ) : ?>
									<p class="p-16-20 mmax695"><?php echo wp_kses_post( $queried_object->description ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</section>
				<?php
				if ( $is_main_catalog_category ) {
					$stories = get_posts(
						array(
							'post_type'   => 'story',
							'numberposts' => -1,
							'orderby'     => array(
								'story_order_' . $queried_object->term_id => 'ASC',
							),
							'meta_key'    => 'story_order_' . $queried_object->term_id,
							'tax_query'   => array(
								array(
									'taxonomy' => 'dress_category',
									'field'    => 'term_id',
									'terms'    => array( $queried_object->term_id ),
								),
							),
						)
					);

					get_template_part( 'template-parts/home/stories-section', null, array( 'stories' => $stories ) );
				}
				?>
				<?php
				$price_range         = loveforever_get_product_price_range( $queried_object->term_id );
				$min_price           = $price_range['min_price'];
				$max_price           = $price_range['max_price'];
				$current_min_price   = ! empty( $_GET['min-price'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['min-price'] ) ) : $min_price;
				$current_max_price   = ! empty( $_GET['max-price'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['max-price'] ) ) : $max_price;
				$current_page        = ! empty( $_GET['page'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['page'] ) ) : get_query_var( 'paged' );
				$selected_silhouette = ! empty( $_GET['silhouette'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['silhouette'] ) ) : null;
				$orderby             = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'views';
				$other_filter_names  = array( 'brand', 'style', 'color' );
				$other_filters       = array();

				foreach ( $other_filter_names as $other_filter_name ) {
					$other_filters[ $other_filter_name ] = loveforever_get_filter_terms_for_dress_category( $other_filter_name );
				}

				$other_filters = array_filter( $other_filters );
				?>
				<section id="catalog" class="section z">
					<div class="container n-top">
						<h2 class="sr-only">Каталог товаров в категории <?php echo esc_html( $queried_object->name ); ?></h2>
						<?php $is_hidden = ! $is_main_catalog_category || 'dress_category' !== $queried_object->taxonomy; ?>
						<form 
							id="<?php echo esc_attr( $catalog_filter_form_id ); ?>" 
							class="filters-form lf-product-filter-form" 
							data-js-product-filter-form
							<?php echo $is_hidden ? 'hidden' : ''; ?>
						>
							<div class="vert vert-fw">
								<div class="spleet m-vert">
									<?php
									// $silhouettes = loveforever_get_silhouettes_for_dress_category();
									$silhouettes = ! empty( get_field( 'silhouettes', $queried_object ) ) ? array_map( 'get_term', get_field( 'silhouettes', $queried_object ) ) : array();

									if ( ! empty( $silhouettes ) ) :
										?>
										<div class="horiz categeory-list lf-filter-radio-group">
											<label class="lf-filter-radio">
												<input 
													type="radio" 
													id="silhouette-0" 
													name="silhouette" 
													class="lf-filter-radio__control" 
													value=""
													<?php echo empty( $selected_silhouette ) ? 'checked' : ''; ?>
												>
												<span 
													for="silhouette-0"
													class="lf-filter-radio__label"
												>Все</span>
											</label>
											<?php
											$count_silhouettes = count( $silhouettes );
											foreach ( $silhouettes as $silhouettes_index => $silhouette ) :
												?>
												<?php if ( $silhouettes_index === 7 ) : ?>
													<div class="dropdown-container">
														<div class="more-button">
															Еще
															<svg 
																width="6" 
																height="4" 
																viewBox="0 0 6 4" 
																fill="none" 
																xmlns="http://www.w3.org/2000/svg"
																style="width: 6rem; height: 4rem;"
															>
																<path fill-rule="evenodd" clip-rule="evenodd" d="M2.48716 3.41604L0 0.583958L0.512837 0L3 2.83208L5.48716 0L6 0.583958L3.51284 3.41604L3 4L2.48716 3.41604Z" fill="black"/>
															</svg>
														</div>
														<div class="dropdown-menu">
												<?php endif; ?>
														<label class="lf-filter-radio">
																<input
																		type="radio"
																		id="<?php echo esc_attr( 'silhouette-' . $silhouette->term_id ); ?>"
																		name="silhouette"
																		class="lf-filter-radio__control"
																		value="<?php echo esc_attr( $silhouette->term_id ); ?>"
																		<?php echo ! empty( $selected_silhouette ) && $silhouette->term_id === $selected_silhouette ? 'checked' : ''; ?>
																>
																<span
																		for="<?php echo esc_attr( 'silhouette-' . $silhouette->term_id ); ?>"
																		class="lf-filter-radio__label"
																><?php echo esc_html( $silhouette->name ); ?></span>
														</label>
												<?php if ( 8 <= $count_silhouettes && $silhouettes_index === count( $silhouettes ) - 1 ) : ?>
														</div>
												</div>
												<?php endif; ?>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
									<?php

									if ( ! empty( $price_range ) ) :
										$range_slider_config = array(
											'values' => array( $current_min_price, $current_max_price ),
										);
										?>
										<div class="code-embed-7 w-embed" data-js-range-slider="<?php echo esc_attr( wp_json_encode( $range_slider_config ) ); ?>">
											<div data-js-range-slider-value-min><?php echo esc_html( loveforever_format_price( $current_min_price ) ); ?></div>
											<div id="slider" data-js-range-slider-custom-component></div>
											<div id="slider-range">
												<input 
													type="number" 
													id="min" 
													name="min-price" 
													value="<?php echo esc_attr( $current_min_price ); ?>"
													min="<?php echo esc_attr( $price_range['min_price'] ); ?>"
													max="<?php echo esc_attr( $price_range['max_price'] ); ?>"
													data-js-range-slider-control-min
												> 
												<input 
													type="number" 
													id="max" 
													name="max-price" 
													value="<?php echo esc_attr( $current_max_price ); ?>"
													min="<?php echo esc_attr( $price_range['min_price'] ); ?>"
													max="<?php echo esc_attr( $price_range['max_price'] ); ?>"
													data-js-range-slider-control-max
												>
											</div>
											<div data-js-range-slider-value-max><?php echo esc_html( loveforever_format_price( $current_max_price ) ); ?></div>
										</div>
									<?php endif; ?>
								</div>
								<div class="_1px-line"></div>
								<div class="spleet botm-filter">
									<div class="custom-filter-drop lf-select">
										<select 
											id="orderby" 
											name="orderby" 
											aria-label="<?php echo esc_attr( 'Сортировка товаров' ); ?>"
											class="lf-select__control" 
											data-js-custom-select="
											<?php
											echo esc_attr(
												wp_json_encode(
													array(
														'hasBorder' => false,
														'position' => array(
															'my' => 'left top',
															'at' => 'left-10rem top-8rem',
														),
													)
												)
											);
											?>
											">
											<?php
											$orderby_options = array(
												'views' => 'По популярности',
												'date'  => 'По новизне',
												'min-price' => 'По возрастанию цены',
												'max-price' => 'По убыванию цены',
											);

											foreach ( $orderby_options as $orderby_option_value => $orderby_option_name ) :
												?>
												<option 
													value="<?php echo esc_attr( $orderby_option_value ); ?>"
													<?php echo $orderby === $orderby_option_value ? 'selected' : ''; ?>
												><?php echo esc_html( $orderby_option_name ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<?php
									if ( ! empty( $other_filters ) ) :
										$has_active_filters = loveforever_has_active_filters(
											array(
												'min-price' => $price_range['min_price'],
												'max-price' => $price_range['max_price'],
												'orderby' => 'views',
											)
										);
										?>
										<button 
											type="reset" 
											class="lf-filter-button lf-filter-button--reset"
											form="<?php echo esc_attr( $catalog_filter_form_id ); ?>"
											data-js-product-filter-form-reset-button 
											<?php echo $has_active_filters ? '' : 'disabled'; ?>
										>Очистить</button>
										<button 
											type="button" 
											class="lf-filter-button" 
											data-js-dialog-open-button="filterDialog"
										>
											<span class="lf-filter-button__icon" aria-hidden="true"></span>
											<span class="lf-filter-button__text">Фильтры</span>
										</button>
									<?php endif; ?>
								</div>
							</div>
							<input type="hidden" name="page" value="1">
							<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $queried_object->taxonomy ); ?>">
							<input type="hidden" name="<?php echo esc_attr( $queried_object->taxonomy ); ?>" value="<?php echo esc_attr( $queried_object->term_id ); ?>">
							<input type="hidden" name="action" value="get_filtered_products">
							<?php wp_nonce_field( 'submit_filter_form', 'submit_filter_form_nonce', false ); ?>
						</form>
						<?php
						$catalog_grid_classes = array(
							// 'catalog-grid',
							// 'catalog-page-grid',
							'lf-catalog-grid',
						);

						$catalog_grid = get_field( 'catalog_grid', 'option' );

						if ( ! empty( $catalog_grid ) ) {
							$catalog_grid_classes[] = 'lf-catalog-grid--' . $catalog_grid . '-col';
						}
						?>
						<div class="<?php echo esc_attr( implode( ' ', $catalog_grid_classes ) ); ?>" data-js-product-filter-form-content-element>
							<?php
							// Получаем базовые параметры.
							$current_page   = max( 1, $current_page );
							$posts_per_page = intval( get_field( 'products_per_page', 'option' ) );

							// Определяем позиции промо-блоков в зависимости от сетки каталога.
							if ( '3' === $catalog_grid ) {
								$promo_insert_positions = array( 8, 19 ); // Для 3-колоночной сетки.
							} else {
								$promo_insert_positions = array( 5, 13 ); // Для других сеток.
							}
							$promo_needed = 2; // Всегда показываем 2 промо-блока.

							// Проверка, можно ли показывать промо
							$can_show_promo = true;
							if (
								isset( $_GET['min-price'] ) && intval( $_GET['min-price'] ) !== $price_range['min_price'] ||
								isset( $_GET['max-price'] ) && intval( $_GET['max-price'] ) !== $price_range['max_price'] ||
								! empty( $_GET['silhouette'] ) ||
								! empty( $_GET['brand'] ) ||
								! empty( $_GET['style'] ) ||
								! empty( $_GET['color'] ) ||
								! empty( $_GET['fabric'] ) ||
								( ! empty( $_POST['orderby'] ) && $orderby != 'views' )
							) {
								$can_show_promo = false;
							}

							// Базовые аргументы для запроса товаров
							$products_query_args = array(
								'post_type'      => 'dress',
								'posts_per_page' => 1, // Для подсчета
								'fields'         => 'ids',
								'tax_query'      => array(
									array(
										'taxonomy' => $queried_object->taxonomy,
										'field'    => 'term_id',
										'terms'    => array( $queried_object->term_id ),
									),
								),
								'meta_query'     => array(
									array(
										'key'   => 'availability',
										'value' => '1',
									),
									array(
										'key'     => 'final_price',
										'value'   => array( intval( $current_min_price ), intval( $current_max_price + 1 ) ),
										'compare' => 'BETWEEN',
										'type'    => 'DECIMAL',
									),
								),
							);

							// Добавляем сортировку и фильтры
							switch ( $orderby ) {
								case 'date':
									$products_query_args['orderby'] = 'date';
									$products_query_args['order']   = 'DESC';
									break;
								case 'min-price':
									$products_query_args['meta_key'] = 'final_price';
									$products_query_args['orderby']  = 'meta_value_num';
									$products_query_args['order']    = 'ASC';
									break;
								case 'max-price':
									$products_query_args['meta_key'] = 'final_price';
									$products_query_args['orderby']  = 'meta_value_num';
									$products_query_args['order']    = 'DESC';
									break;
								default:
									$products_query_args['meta_query']['product_views_count']                       = array(
										'key'     => 'product_views_count',
										'compare' => 'EXISTS',
										'type'    => 'NUMERIC',
									);
									$products_query_args['meta_query'][ 'dress_order_' . $queried_object->term_id ] = array(
										'key'     => 'dress_order_' . $queried_object->term_id,
										'compare' => 'EXISTS',
										'type'    => 'NUMERIC',
									);
									$products_query_args['orderby'] = array(
										'dress_order_' . $queried_object->term_id => 'ASC',
										'product_views_count' => 'DESC',
									);
									break;
							}

							if ( ! empty( $selected_silhouette ) ) {
								$products_query_args['tax_query'][] = array(
									'taxonomy' => 'silhouette',
									'field'    => 'term_id',
									'terms'    => array( $selected_silhouette ),
								);
							}

							if ( isset( $_GET['brand'] ) ) {
								$products_query_args['tax_query'][] = array(
									'taxonomy' => 'brand',
									'field'    => 'term_id',
									'terms'    => array_map( 'absint', wp_unslash( $_GET['brand'] ) ),
								);
							}

							if ( isset( $_GET['style'] ) ) {
								$products_query_args['tax_query'][] = array(
									'taxonomy' => 'style',
									'field'    => 'term_id',
									'terms'    => is_array( $_GET['style'] ) ? array_map( 'absint', wp_unslash( $_GET['style'] ) ) : array( absint( $_GET['style'] ) ),
								);
							}

							if ( isset( $_GET['color'] ) ) {
								$products_query_args['tax_query'][] = array(
									'taxonomy' => 'color',
									'field'    => 'term_id',
									'terms'    => array_map( 'absint', wp_unslash( $_GET['color'] ) ),
								);
							}

							if ( isset( $_GET['fabric'] ) ) {
								$products_query_args['tax_query'][] = array(
									'taxonomy' => 'fabric',
									'field'    => 'term_id',
									'terms'    => array_map( 'absint', wp_unslash( $_GET['fabric'] ) ),
								);
							}

							// Подсчет общего количества товаров
							$products_count_query = new WP_Query( $products_query_args );
							$total_products       = $products_count_query->found_posts;

							// Подсчет промо-блоков
							$total_promos = 0;
							if ( $can_show_promo ) {
								$promo_count_query = new WP_Query(
									array(
										'post_type'      => 'promo_blocks',
										'posts_per_page' => 1,
										'fields'         => 'ids',
										'post_status'    => 'publish',
										'meta_key'       => 'promo_order_' . $queried_object->term_id, // Указываем ключ метаполя
										'orderby'        => 'meta_value_num', // Сортировка по числовому значению
										'order'          => 'ASC',
										'tax_query'      => array(
											array(
												'taxonomy' => $queried_object->taxonomy,
												'field'    => 'term_id',
												'terms'    => array( $queried_object->term_id ),
											),
										),
									)
								);
								$total_promos      = $promo_count_query->found_posts;
							}

							// Расчет доступных промо на текущей странице
							$promo_offset    = ( $current_page - 1 ) * $promo_needed;
							$promo_remaining = max( 0, $total_promos - $promo_offset );
							$promo_available = min( $promo_needed, $promo_remaining );

							// Расчет смещения для товаров
							$prev_promos_inserted = min( ( $current_page - 1 ) * $promo_needed, $total_promos );
							$products_offset      = max( 0, ( $current_page - 1 ) * $posts_per_page - $prev_promos_inserted );

							// Расчет количества товаров для выборки
							$products_to_fetch = $posts_per_page - $promo_available;


							// Модифицируем аргументы для основного запроса
							$products_query_args['posts_per_page'] = $products_to_fetch;
							$products_query_args['offset']         = $products_offset;
							unset( $products_query_args['fields'] ); // Убираем ограничение только на ID

							// Основной запрос товаров
							$products_query = new WP_Query( $products_query_args );
							$products       = $products_query->posts;

							// Запрос промо-блоков
							$promo_posts = array();
							if ( $can_show_promo && $promo_available > 0 ) {
								$promo_posts = get_posts(
									array(
										'post_type'      => 'promo_blocks',
										'posts_per_page' => $promo_available,
										'offset'         => $promo_offset,
										'post_status'    => 'publish',
										'meta_key'       => 'promo_order_' . $queried_object->term_id, // Ключ метаполя
										'orderby'        => 'meta_value_num', // Сортировка по числовому значению
										'order'          => 'ASC',
										'tax_query'      => array(
											array(
												'taxonomy' => $queried_object->taxonomy,
												'field'    => 'term_id',
												'terms'    => array( $queried_object->term_id ),
											),
										),
									)
								);
							}

							// Формируем общий массив с фиксированными позициями
							$all_posts = $products; // Начинаем с товаров
							$positions = array_slice( $promo_insert_positions, 0, $promo_available );
							foreach ( $positions as $index => $position ) {
								$insert_index = $position - 1;
								if ( $insert_index <= count( $all_posts ) ) {
									array_splice( $all_posts, $insert_index, 0, array( $promo_posts[ $index ] ) );
								}
							}
							$all_posts = array_slice( $all_posts, 0, $posts_per_page );

							// Расчет пагинации
							$total_items   = $total_products + min( $total_promos, ceil( ( $total_products - 1 ) / ( $posts_per_page - $promo_needed ) ) * $promo_needed );
							$max_num_pages = ceil( $total_items / $posts_per_page );

							// Устанавливаем глобальные параметры запроса
							global $wp_query;
							$wp_query->posts         = $all_posts;
							$wp_query->post_count    = count( $all_posts );
							$wp_query->found_posts   = $total_items;
							$wp_query->max_num_pages = $max_num_pages;

							// Вывод товаров и промо
							if ( ! empty( $all_posts ) ) :
								$card_index = 1;
								foreach ( $all_posts as $post ) :
									setup_postdata( $post );

									$image_loading = $card_index < 4 ? 'eager' : 'lazy';

									if ( 'promo_blocks' === $post->post_type ) {
										// Вывод промо-блока
										$template_slug = get_post_meta( $post->ID, 'promo_template', true );
										if ( $template_slug ) {
											get_template_part(
												'template-parts/promo-blocks/' . $template_slug,
												null,
												array(
													'post_object' => $post,
													'image_loading' => 'lazy',
												)
											);
										}
									} else {
										?>
											<?php
											get_template_part(
												'components/dress-card',
												null,
												array(
													'is_paged' => $current_page > 1,
													// 'image_loading' => $image_loading,
													'image_loading' => 'lazy',
												)
											);
											?>
										<?php
									}
									++$card_index;
								endforeach;
							else :
								?>
								<div class="empty-content">
									<p>Товары с заданными параметрами не найдены</p>
								</div>
							<?php endif; ?>
						</div>
						<div data-js-product-filter-form-pagination class="paginate">
							<?php
							if ( $max_num_pages > 1 ) {
								$pagination_query                      = new WP_Query();
								$pagination_query->found_posts         = $total_items;
								$pagination_query->max_num_pages       = $max_num_pages;
								$pagination_query->query_vars['paged'] = $current_page;

								echo loveforever_get_pagination_html(
									$pagination_query,
									array(
										'is_catalog_page' => true,
									)
								);
							}
							?>
						</div>
					</div>
				</section>
				<?php
				if ( ! empty( $all_posts ) ) :
					$catalog_schema = array(
						'@context'        => 'https://schema.org/',
						'@type'           => 'OfferCatalog',
						'name'            => $queried_object->name,
						'image'           => wp_get_attachment_image_url( $thumbnail, 'full' ),
						'description'     => $queried_object->description,
						'itemListElement' => array_map(
							function ( $product ) {
								$product_images = loveforever_get_product_images( $product->ID );

								return array(
									'@type'         => 'Offer',
									'name'          => $product->post_title,
									'description'   => get_post_meta( $product->ID, '_yoast_wpseo_metadesc', true ),
									'url'           => get_permalink( $product->ID ),
									'price'         => get_post_meta( $product->ID, 'final_price', true ),
									'priceCurrency' => 'RUB',
									'image'         => ! empty( $product_images ) ? wp_get_attachment_image_url( $product_images[0], 'fullhd' ) : '',
									'availability'  => 'https://schema.org/InStock',
								);
							},
							$products
						),
					);
					?>
					<script type="application/ld+json">
					<?php echo wp_json_encode( $catalog_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ); ?>	
					</script>
				<?php endif; ?>
				<?php
				$root_term   = loveforever_get_dress_category_root_term( $queried_object );
				$child_terms = get_terms(
					array(
						'taxonomy'   => 'dress_category',
						'parent'     => $root_term->term_id,
						'hide_empty' => false,
					)
				);

				$left_menu       = get_field( 'left_menu', 'option' );
				$menu_categories = array_filter(
					$left_menu,
					function ( $item ) {
						return 'dress-category' === $item['acf_fc_layout'];
					}
				);

				// Извлекаем все URL из структуры данных
				$urls = array();
				array_walk_recursive(
					$menu_categories,
					function ( $value, $key ) use ( &$urls ) {
						if ( $key === 'url' && ! empty( $value ) ) {
							$urls[] = $value;
						}
					}
				);

				$urls        = array_unique( $urls );
				$child_terms = array_filter(
					$child_terms,
					function ( $term ) use ( $urls ) {
						return ! in_array( get_term_link( $term ), $urls, true );
					}
				);
				?>
				<?php if ( ! empty( $child_terms ) ) : ?>
					<section class="section">
						<div class="container">
							<div class="lf-tags">
								<?php foreach ( $child_terms as $child_term ) : ?>
									<a href="<?php echo esc_url( get_term_link( $child_term ) ); ?>" class="lf-tag">
										<div class="lf-tag__text">
											<?php
											$terms_map = array(
												'wedding' => array( 'Свадебные', 'свадебные', 'Платья', 'платья' ),
												'evening' => array( 'Вечерние', 'вечерние', 'Платья', 'платья' ),
												'prom'    => array( 'Выпускные', 'выпускные', 'на выпускной', 'Платья', 'платья' ),
												'sale'    => array(),
												'accessories' => array(),
											);

											$child_term_name = $child_term->name;

											if ( ! empty( get_field( 'tag_label', $child_term ) ) ) {
												$child_term_name = get_field( 'tag_label', $child_term );
											} else {
												$child_term_name = str_replace( $terms_map[ $root_term->slug ], '', $child_term->name );
											}

											echo esc_html( $child_term_name );
											?>
										</div>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					</section>
				<?php endif; ?>
				<?php
				if ( ! empty( $seo_text ) && ( 'under_tag' === $seo_text_display_type || 'wrapped' === $seo_text_display_type ) ) :
					get_template_part(
						'template-parts/global/content-section',
						null,
						array(
							'content'      => $seo_text,
							'display_type' => $seo_text_display_type,
						)
					);
				endif;
				?>
				<?php // get_template_part( 'template-parts/global/personal-choice-section' ); ?>
				<?php get_template_part( 'template-parts/home/recently-viewed-section', null, array( 'view_all_link' => true ) ); ?>
				<?php get_template_part( 'template-parts/global/map-section' ); ?>
				<?php
				if ( ! empty( $seo_text ) && 'standard' === $seo_text_display_type ) :
					get_template_part(
						'template-parts/global/content-section',
						null,
						array(
							'content'      => $seo_text,
							'display_type' => $seo_text_display_type,
						)
					);
				endif;
				?>
				<?php if ( ! empty( $other_filters ) ) : ?>
					<div id="filterDialog" role="dialog" class="dialog" data-js-dialog>
						<div class="dialog__overlay" data-js-dialog-overlay>
							<div class="dialog__content" data-js-dialog-content>
								<div class="dialog-card">
									<div class="dialog-card__header">
										<h3 class="dialog-card__title noitalic uppercase ff-tt-chocolates" data-js-dialog-title>Фильтры</h3>
									</div>
									<div class="dialog-card__body">
										<div id="filterDialogAccordion" class="accordion">
											<?php foreach ( $other_filters as $other_filter_name => $other_filter_fields ) : ?>
												<div class="accordion__item">
													<h3 class="accordion__header">
														<button 
															class="accordion__trigger" 
															aria-expanded="false"
															aria-controls="<?php echo esc_attr( 'accordion-panel-' . $other_filter_name ); ?>"
															data-js-accordion-trigger
															type="button"
															id="<?php echo esc_attr( 'accordion-panel-' . $other_filter_name . '-trigger' ); ?>"
														>
															<span class="accordion__title"><?php echo esc_html( get_taxonomy( $other_filter_name )->labels->singular_name ); ?></span>
															<span class="accordion__icon" aria-hidden="true">
																<svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
																</svg>
															</span>
														</button>
													</h3>
													<div 
														class="accordion__panel" 
														id="<?php echo esc_attr( 'accordion-panel-' . $other_filter_name ); ?>" 
														role="region" 
														aria-labelledby="<?php echo esc_attr( 'accordion-panel-' . $other_filter_name . '-trigger' ); ?>"
														hidden
													>
														<div class="accordion__content">
															<?php
															foreach ( $other_filter_fields as $other_filter_field ) :
																?>
																<label class="loveforever-checkbox">
																	<input
																		form="<?php echo esc_attr( $catalog_filter_form_id ); ?>"
																		id="<?php echo esc_attr( $other_filter_name . '-' . $other_filter_field->term_id ); ?>"
																		type="checkbox"
																		name="<?php echo esc_attr( $other_filter_name . '[]' ); ?>"
																		class="loveforever-checkbox__control"
																		value="<?php echo esc_attr( $other_filter_field->term_id ); ?>"
																		<?php checked( isset( $_GET[ $other_filter_name ] ) && in_array( $other_filter_field->term_id, is_array( $_GET[ $other_filter_name ] ) ? $_GET[ $other_filter_name ] : array( $_GET[ $other_filter_name ] ) ) ); ?>
																	>
																	<span class="loveforever-checkbox__label"><?php echo esc_html( $other_filter_field->name ); ?></span>
																</label>
																<?php
															endforeach;
															?>
														</div>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
									<div class="dialog-card__footer">
										<button 
											type="button"
											class="button" 
											data-js-dialog-close-button
										>
											Показать результат
										</button>
										<button 
											form="<?php echo esc_attr( $catalog_filter_form_id ); ?>"
											type="reset" 
											class="button button--link" 
											data-js-dialog-close-button
											data-js-product-filter-form-reset-button
										>
											Очистить
										</button>
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
				<?php endif; ?>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
		<?php
		if ( $is_main_catalog_category ) {
			get_template_part( 'template-parts/global/stories-dialog', null, array( 'stories' => $stories ) );
		}
		?>
		<?php get_footer(); ?>
<?php
