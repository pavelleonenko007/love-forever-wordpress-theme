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

$template_style1_fields = $template_fields['template_style1_fields'] ?? false;

$field1 = $template_style1_fields['field1'] ?? false;
$field2 = $template_style1_fields['field2'] ?? false;
$field3 = $template_style1_fields['field3'] ?? false;
$field4 = $template_style1_fields['field4'] ?? false;
$log = date('Y-m-d H:i:s') . ' | $custom_img - ' . print_r($custom_img, true);
file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
?>
<div class="test-grid">
    <div class="prod-item-tizer type1">
        <div class="prod-item_top">
            <a href="<?php echo $custom_link ? $custom_link : '#'; ?>" class="link w-inline-block">
                <div class="prod-item_img-mom _3">
                    <div class="to-keeper">
                        <img src="<?php echo $custom_img ? $custom_img : get_template_directory_uri() . '/images/style1_img.jpg'; ?>" alt="" class="img-fw">
                        <div class="map-dot cd2">
                            <div class="p-36-36"><?php echo $field1 ? $field1 : 'платья'; ?></div>
                        </div>
                        <div class="map-dot _2 cd3">
                            <div class="p-36-36"><?php echo $field2 ? $field2 : 'платья'; ?></div>
                        </div>
                        <div class="map-dot _3 cd3">
                            <div class="p-36-36"><?php echo $field3 ? $field3 : 'в загс'; ?></div>
                        </div>
                        <div class="map-dot _4 cd4">
                            <div class="p-36-36"><?php echo $field4 ? $field4 : 'в загс'; ?></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>