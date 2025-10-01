<?php
/**
 * Header
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$data_wf_page                       = ! empty( $args['data-wf-page'] ) ? $args['data-wf-page'] : '';
$barba_container_extra_classes      = ! empty( $args['barba-container-extra-classes'] ) ? $args['barba-container-extra-classes'] : array();
$container_array_of_data_attributes = array_diff_key( $args, array_flip( array( 'data-wf-page', 'barba-container-extra-classes' ) ) );
?>
<!DOCTYPE html>
<html data-wf-page="<?php echo esc_attr( $data_wf_page ); ?>" data-wf-site="<?php echo esc_attr( DATA_WF_SITE ); ?>" <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta content="width=device-width, initial-scale=1" name="viewport">
		<style>
			html {
				font-size:calc(100vw / 1440);
			}
			@media screen and (max-width:992px){
				html {
					font-size:calc(100vw / 756);
				}
			}
			@media screen and (max-width:495px){
				html {
					font-size:calc(100vw / 375);
				}
			}
		</style>
		<script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
		<?php wp_head(); ?>
		<!-- Top.Mail.Ru counter -->
		<script type="text/javascript">
		var _tmr = window._tmr || (window._tmr = []);
		_tmr.push({id: "3575961", type: "pageView", start: (new Date()).getTime()});
		(function (d, w, id) {
			if (d.getElementById(id)) return;
			var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
			ts.src = "https://top-fwz1.mail.ru/js/code.js";
			var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
			if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
		})(document, window, "tmr-code");
		</script>
		<noscript><div><img src="https://top-fwz1.mail.ru/counter?id=3575961;js=na" style="position:absolute;left:-9999px;" alt="Top.Mail.Ru" /></div></noscript>
		<!-- /Top.Mail.Ru counter -->
	</head>
	<body class="body">
		<?php wp_body_open(); ?>
		<?php
		global $template;
		if ( strpos( $template, 'admin-fittings.php' ) === false && ! is_404() ) {
			get_template_part( 'components/floating-cta' );
		}
		?>
		<?php get_template_part( 'components/custom-css' ); ?>
		<div id="barba-wrapper" class="wrapper">
			<?php $barba_container_classes = array_merge( array( 'barba-container' ), $barba_container_extra_classes ); ?>
			<div 
				class="<?php echo esc_attr( implode( ' ', $barba_container_classes ) ); ?>" 
				<?php
				// phpcs: ignore
				echo loveforever_prepare_barba_container_data_attributes( $container_array_of_data_attributes );
				?>
			>
