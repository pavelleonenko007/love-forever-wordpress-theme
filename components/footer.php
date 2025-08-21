<?php
/**
 * Footer
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$infoline_id   = loveforever_get_current_infoline();
$infoline_data = loveforever_get_infoline_data( $infoline_id );
$socials       = loveforever_get_socials();
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
					<div style="display: flex">
						<a id="footerPhoneNumber" data-js-phone-number="<?php echo esc_attr( loveforever_get_phone() ); ?>" href="<?php echo esc_url( loveforever_format_phone_to_link( loveforever_get_phone() ) ); ?>" class="a-12-12 w-inline-block">
							<div><?php echo esc_html( loveforever_mask_phone( loveforever_get_phone() ) ); ?></div>
						</a>
						<button type="button" data-js-phone-number-button="footerPhoneNumber" class="show-all-btn phone-button2 uppercase" style="padding: 0 4rem; background: none; border: none; color: inherit; font-size: 12rem; line-height: 1; font-weight: 400; text-transform: uppercase; cursor: pointer;">Показать</button>
					</div>
					<a href="<?php echo esc_url( loveforever_format_email_to_link( loveforever_get_email() ) ); ?>" class="a-12-12 w-inline-block">
						<div><?php echo esc_html( loveforever_get_email() ); ?></div>
					</a>
					<div class="a-12-12 w-inline-block">
						<div>следите за новинками</div>
					</div>
				</div>
				<?php
				if ( ! empty( $socials ) ) :
					?>
				<div class="div-block-10">
					<div class="p-12-12 uper op05 m-non">следите за новинками</div>
					<div class="soc-grid lf-share-buttons">
						<?php foreach ( $socials as $social ) : ?>
							<a class="lf-share-button" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<svg class="lf-share-button__icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<use href="#<?php echo esc_attr( $social['icon'] ); ?>"></use>
								</svg>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
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
			<!--<div id="w-node-b25aa748-36cb-7692-2710-65768ffd5ea7-b053f56f" class="vert">
				<a href="#" class="a-12-12 w-inline-block">
					<div>публичная офферта</div>
				</a>
			</div>-->
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
	<symbol id="whatsappIcon" viewBox="0 0 24 24">
		<path d="M17.6585 6.33333C16.1533 4.83333 14.1463 4 12.0279 4C7.62369 4 4.05575 7.55555 4.05575 11.9444C4.05575 13.3333 4.44599 14.7222 5.11498 15.8889L4 20L8.23694 18.8889C9.40767 19.5 10.6899 19.8333 12.0279 19.8333C16.4321 19.8333 20 16.2778 20 11.8889C19.9443 9.83333 19.1638 7.83333 17.6585 6.33333ZM15.8746 14.7778C15.7073 15.2222 14.9268 15.6667 14.5366 15.7222C14.2021 15.7778 13.7561 15.7778 13.3101 15.6667C13.0314 15.5556 12.6411 15.4444 12.1951 15.2222C10.1882 14.3889 8.90592 12.3889 8.79443 12.2222C8.68293 12.1111 7.95819 11.1667 7.95819 10.1667C7.95819 9.16667 8.45993 8.72222 8.62718 8.5C8.79442 8.27778 9.01742 8.27778 9.18467 8.27778C9.29617 8.27778 9.46341 8.27778 9.57491 8.27778C9.68641 8.27778 9.85366 8.22222 10.0209 8.61111C10.1882 9 10.5784 10 10.6341 10.0556C10.6899 10.1667 10.6899 10.2778 10.6341 10.3889C10.5784 10.5 10.5226 10.6111 10.4111 10.7222C10.2996 10.8333 10.1882 11 10.1324 11.0556C10.0209 11.1667 9.90941 11.2778 10.0209 11.4444C10.1324 11.6667 10.5226 12.2778 11.1359 12.8333C11.9164 13.5 12.5296 13.7222 12.7526 13.8333C12.9756 13.9444 13.0871 13.8889 13.1986 13.7778C13.3101 13.6667 13.7004 13.2222 13.8118 13C13.9233 12.7778 14.0906 12.8333 14.2578 12.8889C14.4251 12.9444 15.4286 13.4444 15.5958 13.5556C15.8188 13.6667 15.9303 13.7222 15.9861 13.7778C16.0418 13.9444 16.0418 14.3333 15.8746 14.7778Z"/>
	</symbol>
	<symbol id="telegramIcon" viewBox="0 0 24 24">
		<path d="M19 5.49595L16.6273 18.4172C16.6273 18.4172 16.2953 19.313 15.3833 18.8834L9.90888 14.3492L9.88349 14.3358C10.623 13.6186 16.3572 8.04925 16.6078 7.79681C16.9957 7.40583 16.7549 7.17307 16.3044 7.46841L7.83431 13.2789L4.56656 12.0912C4.56656 12.0912 4.05231 11.8936 4.00284 11.464C3.95271 11.0336 4.58348 10.8009 4.58348 10.8009L17.9051 5.1556C17.9051 5.1556 19 4.63594 19 5.49595V5.49595Z"/>
	</symbol>
	<symbol id="instagramIcon" viewBox="0 0 24 24">
		<path d="M15.53 4H8.4282C6.00522 4 4 6.00522 4 8.4282V15.5718C4 17.9948 6.00522 20 8.4282 20H15.53C17.953 20 19.9582 17.9948 20 15.5718V8.46997C20 6.00522 17.9948 4 15.53 4ZM11.9791 15.7807C9.89034 15.7807 8.21932 14.1097 8.21932 12.0209C8.21932 9.93212 9.89034 8.30287 11.9373 8.30287C13.9843 8.30287 15.6554 9.97389 15.6554 12.0209C15.6554 14.0679 14.0261 15.7807 11.9791 15.7807ZM17.8277 7.92689C17.4935 8.46997 16.7833 8.67885 16.282 8.34465C15.7389 8.01044 15.53 7.30026 15.8642 6.79896C16.1984 6.25587 16.9086 6.047 17.4099 6.3812C17.953 6.67363 18.1201 7.38381 17.8277 7.92689Z"/>
	</symbol>
	<symbol id="vkIcon" viewBox="0 0 24 24">
		<path d="M12.3485 18C5.85697 18 2.15428 13.4955 2 6H5.25172C5.35853 11.5015 7.75578 13.8318 9.65459 14.3123V6H12.7164V10.7447C14.5915 10.5405 16.5615 8.37838 17.2261 6H20.2879C20.0375 7.23349 19.5383 8.40142 18.8216 9.43072C18.1048 10.46 17.1859 11.3285 16.1224 11.982C17.3096 12.579 18.3581 13.4241 19.1989 14.4615C20.0397 15.4989 20.6535 16.7049 21 18H17.6296C17.3186 16.8751 16.6865 15.8681 15.8125 15.1053C14.9385 14.3424 13.8615 13.8577 12.7164 13.7117V18H12.3485Z"/>
	</symbol>
</svg>
