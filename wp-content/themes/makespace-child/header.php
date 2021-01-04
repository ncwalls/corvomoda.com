<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<a class="visually-hidden skip-link" href="#MainContent">Skip to content</a>
	<a href="javascript:void(0)" class="nav-toggle" id="ocn-overlay"></a>
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
	</div>
	<header class="site-header">
		<div class="inner container">
			<div class="site-header__logo">
				<a href="<?php echo home_url(); ?>" title="<?php bloginfo( 'name' ); ?>" class="brand">
					<img src="<?php the_field( 'default_logo', 'option' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
				</a>
			</div>
			<div class="site-header__menu">
				<?php
					wp_nav_menu( array(
						'container' => 'nav',
						'container_id' => 'large-nav-primary',
						'theme_location' => 'primary'
					) );
				?>
				<button name="Mobile navigation toggle" aria-pressed="false" class="nav-toggle" type="button" id="nav-toggle" aria-labelledby="nav-toggle-label">
					<span class="screen-reader-text" id="nav-toggle-label">Open off canvas navigation</span>
					<span class="middle-bar"></span>
				</button>
			</div>
		</div>
	</header>

	<div id="MainContent" class="wrapper">

		<?php if( !is_front_page() ) : ?>
			<?php get_template_part( 'template', 'header' ); ?>
		<?php endif; ?>