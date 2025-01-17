<?php
/**
 * Header
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$data_wf_page                  = ! empty( $args['data-wf-page'] ) ? $args['data-wf-page'] : '';
$barba_container_extra_classes = ! empty( $args['barba-container-extra-classes'] ) ? $args['barba-container-extra-classes'] : array();
?>

<!DOCTYPE html>
<html data-wf-page="<?php echo esc_attr( $data_wf_page ); ?>" data-wf-site="<?php echo esc_attr( DATA_WF_SITE ); ?>">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta content="width=device-width, initial-scale=1" name="viewport">
		<script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
		<link href="https://thevogne.ru/clients/gavril/loveforever/jquery-ui.min.css" rel="stylesheet" type="text/css">
		<link href="https://thevogne.ru/clients/gavril/loveforever/style.css" rel="stylesheet" type="text/css">
		<?php wp_head(); ?>
	</head>
	<body class="body">
		<?php wp_body_open(); ?>
		<?php get_template_part( 'components/custom-css' ); ?>
		<?php
		echo '<pre>';
		$current_time = current_time( 'timestamp' );
		var_dump( gmdate( 'Y-m-d H:i:s', $current_time ) );
		var_dump( Fitting_Slots::get_available_slots( gmdate( 'Y-m-d' ), $current_time ) );
		echo '</pre>';
		?>
	<div id="barba-wrapper" class="wrapper">
		<?php $barba_container_classes = array_merge( array( 'barba-container' ), $barba_container_extra_classes ); ?>
		<div class="<?php echo esc_attr( implode( ' ', $barba_container_classes ) ); ?>">
