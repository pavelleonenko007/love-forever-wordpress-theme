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
						<button type="button" data-js-phone-number-button="footerPhoneNumber" class="show-all-btn phone-button uppercase" style="padding: 0 4rem; background: none; border: none; color: inherit; font-size: 12rem; line-height: 1; font-weight: 400; text-transform: uppercase; cursor: pointer;">Показать</button>
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
		<div class="foo-logo-keeper"><a class="w-inline-block"><img src="<?php echo get_template_directory_uri(); ?>/images/672cb1ff4e8d6c12ebcae46c_foo-logo.svg" loading="lazy" alt class="img-fw"></a></div>
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
<?php get_template_part( 'components/icons' ); ?>
