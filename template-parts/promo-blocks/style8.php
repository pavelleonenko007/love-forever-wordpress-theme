<?php
$args = wp_parse_args(
	$args,
	array(
		'post_id'       => 0,
		'post_object'   => null,
		'image_loading' => 'lazy',
	)
);

// Получаем пост по ID или используем объект
if ( $args['post_id'] ) {
	$post = get_post( $args['post_id'] );
} elseif ( $args['post_object'] ) {
	$post = $args['post_object'];
} else {
	// Фолбэк на глобальную переменную (не рекомендуется для AJAX)
	global $post;
}

$promo_template  = get_field( 'promo_template', $post->ID );
$template_fields = get_field( 'template_fields', $post->ID );
$custom_link     = $template_fields['custom_link'] ?? false;

$img_attributes = array(
	'loading' => $args['image_loading'],
	'class'   => 'lf-promo-block__image img-fw',
	'style'   => 'display: block',
);

if ( 'eager' === $args['image_loading'] ) {
	$img_attributes['fetchpriority'] = 'high';
}

$custom_img = $template_fields['custom_img'] ? wp_get_attachment_image(
	$template_fields['custom_img'],
	'fullhd',
	false,
	$img_attributes
) : '';

$template_style8_fields = $template_fields['template_style8_fields'] ?? false;

$field1 = $template_style8_fields['field1'] ?? false;
$field2 = $template_style8_fields['field2'] ?? false;
$field3 = $template_style8_fields['field3'] ?? false;
$field4 = $template_style8_fields['field4'] ?? false;

$link_attributes = loveforever_prepare_link_attributes( array(), $custom_link );
?>
<article class="lf-promo-block lf-promo-block-8">
	<div class="lf-promo-block__wrapper lf-promo-block-8__wrapper">
		<a <?php echo $link_attributes; ?> class="lf-promo-block-8__link">
			<div class="lf-promo-block-8__header">
				<div class="lf-promo-block-8__image">
					<?php echo $custom_img; ?>
				</div>
				<h3 class="lf-promo-block-8__title lf-promo-block-8__title">
					<span class="lf-promo-block-8__span lf-promo-block-8__span--1"><?php echo $field1; ?></span>
					<span class="lf-promo-block-8__span lf-promo-block-8__span--2" aria-hidden="true"><?php echo $field2; ?></span>
					<span class="lf-promo-block-8__span lf-promo-block-8__span--3" aria-hidden="true"><?php echo $field3; ?></span>
					<span class="lf-promo-block-8__span lf-promo-block-8__span--4" aria-hidden="true"><?php echo $field4; ?></span>
				</h3>
			</div>
		</a>
	</div>
	<a <?php echo $link_attributes; ?> class="lf-promo-block__button">
		<span class="lf-promo-block__button-text">Смотреть</span>
	</a>
</article>
