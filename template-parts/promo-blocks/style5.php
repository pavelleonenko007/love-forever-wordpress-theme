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

$template_style5_fields = $template_fields['template_style5_fields'] ?? false;

$field1 = $template_style5_fields['field1'] ?? false;
$field2 = $template_style5_fields['field2'] ?? false;
?>
<div class="test-grid">
    <div class="prod-item-tizer">
        <div class="prod-item_top type5">
            <a href="<?php echo $custom_link ? $custom_link : '#'; ?>" class="link w-inline-block">
                <div class="prod-item_img-mom _3">
                    <div class="to-keeper">
                            <img src="<?php echo $custom_img ? $custom_img : (get_template_directory_uri() . '/images/style5_img.jpg'); ?>" alt="" class="img-fw">
                        <div class="promo-sale"><?php echo $field1 ? $field1 : 'АТЛАСНЫЕ'; ?></div>
                        <div class="promo-discount"><?php echo $field2 ? $field2 : 'совершенство<br> в каждой детали'; ?></div>
                    </div>
                </div>
            </a>

        </div>
    </div>
</div>
