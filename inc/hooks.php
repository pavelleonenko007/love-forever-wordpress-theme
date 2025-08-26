<?php
/**
 * Hooks And Filters
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Безопасная обработка HTML с WebP Express.
 *
 * @param string $html HTML контент для обработки.
 * @return string Обработанный HTML контент.
 */
function loveforever_process_html_with_webp( $html ) {
	// Проверяем, что плагин WebP Express активирован.
	if ( ! function_exists( 'is_plugin_active' ) ) {
		// Если функция недоступна, проверяем наличие файла плагина.
		if ( ! file_exists( ABSPATH . 'wp-content/plugins/webp-express/webp-express.php' ) ) {
			return $html;
		}
	} elseif ( ! is_plugin_active( 'webp-express/webp-express.php' ) ) {
		return $html;
	}

	// Проверяем, что функция alter HTML включена.
	if ( ! get_option( 'webp-express-alter-html', false ) ) {
		return $html;
	}

	// Подключаем основной файл плагина для инициализации автозагрузчика.
	$plugin_file = ABSPATH . 'wp-content/plugins/webp-express/webp-express.php';
	if ( ! file_exists( $plugin_file ) ) {
		return $html;
	}

	// Подключаем основной файл плагина для инициализации автозагрузчика.
	require_once $plugin_file;

	// Подключаем Composer автозагрузчик для vendor классов.
	$vendor_autoload = ABSPATH . 'wp-content/plugins/webp-express/vendor/autoload.php';
	if ( file_exists( $vendor_autoload ) ) {
		require_once $vendor_autoload;
	}

	// Подключаем необходимые классы.
	$alter_html_picture_file    = ABSPATH . 'wp-content/plugins/webp-express/lib/classes/AlterHtmlPicture.php';
	$alter_html_image_urls_file = ABSPATH . 'wp-content/plugins/webp-express/lib/classes/AlterHtmlImageUrls.php';

	if ( ! file_exists( $alter_html_picture_file ) || ! file_exists( $alter_html_image_urls_file ) ) {
		return $html;
	}

	require_once $alter_html_picture_file;
	require_once $alter_html_image_urls_file;

	// Проверяем, что необходимые классы загружены.
	if ( ! class_exists( 'DOMUtilForWebP\\PictureTags' ) || ! class_exists( 'DOMUtilForWebP\\ImageUrlReplacer' ) ) {
		return $html;
	}

	// Обрабатываем HTML с обработкой ошибок.
	try {
		if ( get_option( 'webp-express-alter-html-replacement' ) == 'picture' ) {
			if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
				// Для AMP страниц тег <picture> не разрешен.
				return $html;
			}
			return \WebPExpress\AlterHtmlPicture::replace( $html );
		} else {
			return \WebPExpress\AlterHtmlImageUrls::replace( $html );
		}
	} catch ( Exception $e ) {
		// В случае ошибки возвращаем исходный контент.
		return $html;
	}
}

add_filter( 'wp_mail_from_name', 'loveforever_wp_mail_from_name' );
function loveforever_wp_mail_from_name( $from_name ) {
	$site_name = get_bloginfo( 'name' );
	$from_name = $site_name;
	return $from_name;
}

add_action( 'admin_menu', 'loveforever_remove_comments_admin_menu' );
function loveforever_remove_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}

/*
add_action( 'init', 'loveforever_create_manager_role' ); */
/*
function loveforever_create_manager_role() { */
/*  remove_role( 'manager' ); */
/**

/
/*  add_role( */
/*
		'manager', */
/*
		'Менеджер', */
/*
		array( */
/*
			'read'         => true, */
/*
			'edit_posts'   => false, */
/*
			'upload_files' => false, */
/*
		) */
/*
	); */
/* } */

/*
add_action( 'init', 'loveforever_register_fitting_capabilities' ); */
/*
function loveforever_register_fitting_capabilities() { */
/*  $roles = array( 'manager', 'administrator' ); */
/**

/
/*  foreach ( $roles as $role_name ) { */
/*
		$role = get_role( $role_name ); */
/*
		if ( $role ) { */
/*
			$role->add_cap( 'read' ); */
/*
			$role->add_cap( 'publish_fittings' ); */
/*
			$role->add_cap( 'edit_fittings' ); */
/*
			$role->add_cap( 'edit_others_fittings' ); */
/*
			$role->add_cap( 'edit_published_fittings' ); */
/*
			$role->add_cap( 'read_private_fittings' ); */
/*
			$role->add_cap( 'edit_private_fittings' ); */
/*
			$role->add_cap( 'delete_fittings' ); */
/*
			$role->add_cap( 'delete_published_fittings' ); */
/*
			$role->add_cap( 'delete_private_fittings' ); */
/*
			$role->add_cap( 'delete_others_fittings' ); */
/*
		} */
/*
	} */
/* } */

add_action( 'wp_ajax_create_new_fitting_record', 'loveforever_create_new_fitting_record_via_ajax' );
add_action( 'wp_ajax_nopriv_create_new_fitting_record', 'loveforever_create_new_fitting_record_via_ajax' );
function loveforever_create_new_fitting_record_via_ajax() {
	if ( ! isset( $_POST['submit_fitting_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['submit_fitting_form_nonce'] ) ), 'submit_fitting_form' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка в запросе',
				'debug'   => 'Невалидный nonce',
			),
			400
		);
	}

	if ( ! isset( $_POST['name'] ) || empty( $_POST['name'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Пожалуйста, введите ваше имя',
				'debug'   => 'Поле имя не заполнено',
			),
			400
		);
	}

	if ( ! isset( $_POST['phone'] ) || empty( $_POST['phone'] ) || ! loveforever_is_valid_phone( sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) ) {
		wp_send_json_error(
			array(
				'message' => 'Пожалуйста, введите корректный номер телефона',
				'debug'   => 'Некорректный номер телефона',
			),
			400
		);
	}

	// if ( ! isset( $_POST['fitting_type'] ) || empty( $_POST['fitting_type'] ) ) {
	// wp_send_json_error(
	// array(
	// 'message' => 'Пожалуйста, укажите тип платья',
	// 'debug'   => 'Не указана категория платья',
	// ),
	// 400
	// );
	// }

	if ( ! isset( $_POST['date'] ) || empty( $_POST['date'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Пожалуйста, укажите желаемую дату примерки',
				'debug'   => 'Не указана желаемая дата примерки',
			),
			400
		);
	}

	if ( ! isset( $_POST['time'] ) || empty( $_POST['time'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Пожалуйста, укажите время',
				'debug'   => 'Не указано желаемое время примерки',
			),
			400
		);
	}

	if ( ! class_exists( 'AppointmentManager' ) ) {
		// require_once get_template_directory() . '/includes/bootstrap.php';
	}

	$fitting_id                       = ! empty( $_POST['fitting-id'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['fitting-id'] ) ) ) : 0;
	$name                             = sanitize_text_field( wp_unslash( $_POST['name'] ) );
	$phone                            = sanitize_text_field( wp_unslash( $_POST['phone'] ) );
	$fitting_type                     = '';
	$fitting_step                     = ! empty( $_POST['fitting_step'] ) ? sanitize_text_field( wp_unslash( $_POST['fitting_step'] ) ) : '';
	$date                             = sanitize_text_field( wp_unslash( $_POST['date'] ) );
	$time                             = sanitize_text_field( wp_unslash( $_POST['time'] ) );
	$comment                          = ! empty( $_POST['comment'] ) ? sanitize_textarea_field( wp_unslash( $_POST['comment'] ) ) : '';
	$ip_address                       = ! empty( $_POST['ip-address'] ) ? sanitize_text_field( wp_unslash( $_POST['ip-address'] ) ) : '';
	$target_dress                     = ! empty( $_POST['target_dress'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['target_dress'] ) ) : 0;
	$client_favorite_dresses          = ! empty( $_POST['client_favorite_dresses'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['client_favorite_dresses'] ) ) ) : array();
	$has_change_fittings_capabilities = loveforever_is_user_has_manager_capability();

	if ( ! empty( $_POST['fitting_type'] ) ) {
		$fitting_type = is_array( $_POST['fitting_type'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['fitting_type'] ) ) : sanitize_text_field( wp_unslash( $_POST['fitting_type'] ) );
	}

	if ( ! $has_change_fittings_capabilities && 'delivery' !== $fitting_step ) {
		$is_valid_fitting_time = loveforever_is_valid_fitting_datetime( $date . ' ' . $time, $fitting_type, $fitting_id );

		if ( true !== $is_valid_fitting_time ) {
			wp_send_json_error(
				array(
					'message' => $is_valid_fitting_time,
					'debug'   => 'Невалидное время примерки',
				),
				400
			);
		}
	}

	$fitting_post_data = array(
		'post_title'  => 'Новая примерка для ' . $name,
		'post_status' => 'publish',
		'post_type'   => 'fitting',
	);

	if ( 0 !== $fitting_id ) {
		$fitting_post_data['ID'] = $fitting_id;
	}

	$fitting_post_id = wp_insert_post(
		$fitting_post_data
	);

	if ( is_wp_error( $fitting_post_id ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка при создании заявки. Обновите страницу и попробуйте еще раз',
				'debug'   => $fitting_post_id,
			),
			400
		);
	}

	update_field( 'fitting_time', $date . ' ' . $time, $fitting_post_id );

	update_field( 'phone', $phone, $fitting_post_id );
	$phone_formatted = substr( preg_replace( '/[^0-9]/', '', $phone ), 1 );

	update_post_meta( $fitting_post_id, 'phone_formatted_7', '7' . $phone_formatted );
	update_post_meta( $fitting_post_id, 'phone_formatted_8', '8' . $phone_formatted );

	update_field( 'name', $name, $fitting_post_id );

	if ( ! empty( $comment ) ) {
		update_field( 'comment', $comment, $fitting_post_id );
	}

	if ( ! empty( $fitting_type ) ) {
		update_field( 'fitting_type', $fitting_type, $fitting_post_id );
	}

	if ( ! empty( $fitting_step ) ) {
		update_field( 'fitting_step', $fitting_step, $fitting_post_id );
	}

	if ( ! empty( $ip_address ) ) {
		update_field( 'ip_address', $ip_address, $fitting_post_id );
	}

	if ( ! empty( $target_dress ) ) {
		update_field( 'target_dress', $target_dress, $fitting_post_id );
	}

	if ( ! empty( $client_favorite_dresses ) ) {
		update_field( 'client_favorite_dresses', $client_favorite_dresses, $fitting_post_id );
	}

	// do_action( 'acf/save_post', $fitting_post_id );

	$sended_email = loveforever_send_fitting_email_notification( $fitting_post_id );

	wp_send_json_success(
		array(
			'fitting_type' => $fitting_type,
			'message'      => 0 === $fitting_id ? 'Вы успешно записались на примерку' : 'Запись на примерку успешно обновлена',
			'debug'        => array(
				'sended_email' => $sended_email,
			),
		),
		201
	);
}

