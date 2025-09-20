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
	'class'   => 'lf-promo-block-5__image',
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

$template_style5_fields = $template_fields['template_style5_fields'] ?? false;

$field1 = $template_style5_fields['field1'] ?: 'АТЛАСНЫЕ';
$field2 = $template_style5_fields['field2'] ?: 'совершенство<br> в каждой детали';

$link_attributes = loveforever_prepare_link_attributes(array(), $custom_link);
?>
<article class="lf-promo-block lf-promo-block-5">
  <div class="lf-promo-block__wrapper lf-promo-block-5__wrapper">
    <a <?php echo $link_attributes; ?> class="lf-promo-block-5__link">
      <div class="lf-promo-block-5__image">
        <?php echo $custom_img; ?>
      </div>
      <div class="lf-promo-block-5__content">
        <h3 class="lf-promo-block-5__title"><?php echo $field1; ?></h3>
        <p class="lf-promo-block-5__description"><?php echo $field2; ?></p>
      </div>
    </a>
  </div>
  <a href="#" class="lf-promo-block__button">
    <span class="lf-promo-block__button-text">Смотреть</span>
  </a>
</article>
