<?php get_header(); ?>

	<?php while( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?> id="location-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<div class="wysiwyg">
				<?php the_content(); ?>
			</div>
			<div id="gmap" data-gmapSingleLocation="<?php the_ID(); ?>" data-maxZoom="18" data-minZoom="1"></div>
			<footer>
				<ul>
					<li class="item prev">
						<?php if( get_previous_post() ): $prev = get_previous_post(); ?>
							<a title="Previous post" href="<?php echo get_permalink( $prev->ID ); ?>"><?php echo $prev->post_title; ?></a>
						<?php endif; ?>
					</li>
					<li class="item next">
						<?php if( get_next_post() ): $next = get_next_post(); ?>
						<a title="Next post" href="<?php echo get_permalink( $next->ID ); ?>"><?php echo $next->post_title; ?></a>
						<?php endif; ?>
					</li>
					<li>
						<a title="All posts" href="<?php echo get_post_type_archive_link( 'locations_module' ); ?>">Back to All</a>
					</li>
				</ul>
			</footer>
		</article>
	<?php endwhile; ?>

<?php get_footer();