<?php get_header(); ?>

	<?php while( have_posts() ): the_post(); ?>
		<div class="hero">
			<img class="hero-logo" src="<?php echo get_field('hero_logo')['url']; ?>" alt="Corvomoda">
		</div>
		<article <?php post_class('main'); ?> id="post-<?php the_ID(); ?>">
			<div class="container">
				<div class="woocommerce">
					<?php
					$products = get_posts(array(
						'post_type' => 'product',
						'posts_per_page' => -1,
						'orderby' => 'menu_order',
						'order' => 'ASC'
					));
					// print_r($products );
					?>
					<ul class="products">
						<?php foreach ($products as $p): ?>
							<li class="product">
								<a href="<?php echo get_permalink($p->ID); ?>">
									<figure>
										<img src="<?php echo get_the_post_thumbnail_url( $p->ID, 'medium' ); ?>" alt="">
									</figure>
									<span class="woocommerce-loop-product__title">
										<?php echo get_the_title($p->ID); ?>
									</span>
									<?php 
										$woo_product = wc_get_product( $p->ID );
										if ($woo_product->product_type == 'variable') {
											if($woo_product->get_variation_attributes()){
												echo '<ul class="colors">';
												$product_attr = $woo_product->get_variation_attributes();
												$product_colors = $product_attr['Colors'];
												foreach ($product_colors as $c) {
													$c_class = preg_replace('/\s+/', '_', strtolower($c));
													echo '<li class="' . $c_class . '">' . $c . '</li>';
												}
												echo '</ul>';
											}
										}
									?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</article>
	<?php endwhile; ?>
<?php get_footer();