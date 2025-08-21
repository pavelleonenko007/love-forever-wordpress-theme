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

$template_style2_fields = $template_fields['template_style2_fields'] ?? false;

$field1 = $template_style2_fields['field1'] ?? false;
$field2 = $template_style2_fields['field2'] ?? false;

$link_attributes = loveforever_prepare_link_attributes( array(), $custom_link );
?>
<article class="test-grid lf-promo-block lf-promo-block-2">
	<div class="lf-promo-block__wrapper lf-promo-block-2__wrapper">
		<a <?php echo $link_attributes; ?> class="lf-promo-block-2__link">
			<h3 class="lf-promo-block-2__title"><?php echo wp_kses_post( $field1 ); ?></h3>
			<p class="lf-promo-block-2__description"><?php echo wp_kses_post( $field2 ); ?></p>
		</a>
	</div>
	<a <?php echo $link_attributes; ?> class="lf-promo-block__button">
		<span class="lf-promo-block__button-text">Смотреть</span>
	</a>
</article>