add_action( 'wp_ajax_delete_fitting', 'loveforever_delete_fitting_via_ajax' );
add_action( 'wp_ajax_nopriv_delete_fitting', 'loveforever_delete_fitting_via_ajax' );
function loveforever_delete_fitting_via_ajax() {
	if ( ! isset( $_POST['fitting_id'] ) || empty( $_POST['fitting_id'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка в запросе',
				'debug'   => 'Не указан ID записи',
			),
			400
		);
	}

	$fitting_id = sanitize_text_field( wp_unslash( $_POST['fitting_id'] ) );

	if ( ! isset( $_POST['delete_fitting_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['delete_fitting_nonce'] ) ), "delete_fitting_$fitting_id" ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка в запросе',
				'debug'   => 'Невалидный nonce',
			),
			400
		);
	}

	$delete_result = wp_delete_post( $fitting_id );

	if ( ! $delete_result ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка при удалении записи. Обновите страницу и попробуйте еще раз',
				'debug'   => "Ошибка: $delete_result",
			),
		);
	}

	wp_send_json_success(
		array(
			'message' => 'Запись успешно удалена',
		)
	);
}

add_action( 'wp_ajax_filter_fittings', 'loveforever_filter_fittings_via_ajax' );
add_action( 'wp_ajax_nopriv_filter_fittings', 'loveforever_filter_fittings_via_ajax' );
function loveforever_filter_fittings_via_ajax() {
	if ( ! isset( $_POST['_filter_fitting_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_filter_fitting_nonce'] ) ), 'filter_fittings' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка в запросе',
				'debug'   => 'Невалидный nonce',
			),
			400
		);
	}

	$today         = wp_date( 'Y-m-d', current_time( 'timestamp' ) );
	$selected_date = ! empty( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
	$start_date    = ! empty( $selected_date ) && strtotime( $selected_date ) > strtotime( $today ) ? $selected_date : $today;
	$next_date     = wp_date( 'Y-m-d', strtotime( $start_date . ' +1 day' ) );

	$fittings_query_args = array(
		'post_type'      => 'fitting',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_key'       => 'fitting_time',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => 'fitting_time',
				'value'   => $start_date,
				'compare' => '>=',
				'type'    => 'DATETIME',
			),
			array(
				'key'     => 'fitting_time',
				'value'   => $next_date,
				'compare' => '<=',
				'type'    => 'DATETIME',
			),
		),
	);

	if ( ! empty( $_POST['s'] ) ) {
		$fittings_query_args['s'] = sanitize_text_field( wp_unslash( $_POST['s'] ) );

		unset( $fittings_query_args['meta_query'] );
	}

	$fittings_query = new WP_Query( $fittings_query_args );

	ob_start();

	if ( $fittings_query->have_posts() ) {
		while ( $fittings_query->have_posts() ) {
			$fittings_query->the_post();
			get_template_part( 'components/fitting-table-row' );
		}

		wp_reset_postdata();
	} else { ?>
		<tr>
			<td colspan="6">На выбранную дату нет записей</td>
		</tr>
		<?php
	}

	wp_send_json_success(
		array(
			'message' => "Примерки на $start_date успешно получены!",
			'html'    => ob_get_clean(),
			'debug'   => array(
				'body'                => $_POST,
				'fittings_query_args' => $fittings_query_args,
				'fittings_query'      => $fittings_query,
			),
		)
	);
}

add_action( 'wp_ajax_get_date_time_slots', 'loveforever_get_date_time_slots_via_ajax' );
add_action( 'wp_ajax_nopriv_get_date_time_slots', 'loveforever_get_date_time_slots_via_ajax' );
function loveforever_get_date_time_slots_via_ajax() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка запроса!',
			),
			400
		);
	}

	$date_increment_ratio = ! empty( $_POST['date-increment-ratio'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['date-increment-ratio'] ) ) : 0;

	$start_date        = gmdate( 'd.m.Y', strtotime( '+' . 3 * $date_increment_ratio . 'days', current_time( 'timestamp' ) ) );
	$end_date          = gmdate( 'd.m.Y', strtotime( '+' . 3 * $date_increment_ratio + 2 . 'days', current_time( 'timestamp' ) ) );
	$slots_range       = Fitting_Slots::get_slots_range( $start_date, $end_date );
	$can_edit_fittings = current_user_can( 'edit_fittings' ) || current_user_can( 'manage_options' );

	$html = '';

	ob_start();
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
							<?php echo ! $can_edit_fittings && 0 === $slot['available'] ? 'disabled' : ''; ?>
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

	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'message' => 'Слоты успешно загружены',
			'html'    => $html,
		),
		200
	);
}

add_action( 'wp_ajax_get_fitting_time_slots', 'loveforever_get_date_fitting_time_slots_via_ajax' );
add_action( 'wp_ajax_nopriv_get_fitting_time_slots', 'loveforever_get_date_fitting_time_slots_via_ajax' );
function loveforever_get_date_fitting_time_slots_via_ajax() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка запроса!',
				'debug'   => 'Некорректный nonce',
			),
			400
		);
	}

	if ( empty( $_POST['date'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Укажите желаемую дату примерки',
				'debug'   => 'Не указана дата примерки',
			),
			400
		);
	}

	$fitting_id             = ! empty( $_POST['fitting-id'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['fitting-id'] ) ) ) : null;
	$date                   = sanitize_text_field( wp_unslash( $_POST['date'] ) );
	$can_user_edit_fittings = loveforever_is_user_has_manager_capability();
	$fitting_slots_for_date = Fitting_Slots::get_day_slots( $date, current_time( 'timestamp' ), $fitting_id );

	if ( ! $can_user_edit_fittings ) {
		$fitting_slots_for_date = array_filter(
			$fitting_slots_for_date,
			function ( $slot ) {
				return $slot['available'] > 0;
			}
		);
	}

	wp_send_json_success(
		array(
			'slots'        => $fitting_slots_for_date,
			'disableSlots' => ! $can_user_edit_fittings,
			'message'      => "Слоты для $date успешно загружены",
		)
	);
}

