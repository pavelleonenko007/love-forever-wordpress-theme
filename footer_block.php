
<?php wp_footer(); ?>
<script type="text/javascript"
  src="<?php echo get_template_directory_uri() ?>/js/main.js?ver=1733133350"></script>
<script type="text/javascript"
  src="<?php echo get_template_directory_uri() ?>/js/front.js?ver=1733133350"></script>
<?php if(file_exists(dirname( __FILE__ ).'/mailer.php') && empty(get_field('wtw_forms', 'option'))){ include_once 'mailer.php'; } ?>
<?php if(function_exists('get_field')) { echo get_field('footer_code', 'option'); } ?>
<?php if(file_exists(dirname( __FILE__ ).'/footer_code.php')){ include_once 'footer_code.php'; } ?>
<script type="text/javascript"
  src="<?php echo get_template_directory_uri() ?>/js/shop.js?ver=1733133350"></script>
<script src="https://thevogne.ru/customfiles/barba.js"></script><script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script><script src="https://thevogne.ru/clients/gavril/loveforever/scripts.js"></script>