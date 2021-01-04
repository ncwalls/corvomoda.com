<?php

class MakespaceChild {

	function __construct(){
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'acf/init', array( $this, 'msw_acf_init' ) );
		add_action( 'wp_loaded', array( $this, 'msw_loaded' ) );
		add_action( 'init', array( $this, 'msw_ajax_atc') );

		add_filter( 'wpseo_breadcrumb_links', array( $this, 'add_cpt_archive_parent_breadcrumb' ), 10, 1);
	}

	function add_cpt_archive_parent_breadcrumb( $crumbs ){
		$archive_crumbs = array();
		$post_type;

		// Section for adding the parent one level from the end
		if ( is_post_type_archive() || is_tax() ) {
			if ( is_tax() ) {
				$tax_name = get_queried_object()->taxonomy;
				$module_end = strpos($tax_name, "module") + strlen( "module" );
				$post_type = substr($tax_name, 0, $module_end );
			} else {
				$post_type = get_queried_object()->name;
			}
			$field_name = $post_type . '_parent';
			$archive_parent = get_field( $field_name, 'option' )->ID;

			if( isset( $archive_parent ) ){
				array_push( $archive_crumbs, array('id' => $archive_parent), array_pop( $crumbs ) );
				$crumbs = array_merge( $crumbs, $archive_crumbs);
			}
		}

		// Section for adding the parent two levels from the end
		if ( is_singular() ) {
			$post_type = get_post_type();
			$field_name = $post_type . '_parent';
			$archive_parent = get_field( $field_name, 'option' )->ID;

			if( isset( $archive_parent ) ){
				array_push( $archive_crumbs, array_pop( $crumbs ), array_pop( $crumbs ), array('id' => $archive_parent) );
				$archive_crumbs = array_reverse( $archive_crumbs);
				$crumbs = array_merge( $crumbs, $archive_crumbs);
			}
		}
		return $crumbs;
	}

	function after_setup_theme(){
		// add_theme_support( 'case-studies-module' );
		add_theme_support( 'locations-module' );
		// add_theme_support( 'staff-module' );
	}

	function wp_enqueue_scripts(){
		$msw_object = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'home_url' => home_url(),
			'show_dashboard_link' => current_user_can( 'manage_options' ) ? 1 : 0,
			'site_url' => site_url(),
			'stylesheet_directory' => get_stylesheet_directory_uri(),
		);
		if ( get_theme_support( 'locations-module' ) ) {
		 	$msw_object['google_map_data'] = get_google_map_data();
		}

		if ( get_field( 'default_google_map_api_key', 'option' ) ) :
			$google_api_key = 'https://maps.googleapis.com/maps/api/js?key=' . get_field( 'default_google_map_api_key', 'option' );
			wp_enqueue_script('google-maps', $google_api_key, true);
		endif;

		wp_enqueue_script( 'theme', get_stylesheet_directory_uri() . '/scripts.min.js', array( 'jquery' ), filemtime( get_stylesheet_directory() . '/scripts.min.js' ) );
		wp_localize_script( 'theme', 'MSWObject', $msw_object );

		//wp_enqueue_style( 'google-fonts', '', [], null );
		wp_enqueue_style( 'theme', get_stylesheet_uri(), array(), filemtime( get_stylesheet_directory() . '/style.css' ) );
	}

	function msw_acf_init() {
		if ( get_field( 'default_google_map_api_key', 'option' ) ) :
			acf_update_setting('google_api_key', get_field( 'default_google_map_api_key', 'option' ));
		endif;
	}

	function msw_loaded() {
		// Custom Thumbnail Sizes
		add_theme_support( 'post-thumbnails' );
		// add_image_size( 'blog-image', 400, 300, true ); // Example
	}

	function msw_ajax_atc() {
		// Example use case for shop archive page
		/*remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'woocommerce_after_shop_loop_item', 'ms_ajax_shop', 10 );
		function ms_ajax_shop() {
			echo prepare_ajax_atc();
		}

		add_action( 'wp_ajax_ms_ajax_atc', 'ms_ajax_atc' );
		add_action( 'wp_ajax_nopriv_ms_ajax_atc', 'ms_ajax_atc' );
		function ms_ajax_atc() {
			do_ajax_atc( $_POST['woo_ajax_object'] );
		}*/
	}

	static function format_number_string( $input, $addcommas = false ){
		$num = preg_replace('/[^0-9]/', '', $input);
		if($addcommas == true){
			$numFormatted = number_format($num);
		}
		else{
			$numFormatted = $num;
			/*$numInt = intval($num);
			
			if($numInt >= 2147483647){ // http://php.net/manual/en/function.intval.php
				$numFormatted = $num;
			}
			else{
				$numFormatted = $numInt;
			}*/
		}
		return $numFormatted;
	}
	
	static function get_primary_location(){
		$locations = get_posts(array(
			'post_type' => 'locations_module',
			'meta_key' => 'primary_location',
			'meta_value' => '1'
		));

		return $locations[0] ?? null;
	}

	static function get_google_directions_url( $destination ){
		$url = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode( $destination );
		return $url;
	}

}

$MakespaceChild = new MakespaceChild();