add_action( 'wp_ajax_get_filtered_products', 'loveforever_get_filtered_products_via_ajax' );
add_action( 'wp_ajax_nopriv_get_filtered_products', 'loveforever_get_filtered_products_via_ajax' );
function loveforever_get_filtered_products_via_ajax() {
	// Проверка nonce
	if ( ! isset( $_POST['submit_filter_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['submit_filter_form_nonce'] ) ), 'submit_filter_form' ) ) {
		wp_send_json_error( array( 'message' => 'Ошибка в запросе' ), 400 );
	}

	// Параметры запроса
	$taxonomy    = ! empty( $_POST['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) : '';
	$term_id     = ! empty( $_POST[ $taxonomy ] ) ? (int) sanitize_text_field( wp_unslash( $_POST[ $taxonomy ] ) ) : 0;
	$silhouette  = ! empty( $_POST['silhouette'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['silhouette'] ) ) : null;
	$price_range = loveforever_get_product_price_range( $term_id );
	$min_price   = ! empty( $_POST['min-price'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['min-price'] ) ) : $price_range['min_price'];
	$max_price   = ! empty( $_POST['max-price'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['max-price'] ) ) : $price_range['max_price'];
	$page        = ! empty( $_POST['page'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['page'] ) ) : 1;
	$orderby     = ! empty( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'views';

	$posts_per_page         = intval( get_field( 'products_per_page', 'option' ) );
	$promo_insert_positions = range( 2, $posts_per_page, 6 ); // Позиции 2, 8, 14, 20
	$promo_needed           = count( $promo_insert_positions );

	// Проверка возможности показа промо
	$can_show_promo = true;
	if (
		( ! empty( $_POST['min-price'] ) && $min_price != $price_range['min_price'] ) ||
		( ! empty( $_POST['max-price'] ) && $max_price != $price_range['max_price'] ) ||
		! empty( $_POST['silhouette'] ) ||
		! empty( $_POST['brand'] ) ||
		! empty( $_POST['style'] ) ||
		! empty( $_POST['color'] ) ||
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
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => array( $term_id ),
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

	// Добавляем таксономию если указана
	if ( $taxonomy && $term_id ) {
		$products_query_args['tax_query'][] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => array( $term_id ),
		);
	}

	// Добавляем сортировку
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
			$products_query_args['meta_query']['product_views_count']       = array(
				'key'     => 'product_views_count',
				'compare' => 'EXISTS',
				'type'    => 'NUMERIC',
			);
			$products_query_args['meta_query'][ 'dress_order_' . $term_id ] = array(
				'key'     => 'dress_order_' . $term_id,
				'compare' => 'EXISTS',
				'type'    => 'NUMERIC',
			);
			$products_query_args['orderby']                                 = array(
				'dress_order_' . $term_id => 'ASC',
				'product_views_count'     => 'DESC',
			);
			break;
	}

	// Добавляем фильтры
	$filters = array( 'silhouette', 'brand', 'style', 'color' );
	foreach ( $filters as $filter ) {
		if ( ! empty( $_POST[ $filter ] ) ) {
			$terms = is_array( $_POST[ $filter ] ) ?
				array_map( 'intval', $_POST[ $filter ] ) :
				array( intval( $_POST[ $filter ] ) );

			$products_query_args['tax_query'][] = array(
				'taxonomy' => $filter,
				'field'    => 'term_id',
				'terms'    => $terms,
			);
		}
	}

	// Подсчет общего количества товаров
	$products_count_query = new WP_Query( $products_query_args );
	$total_products       = $products_count_query->found_posts;

	// Подсчет промо-блоков
	$total_promos = 0;
	if ( $can_show_promo && $taxonomy && $term_id ) {
		$promo_count_query = new WP_Query(
			array(
				'post_type'      => 'promo_blocks',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'post_status'    => 'publish',
				'meta_key'       => 'promo_order_' . $term_id,
				'orderby'        => 'meta_value_num', // Сортировка по числовому значению
				'order'          => 'ASC',
				'tax_query'      => array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => array( $term_id ),
					),
				),
			)
		);
		$total_promos      = $promo_count_query->found_posts;
	}

	// Расчет доступных промо на текущей странице
	$promo_offset    = ( $page - 1 ) * $promo_needed;
	$promo_remaining = max( 0, $total_promos - $promo_offset );
	$promo_available = min( $promo_needed, $promo_remaining );

	// Новый расчет смещения для товаров
	$prev_promos_inserted = min( ( $page - 1 ) * $promo_needed, $total_promos );
	$products_offset      = max( 0, ( $page - 1 ) * $posts_per_page - $prev_promos_inserted );

	// Расчет количества товаров для выборки
	$products_to_fetch = $posts_per_page - $promo_available;

	// Основной запрос товаров
	$products_query_args['posts_per_page'] = $products_to_fetch;
	$products_query_args['offset']         = $products_offset;
	unset( $products_query_args['fields'] );
	$products_query = new WP_Query( $products_query_args );
	$products       = $products_query->posts;

	// Запрос промо-блоков
	$promo_posts = array();
	if ( $can_show_promo && $promo_available > 0 && $taxonomy && $term_id ) {
		$promo_posts = get_posts(
			array(
				'post_type'      => 'promo_blocks',
				'posts_per_page' => $promo_available,
				'offset'         => $promo_offset,
				'post_status'    => 'publish',
				'meta_key'       => 'promo_order_' . $term_id, // Указываем ключ метаполя
				'orderby'        => 'meta_value_num', // Сортировка по числовому значению
				'order'          => 'ASC',
				'tax_query'      => array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => array( $term_id ),
					),
				),
			)
		);
	}

	// Формирование общего массива
	$all_posts = $products;
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

	global $wp_query;
	$wp_query->posts         = $all_posts;
	$wp_query->post_count    = count( $all_posts );
	$wp_query->found_posts   = $total_items;
	$wp_query->max_num_pages = $max_num_pages;

	// Генерация HTML
	ob_start();
	if ( ! empty( $all_posts ) ) {
		$card_index = 1;
		foreach ( $all_posts as $post_item ) {
			setup_postdata( $post_item );

			if ( 'promo_blocks' === $post_item->post_type ) {
				$template_slug = get_post_meta( $post_item->ID, 'promo_template', true );
				if ( $template_slug ) {
					get_template_part( 'template-parts/promo-blocks/' . $template_slug, null, array( 'post_object' => $post_item ) );
				}
			} else {
				?>
				<div class="test-grid">
					<?php
					$position_in_block = ( $card_index - 1 ) % 6 + 1;
					$size              = in_array( $position_in_block, array( 3, 4 ) ) ? 'full' : 'large';
					get_template_part(
						'components/dress-card',
						null,
						array(
							'size'        => $size,
							'is_paged'    => $page > 1,
							'post_object' => $post_item,
						)
					);
					?>
				</div>
				<?php
			}
			++$card_index;
			wp_reset_postdata();
		}
	} else {
		echo '<div class="empty-content"><p>Товары с заданными параметрами не найдены</p><button type="reset" class="button" form="catalogFilterForm">Очистить фильтры</button></div>';
	}
	$feed = ob_get_clean();

	// Генерация пагинации
	ob_start();
	$base_url                              = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
	$pagination_query                      = new WP_Query();
	$pagination_query->found_posts         = $total_items;
	$pagination_query->max_num_pages       = $max_num_pages;
	$pagination_query->query_vars['paged'] = $page;

	echo loveforever_get_pagination_html(
		$pagination_query,
		array(
			'base_url'        => $base_url,
			'is_catalog_page' => true,
		)
	);
	$pagination = ob_get_clean();

	// Безопасная обработка HTML с WebP Express
	$processed_feed = loveforever_process_html_with_webp( $feed );

	// Возврат результата
	wp_send_json_success(
		array(
			'message'    => 'Товары получены успешно!',
			'feed'       => $processed_feed,
			'pagination' => $pagination,
		),
		200
	);
}

add_action( 'wp_ajax_track_product_view', 'loveforever_track_product_view_via_ajax' );
add_action( 'wp_ajax_nopriv_track_product_view', 'loveforever_track_product_view_via_ajax' );
function loveforever_track_product_view_via_ajax() {
	// if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
	// wp_send_json_error(
	// array(
	// 'message' => 'Ошибка запроса!',
	// ),
	// 400
	// );
	// }

	if ( empty( $_POST['product_id'] ) ) {
		wp_send_json_error( array( 'message' => 'Id товара не передан!' ) );
	}

	$product_id = (int) sanitize_text_field( wp_unslash( $_POST['product_id'] ) );

	loveforever_update_viewed_products( $product_id );

	wp_send_json_success(
		array(
			'message' => "Товар с ID $product_id успешно добавлен в просмотренные",
		),
		201
	);
}

