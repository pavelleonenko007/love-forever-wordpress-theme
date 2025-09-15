<?php
/*
Template name: 404-page
*/

get_header(
	null,
	array(
		'data-wf-page'                  => '6720c60d7d5faf3e7ea1ac8d',
		'barba-container-extra-classes' => array(
			'home-page',
		),
		'namespace'                     => 'home',
	)
); ?>
	<div id="barba-wrapper" class="wrapper">
		<div class="barba-container white-top">
			<section class="section _404-page">
				<?php get_template_part( 'components/navbar' ); ?>
				<div class="container container-fw _404-page-container">
					<div class="page-top-copy _404-page"><img src="<?php echo get_template_directory_uri(); ?>/images/672b7395d7c0f40bf0140252_Group20647.svg" loading="lazy" alt class="image-3">
						<h1 class="p-64-64 h404">404</h1>
						<div class="p-16-20 p404">к сожалению, страница не найдена</div>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn in-single-btn p-t-30 w-inline-block">
							<div>вернуться на главную</div>
						</a>
					</div>
				</div>
			</section>
		</div>
	</div>
	<?php get_template_part( 'components/icons' ); ?>
	<!-- FOOTER CODE -->
<?php wp_footer(); ?>
</body>
</html>
