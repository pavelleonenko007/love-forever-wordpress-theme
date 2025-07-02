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

$template_style3_fields = $template_fields['template_style3_fields'] ?? false;

$field1 = $template_style3_fields['field1'] ?? false;
$field2 = $template_style3_fields['field2'] ?? false;
?>
<div class="test-grid">
    <div class="prod-item-tizer">
        <div class="prod-item_top type3 promo-gradient" style="background-color: #801F80;">
            <a href="<?php echo $custom_link ? $custom_link : '#'; ?>" class="link w-inline-block">
                <div class="prod-item_img-mom _3">
                    <div class="to-keeper" style="background-image: url(<?php echo $custom_img ? $custom_img : (get_template_directory_uri() . '/images/style3_img.jpg'); ?>);">
                        <div class="promo-sale" style="color: white;">
                            <?php echo $field1 ? $field1 : 'распродажа'; ?>
                        </div>
                        <div class="promo-discount p-16-20 italic" style="color: white;">
                            <?php echo $field2 ? $field2 : 'до 70%'; ?>
                        </div>
                    </div>
                </div>
            </a>

        </div>
    </div>
</div>