add_action( 'wp_ajax_add_review', 'loveforever_add_review_via_ajax' );
add_action( 'wp_ajax_nopriv_add_review', 'loveforever_add_review_via_ajax' );
function loveforever_add_review_via_ajax() {
	if ( ! isset( $_POST['_submit_review_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_submit_review_form_nonce'] ) ), 'submit_review_form' ) ) {
		wp_send_json_error(
			array( 'message' => 'Ошибка в запросе' ),
			400
		);
	}

	$errors = array();

	$rating      = ! empty( $_POST['rating'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['rating'] ) ) ) : null;
	$name        = ! empty( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$date        = ! empty( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
	$review_text = ! empty( $_POST['review_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['review_text'] ) ) : '';

	if ( empty( $rating ) ) {
		$errors['rating'] = 'Пожалуйста, поставьте оценку';
	}

	if ( empty( $name ) ) {
		$errors['name'] = 'Пожалуйста, укажите ваше имя';
	}

	if ( empty( $date ) ) {
		$errors['date'] = 'Пожалуйста, укажите дату';
	}

	if ( empty( $review_text ) ) {
		$errors['review_text'] = 'Поле отзыва не может быть пустым';
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error(
			array(
				'message' => 'Заполните необходимые поля',
				'errors'  => $errors,
			),
			400
		);
	}

	$review_post_id = wp_insert_post(
		array(
			'post_type'    => 'review',
			'post_title'   => "Отзыв от $date от $name",
			'post_content' => '<p></p>',
			'post_status'  => 'pending',
		)
	);

	update_field( 'rating', $rating, $review_post_id );
	update_field( 'author', $name, $review_post_id );
	update_field( 'review_text', $review_text, $review_post_id );

	if ( ! empty( $_FILES ) && isset( $_FILES['file'] ) && ! empty( $_FILES['file'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		if ( is_array( $_FILES['file']['name'] ) ) {
			$image_array = array();

			$file = array(
				'name'     => $_FILES['file']['name'][0],
				'type'     => $_FILES['file']['type'][0],
				'tmp_name' => $_FILES['file']['tmp_name'][0],
				'error'    => $_FILES['file']['error'][0],
				'size'     => $_FILES['file']['size'][0],
			);

			$_FILES['single_attachment'] = $file;
			$featured_image_id           = media_handle_upload( 'single_attachment', 0 );

			// wp_send_json_success( $featured_image_id );

			if ( $featured_image_id && ! is_wp_error( $featured_image_id ) ) {
				set_post_thumbnail( $review_post_id, $featured_image_id );
				$image_array[] = array( 'image' => $featured_image_id );
			}

			// Handle additional images for carousel
			if ( count( $_FILES['file']['name'] ) > 1 ) {
				for ( $i = 1; $i < count( $_FILES['file']['name'] ); $i++ ) {
					$file = array(
						'name'     => $_FILES['file']['name'][ $i ],
						'type'     => $_FILES['file']['type'][ $i ],
						'tmp_name' => $_FILES['file']['tmp_name'][ $i ],
						'error'    => $_FILES['file']['error'][ $i ],
						'size'     => $_FILES['file']['size'][ $i ],
					);

					$_FILES['single_attachment'] = $file;

					$image_id = media_handle_upload( 'single_attachment', 0 );
					if ( $image_id && ! is_wp_error( $image_id ) ) {
						$image_array[] = array( 'image' => $image_id );
					}
				}

				if ( ! empty( $image_array ) ) {
					update_field( 'image_carousel', $image_array, $review_post_id );
				}
			}
		} else {
			$file = array(
				'name'     => $_FILES['file']['name'][0],
				'type'     => $_FILES['file']['type'][0],
				'tmp_name' => $_FILES['file']['tmp_name'][0],
				'error'    => $_FILES['file']['error'][0],
				'size'     => $_FILES['file']['size'][0],
			);

			$_FILES['single_attachment'] = $file;
			$featured_image_id           = media_handle_upload( 'single_attachment', 0 );
			if ( $featured_image_id && ! is_wp_error( $featured_image_id ) ) {
				set_post_thumbnail( $review_post_id, $featured_image_id );
			}
		}
	}

	wp_send_json_success(
		array(
			'message' => 'Отзыв успешно добавлен! В ближайшее время он будет опубликован',
		),
		201
	);
}

add_action( 'wp_ajax_add_product_to_favorites', 'loveforever_add_product_to_favorites_via_ajax' );
add_action( 'wp_ajax_nopriv_add_product_to_favorites', 'loveforever_add_product_to_favorites_via_ajax' );
function loveforever_add_product_to_favorites_via_ajax() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка добавления товара в избранное! Попробуйте перезагрузить страницу',
				'debug'   => 'Неверный nonce',
			),
			400
		);
	}

	if ( empty( $_POST['product-id'] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка добавления товара в избранное! Попробуйте перезагрузить страницу',
				'debug'   => 'Id товара не передан!',
			),
			400
		);
	}

	$product_id           = (int) sanitize_text_field( wp_unslash( $_POST['product-id'] ) );
	$favorite_product_ids = array_map( 'intval', loveforever_get_favorites() );
	$message              = '';

	if ( in_array( $product_id, $favorite_product_ids, true ) ) {
		$favorite_product_ids = array_filter(
			$favorite_product_ids,
			function ( $id ) use ( $product_id ) {
				return $id !== $product_id;
			}
		);
		$message              = 'Товар успешно удален из избранного';
	} else {
		array_unshift( $favorite_product_ids, $product_id );
		$message = 'Товар успешно добавлен в избранное';
	}

	setcookie(
		'favorites',
		implode( ',', $favorite_product_ids ),
		time() + DAY_IN_SECONDS * 30,
		'/',
	);

	wp_send_json_success(
		array(
			'message'        => $message,
			'countFavorites' => count( $favorite_product_ids ),
			'favorites'      => $favorite_product_ids,
		),
		200
	);
}
// add_action( 'pre_get_posts', 'loveforever_modify_dress_category_query' );
// function loveforever_modify_dress_category_query( $query ) {
// if ( $query->is_tax( 'dress_category' ) ) {
// $query->set( 'posts_per_page', 3 );
// }

// if ( $query->is_post_type_archive( 'review' ) && ! is_admin() ) {
// $query->set( 'posts_per_page', 3 );
// }
// }

function loveforever_breadcrumbs_attribute_filter( $li_attributes, $type, $id ) {
	$pattern               = '/class="([^"]*)"/';
	$breadcrumb_item_class = 'breadcrumbs__item p-12-12 uper';

	if ( preg_match( $pattern, $li_attributes, $matches ) ) {
		$class_list   = array();
		$class_list[] = $breadcrumb_item_class;
		$class_list   = array_merge( $class_list, explode( ' ', $matches[1] ) );
		$class_list   = array_unique( $class_list );

		$new_class_string = implode( ' ', $class_list );

		return preg_replace( $pattern, 'class="' . $new_class_string . '"', $li_attributes );
	}

	return $li_attributes;
}

add_action( 'wp', 'loveforever_track_product_view' );
function loveforever_track_product_view() {
	if ( is_singular( 'dress' ) && empty( $_SERVER['HTTP_X_BARBA'] ) ) {
		$product_id = get_the_ID();
		loveforever_update_viewed_products( $product_id );
	}
}

add_action( 'wp_insert_post', 'loveforever_set_initial_product_views', 10, 2 );
function loveforever_set_initial_product_views( $post_id, $post ) {
	if ( 'dress' === $post->post_type ) {
		$views = get_post_meta( $post_id, 'product_views_count', true );

		if ( empty( $views ) ) {
			update_post_meta( $post_id, 'product_views_count', 0 );
		}
	}
}

add_filter( 'acf/format_value/type=text', 'do_shortcode' );
add_filter( 'acf/format_value/type=textarea', 'do_shortcode' );

add_filter( 'manage_dress_posts_columns', 'loveforever_dress_add_sort_column' );
function loveforever_dress_add_sort_column( $columns ) {
	// $columns['menu_order'] = 'Порядок';
	$columns['discount'] = 'Скидка';
	return $columns;
}

add_action( 'manage_dress_posts_custom_column', 'loveforever_dress_sort_column_content', 10, 2 );
function loveforever_dress_sort_column_content( $column_name, $post_id ) {
	// if ( 'menu_order' === $column_name ) {
	// echo esc_html( get_post( $post_id )->menu_order );
	// }

	if ( 'discount' === $column_name ) {
		$discount     = get_field( 'discount_percent', $post_id );
		$badge_styles = array(
			'padding'          => '6px 10px',
			'border-radius'    => '4px',
			'display'          => 'inline-flex',
			'background-color' => 'gray',
			'color'            => '#fff',
		);

		if ( ! empty( $discount ) ) {
			$badge_styles['background-color'] = 'green';
		}

		$style_attr = '';
		array_walk(
			$badge_styles,
			function ( $value, $key ) use ( &$style_attr ) {
				$style_attr .= "$key: $value;";
			}
		);

		$html = '<span style="' . $style_attr . '">';

		$html .= ! empty( $discount ) ? esc_html( $discount ) . '%' : 'Без скидки';

		$html .= '</span>';

		echo $html;
	}
}

