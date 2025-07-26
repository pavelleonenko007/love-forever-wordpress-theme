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
$cards          = ! empty( $args['cards'] ) && is_array( $args['cards'] ) ? array_filter(
	$args['cards'],
	function ( $card ) {
		return ! empty( $card['page_link'] ) && ! empty( $card['image'] );
	}
) : array();
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
	?>
	<a <?php echo loveforever_prepare_tag_attributes_as_string( $link_attributes ); ?>>
		<span><?php echo esc_html( str_replace( ' платья', '', $dress_category->name ) ); ?></span>
		<?php if ( ! empty( $green_badge ) ) : ?>
			<span class="lf-nav-link__badge"><?php echo esc_html( $green_badge ); ?></span>
		<?php endif; ?>
	</a>
	<?php
	if ( ! empty( $cards ) ) :
		$list_class_names = array(
			'hovered-menue',
			'lf-hover-menu',
			'lf-hover-menu-cards',
			'lf-hover-menu-cards--' . count( $cards ) . '-items',
		);
		?>
		<ul class="<?php echo esc_attr( implode( ' ', $list_class_names ) ); ?>">
			<?php
			foreach ( $cards as $card ) :
				?>
				<li class="lf-hover-menu-cards__item">
					<?php
					$page_link_attributes = array(
						'class' => 'lf-hover-menu-card',
					);

					if ( ! empty( $card['page_link'] ) && is_array( $card['page_link'] ) ) {
						$page_link_attributes         = array_merge( $page_link_attributes, $card['page_link'] );
						$page_link_attributes['href'] = $page_link_attributes['url'];
						unset( $page_link_attributes['url'] );
					}

					if ( ! empty( $page_link_attributes['target'] && '_blank' === $page_link_attributes['target'] ) ) {
						$page_link_attributes['rel']        = 'noopener noreferrer';
						$page_link_attributes['title']      = $page_link_attributes['title'] . ' (открывается в новой вкладке)';
						$page_link_attributes['aria-label'] = $page_link_attributes['title'] . ' (открывается в новой вкладке)';
					}

					$page_link_attributes = array_filter( $page_link_attributes );

					$page_link_attributes_str = loveforever_prepare_tag_attributes_as_string( $page_link_attributes );
					?>
					<a <?php echo $page_link_attributes_str; ?>>
						<h3 class="lf-hover-menu-card__title"><?php echo esc_html( $card['page_link']['title'] ); ?></h3>
						<div class="lf-hover-menu-card__image">
							<?php echo wp_get_attachment_image( $card['image'], 'fullhd', false ); ?>
						</div>
					</a>
				</li>
				<?php
			endforeach;
			?>
			<div id="w-node-_144563be-6001-1af8-6446-1240953da930-be61d3ef" class="hovered-menue_close-menu"></div>
		</ul>
	<?php endif; ?>
</div>
