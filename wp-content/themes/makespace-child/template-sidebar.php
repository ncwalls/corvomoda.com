<?php
if ( is_page() && $post->post_parent ) : ?>

<!-- This is a child-page. -->
<aside class="sidebar">
	<div class="sidebar__wrapper">
		<h3 class="sidebar__title"><a href="<?php echo get_permalink($post->post_parent); ?>"><?php echo get_the_title($post->post_parent); ?></a></h3>
		<ul>
			<?php $args_sibings = array( 'title_li' => null, 'child_of' => $post->post_parent, 'depth' => 1 );
			wp_list_pages( $args_sibings ); ?>
		</ul>
	</div>
</aside>

<?php elseif ( is_page() && count( $children ) > 0 ) : ?>

<!-- This is a parent-page (with one or more children) -->
<aside class="sidebar">
	<div class="sidebar__wrapper">
		<h3 class="sidebar__title"><?php the_title(); ?></h3>
		<ul>
			<?php $args_children = array( 'title_li' => null, 'child_of' => get_the_ID(), 'depth' => 1 );
			wp_list_pages( $args_children ); ?>
		</ul>
	</div>
</aside>

<?php else : ?>

<!-- This is a parent page without children. -->

<?php endif; ?>