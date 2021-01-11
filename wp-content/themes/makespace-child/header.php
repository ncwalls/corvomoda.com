<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div id="ocn-overlay"></div>
	<a class="visually-hidden skip-link" href="#MainContent">Skip to content</a>
	<div id="ocn">
		<div id="ocn-inner">
			<div id="ocn-top">
				<a href="<?php echo home_url(); ?>" title="<?php bloginfo( 'name' ); ?>" id="ocn-brand">
					<img src="<?php the_field( 'default_logo', 'option' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
				</a>
				<button name="Mobile navigation toggle" aria-pressed="false" class="nav-toggle" type="button" id="ocn-close" aria-labelledby="ocn-toggle-label">
					<span class="screen-reader-text" id="ocn-toggle-label">Close off canvas navigation</span>
				</button>
			</div>
			<?php wp_nav_menu( array(
				'container' => 'nav',
				'container_id' => 'ocn-nav-primary',
				'theme_location' => 'primary',
				'before' => '<span class="ocn-link-wrap">',
				'after' => '<button aria-pressed="false" name="Menu item dropdown toggle" class="ocn-sub-menu-button"></button></span>'
			) ); ?>
		</div>
		<div class="ocn-bottom"></div>
	</div>
	<header class="site-header">
		<div class="inner">
			<a href="<?php echo home_url(); ?>" title="<?php bloginfo( 'name' ); ?>" class="brand">
				<img src="<?php the_field( 'default_logo', 'option' ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="brand-static">
			</a>
			<?php /*
			<div>
				<?php
					wp_nav_menu( array(
						'container' => 'nav',
						'container_id' => 'large-nav-primary',
						'theme_location' => 'primary'
					) );
				?>
				<?php global $woocommerce; ?>
				<a class="header-cart" href="<?php echo wc_get_cart_url(); ?>">
					<span class="cart-count"><?php echo $woocommerce->cart->cart_contents_count; ?></span>
					<i class="far fa-shopping-cart"></i>
				</a>
			</div>
			<?php /*while(have_rows('contact_information', 'option')): the_row(); ?>
				<ul class="social-links">
				<?php while(have_rows('social_media_links')): the_row(); ?>
					<li><a href="<?php the_sub_field('url'); ?>" class="fa <?php the_sub_field('class'); ?>" title="Bumblephant on <?php the_sub_field('site'); ?>" target="_blank"></a></li>
				<?php endwhile; ?>
				</ul>
			<?php endwhile; ?>*/ ?>
			<button class="nav-toggle" type="button" id="nav-toggle">
				<span class="screen-reader-text">menu</span>
			</button>
		</div>
		<?php
			/*if( 'dropdown' == get_field( 'menu_type', 'option' ) ){
				wp_nav_menu( array(
					'container' => 'nav',
					'container_id' => 'dropdown-nav-primary',
					'theme_location' => 'primary'
				) );
			}*/
		?>
	</header>

	<div id="MainContent" class="wrapper">

		<?php if( !is_front_page() ) : ?>
			<?php //get_template_part( 'template', 'header' ); ?>
		<?php endif; ?>