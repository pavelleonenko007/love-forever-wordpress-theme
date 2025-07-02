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

$template_style10_fields = $template_fields['template_style10_fields'] ?? false;

$field1 = $template_style10_fields['field1'] ?? false;
$field2 = $template_style10_fields['field2'] ?? false;
?>
<div class="test-grid">
    <div class="prod-item-tizer">
        <div class="prod-item_top type10">
            <a href="<?php echo $custom_link ? $custom_link : '#'; ?>" class="link w-inline-block">
                <div class="prod-item_img-mom _3" style="background-image: url(<?php echo $custom_img ? $custom_img : (get_template_directory_uri() . '/images/style10_img.jpg'); ?>);">
                    <div class="to-keeper">
                        <div class="">
                            <div class="promo-sale p-36-36 italic"><?php echo $field1 ? $field1 : 'силуэт русалка'; ?></div>
                            <div class="promo-discount"><?php echo $field2 ? $field2 : 'для утонченного<br> образа'; ?></div>
                        </div>
                    </div>
                </div>
            </a>

        </div>
    </div>
</div>
