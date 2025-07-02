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

$template_style2_fields = $template_fields['template_style2_fields'] ?? false;

$field1 = $template_style2_fields['field1'] ?? false;
$field2 = $template_style2_fields['field2'] ?? false;
?>
<div class="test-grid">
    <div class="prod-item-tizer">
        <div class="prod-item_top type2 promo-gradient" style="background-color: #801F80;">
            <a href="<?php echo $custom_link ? $custom_link : '#'; ?>" class="link w-inline-block">
                <div class="prod-item_img-mom _3">
                    <div class="to-keeper">
                        <div class="promo-sale" style="color: white;"><?php echo $field1 ? $field1 : 'рас<br>про<br>да<br>жа'; ?></div>
                        <div class="promo-discount p-16-20 italic" style="color: white;"><?php echo $field2 ? $field2 : 'до 70%'; ?></div>
                    </div>
                </div>
            </a>

        </div>
    </div>
</div>


