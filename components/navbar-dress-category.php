<?php
/**
 * Navbar dress category component
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['category'] ) ) {
	return;
}

$dress_category = get_term( $args['category'] );
$green_badge    = ! empty( $args['green_badge'] ) ? $args['green_badge'] : '';
$columns        = $args['columns'];
?>
<div class="menu-link-keeper">
	<?php
	$link_class_names = array(
		'n-menu',
		'lf-nav-link',
		'w-nav-link',
	);

	if ( loveforever_is_current_url( get_term_link( $dress_category ) ) ) {
		$link_class_names[] = 'is-active';
	}

	$link_attributes = array(
		'href'  => esc_url( get_term_link( $dress_category ) ),
		'class' => esc_attr( implode( ' ', $link_class_names ) ),
	);

	$name_mappings = array(
		'wedding'     => 'Свадебные',
		'evening'     => 'Вечерние',
		'prom'        => 'Выпускные',
		'sale'        => 'Распродажа',
		'accessories' => 'Аксессуары',
	);
	?>
	<a <?php echo loveforever_prepare_tag_attributes_as_string( $link_attributes ); ?>>
		<span><?php echo esc_html( $name_mappings[ $dress_category->slug ] ); ?></span>
		<?php if ( ! empty( $green_badge ) ) : ?>
			<span class="lf-nav-link__badge"><?php echo esc_html( $green_badge ); ?></span>
		<?php endif; ?>
	</a>
	<?php if ( ! empty( $columns ) ) : ?>
		<div class="hovered-menue lf-hover-menu">
			<?php
			foreach ( $columns as $column ) :
				if ( ! empty( $column['links'] ) ) :
					?>
					<div id="w-node-_144563be-6001-1af8-6446-1240953da88e-be61d3ef" class="m-h-vert lf-hover-menu__column">
						<div class="p-16-16 lf-hover-menu__column-title"><?php echo esc_html( $column['column_name'] ); ?></div>
						<div class="m-h-vert lf-hover-menu__column-list<?php echo 10 < count( $column['links'] ) ? ' grider' : ''; ?>">
							<?php
							foreach ( $column['links'] as $link_item ) :
								$link = $link_item['link'];
								?>
								<a 
									href="<?php echo esc_url( $link['url'] ); ?>" 
									class="a-12-12 lf-hover-menu__column-item w-inline-block"
									target="<?php echo esc_attr( $link['target'] ) ?? '_self'; ?>"
								>
									<div><?php echo esc_html( $link['title'] ); ?></div>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
					<?php
				endif;
			endforeach;
			?>
			<div id="w-node-_144563be-6001-1af8-6446-1240953da930-be61d3ef" class="hovered-menue_close-menu"></div>
		</div>
	<?php endif; ?>
</div>
