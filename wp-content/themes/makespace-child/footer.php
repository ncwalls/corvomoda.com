		</div><!-- /.wrapper -->
		<footer class="site-footer">
			<img src="<?php the_field( 'default_logo', 'option' ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="footer-logo">
			<div class="inner">
				<div class="copyright">
					<ul>
						<li>&copy;<?php echo date('Y'); ?> Corvomoda</li>
						<li><a href="<?php echo home_url( 'privacy-policy' ); ?>">Privacy Policy</a></li>
						<li><a href="<?php echo home_url( 'contact' ); ?>">Contact Us</a></li>
					</ul>
				</div>
				<div class="out-links">
					<?php if(have_rows('social_links', 'option')): ?>
						<ul class="social-links">
						<?php while(have_rows('social_links', 'option')): the_row(); ?>
							<li><a href="<?php the_sub_field('url'); ?>" class="<?php the_sub_field('class'); ?>" title="Corvomoda on <?php the_sub_field('site'); ?>" target="_blank"></a></li>
						<?php endwhile; ?>
						</ul>
					<?php endif; ?>
					<div class="bumblephant">
						<a href="https://www.bumblephantdesign.com/" target="_blank">
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/bumblephant.png" alt="Bumblephant Design" class="static"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/bumblephant-color.png" alt="Bumblephant Design" class="hover">
						</a>
					</div>
				</div>
			</div>
		</footer>
		<?php wp_footer(); ?>
	</body>
</html>
