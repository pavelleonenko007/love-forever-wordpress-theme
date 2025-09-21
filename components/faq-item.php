<?php
/**
 * Faq Item
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post; ?>
<div class="faq-item">
	<div class="faq-top">
		<div class="p-20-20 w500"><?php the_title(); ?></div>
		<div class="faq-svg w-embed">
			<svg width="10" height="6" viewbox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 10rem; height: 6rem;">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M4.28598 5.24977L0 0.74993L0.714289 0L5.00027 4.49984L9.28571 0.000560648L10 0.750491L4.99998 6L4.28569 5.25007L4.28598 5.24977Z" fill="black"></path>
			</svg>
		</div>
	</div>
	<div class="faq-bottom">
		<div class="faq-content">
			<div class="rich-faq flow w-richtext">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
</div>
