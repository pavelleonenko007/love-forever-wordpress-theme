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

$dress_category        = get_term( $args['category'] );
$dropdown_menu_columns = $args['dropdown_menu_columns'];
?>
<div class="menu-link-keeper">
	<a 
		href="<?php echo esc_url( get_term_link( $dress_category ) ); ?>" 
		class="n-menu w-nav-link"
	>
		<?php echo esc_html( str_replace( ' платья', '', $dress_category->name ) ); ?>
	</a>
	<?php if ( ! empty( $dropdown_menu_columns ) ) : ?>
		<div class="hovered-menue">
			<?php
			foreach ( $dropdown_menu_columns as $dropdown_menu_column ) :
				if ( 'price' !== $dropdown_menu_column ) :
					$tax_object = get_taxonomy( $dropdown_menu_column );
					if ( ! empty( $tax_object ) ) :
						?>
						<div id="w-node-_144563be-6001-1af8-6446-1240953da88b-be61d3ef" class="m-h-vert">
							<div class="p-16-16"><?php echo esc_html( $tax_object->labels->singular_name ); ?></div>
							<?php
							$terms_args = array(
								'taxonomy'   => $tax_object->name,
								'hide_empty' => false, // TODO: set to true!
							);
							$terms      = get_terms( $terms_args );
							if ( ! empty( $terms ) ) :
								?>
								<div id="w-node-_144563be-6001-1af8-6446-1240953da88e-be61d3ef" class="m-h-vert">
									<?php foreach ( $terms as $term_item ) : ?>
										<a href="<?php echo esc_url( get_term_link( $dress_category ) . '?' . $tax_object->name . '=' . $term_item->term_id ); ?>" class="a-12-12 w-inline-block">
											<div><?php echo esc_html( $term_item->name ); ?></div>
										</a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
						<?php
					endif;
				else :
					$price_links = $args['price_links'];
					if ( ! empty( $price_links ) ) :
						?>
						<div id="w-node-_144563be-6001-1af8-6446-1240953da904-be61d3ef" class="m-h-vert">
							<div class="p-16-16">Стоимость</div>
							<div id="w-node-_144563be-6001-1af8-6446-1240953da907-be61d3ef" class="m-h-vert">
								<?php
								foreach ( $price_links as $price_links_item ) :
									$price_link       = get_term_link( $dress_category ) . '?';
									$price_link_title = '';

									if ( ! empty( $price_links_item['min_price'] ) ) {
										$price_link .= 'min-price=' . $price_links_item['min_price'];
									}

									if ( ! empty( $price_links_item['max_price'] ) ) {
										$price_link .= 'max-price=' . $price_links_item['max_price'];
									}

									if ( ! empty( $price_links_item['min_price'] ) && ! empty( $price_links_item['max_price'] ) ) {
										$price_link_title = loveforever_format_price( $price_links_item['min_price'], 0 ) . ' ₽ – ' . loveforever_format_price( $price_links_item['max_price'], 0 ) . ' ₽';
									} elseif ( ! empty( $price_links_item['min_price'] ) ) {
										$price_link_title = 'от ' . loveforever_format_price( $price_links_item['min_price'], 0 ) . ' ₽';
									} else {
										$price_link_title = 'до ' . loveforever_format_price( $price_links_item['max_price'], 0 ) . ' ₽';
									}

									?>
									<a href="<?php echo esc_url( $price_link ); ?>" class="a-12-12 w-inline-block">
										<div><?php echo esc_html( $price_link_title ); ?></div>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
						<?php
					endif;
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
