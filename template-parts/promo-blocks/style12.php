<?php
$args = wp_parse_args($args, [
    'post_id' => 0,
    'post_object' => null
]);

// Получаем пост по ID или используем объект
if ($args['post_id']) {
    $post = get_post($args['post_id']);
} elseif ($args['post_object']) {
    $post = $args['post_object'];
} else {
    // Фолбэк на глобальную переменную (не рекомендуется для AJAX)
    global $post;
}
$template_fields = get_field('template_fields', $post->ID);
$custom_link = $template_fields['custom_link'] ?? false;
$custom_img = $template_fields['custom_img']['sizes']['medium'] ?? false;

$template_style12_fields = $template_fields['template_style12_fields'] ?? false;

$field1 = $template_style12_fields['field1'] ?? false;
$field2 = $template_style12_fields['field2'] ?? false;
$field3 = $template_style12_fields['field3'] ?? false;
$field4 = $template_style12_fields['field4'] ?? false;
$field5 = $template_style12_fields['field5'] ?? false;
$field6 = $template_style12_fields['field6'] ?? false;
?>
<div class="test-grid">
    <div class="prod-item-tizer type12 promo-gradient" style="background-color: #F22EA9;">
        <div class="prod-item_top">
            <a href="<?php echo $custom_link ? $custom_link : '#'; ?>" class="link w-inline-block">
                <div class="prod-item_img-mom _3">
                    <div class="to-keeper">
                        <div class="promo-image">
                            <img src="<?php echo $custom_img ? $custom_img : (get_template_directory_uri() . '/images/Alvina.webp'); ?>" alt="" class="img-fw">
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
                        <div class="promo-link" style="color: white;"><?php echo $field6 ? $field6 : 'больше полезных советов '; ?></div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>