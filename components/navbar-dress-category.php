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
$columns        = $args['columns'];
?>
<div class="menu-link-keeper">
	<a 
		href="<?php echo esc_url( get_term_link( $dress_category ) ); ?>" 
		class="n-menu w-nav-link"
	>
		<?php echo esc_html( str_replace( ' платья', '', $dress_category->name ) ); ?>
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
			<?php
			$products_query_args = array(
				'post_type'      => 'dress',
				'posts_per_page' => 3,
				'meta_key'       => 'product_views_count',
				'orderby'        => array(
					'menu_order'     => 'ASC',
					'meta_value_num' => 'DESC',
				),
				'tax_query'      => array(
					array(
						'taxonomy' => 'dress_category',
						'field'    => 'term_id',
						'terms'    => $dress_category->term_id,
					),
				),
			);
			$products_query      = new WP_Query( $products_query_args );
			if ( $products_query->have_posts() ) :
				?>
				<div id="w-node-_144563be-6001-1af8-6446-1240953da917-be61d3ef" class="menu-choosed-items">
					<?php
					while ( $products_query->have_posts() ) :
						$products_query->the_post();
						?>
						<?php get_template_part( 'components/navbar-dress-card' ); ?>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>
			<div id="w-node-_144563be-6001-1af8-6446-1240953da930-be61d3ef" class="hovered-menue_close-menu"></div>
		</div>
	<?php endif; ?>
</div>
