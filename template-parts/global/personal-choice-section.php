<?php
/**
 * Personal Choice Section
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

$text_section = get_field( 'text_section', 'option' );
?>
<section class="section">
	<div class="container">
		<?php if ( ! empty( $text_section['heading'] ) ) : ?>
			<h2 class="h-64-64 uper _w-ligi"><?php echo wp_kses_post( $text_section['heading'] ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $text_section['description'] ) ) : ?>
			<p class="p-20-26 individ"><?php echo wp_kses_post( $text_section['description'] ); ?></p>
		<?php endif; ?>
		<div class="bg-logo">
			<div class="code-embed w-embed">
				<svg width="100%" height="100%" viewbox="0 0 333 458" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path opacity="0.2" d="M252.164 174.533L251.52 175.195C248.008 178.813 245.237 182.947 243.768 187.769L243.767 187.77C242.75 191.083 242.41 194.529 242.269 198.047C242.212 199.465 242.188 200.909 242.164 202.36C242.128 204.474 242.093 206.604 241.956 208.691C241.185 220.578 236.896 232.138 230.025 241.835C226.556 246.741 222.413 251.167 217.723 254.935C212.266 259.32 206.684 261.57 200.115 263.708L200.115 263.709C184.301 268.844 167.864 272.098 151.283 273.358L150.661 273.405L150.75 272.788C155.803 237.808 162.568 202.448 178.543 170.835C188.812 150.503 203.994 131.086 225.474 123.065C238.178 118.318 252.063 117.958 265.503 118.802C274.171 119.344 282.94 120.398 291.21 123.22M252.164 174.533L291.049 123.693M252.164 174.533C257.017 169.759 263.149 165.933 269.215 162.67C270.535 161.961 271.872 161.257 273.216 160.55C282.911 155.45 292.945 150.172 299.374 141.265L299.375 141.263C300.821 139.243 302.177 136.842 302.178 134.212M252.164 174.533L302.178 134.212M291.21 123.22L291.049 123.693M291.21 123.22C291.21 123.22 291.21 123.219 291.21 123.219L291.049 123.693M291.21 123.22C293.772 124.091 296.497 125.414 298.59 127.218C300.686 129.025 302.186 131.352 302.178 134.212M291.049 123.693C296.1 125.411 301.694 128.88 301.678 134.21H302.178C302.178 134.211 302.178 134.211 302.178 134.212M290.607 158.243L290.609 158.254C282.767 161.114 273.92 164.743 268.097 171.186L268.096 171.187C263.531 176.25 263.888 182.344 266.858 187.108C269.815 191.851 275.388 195.333 281.365 195.268L281.367 195.268C283.096 195.243 284.808 195.033 286.489 194.816L286.779 194.779C288.369 194.573 289.931 194.372 291.498 194.313L291.499 194.313C297.695 194.061 304.906 195.97 308.927 200.784C310.151 202.254 310.769 203.722 310.948 205.149C311.127 206.579 310.87 208.001 310.295 209.381C309.14 212.154 306.719 214.715 304.129 216.711C292.507 225.642 277.869 227.707 264.504 221.491C258.053 218.474 252.54 213.671 249.058 207.783L248.325 206.543L248.132 207.971L238.502 279.456H150.369H149.927L149.873 279.894C147.509 298.874 146.163 316.191 145.131 329.479C144.972 331.518 144.821 333.462 144.675 335.302C140.147 392.179 136.766 418.077 110.348 441.566C84.8773 462.36 59.0812 460.893 41.5502 448.096C23.9915 435.278 14.646 411.046 22.2109 386.129C25.9376 375.821 32.4237 367.86 39.3951 362.705C46.3846 357.537 53.8005 355.23 59.3793 356.119C62.156 356.561 64.4669 357.79 66.0751 359.841C67.6859 361.895 68.6306 364.823 68.5763 368.731C68.4676 376.567 64.3407 388.215 53.7959 404.168L53.7799 404.193L53.7667 404.219C50.5586 410.573 48.7704 416.438 48.1909 421.446C47.6124 426.447 48.2349 430.633 49.899 433.59C51.5776 436.572 54.3049 438.275 57.8059 438.271C61.2718 438.268 65.435 436.592 70.0579 432.985C88.525 418.59 96.0395 395.032 101.124 369.074L101.124 369.074C102.175 363.691 103.115 353.347 104.341 339.866C104.47 338.442 104.603 336.983 104.739 335.491C106.167 319.855 107.999 300.568 110.77 280.022L110.847 279.456H110.275H0.5V276.716C9.50093 276.693 16.8162 274.296 22.3814 269.499C27.999 264.656 30.7777 257.661 30.7777 248.595V33.6042C30.7777 24.5362 27.9989 17.1775 22.4082 11.5954L22.4077 11.5949C16.8291 6.04105 9.52048 3.26638 0.5 3.2393V0.5H118.746V3.2391H117.748C108.667 3.2391 101.297 6.01342 95.7068 11.5954L95.7063 11.5959C90.1164 17.1934 87.3373 24.5357 87.3373 33.6042V250.09C87.3373 258.122 89.2843 264.431 93.2646 268.896C96.8353 272.923 102.764 275.074 110.917 275.507L111.375 275.531L111.439 275.077C114.824 251.143 119.512 225.792 126.337 202.7C133.163 179.603 142.115 158.805 154.006 143.947C181.088 111.821 219.459 103.645 253.088 104.449C286.724 105.253 315.523 115.04 323.312 118.719L323.525 118.267L323.312 118.719C327.195 120.552 330.068 122.835 331.491 125.45C332.895 128.028 332.928 130.997 330.993 134.344C329.041 137.719 325.087 141.47 318.522 145.499C311.965 149.522 302.843 153.799 290.607 158.243Z" stroke="#F22EA9"></path>
				</svg>
			</div>
		</div>
	</div>
</section>
