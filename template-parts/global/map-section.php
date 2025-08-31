<?php
/**
 * Map Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$is_contact_page = ! empty( $args['is-contact-page'] ) ? $args['is-contact-page'] : false;
$map_section     = get_field( 'map-section', 'option' );

if ( ! empty( $map_section['map'] ) ) :
	$section_map = $map_section['map'];
	$top_text    = $map_section['top_text'];
	$bottom_text = $map_section['bottom_text'];
	$left_text   = $map_section['left_text'];
	$right_text  = $map_section['right_text'];
	$button      = $map_section['button'];
	?>
	<section id="map" class="section">
		<div class="container">
			<div class="vert vert-center m-str">
				<div class="map-keeper lf-map">
					<div id="yandexMapSpbVoz" class="lf-map__container"></div>
					<?php
					// phpcs:ignore
					//echo $section_map; ?>
					<?php if ( ! empty( $top_text ) ) : ?>
						<div class="map-dot lf-map__text lf-map__text--1">
							<div class="p-64-64 map-line"><?php echo esc_html( $top_text ); ?></div>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $left_text ) ) : ?>
						<div class="map-dot _2 lf-map__text lf-map__text--2">
							<div class="p-64-64 map-line"><?php echo esc_html( $left_text ); ?></div>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $right_text ) ) : ?>
						<div class="map-dot _3 lf-map__text lf-map__text--3">
							<div class="p-64-64 map-line"><?php echo esc_html( $right_text ); ?></div>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $bottom_text ) ) : ?>
						<div class="map-dot _4 lf-map__text lf-map__text--4">
							<div class="p-64-64 map-line"><?php echo esc_html( $bottom_text ); ?></div>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( ! $is_contact_page && ! empty( $button ) ) : ?>
					<a href="<?php echo esc_url( $button['url'] ); ?>" class="btn pink-btn w-inline-block" target="<?php echo esc_attr( $button['target'] ); ?>">
						<div><?php echo esc_html( $button['title'] ); ?></div>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>