add_action( 'bulk_edit_custom_box', 'loveforever_bulk_edit_dress_custom_box', 10, 2 );
add_action( 'quick_edit_custom_box', 'loveforever_bulk_edit_dress_custom_box', 10, 2 );
function loveforever_bulk_edit_dress_custom_box( $column_name, $post_type ) {
	if ( 'discount' !== $column_name || 'dress' !== $post_type ) {
		return;
	}
	?>
	<fieldset class="inline-edit-col-right">
			<div class="inline-edit-group wp-clearfix">
					<label>
							<span class="title">Скидка</span>
							<span class="input-text-wrap">
									<input 
										type="number" 
										name="quick_discount_percent" 
										class="quick-discount-percent" 
										min="0" 
										max="99"
									>
							</span>
					</label>
			</div>
	</fieldset>
		<?php
}

add_action( 'save_post_dress', 'loveforever_save_dress' );
function loveforever_save_dress( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_REQUEST['quick_discount_percent'] ) ) {
		$discount = intval( $_REQUEST['quick_discount_percent'] );

		if ( $discount >= 0 && $discount <= 99 ) {
			update_field( 'discount_percent', $discount, $post_id );
			update_field( 'has_discount', true, $post_id );

			$regular_price = get_field( 'price', $post_id );

			if ( ! empty( $regular_price ) ) {
				$sale_price = ceil( $regular_price - ( $regular_price * $discount / 100 ) );
				update_field( 'price_with_discount', $sale_price, $post_id );
				update_post_meta( $post_id, 'final_price', $sale_price );
			}
		}

		if ( 0 === $discount ) {
			update_field( 'has_discount', false, $post_id );
			update_field( 'price_with_discount', '', $post_id );
			update_post_meta( $post_id, 'final_price', $regular_price );
		}
	}
}

add_action( 'wp_ajax_query_products', 'loveforever_query_products_via_ajax' );
add_action( 'wp_ajax_nopriv_query_products', 'loveforever_query_products_via_ajax' );
function loveforever_query_products_via_ajax() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'loveforever_nonce' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка запроса. Попробуйте перезагрузить страницу',
				'debug'   => 'Неверный nonce',
			),
			400
		);
	}

	if ( empty( $_POST['s'] ) ) {
		wp_send_json_success(
			array(
				'message' => '',
				'html'    => '',
			),
			400
		);
	}

	$query_string = sanitize_text_field( wp_unslash( $_POST['s'] ) );
	$query_args   = array(
		'post_type'      => 'dress',
		'posts_per_page' => isset( $_POST['posts_per_page'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['posts_per_page'] ) ) ) : 6,
		's'              => $query_string,
	);

	function loveforever_smart_search_sort( $clauses, $query ) {
		global $wpdb;

		$search_term = $query->get( 's' );
		if ( $search_term ) {
			$like               = $wpdb->esc_like( $search_term ) . '%'; // начало строки
			$clauses['orderby'] = $wpdb->prepare(
				"CASE 
          WHEN {$wpdb->posts}.post_title LIKE %s THEN 0 
          ELSE 1 
          END, " . $clauses['orderby'],
				$like
			);
		}
		return $clauses;
	}

	add_filter( 'posts_clauses', 'loveforever_smart_search_sort', 20, 2 );

	/*
	// Хук для изменения сортировки */
	/*
	add_filter('posts_orderby', function($orderby) use ($query_string) { */
	/*
		global $wpdb; */
	/*
		$search_term = esc_sql($query_string); */
	/*
		return "({$wpdb->posts}.post_title LIKE '{$search_term}%') DESC, {$wpdb->posts}.post_title ASC"; */
	/* }); */

	$query = new WP_Query( $query_args );

	remove_filter( 'posts_clauses', 'loveforever_smart_search_sort' );

	ob_start();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'components/search-result-item' );
		}
	} else {
		echo wp_kses_post( "<p>По запросу <strong class=\"text-pink\">$query_string</strong> ничего не найдено</p>" );
	}

	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'message' => "Результаты поиска: $query_string",
			'html'    => $html,
		)
	);
}

add_action( 'init', 'loveforever_add_custom_fitting_rewrite_rule' );
function loveforever_add_custom_fitting_rewrite_rule() {
	add_rewrite_rule( 'fittings-admin-panel/([0-9]+)?$', 'index.php?fitting_id=$matches[1]', 'top' );
}

add_action( 'query_vars', 'loveforever_add_custom_fitting_query_var' );
function loveforever_add_custom_fitting_query_var( $query_vars ) {
	$query_vars[] = 'fitting_id';
	return $query_vars;
}

add_action( 'template_include', 'loveforever_include_admin_fittings_template' );
function loveforever_include_admin_fittings_template( $template ) {
	if ( ! empty( get_query_var( 'fitting_id' ) ) ) {
		return get_template_directory() . '/admin-fittings.php';
	}

	return $template;
}

add_filter( 'post_password_expires', 'loveforever_change_post_password_expires_time' );
function loveforever_change_post_password_expires_time() {
	return time() + DAY_IN_SECONDS;
}

add_filter( 'the_password_form', 'loveforever_change_password_form', 10, 2 );
function loveforever_change_password_form( $form_html, $post ) {
	$post      = get_post( $post );
	$label     = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
	$form_html = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
		 <p>' . __( 'This content is password protected. To view it please enter your password below:' ) . '</p>
		 <p class="field">
			 <label for="' . $label . '" class="field__label">' . __( 'Password:' ) . ' </label>
			 <input 
				 name="post_password" 
				 id="' . $label . '" 
				 type="password" 
				 class="field__control" 
				 spellcheck="false" 
				 size="20" 
			 />
		 </p>
		 <input type="submit" class="button" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form' ) . '" />
	 </form>
	 ';

	return $form_html;
}

add_action( 'acf/save_post', 'loveforever_update_dress_final_price', 20 );
function loveforever_update_dress_final_price( $post_id ) {
	if ( 'dress' !== get_post_type( $post_id ) ) {
		return;
	}

	$regular_price = get_field( 'price', $post_id );
	$sale_price    = get_field( 'price_with_discount', $post_id );

	$final_price = $sale_price ? $sale_price : $regular_price;

	update_post_meta( $post_id, 'final_price', $final_price );
}

function loveforever_update_all_dresses_final_price() {
	$dresses = get_posts(
		array(
			'post_type'      => 'dress',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		)
	);

	foreach ( $dresses as $dress ) {
			$regular_price = get_field( 'price', $dress->ID );
			$sale_price    = get_field( 'price_with_discount', $dress->ID );
			$final_price   = $sale_price ? $sale_price : $regular_price;

			update_post_meta( $dress->ID, 'final_price', $final_price );
	}
}

add_action( 'save_post', 'loveforever_set_default_views_count', 10, 3 );
/**
 * Добавляет кастомное поле product_views_count со значением 0 при создании/обновлении поста,
 * если такого поля еще нет.
 *
 * @param int    $post_id ID поста.
 * @param object $post    Объект поста.
 * @param bool   $update  True если это обновление, false если новый пост.
 */
function loveforever_set_default_views_count( $post_id, $post, $update ) {
	// Пропускаем автосохранение
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
	}

	// Пропускаем ревизии
	if ( wp_is_post_revision( $post_id ) ) {
			return;
	}

	// Проверяем тип поста (можно ограничить только нужными типами)
	if ( 'dress' !== $post->post_type ) {
			return;
	}

	// Проверяем, существует ли уже поле product_views_count
	$views_count = get_post_meta( $post_id, 'product_views_count', true );

	// Если поле не существует (пустое), добавляем со значением 0
	if ( '' === $views_count ) {
			update_post_meta( $post_id, 'product_views_count', 0 );
	}
}

