<?php
/**
 * Footer
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$infoline_id   = loveforever_get_current_infoline();
$infoline_data = loveforever_get_infoline_data( $infoline_id );
?>
<footer class="footer">
	<?php get_template_part( 'components/marquee', null, $infoline_data ); ?>
	<div class="container foo-container">
		<div class="foo-top">
			<?php
			$menu_dresses = get_field( 'menu_dresses', 'option' );
			if ( ! empty( $menu_dresses ) ) :
				?>
				<div id="w-node-_234c1641-3300-cb43-e8da-825e992037d9-b053f56f" class="vert">
					<div class="p-12-12 uper op05">Платья</div>
					<div class="vert-foo">
						<?php
						foreach ( $menu_dresses as $menu_dresses_item ) :
							$menu_item_link_attributes = array(
								'class' => 'a-12-12 w-inline-block',
							);

							$menu_item_link_attributes_string = loveforever_prepare_link_attributes( $menu_item_link_attributes, $menu_dresses_item['menu_item'] );
							?>
							<a <?php echo $menu_item_link_attributes_string; ?>>
								<div><?php echo esc_html( $menu_dresses_item['menu_item']['title'] ); ?></div>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
			<div id="w-node-_68f6fa66-2e99-69ec-af7c-da5704d78a88-b053f56f" class="vert">
				<div class="p-12-12 uper op05">Другое</div>
				<div class="vert-foo">
					<?php
					$menu_other = get_field( 'menu_other', 'option' );
					foreach ( $menu_other as $menu_other_item ) :
						$menu_item_link_attributes = array(
							'class' => 'a-12-12 w-inline-block',
						);

						$menu_item_link_attributes_string = loveforever_prepare_link_attributes( $menu_item_link_attributes, $menu_other_item['menu_item'] );
						?>
						<a <?php echo $menu_item_link_attributes_string; ?>>
							<div><?php echo esc_html( $menu_other_item['menu_item']['title'] ); ?></div>
						</a>
					<?php endforeach; ?>
					<?php $favorites = loveforever_get_favorites(); ?>
					<a href="<?php echo esc_url( get_the_permalink( FAVORITES_PAGE_ID ) ); ?>" class="a-12-12 w-inline-block" data-js-favorites-button>
						<div>примерочная (<span class="prim-count" data-js-favorites-button-counter><?php echo esc_html( count( $favorites ) ); ?></span>)</div>
					</a>
				</div>
			</div>
			<div id="w-node-e462508c-6438-de3d-e96e-4e0519948197-b053f56f" class="vert">
				<div class="p-12-12 uper op05">Контакты</div>
				<div class="vert-foo">
					<a href="<?php echo esc_url( loveforever_format_phone_to_link( loveforever_get_phone() ) ); ?>" class="a-12-12 w-inline-block">
						<div><?php echo esc_html( loveforever_get_phone() ); ?></div>
					</a>
					<a href="<?php echo esc_url( loveforever_format_email_to_link( loveforever_get_email() ) ); ?>" class="a-12-12 w-inline-block">
						<div><?php echo esc_html( loveforever_get_email() ); ?></div>
					</a>
					<div class="a-12-12 w-inline-block">
						<div>следите за новинками</div>
					</div>
				</div>
				<div class="div-block-10">
					<div class="p-12-12 uper op05 m-non">следите за новинками</div>
					<div class="soc-grid">
						<?php if ( ! empty( VK_LINK ) ) : ?>
							<a href="<?php echo esc_url( VK_LINK ); ?>" class="soc-btn w-inline-block" style="color: var(--white);">
								<div class="svg-share w-embed">
									<svg width="16" height="10" viewbox="0 0 16 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
										<use href="#vkIcon"></use>
									</svg>
								</div>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( TELEGRAM_LINK ) ) : ?>
							<a href="<?php echo esc_url( TELEGRAM_LINK ); ?>" class="soc-btn w-inline-block" style="color: var(--white);">
								<div class="svg-share w-embed">
									<svg width="14" height="13" viewbox="0 0 14 13" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
										<use href="#telegramIcon"></use>
									</svg>
								</div>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( WHATSAPP_LINK ) ) : ?>
						<a href="<?php echo esc_url( WHATSAPP_LINK ); ?>" class="soc-btn w-inline-block" style="color: var(--white);">
							<div class="svg-share w-embed">
								<svg width="16" height="16" viewbox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<use href="#whatsappIcon"></use>
								</svg>
							</div>
						</a>
						<?php endif; ?>
						<?php if ( ! empty( INSTAGRAM_LINK ) ) : ?>
						<a href="<?php echo esc_url( INSTAGRAM_LINK ); ?>" class="soc-btn w-inline-block" style="color: var(--white);">
							<div class="svg-share w-embed">
								<svg width="100%" height="100%" viewbox="0 0 40 60" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<use href="#instagramIcon"></use>
								</svg>
							</div>
						</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div id="w-node-_84c093a7-84bc-1023-9faa-7edd926bb4b0-b053f56f" class="vert">
				<div class="p-12-12 uper op05">Мы принимаем</div>
				<div class="vert-foo"><img src="<?php echo get_template_directory_uri(); ?>/images/672cb6036d7798fdda9576af_D09FD0BBD0B0D182D191D0B6D0BDD18BD0B520D181D0B8D181D182D0B5D0BCD18B.svg" loading="lazy" alt></div>
			</div>
		</div>
		<div class="foo-logo-keeper"><a href="#" class="w-inline-block"><img src="<?php echo get_template_directory_uri(); ?>/images/672cb1ff4e8d6c12ebcae46c_foo-logo.svg" loading="lazy" alt class="img-fw"></a></div>
		<div class="foo-top bott">
			<div id="w-node-b25aa748-36cb-7692-2710-65768ffd5e84-b053f56f" class="vert">
				<div class="p-12-12 uper m-12-12"><?php echo esc_html( '© ' . wp_date( 'Y' ) . ' ' . get_bloginfo( 'site_name' ) ); ?></div>
			</div>
			<?php if ( ! empty( PRIVACY_POLICY_LINK ) ) : ?>
				<div id="w-node-b25aa748-36cb-7692-2710-65768ffd5e94-b053f56f" class="vert">
					<a href="<?php echo esc_url( PRIVACY_POLICY_LINK ); ?>" class="a-12-12 w-inline-block">
						<div>Политика конфиденциальности</div>
					</a>
				</div>
			<?php endif; ?>
			<div id="w-node-b25aa748-36cb-7692-2710-65768ffd5ea7-b053f56f" class="vert">
				<a href="#" class="a-12-12 w-inline-block">
					<div>публичная офферта</div>
				</a>
			</div>
			<div id="w-node-b25aa748-36cb-7692-2710-65768ffd5ebd-b053f56f" class="vert m-none">
				<a href="#" class="a-12-12 to-top w-inline-block" onclick="window.scrollTo({ top: 0, behavior: 'smooth' }); return false;">
					<div>Вернуться к началу</div>
					<div class="code-embed-5 w-embed">
						<svg width="100%" height="100%" viewbox="0 0 6 4" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M3.51284 0.583957L6 3.41604L5.48716 4L3 1.16791L0.512837 4L5.10512e-08 3.41604L2.48716 0.583957L3 -2.62268e-07L3.51284 0.583957Z" fill="white"></path>
						</svg>
					</div>
				</a>
			</div>
		</div>
	</div>
	<div class="foo-bg">
		<div class="bg-grad ins"></div>
	</div>
</footer>
<svg style="display: none">
	<symbol id="searchIcon" viewBox="0 0 16 16">
		<path d="M7.27734 0.900391C4.30475 0.900391 1.90045 3.38524 1.90039 6.44434C1.90039 9.50348 4.30472 11.9893 7.27734 11.9893C8.44479 11.9892 9.52496 11.6046 10.4062 10.9531L14.3955 15.0693L14.4668 15.1436L14.5391 15.0693L15.0723 14.5195L15.1396 14.4502L15.0723 14.3809L11.1289 10.3105C12.0722 9.31147 12.6543 7.94807 12.6543 6.44434C12.6542 3.38531 10.2498 0.900508 7.27734 0.900391ZM7.27734 1.87793C9.71719 1.87805 11.7001 3.91939 11.7002 6.44434C11.7002 8.96933 9.71722 11.0106 7.27734 11.0107C4.83737 11.0107 2.85352 8.9694 2.85352 6.44434C2.85357 3.91932 4.8374 1.87793 7.27734 1.87793Z" stroke-width="0.2"/>
	</symbol>
	<symbol id="heartIcon" viewBox="0 0 16 16" >
		<path d="M8.86426 2.62207C10.3821 1.126 12.8463 1.12611 14.3643 2.62207C15.8308 4.06759 15.8769 6.38015 14.502 7.87988L14.3643 8.02246L13.8506 8.5293L13.4893 8.88477L13.4912 8.88672L8.00195 14.2969L1.63574 8.02246C0.121838 6.5303 0.121838 4.11423 1.63574 2.62207C3.10626 1.17285 5.46431 1.12755 6.99023 2.48633L7.13574 2.62207L7.64941 3.12891L8 3.47461L8.35059 3.12891L8.86426 2.62207Z"/>
	</symbol>
	<symbol id="whatsappIcon" viewBox="0 0 16 16">
		<path d="M13.6585 2.33333C12.1533 0.833333 10.1463 0 8.02787 0C3.62369 0 0.0557483 3.55556 0.0557483 7.94444C0.0557483 9.33333 0.445993 10.7222 1.11498 11.8889L0 16L4.23694 14.8889C5.40767 15.5 6.68989 15.8333 8.02787 15.8333C12.4321 15.8333 16 12.2778 16 7.88889C15.9443 5.83333 15.1638 3.83333 13.6585 2.33333ZM11.8746 10.7778C11.7073 11.2222 10.9268 11.6667 10.5366 11.7222C10.2021 11.7778 9.7561 11.7778 9.31011 11.6667C9.03136 11.5556 8.64112 11.4444 8.19512 11.2222C6.18815 10.3889 4.90592 8.38889 4.79443 8.22222C4.68293 8.11111 3.95819 7.16667 3.95819 6.16667C3.95819 5.16667 4.45993 4.72222 4.62718 4.5C4.79443 4.27778 5.01742 4.27778 5.18467 4.27778C5.29617 4.27778 5.46341 4.27778 5.57491 4.27778C5.68641 4.27778 5.85366 4.22222 6.02091 4.61111C6.18815 5 6.5784 6 6.63415 6.05556C6.68989 6.16667 6.68989 6.27778 6.63415 6.38889C6.5784 6.5 6.52265 6.61111 6.41115 6.72222C6.29965 6.83333 6.18815 7 6.1324 7.05556C6.0209 7.16667 5.90941 7.27778 6.02091 7.44444C6.1324 7.66667 6.52265 8.27778 7.13589 8.83333C7.91638 9.5 8.52962 9.72222 8.75261 9.83333C8.97561 9.94445 9.08711 9.88889 9.1986 9.77778C9.3101 9.66667 9.70035 9.22222 9.81185 9C9.92335 8.77778 10.0906 8.83333 10.2578 8.88889C10.4251 8.94444 11.4286 9.44445 11.5958 9.55556C11.8188 9.66667 11.9303 9.72222 11.9861 9.77778C12.0418 9.94444 12.0418 10.3333 11.8746 10.7778Z"/>
	</symbol>
	<symbol id="telegramIcon" viewBox="0 0 16 16">
		<path d="M15 1.49595L12.6273 14.4172C12.6273 14.4172 12.2953 15.313 11.3833 14.8834L5.90888 10.3492L5.88349 10.3358C6.62296 9.61856 12.3572 4.04925 12.6078 3.79681C12.9957 3.40583 12.7549 3.17307 12.3044 3.46841L3.83431 9.27892L0.566557 8.09122C0.566557 8.09122 0.0523084 7.89362 0.00283645 7.46397C-0.0472864 7.03361 0.583481 6.80086 0.583481 6.80086L13.9051 1.1556C13.9051 1.1556 15 0.635942 15 1.49595V1.49595Z"/>
	</symbol>
	<symbol id="instagramIcon" viewbox="0 0 40 60">
		<path d="M23.53 22H16.4282C14.0052 22 12 24.0052 12 26.4282V33.5718C12 35.9948 14.0052 38 16.4282 38H23.53C25.953 38 27.9582 35.9948 28 33.5718V26.47C28 24.0052 25.9948 22 23.53 22ZM19.9791 33.7807C17.8903 33.7807 16.2193 32.1097 16.2193 30.0209C16.2193 27.9321 17.8903 26.3029 19.9373 26.3029C21.9843 26.3029 23.6554 27.9739 23.6554 30.0209C23.6554 32.0679 22.0261 33.7807 19.9791 33.7807ZM25.8277 25.9269C25.4935 26.47 24.7833 26.6789 24.282 26.3446C23.7389 26.0104 23.53 25.3003 23.8642 24.799C24.1984 24.2559 24.9086 24.047 25.4099 24.3812C25.953 24.6736 26.1201 25.3838 25.8277 25.9269Z"></path>
	</symbol>
	<symbol id="vkIcon" viewBox="0 0 16 12">
		<path d="M8.71455 10C3.24797 10 0.129919 6.24625 0 0H2.73829C2.82823 4.58458 4.84697 6.52653 6.44597 6.92693V0H9.02436V3.95395C10.6034 3.78378 12.2623 1.98198 12.822 0H15.4004C15.1895 1.02791 14.7691 2.00118 14.1655 2.85893C13.562 3.71668 12.7882 4.44045 11.8926 4.98498C12.8923 5.48254 13.7753 6.18678 14.4833 7.05125C15.1913 7.91571 15.7082 8.92073 16 10H13.1618C12.8999 9.06258 12.3676 8.22343 11.6316 7.58773C10.8956 6.95203 9.9886 6.54805 9.02436 6.42643V10H8.71455Z"></path>
	</symbol>
</svg>
