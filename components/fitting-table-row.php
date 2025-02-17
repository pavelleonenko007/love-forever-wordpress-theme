<?php
/**
 * Fitting Table Row Component
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;

$name                    = get_field( 'name' );
$number                  = get_field( 'phone' );
$fitting_comment         = get_field( 'comment' );
$ip_address              = get_field( 'ip_address' );
$fitting_type            = get_field( 'fitting_type' );
$fitting_step            = get_field( 'fitting_step' );
$fitting_time            = get_field( 'fitting_time' );
$client_favorite_dresses = get_field( 'client_favorite_dresses' );
$fitting_types           = array(
	'wedding' => 'Свадебные платья',
	'evening' => 'Вечерние платья',
	'prom'    => 'Выпускные платья',
);
$fitting_steps           = array(
	'delivery'   => '#F2DEDF',
	'fitting'    => '#FCF9E0',
	're-fitting' => '#E1EFDA',
);
?>
<tr class="fittings-table__row" style="background-color: <?php echo esc_attr( $fitting_steps[ $fitting_step ] ?? '#fff' ); ?>">
	<td>
		<div class="fittings-table__cell">
			<p><strong><?php echo esc_html( date_i18n( 'H:i', strtotime( $fitting_time ) ) ); ?></strong> <?php echo esc_html( date_i18n( 'd M Y', strtotime( $fitting_time ) ) ); ?></p>
			<p class="dress-types">
				<?php foreach ( $fitting_type as $fitting_type_value ) : ?>
					<?php if ( isset( $fitting_types[ $fitting_type_value ] ) ) : ?>
						<span class="dress-type-tag"><?php echo esc_html( $fitting_types[ $fitting_type_value ] ); ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</p>
		</div>
	</td>
	<td>
		<div class="fittings-table__cell">
			<?php if ( ! empty( $name ) ) : ?>
				<p><?php echo esc_html( $name ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $phone ) ) : ?>
				<p><a href="<?php echo esc_url( loveforever_format_phone_to_link( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
			<?php endif; ?>
		</div>
	</td>
	<td>
		<div class="fittings-table__cell">
			<?php if ( ! empty( $client_favorite_dresses ) ) : ?>
				<p><a href="<?php echo esc_url( home_url( '/' ) . 'favorites?favorites=' . implode( ',', $client_favorite_dresses ) ); ?>">Избранное пользователя</a></p>
			<?php endif; ?>
			<?php if ( ! empty( $ip_address ) ) : ?>
				<p>IP Адрес: <?php echo esc_html( $ip_address ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $fitting_comment ) ) : ?>
				<p><?php echo esc_html( $fitting_comment ); ?></p>
			<?php endif; ?>
		</div>
	</td>
	<td class="fittings-table__row-action">
		<a href="<?php echo esc_url( home_url( '/' ) . 'fittings-admin-panel/' . get_the_ID() ); ?>" class="fittings-table__row-action-button">Изменить</a>
		<button
			type="button"
			class="fittings-table__row-action-button fittings-table__row-action-button--delete"
			data-js-delete-fitting-button="<?php echo esc_attr( get_the_ID() ); ?>"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_fitting_' . get_the_ID() ) ); ?>"
		>
			Удалить
		</button>
	</td>
</tr>
