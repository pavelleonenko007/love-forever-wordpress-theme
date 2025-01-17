<?php
/**
 * Blog Card
 *
 * @package 0.0.1
 */

defined( 'ABSPATH' ) || exit;

global $post;
?>
<div id="w-node-_52717106-e811-81f7-0814-757adac5dfcc-dac5dfcc" class="blog-item">
	<a href="<?php the_permalink(); ?>" class="blog-link w-inline-block">
		<div class="blog-link_abs-mom">
			<div class="mom-abs">
				<?php if ( has_post_thumbnail() ) : ?>
					<img class="img-cover" src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" alt loading="lazy">
				<?php endif; ?>
			</div>
		</div>
		<div class="p-21-26 w500"><?php the_title(); ?></div>
		<?php if ( ! empty( get_field( 'description' ) ) ) : ?>
			<div class="p-16-20 m-14-18"><?php echo wp_kses_post( get_field( 'description' ) ); ?></div>
		<?php endif; ?>
		<div class="blog-horiz">
			<div class="p-12-12 uper"><?php echo esc_html( get_the_date( 'd.m.y' ) ); ?></div>
			<?php
			$post_tags = get_the_terms( $post->ID, 'post_tag' );

			if ( ! empty( $post_tags ) ) :
				?>
				<div class="_2px_romb purp"></div>
				<div class="blog-tags">
					<?php
					foreach ( $post_tags as $post_tag_index => $post_tag ) :
						if ( 0 === $post_tag_index ) :
							?>
							<div class="blog-tags__item">
								<div class="p-12-12 uper"><?php echo esc_html( $post_tag->name ); ?></div>
							</div>
						<?php else : ?>
							<div class="blog-tags__item">
								<div class="_2px_romb purp"></div>
								<div class="p-12-12 uper"><?php echo esc_html( $post_tag->name ); ?></div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</a>
</div>
