<?php
/**
 * Global Fitting Dialog
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$can_edit_fittings                 = loveforever_is_user_has_manager_capability();
$date_with_nearest_available_slots = Fitting_Slots::get_nearest_available_date();
?>
<div id="globalFittingDialog" role="dialog" class="dialog" data-js-dialog>
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
					<form id="globalDressFittingFormSimpler" class="fitting-form" data-js-fitting-form>
						<button type="button" class="fitting-form__back" data-js-fitting-form-back-button disabled>
							<svg width="9" height="16" viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M0.707107 7.14201L0.00075584 7.84914L0.707862 8.55621L7.77817 15.6273L8.48528 14.9202L1.41421 7.84912L8.48528 0.778053L7.77818 0.070946L0.707107 7.14201Z" fill="black"/>
							</svg>
						</button>
						<fieldset class="fitting-form__step" data-js-fitting-form-step>
							<fieldset class="fitting-form__group">
								<div class="fitting-form__group-header">
									<p class="fitting-form__group-heading">Какие платья желаете примерить?</p>
								</div>
								<div class="fitting-form__columns">
									<?php
									$fitting_types = array(
										'wedding' => 'Свадебные',
										'evening' => 'Вечерние',
										'prom'    => 'Выпускные',
									);
									foreach ( $fitting_types as $fitting_type_key => $fitting_type_label ) :
										?>
										<label class="loveforever-checkbox">
											<input 
												class="loveforever-checkbox__control" 
												type="checkbox" 
												name="fitting_type[]" 
												value="<?php echo esc_attr( $fitting_type_key ); ?>"
											>
											<span class="loveforever-checkbox__label"><?php echo esc_html( $fitting_type_label ); ?></span>
										</label>
									<?php endforeach; ?>
									<span class="field__errors" data-js-form-field-errors></span>
								</div>
								<?php
								if ( $can_edit_fittings ) :
									?>
									<div class="fitting-form__columns">
										<?php
										$fitting_steps = array(
											'delivery'   => 'Выдача',
											'fitting'    => 'Подгонка',
											're-fitting' => 'Повтор',
										);
										foreach ( $fitting_steps as $fitting_step_key => $fitting_step_label ) :
											?>
											<label class="loveforever-radio">
												<input
													id="<?php echo esc_attr( 'fittingStep-' . $fitting_step_key ); ?>"
													type="radio"
													name="fitting_step"
													class="loveforever-radio__control"
													value="<?php echo esc_attr( $fitting_step_key ); ?>"
												>
												<span class="loveforever-radio__label"><?php echo esc_html( $fitting_step_label ); ?></span>
											</label>
										<?php endforeach; ?>
										<span class="field__errors" data-js-form-field-errors></span>
									</div>
								<?php endif; ?>
								<div class="fitting-form__group-body">
									<div class="fitting-form__double">
										<div class="field field--date" data-js-datepicker>
											<?php
											$date_input_attributes = array(
												'type'  => 'date',
												'name'  => 'date',
												'class' => 'field__control',
												'id'    => 'globalDressFittingFormSimplerDateControl',
												'value' => $date_with_nearest_available_slots,
												'data-js-datepicker-original-control' => '',
											);
	
											if ( ! $can_edit_fittings ) {
												$date_input_attributes['min'] = $date_with_nearest_available_slots;
											}
	
											$date_input_attributes_str = loveforever_prepare_tag_attributes_as_string( $date_input_attributes );
											?>
											<input <?php echo $date_input_attributes_str; ?>>
											<?php
											$min_date          = wp_date( 'd F (D)', strtotime( $date_with_nearest_available_slots ) );
											$datepicker_config = array(
												'minDate' => $min_date,
											);
											?>
											<input 
												type="text" 
												name="altdate" 
												id="globalDressFittingFormSimplerCustomDateControl" 
												class="field__control"
												value="<?php echo esc_attr( $min_date ); ?>"
												data-js-datepicker-custom-control
												data-js-datepicker-config="<?php echo esc_attr( wp_json_encode( $datepicker_config ) ); ?>"
											/>
										</div>
										<div class="field field--time">
											<?php
												$slots = Fitting_Slots::get_day_slots( $date_with_nearest_available_slots, current_time( 'timestamp' ) );
	
											if ( ! $can_edit_fittings ) {
												$slots = array_filter(
													$slots,
													function ( $slot ) {
														return $slot['available'] > 0;
													}
												);
											}
											?>
											<select 
												class="field__control"
												name="time" 
												id="globalDressFittingFormSimplerTimeControl" data-js-custom-select
											>
												<?php
												foreach ( $slots as $time => $slot_data ) :
													$option_attributes = array(
														'value' => $time,
													);
													$option_name       = $time;
	
													if ( 0 === $slot_data['available'] ) {
														$option_attributes['disabled'] = '';
													}
	
													if ( $can_edit_fittings ) {
														$option_name .= ' (' . $slot_data['available'] . ' из ' . $slot_data['max_fittings'] . ')';
													}
	
													$option_attributes_str = loveforever_prepare_tag_attributes_as_string( $option_attributes );
													?>
													<option <?php echo $option_attributes_str; ?>>
														<?php echo esc_html( $option_name ); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="field">
										<input 
											type="text" 
											class="field__control" 
											name="name" 
											placeholder="Имя" 
											id="globalDressFittingFormSimplerNameField"
											autocomplete="name"
										>
									</div>
									<div class="field">
										<input 
											id="globalDressFittingFormSimplerPhoneField"
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
									<?php if ( is_singular( 'dress' ) ) : ?>
										<input type="hidden" name="target_dress" value="<?php echo esc_attr( get_the_ID() ); ?>">
									<?php endif; ?>
									<?php if ( ! $can_edit_fittings && ! empty( $_COOKIE['favorites'] ) ) : ?>
										<input type="hidden" name="client_favorite_dresses" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_COOKIE['favorites'] ) ) ); ?>">
									<?php endif; ?>
									<button type="submit" class="button" data-js-fitting-form-submit-button>Записаться</button>
								</div>
							</fieldset>
						</fieldset>
						<div class="fitting-form__errors" data-js-fitting-form-errors hidden></div>
						<?php wp_nonce_field( 'submit_fitting_form', 'submit_fitting_form_nonce' ); ?>
					</form>
					<button type="dialog-card__body-button button" class="button" disabled hidden data-js-dialog-close-button>Хорошо</button>
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
