<?php
/**
 * Three Cards Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;
$big_card   = $args['big_card'];
$left_card  = $args['left_card'];
$right_card = $args['right_card'];
?>
<section class="section">
	<div class="container">
		<div class="bigcards-grid">
			<?php
			get_template_part(
				'components/category-card',
				null,
				array(
					'card' => $big_card,
					'size' => 'big',
					'side' => 'top',
				)
			);
			?>
			<?php
			get_template_part(
				'components/category-card',
				null,
				array(
					'card' => $left_card,
					'side' => 'left',
				)
			);
			?>
			<?php
			get_template_part(
				'components/category-card',
				null,
				array(
					'card' => $right_card,
					'side' => 'right',
				)
			);
			?>
		</div>
	</div>
</section>
