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
			$archive_parent = get_field( $field_name, 'option' );

			if( $archive_parent ){
				$archive_parent_id = $archive_parent->ID;
				
				array_push( $archive_crumbs, array_pop( $crumbs ), array_pop( $crumbs ), array('id' => $archive_parent_id) );
				$archive_crumbs = array_reverse( $archive_crumbs);
				$crumbs = array_merge( $crumbs, $archive_crumbs);
			}
		}
		return $crumbs;
	}

	function after_setup_theme(){
		// add_theme_support( 'case-studies-module' );
		// add_theme_support( 'locations-module' );
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

		wp_enqueue_script( 'theme', get_stylesheet_directory_uri() . '/scripts.min.js', array( 'jquery' ) );
		wp_localize_script( 'theme', 'MSWObject', $msw_object );

		wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap', [], null );
		wp_enqueue_style( 'theme', get_stylesheet_uri(), array() );
	}

	function msw_acf_init() {
		if ( get_field( 'default_google_map_api_key', 'option' ) ) :
			acf_update_setting('google_api_key', get_field( 'default_google_map_api_key', 'option' ));
		endif;
	}

	function msw_loaded() {
		// add_theme_support( 'woocommerce', array(
		// 	'thumbnail_image_width' => 500
		// ) );
		
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

	static function obfuscate_email($email) {
		$output = '';

		for ($i = 0; $i < (strlen($email)); $i++) {
			$output .= '&#' . ord($email[$i]) . ';';
		}
		

		return $output;
	}

}

$MakespaceChild = new MakespaceChild();



/* woocom stuff */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10, 0);
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10, 0);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);


add_action('woocommerce_before_shop_loop_item_title', 'loop_img_wrap_open', 5);
add_action('woocommerce_before_shop_loop_item_title', 'loop_img_wrap_close', 15);
// add_action( 'woocommerce_before_single_product', 'product_back_link', 15);
add_action( 'woocommerce_before_single_product', 'woocommerce_template_single_title', 20);
// add_action( 'woocommerce_single_product_summary', 'before_product_options', 5);
add_action( 'woocommerce_product_after_tabs', 'share_product', 30);
// add_action( 'woocommerce_single_product_summary', 'after_cart_buttons', 55);
add_action('woocommerce_after_shop_loop_item_title', 'archive_color_list', 30 );
add_action('woocommerce_single_product_summary', 'starting_price', 5 );

add_filter('woocommerce_paypal_payments_single_product_renderer_hook', function() {
    return 'woocommerce_after_add_to_cart_button';
});

add_filter( 'woocommerce_get_image_size_thumbnail', function( $size ) {
	return array(
		'width'  => 500,
		'height' => 500,
		'crop'   => 1,
	);
} );

function loop_img_wrap_open(){
	echo '<figure>';
}
function loop_img_wrap_close(){
	echo '</figure>';
}

function before_product_options(){
	global $product;

	// echo '<h3>Pick a size and color.</h3>';
}

function archive_color_list(){
	global $product;

	if ($product->get_type() == 'variable') {
		if($product->get_variation_attributes()){
			echo '<ul class="colors">';
			$product_attr = $product->get_variation_attributes();
			$product_colors = $product_attr['Colors'];
			foreach ($product_colors as $c) {
				$c_class = preg_replace('/\s+/', '_', strtolower($c));
				echo '<li class="' . $c_class . '">' . $c . '</li>';
			}
			echo '</ul>';
		}
	}
}

function starting_price(){
	global $product;
	// if ( $price_html = $product->get_price_html() ) {
		echo '<div class="price">';
		// print_r($product);
		// echo $price_html;

	    if($product->get_available_variations()){
			$product_variations = $product->get_available_variations();

			#2 Get one variation id of a product
			$variation_product_id = $product_variations [0]['variation_id'];

			#3 Create the product object
			$variation_product = new WC_Product_Variation( $variation_product_id );

			#4 Use the variation product object to get the variation prices
		    if( $product->is_on_sale() ) {
				$price = wc_price( $variation_product->get_sale_price() );
			}
			else{
				$price = wc_price( $variation_product->get_regular_price() );
			}
			echo $price;
		}
		else{
			 echo $product->get_price_html();
		    // if( $product->is_on_sale() ) {
		    //     echo $product->sale_price;
		    // }
		    // else{
		    // 	echo $product->regular_price;
		    // }
		}


		echo '</div>';
	// }
}

add_filter( 'woocommerce_pagination_args', 	'rocket_woo_pagination' );
function rocket_woo_pagination( $args ) {

	$args['prev_text'] = '<i class="fal fa-angle-left"></i>';
	$args['next_text'] = '<i class="fal fa-angle-right"></i>';

	return $args;
}

