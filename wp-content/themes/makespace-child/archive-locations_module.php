<?php get_header(); ?>
	
	<h1><?php echo get_post_type_object('locations_module')->labels->name; ?></h1>

	<ol class="msw-locations-module-list">
		<?php while( have_posts() ) : the_post(); ?>
			<li class="msw-locations-module-item">
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="msw-locations-module-permalink">
					<h2 class="msw-locations-module-name"><?php the_title(); ?></h2>
					<div class="msw-locations-module-hentry"><?php the_content(); ?></div>
				</a>
			</li>
		<?php endwhile; ?>
	</ol>

	<?php
		$translated = __( 'Page ', 'mytextdomain' );
		echo paginate_links( array(
			'prev_text' => '<i class="fa fa-angle-left"><span class="screen-reader-text">Previous Page</span></i>',
			'next_text' => '<i class="fa fa-angle-right"><span class="screen-reader-text">Next Page</span></i>',
			'type' => 'plain',
			'before_page_number' => '<span class="screen-reader-text">' . $translated . '</span>'
		) );
	?>

<?php get_footer();