<?php
/**
 * Template name: Записи на примерку
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;

get_header(
	null,
	array(
		'data-wf-page'                  => '672b5edfcfeb9652455dadc7',
		'barba-container-extra-classes' => array( 'white-top' ),
		'namespace'                     => 'favorite-products',
	)
);

$favorites      = ! empty( $_COOKIE['favorites'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['favorites'] ) ) : '';
$favorites_link = esc_attr( get_the_permalink() . '?favorites=' . $favorites );

$post            = get_post( 471 );
$fitting_id      = get_query_var( 'fitting_id' );
$admin_page_link = get_the_permalink();
$page_title      = ! empty( $fitting_id ) ? 'Редактировать примерку' : get_the_title();

$fitting_steps        = array(
	'delivery'   => 'Выдача',
	'fitting'    => 'Подгонка',
	're-fitting' => 'Повтор',
);
$fitting_steps_colors = array(
	'delivery'   => '#F2DEDF',
	'fitting'    => '#FCF9E0',
	're-fitting' => '#E1EFDA',
);
?>
				<section class="section">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/navbar' ); ?>
					</div>
				</section>
				<section class="section admin-fittings" style="padding-top: 100rem">
					<div class="container">
						<h1 class="p-86-96">
						<?php
						echo wp_kses_post( $page_title );
						?>
						</h1>
						<?php if ( post_password_required() ) : ?>
							<?php echo get_the_password_form(); ?>
						<?php else : ?>
							<?php
							if ( ! empty( $fitting_id ) ) :
								$fitting_time = get_field( 'fitting_time', $fitting_id );
								$fitting_type = get_field( 'fitting_type', $fitting_id );
								$name         = get_field( 'name', $fitting_id );
								$phone        = get_field( 'phone', $fitting_id );
								$comment      = get_field( 'comment', $fitting_id );

								$fitting_date  = gmdate( 'Y-m-d', strtotime( $fitting_time ) );
								$fitting_hours = gmdate( 'H:i', strtotime( $fitting_time ) );
								$fitting_step  = get_field( 'fitting_step', $fitting_id );
								?>
								<div class="edit-fitting">
									<a href="<?php echo esc_url( $admin_page_link ); ?>" class="edit-fitting__button button">← Назад к примеркам</a>
									<form id="editFittingForm" class="edit-fitting__form edit-fitting-form" novalidate data-js-form data-js-fitting-form>
										<div class="edit-fitting-form__inner">
											<?php
											if ( ! empty( $_GET['updated'] ) && 'true' === $_GET['updated'] ) :
												?>
												<div class="alert alert--success">
													<p>Примерка успешно обновлена</p>
												</div>
											<?php endif; ?>
											<h2 class="edit-fitting-form__title h2 ff-tt-norms-pro"><?php echo wp_kses_post( get_the_title( $fitting_id ) ); ?></h2>
											<div class="field">
												<label for="fittingDate" class="field__label">Дата</label>
												<input 
													type="date" 
													name="date" 
													id="fittingDate" 
													class="field__control" 
													value="<?php echo esc_attr( $fitting_date ); ?>"
													required
													min="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>"
												>
												<span class="field__errors" id="fittingDateErrors" data-js-form-field-errors></span>
											</div>
											<?php
											$slots = Fitting_Slots::get_day_slots( $fitting_date, current_time( 'timestamp' ), $fitting_id );
											?>
											<div class="field">
												<label for="fittingTime" class="field__label">Время</label>
												<select name="time" id="fittingTime" required data-js-custom-select>
													<?php foreach ( $slots as $time => $slot_object ) : ?>
														<option 
															value="<?php echo esc_attr( $time ); ?>"
															<?php echo $time === $fitting_hours ? 'selected' : ''; ?>
														>
															<?php echo esc_html( $time . ' (Доступно примерок: ' . $slot_object['available'] . ' из ' . $slot_object['max_fittings'] . ')' ); ?>
														</option>
													<?php endforeach; ?>
												</select>
												<span class="field__errors" id="fittingTimeErrors" data-js-form-field-errors></span>
											</div>
											<div class="field">
												<label for="fittingName" class="field__label">Имя</label>
												<input 
													type="text" 
													name="name" 
													id="fittingName" 
													class="field__control"
													value="<?php echo esc_attr( $name ); ?>"
												>
												<span class="field__errors" id="fittingNameErrors" data-js-form-field-errors></span>
											</div>
											<div class="field">
												<label for="fittingPhone" class="field__label">Телефон</label>
												<input 
													type="text" 
													name="phone" 
													id="fittingPhone" 
													class="field__control" 
													value="<?php echo esc_attr( $phone ); ?>"
													data-js-input-mask="phone"
													autocomplete="tel"
													inputmode="tel"
													required
												>
												<span class="field__errors" id="fittingPhoneErrors" data-js-form-field-errors></span>
											</div>
											<div class="field">
												<textarea 
													placeholder="Комментарий" 
													maxlength="5000" 
													id="fittingComment" 
													name="comment" 
													class="field__control"
													rows="10"
												><?php echo esc_html( $comment ); ?></textarea>
												<span class="field__errors" id="fittingCommentErrors" data-js-form-field-errors></span>
											</div>
											<?php
											$fitting_types = array(
												'wedding' => 'Свадебные платья',
												'evening' => 'Вечерние платья',
												'prom'    => 'Выпускные платья',
											);
											?>
											<fieldset class="edit-fitting-form__fieldset">
												<legend>Тип примерки</legend>
												<?php foreach ( $fitting_types as $fitting_type_key => $fitting_type_label ) : ?>
													<label class="loveforever-checkbox">
														<input
															id="<?php echo esc_attr( 'fittingType' . $fitting_type_key ); ?>"
															type="checkbox"
															name="fitting_type[]"
															class="loveforever-checkbox__control"
															value="<?php echo esc_attr( $fitting_type_key ); ?>"
															<?php echo esc_attr( in_array( $fitting_type_key, $fitting_type, true ) ? 'checked' : '' ); ?>
															required
														>
														<span class="loveforever-checkbox__label"><?php echo esc_html( $fitting_type_label ); ?></span>
													</label>
												<?php endforeach; ?>
												<span class="field__errors" data-js-form-field-errors></span>
											</fieldset>
											<fieldset class="edit-fitting-form__fieldset">
												<legend>Этап</legend>
												<?php foreach ( $fitting_steps as $fitting_steps_value => $fitting_steps_label ) : ?>
													<label class="loveforever-radio">
														<input
															id="<?php echo esc_attr( 'fittingStep' . $fitting_steps_value ); ?>"
															type="radio"
															name="fitting_step"
															class="loveforever-radio__control"
															value="<?php echo esc_attr( $fitting_steps_value ); ?>"
															<?php echo $fitting_step === $fitting_steps_value ? 'checked' : ''; ?>
														>
														<span class="loveforever-radio__label"><?php echo esc_html( $fitting_steps_label ); ?></span>
													</label>
												<?php endforeach; ?>
												<span class="field__errors" data-js-form-field-errors></span>
											</fieldset>
											<button type="submit" class="button">Обновить</button>
											<div class="edit-fitting-form__error" data-js-fitting-form-error></div>
										</div>
										<input type="hidden" name="action" value="create_new_fitting_record">
										<input type="hidden" name="fitting-id" value="<?php echo esc_attr( $fitting_id ); ?>">
										<?php wp_nonce_field( 'submit_fitting_form', 'submit_fitting_form_nonce', false ); ?>
									</form>
								</div>
								<?php
							else :
								$today    = wp_date( 'Y-m-d', current_time( 'timestamp' ) );
								$tomorrow = wp_date( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) );
								?>
								<form id="filterFittingForm" class="admin-fittings__filter-form fitting-filter-form" data-js-filter-fitting-form>
									<div class="fitting-filter-form__actions">
										<div class="fitting-step-tags">
											<?php foreach ( $fitting_steps as $fitting_step_key => $fitting_step_label ) : ?>
												<span 
													class="fitting-step-tags__item fitting-step-tag fitting-step-tag--<?php echo esc_attr( $fitting_step_key ); ?>" 
													style="background-color: <?php echo esc_attr( $fitting_steps_colors[ $fitting_step_key ] ); ?>;"
												>
													<?php echo esc_html( $fitting_step_label ); ?>
												</span>
											<?php endforeach; ?>
										</div>
									</div>
									<div class="fitting-filter-form__wrapper">
										<div class="fitting-filter-form__inner">
											<div class="fitting-filter-form__field field">
												<input type="search" name="s" id="filterFittingFormSearchField" placeholder="Поиск" value="" class="field__control">
											</div>
											<div class="fitting-filter-form__field field">
												<input type="date" name="date" id="filterFittingFormDateField" class="field__control">
											</div>
											<button type="button" class="button" data-js-filter-fitting-form-date-button="<?php echo esc_attr( $today ); ?>">Сегодня</button>
											<button type="button" class="button" data-js-filter-fitting-form-date-button="<?php echo esc_attr( $tomorrow ); ?>">Завтра</button>
											<button type="reset" class="button button--link">Сбросить фильтры</button>
											<input type="hidden" name="action" value="filter_fittings">
											<?php wp_nonce_field( 'filter_fittings', '_filter_fitting_nonce', false ); ?>
											<input type='hidden' value='474' name='wpessid' />
										</div>
										<button 
											type="button" 
											class="button button--success" 
											data-js-fitting-form-dialog-button 
											data-js-dialog-open-button="globalFittingDialog"
										>
											Добавить примерку
										</button>
									</div>
								</form>
								<table class="fittings-table">
									<thead class="fittings-table__head">
										<tr>
											<th>Дата / Тип</th>
											<th>Клиент</th>
											<th>Комментарий</th>
											<th>Действия</th>
										</tr>
									</thead>
									<tbody class="fittings-table__body" data-js-filter-fitting-form-fitting-container>
										<?php
										$fittings_query = new WP_Query(
											array(
												'post_type' => 'fitting',
												'posts_per_page' => -1,
												'post_status' => 'publish',
												'meta_key' => 'fitting_time',
												'orderby'  => 'meta_value',
												'order'    => 'ASC',
												'meta_query' => array(
													array(
														'key' => 'fitting_time',
														'value' => $today,
														'compare' => '>=',
														'type' => 'DATETIME',
													),
													array(
														'key' => 'fitting_time',
														'value' => $tomorrow,
														'compare' => '<=',
														'type' => 'DATETIME',
													),
												),
											)
										);

										if ( $fittings_query->have_posts() ) :
											while ( $fittings_query->have_posts() ) :
												$fittings_query->the_post();
												?>
												<?php get_template_part( 'components/fitting-table-row' ); ?>
												<?php
											endwhile;
											wp_reset_postdata();
										else :
											?>
											<tr>
												<td colspan="6">На сегодня нет записей</td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</section>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
		<?php get_footer(); ?>
