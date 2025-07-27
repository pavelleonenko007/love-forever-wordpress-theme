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

$queried_object = get_queried_object();

$thumbnail = get_field( 'thumbnail', $queried_object );

$hero_section_classes = array( 'section', 'section_100vh', 'hero-section' );

if ( empty( $thumbnail ) ) {
	$hero_section_classes[] = 'hero-section--no-image';
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
								<h1 class="p-86-96"><?php echo esc_html( mb_strtolower( $queried_object->name ) ); ?></h1>
								<?php if ( ! empty( $queried_object->description ) ) : ?>
									<p class="p-16-20 mmax695"><?php echo wp_kses_post( $queried_object->description ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</section>
				<?php get_template_part( 'template-parts/home/stories-section' ); ?>
				<?php
				$price_range         = loveforever_get_product_price_range( $queried_object->term_id );
				$min_price           = ! empty( $_GET['min-price'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['min-price'] ) ) : $price_range['min_price'];
				$max_price           = ! empty( $_GET['max-price'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['max-price'] ) ) : $price_range['max_price'];
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
						<?php $is_hidden = 0 !== $queried_object->parent || $queried_object->taxonomy !== 'dress_category'; ?>
						<form 
							id="<?php echo esc_attr( $catalog_filter_form_id ); ?>" 
							class="filters-form lf-product-filter-form" 
							data-js-product-filter-form
							<?php echo $is_hidden ? 'hidden' : ''; ?>
						>
							<div class="vert vert-fw">
								<div class="spleet m-vert">
									<script>
										let silhouettes = <?php echo wp_json_encode( loveforever_get_silhouettes_for_dress_category() ); ?>;
									</script>
									<?php
									$silhouettes = loveforever_get_silhouettes_for_dress_category();

									if ( ! empty( $silhouettes ) ) :
										?>
										<div class="horiz categeory-list">
											<label class="label loveforever-filter-radio">
												<input 
													type="radio" 
													id="silhouette-0" 
													name="silhouette" 
													class="input loveforever-filter-radio__control" 
													value=""
													<?php echo empty( $selected_silhouette ) ? 'checked' : ''; ?>
												>
												<span 
													for="silhouette-0"
													class="loveforever-filter-radio__label"
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
																<svg width="6" height="4" viewBox="0 0 6 4" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<path fill-rule="evenodd" clip-rule="evenodd" d="M2.48716 3.41604L0 0.583958L0.512837 0L3 2.83208L5.48716 0L6 0.583958L3.51284 3.41604L3 4L2.48716 3.41604Z" fill="black"/>
																</svg>
														</div>
														<div class="dropdown-menu">
												<?php endif; ?>
														<label class="label loveforever-filter-radio">
																<input
																		type="radio"
																		id="<?php echo esc_attr( 'silhouette-' . $silhouette->term_id ); ?>"
																		name="silhouette"
																		class="input loveforever-filter-radio__control"
																		value="<?php echo esc_attr( $silhouette->term_id ); ?>"
																		<?php echo ! empty( $selected_silhouette ) && $silhouette->term_id === $selected_silhouette ? 'checked' : ''; ?>
																>
																<span
																		for="<?php echo esc_attr( 'silhouette-' . $silhouette->term_id ); ?>"
																		class="loveforever-filter-radio__label"
																><?php echo esc_html( $silhouette->name ); ?></span>
														</label>
												<?php if ( 8 <= $count_silhouettes && $silhouettes_index === count( $silhouettes ) - 1 ) : ?>
														</div>
												</div>
												<?php endif; ?>
											<?php endforeach; ?>
											<?php
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
												class="lf-product-filter-form__reset-button" 
												data-js-product-filter-form-reset-button
												<?php echo $has_active_filters ? '' : 'disabled'; ?>
											>Очистить</button>
										</div>
									<?php endif; ?>
									<?php

									if ( ! empty( $price_range ) ) :
										?>
										<div class="code-embed-7 w-embed" data-js-range-slider>
											<div data-js-range-slider-value-min><?php echo esc_html( loveforever_format_price( $min_price ) ); ?></div>
											<div id="slider" data-js-range-slider-custom-component></div>
											<div id="slider-range">
												<input 
													type="number" 
													id="min" 
													name="min-price" 
													value="<?php echo esc_attr( $min_price ); ?>"
													min="<?php echo esc_attr( $price_range['min_price'] ); ?>"
													max="<?php echo esc_attr( $price_range['max_price'] ); ?>"
													data-js-range-slider-control-min
												> 
												<input 
													type="number" 
													id="max" 
													name="max-price" 
													value="<?php echo esc_attr( $max_price ); ?>"
													min="<?php echo esc_attr( $price_range['min_price'] ); ?>"
													max="<?php echo esc_attr( $price_range['max_price'] ); ?>"
													data-js-range-slider-control-max
												>
											</div>
											<div data-js-range-slider-value-max><?php echo esc_html( loveforever_format_price( $max_price ) ); ?></div>
										</div>
									<?php endif; ?>
								</div>
								<div class="_1px-line"></div>
								<div class="spleet botm-filter">
									<div class="custom-filter-drop lf-select">
										<select 
											id="orderby" 
											name="orderby" 
											class="lf-select__control" 
											data-js-custom-select="
											<?php
											echo esc_attr(
												wp_json_encode(
													array(
														'hasBorder' => false,
														'position' => array(
															'my' => 'left top',
															'at' => 'left bottom',
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
										?>
										<button type="button" class="filters-btn button button--filter w-inline-block" data-js-dialog-open-button="filterDialog">
											<div class="w-embed">
												<svg xmlns="http://www.w3.org/2000/svg" width="7" height="7" viewbox="0 0 7 7" fill="none">
													<line x1="3.5" y1="2.18552e-08" x2="3.5" y2="7" stroke="black"></line>
													<line y1="3.5" x2="7" y2="3.5" stroke="black"></line>
												</svg>
											</div>
											<div>Фильтры</div>
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
							'catalog-grid',
							'catalog-page-grid',
							'lf-catalog-grid',
						);

						$catalog_grid = get_field( 'catalog_grid', 'option' );

						if ( '3' === $catalog_grid ) {
							$catalog_grid_classes[] = 'lf-catalog-grid--3-col';
						}
						?>
						<div class="<?php echo esc_attr( implode( ' ', $catalog_grid_classes ) ); ?>" data-js-product-filter-form-content-element>
							<?php
							// Получаем базовые параметры
							$current_page           = max( 1, $current_page );
							$posts_per_page         = intval( get_field( 'products_per_page', 'option' ) );
							$promo_insert_positions = range( 2, $posts_per_page, 6 ); // Позиции 2, 8, 14, 20
							$promo_needed           = count( $promo_insert_positions );

							// Проверка, можно ли показывать промо
							$can_show_promo = true;
							if (
								isset( $_GET['min-price'] ) && intval( $_GET['min-price'] ) !== $price_range['min_price'] ||
								isset( $_GET['max-price'] ) && intval( $_GET['max-price'] ) !== $price_range['max_price'] ||
								! empty( $_GET['silhouette'] ) ||
								! empty( $_GET['brand'] ) ||
								! empty( $_GET['style'] ) ||
								! empty( $_GET['color'] ) ||
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
										'value'   => array( intval( $min_price ), intval( $max_price + 1 ) ),
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
									'terms'    => array_map( 'absint', wp_unslash( $_GET['style'] ) ),
								);
							}

							if ( isset( $_GET['color'] ) ) {
								$products_query_args['tax_query'][] = array(
									'taxonomy' => 'color',
									'field'    => 'term_id',
									'terms'    => array_map( 'absint', wp_unslash( $_GET['color'] ) ),
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
													'image_loading' => $image_loading,
												)
											);
										}
									} else {
										?>
										<div id="w-node-_53fa07b3-8fd9-bf77-2e13-30ca426c3020-d315ac0c" class="test-grid">
											<?php
											get_template_part(
												'components/dress-card',
												null,
												array(
													'size' => $size,
													'is_paged' => $current_page > 1,
													'image_loading' => $image_loading,
												)
											);
											?>
										</div>
										<?php
									}
									++$card_index;
								endforeach;
							else :
								?>
								<div class="empty-content">
									<p>Товары с заданными параметрами не найдены</p>
									<button 
										type="reset" 
										class="button"
										form="catalogFilterForm"
									>Очистить фильтры</button>
								</div>
							<?php endif; ?>
							<!-- <div id="w-node-_53fa07b3-8fd9-bf77-2e13-30ca426c3020-d315ac0c" class="test-grid">
								<div id="w-node-_53fa07b3-8fd9-bf77-2e13-30ca426c3021-d315ac0c" class="prod-item-tizer">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom _3">
												<div class="to-keeper">
													<img src="<?php echo get_template_directory_uri(); ?>/images/6721fb71c48ed057ca993543_Group20184.avif" loading="lazy" alt class="img-fw">
													<div class="map-dot cd2">
														<div class="p-36-36">платья</div>
													</div>
													<div class="map-dot _2 cd3">
														<div class="p-36-36">в загз</div>
													</div>
													<div class="map-dot _3 cd3">
														<div class="p-36-36">в загз</div>
													</div>
													<div class="map-dot _4 cd4">
														<div class="p-36-36">платья</div>
													</div>
												</div>
											</div>
										</a>
										<a href="#" class="btn pink-btn in-card w-inline-block">
											<div>смотреть</div>
										</a>
									</div>
								</div>
							</div>
							<div id="w-node-cc202385-12e9-fec8-f3ae-5e0ad563e297-d315ac0c" class="test-grid">
								<div id="w-node-cc202385-12e9-fec8-f3ae-5e0ad563e298-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom _2">
												<div class="to-keeper _2">
													<img src="<?php echo get_template_directory_uri(); ?>/images/6721fb71c48ed057ca993543_Group20184.avif" loading="lazy" alt class="img-fw">
													<div class="map-dot cd2">
														<div class="p-36-36 white">совет</div>
													</div>
													<div class="map-dot _2 cd3">
														<div class="p-36-36 white">совет</div>
													</div>
													<div class="map-dot _3 cd3">
														<div class="p-36-36 white">совет</div>
													</div>
													<div class="map-dot _4 cd4">
														<div class="p-36-36 white">совет</div>
													</div>
												</div>
												<div class="p-12-16 italic mmax-263rem">Короткое свадебное платье стройным девушкам. Хорошо сочетается с высоким  каблуком и с обувью на плоской подошве, <br>если позволяет рост. Часто  дополняется шлейфом или юбкой из прозрачного кружева</div>
												<div class="psd-horiz">
													<div class="p-12-12 uper">больше полезных советов</div>
													<img src="<?php echo get_template_directory_uri(); ?>/images/6720d17cfb5622b535a21354_Arrow20Down.svg" loading="eager" alt class="img-arrow inv">
												</div>
											</div>
										</a>
									</div>
								</div>
							</div>
							<div id="w-node-_3eee8acb-afe5-f6d8-4f80-3838c06dc5cc-d315ac0c" class="test-grid">
								<div id="w-node-_6e88719d-fe8f-116f-4337-b580b5a0b461-b5a0b461" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
										<div class="img-indirim"><img src="<?php echo get_template_directory_uri(); ?>/images/6722209ada917fe93f9a511b_Group20185.svg" loading="lazy" alt class="img-fh"><img src="<?php echo get_template_directory_uri(); ?>/images/6722210212c04f42b1086a3c_Rectangle2021.svg" loading="lazy" alt class="img-tr"></div>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12 uper m-12-12">Свадебное платье Даниэль</div>
										<div class="horiz indirim-horiz">
											<div class="p-12-12 italic letter-5">29 500 ₽</div>
											<div class="p-12-12 italic letter-5 oldprice">29 500 ₽</div>
										</div>
									</a>
								</div>
							</div>
							<div id="w-node-_68ef509b-b329-101f-8c3a-217efeebc923-d315ac0c" class="test-grid">
								<div id="w-node-_68ef509b-b329-101f-8c3a-217efeebc924-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
										<div class="img-indirim"><img src="<?php echo get_template_directory_uri(); ?>/images/672220e8def284ed6fb2d7a9_Group20304.svg" loading="lazy" alt class="img-fh"><img src="<?php echo get_template_directory_uri(); ?>/images/6722210212c04f42b1086a3c_Rectangle2021.svg" loading="lazy" alt class="img-tr"></div>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-f4761815-d2f3-fec5-0c08-ede9829bb6c0-d315ac0c" class="test-grid">
								<div id="w-node-f4761815-d2f3-fec5-0c08-ede9829bb6c1-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-_18ed627e-bdd4-490b-511f-927fcd131dc7-d315ac0c" class="test-grid">
								<div id="w-node-_18ed627e-bdd4-490b-511f-927fcd131dc8-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-_8f0c638b-1840-9b28-eaf9-6c10d33048fc-d315ac0c" class="test-grid">
								<div class="oyziv-item no-image in-item">
									<div class="vert">
										<div class="otziv-horiz no-image">
											<div class="p-12-12 uper">отзыв невесты, Ольга</div>
										</div>
										<p class="p-14-20 italic">Хочу сказать большое спасибо СалонуLove Forever и в частности  Елене, которая помогла мне подобрать платье к самому важному дню в моей  жизни. Час пролетел как 5 минут. Хотелось что-то красивое и свадебное, но в то же время без  излишеств...именно "свое" платье я и нашла в этом чудесном салоне,  платье-находка, платье-трансформер!</p>
										<div class="otziv-flex">
											<div class="otziv-flex_in">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721fb71c48ed057ca993543_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
											<div class="otziv-flex_in">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721fb71c48ed057ca993543_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
											<div class="otziv-flex_in">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721fb71c48ed057ca993543_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</div>
										<a id="w-node-_370d4190-95f4-ba25-fdbd-fc8dd40dd2ce-d315ac0c" href="#" class="btn btn-with-arrow cc-purple w-inline-block">
											<div>еще</div>
											<img src="<?php echo get_template_directory_uri(); ?>/images/6720d17cfb5622b535a21354_Arrow20Down.svg" loading="eager" alt class="img-arrow">
										</a>
									</div>
								</div>
							</div>
							<div id="w-node-_97eeab43-0852-6a7b-ff73-c2c717dc386f-d315ac0c" class="test-grid">
								<div id="w-node-_97eeab43-0852-6a7b-ff73-c2c717dc3870-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-f44148ad-898c-c76d-5947-eb740619e940-d315ac0c" class="test-grid">
								<div id="w-node-f44148ad-898c-c76d-5947-eb740619e941-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-_55c04207-60a4-5ff0-25e3-8691ba04b4ab-d315ac0c" class="test-grid">
								<div id="w-node-_55c04207-60a4-5ff0-25e3-8691ba04b4ac-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-a685eb5e-aa3f-368d-e3cf-b236dd105b62-d315ac0c" class="test-grid">
								<div id="w-node-a685eb5e-aa3f-368d-e3cf-b236dd105b63-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-c0c8eebd-4208-bf21-8be2-4bee750cc98f-d315ac0c" class="test-grid">
								<div id="w-node-c0c8eebd-4208-bf21-8be2-4bee750cc990-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-_053bfc90-1c4b-21f4-df68-95ba7b687153-d315ac0c" class="test-grid">
								<div id="w-node-_053bfc90-1c4b-21f4-df68-95ba7b687154-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-d6e9c8dc-2fb3-f2bc-1b36-d8a00760b45e-d315ac0c" class="test-grid">
								<div id="w-node-d6e9c8dc-2fb3-f2bc-1b36-d8a00760b45f-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-_2cac7733-1dda-e850-5712-c99474bb5685-d315ac0c" class="test-grid">
								<div id="w-node-_2cac7733-1dda-e850-5712-c99474bb5686-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-_1ecbe7b3-15c2-50e5-bc2b-d5db4b65c378-d315ac0c" class="test-grid">
								<div id="w-node-_1ecbe7b3-15c2-50e5-bc2b-d5db4b65c379-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div>
							<div id="w-node-_28ef0e2d-91ca-a758-ff85-e6ff2333a438-d315ac0c" class="test-grid">
								<div id="w-node-_28ef0e2d-91ca-a758-ff85-e6ff2333a439-d315ac0c" class="prod-item">
									<div class="prod-item_top">
										<a href="#" class="link w-inline-block">
											<div class="prod-item_img-mom">
												<div class="mom-abs"><img src="<?php echo get_template_directory_uri(); ?>/images/6721e83f61d503dbabc19bcc_Group20184.avif" loading="lazy" alt class="img-cover"></div>
											</div>
										</a>
										<a href="#" class="btn-like w-inline-block">
											<div class="svg w-embed">
												<svg width="30" height="35" viewbox="0 0 30 35" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect y="5" width="30" height="30" fill="#F22EA9"></rect>
													<path d="M30 5L25 0V5H30Z" fill="#F22EA9"></path>
													<path d="M30 5L25 0V5H30Z" fill="black" fill-opacity="0.2"></path>
													<path d="M20.4894 20.8851L20.4917 20.8874L15.0022 26.298L8.63543 20.0226C7.12152 18.5304 7.12152 16.1143 8.63543 14.6221C10.1534 13.126 12.6173 13.126 14.1353 14.6221L14.649 15.1285L15 15.4744L15.351 15.1285L15.8647 14.6221C17.3827 13.126 19.8466 13.126 21.3646 14.6221C22.8785 16.1143 22.8785 18.5304 21.3646 20.0226L20.8508 20.529L20.4894 20.8851Z" stroke="white"></path>
												</svg>
											</div>
										</a>
									</div>
									<a href="#" class="prod-item_bottom w-inline-block">
										<div class="p-12-12">Свадебное платье Даниэль</div>
										<div class="p-12-12 italic letter-5">29 500 ₽</div>
									</a>
								</div>
							</div> -->
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
				$root_term   = loveforever_get_dress_category_root_term( $queried_object );
				$child_terms = get_terms(
					array(
						'taxonomy'   => 'dress_category',
						'parent'     => $root_term->term_id,
						'hide_empty' => true,
					)
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
											);

											$child_term_name = str_replace( $terms_map[ $root_term->slug ], '', $child_term->name );

											echo esc_html( $child_term_name );
											?>
										</div>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					</section>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/global/personal-choice-section' ); ?>
				<?php get_template_part( 'template-parts/home/recently-viewed-section' ); ?>
				<?php get_template_part( 'template-parts/global/map-section' ); ?>
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
																		<?php checked( isset( $_GET[ $other_filter_name ] ) && in_array( $other_filter_field->term_id, $_GET[ $other_filter_name ] ) ); ?>
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
		<?php get_template_part( 'template-parts/global/stories-dialog' ); ?>
		<?php get_footer(); ?>
<?php