/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );
function new_loop_shop_per_page( $number ) {
  // $cols contains the current number of products per page based on the value stored on Options –> Reading
  // Return the number of products you wanna show per page.
  $number = 50;
  return $number;
}


add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');        
function woocommerce_ajax_add_to_cart() {
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = absint($_POST['variation_id']);
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $product_status = get_post_status($product_id);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX :: get_refreshed_fragments();
    } else {

        $data = array(
            'error' => true,
            'product_id' =>  $product_id,
            'variation_id' =>  $variation_id,
            'post' => $_POST,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($variation_id), $variation_id));

        echo wp_send_json($data);
    }

    wp_die();
}


add_filter( 'woocommerce_add_to_cart_fragments', 'iconic_cart_count_fragments', 10, 1 );
function iconic_cart_count_fragments( $fragments ) {
    $fragments['span.cart-count'] = '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';
    return $fragments;
}

function share_product(){
	echo '<div class="product-share">
		<div class="product-share-title">
			 <i class="fas fa-share"></i> Share
		</div>';
	echo do_shortcode('[addtoany]');
	echo '</div>';
}

// function after_cart_buttons(){
// 	echo '<div class="after-cart-buttons">';
// 	// product_back_link();
// 	echo '</div>';
// }


function product_back_link(){
	echo '<a href="' . get_permalink(get_option( 'woocommerce_shop_page_id' )) . '" class="back-link">&lt; Keep Shopping</a>';
}

add_filter( 'woocommerce_return_to_shop_redirect', 'product_shop_link');
function product_shop_link(){
	return home_url();
}

// function msw_woo_qty_before(){
// 	echo '<div class="numberinput-wrapper">';
// }
// function msw_woo_qty_after(){
// 	echo '<div class="numberinput-increment up"></div>';
// 	echo '<div class="numberinput-increment down"></div>';
// 	echo '</div>';
// }
// add_action('woocommerce_before_quantity_input_field', 'msw_woo_qty_before', 10, 0);
// add_action('woocommerce_after_quantity_input_field', 'msw_woo_qty_after', 10, 0);

/**
 * Change number of related products output
 */ 
function woo_related_products_limit() {
  global $product;
	
	$args['posts_per_page'] = 3;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args', 20 );
function jk_related_products_args( $args ) {
	$args['posts_per_page'] = 3; // 4 related products
	$args['columns'] = 3; // arranged in 2 columns
	return $args;
}


/*************************************************
some spam blocks for the default contact form
************************************************/
add_filter( 'gform_validation', 'custom_validation' );
function custom_validation( $validation_result ) {
	$form = $validation_result['form'];

	if($form['id'] == 1){

		$firstname = rgpost( 'input_1' );
		$lastname = rgpost( 'input_2' );
		$email = rgpost( 'input_3' );
		$phone = rgpost( 'input_4' );
		$textarea = rgpost( 'input_5' );

		if ( $firstname == $lastname ) {

			// set the form validation to false
			$validation_result['is_valid'] = false;

			foreach( $form['fields'] as &$field ) {

				if ( $field->id == '1' || $field->id == '2' ) {
					$field->failed_validation = true;
					$field->validation_message = 'This field is invalid!';

					if ( $field->id == '2' ) {
						break;
					}
				}
			}
		}

		elseif($email == "eric.jones.z.mail@gmail.com"){
			$validation_result['is_valid'] = false;

			foreach( $form['fields'] as &$field ) {
				if ( $field->id == '3' ) {
					$field->failed_validation = true;
					$field->validation_message = 'This email is invalid!';
					break;
				}
			}
		}

		elseif (
			strpos($textarea, '.ru') !== false ||
			strpos($textarea, 'youtube.com') !== false ||
			strpos($textarea, 'youtu.be') !== false ||
			strpos($textarea, 'porn') !== false ||
			strpos($textarea, 'sex') !== false ||
			strpos($textarea, 'SEO') !== false ||
			strpos($textarea, 'PPC') !== false ||
			strpos($textarea, 'crypto') !== false ||
			strpos($textarea, 'http') !== false ||
			strpos($textarea, 'www') !== false ||
			strpos($textarea, '@') !== false ||
			strpos($textarea, 'nutricompany') !== false
		) {
			$validation_result['is_valid'] = false;
			
			foreach( $form['fields'] as &$field ) {
				if ( $field->id == '5' ) {
					$field->failed_validation = true;
					$field->validation_message = 'Contains invalid content! No spam, URLs, or email allowed';
					break;
				}
			}
		}
	}

	//Assign modified $form object back to the validation result
	$validation_result['form'] = $form;
	return $validation_result;
}
