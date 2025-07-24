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

$template_style9_fields = $template_fields['template_style9_fields'] ?? false;

$field1 = $template_style9_fields['field1'] ?? false;
$field2 = $template_style9_fields['field2'] ?? false;
?>
<article class="test-grid lf-promo-block lf-promo-block--<?php echo $promo_template; ?>">
	<div class="prod-item-tizer">
		<div class="prod-item_top type9">
			<?php
			$link_attributes = array(
				'class' => 'link w-inline-block',
			);

			if ( ! empty( $custom_link ) && is_array( $custom_link ) ) {
				$link_attributes         = array_merge( $link_attributes, $custom_link );
				$link_attributes['href'] = $link_attributes['url'];
				unset( $link_attributes['url'] );
			}

			if ( ! empty( $link_attributes['target'] && '_blank' === $link_attributes['target'] ) ) {
				$link_attributes['rel']        = 'noopener noreferrer';
				$link_attributes['title']      = $link_attributes['title'] . ' (открывается в новой вкладке)';
				$link_attributes['aria-label'] = $link_attributes['title'] . ' (открывается в новой вкладке)';
			}

			$link_attributes = array_filter( $link_attributes );

			$link_attributes_str = loveforever_prepare_tag_attributes_as_string( $link_attributes );
			?>
			<a <?php echo $link_attributes_str; ?>>
				<div class="prod-item_img-mom _3" style="background-image: url(<?php echo $custom_img ? $custom_img : esc_url( TEMPLATE_PATH . '/images/style9_img.jpg' ); ?>);">
					<div class="to-keeper">
						<div class="promo-sale"><?php echo $field1 ? $field1 : 'размеры'; ?></div>
						<div class="promo-box-devider"></div>
						<div class="promo-sale"><?php echo $field2 ? $field2 : 'от 48 до 52'; ?></div>
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

	if ( ! empty( $link_attributes['target'] && '_blank' === $link_attributes['target'] ) ) {
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
