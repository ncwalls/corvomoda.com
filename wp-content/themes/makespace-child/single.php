<?php get_header(); ?>

	<div class="container">
		<?php while( have_posts() ): the_post(); ?>
			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<h1><?php the_title(); ?></h1>
				<ul class="post-meta">
					<li><?php the_time( 'F j, Y' ); ?></li>
					<li><?php read_time(); ?></li>
				</ul>
				<div class="wysiwyg">
					<?php the_content(); ?>
				</div>
				<div class="single-share">
					<div class="inner">
						<?php echo do_shortcode('[addtoany]'); ?>
					</div>
				</div>
				<footer class="single-pagination">
					<ul>
						<li class="item prev">
							<?php if( get_previous_post() ): $prev = get_previous_post(); ?>
								<a title="Previous" href="<?php echo get_permalink( $prev->ID ); ?>">
									<i class="fas fa-angle-left"></i> Previous
								</a>
							<?php endif; ?>
						</li>
						<li class="item all">
							<a title="All posts" href="<?php echo get_permalink(get_option('page_for_posts')); ?>">Back to All</a>
						</li>
						<li class="item next">
							<?php if( get_next_post() ): $next = get_next_post(); ?>
								<a title="Next" href="<?php echo get_permalink( $next->ID ); ?>">
									Next <i class="fas fa-angle-right"></i>
								</a>
							<?php endif; ?>
						</li>
					</ul>
				</footer>
			</article>
		<?php endwhile; ?>
	</div>

<?php get_footer();