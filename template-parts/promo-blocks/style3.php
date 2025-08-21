<?php
$args = wp_parse_args(
	$args,
	array(
		'post_id'     => 0,
		'post_object' => null,
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
$custom_img      = $template_fields['custom_img'] ? wp_get_attachment_image_url(
	$template_fields['custom_img'],
	'fullhd',
	false,
) : false;

$template_style3_fields = $template_fields['template_style3_fields'] ?? false;

$field1 = $template_style3_fields['field1'] ?? false;
$field2 = $template_style3_fields['field2'] ?? false;

$link_attributes = loveforever_prepare_link_attributes( array(), $custom_link );
?>
<article class="test-grid lf-promo-block lf-promo-block-3">
	<div class="lf-promo-block__wrapper lf-promo-block-3__wrapper">
		<a <?php echo $link_attributes; ?> class="lf-promo-block-3__link">
			<div class="lf-promo-block-3__image">
				<?php echo wp_get_attachment_image( $template_fields['custom_img'], 'fullhd' ); ?>
			</div>
			<div class="lf-promo-block-3__content">
				<h3 class="lf-promo-block-3__title">
					<?php echo wp_kses_post( $field1 ); ?>
				</h3>
				<p class="lf-promo-block-3__description">
					<?php echo wp_kses_post( $field2 ); ?>
				</p>
			</div>
		</a>
	</div>
	<a <?php echo $link_attributes; ?> class="lf-promo-block__button">
		<span class="lf-promo-block__button-text">Смотреть</span>
	</a>
</article>