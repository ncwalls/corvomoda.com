<?php get_header(); ?>
	
	<?php while( have_posts() ): the_post(); ?>
	
		<?php
		global $post;
		$args = array ( 'parent' => $post->ID );
		$children = get_pages( $args );
		$noSidebar = "container__main--no-sidebar";
		if ( is_page() && $post->post_parent ) :
			$noSidebar = "";
		elseif ( is_page() && count( $children ) > 0 ) :
			$noSidebar = "";
		endif;
		?>

		<article class="container" id="post-<?php the_ID(); ?>">
			
			<div class="container__main <?php echo $noSidebar; ?>">

				<div <?php post_class(); ?>>

					<h1><?php the_title(); ?></h1>

					<div class="wysiwyg">
						<?php the_content(); ?>
					</div>

				</div>

			</div>

			<?php include(locate_template('template-sidebar.php')); ?>

		</article>

	<?php endwhile; ?>

<?php get_footer();