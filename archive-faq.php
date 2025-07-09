<?php
/**
 * Template name: Вопрос-ответ
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

get_header(
	null,
	array(
		'data-wf-page'                  => '6724cba824e8ec73aed76856',
		'barba-container-extra-classes' => array( 'white-top' ),
		'barba-namespace'               => 'archive-faq',
	)
);

$infoline_id   = loveforever_get_current_infoline();
$infoline_data = loveforever_get_infoline_data( $infoline_id );
?>
				<section class="section">
					<div class="container container-fw n-top">
						<?php get_template_part( 'components/marquee', null, $infoline_data ); ?>
						<?php get_template_part( 'components/navbar' ); ?>
						<div class="page-top">
							<?php get_template_part( 'components/breadcrumb' ); ?>
							<h1 class="p-86-96">Вопрос-ответ</h1>
						</div>
					</div>
				</section>
				<section class="section">
					<div class="container n-top">
						<?php
						if ( have_posts() ) :
							$faq_categories = get_terms(
								array(
									'taxonomy'   => 'faq_category',
									'hide_empty' => false, // TODO: set to true!
								)
							);
							?>
							<div data-current="Tab 1" data-easing="ease" data-duration-in="300" data-duration-out="100" class="w-tabs">
								<div class="tabs-menu w-tab-menu">
									<a data-w-tab="Tab 1" class="faq-btn w-inline-block w-tab-link w--current">
										<div class="p-12-12 uper m-12-12">все</div>
									</a>
									<?php
									foreach ( $faq_categories as $faq_category ) :
										?>
										<a data-w-tab="<?php echo esc_attr( 'Tab ' . $faq_category->term_id ); ?>" class="faq-btn w-inline-block w-tab-link">
											<div class="p-12-12 uper m-12-12"><?php echo esc_html( $faq_category->name ); ?></div>
										</a>
									<?php endforeach; ?>
								</div>
								<div class="w-tab-content">
									<div data-w-tab="Tab 1" class="w-tab-pane w--tab-active">
										<div class="faq-block n-top">
											<?php
											while ( have_posts() ) :
												the_post();
												?>
												<?php get_template_part( 'components/faq-item' ); ?>
											<?php endwhile; ?>
										</div>
									</div>
									<?php foreach ( $faq_categories as $faq_category ) : ?>
										<div data-w-tab="<?php echo esc_attr( 'Tab ' . $faq_category->term_id ); ?>" class="w-tab-pane">
											<div class="faq-block n-top">
												<?php
												$faq_query_args = array(
													'post_type' => 'faq',
													'tax_query' => array(
														array(
															'taxonomy' => $faq_category->taxonomy,
															'field' => 'term_id',
															'terms' => array( $faq_category->term_id ),
														),
													),
												);
												$faq_query      = new WP_Query( $faq_query_args );
												if ( $faq_query->have_posts() ) :
													while ( $faq_query->have_posts() ) :
														$faq_query->the_post();
														?>
														<?php get_template_part( 'components/faq-item' ); ?>
														<?php
													endwhile;
													wp_reset_postdata();
													?>
												<?php else : ?>
													<div class="empty-content">
														<?php echo wp_kses_post( "<p>Вопросы в категории $faq_category->name еще не добавлены</p>" ); ?> 
													</div>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php else : ?>
							<div class="empty-content">
								<p>Вопросы еще не добавлены</p>
							</div>
						<?php endif; ?>
					</div>
				</section>
			</div>
		</div>
		<?php get_template_part( 'components/footer' ); ?>
<?php get_footer(); ?>
