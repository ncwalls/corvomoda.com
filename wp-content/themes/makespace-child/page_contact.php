<?php
/*
 * Template Name: Contact
 */
get_header(); ?>
	<?php while( have_posts() ): the_post(); ?>
		<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<div class="container">
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
				<div class="contact-info">
					<div class="col">
						<h3>Email:</h3>
						<?php
							$email = get_field('email', 'option');
						?>
						<p><a href="<?php echo MakespaceChild::obfuscate_email( 'mailto:' . $email ); ?>"><?php echo MakespaceChild::obfuscate_email( $email ); ?></a></p>
					</div>
					<div class="col">
						<?php if(have_rows('social_links', 'option')): ?>
							<h3>Social Media:</h3>
							<ul class="social-links">
								<?php while(have_rows('social_links', 'option')): the_row(); ?>
									<li><a href="<?php the_sub_field('url'); ?>"  title="Bumblephant on <?php the_sub_field('site'); ?>" target="_blank"><span class="<?php the_sub_field('class'); ?>"></span><?php //the_sub_field('site'); ?></a></li>
								<?php endwhile; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
				
				<?php echo do_shortcode( '[gravityform id="1" title="false" description="false" ajax="true"]' ); ?>
			</div>
		</article>
	<?php endwhile; ?>

<?php get_footer();
