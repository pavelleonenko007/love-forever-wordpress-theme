<?php
/**
 *  Marquee component
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args ) ) {
	return;
}

$config = wp_parse_args(
	$args,
	array(
		'line_type' => 'static',
		'line_link' => '',
		'text'      => '',
	)
);

$class_names = array(
	'lf-info-line',
	'lf-info-line--' . $config['line_type'],
);

$link_attributes = array(
	'href'  => $config['line_link'],
	'class' => implode( ' ', $class_names ),
);

$line_attributes_str = loveforever_prepare_tag_attributes_as_string( $link_attributes );

$items = array(
	$config['text'],
);

if ( 'marquee' === $config['line_type'] ) {
	$items = array_fill( 0, 10, $config['text'] );
}

?>

<a <?php echo $line_attributes_str; ?>>
	<ul class="lf-info-line__list">
		<?php foreach ( $items as $item ) : ?>
			<li class="lf-info-line__list-item">
				<?php echo wp_kses_post( $item ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( 'marquee' === $config['line_type'] ) : ?>
		<ul class="lf-info-line__list" aria-hidden="true">
		<?php foreach ( $items as $item ) : ?>
			<li class="lf-info-line__list-item">
				<?php echo wp_kses_post( $item ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</a>
