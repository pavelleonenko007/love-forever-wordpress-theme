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

$template_style6_fields = $template_fields['template_style6_fields'] ?? false;

$field1 = $template_style6_fields['field1'] ?? false;
$field2 = $template_style6_fields['field2'] ?? false;
$field3 = $template_style6_fields['field3'] ?? false;
?>
<div class="test-grid">
    <div class="prod-item-tizer">
        <div class="prod-item_top type6">
            <a href="<?php echo $custom_link ? $custom_link : '#'; ?>" class="link w-inline-block">
                <div class="prod-item_img-mom _3">
                    <div class="to-keeper">
                        <img src="<?php echo $custom_img ? $custom_img : (get_template_directory_uri() . '/images/style6_img.jpg');?>" alt="" class="img-fw" style="display: block">
                        <div class="map-dot _2 cd3">
                            <div class="" style="color: white"><?php echo $field1 ? $field1 : 'классические'; ?></div>
                        </div>
                        <div class="map-dot _3 cd3">
                            <div class="" style="color: white"><?php echo $field2 ? $field2 : 'классические'; ?></div>
                        </div>
                    </div>
                    <div class="promo-discount"><?php echo $field3 ? $field3 : "безупречность <br>вне времени"; ?></div>
                </div>
            </a>

        </div>
    </div>
</div>