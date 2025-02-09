<?php
/**
 * Navbar Simple Link
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$menu_link      = $args['link'] ?? array();
$is_current_url = loveforever_is_current_url( $menu_link['url'] ?? '' );
$current_class  = $is_current_url ? ' w--current' : '';
$aria_current   = $is_current_url ? ' aria-current="page"' : '';
?>
<a 
	href="<?php echo esc_url( $menu_link['url'] ?? '' ); ?>" 
	class="n-menu w-nav-link<?php echo esc_attr( $current_class ); ?>" 
	target="<?php echo esc_attr( $menu_link['target'] ?? '' ); ?>"
	<?php echo esc_attr( $aria_current ); ?>
>
	<span><?php echo esc_html( $menu_link['title'] ?? '' ); ?></span>
</a>
