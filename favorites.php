<?php
/**
 * Template name: Избранное
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

get_header(
	null,
	array(
		'data-wf-page'                  => '672b5edfcfeb9652455dadc7',
		'barba-container-extra-classes' => array( 'white-top' ),
		'namespace'                     => 'favorite-products',
	)
);

$is_from_link = ! empty( $_GET['favorites'] );
$favorites    = '';

if ( $is_from_link ) {
	$favorites = sanitize_text_field( wp_unslash( $_GET['favorites'] ) );
} else {
	$favorites = ! empty( $_COOKIE['favorites'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['favorites'] ) ) : '';
}

$favorites_link = esc_attr( get_the_permalink() . '?favorites=' . $favorites );
?>
				<section class="section">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee' ); ?>
						<?php get_template_part( 'components/navbar' ); ?>
						<div class="page-top">
							<?php get_template_part( 'components/breadcrumb' ); ?>
							<h1 class="p-86-96"><?php the_title(); ?></h1>
							<?php if ( ! $is_from_link ) : ?>
								<div class="p-16-20 mmax480"><?php the_content(); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $favorites ) ) : ?>
								<button type="button" data-js-copy-button="<?php echo $favorites_link; ?>" class="btn pink-btn p-t-30 w-inline-block">
                <div 
                  data-js-copy-button-text 
                  data-js-copy-button-copied-text="Ссылка скопирована"
                >Поделиться избранным</div>
								</button>
							<?php endif; ?>
						</div>
					</div>
				</section>
				<section class="section">
					<div class="container n-top">
						<div class="catalog-grid search-page">
							<?php
							$products_query_args = array(
								'post_type'      => 'dress',
								'posts_per_page' => -1,
								'post__in'       => explode( ',', $favorites ),
							);

							$products_query = new WP_Query( $products_query_args );
							if ( $products_query->have_posts() ) :
								while ( $products_query->have_posts() ) :
									$products_query->the_post();
									?>
									<div id="w-node-cdc85bd6-5c09-bad4-48de-6e3feba43769-455dadc7" class="test-grid">
										<?php get_template_part( 'components/dress-card' ); ?>
									</div>
									<?php
								endwhile;
								wp_reset_postdata();
								?>
							<?php else : ?>
								<div class="empty-content">
									<p>В избранном пока нет товаров</p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</section>
				<?php if ( ! $is_from_link && ! empty( $favorites ) ) : ?>
					<section class="section">
						<div class="container">
							<div class="spleet">
								<h2 class="h-36-36">Сохранить список избранных платьев</h2>
							</div>
							<div class="bg-grad">
								<div class="pp-vert">
									<div class="p-12-12 uper white m-12-12">ссылка на избранное</div>
									<div class="form-block-2">
										<div class="form-2" data-wf-page-id="672b5edfcfeb9652455dadc7" data-wf-element-id="815950a7-30a8-0edf-28a1-0d81d780a3e3">
											<input 
												type="text"
												name="favorites_link" 
												class="input transp-input w-input" 
												maxlength="256" 
												placeholder="Ссылка"
												id="favoritesLinkField" 
												value="<?php echo $favorites_link; ?>" 
												readonly
											>
											<button type="button" class="btn send-white w-button" data-js-copy-button="<?php echo $favorites_link; ?>">Копировать</button>
										</div>
									</div>
								</div>
								<div class="div-block-2"></div>
								<div class="pp-vert">
									<div class="p-12-12 uper white m-12-12">Укажите телефон и мы пришлем вам список избранных платьев</div>
									<div class="form-block-2">
										<form id="favoritesContactForm" data-js-form class="form-2 favorites-contact-form" data-wf-page-id="672b5edfcfeb9652455dadc7" data-wf-element-id="8d5544fe-3d2a-0ad9-f4de-5a958bae3704" novalidate>
											<div class="favorites-contact-form__field field">
												<input 
													class="field__control" 
													maxlength="18" 
													name="name" 
													placeholder="+7 (000) 000-00-00" 
													type="text" 
													id="favoritesContactFormPhoneField" 
													aria-errormessage="favoritesContactFormPhoneFieldErrors"
													data-js-input-mask="phone"
													autocomplete="tel"
													inputmode="tel"
													required
												>
												<span class="field__errors" id="favoritesContactFormPhoneFieldErrors" data-js-form-field-errors></span>
											</div>
											<input type="hidden" name="action" value="submit_favorites_to_phone">
											<?php wp_nonce_field( 'submit_favorites_to_phone', '_submit_favorites_to_phone_nonce', false ); ?>
											<input type="submit" data-wait="Please wait..." class="btn send-white w-button" value="Oтправить">
										</form>
										<div class="w-form-done">
											<div>Thank you! Your submission has been received!</div>
										</div>
										<div class="w-form-fail">
											<div>Oops! Something went wrong while submitting the form.</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section>
				<?php endif; ?>
				<?php
				if ( ! $is_from_link ) :
					get_template_part( 'template-parts/home/recently-viewed-section' );
				endif;
				?>
				<?php
				if ( ! $is_from_link ) :
					$faq_section = get_field( 'faq_section' );
					if ( ! empty( $faq_section['faqs'] ) ) :
						?>
					<section class="section">
						<div class="container">
							<div class="spleet">
								<h2 class="h-36-36"><?php echo $faq_section['heading'] ? esc_html( $faq_section['heading'] ) : 'Что нужно знать перед примеркой?'; ?></h2>
							</div>
							<div class="faq-block">
								<?php
								foreach ( $faq_section['faqs'] as $post ) :
									setup_postdata( $post );
									?>
									<?php get_template_part( 'components/faq-item' ); ?>
									<?php
									endforeach;
								wp_reset_postdata();
								?>
							</div>
						</div>
					</section>
					<?php endif; ?>
					<?php
					get_template_part( 'template-parts/global/map-section' );
				endif;
				?>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
		<?php get_footer(); ?>
