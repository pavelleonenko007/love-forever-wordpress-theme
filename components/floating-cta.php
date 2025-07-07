<?php
/**
 * Floating CTA Component
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$telegram_url = get_field( 'telegram_url', 'option' );
$whatsapp_url = get_field( 'whatsapp_url', 'option' );

$class_names = array( 'floating-cta' );

if ( is_singular( 'dress' ) ) {
	$class_names[] = 'visible-mobile';
}

?>
<div class="<?php echo esc_attr( implode( ' ', $class_names ) ); ?>">
	<?php if ( ! empty( $telegram_url ) ) : ?>
	<a 
		href="<?php echo esc_url( $telegram_url ); ?>" 
		class="button button--square button--tg"
		target="_blank"
		rel="noopener noreferrer"
		aria-label="Написать в Telegram (открывается в новой вкладке)"
	>
		<svg 
			class="button__icon" 
			viewBox="0 0 16 16" 
			xmlns="http://www.w3.org/2000/svg"
		>
			<use href="#telegramIcon"/>
		</svg>
	</a>
	<?php endif; ?>
	<?php if ( ! empty( $whatsapp_url ) ) : ?>
	<a 
		href="<?php echo esc_url( $whatsapp_url ); ?>" 
		class="button button--square button--whatsapp"
		target="_blank"
		rel="noopener noreferrer"
		aria-label="Написать в WhatsApp (открывается в новой вкладке)"
	>
		<svg 
			class="button__icon" 
			viewBox="0 0 16 16" 
			xmlns="http://www.w3.org/2000/svg"
		>
			<use href="#whatsappIcon"/>
		</svg>
	</a>
	<?php endif; ?>
	<button 
		type="button" 
		class="button"
		data-js-dialog-open-button="globalFittingDialog"
	>Записаться на примерку</button>
</div>