function dress_admin_scripts() {
	global $post;

	// Проверяем, находимся ли мы на странице редактирования платья
	if ( isset( $post ) && $post->post_type === 'dress' ) {
		// Регистрируем и подключаем наш скрипт
		wp_enqueue_script( 'dress-acf-filter', get_template_directory_uri() . '/js/dress-acf-filter.js', array( 'jquery', 'acf-input' ), '1.0', true );

		$map = array(
			311 => 'wedding',
			199 => 'evening',
			282 => 'prom',
			375 => 'boudoir',
		);

		$dependencies = array();

		$dress_categories = get_terms(
			array(
				'taxonomy'   => 'dress_category',
				'parent'     => 0,
				'hide_empty' => false,
			)
		);

		foreach ( $dress_categories as $dress_category ) {
			$dependencies[ $dress_category->term_id ]['silhouette'] = get_field( 'silhouette', $dress_category );
			$dependencies[ $dress_category->term_id ]['brand']      = get_field( 'brand', $dress_category );
			$dependencies[ $dress_category->term_id ]['style']      = get_field( 'style', $dress_category );
			$dependencies[ $dress_category->term_id ]['color']      = get_field( 'color', $dress_category );
			$dependencies[ $dress_category->term_id ]['fabric']     = get_field( 'fabric', $dress_category );
		}

			// // Создаем массив зависимостей для передачи в JavaScript
			// $dependencies = array(
			// 'wedding' => array( // ID категории "Свадебные платья"
			// 'silhouette' => array( 9, 17, 11, 15 ), // ID допустимых силуэтов
			// 'brand'      => array( 'brand1', 'brand2', 'brand3' ), // ID допустимых брендов
			// 'style'      => array( 'classic', 'modern', 'vintage' ), // ID допустимых стилей
			// 'color'      => array( 462, 469, 445, 470 ), // ID допустимых цветов
			// ),
			// 'evening' => array( // ID категории "Вечерние платья"
			// 'silhouette' => array( 'sheath', 'trumpet', 'ball-gown' ),
			// 'brand'      => array( 'brand2', 'brand4', 'brand5' ),
			// 'style'      => array( 'glamour', 'minimalist', 'luxury' ),
			// 'color'      => array( 'black', 'red', 'blue', 'gold', 'silver' ),
			// ),
			// Добавьте другие категории по аналогии
			// );

		// Передаем данные в JavaScript
		wp_localize_script(
			'dress-acf-filter',
			'dressData',
			array(
				'map'          => $map,
				'dependencies' => $dependencies,
				'fieldKeys'    => array(
					'category'   => 'dress_category', // Замените на ключ вашего поля категории
					'silhouette' => 'silhouette', // Замените на ключ вашего поля силуэта
					'brand'      => 'brand', // Замените на ключ вашего поля бренда
					'style'      => 'style', // Замените на ключ вашего поля стиля
					'color'      => 'color', // Замените на ключ вашего поля цвета
					'fabric'     => 'fabric', // Замените на ключ вашего поля цвета
				),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'dress_admin_scripts' );

add_filter(
	'acf/fields/taxonomy/query/key=field_67d8023f498e9',
	'loveforever_filter_dress_taxonomy_filters',
	10,
	3
);
add_filter(
	'acf/fields/taxonomy/query/key=field_67d80188498e3',
	'loveforever_filter_dress_taxonomy_filters',
	10,
	3
);
add_filter(
	'acf/fields/taxonomy/query/key=field_67d801c6498e4',
	'loveforever_filter_dress_taxonomy_filters',
	10,
	3
);
add_filter(
	'acf/fields/taxonomy/query/key=field_67d801dc498e5',
	'loveforever_filter_dress_taxonomy_filters',
	10,
	3
);
add_filter(
	'acf/fields/taxonomy/query/key=field_67d801f8498e7',
	'loveforever_filter_dress_taxonomy_filters',
	10,
	3
);
function loveforever_filter_dress_taxonomy_filters( $args, $field, $post_id ) {
	error_log( '$ARGS_BEFORE: ' . wp_json_encode( $args ) );

	if ( ! empty( $_POST['dress_id'] ) ) {
		$include = array();

		foreach ( $_POST['dress_id'] as $dress_id ) {
			$cat                = get_term( $dress_id, 'dress_category' );
			$allowed_taxonomies = ! empty( get_field( $args['taxonomy'], $cat ) ) ? get_field( $args['taxonomy'], $cat ) : array();

			error_log( 'ALLOWED_TAX: ' . wp_json_encode( $allowed_taxonomies ) . ' | DRESS_ID: ' . $dress_id );
			$include = array_merge( $include, $allowed_taxonomies );
		}

		$args['include'] = array_unique( $include );
	}

	error_log( '$ARGS: ' . wp_json_encode( $args ) );
	error_log( '$_REQUEST: ' . wp_json_encode( $_REQUEST ) );
	error_log( '$_POST: ' . wp_json_encode( $_POST ) );

	return $args;
}

add_filter( 'acf/fields/taxonomy/query/key=field_67fbc6524cab9', 'loveforever_filter_base_dress_category_field' );
add_filter( 'acf/fields/taxonomy/query/key=field_67d6fec761d73', 'loveforever_filter_base_dress_category_field' );
add_filter( 'acf/fields/taxonomy/query/key=field_price_base_category', 'loveforever_filter_base_dress_category_field' );
function loveforever_filter_base_dress_category_field( $args ) {
	$args['parent'] = 0;
	return $args;
}

add_action(
	'acf/save_post',
	function ( $post_id ) {
		if ( get_post_type( $post_id ) !== 'auto_rule' ) {
			return;
		}

		// Получаем поля
		$base    = get_field( 'base_dress_category', $post_id );
		$filters = get_field( 'filters', $post_id );
		$target  = get_field( 'result_dress_category', $post_id );

		if ( ! $base || ! $target ) {
			return;
		}

		$filter_parts = array();

		foreach ( $filters as $tax => $terms ) {
			if ( ! empty( $terms ) ) {
				$names = array();
				foreach ( $terms as $term_id ) {
						$term = get_term_by( 'term_id', $term_id, $tax );
					if ( $term ) {
						$names[] = $term->name;
					}
				}

				if ( ! empty( $names ) ) {
						$filter_parts[] = implode( ', ', $names );
				}
			}
		}

		// Добавляем бейдж в заголовок, если он установлен
		if ( ! empty( $filters['badge'] ) ) {
			$badge_labels = array(
				'new'     => 'Новинка',
				'popular' => 'Популярное',
				'sale'    => 'Распродажа',
			);

			$badge_label = isset( $badge_labels[ $filters['badge'] ] )
				? $badge_labels[ $filters['badge'] ]
				: $filters['badge'];

			$filter_parts[] = $badge_label;
		}

		$base_term   = get_term_by( 'term_id', $base, 'dress_category' );
		$target_term = get_term_by( 'term_id', $target, 'dress_category' );

		if ( ! $base_term || ! $target_term ) {
			return;
		}

		// Формируем заголовок
		$title = $base_term->name;
		if ( ! empty( $filter_parts ) ) {
			$title .= ' + ' . implode( ', ', $filter_parts );
		}
		$title .= ' → ' . $target_term->name;

		// Обновляем заголовок
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => $title,
			)
		);
	},
	20
); // Поздний приоритет, чтобы ACF успел сохранить

add_action(
	'acf/save_post',
	function ( $post_id ) {
		if ( get_post_type( $post_id ) !== 'dress' ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		loveforever_apply_auto_rules_to_post( $post_id );
		loveforever_apply_price_rules_to_post( $post_id );
	},
	20
);


function loveforever_apply_auto_rules_to_post( $post_id ) {
	$dress_categories = get_field( 'dress_category', $post_id );

	if ( empty( $dress_categories ) ) {
		return;
	}

	$dress_categories_objects  = array_map( 'get_term', $dress_categories );
	$parent_dress_categories   = array_filter( $dress_categories_objects, fn( $cat ) => 0 === $cat->parent );
	$parent_dress_category_ids = array_map( fn( $cat ) => $cat->term_id, $parent_dress_categories );

	if ( empty( $parent_dress_category_ids ) ) {
		return;
	}

	$filters = array(
		'brand'      => get_field( 'brand', $post_id ),
		'style'      => get_field( 'style', $post_id ),
		'silhouette' => get_field( 'silhouette', $post_id ),
		'color'      => get_field( 'color', $post_id ),
		'fabric'     => get_field( 'fabric', $post_id ),
		'badge'      => get_field( 'badge', $post_id ),
	);

	if ( empty( array_filter( array_values( $filters ) ) ) ) {
		return;
	}

	$rules = get_posts(
		array(
			'post_type'   => 'auto_rule',
			'numberposts' => -1,
			'post_status' => 'publish',
		)
	);

	$matched_terms = array();

	foreach ( $rules as $rule ) {
		$base_category_id   = get_field( 'base_dress_category', $rule->ID );
		$result_category_id = get_field( 'result_dress_category', $rule->ID );
		$rule_filters       = get_field( 'filters', $rule->ID );

		if ( ! $base_category_id || ! $result_category_id ) {
			continue;
		}

		if ( ! in_array( $base_category_id, $parent_dress_category_ids, true ) ) {
			continue;
		}

		$matched = false;

		foreach ( $rule_filters as $taxonomy => $rule_terms ) {
			if ( empty( $rule_terms ) || empty( $filters[ $taxonomy ] ) ) {
					continue;
			}

			// Специальная обработка для поля badge (не таксономия)
			if ( 'badge' === $taxonomy ) {
				if ( $filters[ $taxonomy ] === $rule_terms ) {
					$matched = true;
				} else {
					$matched = false;
					break;
				}
			} else {
				// Обычная обработка для таксономий
				$common = array_intersect( $filters[ $taxonomy ], $rule_terms );

				if ( empty( $common ) ) {
						$matched = false;
						break;
				}

				$matched = true;
			}
		}

		if ( $matched ) {
				$matched_terms[] = $result_category_id;
		}
	}

	// 4. Сохраняем термы в ACF-поле таксономии
	if ( ! empty( $matched_terms ) ) {
		update_field( 'dress_category', array_unique( array_merge( $dress_categories, $matched_terms ) ), $post_id );
		wp_set_post_terms( $post_id, array_unique( array_merge( $dress_categories, $matched_terms ) ), 'dress_category' );
	}
}

add_filter( 'big_image_size_threshold', '__return_false' );

add_action( 'acf/save_post', 'loveforever_appy_dress_to_sale_categories', 22 );
function loveforever_appy_dress_to_sale_categories( $product_id ) {
	if ( get_post_type( $product_id ) !== 'dress' ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$sale_category_id = 379;

	$has_discount = get_field( 'has_discount', $product_id );

	if ( ! $has_discount ) {
		return;
	}

	$dress_categories = wp_get_post_terms( $product_id, 'dress_category', array( 'fields' => 'all' ) );

	$dress_category_ids = array_map( fn( $dress_cat ) => $dress_cat->term_id, $dress_categories );

	$dress_category_ids[] = $sale_category_id; // sale category id!

	$dress_category_ids = array_unique( $dress_category_ids );

	update_field( 'dress_category', $dress_category_ids, $product_id );
	wp_set_post_terms( $product_id, $dress_category_ids, 'dress_category' );

	$base_categories = array();

	$category_slugs = array(
		'wedding',
		'evening',
		'prom',
	);

	foreach ( $dress_categories as $dress_cat ) {
		if ( 0 === $dress_cat->parent ) {
			$base_categories[] = $dress_cat;
		}
	}

	$extra_cagegories_to_append = array();

	foreach ( $base_categories as $base_cat ) {
		if ( in_array( $base_cat->slug, $category_slugs, true ) ) {
			$child_sale_cat = get_term_by( 'slug', $base_cat->slug . '-sale', 'dress_category' );

			if ( ! ( $child_sale_cat instanceof WP_Term ) ) {
				continue;
			}

			$extra_cagegories_to_append[] = $child_sale_cat->term_id;
		}
	}

	update_field( 'dress_category', array_unique( array_merge( $dress_category_ids, $extra_cagegories_to_append ) ), $product_id );
	wp_set_post_terms( $product_id, array_unique( array_merge( $dress_category_ids, $extra_cagegories_to_append ) ), 'dress_category' );
}

add_action( 'admin_footer-edit.php', 'custom_admin_bar_js_for_dress' );
function custom_admin_bar_js_for_dress() {
	global $typenow;

	$allowed_post_types = array(
		'dress',
		'promo_blocks',
		'story',
	);

	if ( ! in_array( $typenow, $allowed_post_types, true ) ) {
		return;
	}

	$main_categories = get_terms(
		array(
			'taxonomy'   => 'dress_category',
			'parent'     => 0,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'hide_empty' => false,
		)
	);

	$html = '<div style="padding-block: 20px; display: flex; flex-wrap: wrap; gap: 10px;">';

	foreach ( $main_categories as $main_category ) {
		$html .= wp_sprintf(
			'<a href="%s" class="button button-small button-primary">%s</a>',
			admin_url( 'edit.php?post_type=' . $typenow . '&dress_category=' . $main_category->slug ),
			$main_category->name
		);
	}

	$html .= '</div>';
	?>
	<script>
	jQuery(function($) {
		var $form = $('.wp-header-end'); // верхняя часть таблицы
		if ($form.length) {
			$form.after('<?php echo $html; ?>');
		}
	});
	</script>
	<?php
}

add_filter( 'wp_editor_set_quality', 'loveforever_set_image_quality' );
function loveforever_set_image_quality( $quality ) {
	return 100;
}





// Добавляем метабокс для выбора шаблона
// function add_promo_template_metabox() {
// add_meta_box(
// 'promo_template_selector',
// 'Выбор шаблона',
// 'render_promo_template_metabox',
// 'promo_blocks',
// 'side'
// );
// }
// add_action('add_meta_boxes', 'add_promo_template_metabox');
//
// Варианты шаблонов с путями к изображениям
function get_promo_templates() {
	$base_url = get_template_directory_uri();

	return array(
		'style1'  => array(
			'name'  => 'Вариант 1',
			'image' => $base_url . '/images/style1_preview.jpg',
		),
		'style2'  => array(
			'name'  => 'Вариант 2',
			'image' => $base_url . '/images/style2_preview.jpg',
		),
		'style3'  => array(
			'name'  => 'Вариант 3',
			'image' => $base_url . '/images/style3_preview.jpg',
		),
		'style4'  => array(
			'name'  => 'Вариант 4',
			'image' => $base_url . '/images/style4_preview.jpg',
		),
		'style5'  => array(
			'name'  => 'Вариант 5',
			'image' => $base_url . '/images/style5_preview.jpg',
		),
		'style6'  => array(
			'name'  => 'Вариант 6',
			'image' => $base_url . '/images/style6_preview.jpg',
		),
		'style7'  => array(
			'name'  => 'Вариант 7',
			'image' => $base_url . '/images/style7_preview.jpg',
		),
		'style8'  => array(
			'name'  => 'Вариант 8',
			'image' => $base_url . '/images/style8_preview.jpg',
		),
		'style9'  => array(
			'name'  => 'Вариант 9',
			'image' => $base_url . '/images/style9_preview.jpg',
		),
		'style10' => array(
			'name'  => 'Вариант 10',
			'image' => $base_url . '/images/style10_preview.jpg',
		),
		'style11' => array(
			'name'  => 'Вариант 11',
			'image' => $base_url . '/images/style11_preview.jpg',
		),
		'style12' => array(
			'name'  => 'Вариант 12',
			'image' => $base_url . '/images/style12_preview.jpg',
		),
	);
}
//
// Отображение метабокса
// function render_promo_template_metabox($post) {
// $current_template = get_post_meta($post->ID, '_promo_template', true);
// $templates = get_promo_templates();
//
// wp_nonce_field('save_promo_template', 'promo_template_nonce');
//
// echo '<select name="promo_template" id="promo_template_select" style="width:100%">';
// echo '<option value="">— Выберите шаблон —</option>';
//
// foreach ($templates as $value => $data) {
// printf(
// '<option value="%s"%s>%s</option>',
// esc_attr($value),
// selected($value, $current_template, false),
// esc_html($data['name'])
// );
// }
// echo '</select>';
//
// Контейнер для превью
// echo '<div id="promo_template_preview" style="margin-top:15px">';
// if ($current_template && isset($templates[$current_template])) {
// echo '<strong>Превью:</strong><br>';
// echo '<img src="' . esc_url($templates[$current_template]['image']) . '" style="max-width:100%; margin-top:10px">';
// }
// echo '</div>';
//
// Скрипт для динамического обновления превью
// }
//
// Сохранение данных
// function save_promo_template_meta($post_id) {
// if (!isset($_POST['promo_template_nonce']) ||
// !wp_verify_nonce($_POST['promo_template_nonce'], 'save_promo_template') ||
// defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||
// !current_user_can('edit_post', $post_id) ||
// $_POST['post_type'] !== 'promo_blocks') {
// return;
// }
//
// if (isset($_POST['promo_template'])) {
// update_post_meta(
// $post_id,
// '_promo_template',
// sanitize_text_field($_POST['promo_template'])
// );
// }
// }
// add_action('save_post', 'save_promo_template_meta');
//
// Добавляем колонку в админку
// function add_promo_template_column($columns) {
// $new_columns = array();
// foreach ($columns as $key => $title) {
// $new_columns[$key] = $title;
// if ($key === 'title') {
// $new_columns['promo_template'] = 'Шаблон';
// }
// }
// return $new_columns;
// }
// add_filter('manage_promo_blocks_posts_columns', 'add_promo_template_column');
//
// function display_promo_template_column($column, $post_id) {
// if ($column === 'promo_template') {
// $template = get_post_meta($post_id, '_promo_template', true);
// $templates = get_promo_templates();
// echo ($template && isset($templates[$template])) ? $templates[$template]['name'] : '—';
// }
// }
// add_action('manage_promo_blocks_posts_custom_column', 'display_promo_template_column', 10, 2);

// add_filter('acf/fields/taxonomy/query', 'filter_specific_taxonomy_field', 10, 3);
function filter_specific_taxonomy_field( $args, $field, $post_id ) {
	if ( $field['name'] === 'tax' ) {
		$args['parent']     = 0; // Только корневые термины
		$args['hide_empty'] = false; // Показывать даже пустые
	}
	return $args;
}

add_action( 'acf/render_field/name=promo_template', 'acf_add_promo_preview' );
function acf_add_promo_preview( $field ) {
	$templates = get_promo_templates();

	?>
	<script>
		(function($) {
			const templates = <?php echo json_encode( $templates ); ?>;

			// Ждём полной инициализации ACF
			acf.addAction('ready', function($el) {
				const $select = $('select[name="acf[field_685e5a5413232]"]');

				// Создаём превью-блок, если он ещё не существует
				if ($('#acf-promo-template-preview').length === 0) {
					$select.closest('.acf-field').after('<div id="acf-promo-template-preview" style="padding: 16px; padding-top: 10px"></div>');
				}

				// Функция обновления превью
				function updatePreview(value) {
					if (templates[value]) {
						$('#acf-promo-template-preview').html(
							'<strong>Превью:</strong><br>' +
							'<img src="' + templates[value].image + '" style="width:343px; margin-top:10px;">'
						);
					} else {
						$('#acf-promo-template-preview').html('');
					}
				}

				// Первичная инициализация
				updatePreview($select.val());

				// Обработка изменения
				$select.on('change', function() {
					updatePreview($(this).val());
				});
			});
		})(jQuery);
	</script>

	<?php
}

add_filter(
	'get_terms',
	function ( $terms, $taxonomies, $args ) {
		if ( ! is_admin() ) {
			return $terms;
		}

		if ( ! in_array( 'dress_category', (array) $taxonomies ) ) {
			return $terms;
		}

		global $pagenow, $post_type;

		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			return $terms;
		}

		if ( 'promo_blocks' !== $post_type && 'story' !== $post_type ) {
			return $terms;
		}

		if ( 'all' !== $args['fields'] ) {
			return $terms;
		}

		return array_filter(
			$terms,
			function ( $term ) {
				return 0 === $term->parent;
			}
		);
	},
	10,
	3
);

add_action( 'wp_ajax_loveforever_request_callback', 'loveforever_request_callback' );
add_action( 'wp_ajax_nopriv_loveforever_request_callback', 'loveforever_request_callback' );
function loveforever_request_callback() {
	if ( ! isset( $_POST['submit_callback_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['submit_callback_form_nonce'] ) ), 'submit_callback_form' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка в запросе',
				'debug'   => 'Невалидный nonce',
			),
			400
		);
	}

	$errors = array();

	if ( empty( $_POST['phone'] ) ) {
		$errors[] = 'Пожалуйста, заполните номер телефона';
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка в запросе',
				'debug'   => 'Некорректные данные',
				'errors'  => $errors,
			),
			400
		);
	}

	$name  = ! empty( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : 'Неизвестно';
	$phone = sanitize_text_field( wp_unslash( $_POST['phone'] ) );

	$email   = loveforever_get_application_email();
	$subject = 'Новая заявка с сайта ' . get_bloginfo( 'name' );

	$message = '
		<p>Имя: ' . $name . '</p>
		<p>Телефон: ' . $phone . '</p>
	';

	$sent = wp_mail( $email, $subject, $message, array( 'Content-Type: text/html; charset=UTF-8' ) );

	if ( ! $sent ) {
		wp_send_json_error(
			array(
				'message' => 'Ошибка в запросе',
				'debug'   => 'Не удалось отправить письмо',
				'errors'  => array(
					'Не удалось отправить письмо. Попробуйте чуть позже!',
				),
			)
		);
	}

	wp_send_json_success(
		array(
			'message' => 'Заявка успешно отправлена',
		)
	);
}

function simple_search_priority( $orderby, $wp_query ) {
	global $wpdb;

	if ( ! is_search() || empty( $wp_query->query_vars['s'] ) ) {
		return $orderby;
	}

	$search_term = esc_sql( $wp_query->query_vars['s'] );

	return "
        ({$wpdb->posts}.post_title LIKE '{$search_term}%') DESC,
        {$wpdb->posts}.post_title ASC
    ";
}

add_filter( 'posts_orderby', 'simple_search_priority', 10, 2 );

/**
 * Применяет ценовые правила к платью
 *
 * @param int $post_id ID платья
 */
function loveforever_apply_price_rules_to_post( $post_id ) {
	// Получаем финальную цену платья
	$final_price = get_field( 'final_price', $post_id );

	if ( empty( $final_price ) || ! is_numeric( $final_price ) ) {
		return;
	}

	// Получаем текущие категории платья
	$dress_categories = get_field( 'dress_category', $post_id );
	$dress_categories = is_array( $dress_categories ) ? $dress_categories : array();

	if ( empty( $dress_categories ) ) {
		return;
	}

	// Получаем родительские категории платьев (корневые)
	$dress_categories_objects  = array_map( 'get_term', $dress_categories );
	$parent_dress_categories   = array_filter( $dress_categories_objects, fn( $cat ) => 0 === $cat->parent );
	$parent_dress_category_ids = array_map( fn( $cat ) => $cat->term_id, $parent_dress_categories );

	if ( empty( $parent_dress_category_ids ) ) {
		return;
	}

	// Получаем все активные ценовые правила
	$price_rules = get_posts(
		array(
			'post_type'   => 'price_rule',
			'numberposts' => -1,
			'post_status' => 'publish',
		)
	);

	$matched_categories = array();

	foreach ( $price_rules as $rule ) {
		$min_price       = get_field( 'min_price', $rule->ID );
		$max_price       = get_field( 'max_price', $rule->ID );
		$base_category   = get_field( 'base_dress_category', $rule->ID ); // Базовая категория
		$target_category = get_field( 'target_category', $rule->ID );

		if ( empty( $target_category ) ) {
			continue;
		}

		// Проверяем, что платье принадлежит к базовой категории правила
		if ( ! empty( $base_category ) && ! in_array( $base_category, $parent_dress_category_ids, true ) ) {
			continue;
		}

		// Проверяем соответствие цены правилу
		$matches = false;

		if ( ! empty( $min_price ) && ! empty( $max_price ) ) {
			// Диапазон цен
			$matches = ( $final_price >= $min_price && $final_price <= $max_price );
		} elseif ( ! empty( $min_price ) ) {
			// Минимальная цена
			$matches = ( $final_price >= $min_price );
		} elseif ( ! empty( $max_price ) ) {
			// Максимальная цена
			$matches = ( $final_price <= $max_price );
		}

		if ( $matches ) {
			$matched_categories[] = $target_category;
		}
	}

	// Применяем найденные категории
	if ( ! empty( $matched_categories ) ) {
		$new_categories = array_unique( array_merge( $dress_categories, $matched_categories ) );

		update_field( 'dress_category', $new_categories, $post_id );
		wp_set_post_terms( $post_id, $new_categories, 'dress_category' );
	}
}

/**
 * Автоматически обновляет заголовок ценового правила
 */
add_action(
	'acf/save_post',
	function ( $post_id ) {
		if ( get_post_type( $post_id ) !== 'price_rule' ) {
			return;
		}

		$base_category   = get_field( 'base_dress_category', $post_id );
		$min_price       = get_field( 'min_price', $post_id );
		$max_price       = get_field( 'max_price', $post_id );
		$target_category = get_field( 'target_category', $post_id );

		if ( empty( $target_category ) ) {
			return;
		}

		$target_term = get_term_by( 'term_id', $target_category, 'dress_category' );
		if ( ! $target_term ) {
			return;
		}

		// Формируем заголовок правила
		$title_parts = array();

		// Добавляем базовую категорию, если она указана
		if ( ! empty( $base_category ) ) {
			$base_term = get_term_by( 'term_id', $base_category, 'dress_category' );
			if ( $base_term ) {
				$title_parts[] = $base_term->name;
			}
		}

		// Добавляем ценовой диапазон
		if ( ! empty( $min_price ) && ! empty( $max_price ) ) {
			$title_parts[] = number_format( $min_price, 0, '.', ' ' ) . ' - ' . number_format( $max_price, 0, '.', ' ' ) . ' ₽';
		} elseif ( ! empty( $min_price ) ) {
			$title_parts[] = 'от ' . number_format( $min_price, 0, '.', ' ' ) . ' ₽';
		} elseif ( ! empty( $max_price ) ) {
			$title_parts[] = 'до ' . number_format( $max_price, 0, '.', ' ' ) . ' ₽';
		}

		$title_parts[] = '→ ' . $target_term->name;

		$title = implode( ' ', $title_parts );

		// Обновляем заголовок
		wp_update_post(
			array(
				'ID'         => $post_id,
				'post_title' => $title,
			)
		);
	},
	20
);
