<?php
/**
 * Category Card
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$card         = $args['card'];
$size         = $args['size'] ?? 'normal';
$side         = $args['side'] ?? 'top';
$card_classes = array( 'bigcard-item' );

if ( 'big' === $size ) {
	$card_classes[] = '_2cols';
}

$card_id = '';

switch ( $side ) {
	case 'left':
		$card_id = 'w-node-cd7e1829-1ada-5809-2a7b-c6853ce2af05-7ea1ac8d';
		break;
	case 'right':
		$card_id = 'w-node-_33750394-2a67-a842-08f3-f11e4775ede9-7ea1ac8d';
		break;
	default:
		$card_id = 'w-node-e4234285-7aeb-3f92-ffde-315fc2aa29dc-7ea1ac8d';
		break;
}

if ( ! empty( $card['image'] ) && ! empty( $card['heading'] ) ) : ?>
	<div id="<?php echo esc_attr( $card_id ); ?>" class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>">
		<div class="slider_home-slider_slide-in">
			<div class="mom-abs">
				<?php if ( ! empty( $card['image'] ) ) : ?>
					<img src="<?php echo esc_url( $card['image']['url'] ); ?>" loading="eager" alt class="img-cover">
				<?php endif; ?>
			</div>
			<div class="slider-bottom-content _w-auto">
				<?php if ( ! empty( $card['subheading'] ) ) : ?>
					<div class="p-12-12 uper"><?php echo esc_html( $card['subheading'] ); ?></div>
				<?php endif; ?>
				<?php if ( ! empty( $card['heading'] ) ) : ?>
					<p class="p-64-64"><?php echo esc_html( $card['heading'] ); ?></p>
				<?php endif; ?>
				<?php
				$bullets = $card['bullets'];
				$bullets = array_filter(
					$bullets,
					function ( $bullet ) {
						return ! empty( $bullet['bullet_link'] );
					}
				);
				if ( ! empty( $bullets ) ) :
					?>
					<div class="horiz center-horiz">
						<?php
						foreach ( $bullets as $bullets_index => $bullet ) :
							$link_attributes_str = loveforever_prepare_link_attributes( array( 'class' => 'a-12-12' ), $bullet['bullet_link'] );
							?>
							<a <?php echo $link_attributes_str; ?>>
								<?php echo esc_html( $bullet['bullet_link']['title'] ); ?>
							</a>
							<?php if ( $bullets_index < count( $bullets ) - 1 ) : ?>
								<div class="_2px_romb"></div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $card['button'] ) ) : ?>
					<a href="<?php echo esc_url( $card['button']['url'] ); ?>" target="<?php echo esc_attr( $card['button']['target'] ); ?>" class="btn in-slider-btn w-inline-block">
						<div><?php echo esc_html( $card['button']['title'] ); ?></div>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
