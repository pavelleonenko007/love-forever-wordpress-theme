<?php
/**
 * Global Fitting Dialog
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="globalCallbackDialog" role="dialog" class="dialog" data-js-dialog>
	<div class="dialog__overlay" data-js-dialog-overlay>
		<div class="dialog__content" data-js-dialog-content>
			<div class="dialog-card">
				<div class="dialog-card__header">
					<h3 class="dialog-card__title italic ff-tt-norms-pro" data-js-dialog-title>Заказать звонок</h3>
					<?php if ( ! empty( ADDRESS ) ) : ?>
						<p class="dialog-card__subtitle"><?php echo esc_html( ADDRESS ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( MAP_LINK ) ) : ?>
						<?php
						$map_link_attributes = array(
							'class'  => 'dialog-card__link menu-link active',
							'href'   => MAP_LINK['url'],
							'title'  => MAP_LINK['title'],
							'target' => MAP_LINK['target'],
						);

						if ( ! empty( $map_link_attributes['target'] ) && '_blank' === $map_link_attributes['target'] ) {
							$aria_label                        = MAP_LINK['title'] . ' (Открыть в новой вкладке)';
							$map_link_attributes['rel']        = 'noopener noreferrer';
							$map_link_attributes['aria-label'] = $aria_label;
							$map_link_attributes['title']      = $aria_label;
						}

						$map_link_attributes_str = loveforever_prepare_tag_attributes_as_string( $map_link_attributes );
						?>
						<a <?php echo $map_link_attributes_str; ?>><?php echo esc_html( MAP_LINK['title'] ); ?></a>
					<?php endif; ?>
				</div>
				<div class="dialog-card__body">
					<form id="globalCallbackForm" class="fitting-form" data-js-callback-form>
						<fieldset class="fitting-form__group">
							<div class="fitting-form__group-body">
								<div class="field">
									<input 
										type="text" 
										class="field__control" 
										name="name" 
										placeholder="Имя" 
										id="globalFittingDialogNameField"
										autocomplete="name"
									>
								</div>
								<div class="field">
									<input 
										id="globalFittingDialogPhoneField"
										type="text" 
										class="field__control" 
										name="phone" 
										placeholder="Телефон" 
										data-js-input-mask="phone"
										autocomplete="tel"
										inputmode="tel"
										required
									>
								</div>
								<button type="submit" class="button" data-js-callback-form-submit-button>Заказать звонок</button>
							</div>
						</fieldset>
						<div class="fitting-form__errors" data-js-callback-form-errors hidden></div>
						<input type="hidden" name="action" value="loveforever_request_callback">
						<?php wp_nonce_field( 'submit_callback_form', 'submit_callback_form_nonce' ); ?>
					</form>
					<button type="dialog-card__body-button button" class="button" disabled hidden data-js-dialog-close-button>Закрыть</button>
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
