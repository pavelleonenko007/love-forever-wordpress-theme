<?php
/**
 * Breadcrumb
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;
$breadcrumb_classes = array( 'breadcrumbs' );

if ( ! empty( $args['extra_classes'] ) ) {
	$breadcrumb_classes = array_merge( $breadcrumb_classes, $args['extra_classes'] );
}
?>

<ol class="<?php echo esc_attr( implode( ' ', $breadcrumb_classes ) ); ?>">
	<?php
	add_filter( 'bcn_display_attributes', 'loveforever_breadcrumbs_attribute_filter', 10, 3 );
	bcn_display_list();
	remove_filter( 'bcn_display_attributes', 'loveforever_breadcrumbs_attribute_filter' );
	?>
</ol>
