<?php get_header(); ?>
	
	<h1><?php echo get_post_type_object('staff_module')->labels->name; ?></h1>

	<div class="msw-case-studies-filter">
		<div class="filter-container filter-container-services">
			<div class="filter-label">Filter By Department</div>
			<div class="filter-dropdown">
				<div class="filter-display">
					<?php
						if( single_term_title( '', false ) ){
							single_term_title();
						} else {
							echo 'Filter By Department';
						}
					?>
				</div>
				<ul>
					<?php $post_type_name = get_post_type_object( get_post_type( get_the_ID() ) )->labels->name;  ?>
					<li><a title="View All <?php echo $post_type_name; ?>" href="<?php echo get_post_type_archive_link( get_post_type( get_the_ID() ) ); ?>">All</a></li>
					<?php
						$categories = get_terms( array(
							'orderby' => 'name',
							'order'   => 'ASC',
							'taxonomy' => 'staff_module_department'
						) );
						foreach( $categories as $category ) {
							$caturl = get_term_link( $category->term_id );
							$catname = $category->name;
							$accessibility_title = $catname . ' ' . $post_type_name;
							echo '<li><a title="' . $accessibility_title . '" href="' . $caturl .'">' . $catname. '</a></li>';
						}
					?>
				</ul>
			</div>
		</div>
	</div>

	<ol class="msw-staff-module-list">
		<?php while( have_posts() ) : the_post(); ?>
			<li class="msw-staff-module-item">
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="msw-staff-module-permalink">
					<?php
						$staff_image = '';
						if( get_field('primary_photo') ):
							$staff_image = get_field('primary_photo')['sizes']['medium'];
							
							if(get_field('secondary_photo')){
								$staff_image_secondary = get_field('secondary_photo')['sizes']['medium'];
							}

					?>
					<figure class="msw-staff-module-thumbnail">
						<div class="img <?php echo get_field('secondary_photo') ? 'primary' : ''; ?>" style="background-image: url(<?php echo $staff_image; ?>)"></div>
						<?php if(get_field('secondary_photo')): ?>
							<div class="img secondary" style="background-image: url(<?php echo $staff_image_secondary; ?>)"></div>
						<?php endif; ?>
					</figure>
				<?php endif; ?>
					<h2 class="msw-staff-module-name"><?php the_title(); ?></h2>
					<h3 class="msw-staff-position"><?php the_field('title'); ?></h3>
				</a>
			</li>
		<?php endwhile; ?>
	</ol>

	
	<?php if(paginate_links()): ?>
		<footer class="archive-pagination">
			<div class="pagination-links">
				<?php
					$translated = __( 'Page ', 'mytextdomain' );
					echo paginate_links( array(
						'prev_text' => '<i class="fal fa-chevron-left"></i><span class="screen-reader-text">Previous Page</span></i>',
						'next_text' => '<i class="fal fa-chevron-right"></i><span class="screen-reader-text">Next Page</span></i>',
						'type' => 'plain',
						'before_page_number' => '<span class="screen-reader-text">' . $translated . '</span>'
					) );
				?>
			</div>
		</footer>
	<?php endif; ?>

<?php get_footer();