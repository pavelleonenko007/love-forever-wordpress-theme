<?php
/**
 * Navbar
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;
$phone         = get_field( 'phone', 'option' );
$address       = get_field( 'address', 'option' );
$working_hours = get_field( 'working_hours', 'option' );
$socials       = loveforever_get_socials();
$favorites     = loveforever_get_favorites();

$left_menu    = get_field( 'left_menu', 'option' );
$right_menu   = get_field( 'right_menu', 'option' );
$search_links = get_field( 'search_links', 'option' );

$mobile_menu_items  = array( ...$left_menu, ...$right_menu );
$only_catalog_items = array_filter(
	$mobile_menu_items,
	function ( $item ) {
		return 'dress-category' === $item['acf_fc_layout'] || 'dress-category-with-images' === $item['acf_fc_layout'];
	}
);
$filter_taxonomies  = array( 'silhouette', 'style', 'fabric', 'brand' );
?>
<div data-animation="default" data-collapse="medium" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="navbar w-nav">
	<header class="header menuline">
		<div class="vert-menu">
			<div class="spleet pc-none">
				<?php if ( ! empty( $phone ) ) : ?>
				<div class="menu-line p-12-12 white uper n-voreder">
					<a href="<?php echo esc_url( loveforever_format_phone_to_link( $phone ) ); ?>" class="n-menu w-nav-link"><?php echo esc_html( $phone ); ?></a>
				</div>
				<?php endif; ?>
				<div class="l-spacer"></div>
				<div class="menu-line p-12-12 white uper rev n-voreder">
					<div class="div-block-5">
						<!-- Favorites button -->
						<a href="<?php echo esc_url( home_url( '/' ) . 'favorites' ); ?>" class="lf-icon-button lf-icon-button--favorites lf-icon-button--white <?php echo 0 < count( $favorites ) ? 'is-active' : ''; ?>" data-js-favorites-button>
							<div class="lf-icon-button__icon-wrapper">
								<svg viewbox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
									<use href="#heartIcon"></use>
								</svg>
							</div>
							<span class="lf-icon-button__counter" data-js-favorites-button-counter><?php echo esc_html( (string) count( $favorites ) ); ?></span>
						</a>
						<!-- Favorites button end -->
						<div class="menu-link-keeper">
							<button type="button" class="lf-icon-button lf-icon-button--search lf-icon-button--white">
								<div class="lf-icon-button__icon-wrapper">
									<svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 16 16" class="lf-icon-button__icon">
										<use href="#searchIcon"></use>
									</svg>
								</div>
							</button>
							<div class="hovered-menue search-m">
								<div id="w-node-_29763d6b-4a4a-4ba9-96d9-354223034cf4-be61d3ef" class="div-block-6">
									<div class="div-block-7">
										<form id="searchForm1" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="lf-search-form search" data-js-search-form>
											<input
												type="lf-search-form__search-input search" 
												name="s" 
												maxlength="256" 
												placeholder="Напишите, что вы ищите..." 
												id="searchForm1SearchControl" 
												class="search-input w-input" 
												required
												data-js-search-form-search-input
											>
											<input type="submit" class="search-button w-button" value="Search">
											<button type="reset" class="lf-search-form__reset clear-search">Очистить</button>
										</form>
										<div class="search-ajaxed" data-js-search-form-results></div>
									</div>
								</div>
								<?php if ( ! empty( $search_links ) ) : ?>
									<div class="serch-mob">
										<div class="m-nav-cats">
											<?php foreach ( $search_links as $search_column ) : ?>
												<div class="m-nav-drops">
													<a href="#" class="m-nav-drop-btn w-inline-block">
														<div><?php echo esc_html( $search_column['column_name'] ); ?></div>
														<img src="<?php echo TEMPLATE_PATH; ?>/images/673dc9a4d3949ca7d7c90f76_Union.svg" loading="eager" alt class="image-6-drop">
													</a>
													<div class="m-nav-drop-contant">
														<?php
														$column_links = $search_column['column_links'];
														if ( ! empty( $column_links ) ) :
															?>
															<div class="div-block-11">
																<?php
																foreach ( $column_links as $column_link ) :
																	$column_link_attributes_str = loveforever_prepare_link_attributes(
																		array(
																			'class' => 'a-12-12 in-drop',
																		),
																		$column_link['link']
																	);
																	?>
																	<a <?php echo $column_link_attributes_str; ?>><?php echo esc_html( $column_link['link']['title'] ); ?></a>
																<?php endforeach; ?>
															</div>
														<?php endif; ?>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
								<div class="vert m-none">
									<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a05-be61d3ef" class="m-h-vert">
										<div class="p-16-16">силуэт 2</div>
										<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a08-be61d3ef" class="m-h-vert">
											<a href="#" class="a-12-12 w-inline-block">
												<div>а-силуэт</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>греческий стиль</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>пышные</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>короткие</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>со шлейфом</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>трансформеры</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>прямые</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>комбинизон / костюм</div>
											</a>
										</div>
									</div>
									<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a21-be61d3ef" class="m-h-vert">
										<div class="p-16-16">стиль</div>
										<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a24-be61d3ef" class="m-h-vert">
											<a href="#" class="a-12-12 w-inline-block">
												<div>открытые</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>закрытые</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>с рукавами</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>с открытой спиной</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>пляжные / на море</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>минимализм / простые</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>блестящие</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>бохо / рустик</div>
											</a>
										</div>
									</div>
									<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a3d-be61d3ef" class="m-h-vert">
										<div class="p-16-16">ткань</div>
										<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a40-be61d3ef" class="m-h-vert">
											<a href="#" class="a-12-12 w-inline-block">
												<div>Фатин</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Атлас / Сатин</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Шифон</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>кружевные</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>креп</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>шелк</div>
											</a>
										</div>
									</div>
									<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a53-be61d3ef" class="m-h-vert">
										<div class="p-16-16">топ бренды</div>
										<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a56-be61d3ef" class="m-h-vert grider">
											<a href="#" class="a-12-12 w-inline-block">
												<div>AVE</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>paulain</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>tatiana Kaplun</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>love forever</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Divino Rose</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Rima Lav</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>paulain</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Divino Rose</div>
											</a>
											<a id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a6f-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>Kookla</div>
											</a>
											<a id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a72-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>S. markelova</div>
											</a>
											<a id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a75-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>milva</div>
											</a>
											<a id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a78-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>Divino Rose</div>
											</a>
											<a id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a7b-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>S. markelova</div>
											</a>
										</div>
									</div>
									<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a7e-be61d3ef" class="m-h-vert">
										<div class="p-16-16">стоимость</div>
										<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6a81-be61d3ef" class="m-h-vert">
											<a href="#" class="a-12-12 w-inline-block">
												<div>10 000 ₽ - 20 000 ₽</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>20 000 ₽ - 30 000 ₽</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>30 000 ₽ - 40 000 ₽</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>40 000 ₽ - 50 000 ₽</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>50 000 ₽ - 100 000 ₽</div>
											</a>
										</div>
									</div>
									<div id="w-node-_2865cd4a-e461-7ab8-29b0-4dac76c087cf-be61d3ef" class="div-block-6 _3">
										<a href="#" class="btn in-single-btn _3 w-inline-block">
											<div>перейти в каталог</div>
										</a>
									</div>
								</div>
								<div id="w-node-_3c45bc09-5b3c-1a4e-f526-aea5e4cb6aaa-be61d3ef" class="hovered-menue_close-menu"></div>
							</div>
						</div>
						<div class="menu-link-keeper">
							<button type="button" class="lf-burger-button menu-bnt w-inline-block">
								<div class="lf-burger-button__line b-line"></div>
								<div class="lf-burger-button__line b-line"></div>
								<div class="lf-burger-button__line b-line"></div>
							</button>
							<div class="hovered-menue mob-menue">
								<div class="mob-menu-kee">
									<div class="m-nav-keep">
										<div class="m-nav-top">
											<?php foreach ( $mobile_menu_items as $mobile_menu_item ) : ?>
												<?php
												if ( 'dress-category' === $mobile_menu_item['acf_fc_layout'] || 'dress-category-with-images' === $mobile_menu_item['acf_fc_layout'] ) :
													$dress_category = get_term( $mobile_menu_item['category'] );
													?>
													<a href="#" class="m-nav-drop w-inline-block">
														<div><?php echo esc_html( $dress_category->name ); ?></div>
														<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc676af0eceedf43e40c1_Union.svg' ); ?>" loading="eager" alt class="image-6">
													</a>
												<?php else : ?>
													<?php $menu_link = $mobile_menu_item['link']; ?>
													<a href="<?php echo esc_url( $menu_link['url'] ); ?>" class="m-nav-a w-inline-block">
														<div>
															<?php echo esc_html( $menu_link['title'] ); ?>
															<?php if ( ! empty( $mobile_menu_item['green_badge'] ) ) : ?>
																<span class="indirim-span"><?php echo esc_html( $mobile_menu_item['green_badge'] ); ?></span>
															<?php endif; ?>
														</div>
													</a>
												<?php endif; ?>
											<?php endforeach; ?>
										</div>
										<div id="w-node-_3514c32b-e70e-46c1-9d79-f82fca09e2e3-be61d3ef" class="div-block-4 cont-item">
											<div class="p-12-12 uper m-12-12">Наши группы в социальных сетях</div>
						<div class="soc-grid mpb lf-share-buttons">
									<?php foreach ( $socials as $social ) : ?>
						<a class="lf-share-button lf-share-button--dark" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<svg class="lf-share-button__icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
							<use href="#<?php echo esc_attr( $social['icon'] ); ?>"></use>
							</svg>
						</a>
						<?php endforeach; ?>
											</div>
										</div>
										<div class="mob-work-time m-12-12">
											<?php if ( ! empty( WORKING_HOURS ) ) : ?>
												<div><?php echo esc_html( WORKING_HOURS ); ?></div>
											<?php endif; ?>
											<?php if ( ! empty( ADDRESS ) ) : ?>
												<div><?php echo esc_html( ADDRESS ); ?></div>
											<?php endif; ?>
										</div>
									</div>
									<?php if ( ! empty( $only_catalog_items ) ) : ?>
										<div class="m-nav-content">
											<?php
											foreach ( $only_catalog_items as $only_catalog_item ) :
												$dress_category = get_term( $only_catalog_item['category'] );
												?>
												<div class="m-nav-content_in">
													<a href="#" class="m-nav-content_back w-inline-block">
														<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc840b23caa30509cbdf5_Union.svg' ); ?>" loading="eager" alt class="image-7">
														<div class="p-12-12 uper m-12-12"><?php echo esc_html( $dress_category->name ); ?></div>
													</a>
													<a href="<?php echo esc_url( get_term_link( $dress_category ) ); ?>" class="m-nav-a long w-inline-block">
														<div>СМОТРЕТЬ ВСЕ</div>
														<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc676af0eceedf43e40c1_Union.svg' ); ?>" loading="eager" alt class="image-6">
													</a>
													<?php
													$dropdowns = ! empty( $only_catalog_item['columns'] ) ? $only_catalog_item['columns'] : array();
													$dropdowns = array_filter(
														$dropdowns,
														function ( $dropdown ) {
															return ! empty( $dropdown['links'] );
														}
													);
													if ( ! empty( $dropdowns ) ) :
														?>
														<div class="m-nav-cats">
															<?php foreach ( $dropdowns as $dropdown ) : ?>
																<div class="m-nav-drops">
																	<a href="#" class="m-nav-drop-btn w-inline-block">
																		<div><?php echo esc_html( $dropdown['column_name'] ); ?></div>
																		<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc9a4d3949ca7d7c90f76_Union.svg' ); ?>" loading="eager" alt="" class="image-6-drop">
																	</a>
																	<div class="m-nav-drop-contant">
																		<div class="div-block-11">
																			<?php foreach ( $dropdown['links'] as $dropdown_link_item ) : ?>
																				<?php $dropdown_link = $dropdown_link_item['link']; ?>
																				<a 
																					href="<?php echo esc_url( $dropdown_link['url'] ); ?>" 
																					class="a-12-12 in-drop"
																					target="<?php echo esc_attr( $dropdown_link['target'] ) ?? '_self'; ?>"
																				>
																					<?php echo esc_html( $dropdown_link['title'] ); ?>
																				</a>
																			<?php endforeach; ?>
																		</div>
																	</div>
																</div>
															<?php endforeach; ?>
														</div>
													<?php endif; ?>
													<?php if ( 'dress-category-with-images' === $only_catalog_item['acf_fc_layout'] ) : ?>
														<?php $submenu_items = ! empty( $only_catalog_item['cards'] ) ? $only_catalog_item['cards'] : array(); ?>
														<?php if ( ! empty( $submenu_items ) ) : ?>
															<div class="m-nav-cats">
																<?php foreach ( $submenu_items as $submenu_item ) : ?>
																	<div class="m-nav-drops">
																		<a href="<?php echo esc_url( $submenu_item['page_link']['url'] ); ?>" class="m-nav-drop-btn w-inline-block">
																			<div><?php echo esc_html( $submenu_item['page_link']['title'] ); ?></div>
																			<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc9a4d3949ca7d7c90f76_Union.svg' ); ?>" loading="eager" alt="" class="image-6-drop"
																			style="transform: rotate(-90deg);">
																		</a>
																	</div>
																<?php endforeach; ?>
															</div>
														<?php endif; ?>
													<?php endif; ?>
												</div>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="spleet m-none">
				<?php if ( ! empty( $left_menu ) ) : ?>
					<!-- Desktop Left menu start -->
					<div class="menu-line p-12-12 white uper n-voreder">
						<?php foreach ( $left_menu as $left_menu_item ) : ?>
							<?php get_template_part( 'components/navbar-' . $left_menu_item['acf_fc_layout'], null, $left_menu_item ); ?>
						<?php endforeach; ?>
					</div>
					<!-- Desktop Left menu end -->
				<?php endif; ?>
				<div class="l-spacer"></div>
				<!-- Desktop rigth menu start -->
				<div class="menu-line p-12-12 white uper rev n-voreder">
						<?php
						if ( ! empty( $right_menu ) ) :
							foreach ( $right_menu as $right_menu_item ) :
								?>
								<?php get_template_part( 'components/navbar-' . $right_menu_item['acf_fc_layout'], null, $right_menu_item ); ?>
								<?php
							endforeach;
							endif;
						?>
						<div class="div-block-5">
							<div class="menu-link-keeper">
								<a href="#" class="lf-icon-button lf-icon-button--search lf-icon-button--white">
									<div class="lf-icon-button__icon-wrapper">
										<svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 16 16" class="lf-icon-button__icon">
											<use href="#searchIcon" />
										</svg>
									</div>
								</a>
								<div class="hovered-menue search-m" style="grid-template-columns: repeat(6, auto);">
									<div id="w-node-_144563be-6001-1af8-6446-1240953da9f3-be61d3ef" class="div-block-6">
										<div class="div-block-7">
											<form id="searchForm2" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="lf-search-form search" data-js-search-form>
												<input 
													class="lf-search-form__search-input search-input w-input" 
													maxlength="256" 
													name="s" 
													placeholder="Напишите, что вы ищите..." 
													type="search" 
													id="searchForm2SearchControl" 
													required
													data-js-search-form-search-input
												>
												<input type="hidden" name="posts_per_page" value="12">
												<input type="submit" class="search-button w-button" value="Search">
												<button type="reset" class="lf-search-form__reset clear-search">Очистить</button>
											</form>
											<div class="search-ajaxed" data-js-search-form-results></div>
										</div>
									</div>
									<?php foreach ( $search_links as $search_column ) : ?>
										<div id="w-node-_144563be-6001-1af8-6446-1240953daa3d-be61d3ef" class="m-h-vert">
											<div class="p-16-16"><?php echo esc_html( $search_column['column_name'] ); ?></div>
											<?php
											$column_links = $search_column['column_links'] ?: array();

											if ( ! empty( $column_links ) ) :
												?>
												<div id="w-node-_144563be-6001-1af8-6446-1240953daa40-be61d3ef" class="m-h-vert">
													<?php
													foreach ( $column_links as $column_link ) :
														$column_link_attributes_str = loveforever_prepare_link_attributes(
															array(
																'class' => 'a-12-12 w-inline-block',
															),
															$column_link['link']
														);
														?>
													<a <?php echo $column_link_attributes_str; ?>>
														<div><?php echo esc_html( $column_link['link']['title'] ); ?></div>
													</a>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>
										</div>
									<?php endforeach; ?>
									<!-- <div id="w-node-_144563be-6001-1af8-6446-1240953daac9-be61d3ef" class="div-block-6 _3">
										<a href="#" class="btn in-single-btn _3 w-inline-block">
											<div>перейти в каталог</div>
										</a>
									</div> -->
									<div id="w-node-_144563be-6001-1af8-6446-1240953daacd-be61d3ef" class="hovered-menue_close-menu"></div>
								</div>
							</div>
							<!-- Favorites button -->
							<a href="<?php echo esc_url( home_url( '/' ) . 'favorites' ); ?>" class="lf-icon-button lf-icon-button--favorites lf-icon-button--white <?php echo 0 < count( $favorites ) ? 'is-active' : ''; ?>" data-js-favorites-button>
								<div class="lf-icon-button__icon-wrapper">
									<svg viewbox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
										<use href="#heartIcon"></use>
									</svg>
								</div>
								<span class="lf-icon-button__counter" data-js-favorites-button-counter><?php echo esc_html( (string) count( $favorites ) ); ?></span>
							</a>
							<!-- Favorites button end -->
						</div>
					</div>
					<!-- Desktop rigth menu end -->
			</div>
			<div class="spleet m-none">
				<a href="<?php echo esc_url( get_the_permalink( CONTACT_PAGE_ID ) . '#contacts' ); ?>" class="menu-line p-12-12 white uper" style="text-decoration: none;">
					<?php if ( ! empty( ADDRESS ) ) : ?>
						<div><?php echo esc_html( $address ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( WORKING_HOURS ) ) : ?>
						<div><?php echo esc_html( WORKING_HOURS ); ?></div>
					<?php endif; ?>
				</a>
				<div class="l-spacer"></div>
				<div class="menu-line p-12-12 white uper rev">
					<?php if ( ! empty( PHONE ) ) : ?>
						<div class="horiz lf-hidden-phone">
							<div id="headerPhoneNumber" data-js-phone-number="<?php echo esc_attr( PHONE ); ?>"><?php echo esc_html( loveforever_mask_phone( PHONE ) ); ?></div>
							<button type="button" data-js-phone-number-button="headerPhoneNumber" class="show-all-btn phone-button uppercase">Показать</button>
						</div>
					<?php endif; ?>
					<?php
					if ( ! empty( $socials ) ) :
						?>
			<div class="head-soc-menu">
						<?php foreach ( $socials as $social ) : ?>
				<a href="<?php echo esc_url( $social['url'] ); ?>" aria-label="<?php echo esc_attr( $social['aria-label'] ); ?>" class="lf-header-social" target="_blank" rel="noopener noreferrer">
				<svg class="lf-header-social__icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
				<use href="#<?php echo esc_attr( $social['icon'] ); ?>" />
				</svg>
				</a>
			<?php endforeach; ?>
						</div>
								<?php endif; ?>
				</div>
			</div>
			<div class="logo-keeper">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link w-inline-block">
					<div class="svg w-embed">
						<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewbox="0 0 225 141" fill="none">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M103.743 21.3738C107.834 25.4976 107.305 27.4097 106.093 29.0892C105.492 29.9172 100.564 32.9568 97.168 23.6131C96.1926 21.2516 94.5065 9.03773 101.63 5.59988C109.678 1.91365 113.501 9.54237 114.591 11.9749C115.337 13.636 119.436 26.9355 123.283 39.4202L123.284 39.4241C126.705 50.5241 129.927 60.979 130.418 62.0366C131.457 64.2839 133.222 64.3036 134.173 63.3771C135.129 62.4506 146.455 39.3161 147.525 36.4617C148.595 33.6113 157.148 10.7764 129.285 2.66272C133.297 -1.65036 144.516 -0.033941 150.072 2.66272C155.628 5.35939 164.327 4.18847 164.327 4.18847C164.327 4.18847 157.772 9.25851 150.273 5.50132C144.097 2.40646 140.563 2.5957 140.563 2.5957C157.555 8.52915 158.981 17.7861 149.736 37.2265C142.62 52.1844 138.047 60.7474 135.706 64.477C133.364 68.2066 128.175 74.9838 123.349 67.6389C120.618 63.4753 115.271 45.8373 110.742 30.8997C107.278 19.4724 104.293 9.62539 103.324 8.60011C101.658 6.82994 99.7269 9.71584 99.6559 12.0774C99.5848 14.4389 99.6559 17.2618 103.747 21.3856L103.743 21.3738ZM49.625 70.8602V70.1821C51.252 70.1821 52.5748 69.6972 53.5897 68.7273C54.6046 67.7575 55.114 66.4368 55.114 64.7691V26.2036C55.114 24.5793 54.6046 23.2704 53.5897 22.2809C52.5748 21.2874 51.252 20.7906 49.625 20.7906V20.1125H56.5356C58.8418 20.1125 60.603 19.8641 61.8232 19.3674C62.9526 18.8706 63.7424 18.2635 64.1965 17.542H64.8757V64.7691C64.8757 66.4368 65.3852 67.7575 66.4 68.7273C67.4149 69.6972 68.7378 70.1821 70.3647 70.1821V70.8602H49.625ZM42.8939 122.976C39.5767 122.976 36.6743 122.309 34.1865 120.977C31.6986 119.644 29.7676 117.768 28.3934 115.343C27.0192 112.922 26.332 110.052 26.332 106.744C26.332 103.437 27.0192 100.567 28.3934 98.1458C29.7676 95.7212 31.6986 93.8446 34.1865 92.512C36.6743 91.1795 39.5767 90.5132 42.8939 90.5132C46.2109 90.5132 49.1016 91.1795 51.5696 92.512C54.0377 93.8446 55.9569 95.7212 57.3311 98.1458C58.7053 100.567 59.3925 103.437 59.3925 106.744C59.3925 110.052 58.7053 112.922 57.3311 115.343C55.9569 117.764 54.0338 119.644 51.5696 120.977C49.1016 122.309 46.2109 122.976 42.8939 122.976ZM42.8346 122.187C44.2483 122.187 45.4646 121.481 46.4755 120.066C47.4865 118.655 48.2762 116.766 48.8409 114.405C49.4056 112.043 49.6899 109.469 49.6899 106.681C49.6899 103.654 49.3977 100.98 48.8093 98.6584C48.2209 96.3362 47.4153 94.5188 46.3807 93.2059C45.3501 91.8931 44.1851 91.2386 42.8939 91.2386C41.5196 91.2386 40.3152 91.9443 39.2845 93.3597C38.2538 94.775 37.4522 96.6516 36.8875 98.9935C36.3188 101.335 36.0385 103.898 36.0385 106.685C36.0385 109.753 36.3426 112.461 36.9468 114.803C37.5549 117.145 38.3723 118.962 39.403 120.255C40.4337 121.548 41.5749 122.195 42.8306 122.195L42.8346 122.187ZM80.8097 69.5745C83.5898 71.0609 86.8318 71.806 90.5359 71.806C94.2401 71.806 97.4702 71.0648 100.227 69.5745C102.979 68.0882 105.127 65.9869 106.663 63.2823C108.199 60.5738 108.97 57.3725 108.97 53.6745C108.97 49.9764 108.199 46.7712 106.663 44.0666C105.127 41.3621 102.983 39.2647 100.227 37.7744C97.4702 36.2881 94.244 35.543 90.5359 35.543C86.8279 35.543 83.5898 36.2842 80.8097 37.7744C78.0297 39.2607 75.8735 41.3621 74.3374 44.0666C72.7974 46.7712 72.0312 49.9764 72.0312 53.6745C72.0312 57.3725 72.8013 60.5778 74.3374 63.2823C75.8735 65.9869 78.0297 68.0843 80.8097 69.5745ZM94.5362 68.5574C93.4068 70.1383 92.0483 70.9268 90.4688 70.9268C89.0669 70.9268 87.7914 70.2053 86.6383 68.7624C85.4852 67.3194 84.5731 65.2891 83.8938 62.6712C83.2186 60.0534 82.879 57.0335 82.879 53.6035C82.879 50.489 83.1949 47.6267 83.8267 45.0089C84.4585 42.395 85.351 40.2976 86.5041 38.7167C87.6572 37.1357 88.9998 36.3472 90.5359 36.3472C91.9813 36.3472 93.2805 37.0805 94.4335 38.5471C95.5826 40.0138 96.4869 42.0441 97.1425 44.6383C97.798 47.2325 98.1257 50.2209 98.1257 53.6035C98.1257 56.7141 97.8099 59.5922 97.178 62.2297C96.5462 64.8712 95.6656 66.9804 94.5362 68.5574ZM182.322 59.7617C181.101 61.5674 179.51 62.9985 177.543 64.059C175.577 65.1196 173.511 65.6479 171.344 65.6479C167.774 65.6479 164.927 64.2167 162.806 61.3505C160.681 58.4883 159.619 54.6641 159.619 49.8818V49.5428H182.389L182.322 48.5296C182.14 45.9591 181.405 43.6921 180.118 41.7288C178.831 39.7654 177.113 38.2436 174.969 37.1634C172.82 36.0792 170.372 35.5391 167.616 35.5391C164.318 35.5391 161.369 36.296 158.77 37.806C156.172 39.316 154.126 41.4252 152.637 44.1337C151.145 46.8382 150.402 49.9962 150.402 53.6075C150.402 57.2188 151.082 60.2506 152.436 62.9788C153.791 65.707 155.678 67.8399 158.095 69.3735C160.512 70.9072 163.28 71.6759 166.396 71.6759C168.97 71.6759 171.363 71.1792 173.579 70.1857C175.79 69.1922 177.713 67.8162 179.34 66.0579C180.967 64.2995 182.164 62.2692 182.934 59.9667L182.326 59.7657L182.322 59.7617ZM166.869 36.5523C168.946 36.5523 170.562 37.6562 171.715 39.8679C172.797 41.9456 173.365 44.6304 173.429 47.9067H159.722C159.955 44.5437 160.685 41.8431 161.921 39.8009C163.232 37.6365 164.879 36.5523 166.869 36.5523ZM188.27 116.036C190.032 115.09 191.457 113.809 192.547 112.192L193.092 112.374C192.405 114.436 191.335 116.253 189.878 117.826C188.42 119.399 186.703 120.633 184.72 121.52C182.738 122.407 180.598 122.853 178.292 122.853C175.5 122.853 173.024 122.167 170.86 120.795C168.696 119.423 167.005 117.515 165.793 115.07C164.581 112.626 163.973 109.91 163.973 106.681C163.973 103.452 164.64 100.621 165.975 98.2006C167.31 95.7799 169.138 93.8914 171.464 92.5392C173.79 91.1869 176.428 90.5088 179.381 90.5088C181.849 90.5088 184.041 90.9937 185.964 91.9636C187.887 92.9334 189.424 94.2936 190.577 96.0519C191.73 97.8103 192.385 99.8367 192.547 102.139L192.606 103.046H172.222V103.349C172.222 107.631 173.17 111.053 175.073 113.616C176.977 116.178 179.524 117.46 182.718 117.46C184.661 117.46 186.513 116.987 188.27 116.036ZM183.05 94.3882C182.019 92.4091 180.574 91.4195 178.714 91.4195L178.71 91.4234C176.933 91.4234 175.456 92.3894 174.283 94.3291C173.197 96.1189 172.554 98.4845 172.333 101.414H184.582C184.507 98.5594 184.002 96.2136 183.05 94.3882ZM15.8644 78.6424C16.512 76.6633 17.4796 75.6737 18.7748 75.6737C20.07 75.6737 21.0573 76.5608 21.8668 78.3388C22.2696 79.2259 22.5657 80.2588 22.7474 81.4258C22.9291 82.5967 23.0396 83.9096 23.0791 85.3644L30.357 81.2445C29.8713 79.9947 29.0538 78.8908 27.9008 77.9446C26.7477 76.9945 25.3735 76.2493 23.7741 75.7053C22.1748 75.1612 20.4886 74.8892 18.7076 74.8892C16.1172 74.8892 13.8741 75.516 11.9747 76.7658C10.0714 78.0195 8.60632 79.7621 7.57562 82.0054C6.54498 84.2447 6.02765 86.9019 6.02765 89.9692V91.3609H1.48639L0.578125 92.9931H6.03552V116.676C6.03552 118.13 5.58142 119.301 4.66919 120.188C3.76099 121.075 2.57629 121.521 1.11914 121.521V122.128H20.8993V121.521H20.8401C19.0196 121.521 17.5546 121.075 16.441 120.188C15.3274 119.301 14.7745 118.13 14.7745 116.676V92.9931H22.0563L22.9646 91.3609H14.7745V86.7561C14.7745 83.3261 15.1378 80.6176 15.8683 78.6385L15.8644 78.6424ZM196.992 121.521V122.128L196.996 122.124H215.56V121.517H215.501C214.044 121.517 212.871 121.072 211.982 120.185C211.094 119.297 210.648 118.127 210.648 116.672V100.937C211.133 99.7664 211.71 98.7571 212.377 97.9095C213.045 97.0619 214.067 96.2024 215.442 96.2024C216.247 96.2024 217.191 96.364 218.261 96.6873C219.331 97.0106 220.595 97.5744 222.052 98.3826L224.417 91.2979C223.647 90.9746 222.119 90.399 219.58 90.3123C217.799 90.2532 216.757 90.4937 215.481 91.4004C214.21 92.3111 213.384 93.4702 212.555 94.8028C211.726 96.1354 211.046 97.4088 210.521 98.6191L210.462 89.0586H210.158C209.712 89.7446 208.942 90.2965 207.852 90.7223C206.758 91.1442 205.143 91.3571 202.999 91.3571H196.992V91.9642C199.014 91.9642 200.337 92.39 200.965 93.2376C201.593 94.0853 201.905 95.2759 201.905 96.8095V116.676C201.905 118.131 201.458 119.301 200.57 120.188C199.681 121.076 198.509 121.521 197.051 121.521H196.992ZM61.3906 122.128V121.521H61.4499C62.907 121.521 64.0798 121.076 64.9684 120.188C65.8569 119.301 66.3031 118.131 66.3031 116.676V96.8095C66.3031 95.2759 65.9911 94.0853 65.3633 93.2376C64.7354 92.39 63.4125 91.9642 61.3906 91.9642V91.3571H67.397C69.5412 91.3571 71.1563 91.1442 72.2502 90.7223C73.3401 90.2965 74.1101 89.7446 74.5563 89.0586H74.8604L74.9197 98.6191C75.4449 97.4088 76.1241 96.1354 76.9534 94.8028C77.7827 93.4702 78.608 92.3111 79.8795 91.4004C81.155 90.4937 82.1975 90.2532 83.9785 90.3123C86.5176 90.399 88.0459 90.9746 88.8159 91.2979L86.4505 98.3826C84.9933 97.5744 83.7297 97.0106 82.6595 96.6873C81.5894 96.364 80.6456 96.2024 79.84 96.2024C78.4658 96.2024 77.443 97.0619 76.7756 97.9095C76.1083 98.7571 75.5317 99.7664 75.046 100.937V116.672C75.046 118.127 75.4922 119.297 76.3807 120.185C77.2693 121.072 78.4421 121.517 79.8992 121.517H79.9585V122.124H61.3946L61.3906 122.128ZM133.735 95.2325L145.807 123.094V123.086H146.352L157.515 97.7714C158.285 96.0762 159.154 94.7436 160.125 93.7738C161.097 92.8039 162.108 92.1968 163.158 91.9563V91.3491H153.574V91.9563C154.668 92.0785 155.331 92.2559 155.94 92.9025C156.548 93.549 156.848 94.0142 156.848 95.0235C156.848 95.8037 156.484 96.6231 156.124 97.4368L156.077 97.5418L156.02 97.6725L155.971 97.7833L149.886 111.728L143.99 97.9567C143.303 96.3009 143.169 94.9289 143.595 93.8368C144.022 92.7487 144.938 92.1218 146.355 91.9602V91.3531H129.367V91.9602C130.54 92.0036 131.46 92.3229 132.128 92.9301C132.795 93.5372 133.332 94.306 133.735 95.2325ZM126.875 110.998C132.55 110.746 139.433 113.896 140.199 119.143V119.139C141.285 124.572 137.79 129.512 136.234 131.385C127.661 141.793 108.888 143.737 98.1391 137.287C72.8265 122.1 85.6368 89.204 114.219 92.0584C116.75 92.2397 121.687 91.8455 122.291 90.5602C123.302 88.4076 120.549 87.8754 118.934 87.8754C116.742 88.065 114.443 88.7665 112.093 89.4831C108.287 90.6438 104.351 91.8444 100.532 90.9742C91.0192 88.8058 90.806 75.7167 99.6516 71.5929C107.695 67.6228 119.783 71.4628 124.609 78.6697C126.69 81.7787 127.749 83.6778 128.479 84.9868C129.441 86.7121 129.831 87.4122 131.239 88.5062C134.106 90.8047 137.36 87.4141 135.776 84.4415C135.377 83.632 134.397 83.0024 133.414 82.3708C131.726 81.286 130.029 80.1952 131.247 78.1769C133.632 75.5275 137.735 78.7446 138.351 81.2757C139.792 84.82 137.652 90.1108 133.537 90.1187C129.979 90.1237 128.254 87.1456 126.228 83.6483C123.501 78.9404 120.228 73.2917 111.206 72.7126C107.64 72.7559 102.981 73.505 100.856 77.5934C99.0553 81.1653 99.853 85.2655 103.229 87.2328C107.354 89.6353 111.132 88.1791 114.947 86.7085C117.149 85.86 119.363 85.0067 121.663 84.887C129.419 85.5493 127.357 95.1256 120.486 94.621C119.19 94.4548 117.957 94.2636 116.774 94.0803C109.571 92.9637 104.246 92.1382 97.9338 99.0129C84.6496 114.641 98.7867 134.487 116.541 135.777C120.711 136.096 126.753 135.583 130.268 133.593C137.605 129.934 141.956 119.608 132.973 115.82C128.013 113.604 121.971 113.462 116.727 115.075C113.891 115.851 110.886 116.648 107.893 116.352C103.711 115.95 99.1974 112.567 99.2408 108.175C99.3831 104.816 102.033 101.792 105.847 101.371C108.126 101.248 110.002 104.055 108.552 105.979C107.79 107.04 106.614 107.501 105.476 107.943C103.719 108.546 101.717 109.658 102.404 111.964C104.675 117.121 111.881 115.228 116.044 113.375C119.491 111.929 123.333 110.959 126.875 110.998Z" fill="white"></path>
						</svg>
					</div>
				</a>
			</div>
		</div>
	</header>
	<div class="fixed-navbar menuline lf-fixed-navbar">
		<div class="vert-menu">
			<div class="spleet m-none">
				<?php if ( ! empty( $left_menu ) ) : ?>
					<div class="menu-line p-12-12 white uper n-voreder">
						<?php foreach ( $left_menu as $left_menu_item ) : ?>
							<?php get_template_part( 'components/navbar-' . $left_menu_item['acf_fc_layout'], null, $left_menu_item ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="l-spacer">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="w-inline-block">
						<div class="code-embed-4 pink-svg w-embed">
							<svg width="100%" height="100%" viewbox="0 0 203 40" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M27.2869 10.3459C29.2653 12.3426 29.0098 13.2662 28.4221 14.0802C28.1301 14.4817 25.7501 15.9528 24.1112 11.4301C23.6403 10.2875 22.8264 4.374 26.2685 2.70945C30.1596 0.924447 32.0029 4.61857 32.5322 5.79397C32.896 6.60329 34.9021 13.1247 36.7723 19.2044L36.7724 19.2047L36.773 19.2067L36.7733 19.2076L36.7738 19.2092C38.4121 24.535 39.9458 29.5206 40.1793 30.0284C40.683 31.1162 41.5335 31.1272 41.9934 30.6782C42.4534 30.2292 47.9286 19.03 48.4469 17.6502C48.9653 16.2704 53.0973 5.21722 39.6318 1.28948C41.57 -0.798503 46.9905 -0.0173352 49.6771 1.28948C52.3636 2.59629 56.5649 2.02684 56.5649 2.02684C56.5649 2.02684 53.3966 4.47986 49.772 2.662C46.7861 1.16537 45.0778 1.25663 45.0778 1.25663C53.2907 4.12943 53.9806 8.60837 49.5091 18.0189C46.0707 25.2575 43.8587 29.4042 42.7271 31.2111C41.5956 33.0181 39.0879 36.2997 36.7554 32.7406C35.4328 30.7245 32.846 22.1737 30.657 14.9381L30.657 14.9381C28.9858 9.41398 27.5466 4.65644 27.0788 4.15863C26.2721 3.30081 25.3413 4.69888 25.3048 5.84143C25.2683 6.98398 25.3048 8.3492 27.2796 10.3459H27.2869ZM0 32.507V32.1748C0.803039 32.1748 1.45277 31.9376 1.95284 31.4557C2.45292 30.9775 2.70478 30.3241 2.70478 29.5028V10.4627C2.70478 9.65967 2.45292 9.01722 1.95284 8.52442C1.45277 8.03528 0.799389 7.79071 0 7.79071V7.45488H3.40561C4.54082 7.45488 5.40956 7.33442 6.01184 7.0862C6.56667 6.84163 6.95724 6.5423 7.1799 6.18457H7.51571V29.4992C7.51571 30.3241 7.76758 30.9739 8.26765 31.4521C8.76772 31.9303 9.42111 32.1712 10.2205 32.1712V32.507H0ZM20.1689 32.9778C18.3438 32.9778 16.7451 32.6091 15.3762 31.8754C14.0074 31.1417 12.9416 30.105 12.186 28.769C11.4304 27.433 11.0508 25.8524 11.0508 24.0272C11.0508 22.2021 11.4304 20.6215 12.186 19.2855C12.9416 17.9495 14.0074 16.9128 15.3762 16.1791C16.7451 15.4453 18.3438 15.0767 20.1689 15.0767C21.994 15.0767 23.5855 15.4453 24.947 16.1791C26.3049 16.9128 27.3634 17.9495 28.119 19.2855C28.8746 20.6215 29.2542 22.2021 29.2542 24.0272C29.2542 25.8524 28.8746 27.433 28.119 28.769C27.3634 30.105 26.3049 31.1417 24.947 31.8754C23.5891 32.6091 21.9977 32.9778 20.1689 32.9778ZM20.138 32.5434C20.9146 32.5428 21.582 32.1522 22.14 31.3753C22.6948 30.5978 23.1292 29.5538 23.4431 28.2507C23.7534 26.9475 23.9104 25.5275 23.9104 23.9907C23.9104 22.3189 23.7497 20.8442 23.4249 19.5666C23.1037 18.2853 22.6583 17.2851 22.0889 16.5587C21.5195 15.8359 20.8807 15.4745 20.1689 15.4745C19.4133 15.4745 18.749 15.8651 18.1832 16.6426C17.6138 17.4238 17.1758 18.4569 16.8655 19.7491C16.5552 21.0413 16.3983 22.454 16.3983 23.9907C16.3983 25.6845 16.5662 27.1738 16.8984 28.466C17.2342 29.7582 17.6832 30.7584 18.2526 31.4739C18.8215 32.1851 19.4488 32.5428 20.138 32.5434ZM63.7651 29.1559C64.7324 28.6339 65.5172 27.9258 66.1195 27.0351H66.1304L66.4298 27.1337C66.0501 28.2726 65.4625 29.2727 64.6594 30.1415C63.8564 31.0103 62.911 31.6893 61.8196 32.1784C60.7282 32.6675 59.5492 32.9121 58.2789 32.9121C56.7422 32.9121 55.377 32.5325 54.1871 31.7769C52.9971 31.0176 52.0663 29.9663 51.3983 28.6193C50.7303 27.2724 50.3945 25.7757 50.3945 23.9944C50.3945 22.213 50.7632 20.6543 51.4969 19.3183C52.2306 17.9823 53.238 16.9383 54.5192 16.1936C55.8004 15.4453 57.2532 15.073 58.8775 15.073C60.2354 15.073 61.4436 15.3431 62.5022 15.8761C63.5571 16.4127 64.4039 17.1646 65.039 18.132C65.6705 19.1029 66.0355 20.2199 66.1231 21.4903L66.156 21.9904H54.9317V22.1583C54.9317 24.52 55.4537 26.4072 56.5013 27.8199C57.5489 29.2326 58.9506 29.9407 60.7099 29.9407C61.7794 29.9407 62.7978 29.6779 63.7651 29.1559ZM60.8924 17.2121C60.3267 16.1243 59.5309 15.5767 58.5052 15.5767C57.527 15.5767 56.713 16.1097 56.0669 17.1792C55.4573 18.1867 55.096 19.5191 54.9828 21.18H61.7393C61.7064 19.5629 61.4254 18.2378 60.8924 17.2121ZM95.57 31.8754C96.9461 32.6164 98.5522 32.9851 100.388 32.9851C102.221 32.9851 103.823 32.6128 105.188 31.8754C106.553 31.138 107.616 30.0977 108.378 28.7544C109.141 27.411 109.521 25.8232 109.521 23.9871C109.521 22.1509 109.138 20.5631 108.378 19.2197C107.616 17.8764 106.553 16.8361 105.188 16.0987C103.823 15.3577 102.224 14.989 100.388 14.989C98.5522 14.989 96.9461 15.3613 95.57 16.0987C94.1939 16.8361 93.1244 17.8764 92.3652 19.2197C91.6023 20.5631 91.2227 22.1509 91.2227 23.9871C91.2227 25.8232 91.6023 27.411 92.3652 28.7544C93.1244 30.094 94.1939 31.138 95.57 31.8754ZM102.37 31.3716C101.812 32.1565 101.14 32.5471 100.355 32.5471L100.363 32.5434C99.6692 32.5434 99.034 32.1857 98.4646 31.4702C97.8915 30.7547 97.4389 29.7473 97.1031 28.4477C96.7673 27.1482 96.5994 25.6479 96.5994 23.9469C96.5994 22.4028 96.7563 20.9828 97.0702 19.6833C97.3805 18.3875 97.8258 17.3471 98.3952 16.5623C98.9647 15.7775 99.629 15.3869 100.392 15.3869C101.107 15.3869 101.753 15.7519 102.323 16.4783C102.892 17.2084 103.341 18.2122 103.666 19.5008C103.987 20.7894 104.152 22.2714 104.152 23.9505C104.152 25.4946 103.995 26.9219 103.681 28.2324C103.367 29.5428 102.929 30.5868 102.37 31.3716ZM185.336 27.0095C184.73 27.9038 183.942 28.6157 182.971 29.1413C181.996 29.6669 180.974 29.9298 179.897 29.9298C178.131 29.9298 176.718 29.218 175.667 27.798C174.616 26.378 174.09 24.4798 174.09 22.1071V21.9392H185.373L185.34 21.4355C185.249 20.1579 184.887 19.0336 184.249 18.0626C183.61 17.0879 182.759 16.3323 181.697 15.7957C180.635 15.2591 179.419 14.989 178.054 14.989C176.419 14.989 174.959 15.365 173.674 16.1133C172.385 16.8653 171.374 17.9093 170.637 19.2526C169.9 20.5959 169.527 22.1619 169.527 23.9542C169.527 25.7465 169.863 27.2504 170.535 28.6047C171.206 29.959 172.141 31.0176 173.338 31.7768C174.535 32.5361 175.908 32.9194 177.452 32.9194C178.729 32.9194 179.912 32.6712 181.011 32.182C182.106 31.6892 183.059 31.0066 183.865 30.1342C184.672 29.2618 185.263 28.2543 185.647 27.1117L185.344 27.0095H185.336ZM177.682 15.4928C178.711 15.4928 179.511 16.0403 180.084 17.1391C180.609 18.1502 180.89 19.4497 180.93 21.0339H174.152C174.273 19.4096 174.63 18.0991 175.233 17.1062C175.882 16.033 176.7 15.4928 177.682 15.4928ZM87.7461 6.7613C87.027 6.7613 86.4904 7.3125 86.1327 8.4076V8.4149C85.7276 9.51364 85.5268 11.0139 85.5268 12.9157V15.4673H90.0603L89.5566 16.3726H85.5268V29.5028C85.5268 30.3095 85.8334 30.9593 86.4503 31.4521C87.0672 31.9449 87.8775 32.1894 88.885 32.1894H88.9178V32.5253H77.9709V32.1894C78.7776 32.1894 79.431 31.9449 79.9347 31.4521C80.4385 30.9593 80.6903 30.3095 80.6903 29.5028V16.3726H77.668L78.1717 15.4673H80.6903V14.6934C80.6903 12.9924 80.9787 11.5177 81.5481 10.2766C82.1212 9.0318 82.9315 8.06447 83.9828 7.37091C85.034 6.67735 86.2751 6.33057 87.7096 6.33057C88.6952 6.33057 89.6296 6.48023 90.5129 6.78321C91.3999 7.08253 92.1592 7.49867 92.798 8.02431C93.4367 8.54996 93.8894 9.15956 94.1595 9.85312L90.1297 12.1346C90.1041 11.3279 90.0457 10.6014 89.9435 9.95168C89.845 9.30193 89.6807 8.73248 89.458 8.23968C89.0091 7.2541 88.4652 6.7613 87.7461 6.7613ZM187.441 32.5143V32.5106H197.713V32.1748H197.68C196.873 32.1748 196.223 31.9302 195.731 31.4374C195.238 30.9446 194.993 30.2949 194.993 29.4881V20.7639C195.26 20.1141 195.581 19.5556 195.95 19.0847C196.322 18.6175 196.884 18.1393 197.647 18.1393C198.096 18.1393 198.614 18.2306 199.209 18.4094C199.804 18.5919 200.501 18.9022 201.308 19.3512L202.619 15.4235C202.195 15.2446 201.348 14.9234 199.943 14.8759C198.957 14.8431 198.381 14.9745 197.676 15.4782C196.972 15.982 196.516 16.6281 196.056 17.3654C195.596 18.1028 195.223 18.8073 194.931 19.479L194.898 14.1787H194.731C194.482 14.5583 194.059 14.8686 193.453 15.1022C192.847 15.3395 191.953 15.4563 190.766 15.4563H187.441V15.7922C188.558 15.7922 189.292 16.0258 189.639 16.4967C189.985 16.9676 190.161 17.6283 190.161 18.4788V29.4918C190.161 30.2985 189.916 30.9483 189.423 31.4411C188.93 31.9339 188.281 32.1784 187.474 32.1784H187.441V32.5106H187.438L187.441 32.5143ZM110.976 32.5106V32.5143L110.973 32.5106H110.976ZM110.976 32.5106V32.1784H111.009C111.816 32.1784 112.466 31.9339 112.958 31.4411C113.451 30.9483 113.696 30.2985 113.696 29.4918V18.4788C113.696 17.6283 113.52 16.9676 113.174 16.4967C112.827 16.0258 112.093 15.7922 110.976 15.7922V15.4563H114.302C115.488 15.4563 116.382 15.3395 116.988 15.1022C117.594 14.8686 118.018 14.5583 118.266 14.1787H118.434L118.466 19.479C118.758 18.8073 119.131 18.1028 119.591 17.3654C120.051 16.6281 120.507 15.982 121.211 15.4782C121.916 14.9745 122.493 14.8431 123.478 14.8759C124.883 14.9234 125.73 15.2446 126.154 15.4235L124.843 19.3512C124.037 18.9022 123.339 18.5919 122.744 18.4094C122.149 18.2306 121.631 18.1393 121.182 18.1393C120.419 18.1393 119.857 18.6175 119.485 19.0847C119.113 19.552 118.795 20.1141 118.529 20.7639V29.4881C118.529 30.2949 118.773 30.9446 119.266 31.4374C119.759 31.9302 120.408 32.1748 121.215 32.1748H121.248V32.5106H110.976ZM159.823 33.0508L153.143 17.6063C152.92 17.0916 152.621 16.6646 152.252 16.3287C151.883 15.9929 151.372 15.814 150.723 15.7921V15.4563H160.126V15.7921C159.341 15.8834 158.833 16.2302 158.596 16.8325C158.362 17.4384 158.432 18.1977 158.815 19.1176L162.078 26.754L165.447 19.0227C165.476 18.9564 165.505 18.8901 165.535 18.8237C165.733 18.3741 165.933 17.9226 165.933 17.4932C165.933 16.9347 165.765 16.6755 165.429 16.3178C165.093 15.96 164.725 15.8615 164.122 15.7958V15.4599H169.426V15.7958C168.842 15.9308 168.284 16.2667 167.747 16.8033C167.21 17.3399 166.729 18.0809 166.302 19.019L160.126 33.0545H159.823V33.0508ZM147.9 24.3667C150.849 24.2353 154.427 25.8779 154.825 28.6084V28.6157C155.387 31.4447 153.569 34.0181 152.762 34.9928C148.305 40.4135 138.545 41.4246 132.96 38.0663C119.805 30.1598 126.463 13.0325 141.319 14.5181C142.633 14.613 145.199 14.405 145.513 13.737C146.039 12.6163 144.608 12.3389 143.768 12.3389C142.631 12.4378 141.437 12.8027 140.217 13.1757C138.238 13.7804 136.192 14.4062 134.205 13.9523C129.259 12.8244 129.149 6.00924 133.745 3.86286C137.928 1.79312 144.21 3.7935 146.717 7.54603C147.799 9.16429 148.349 10.1533 148.729 10.8349C149.23 11.734 149.432 12.0986 150.163 12.6674C151.653 13.8647 153.343 12.098 152.521 10.5502C152.314 10.1281 151.805 9.79955 151.294 9.46993C150.417 8.90455 149.535 8.33606 150.167 7.28686C151.408 5.90703 153.54 7.58253 153.861 8.9003C154.613 10.7437 153.5 13.4997 151.361 13.5033C149.511 13.5049 148.614 11.954 147.561 10.1331C146.143 7.68183 144.442 4.74121 139.753 4.43961C137.899 4.46151 135.479 4.85209 134.373 6.98023C133.438 8.83824 133.851 10.9737 135.606 11.9994C137.75 13.2496 139.715 12.4911 141.698 11.7252C142.843 11.2835 143.993 10.8393 145.188 10.7766C149.221 11.1197 148.148 16.106 144.578 15.8432C143.901 15.7567 143.256 15.657 142.639 15.5613C138.9 14.9827 136.134 14.5545 132.858 18.132C125.952 26.2685 133.303 36.6026 142.531 37.2742C144.695 37.4385 147.838 37.172 149.663 36.1353C153.478 34.2299 155.737 28.8529 151.068 26.8818C148.488 25.7246 145.349 25.6516 142.622 26.4912C141.147 26.8927 139.585 27.3089 138.03 27.1555C135.858 26.9475 133.511 25.1844 133.533 22.8993C133.606 21.1471 134.986 19.5738 136.968 19.3548C138.151 19.2928 139.125 20.7565 138.373 21.7567C137.979 22.3079 137.366 22.5488 136.774 22.7788C135.862 23.0927 134.822 23.6695 135.179 24.8704C136.358 27.5534 140.103 26.5678 142.268 25.6042C144.064 24.8522 146.06 24.3448 147.9 24.3667Z" fill="#801F80"></path>
							</svg>
						</div>
					</a>
				</div>
				<div class="menu-line p-12-12 white uper rev n-voreder">
					<?php if ( ! empty( $right_menu ) ) : ?>
						<?php foreach ( $right_menu as $right_menu_item ) : ?>
							<?php get_template_part( 'components/navbar-' . $right_menu_item['acf_fc_layout'], null, $right_menu_item ); ?>
						<?php endforeach; ?>
					<?php endif; ?>
					<div class="horiz lf-hidden-phone">
						<div id="fixedHeaderPhoneNumber" data-js-phone-number="<?php echo esc_attr( PHONE ); ?>"><?php echo esc_html( loveforever_mask_phone( PHONE ) ); ?></div>
						<button type="button" class="show-all-btn phone-button uppercase" data-js-phone-number-button="fixedHeaderPhoneNumber">Показать</button>
					</div>
					<div class="div-block-5 wh-head">
						<div class="menu-link-keeper">
							<a href="#" class="lf-icon-button lf-icon-button--search">
								<div class="lf-icon-button__icon-wrapper">
									<svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 16 16" class="lf-icon-button__icon">
										<use href="#searchIcon" />
									</svg>
								</div>
							</a>
							<div class="hovered-menue search-m" style="grid-template-columns: repeat(6, auto);">
								<div id="w-node-_1716cbec-a8d5-9533-681b-95848935b87a-be61d3ef" class="div-block-6">
									<div class="div-block-7">
										<form id="searchForm3" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="lf-search-form search" data-js-search-form>
											<input 
												class="lf-search-form__search-input search-input w-input" 
												maxlength="256" 
												name="s" 
												placeholder="Напишите, что вы ищите..." 
												type="search" 
												id="searchForm3SearchControl" 
												required
												data-js-search-form-search-input
											>
											<input type="submit" class="search-button w-button" value="Search">
											<button type="reset" class="ls-search-form__reset clear-search">Очистить</button>
										</form>
										<div class="search-ajaxed" data-js-search-form-results></div>
									</div>
								</div>
								<?php foreach ( $search_links as $search_column ) : ?>
									<div id="w-node-_1716cbec-a8d5-9533-681b-95848935b8c4-be61d3ef" class="m-h-vert">
										<div class="p-16-16"><?php echo esc_html( $search_column['column_name'] ); ?></div>
										<?php
										$column_links = $search_column['column_links'];
										if ( ! empty( $column_links ) ) :
											?>
											<div id="w-node-_1716cbec-a8d5-9533-681b-95848935b8c7-be61d3ef" class="m-h-vert">
												<?php
												foreach ( $column_links as $column_link ) :
													$column_link_attributes_str = loveforever_prepare_link_attributes( array( 'class' => 'a-12-12 w-inline-block' ), $column_link['link'] );
													?>
													<a <?php echo $column_link_attributes_str; ?>>
														<div><?php echo esc_html( $column_link['link']['title'] ); ?></div>
													</a>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
								<!-- <div id="w-node-_1716cbec-a8d5-9533-681b-95848935b950-be61d3ef" class="div-block-6 _3">
									<a href="#" class="btn in-single-btn _3 w-inline-block">
										<div>перейти в каталог</div>
									</a>
								</div> -->
								<div id="w-node-_1716cbec-a8d5-9533-681b-95848935b954-be61d3ef" class="hovered-menue_close-menu"></div>
							</div>
						</div>
						<a href="<?php echo esc_url( home_url( '/' ) . 'favorites' ); ?>" class="lf-icon-button lf-icon-button--favorites <?php echo 0 < count( $favorites ) ? 'is-active' : ''; ?>" data-js-favorites-button>
							<div class="lf-icon-button__icon-wrapper">
								<svg xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon" viewBox="0 0 16 16">
									<use href="#heartIcon"/>
								</svg>
							</div>
							<span class="lf-icon-button__counter" data-js-favorites-button-counter>
								<?php echo esc_html( (string) count( $favorites ) ); ?>
							</span>
						</a>
					</div>
				</div>
			</div>
			<div class="spleet pc-none">
				<div class="menu-line p-12-12 white uper n-voreder">
					<a href="<?php echo esc_url( home_url( '/' ) . 'favorites' ); ?>" class="lf-icon-button lf-icon-button--favorites <?php echo 0 < count( $favorites ) ? 'is-active' : ''; ?>" data-js-favorites-button>
						<div class="lf-icon-button__icon-wrapper">
							<svg viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
								<use href="#heartIcon"  />
							</svg>
						</div>
						<div data-js-favorites-button-counter class="lf-icon-button__counter"><?php echo esc_html( (string) count( $favorites ) ); ?></div>
					</a>
				</div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( 'Перейти на главную ' . get_bloginfo( 'name' ) ); ?>" class="lf-scroll-navbar-logo lf-scroll-navbar-logo--mobile w-inline-block">
					<svg width="177" height="35" class="lf-scroll-navbar-logo__icon">
						<use href="#scrollNavbarLogo" />
					</svg>
				</a>
				<div class="menu-line p-12-12 white uper rev n-voreder">
					<div class="div-block-5 wh-head">
						<div class="menu-link-keeper">
							<a href="#" class="lf-icon-button lf-icon-button--search">
								<div class="lf-icon-button__icon-wrapper">
									<svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 16 16" class="lf-icon-button__icon">
										<use href="#searchIcon" />
									</svg>
								</div>
							</a>
							<div class="hovered-menue search-m">
								<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc090-be61d3ef" class="div-block-6">
									<div class="div-block-7">
										<form id="searchForm4" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search" data-js-search-form>
											<input 
												class="search-input w-input" 
												maxlength="256" 
												name="s" 
												placeholder="Напишите, что вы ищите..." 
												type="search" 
												id="searchForm4SearchControl" 
												required
												data-js-search-form-search-input
											>
											<input type="submit" class="search-button w-button" value="Search">
											<button type="reset" class="clear-search">Очистить</button>
										</form>
										<div class="search-ajaxed" data-js-search-form-results></div>
									</div>
								</div>
								<?php if ( ! empty( $search_links ) ) : ?>
									<div class="serch-mob">
										<div class="m-nav-cats">
											<?php foreach ( $search_links as $search_column ) : ?>
												<div class="m-nav-drops">
													<a href="#" class="m-nav-drop-btn w-inline-block">
														<div><?php echo esc_html( $search_column['column_name'] ); ?></div>
														<img src="<?php echo TEMPLATE_PATH; ?>/images/673dc9a4d3949ca7d7c90f76_Union.svg" loading="eager" alt class="image-6-drop">
													</a>
													<?php $column_links = $search_column['column_links']; ?>
													<div class="m-nav-drop-contant">
														<?php if ( ! empty( $column_links ) ) : ?>
															<div class="div-block-11">
																<?php
																foreach ( $column_links as $column_link ) :
																	$column_link_attributes_str = loveforever_prepare_link_attributes( array( 'class' => 'a-12-12 in-drop' ), $column_link['link'] );
																	?>
																	<a <?php echo $column_link_attributes_str; ?>>
																		<?php echo esc_html( $column_link['link']['title'] ); ?>
																	</a>
																<?php endforeach; ?>
															</div>
														<?php endif; ?>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endif; ?>
								<div class="vert m-none">
									<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc128-be61d3ef" class="m-h-vert">
										<div class="p-16-16">силуэт 2</div>
										<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc12b-be61d3ef" class="m-h-vert">
											<a href="#" class="a-12-12 w-inline-block">
												<div>а-силуэт</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>греческий стиль</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>пышные</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>короткие</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>со шлейфом</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>трансформеры</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>прямые</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>комбинизон / костюм</div>
											</a>
										</div>
									</div>
									<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc144-be61d3ef" class="m-h-vert">
										<div class="p-16-16">стиль</div>
										<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc147-be61d3ef" class="m-h-vert">
											<a href="#" class="a-12-12 w-inline-block">
												<div>открытые</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>закрытые</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>с рукавами</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>с открытой спиной</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>пляжные / на море</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>минимализм / простые</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>блестящие</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>бохо / рустик</div>
											</a>
										</div>
									</div>
									<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc160-be61d3ef" class="m-h-vert">
										<div class="p-16-16">ткань</div>
										<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc163-be61d3ef" class="m-h-vert">
											<a href="#" class="a-12-12 w-inline-block">
												<div>Фатин</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Атлас / Сатин</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Шифон</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>кружевные</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>креп</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>шелк</div>
											</a>
										</div>
									</div>
									<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc176-be61d3ef" class="m-h-vert">
										<div class="p-16-16">топ бренды</div>
										<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc179-be61d3ef" class="m-h-vert grider">
											<a href="#" class="a-12-12 w-inline-block">
												<div>AVE</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>paulain</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>tatiana Kaplun</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>love forever</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Divino Rose</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Rima Lav</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>paulain</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>Divino Rose</div>
											</a>
											<a id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc192-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>Kookla</div>
											</a>
											<a id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc195-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>S. markelova</div>
											</a>
											<a id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc198-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>milva</div>
											</a>
											<a id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc19b-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>Divino Rose</div>
											</a>
											<a id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc19e-be61d3ef" href="#" class="a-12-12 w-inline-block">
												<div>S. markelova</div>
											</a>
										</div>
									</div>
									<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc1a1-be61d3ef" class="m-h-vert">
										<div class="p-16-16">стоимость</div>
										<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc1a4-be61d3ef" class="m-h-vert">
											<a href="#" class="a-12-12 w-inline-block">
												<div>10 000 ₽ - 20 000 ₽</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>20 000 ₽ - 30 000 ₽</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>30 000 ₽ - 40 000 ₽</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>40 000 ₽ - 50 000 ₽</div>
											</a>
											<a href="#" class="a-12-12 w-inline-block">
												<div>50 000 ₽ - 100 000 ₽</div>
											</a>
										</div>
									</div>
									<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc1b4-be61d3ef" class="div-block-6 _3">
										<a href="#" class="btn in-single-btn _3 w-inline-block">
											<div>перейти в каталог</div>
										</a>
									</div>
								</div>
								<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc1b8-be61d3ef" class="hovered-menue_close-menu"></div>
							</div>
						</div>
						<div class="menu-link-keeper">
							<button type="button" class="lf-burger-button menu-bnt w-inline-block">
								<div class="lf-burger-button__line b-line"></div>
								<div class="lf-burger-button__line b-line"></div>
								<div class="lf-burger-button__line b-line"></div>
							</button>
							<?php
							if ( ! empty( $mobile_menu_items ) ) :
								?>
								<div class="hovered-menue mob-menue">
									<div class="mob-menu-kee">
										<div class="m-nav-keep">
											<div class="m-nav-top">
												<?php foreach ( $mobile_menu_items as $mobile_menu_item ) : ?>
													<?php
													if ( 'dress-category' === $mobile_menu_item['acf_fc_layout'] || 'dress-category-with-images' === $mobile_menu_item['acf_fc_layout'] ) :
														$dress_category = get_term( $mobile_menu_item['category'] );
														?>
														<a href="#" class="m-nav-drop w-inline-block">
															<div><?php echo esc_html( $dress_category->name ); ?></div>
															<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc676af0eceedf43e40c1_Union.svg' ); ?>" loading="eager" alt class="image-6">
														</a>
													<?php else : ?>
														<?php $link = $mobile_menu_item['link']; ?>
														<a href="<?php echo esc_url( $link['url'] ); ?>" class="m-nav-a w-inline-block">
															<div>
																<?php echo esc_html( $link['title'] ); ?>
																<?php if ( ! empty( $mobile_menu_item['green_badge'] ) ) : ?>
																	<span class="indirim-span"><?php echo esc_html( $mobile_menu_item['green_badge'] ); ?></span>
																<?php endif; ?>
															</div>
														</a>
													<?php endif; ?>
												<?php endforeach; ?>
											</div>
											<div id="w-node-_34c51008-eae1-f821-4ba8-4e111ca07545-be61d3ef" class="div-block-4 cont-item">
												<div class="p-12-12 uper m-12-12">Наши группы в социальных сетях</div>
												<div class="soc-grid mpb">
													<a href="#" class="soc-btn w-inline-block">
														<div class="svg-share w-embed">
															<svg width="16" height="10" viewbox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path d="M8.71455 10C3.24797 10 0.129919 6.24625 0 0H2.73829C2.82823 4.58458 4.84697 6.52653 6.44597 6.92693V0H9.02436V3.95395C10.6034 3.78378 12.2623 1.98198 12.822 0H15.4004C15.1895 1.02791 14.7691 2.00118 14.1655 2.85893C13.562 3.71668 12.7882 4.44045 11.8926 4.98498C12.8923 5.48254 13.7753 6.18678 14.4833 7.05125C15.1913 7.91571 15.7082 8.92073 16 10H13.1618C12.8999 9.06258 12.3676 8.22343 11.6316 7.58773C10.8956 6.95203 9.9886 6.54805 9.02436 6.42643V10H8.71455Z" fill="black"></path>
															</svg>
														</div>
													</a>
													<a href="#" class="soc-btn w-inline-block">
														<div class="svg-share w-embed">
															<svg width="14" height="13" viewbox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path d="M14 0.460525L11.7855 12.4588C11.7855 12.4588 11.4756 13.2907 10.6244 12.8917L5.51495 8.68138L5.49126 8.66897C6.18143 8.00295 11.5333 2.83145 11.7673 2.59703C12.1294 2.23398 11.9046 2.01785 11.4841 2.2921L3.57869 7.68757L0.528786 6.5847C0.528786 6.5847 0.0488212 6.40122 0.00264736 6.00226C-0.044134 5.60264 0.544582 5.38651 0.544582 5.38651L12.9781 0.144489C12.9781 0.144489 14 -0.338054 14 0.460525Z" fill="black"></path>
															</svg>
														</div>
													</a>
													<a href="#" class="soc-btn w-inline-block">
														<div class="svg-share w-embed">
															<svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path d="M13.6585 2.33333C12.1533 0.833333 10.1463 0 8.02787 0C3.62369 0 0.0557483 3.55556 0.0557483 7.94444C0.0557483 9.33333 0.445993 10.7222 1.11498 11.8889L0 16L4.23694 14.8889C5.40767 15.5 6.68989 15.8333 8.02787 15.8333C12.4321 15.8333 16 12.2778 16 7.88889C15.9443 5.83333 15.1638 3.83333 13.6585 2.33333ZM11.8746 10.7778C11.7073 11.2222 10.9268 11.6667 10.5366 11.7222C10.2021 11.7778 9.7561 11.7778 9.31011 11.6667C9.03136 11.5556 8.64112 11.4444 8.19512 11.2222C6.18815 10.3889 4.90592 8.38889 4.79443 8.22222C4.68293 8.11111 3.95819 7.16667 3.95819 6.16667C3.95819 5.16667 4.45993 4.72222 4.62718 4.5C4.79443 4.27778 5.01742 4.27778 5.18467 4.27778C5.29617 4.27778 5.46341 4.27778 5.57491 4.27778C5.68641 4.27778 5.85366 4.22222 6.02091 4.61111C6.18815 5 6.5784 6 6.63415 6.05556C6.68989 6.16667 6.68989 6.27778 6.63415 6.38889C6.5784 6.5 6.52265 6.61111 6.41115 6.72222C6.29965 6.83333 6.18815 7 6.1324 7.05556C6.0209 7.16667 5.90941 7.27778 6.02091 7.44444C6.1324 7.66667 6.52265 8.27778 7.13589 8.83333C7.91638 9.5 8.52962 9.72222 8.75261 9.83333C8.97561 9.94445 9.08711 9.88889 9.1986 9.77778C9.3101 9.66667 9.70035 9.22222 9.81185 9C9.92335 8.77778 10.0906 8.83333 10.2578 8.88889C10.4251 8.94444 11.4286 9.44445 11.5958 9.55556C11.8188 9.66667 11.9303 9.72222 11.9861 9.77778C12.0418 9.94444 12.0418 10.3333 11.8746 10.7778Z" fill="black"></path>
															</svg>
														</div>
													</a>
													<a href="#" class="soc-btn w-inline-block">
														<div class="svg-share w-embed">
															<svg width="100%" height="100%" viewbox="0 0 40 60" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path d="M23.53 22H16.4282C14.0052 22 12 24.0052 12 26.4282V33.5718C12 35.9948 14.0052 38 16.4282 38H23.53C25.953 38 27.9582 35.9948 28 33.5718V26.47C28 24.0052 25.9948 22 23.53 22ZM19.9791 33.7807C17.8903 33.7807 16.2193 32.1097 16.2193 30.0209C16.2193 27.9321 17.8903 26.3029 19.9373 26.3029C21.9843 26.3029 23.6554 27.9739 23.6554 30.0209C23.6554 32.0679 22.0261 33.7807 19.9791 33.7807ZM25.8277 25.9269C25.4935 26.47 24.7833 26.6789 24.282 26.3446C23.7389 26.0104 23.53 25.3003 23.8642 24.799C24.1984 24.2559 24.9086 24.047 25.4099 24.3812C25.953 24.6736 26.1201 25.3838 25.8277 25.9269Z" fill="black"></path>
															</svg>
														</div>
													</a>
													<a href="#" class="soc-btn w-inline-block">
														<div class="svg-share w-embed">
															<svg width="100%" height="100%" viewbox="0 0 40 60" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path fill-rule="evenodd" clip-rule="evenodd" d="M12.1596 36.5864C11.7852 36.478 11.4436 36.2853 11.1635 36.0245C10.8835 35.7637 10.6733 35.4424 10.5507 35.0878C9.85006 33.2549 9.64246 25.6011 10.9919 24.0902C11.441 23.5987 12.0751 23.2961 12.7565 23.248C16.3766 22.8765 27.5612 22.926 28.8458 23.3719C29.2071 23.4838 29.5372 23.6722 29.8113 23.9232C30.0854 24.1741 30.2965 24.481 30.4287 24.8209C31.1943 26.7158 31.2202 33.6016 30.3249 35.4222C30.0875 35.8961 29.6899 36.28 29.1961 36.5121C27.8467 37.1561 13.9502 37.1437 12.1596 36.5864ZM17.9077 32.9948L24.3953 29.7748L17.9077 26.53V32.9948Z" fill="black"></path>
															</svg>
														</div>
													</a>
												</div>
											</div>
											<div class="mob-work-time m-12-12">
												<?php if ( ! empty( WORKING_HOURS ) ) : ?>
													<div><?php echo esc_html( WORKING_HOURS ); ?></div>
												<?php endif; ?>
												<?php if ( ! empty( ADDRESS ) ) : ?>
													<div><?php echo esc_html( ADDRESS ); ?></div>
												<?php endif; ?>
											</div>
										</div>
										<?php if ( ! empty( $only_catalog_items ) ) : ?>
											<div class="m-nav-content">
												<?php
												foreach ( $only_catalog_items as $only_catalog_item ) :
													$dress_category = get_term( $only_catalog_item['category'] );
													?>
													<div class="m-nav-content_in">
														<a href="#" class="m-nav-content_back w-inline-block">
															<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc840b23caa30509cbdf5_Union.svg' ); ?>" loading="eager" alt class="image-7">
															<div class="p-12-12 uper m-12-12"><?php echo esc_html( $dress_category->name ); ?></div>
														</a>
														<a href="<?php echo esc_url( get_term_link( $dress_category ) ); ?>" class="m-nav-a long w-inline-block">
															<div>СМОТРЕТЬ ВСЕ</div>
															<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc676af0eceedf43e40c1_Union.svg' ); ?>" loading="eager" alt class="image-6">
														</a>
														<?php
														if ( ! empty( $only_catalog_item['dropdown_menu_columns'] ) ) :
															$mobile_dropdown_menu_columns = $only_catalog_item['dropdown_menu_columns'];
															?>
															<div class="m-nav-cats">
																<?php foreach ( $mobile_dropdown_menu_columns as $mobile_dropdown_menu_column ) : ?>
																	<?php
																	if ( 'price' !== $mobile_dropdown_menu_column ) :
																		$tax_object = get_taxonomy( $mobile_dropdown_menu_column );
																		if ( ! empty( $tax_object ) ) :
																			?>
																			<div class="m-nav-drops">
																				<a href="#" class="m-nav-drop-btn w-inline-block">
																					<div><?php echo esc_html( $tax_object->labels->singular_name ); ?></div>
																					<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc9a4d3949ca7d7c90f76_Union.svg' ); ?>" loading="eager" alt class="image-6-drop">
																				</a>
																				<?php
																				$terms_args = array(
																					'taxonomy'   => $tax_object->name,
																					'hide_empty' => false, // TODO: set to true!
																				);
																				$terms      = get_terms( $terms_args );
																				if ( ! empty( $terms ) ) :
																					?>
																					<div class="m-nav-drop-contant">
																						<div class="div-block-11">
																							<?php foreach ( $terms as $term_item ) : ?>
																								<a href="<?php echo esc_url( get_term_link( $dress_category ) . '?' . $tax_object->name . '=' . $term_item->term_id ); ?>" class="a-12-12 in-drop"><?php echo esc_html( $term_item->name ); ?></a>
																							<?php endforeach; ?>
																						</div>
																					</div>
																				<?php endif; ?>
																			</div>
																		<?php endif; ?>
																		<?php
																	else :
																		$price_links = $only_catalog_item['price_links'];
																		if ( ! empty( $price_links ) ) :
																			?>
																			<div class="m-nav-drops">
																				<a href="#" class="m-nav-drop-btn w-inline-block">
																					<div>Cтоимость</div>
																					<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc9a4d3949ca7d7c90f76_Union.svg' ); ?>" loading="eager" alt class="image-6-drop">
																				</a>
																				<div class="m-nav-drop-contant">
																					<div class="div-block-11">
																					<?php
																					foreach ( $price_links as $price_links_item ) :
																						$price_link       = get_term_link( $dress_category ) . '?';
																						$price_link_title = '';

																						if ( ! empty( $price_links_item['min_price'] ) ) {
																							$price_link .= 'min-price=' . $price_links_item['min_price'];
																						}

																						if ( ! empty( $price_links_item['max_price'] ) ) {
																							$price_link .= 'max-price=' . $price_links_item['max_price'];
																						}

																						if ( ! empty( $price_links_item['min_price'] ) && ! empty( $price_links_item['max_price'] ) ) {
																							$price_link_title = loveforever_format_price( $price_links_item['min_price'], 0 ) . ' ₽ – ' . loveforever_format_price( $price_links_item['max_price'], 0 ) . ' ₽';
																						} elseif ( ! empty( $price_links_item['min_price'] ) ) {
																							$price_link_title = 'от ' . loveforever_format_price( $price_links_item['min_price'], 0 ) . ' ₽';
																						} else {
																							$price_link_title = 'до ' . loveforever_format_price( $price_links_item['max_price'], 0 ) . ' ₽';
																						}

																						?>
																							<a href="<?php echo esc_url( $price_link ); ?>" class="a-12-12 in-drop"><?php echo esc_html( $price_link_title ); ?></a>
																						<?php endforeach; ?>
																					</div>
																				</div>
																			</div>
																			<?php
																		endif;
																	endif;
																	?>
																<?php endforeach; ?>
															</div>
														<?php endif; ?>
														<?php if ( 'dress-category-with-images' === $only_catalog_item['acf_fc_layout'] ) : ?>
															<?php $submenu_items = ! empty( $only_catalog_item['cards'] ) ? $only_catalog_item['cards'] : array(); ?>
															<?php if ( ! empty( $submenu_items ) ) : ?>
															<div class="m-nav-cats">
																<?php foreach ( $submenu_items as $submenu_item ) : ?>
																	<div class="m-nav-drops">
																		<a href="<?php echo esc_url( $submenu_item['page_link']['url'] ); ?>" class="m-nav-drop-btn w-inline-block">
																			<div><?php echo esc_html( $submenu_item['page_link']['title'] ); ?></div>
																			<img src="<?php echo esc_url( TEMPLATE_PATH . '/images/673dc9a4d3949ca7d7c90f76_Union.svg' ); ?>" loading="eager" alt="" class="image-6-drop"
																			style="transform: rotate(-90deg);">
																		</a>
																	</div>
																<?php endforeach; ?>
															</div>
														<?php endif; ?>
													<?php endif; ?>
													</div>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="rem10"></div>
</div>
