<?php
/**
 * Global Fitting Dialog
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$can_edit_fittings = current_user_can( 'edit_fittings' ) || current_user_can( 'manage_options' );
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
					<form id="globalDressFittingForm" class="fitting-form" data-js-fitting-form>
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
							</fieldset>
							<fieldset class="fitting-form__group fitting-form__group--calendar">
								<div class="fitting-form__group-header">
									<p class="fitting-form__group-heading">Выберите день и время</p>
									<div class="fitting-form__actions">
										<button type="button" class="fitting-form__actions-button fitting-form__actions-button--prev" disabled data-js-fitting-form-prev-slots-button>
											<svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" clip-rule="evenodd" d="M5.24977 4.28598L0.74993 0L0 0.714289L4.49984 5.00027L0.000560648 9.28571L0.750491 10L6 4.99998L5.25007 4.28569L5.24977 4.28598Z" fill="black"/>
											</svg>
										</button>
										<button type="button" class="fitting-form__actions-button fitting-form__actions-button--next" data-js-fitting-form-next-slots-button>
											<svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" clip-rule="evenodd" d="M5.24977 4.28598L0.74993 0L0 0.714289L4.49984 5.00027L0.000560648 9.28571L0.750491 10L6 4.99998L5.25007 4.28569L5.24977 4.28598Z" fill="black"/>
											</svg>
										</button>
									</div>
								</div>
								<?php
								$current_date = gmdate( 'd.m.Y', current_time( 'timestamp' ) );
								$end_date     = gmdate( 'd.m.Y', strtotime( '+2 days', current_time( 'timestamp' ) ) );
								$slots_range  = Fitting_Slots::get_slots_range( $current_date, $end_date );
								?>
								<div class="fitting-form__columns" data-js-fitting-form-slots-container>
									<?php
									foreach ( $slots_range as $slots_range_date => $slots ) :
										?>
										<div class="fitting-form__day-column">
											<div class="fitting-form__day-column-head">
												<label class="fitting-form__day-input loveforever-radio">
													<!-- <input class="radio__input" type="radio" name="date" id="" value="01.02"> -->
													<span class="radio__label"><?php echo esc_html( date_i18n( 'd.m (D)', strtotime( $slots_range_date ) ) ); ?></span>
												</label>
											</div>
											<ol class="fitting-form__day-column-list">
												<?php foreach ( $slots as $time => $slot ) : ?>
												<li class="fitting-form__day-column-list-item">
													<label class="loveforever-radio">
														<input 
															class="loveforever-radio__control" 
															type="radio" 
															name="time" 
															id="<?php echo esc_attr( 'globalDressFittingTimeField' . $time ); ?>" 
															value="<?php echo esc_attr( $time ); ?>"
															<?php echo 0 === $slot['available'] ? 'disabled' : ''; ?>
															data-js-fitting-form-date-value="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $slots_range_date ) ) ); ?>"
														>
														<span class="loveforever-radio__label">
															<?php echo esc_html( $time ); ?>
															<?php echo $can_edit_fittings ? '(' . $slot['available'] . ')' : ''; ?>
														</span>
													</label>
												</li>
												<?php endforeach; ?>
											</ol>
										</div>
										<?php
									endforeach;
									?>
								</div>
								<input type="hidden" name="date" value="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>" data-js-fitting-form-date-control>
							</fieldset>
						</fieldset>
						<fieldset class="fitting-form__step" data-js-fitting-form-step hidden>
							<fieldset class="fitting-form__group">
								<div class="fitting-form__group-header">
									<p class="fitting-form__group-heading" data-js-fitting-form-selected-date></p>
								</div>
								<div class="fitting-form__group-body">
									<div class="field">
										<input 
											type="text" 
											class="field__control" 
											name="name" 
											placeholder="Имя" 
											id="globalDressFittingFormNameField"
										>
									</div>
									<div class="field">
										<input 
											id="globalDressFittingFormPhoneField"
											type="text" 
											class="field__control" 
											name="phone" 
											placeholder="Телефон" 
											data-js-input-mask="+{7} (000) 000-00-00">
									</div>
									<input type="hidden" name="target_dress" value="<?php echo esc_attr( get_the_ID() ); ?>">
									<?php if ( ! empty( $_COOKIE['favorites'] ) ) : ?>
										<input type="hidden" name="client_favorite_dresses" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_COOKIE['favorites'] ) ) ); ?>">
									<?php endif; ?>
									<button type="submit" class="button" data-js-fitting-form-submit-button>Записаться</button>
								</div>
								<div class="fitting-form__group-footer">
									<p>Нажимая записаться вы соглашаетесь с <a class="menu-link" href="#">политикой конфиденциальности</a></p>
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
