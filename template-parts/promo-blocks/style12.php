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

$template_style12_fields = $template_fields['template_style12_fields'] ?? false;

$field1 = $template_style12_fields['field1'] ?? false;
$field2 = $template_style12_fields['field2'] ?? false;
$field3 = $template_style12_fields['field3'] ?? false;
$field4 = $template_style12_fields['field4'] ?? false;
$field5 = $template_style12_fields['field5'] ?? false;
$field6 = $template_style12_fields['field6'] ?? false;

$link_attributes = loveforever_prepare_link_attributes( array(), $custom_link );
?>
<article class="lf-promo-block lf-promo-block-12">
	<div class="lf-promo-block__wrapper lf-promo-block-12__wrapper">
		<a <?php echo $link_attributes; ?> class="lf-promo-block-12__link">
			<div class="lf-promo-block-12__header">
				<div class="lf-promo-block-12__image">
					<?php echo $custom_img; ?>
				</div>
				<h3 class="lf-promo-block-12__title">
					<span class="lf-promo-block-12__span lf-promo-block-12__span--1">
						<?php echo $field1; ?>
					</span>
					<span class="lf-promo-block-12__span lf-promo-block-12__span--2">
						<?php echo $field2; ?>
					</span>
					<span class="lf-promo-block-12__span lf-promo-block-12__span--3">
						<?php echo $field3; ?>
					</span>
					<span class="lf-promo-block-12__span lf-promo-block-12__span--4">
						<?php echo $field4; ?>
					</span>
				</h3>
			</div>
			<div class="lf-promo-block-12__content">
				<p class="lf-promo-block-12__description">
					<?php echo $field5; ?>
				</p>
				<?php if ( ! empty( $custom_link ) ) : ?>
				<p class="lf-promo-block-12__cta">
					<span class="lf-promo-block-12__cta-text">
						<?php echo $custom_link['title']; ?>
					</span>
					<svg width="4" height="6" viewBox="0 0 4 6" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M3.41604 3.51284L0.583958 6L0 5.48716L2.83208 3L0 0.512837L0.583958 0L3.41604 2.48716L4 3L3.41604 3.51284Z" fill="white"/>
					</svg>
				</p>
				<?php endif; ?>
			</div>
		</a>
	</div>
</article>
