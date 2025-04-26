<?php
/**
 * Stories Modal Component
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="storiesDialog" role="dialog" class="dialog" data-js-dialog>
	<div class="dialog__overlay" data-js-dialog-overlay>
		<div class="dialog__content" data-js-dialog-content>
			<div class="stories">
				<ol class="stories__list">
					<li class="stories__item story"></li>
					<li class="stories__item story"></li>
					<li class="stories__item story"></li>
					<li class="stories__item story"></li>
					<li class="stories__item story"></li>
				</ol>
			</div>
		</div>
	</div>
	<button type="button" class="dialog__close" data-js-dialog-close-button>
		<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
			<mask id="mask0_451_2489" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="18" height="18">
				<rect width="18" height="18" fill="#D9D9D9"/>
			</mask>
			<g mask="url(#mask0_451_2489)">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M8.84924 8.14201L1.77818 1.07095L1.07107 1.77805L8.14214 8.84912L1.07107 15.9202L1.77817 16.6273L8.84924 9.55623L15.9203 16.6273L16.6274 15.9202L9.55635 8.84912L16.6274 1.77805L15.9203 1.07095L8.84924 8.14201Z" fill="black"/>
			</g>
		</svg>
	</button>
</div>