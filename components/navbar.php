<?php
	/**
	 * Navbar
	 *
	 * @package 0.0.1
	 */

	defined( 'ABSPATH' ) || exit;
	global $template;

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
	<header class="lf-navbar header menuline">
		<div class="vert-menu">
			<div class="spleet pc-none">
				<?php if ( ! empty( $phone ) ) : ?>
				<div class="lf-navbar__menu-line lf-navbar__menu-line--mobile menu-line p-12-12 white uper n-voreder">
					<a 
						href="<?php echo esc_url( loveforever_format_phone_to_link( $phone ) ); ?>" 
						class="n-menu w-nav-link"
						aria-label="<?php echo esc_attr( 'Позвонить на номер ' . $phone ); ?>"
					><?php echo esc_html( $phone ); ?></a>
				</div>
				<?php endif; ?>
				<div class="l-spacer"></div>
				<div class="lf-navbar__menu-line lf-navbar__menu-line--right lf-navbar__menu-line--mobile menu-line p-12-12 white uper rev n-voreder">
					<div class="div-block-5">
						<!-- Favorites button -->
						<a 
							href="<?php echo esc_url( home_url( '/' ) . 'favorites/' ); ?>" class="lf-icon-button lf-icon-button--favorites lf-icon-button--white <?php echo 0 < count( $favorites ) ? 'is-active' : ''; ?>" data-js-favorites-button
							aria-label="<?php echo esc_attr( 'Перейти в избранное' ); ?>"
							title="<?php echo esc_attr( 'Перейти в избранное' ); ?>"
							>
							<svg xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
								<use href="#heartIcon"></use>
							</svg>
							<span class="lf-icon-button__counter" data-js-favorites-button-counter><?php echo esc_html( (string) count( $favorites ) ); ?></span>
						</a>
						<!-- Favorites button end -->
						<div class="menu-link-keeper">
							<button 
								type="button" 
								class="lf-icon-button lf-icon-button--search lf-icon-button--white"
								aria-label="<?php echo esc_attr( 'Открыть поиск' ); ?>"
								title="<?php echo esc_attr( 'Открыть поиск' ); ?>"
								>
								<svg xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
									<use href="#searchIcon"></use>
								</svg>
							</button>
							<div class="hovered-menue search-m">
								<div id="w-node-_29763d6b-4a4a-4ba9-96d9-354223034cf4-be61d3ef" class="div-block-6">
									<div class="div-block-7">
										<form 
											id="searchForm1" 
											action="<?php echo esc_url( home_url( '/' ) ); ?>" 
											class="lf-search-form search" 
											data-js-search-form
											data-js-input-zoom-prevention
											>
											<input
												type="search" 
												name="s" 
												maxlength="256" 
												placeholder="Напишите, что вы ищите..." 
												id="searchForm1SearchControl" 
												class="lf-search-form__search-input search-input w-input" 
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
							<button type="button" class="lf-burger-button menu-bnt w-inline-block" aria-label="<?php echo esc_attr( 'Открыть меню' ); ?>" title="<?php echo esc_attr( 'Открыть меню' ); ?>">
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
												<a 
													class="lf-share-button lf-share-button--dark" 
													href="<?php echo esc_url( $social['url'] ); ?>" 
													target="_blank" 
													rel="noopener noreferrer" 
													aria-label="<?php echo esc_attr( $social['aria-label'] ); ?>" 
													title="<?php echo esc_attr( $social['aria-label'] ); ?>">
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
				<div class="lf-navbar__menu-line menu-line p-12-12 white uper n-voreder">
					<?php foreach ( $left_menu as $left_menu_item ) : ?>
						<?php get_template_part( 'components/navbar-' . $left_menu_item['acf_fc_layout'], null, $left_menu_item ); ?>
					<?php endforeach; ?>
				</div>
				<!-- Desktop Left menu end -->
				<?php endif; ?>
				<div class="l-spacer"></div>
				<!-- Desktop rigth menu start -->
				<div class="lf-navbar__menu-line lf-navbar__menu-line--right menu-line p-12-12 white uper rev n-voreder">
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
							<a href="#" 
								class="lf-icon-button lf-icon-button--search lf-icon-button--white"
								aria-label="<?php echo esc_attr( 'Открыть поиск' ); ?>"
								title="<?php echo esc_attr( 'Открыть поиск' ); ?>"
								>
								<svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 16 16" class="lf-icon-button__icon">
									<use href="#searchIcon" />
								</svg>
							</a>
							<div class="hovered-menue search-m" style="grid-template-columns: repeat(6, auto);">
								<div id="w-node-_144563be-6001-1af8-6446-1240953da9f3-be61d3ef" class="div-block-6">
									<div class="div-block-7">
										<form 
											id="searchForm2" 
											action="<?php echo esc_url( home_url( '/' ) ); ?>" 
											class="lf-search-form search" 
											data-js-search-form
											data-js-input-zoom-prevention
											>
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
						<a 
							href="<?php echo esc_url( home_url( '/' ) . 'favorites/' ); ?>" class="lf-icon-button lf-icon-button--favorites lf-icon-button--white <?php echo 0 < count( $favorites ) ? 'is-active' : ''; ?>" data-js-favorites-button
							aria-label="<?php echo esc_attr( 'Перейти в избранное' ); ?>"
							title="<?php echo esc_attr( 'Перейти в избранное' ); ?>"
							>
							<svg xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
								<use href="#heartIcon"></use>
							</svg>
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
						<a 
							href="<?php echo esc_url( $social['url'] ); ?>" 
							aria-label="<?php echo esc_attr( $social['aria-label'] ); ?>" 
							class="lf-header-social" 
							target="_blank" 
							rel="noopener noreferrer"
							title="<?php echo esc_attr( $social['aria-label'] ); ?>"
						>
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
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link w-inline-block" aria-label="<?php echo esc_attr( 'Перейти на главную ' . get_bloginfo( 'name' ) ); ?>">
					<div class="svg w-embed">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 226 145">
							<path fill="#fff" fill-rule="evenodd" d="M104.165 23.374c4.091 4.124 3.562 6.036 2.349 7.715-.6.828-5.528 3.868-8.924-5.476-.975-2.361-2.662-14.575 4.462-18.013 8.048-3.686 11.871 3.942 12.961 6.375.746 1.661 4.844 14.96 8.692 27.445l.001.004c3.421 11.1 6.643 21.555 7.134 22.613 1.038 2.247 2.804 2.267 3.755 1.34.956-.926 12.281-24.06 13.352-26.915 1.07-2.85 9.623-25.686-18.24-33.8 4.012-4.312 15.231-2.696 20.787 0s14.255 1.526 14.255 1.526-6.555 5.07-14.054 1.313c-6.176-3.095-9.71-2.905-9.71-2.905 16.992 5.933 18.418 15.19 9.173 34.63-7.116 14.958-11.689 23.521-14.03 27.251-2.342 3.73-7.531 10.507-12.357 3.162-2.732-4.164-8.078-21.802-12.607-36.74-3.464-11.427-6.449-21.274-7.418-22.299-1.666-1.77-3.597 1.116-3.668 3.477-.071 2.362 0 5.185 4.091 9.309zM50.047 72.86v-.678q2.442 0 3.965-1.455t1.524-3.958V28.204q-.002-2.438-1.524-3.923-1.523-1.49-3.965-1.49v-.678h6.91q3.46 0 5.288-.746 1.694-.744 2.373-1.825h.68v47.227q.002 2.503 1.524 3.958t3.965 1.455v.678zm-6.731 52.116q-4.976 0-8.708-1.999-3.73-1.998-5.793-5.634c-1.374-2.421-2.061-5.291-2.061-8.599s.687-6.177 2.061-8.598q2.061-3.636 5.793-5.634 3.731-1.999 8.708-1.999 4.974 0 8.676 1.999t5.761 5.634c1.374 2.421 2.061 5.291 2.061 8.598s-.687 6.178-2.061 8.599q-2.063 3.633-5.761 5.634-3.703 1.999-8.676 1.999m-.06-.789q2.123 0 3.641-2.121 1.518-2.118 2.366-5.661t.849-7.724q.001-4.54-.88-8.023c-.59-2.322-1.395-4.14-2.43-5.452-1.03-1.313-2.195-1.967-3.486-1.967q-2.063-.001-3.61 2.12c-1.03 1.416-1.832 3.293-2.397 5.634q-.85 3.512-.849 7.692 0 4.603.909 8.118.91 3.513 2.456 5.452t3.427 1.94zm37.976-52.612q4.17 2.23 9.726 2.231 5.556.002 9.69-2.231c2.753-1.487 4.901-3.588 6.437-6.293q2.305-4.062 2.306-9.607-.001-5.55-2.306-9.608-2.303-4.057-6.437-6.293c-2.756-1.486-5.982-2.231-9.69-2.231s-6.946.741-9.726 2.231c-2.78 1.487-4.937 3.588-6.473 6.293-1.54 2.704-2.306 5.91-2.306 9.608s.77 6.903 2.306 9.607q2.303 4.057 6.473 6.293m13.726-1.018q-1.697 2.37-4.067 2.37-2.102 0-3.83-2.165c-1.154-1.443-2.066-3.473-2.745-6.09q-1.014-3.927-1.015-9.068c0-3.115.316-5.977.948-8.595q.947-3.92 2.677-6.292 1.729-2.37 4.032-2.37 2.167 0 3.897 2.2 1.726 2.2 2.71 6.091.983 3.892.983 8.965c0 3.111-.316 5.99-.948 8.627q-.947 3.962-2.642 6.327m87.785-8.795q-1.829 2.707-4.778 4.297t-6.2 1.589q-5.355.001-8.537-4.297-3.187-4.295-3.187-11.47v-.338h22.77l-.068-1.013q-.272-3.856-2.203-6.801-1.932-2.945-5.149-4.566c-2.149-1.084-4.597-1.624-7.353-1.624q-4.948.001-8.846 2.267t-6.133 6.328q-2.236 4.056-2.235 9.474c0 3.61.679 6.643 2.034 9.37q2.032 4.094 5.659 6.396 3.626 2.3 8.3 2.302 3.861 0 7.183-1.49a17.6 17.6 0 0 0 5.762-4.128 16.6 16.6 0 0 0 3.593-6.091l-.608-.201zm-15.452-23.21q3.116-.001 4.846 3.316c1.082 2.078 1.65 4.762 1.713 8.039h-13.706c.233-3.363.963-6.064 2.199-8.106q1.965-3.248 4.948-3.249m21.401 79.484q2.642-1.419 4.277-3.844l.545.182a14.8 14.8 0 0 1-3.215 5.452 15.7 15.7 0 0 1-5.157 3.694c-1.982.887-4.122 1.333-6.429 1.333q-4.187 0-7.432-2.058-3.247-2.058-5.066-5.725c-1.212-2.444-1.82-5.16-1.82-8.389s.667-6.06 2.002-8.48q2-3.633 5.489-5.662t7.917-2.03q3.7 0 6.583 1.455 2.883 1.453 4.612 4.088t1.971 6.087l.059.907h-20.384v.303q-.002 6.423 2.851 10.267t7.645 3.844q2.916.001 5.552-1.424m-5.22-21.648q-1.546-2.969-4.336-2.969l-.004.004c-1.777 0-3.254.966-4.427 2.906-1.086 1.79-1.729 4.155-1.951 7.085h12.25c-.075-2.855-.58-5.2-1.532-7.026M16.286 80.642c.648-1.979 1.615-2.968 2.91-2.968s2.283.887 3.093 2.665c.402.887.699 1.92.88 3.087q.273 1.756.332 3.938l7.278-4.12c-.486-1.25-1.303-2.353-2.456-3.3q-1.729-1.424-4.127-2.239a15.6 15.6 0 0 0-5.067-.816q-3.884.001-6.732 1.877c-1.904 1.253-3.369 2.996-4.4 5.24-1.03 2.239-1.547 4.896-1.547 7.963v1.392H1.908L1 94.993h5.457v23.683q.001 2.181-1.366 3.512-1.363 1.332-3.55 1.333v.607h19.78v-.607h-.059q-2.73-.001-4.4-1.333-1.668-1.33-1.666-3.512V94.993h7.282l.909-1.632h-8.19v-4.605q-.001-5.147 1.093-8.118zm181.128 42.879v.607l.004-.004h18.564v-.607h-.059q-2.185 0-3.519-1.332-1.333-1.331-1.335-3.513v-15.735q.73-1.756 1.73-3.028c.667-.847 1.69-1.707 3.064-1.707.806 0 1.75.162 2.82.485q1.605.484 3.791 1.696l2.365-7.085c-.77-.323-2.298-.899-4.837-.986-1.781-.059-2.824.182-4.099 1.088-1.272.911-2.097 2.07-2.926 3.403q-1.245 2-2.034 3.816l-.059-9.56h-.304c-.446.686-1.216 1.237-2.306 1.663-1.094.422-2.709.635-4.854.635h-6.006v.607q3.032.001 3.973 1.274.941 1.27.94 3.572v19.866q-.001 2.182-1.335 3.512-1.333 1.332-3.519 1.333zm-135.601.607v-.607h.059q2.186 0 3.518-1.333 1.334-1.33 1.335-3.512V98.809q.001-2.3-.94-3.571-.94-1.273-3.972-1.274v-.607h6.006q3.215-.001 4.853-.635 1.636-.636 2.306-1.663h.304l.06 9.56q.788-1.816 2.033-3.816c.83-1.333 1.655-2.492 2.926-3.403 1.276-.906 2.318-1.147 4.1-1.088 2.538.087 4.067.663 4.837.986l-2.366 7.085q-2.185-1.212-3.79-1.696c-1.07-.323-2.015-.485-2.82-.485-1.374 0-2.397.86-3.064 1.707q-1.002 1.272-1.73 3.028v15.735q0 2.182 1.335 3.513 1.332 1.331 3.518 1.332h.06v.607H61.815zm72.344-26.895 12.071 27.861v-.008h.545l11.164-25.315q1.154-2.542 2.61-3.997 1.458-1.456 3.033-1.818v-.607h-9.584v.607c1.094.123 1.757.3 2.365.947.609.646.909 1.111.909 2.12 0 .78-.364 1.6-.724 2.414l-.047.105-.058.13-.048.111-6.085 13.945-5.896-13.771q-1.032-2.483-.395-4.12c.427-1.088 1.343-1.715 2.76-1.877v-.607h-16.988v.607c1.173.044 2.093.363 2.76.97q1.003.912 1.608 2.302m-6.86 15.765c5.675-.252 12.558 2.898 13.324 8.145v-.004c1.086 5.433-2.409 10.373-3.965 12.246-8.573 10.408-27.346 12.352-38.095 5.902-25.313-15.187-12.502-48.083 16.08-45.229 2.531.182 7.467-.213 8.072-1.498 1.011-2.152-1.742-2.685-3.357-2.685-2.192.19-4.491.891-6.841 1.608-3.806 1.16-7.742 2.361-11.561 1.491-9.513-2.168-9.726-15.257-.881-19.381 8.044-3.97 20.132-.13 24.958 7.077 2.081 3.109 3.14 5.008 3.87 6.317.962 1.725 1.352 2.425 2.76 3.52 2.867 2.298 6.121-1.093 4.537-4.065-.399-.81-1.379-1.44-2.362-2.071-1.688-1.085-3.385-2.176-2.167-4.194 2.385-2.65 6.488.568 7.104 3.099 1.441 3.544-.699 8.835-4.814 8.843-3.558.005-5.283-2.973-7.309-6.47-2.727-4.709-6-10.357-15.022-10.936-3.566.043-8.226.792-10.35 4.88-1.8 3.572-1.003 7.672 2.373 9.64 4.124 2.402 7.903.946 11.718-.525 2.202-.848 4.416-1.701 6.716-1.821 7.755.662 5.694 10.239-1.177 9.734-1.296-.166-2.529-.357-3.712-.54-7.203-1.117-12.528-1.943-18.84 4.932-13.284 15.628.853 35.474 18.607 36.764 4.17.319 10.212-.194 13.726-2.184 7.338-3.659 11.689-13.985 2.705-17.773-4.959-2.216-11.001-2.358-16.245-.745-2.836.776-5.841 1.573-8.834 1.277-4.182-.402-8.696-3.785-8.652-8.177.142-3.359 2.792-6.383 6.606-6.804 2.279-.123 4.155 2.684 2.705 4.608-.762 1.061-1.939 1.522-3.076 1.964-1.757.603-3.759 1.715-3.072 4.021 2.27 5.157 9.477 3.264 13.639 1.411 3.448-1.446 7.29-2.416 10.832-2.377" clip-rule="evenodd"/>
						</svg>
					</div>
				</a>
			</div>
		</div>
	</header>
	<?php
	if ( ! str_contains( $template, 'admin-fittings.php' ) ) :
		?>
	<div class="fixed-navbar menuline lf-navbar lf-fixed-navbar">
		<div class="vert-menu">
			<div class="spleet m-none">
			<?php if ( ! empty( $left_menu ) ) : ?>
				<div class="lf-navbar__menu-line menu-line p-12-12 white uper n-voreder">
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
				<div class="lf-navbar__menu-line lf-navbar__menu-line--right menu-line p-12-12 white uper rev n-voreder">
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
							<a 
								href="#" 
								class="lf-icon-button lf-icon-button--search"
								aria-label="<?php echo esc_attr( 'Открыть поиск' ); ?>"
								title="<?php echo esc_attr( 'Открыть поиск' ); ?>"
							>
								<svg xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
									<use href="#searchIcon" />
								</svg>
							</a>
							<div class="hovered-menue search-m" style="grid-template-columns: repeat(6, auto);">
								<div id="w-node-_1716cbec-a8d5-9533-681b-95848935b87a-be61d3ef" class="div-block-6">
									<div class="div-block-7">
										<form 
											id="searchForm3" 
											action="<?php echo esc_url( home_url( '/' ) ); ?>" 
											class="lf-search-form search" 
											data-js-search-form
											data-js-input-zoom-prevention
											>
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
						<a 
							href="<?php echo esc_url( home_url( '/' ) . 'favorites/' ); ?>" 
							class="lf-icon-button lf-icon-button--favorites <?php echo 0 < count( $favorites ) ? 'is-active' : ''; ?>" 
							data-js-favorites-button
							aria-label="<?php echo esc_attr( 'Перейти в избранное' ); ?>"
							title="<?php echo esc_attr( 'Перейти в избранное' ); ?>"
							>
							<svg xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
								<use href="#heartIcon"/>
							</svg>
							<span class="lf-icon-button__counter" data-js-favorites-button-counter>
							<?php echo esc_html( (string) count( $favorites ) ); ?>
							</span>
						</a>
					</div>
				</div>
			</div>
			<div class="spleet pc-none">
				<div class="lf-navbar__menu-line lf-navbar__menu-line--mobile menu-line p-12-12 white uper n-voreder">
					<a 
						href="<?php echo esc_url( home_url( '/' ) . 'favorites/' ); ?>" 
						class="lf-icon-button lf-icon-button--favorites <?php echo 0 < count( $favorites ) ? 'is-active' : ''; ?>" 
						data-js-favorites-button
						aria-label="<?php echo esc_attr( 'Перейти в избранное' ); ?>"
						title="<?php echo esc_attr( 'Перейти в избранное' ); ?>"
						>
						<svg xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
							<use href="#heartIcon"  />
						</svg>
						<div data-js-favorites-button-counter class="lf-icon-button__counter"><?php echo esc_html( (string) count( $favorites ) ); ?></div>
					</a>
					<a 
						href="<?php echo esc_url( loveforever_format_phone_to_link( $phone ) ); ?>" 
						class="lf-icon-button lf-icon-button--phone" 
						aria-label="<?php echo esc_attr( 'Позвонить на номер ' . $phone ); ?>"
						title="<?php echo esc_attr( 'Позвонить на номер ' . $phone ); ?>"
						>
						<svg xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
							<use href="#phoneIcon" />
							<!-- <use href="#deviceIcon" /> -->
						</svg>
					</a>
				</div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( 'Перейти на главную ' . get_bloginfo( 'name' ) ); ?>" class="lf-scroll-navbar-logo lf-scroll-navbar-logo--mobile w-inline-block">
					<svg width="177" height="35" class="lf-scroll-navbar-logo__icon">
						<use href="#scrollNavbarLogo" />
					</svg>
				</a>
				<div class="lf-navbar__menu-line lf-navbar__menu-line--right lf-navbar__menu-line--mobile menu-line p-12-12 white uper rev n-voreder">
					<div class="div-block-5 wh-head">
						<div class="menu-link-keeper">
							<a href="#" 
								class="lf-icon-button lf-icon-button--search"
								aria-label="<?php echo esc_attr( 'Открыть поиск' ); ?>"
								title="<?php echo esc_attr( 'Открыть поиск' ); ?>"
								>
								<svg xmlns="http://www.w3.org/2000/svg" class="lf-icon-button__icon">
									<use href="#searchIcon" />
								</svg>
							</a>
							<div class="hovered-menue search-m">
								<div id="w-node-_7814220d-338f-0ab7-0d3b-7d1e447cc090-be61d3ef" class="div-block-6">
									<div class="div-block-7">
										<form 
											id="searchForm4" 
											action="<?php echo esc_url( home_url( '/' ) ); ?>" 
											class="search" 
											data-js-search-form
											data-js-input-zoom-prevention
											>
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
							<button type="button" class="lf-burger-button menu-bnt w-inline-block" aria-label="<?php echo esc_attr( 'Открыть меню' ); ?>" title="<?php echo esc_attr( 'Открыть меню' ); ?>">
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
										<div id="w-node-_3514c32b-e70e-46c1-9d79-f82fca09e2e3-be61d3ef" class="div-block-4 cont-item">
											<div class="p-12-12 uper m-12-12">Наши группы в социальных сетях</div>
											<div class="soc-grid mpb lf-share-buttons">
												<?php foreach ( $socials as $social ) : ?>
												<a 
													class="lf-share-button lf-share-button--dark" 
													href="<?php echo esc_url( $social['url'] ); ?>" 
													target="_blank" 
													rel="noopener noreferrer"
													aria-label="<?php echo esc_attr( $social['aria-label'] ); ?>"
													title="<?php echo esc_attr( $social['aria-label'] ); ?>"
												>
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
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div class="rem10"></div>
</div>
