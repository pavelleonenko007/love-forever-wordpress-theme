<?php
/**
 * WP-CLI reupdate posts
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'posts reupdate',
		function () {
			$args = array(
				'post_type'      => 'dress',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			);

			$posts = get_posts( $args );
			$count = 0;

			// Лог-файл в папке uploads (wp-content/uploads/reupdate-posts.log)
			$upload_dir = wp_upload_dir();
			$log_path   = trailingslashit( $upload_dir['basedir'] ) . 'reupdate-posts.log';
			$log_file   = fopen( $log_path, 'a' );

			if ( ! $log_file ) {
				WP_CLI::error( "Не удалось открыть лог-файл: $log_path" );
				return;
			}

			fwrite( $log_file, '=== Запуск обновления постов: ' . date( 'Y-m-d H:i:s' ) . " ===\n" );

			foreach ( $posts as $p ) {
				loveforever_apply_auto_rules_to_post( $p->ID );
				loveforever_appy_dress_to_sale_categories( $p->ID );

				wp_cache_delete( $p->ID, 'posts' );
				wp_cache_delete( $p->ID, 'post_meta' );
				clean_object_term_cache( $p->ID, $p->post_type );

				acf_reset_meta( $p->ID );

				$dress_categories = wp_get_object_terms( $p->ID, 'dress_category', array( 'fields' => 'all' ) );

				foreach ( $dress_categories as $dress_cat ) {
					$key   = 'dress_order_' . $dress_cat->term_id;
					$order = get_field( $key, $p->ID );

					fwrite( $log_file, 'Поле dress_order_' . $dress_cat->term_id . ' (' . $dress_cat->name . ') имеет значение ' . $order . ' для поста ' . $p->post_title . "\n" );

					if ( empty( $order ) ) {
						fwrite( $log_file, 'dress_order_' . $dress_cat->term_id . " устанавливаем значение 0\n" );
						update_field( $key, 0, $p->ID );
					}
				}

				// Логируем
				$log_entry = sprintf(
					"[%s] Обновлен пост #%d: \"%s\"\n",
					current_time( 'Y-m-d H:i:s' ),
					$p->ID,
					$p->post_title
				);
				fwrite( $log_file, $log_entry );

				$count++;
			}

			fwrite( $log_file, "=== Обновление завершено: $count постов обработано ===\n\n" );
			fclose( $log_file );

			WP_CLI::success( "Обновлено $count постов. Лог: $log_path" );
		}
	);
}
