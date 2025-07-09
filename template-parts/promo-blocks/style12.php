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
$custom_img      = $template_fields['custom_img'] ? wp_get_attachment_image(
	$template_fields['custom_img'],
	'fullhd',
	false,
	array(
		'class'   => 'lf-promo-block__image img-fw',
		'loading' => 'lazy',
	)
) : '';

$template_style12_fields = $template_fields['template_style12_fields'] ?? false;

$field1 = $template_style12_fields['field1'] ?? false;
$field2 = $template_style12_fields['field2'] ?? false;
$field3 = $template_style12_fields['field3'] ?? false;
$field4 = $template_style12_fields['field4'] ?? false;
$field5 = $template_style12_fields['field5'] ?? false;
$field6 = $template_style12_fields['field6'] ?? false;
?>
<article class="test-grid lf-promo-block lf-promo-block--<?php echo $promo_template; ?>">
	<div class="prod-item-tizer type12 promo-gradient" style="background-color: #F22EA9;">
		<div class="prod-item_top">
		<?php
			$link_attributes = array(
				'class' => 'link w-inline-block',
			);

			if ( ! empty( $custom_link ) && is_array( $custom_link ) ) {
				$link_attributes = array_merge( $link_attributes, $custom_link );
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
				<div class="prod-item_img-mom _3">
					<div class="to-keeper">
						<div class="promo-image">
							<?php echo $custom_img; ?>
							<div class="map-dot cd2">
								<div class="p-36-36"><?php echo $field1 ? $field1 : 'совет'; ?></div>
							</div>
							<div class="map-dot _2 cd3">
								<div class="p-36-36"><?php echo $field2 ? $field2 : 'совет'; ?></div>
							</div>
							<div class="map-dot _3 cd3">
								<div class="p-36-36"><?php echo $field3 ? $field3 : 'совет'; ?></div>
							</div>
							<div class="map-dot _4 cd4">
								<div class="p-36-36"><?php echo $field4 ? $field4 : 'совет'; ?></div>
							</div>
						</div>
						<div class="promo-discount" style="color: white;"><?php echo $field5 ? $field5 : 'Короткое свадебное платье стройным девушкам. Хорошо сочетается с высоким  каблуком и с обувью на плоской подошве, если позволяет рост. Часто  дополняется шлейфом или юбкой из прозрачного кружева'; ?></div>
						<div class="promo-link lf-promo-block__link lf-promo-block__link--advice" style="color: white;">
							<?php echo $field6 ? $field6 : 'больше полезных советов '; ?>
							<svg 
								width="4" 
								height="6" 
								viewBox="0 0 4 6" 
								fill="none" 
								xmlns="http://www.w3.org/2000/svg"
								class="lf-promo-block__icon"
							>
								<path fill-rule="evenodd" clip-rule="evenodd" d="M3.41604 3.51284L0.583958 6L0 5.48716L2.83208 3L0 0.512837L0.583958 0L3.41604 2.48716L4 3L3.41604 3.51284Z" fill="white"/>
							</svg>
						</div>
					</div>
				</div>
			</a>
		</div>
	</div>
</article>
