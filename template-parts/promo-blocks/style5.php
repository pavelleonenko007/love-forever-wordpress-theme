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

$template_style5_fields = $template_fields['template_style5_fields'] ?? false;

$field1 = $template_style5_fields['field1'] ?? false;
$field2 = $template_style5_fields['field2'] ?? false;
?>
<article class="test-grid lf-promo-block lf-promo-block--<?php echo $promo_template; ?>">
	<div class="prod-item-tizer">
		<div class="prod-item_top type5">
		<?php
			$link_attributes = array(
				'class' => 'link w-inline-block',
			);

			if ( ! empty( $custom_link ) && is_array( $custom_link ) ) {
				$link_attributes         = array_merge( $link_attributes, $custom_link );
				$link_attributes['href'] = $link_attributes['url'];
				unset( $link_attributes['url'] );
			}

			if ( ! empty( $link_attributes['target'] ) && '_blank' === $link_attributes['target'] ) {
				$link_attributes['rel']        = 'noopener noreferrer';
				$link_attributes['title']      = $link_attributes['title'] . ' (открывается в новой вкладке)';
				$link_attributes['aria-label'] = $link_attributes['title'] . ' (открывается в новой вкладке)';
			}

			$link_attributes = array_filter( $link_attributes );

			$link_attributes_str = loveforever_prepare_tag_attributes_as_string( $link_attributes );
			?>
			<a <?php echo $link_attributes_str; ?>>
				<div class="prod-item_img-mom _3 lf-promo-block__wrapper">
					<div class="to-keeper lf-promo-block__content">
						<?php echo $custom_img; ?>
						<div class="promo-sale"><?php echo $field1 ? $field1 : 'АТЛАСНЫЕ'; ?></div>
						<div class="promo-discount"><?php echo $field2 ? $field2 : 'совершенство<br> в каждой детали'; ?></div>
					</div>
				</div>
			</a>
		</div>
	</div>
	<?php
	$link_attributes = array(
		'class' => 'lf-promo-block__button',
	);

	if ( ! empty( $custom_link ) && is_array( $custom_link ) ) {
		$link_attributes         = array_merge( $link_attributes, $custom_link );
		$link_attributes['href'] = $link_attributes['url'];
		unset( $link_attributes['url'] );
	}

	if ( ! empty( $link_attributes['target'] ) && '_blank' === $link_attributes['target'] ) {
		$link_attributes['rel']        = 'noopener noreferrer';
		$link_attributes['title']      = $link_attributes['title'] . ' (открывается в новой вкладке)';
		$link_attributes['aria-label'] = $link_attributes['title'] . ' (открывается в новой вкладке)';
	}

	$link_attributes = array_filter( $link_attributes );

	$link_attributes_str = loveforever_prepare_tag_attributes_as_string( $link_attributes );
	?>
	<a <?php echo $link_attributes_str; ?>>
		<span class="lf-promo-block__button-text">Смотреть</span>
	</a>
</article>
