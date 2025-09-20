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
	'class'   => 'lf-promo-block-1__image',
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

$template_style1_fields = $template_fields['template_style1_fields'] ?? false;

$field1           = $template_style1_fields['field1'] ?? false;
$field2           = $template_style1_fields['field2'] ?? false;
$field3           = $template_style1_fields['field3'] ?? false;
$field4           = $template_style1_fields['field4'] ?? false;
$text_under_image = $template_style1_fields['text_under_image'] ?? '';

$link_attributes = loveforever_prepare_link_attributes( array(), $custom_link );
?>
<article class="lf-promo-block lf-promo-block-1">
	<div class="lf-promo-block__wrapper lf-promo-block-1__wrapper">
		<a <?php echo $link_attributes; ?> class="lf-promo-block-1__link">
			<div class="lf-promo-block-1__header">
				<div class="lf-promo-block-1__image">
					<?php echo $custom_img; ?>
				</div>
				<h3 class="lf-promo-block-1__title">
					<span class="lf-promo-block-1__span lf-promo-block-1__span--1"><?php echo $field1; ?></span>
					<span class="lf-promo-block-1__span lf-promo-block-1__span--2"><?php echo $field3; ?></span>
				</h3>
				<h3 class="lf-promo-block-1__title lf-promo-block-1__title--reversed" aria-hidden="true">
					<span class="lf-promo-block-1__span lf-promo-block-1__span--3"><?php echo $field2; ?></span>
					<span class="lf-promo-block-1__span lf-promo-block-1__span--4"><?php echo $field4; ?></span>
				</h3>
			</div>
			<?php if ( ! empty( $text_under_image ) ) : ?>
				<p class="lf-promo-block-1__description"><?php echo $text_under_image; ?></p>
			<?php endif; ?>
		</a>
	</div>
	<a <?php echo $link_attributes; ?> class="lf-promo-block__button">
		<span class="lf-promo-block__button-text">Смотреть</span>
	</a>
</article>
