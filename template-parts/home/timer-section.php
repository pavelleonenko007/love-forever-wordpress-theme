<?php
/**
 * Timer Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$section         = $args;
$timer_stop_date = $section['timer_stop_date'];
$description     = $section['description'];
$cta             = $section['cta'];

if ( ! empty( $timer_stop_date ) || $timer_stop_date > time() ) :
	?>
<section class="section">
	<div class="container n-top">
		<div class="akcc-line">
			<div data-js-timer data-js-timer-stop-date="<?php echo esc_attr( $timer_stop_date ); ?>" class="timer-mom">
				<div data-js-timer-days class="p-48-48">00</div>
				<div class="p-12-12 p-r-40">дни</div>
				<div data-js-timer-hours class="p-48-48">00</div>
				<div class="p-12-12 p-r-40">часы</div>
				<div data-js-timer-minutes class="p-48-48">00</div>
				<div class="p-12-12 p-r-40">минуты</div>
			</div>
		<div class="p-20-26 mmax295"><?php echo wp_kses_post( $description ); ?></div>
		<?php
		if ( ! empty( $cta ) ) :
			$title  = $cta['title'];
			$url    = $cta['url'];
			$target = ! empty( $cta['target'] ) ? $cta['target'] : '_self';
			?>
			<a href="<?php echo esc_url( $url ); ?>" target="<?php echo esc_attr( $target ); ?>" class="btn in-slider-btn purple-color p-l-48 w-inline-block">
				<div><?php echo esc_html( $title ); ?></div>
			</a>
		<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>
