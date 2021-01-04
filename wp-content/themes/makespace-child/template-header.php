<?php
	$header_image = get_field( 'default_header_image', 'option' );
	if( is_singular() && get_field( 'header_image' ) ){
		$header_image = get_field( 'header_image' );
	} else {
		$slug = get_post_type();
		if( 'post' != $slug && get_field( $slug . '_header_image', 'option' ) ){
			$header_image = get_field( $slug . '_header_image', 'option' );
		} elseif( 'post' == $slug && get_field( 'header_image', get_option( 'page_for_posts' ) ) ){
			$header_image = get_field( 'header_image', get_option( 'page_for_posts' ) );
		}
	}

	if(is_array( $header_image )){
		$header_image = $header_image['url'];
	}
?>
<aside class="msw-page-header" style="background-image: url(<?php echo $header_image; ?>);">
	<?php do_action( 'msw_page_header_content' ); ?>
</aside>
<?php
	if( function_exists( 'yoast_breadcrumb' ) ){
		yoast_breadcrumb( '<div id="breadcrumbs"><div class="container">', '</div></div>' );
	}
?>