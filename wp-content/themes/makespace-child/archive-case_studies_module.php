<?php get_header(); ?>
	
	<h1><?php echo get_post_type_object('case_studies_module')->labels->name; ?></h1>

	<div class="msw-case-studies-filter">
		<div class="filter-container filter-container-services">
			<div class="filter-label">Filter By Industry</div>
			<div class="filter-dropdown">
				<div class="filter-display">
					<?php
						if( single_term_title( '', false ) ){
							single_term_title();
						} else {
							echo 'Filter By Industry';
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
							'taxonomy' => 'case_studies_module_industry'
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

	<ol class="msw-case-studies-module-list">
		<?php while( have_posts() ) : the_post(); ?>
			<li class="msw-case-studies-module-item">
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="msw-case-studies-module-permalink">
					<?php
						$case_image = '';
						if( get_field('header_image') ):
							$case_image = get_field('header_image')['sizes']['medium'];
					?>
						<figure class="msw-case-thumbnail">
							<div class="img" style="background-image: url(<?php echo $case_image; ?>)"></div>
						</figure>
					<?php endif; ?>
					<h2 class="msw-case-studies-module-name"><?php the_title(); ?></h2>
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