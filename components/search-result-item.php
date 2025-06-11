<?php
/**
 * Search Result Item component
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;

$price = get_field( 'price' );
?>
<a href="<?php the_permalink(); ?>" class="lf-search-result" data-js-search-result>
	<span class="lf-search-result__title"><?php the_title(); ?></span>
	<span class="lf-search-result__image-wrapper">
		<span class="lf-search-result__image-preview" data-js-search-result-image-preview>
			<?php
			the_post_thumbnail(
				'medium',
				array(
					'class' => 'lf-search-result__image',
				)
			);
			?>
		</span>
	</span>
	<span class="lf-search-result__price"><?php echo esc_html( loveforever_format_price( $price, 0 ) ); ?></span>
</a>