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

$template_style6_fields = $template_fields['template_style6_fields'] ?? false;

$field1 = $template_style6_fields['field1'] ?? false;
$field2 = $template_style6_fields['field2'] ?? false;
$field3 = $template_style6_fields['field3'] ?? false;

$link_attributes = loveforever_prepare_link_attributes( array(), $custom_link );
?>
<article class="lf-promo-block lf-promo-block-6">
	<div class="lf-promo-block__wrapper lf-promo-block-6__wrapper">
		<a <?php echo $link_attributes; ?> class="lf-promo-block-6__link">
			<div class="lf-promo-block-6__header">
				<div class="lf-promo-block-6__image">
					<?php echo $custom_img; ?>
				</div>
				<h3 class="lf-promo-block-6__title lf-promo-block-6__title--reversed">
					<span class="lf-promo-block-6__span lf-promo-block-6__span--3"><?php echo $field1; ?></span>
					<span class="lf-promo-block-6__span lf-promo-block-6__span--4"><?php echo $field2; ?></span>
				</h3>
			</div>
			<?php if ( ! empty( $field3 ) ) : ?>
				<p class="lf-promo-block-6__description"><?php echo $field3; ?></p>
			<?php endif; ?>
		</a>
	</div>
	<a <?php echo $link_attributes; ?> class="lf-promo-block__button">
		<span class="lf-promo-block__button-text">Смотреть</span>
	</a>
</article>
