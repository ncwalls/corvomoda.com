<?php get_header(); ?>

	<?php while( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?> id="staff-<?php the_ID(); ?>">
			<div class="msw-staff-content">
				<h1><?php the_title(); ?></h1>
				<h3 class="msw-staff-position"><?php the_field('title'); ?></h3>
				<?php
					$staff_image = '';
					if( get_field('primary_photo') ):
						$staff_image = get_field('primary_photo')['sizes']['medium'];

				?>
					<figure class="msw-staff-featured-image">
						<div class="img" style="background-image: url(<?php echo $staff_image; ?>)"></div>
					</figure>
				<?php endif; ?>
				<div class="wysiwyg">
					<?php the_content(); ?>
				</div>
				<?php if(have_rows('social_media')): ?>
					<div class="msw-staff-social">
						<?php while(have_rows('social_media')): the_row(); ?>
						<a href="<?php the_sub_field('link'); ?>" target="_blank" title="<?php the_sub_field('site_name'); ?>"><i class="<?php the_sub_field('css_class'); ?>"></i></a>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>
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
						<a title="All" href="<?php echo get_post_type_archive_link( 'staff_module' ); ?>">All</a>
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

<?php get_footer();