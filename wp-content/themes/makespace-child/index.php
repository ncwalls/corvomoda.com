<?php get_header(); ?>

	<div class="container">
		<div class="filter-container">
			<div class="filter-label">Filter By</div>
			<div class="filter-dropdown">
				<div class="filter-display">
					<?php
						if( single_term_title( '', false ) ){
							single_term_title();
						} else {
							echo 'Filter By';
						}
					?>
				</div>
				<ul>
					<?php $post_type_name = get_post_type_object( get_post_type( get_the_ID() ) )->labels->name;  ?>
					<li><a title="View All <?php echo $post_type_name; ?>" href="<?php echo get_post_type_archive_link( get_post_type( get_the_ID() ) ); ?>">All</a></li>
					<?php
						$categories = get_categories( array(
							'orderby' => 'name',
							'order'   => 'ASC'
						) );

						foreach( $categories as $category ) {
							$caturl = get_category_link( $category->term_id );
							$catname = $category->name;
							$accessibility_title = $catname . ' ' . $post_type_name;
							echo '<li><a title="' . $accessibility_title . '" href="' . $caturl .'">' . $catname. '</a></li>';
						}
					?>
				</ul>
			</div>
		</div>
		
		<?php
		/*
			.archive-list has 3 variation classes:
			
			.full-width
				Each article is 100% width.
				Image and content are stacked, also 100% width.
			
			.half-col
				Each article is 50% width.
				Image and content are stacked, 100% of the article width.
			
			.alternating-cols
				All articles are 100% width.
				Image is 25% width, content is 75%.
				Odd articles have image on left, content on right, text aligned left.
				Even articles have image on right, content on left, text aligned right.
				
		*/
		?>
		<div class="archive-list half-cols">
			<?php while( have_posts() ): the_post(); ?>
				<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<?php
						$thumb_image = '';
						if( get_the_post_thumbnail_url() ):
							$thumb_image = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
						
					?>
						<figure class="post-thumbnail">
							<a title="<?php the_title(); ?>" href="" class="img" style="background-image: url(<?php echo $thumb_image; ?>)"></a>
						</figure>
					<?php endif; ?>
					<div class="content">
						<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
						<ul class="post-meta">
							<li><?php the_time( 'F j, Y' ); ?></li>
							<li><?php read_time(); ?></li>
						</ul>
						<?php the_excerpt(); ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
		
		<footer class="archive-pagination">
			<?php
				$translated = __( 'Page ', 'mytextdomain' );
				echo paginate_links( array(
					'prev_text' => '<i class="fa fa-angle-left"><span class="screen-reader-text">Previous Page</span></i>',
					'next_text' => '<i class="fa fa-angle-right"><span class="screen-reader-text">Next Page</span></i>',
					'type' => 'plain',
					'before_page_number' => '<span class="screen-reader-text">' . $translated . '</span>'
				) );
			?>
		</footer>
	</div>

<?php get_footer();