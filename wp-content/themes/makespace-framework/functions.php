<?php
/*----------------------------------------------------------------------------------------------------
  Our standard error logging function for debugging WordPress
----------------------------------------------------------------------------------------------------*/
if( !function_exists( 'write_log' ) ){
	function write_log( $log, $title = 'MAKESPACE DEBUG EVENT' ){
		if( true === WP_DEBUG ){
			if( wp_doing_cron() ){
				$title = 'DOING CRON: ' . $title;
			}
			error_log( '---------- ' . $title . ' ----------' );
			if( is_array( $log ) || is_object( $log ) ){
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		} else {
			// If we left any write_log calls in our code, we should clean
			// them up. But this will also ensure that we don't pollute the
			// debug log if we're not looking at it!
			return null;
		}
	}
}

if( defined( 'WP_DEBUG' ) && true === WP_DEBUG ){
	ini_set( 'error_log', get_stylesheet_directory() . '/debug.log' );
}

class MakespaceFramework {

	function __construct(){
		include_once( get_template_directory() . '/functions-pluggable.php' );
		include_once( get_template_directory() . '/functions-woocommerce.php' );
		// include_once( get_template_directory() . '/includes/acf/acf-makespace.php' );
		include_once( get_template_directory() . '/includes/tgmpa/class-tgm-plugin-activation.php' );

		// Load Makespace Modules
		include_once( get_template_directory() . '/modules/index.php' );

		// Universal Actions
		add_action( 'admin_head', array( $this, 'do_favicon' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_scripts' ) );
		add_action( 'login_head', array( $this, 'do_favicon' ) );
		add_action( 'init', array( $this, 'check_required_plugins' ) );
		add_action( 'tgmpa_register', array( $this, 'tgmpa_register' ) );
		add_action( 'woocommerce_after_main_content', array( $this, 'woocommerce_after_main_content' ) );
		add_action( 'woocommerce_before_main_content', array( $this, 'woocommerce_before_main_content' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 9 );
		add_action( 'wp_head', array( $this, 'do_favicon' ) );
		add_action( 'wp_head', array( $this, 'javascript_detection' ), 0 );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );

		// Universal Filters
		add_filter( 'acf/settings/show_admin', array( $this, 'acf_show_admin' ) );
		add_filter( 'acf/settings/load_json', array( $this, 'acf_load_json_path' ) );
		add_filter( 'acf/settings/save_json', array( $this, 'acf_save_json_path' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
		add_filter( 'auto_core_update_send_email', '__return_false' );
		add_filter( 'auto_update_core', '__return_true' );
		add_filter( 'auto_update_plugin', '__return_true' );
		add_filter( 'auto_update_theme', '__return_true' );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'default_page_template_title', array( $this, 'default_page_template_title' ) );
		add_filter( 'embed_oembed_html', array( $this, 'embed_oembed_html' ), 99, 4 );
		add_filter( 'gform_init_scripts_footer', '__return_true' );
		add_filter( 'get_next_post_sort', array( $this, 'get_next_post_sort' ) );
		add_filter( 'get_next_post_where',  array( $this, 'get_next_post_where' ) );
		add_filter( 'get_previous_post_sort', array( $this, 'get_previous_post_sort' ) );
		add_filter( 'get_previous_post_where', array( $this, 'get_previous_post_where' ) );
		add_filter( 'login_headertext', array( $this, 'login_headertext' ) );
		add_filter( 'login_headerurl', array( $this, 'login_headerurl' ) );
		add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), 10, 2 );
		add_filter( 'show_admin_bar', '__return_false' );
		add_filter( 'tablepress_use_default_css', '__return_false' );
		add_filter( 'the_generator', '__return_false' );
		add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojicons_tinymce' ) );
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'woocommerce_breadcrumb_defaults' ) );
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		add_filter( "mce_external_plugins", array( $this, "msw_add_buttons" ) );
		add_filter( 'mce_buttons_3', array( $this, 'msw_mce_buttons' ) );
		add_filter( 'wp_handle_upload', array( $this, 'wp_handle_upload' ), 1, 2 );
		add_filter( 'wpseo_breadcrumb_output_wrapper', array( $this, 'wpseo_breadcrumb_output_wrapper' ) );
		add_filter( 'wpseo_breadcrumb_links', array( $this, 'wpseo_breadcrumb_links' ) );
		add_filter( 'wpseo_breadcrumb_separator', array( $this, 'wpseo_breadcrumb_separator' ) );
		add_filter( 'wpseo_breadcrumb_single_link', array( $this, 'wpseo_breadcrumb_single_link' ), 10, 2 );
		add_filter( 'wpseo_breadcrumb_single_link_wrapper', array( $this, 'wpseo_breadcrumb_single_link_wrapper' ) );
		add_filter( 'wpseo_metabox_prio', array( $this, 'wpseo_metabox_prio' ) );

		// Universal Shortcodes
		add_shortcode( 'protected_email', array( $this, 'sc_protected_email' ) );
		add_shortcode( 'makespace_sitemap', array( $this, 'sc_makespace_sitemap' ) );
		add_shortcode( 'makespace_button', array( $this, 'sc_makespace_button' ) );

		// Universal Cleanup
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
		remove_action( 'wp_head', 'feed_links' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
		remove_action( 'wp_head', 'print_emoji_detection_script' , 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}

	function msw_add_buttons( $plugin_array ) {
		$plugin_array['msweditor'] = get_template_directory_uri() . '/includes/editor-buttons/editor-plugin.js';
		return $plugin_array;
	}

	function msw_mce_buttons( $buttons ) {	
		/**
		 * Add in a core button that's disabled by default
		 */
		$buttons[] = 'msw_email';
		$buttons[] = 'msw_button';

		return $buttons;
	}

	function acf_load_json_path( $paths ){
		unset( $paths[ 0 ] );
		$paths[] = get_stylesheet_directory() . '/acf-json';
		return $paths;
	}

	function acf_map_key(){
		$google_api_key = get_field( 'default_google_map_api_key', 'option' );
		if( $google_api_key ){
			acf_update_setting( 'google_api_key', $google_api_key );
		} elseif( defined( 'GOOGLE_MAPS_API_KEY' ) ) {
			acf_update_setting( 'google_api_key', GOOGLE_MAPS_API_KEY );
		}
	}

	function acf_save_json_path( $path ){
		return get_stylesheet_directory() . '/acf-json';
	}

	function acf_show_admin(){
		return WP_DEBUG === true;
	}

	function admin_body_class( $classes ){
		return $classes;
	}

	function admin_footer_text( $text ){
		$text = 'Powered by <a href="https://www.makespaceweb.com" target="_blank">Makespace</a>';
		return $text;
	}

	function admin_menu(){
		global $wpdb;
		acf_add_options_page( array(
			'page_title' => 'Makespace Theme Options',
			'menu_title' => 'Makespace',
			'icon_url' => 'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgMTAzIDgxLjEiPjxzdHlsZT4uc3Qwe2ZpbGw6I2ZmZn08L3N0eWxlPjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik02Ni4yIDM0YzEuOSAwIDMuNSAxLjYgMy41IDMuNXYzLjJIMzMuM3YtMy4yYzAtMS45IDEuNi0zLjUgMy41LTMuNWgyOS40bTAtMy41SDM2LjhjLTMuOSAwLTcgMy4xLTcgN3Y2LjdoNDMuNHYtMy41LTMuMmMwLTMuOS0zLjEtNy03LTd6Ii8+PHBhdGggY2xhc3M9InN0MCIgZD0iTTY5LjYgNDQuMmwtLjEgNi43SDMzLjRsLS4xLTYuN2gzNi4zbTMuNi0zLjVIMjkuN2wuMSAzLjYuMSA2LjcuMSAzLjRoNDN2LTMuNWwuMS02LjcuMS0zLjV6Ii8+PHBhdGggY2xhc3M9InN0MCIgZD0iTTY5LjUgNTQuNGwtLjEgNi43SDMzLjZsLS4xLTYuN2gzNm0zLjUtMy41SDI5LjhsLjEgMy42LjEgNi43LjEgMy40SDcyLjdsLjEtMy40LjEtNi43LjEtMy42eiIvPjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik02OS4zIDY0LjdjMCAuNCAwIDEuMy0uNyAyQzY3LjEgNjggNTEuNSA3NyA1MS41IDc3cy0xNS42LTktMTcuMS0xMC40Yy0uNy0uNy0uNy0xLjYtLjctMmgzNS42bTAtMy40SDMwLjFsLjEgMy42Yy4xIDEuOS43IDMuNCAxLjggNC40IDEuNiAxLjUgMTEuOCA3LjQgMTcuNyAxMC45bDEuNyAxIDEuNy0xYzUuOS0zLjQgMTYuMi05LjQgMTcuNy0xMC45IDEuMi0xLjEgMS44LTIuNiAxLjgtNC40bC4xLTMuNmgtMy40eiIvPjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik01MS41IDguMmMyLjggMCAxNS4yIDkgMTYuNyAxMC40LjcuNy43IDEuNi44IDJsLjEuN2MuMSAxLjQuMiAzLjcuMyA1LjYuMSAyLTEuNSAzLjctMy41IDMuN0gzNy4yYy0yIDAtMy42LTEuNy0zLjUtMy43LjEtMS45LjItNC4yLjMtNS42bC4xLS43YzAtLjQgMC0xLjMuNy0yIDEuNS0xLjQgMTMuOS0xMC40IDE2LjctMTAuNG0wLTMuNWMtNC44IDAtMTguNSAxMC44LTE5LjEgMTEuNC0xLjUgMS41LTEuNyAzLjMtMS44IDR2LjJsLS4xLjdjLS4xIDEuNi0uMiAzLjktLjMgNS44LS4xIDEuOS42IDMuOCAxLjkgNS4yIDEuMyAxLjQgMy4yIDIuMiA1LjEgMi4yaDI4LjZjMS45IDAgMy44LS44IDUuMS0yLjIgMS4zLTEuNCAyLTMuMyAxLjktNS4yLS4xLTEuOS0uMi00LjItLjMtNS43bC0uMS0uN1YyMC4xYy0uMS0uNy0uMy0yLjUtMS44LTQtLjEtLjItMTQuMi0xMS40LTE5LjEtMTEuNHoiIGlkPSJYTUxJRF8xM18iLz48cGF0aCBjbGFzcz0ic3QwIiBkPSJNNzMuMiAyNS42YzUuNiAzIDEzLjYgOC4xIDIwLjcgMTIuOCA3LjUgNSA3LjUgMTYuMS0uMSAyMC45LS4xIDAtLjEuMS0uMi4xLTIuMyAxLjQtNC43IDIuMS03LjEgMi4xLTYuOSAwLTEzLjMtNS41LTEzLjMtMTMuM1YyNS42bS0zLjUtNS45djI4LjVjMCA0LjYgMS44IDguOSA1LjEgMTIuMSAzLjEgMyA3LjQgNC43IDExLjcgNC43IDMuMiAwIDYuMy0uOSA5LTIuNmwuMi0uMWM0LjYtMi45IDcuMy03LjkgNy40LTEzLjMgMC01LjQtMi42LTEwLjUtNy4yLTEzLjQtOS4xLTYtMTYuMi0xMC40LTIxLTEzbC01LjItMi45eiIgaWQ9IlhNTElEXzlfIi8+PHBhdGggY2xhc3M9InN0MCIgZD0iTTI5LjggMjUuNnYyMi42YzAgNy44LTYuNCAxMy4zLTEzLjMgMTMuMy0yLjQgMC00LjgtLjctNy4xLTIuMS0uMSAwLS4xLS4xLS4yLS4xLTcuNi00LjktNy43LTE2LS4xLTIwLjkgNy4xLTQuNyAxNS4xLTkuOCAyMC43LTEyLjhtMy41LTUuOWwtNS4yIDIuOGMtNC44IDIuNi0xMS44IDYuOS0yMSAxMy00LjUgMy03LjIgOC03LjIgMTMuNHMyLjggMTAuNCA3LjQgMTMuM2wuMi4xYzIuNyAxLjcgNS44IDIuNiA5IDIuNiA5LjMgMCAxNi44LTcuNSAxNi44LTE2LjhWMTkuN3oiIGlkPSJYTUxJRF8yXyIvPjxnPjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik02MC45IDEyLjJjLTEuMSAwLTItLjktMi0yVjJjMC0uNy40LTEuNCAxLjEtMS44LjYtLjMgMS40LS4zIDIgLjFsNi4xIDRjLjkuNiAxLjIgMS44LjYgMi44cy0xLjggMS4yLTIuOC42bC0zLTJ2NC41YzAgMS4xLS45IDItMiAyeiIvPjwvZz48Zz48cGF0aCBjbGFzcz0ic3QwIiBkPSJNNDEuOSAxMi4yYy0xLjEgMC0yLS45LTItMlY1LjdsLTMgMmMtLjkuNi0yLjEuMy0yLjctLjYtLjYtLjktLjQtMi4yLjYtMi44bDYuMS00Yy42LS40IDEuNC0uNCAyLS4xLjYuMyAxLjEgMSAxLjEgMS44djguMmMtLjEgMS4xLTEgMi0yLjEgMnoiLz48L2c+PC9zdmc+',
			'redirect' => false,
			'menu_slug' => 'msw-framework-options',
			'autoload' => true
		) );
		$comments = $wpdb->get_row( "SELECT * FROM $wpdb->comments LIMIT 1" );
		if( null == $comments ){
			remove_menu_page( 'edit-comments.php' );
		}
	}

	function admin_notices(){
		if( get_option( 'msw_admin_notice_acf_delete', false ) ){
			printf(
				'<div class="notice notice-error"><p><strong>NOTICE:</strong> You removed a Makespace module from your theme, but the ACF fields remain. If you want to remove the fields as well, <a href="%s">click here to go to your field groups</a>.</p><p>This message only appears once per module deactivation.</p></div>',
				admin_url( 'edit.php?post_type=acf-field-group' )
			);
		}
		if( get_option( 'msw_admin_notice_acf_sync', false ) ){
			printf(
				'<div class="notice notice-error"><p><strong>NOTICE:</strong> You have ACF fields that require syncing. <a href="%s">Click here to sync your field groups</a>.</p><p>This message only appears once per module activation.</p></div>',
				admin_url( 'edit.php?post_type=acf-field-group&post_status=sync' )
			);
		}
		delete_option( 'msw_admin_notice_acf_sync' );
		delete_option( 'msw_admin_notice_acf_delete' );


	}

	function after_setup_theme(){
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 2048, 2048 );
		add_theme_support( 'woocommerce' );
		register_nav_menus( array(
			'primary' => 'Primary Navigation',
			'footer' => 'Footer Links'
		) );
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		if( class_exists( 'GFForms' ) ){
			$gf = ABSPATH . 'wp-content/plugins/gravityforms';
			$themes = get_option( 'gf_imported_theme_file', array() );
			$theme = get_template();
			if( !isset( $themes[ $theme ] ) ){
				require_once( $gf . '/export.php' );
				if( GFExport::import_file( get_template_directory() . '/includes/gravityforms/standard-contact-form.json' ) ){
					$themes[ $theme ] = true;
					update_option( 'gf_imported_theme_file', $themes );
				}
			}
		}
	}

	function after_switch_theme(){
		$makespace_theme_config = get_option( 'makespace_theme_config', array() );

		if( !array_key_exists( 'did_install_framework', $makespace_theme_config ) ){
			if( !get_page_by_path( 'home-page' ) ){
				$id = wp_insert_post( array(
					'menu_order' => 0,
					'post_content' => '',
					'post_name' => 'home-page',
					'post_title' => 'Home Page',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $id );
			}
			if( !get_page_by_path( 'blog' ) ){
				$id = wp_insert_post( array(
					'menu_order' => 10,
					'post_name' => 'blog',
					'post_title' => 'Blog',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
				wp_delete_post( 1, true );
				update_option( 'page_for_posts', $id );
				update_option( 'close_comments_days_old', 1 );
				update_option( 'close_comments_for_old_posts', 1 );
				update_option( 'comment_moderation', 1 );
				update_option( 'comment_registration', 1 );
				update_option( 'comments_notify', 0 );
				update_option( 'default_comment_status', 'closed' );
				update_option( 'default_pingback_flag', 0 );
				update_option( 'default_ping_status', 'closed' );
				update_option( 'moderation_notify', 0 );
				update_option( 'show_avatars', 0 );
				update_option( 'thread_comments', 0 );
			}
			if( !get_page_by_path( 'contact' ) ){
				$contact_page_id = wp_insert_post( array(
					'menu_order' => 20,
					'post_name' => 'contact',
					'post_title' => 'Contact Us',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
				add_post_meta( $contact_page_id, '_wp_page_template', 'page_contact.php' );
			}
			if( !get_page_by_path( 'privacy-policy' ) ){
				$privacy_policy = file_get_contents( get_template_directory() . '/includes/prepackaged-content/privacy_policy.txt' );
				$privacy_policy = str_replace( '{{site_name}}', get_bloginfo( 'name' ), $privacy_policy );

				$contact_link = '<a href="'. get_page_link( get_page_by_path( 'contact' ) ) . '">contact us</a>';
				$privacy_policy = str_replace( '{{contact_link}}', $contact_link, $privacy_policy);
				wp_insert_post( array(
					'menu_order' => 97,
					'post_content' => $privacy_policy,
					'post_name' => 'privacy-policy',
					'post_title' => 'Online Privacy Policy',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
			}
			if( !get_page_by_path( 'site-info' ) ){
				$site_info = file_get_contents( get_template_directory() . '/includes/prepackaged-content/site_info.txt' );
				$site_info = str_replace( '{{site_name}}', get_bloginfo( 'name' ), $site_info );
				$site_info = str_replace( '{{year}}', date( 'Y' ), $site_info );
				wp_insert_post( array(
					'menu_order' => 98,
					'post_content' => $site_info,
					'post_name' => 'site-info',
					'post_title' => 'Site Info',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
			}
			if( !get_page_by_path( 'site-map' ) ){
				wp_insert_post( array(
					'menu_order' => 99,
					'post_content' => '[makespace_sitemap]',
					'post_name' => 'site-map',
					'post_title' => 'Site Map',
					'post_type' => 'page',
					'post_status' => 'publish'
				) );
			}
			if( get_page_by_path( 'sample-page' ) ){
				$sample_page = get_page_by_path( 'sample-page' );
				wp_delete_post( $sample_page->ID, true );
				wp_delete_comment( 1, true );
			}
			$has_timezone = get_option( 'timezone_string' );
			if( !$has_timezone ){
				update_option( 'timezone_string', 'America/Kentucky/Louisville' );
			}

			$this->create_sample_posts();
			$makespace_theme_config = get_option( 'makespace_theme_config', array() );

			if( !get_option( 'rg_gforms_disable_css' ) ){
				update_option( 'rg_gforms_disable_css', 1 );
				update_option( 'rg_gforms_enable_html5', 1 );
			}
			if( !get_option( 'rg_gforms_key' ) && defined( 'GF_LICENSE_KEY' ) ){
				update_option( 'rg_gforms_key', GF_LICENSE_KEY );
			}
			if( !get_field( 'n9m_option_google_map_key', 'option' ) && defined( 'GOOGLE_MAPS_API_KEY' ) ){
				update_field( 'n9m_option_google_map_key', GOOGLE_MAPS_API_KEY, 'option' );
			}
			if( defined( 'ACF_PRO_LICENSE_KEY' ) && !get_option( 'acf_pro_license', false ) ){
				$acf_license = array(
					'key' => ACF_PRO_LICENSE_KEY,
					'url' => home_url()
				);
				update_option( 'acf_pro_license', base64_encode( maybe_serialize( $acf_license ) ) );
			}
			update_option( 'thumbnail_size_w', 300 );
			update_option( 'thumbnail_size_h', 300 );
			update_option( 'medium_size_w', 900 );
			update_option( 'medium_size_h', 900 );
			update_option( 'large_size_w', 2048 );
			update_option( 'large_size_h', 2048 );
			flush_rewrite_rules();
			$makespace_theme_config[ 'did_install_framework' ] = 1;
			$makespace_theme_config[ 'did_install_framework_templates' ] = array();
			update_option( 'makespace_theme_config', $makespace_theme_config );
		}
	}

	function body_class( $classes ){
		$classes[] = 'makespace';
		return $classes;
	}

	function check_required_plugins(){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if( !is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ){
			$activate_acf = activate_plugin( 'advanced-custom-fields-pro/acf.php', null );
			add_action( 'admin_notices', function(){
				echo '<div class="notice notice-info is-dismissible"><p><strong>Advanced Custom Fields PRO</strong> is a required plugin for the Makespace theme and has been automatically activated.</p></div>';
			} );
		}
	}

	function create_sample_posts( $post_type  = 'post' ){
		$makespace_theme_config = get_option( 'makespace_theme_config', array() );
		$populated_post_types = array_key_exists( 'populated_post_types', $makespace_theme_config ) ? $makespace_theme_config[ 'populated_post_types' ] : array();
		if( !in_array( $post_type, $populated_post_types ) ){

			$blog_titles = array(
				'This Post Has a Very Long Title and Should Look Great on All Devices',
				'Short Article Title',
				'This Is a Fascinating Article About Us',
				'Post About What Is Happening In Our Industry',
				'Ten Things We Say In Sample Blog Posts',
				'This Post Has a Very Long Title and Should Look Great on All Devices',
				'Short Article Title',
				'This Is a Fascinating Article About Us',
				'Post About What Is Happening In Our Industry',
				'Ten Things We Say In Sample Blog Posts',
				'This Post Has a Very Long Title and Should Look Great on All Devices',
				'Short Article Title'
			);

			$unsplash_images = array();

			if( post_type_supports( $post_type, 'thumbnail' ) ){

				if( !function_exists( 'media_handle_upload' ) ){
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );
				}

				for ( $i = 0; $i < 3; $i++ ){
					$url = 'https://unsplash.it/1200/800/?random';
					$tmp = download_url( $url );

					if( is_wp_error( $tmp ) ){
						write_log( 'COULD NOT SET A FEATURED IMAGE' );
						@unlink( $tmp );
					} else {
						$unsplash_images[] = $tmp;
					}
				}
			}

			$set_menu_order = false;

			if( post_type_supports( $post_type, 'page-attributes' ) ){
				$set_menu_order = true;
			}

			$content = array();
			$content[] = file_get_contents( get_template_directory() . '/includes/prepackaged-content/post_html_markup.txt' );
			$content[] = file_get_contents( get_template_directory() . '/includes/prepackaged-content/post_image_alignment.txt' );
			$content[] = file_get_contents( get_template_directory() . '/includes/prepackaged-content/post_lorem.txt' );

			for( $i = 1; $i < 12; $i++ ){
				$random = rand( 0, count( $unsplash_images ) - 1 );
				$post_id = 0;

				$post_id = wp_insert_post( array(
					'menu_order' => $set_menu_order ? $i : 0,
					'post_content' => $content[ rand( 0, count( $content ) - 1 ) ],
					'post_date' => date( 'Y-m-d H:i:s', time() - ( $i * DAY_IN_SECONDS ) ),
					'post_title' => $blog_titles[ $i - 1 ],
					'post_type' => $post_type,
					'post_status' => 'publish'
				) );

				if( !empty( $unsplash_images ) && 0 != ( $i % 3 ) ){
					$desc = 'Sample Image #' . $i;
					$file_array = array(
						'name' => 'unsplash.jpeg',
						'tmp_name' => $unsplash_images[ $random ]
					);
					$attach_id = media_handle_sideload( $file_array, $post_id, $desc );
					set_post_thumbnail( $post_id, $attach_id );
				}
			}
			$populated_post_types[] = $post_type;
			$makespace_theme_config[ 'populated_post_types' ] = $populated_post_types;
			update_option( 'makespace_theme_config', $makespace_theme_config );
		}
	}

	function default_page_template_title(){
		return 'General Content';
	}

	function disable_emojicons_tinymce( $plugins ){
		if( is_array( $plugins ) ){
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}

	function do_favicon(){
		$favicon = get_field( 'default_favicon', 'option' );
		if( $favicon ){
			echo '<link rel="icon" type="image/png" href="' . $favicon . '">' . PHP_EOL;
		}
	}

	function embed_oembed_html( $html, $url, $attr, $post_id ){
		if( false !== strpos( $html, 'youtube' ) || false !== strpos( $html, 'vimeo' ) || false !== strpos( $html, 'v.wordpress.com' ) ){
			return '<div class="responsive-video-outer"><div class="responsive-video">' . $html . '</div></div>';
		}
		return $html;
	}

	function get_next_post_sort( $sort ){
		global $post;
		if( is_singular() && post_type_supports( $post->post_type, 'page-attributes' ) ){
			$sort = 'ORDER BY p.menu_order ASC LIMIT 1';
		}
		return $sort;
	}
	function get_next_post_where( $where ){
		global $post, $wpdb;
		if( is_singular() && post_type_supports( $post->post_type, 'page-attributes' ) ){
			$where = $wpdb->prepare( "WHERE p.menu_order > '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->menu_order, $post->post_type );
		}
		return $where;
	}
	function get_previous_post_sort( $sort ){
		global $post;
		if( is_singular() && post_type_supports( $post->post_type, 'page-attributes' ) ){
			$sort = 'ORDER BY p.menu_order DESC LIMIT 1';
		}
		return $sort;
	}
	function get_previous_post_where( $where ){
		global $post, $wpdb;
		if( is_singular() && post_type_supports( $post->post_type, 'page-attributes' ) ){
			$where = $wpdb->prepare( "WHERE p.menu_order < '%s' AND p.post_type = '%s' AND p.post_status = 'publish'", $post->menu_order, $post->post_type );
		}
		return $where;
	}

	function javascript_detection(){
		echo '<script>(function(html){html.className = html.className.replace(/\bno-js\b/,\'js\')})(document.documentElement);</script>' . PHP_EOL;
	}

	function live_reload(){
		if( true === WP_DEBUG ){
			$contents = '/**' . PHP_EOL
						. ' * This file exists to let Browsersync know that a post has been saved in the dashboard so it can live reload in development.' . PHP_EOL
						. ' */'. PHP_EOL
						. 'Last updated: ' . time();
			$filename = get_stylesheet_directory() . '/.gulpwatch';
			file_put_contents( $filename, $contents );
		}
	}

	function login_enqueue_scripts(){
		echo '<style type="text/css">body,html{background:#f8f7f2;font-family:arial}body.login h1 a{background-repeat:no-repeat;background-image:url(' . get_template_directory_uri() . '/includes/images/bee.png);background-size:contain;height:66px;line-height:1;width:84px}.wp-core-ui .button.button-large{background:#FF7059;border:none;border-radius:0;box-shadow:none;display:inline-block;font-weight:700;position:relative;text-transform:uppercase;-webkit-transition-property:background-color;transition-property:background-color;-webkit-transition-duration:.5s;transition-duration:.5s;height:36px;line-height:36px;text-shadow:none}.login-action-lostpassword.wp-core-ui .button.button-large{width:100%}.wp-core-ui .button.button-large:hover{background-color:#E65740}.login .message{border-color:#7795B5}.login input[type=text]:focus,.login input[type=password]:focus{border:1px solid #ddd;box-shadow:none}.login form .forgetmenot{margin-top:7px}.login #backtoblog,.login #nav{text-align:center}.login #backtoblog a,.login #nav a{-webkit-transition-property:color;transition-property:color;-webkit-transition-duration:.3s;transition-duration:.3s}.login #backtoblog a:hover,.login #nav a:hover{color:#7795B5}</style>' . PHP_EOL;
	}

	function login_headertext(){
		return 'Built by Makespace.';
	}

	function login_headerurl(){
		return home_url();
	}

	function sc_protected_email( $args, $content = null ){
		$atts = shortcode_atts( array(
			'email' => ''
		), $args );
		if( is_email( $atts[ 'email' ] ) ){
			$atts[ 'content' ] = !empty( $content ) ? $content : $atts[ 'email' ];
			return '<a href="' . antispambot( 'mailto:' . $atts[ 'email' ] ) . '">' . antispambot( $atts[ 'content' ] ) . '</a>';
		}
		return;
	}

	function sc_makespace_button( $args, $content = null ){
		$atts = shortcode_atts( array(
			'link' => '',
			'label' => '',
			'target' => ''
		), $args );
		if( esc_url( $atts[ 'link' ] ) ){
			$atts[ 'content' ] = !empty( $atts[ 'label' ] ) ? $atts[ 'label' ] : $atts[ 'link' ];
			return '<a class="button" href="' . $atts[ 'link' ] . '" target="' . $atts[ 'target' ] . '">' . $atts[ 'content' ] . '</a>';
		}
		return;
	}

	function sc_makespace_sitemap( $args, $content = null ){
		// Makes sure that the blog overview page isn't the front page and if so,
		// exclude that page from the page list.
		$blogID = get_option( 'page_for_posts' );
		$exclude = array();
		if( $blogID != get_option( 'page_on_front' ) ) {
			$exclude[] = $blogID;
		}
		if( class_exists( 'WooCommerce' ) ){
			$exclude[] = get_option( 'woocommerce_shop_page_id' );
			$exclude[] = get_option( 'woocommerce_cart_page_id' );
			$exclude[] = get_option( 'woocommerce_checkout_page_id' );
			$exclude[] = get_option( 'woocommerce_pay_page_id' );
			$exclude[] = get_option( 'woocommerce_thanks_page_id' );
			$exclude[] = get_option( 'woocommerce_myaccount_page_id' );
			$exclude[] = get_option( 'woocommerce_edit_address_page_id' );
			$exclude[] = get_option( 'woocommerce_view_order_page_id' );
			$exclude[] = get_option( 'woocommerce_terms_page_id' );
		}

		$exclude = implode( ',', $exclude );

		// Unwraps the page list of the <ul> and excludes the blog overview page
		// in order to then add the blog overview page and posts at the end
		$args = array( 'title_li' => '', 'exclude' => $exclude, 'echo' => 0, 'sort_column' => 'menu_order' );
		$pages = '<ul id="sitemap">';
		$pages .= wp_list_pages( $args );

		// On large sites, listing all of the old blog posts will be too
		// resource intensive. Instead, if there is a blog "page", we'll
		// list that and then list the categories under it. If there is
		// no blog page, we'll just list the categories.
		$posts_msw = get_posts();
		if( $posts_msw ) {
			if( $blogID ){
				// We have a blog page
				$pages .= '<li><a href="' . get_the_permalink( $blogID ) . '" title="' . get_the_title( $blogID ) . '">' . get_the_title( $blogID ) . '</a><ul>';
				$pages .= wp_list_categories( array(
					'echo' => false,
					'orderby' => 'name',
					'title_li' => ''
				) );
				$pages .= '</ul></li>';
			} else {
				$pages .= wp_list_categories( array(
					'echo' => false,
					'orderby' => 'name',
					'title_li' => apply_filters( 'makespace_sitemap_blog_title', 'Article Categories' )
				) );
			}
		}

		if( class_exists( 'WooCommerce' ) ){
			$woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id' );
			$woocommerce_products = get_posts( array(
				'nopaging' => true,
				'post_type' => 'product'
			) );
			if( $woocommerce_products ){
				$pages .= '<li><a href="' . get_permalink( $woocommerce_shop_page_id ) . '" title="' . get_the_title( $woocommerce_shop_page_id ) . '">' . get_the_title( $woocommerce_shop_page_id ) . '</a>';
				$pages .= '<ul>';
				$product_terms = get_terms( array(
					'taxonomy' => 'product_cat',
				) );
				if( $product_terms ){
					$pages .= '<li><strong>Categories</strong><ul>';
					foreach( $product_terms as $product_term ){
						$pages .= '<li><a href="' . get_term_link( $product_term->term_id ) . '" title="' . $product_term->name . '">' . $product_term->name . '</a></li>';
					}
					$pages .= '</ul></li>';
				}
				if( $product_terms ){
					$pages .= '<li><strong>All Products</strong><ul>';
					foreach( $woocommerce_products as $woocommerce_product ){
						$pages .= '<li><a href="' . get_permalink( $woocommerce_product->ID ) . '" title="' . $woocommerce_product->post_title . '">' . $woocommerce_product->post_title . '</a></li>';
					}
					$pages .= '</ul></li>';
				}
				$pages .= '</ul>';
				$pages .= '</li>';
			}
		}

		// Get Only New Custom Post Types
		$new_post_types_array = array(
		   'public'   => true,
		   '_builtin' => false
		);
		foreach ( get_post_types( $new_post_types_array ) as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			// Disregard if its the Locations Module
			if ( $post_type !== 'locations_module' ) {
				// Apply the Archive Link
				$pages .= '<li><a href="' . get_post_type_archive_link( $post_type ) . '">' . $post_type_object->label . '</a>';
				
				// Cycle through all posts within this post type
				$post_type_query = new WP_Query( array( 'post_type' => $post_type ) );
				if ( $post_type_query->have_posts() ) {
					$pages .= '<ul>';
					while ( $post_type_query->have_posts() ) {
						$post_type_query->the_post();
						$pages .= '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
					}
					$pages .= '</ul>';
					wp_reset_postdata();
				}

				$pages .= '</li>';
			}
		}

		$pages = apply_filters( 'makespace_add_to_sitemap', $pages );

		$pages .= '</ul>';

		return $pages;
	}

	function script_loader_tag( $tag, $handle ){
		if( 'google-maps' == $handle ){
			$tag = str_replace( ' src', ' async defer src', $tag );
		}
		return $tag;
	}

	function tgmpa_register(){
		tgmpa( array(
			array(
				'name' => 'Add to Any Social Sharing',
				'slug' => 'add-to-any'
			),
			array(
				'name' => 'Advanced Custom Fields (Pro)',
				'slug' => 'advanced-custom-fields-pro',
				'source' => 'advanced-custom-fields-pro.zip'
			),
			array(
				'name' => 'AMP: Accelerated Mobile Pages',
				'slug' => 'amp'
			),
			array(
				'name' => 'AMP: Glue for Yoast SEO & AMP',
				'slug' => 'glue-for-yoast-seo-amp'
			),
			array(
				'name' => 'Custom Post Type UI',
				'slug' => 'custom-post-type-ui'
			),
			array(
				'name' => 'Gravity Forms',
				'slug' => 'gravityforms',
				'source' => 'gravityforms.zip'
			),
			array(
				'name' => 'Redirection',
				'slug' => 'redirection'
			),
			array(
				'name' => 'Post Duplicator',
				'slug' => 'post-duplicator'
			),
			array(
				'name' => 'SendGrid',
				'slug' => 'sendgrid-email-delivery-simplified'
			),
			array(
				'name' => 'Simple Page Ordering',
				'slug' => 'simple-page-ordering'
			),
			array(
				'name' => 'The Events Calendar',
				'slug' => 'the-events-calendar'
			),
			array(
				'name' => 'Tablepress',
				'slug' => 'tablepress'
			),
			array(
				'name' => 'WooCommerce',
				'slug' => 'woocommerce'
			),
			array(
				'name' => 'WP Migrate DB',
				'slug' => 'wp-migrate-db'
			),
			array(
				'is_callable' => 'wpseo_init',
				'name' => 'Yoast SEO',
				'slug' => 'wordpress-seo',
			),
		), array(
			'has_notices' => false,
			'default_path' => get_template_directory() . '/includes/tgmpa/plugins/',
			'is_automatic' => true,
			'parent_slug' => 'plugins.php',
			'strings' => array(
				'menu_title' => 'Top Plugins',
				'page_title' => 'List of Top or Approved Plugins',
				'return' => 'Return to top plugins'
			)
		) );
	}

	function woocommerce_after_main_content(){
		echo '</section>';
	}

	function woocommerce_before_main_content(){
		echo '<section class="woocommerce-main-content" id="main">';
	}

	function wp_dashboard_setup(){
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	}

	function wp_enqueue_scripts(){
		wp_enqueue_script( 'jquery' );
	}

	function wp_handle_upload_get_image_sizes(){
		global $_wp_additional_image_sizes;
		$sizes = array(
			'height' => 0,
			'width' => 0
		);
		$h = 0;
		$w = 0;
		foreach( get_intermediate_image_sizes() as $_size ){
			if( in_array( $_size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ){
				$h = intval( get_option( "{$_size}_size_h" ) );
				$w = intval( get_option( "{$_size}_size_w" ) );
			} elseif( isset( $_wp_additional_image_sizes[ $_size ] ) ){
				$h = intval( $_wp_additional_image_sizes[ $_size ][ 'height' ] );
				$w = intval( $_wp_additional_image_sizes[ $_size ][ 'width' ] );
			}
			$sizes[ 'height' ] = max( $h, $sizes[ 'height' ] );
			$sizes[ 'width' ] = max( $w, $sizes[ 'width' ] );
		}
		return $sizes;
	}

	function wp_handle_upload( $file, $overrides ){
		// Don't interfere with pdf thumbnail generation
		if( 'application/pdf' == $file['type'] ){
			return $file;
		}
		$sizes = $this->wp_handle_upload_get_image_sizes();
		$max_height = $sizes[ 'height' ];
		$max_width = $sizes[ 'width' ];
		$image = wp_get_image_editor( $file[ 'file' ] );
		if( !is_wp_error( $image ) ){
			$image->resize( $max_width, $max_height );
			$image->set_quality( 80 );
			$image->save( $file[ 'file' ] );
		}
		return $file;
	}

	function wp_head(){
		$has_posts = get_posts();
		if( $has_posts ){
			echo '<link rel="alternate" type="application/rss+xml" title="' . get_bloginfo( 'name' ) . ' RSS Feed" href="' . get_bloginfo( 'rss2_url' ) . '">' . PHP_EOL;
		}
		the_field( 'universal_header_scripts', 'option' );
	}

	function wp_footer(){
		the_field( 'universal_footer_scripts', 'option' );
	}

	function wpseo_breadcrumb_output_wrapper( $wrapper ){
		return 'ul';
	}

	function wpseo_breadcrumb_links( $crumbs ){
		if( class_exists( 'WooCommerce' ) ){
			if( is_shop() || is_product_category() || is_product_tag() || is_product() || is_cart() || is_checkout() || is_account_page() ){
				$woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id' );
				$did_replace_product_link = false;
				for( $i = 0; $i < count( $crumbs ); $i++ ){
					if( array_key_exists( 'ptarchive', $crumbs[ $i ] ) ){
						$crumbs[ $i ] = array(
							'text' => get_the_title( $woocommerce_shop_page_id ),
							'url' => get_permalink( $woocommerce_shop_page_id ),
							'allow_html' => 1
						);
						$did_replace_product_link = true;
					}
				}
				if( !$did_replace_product_link ){
					$shop_page_link = array( array(
						'text' => get_the_title( $woocommerce_shop_page_id ),
						'url' => get_permalink( $woocommerce_shop_page_id ),
						'allow_html' => 1
					) );
					array_splice( $crumbs, 1, 0, $shop_page_link );
				}
			}
		}
		return $crumbs;
	}

	function wpseo_breadcrumb_separator( $sep ){
		return '';
	}

	function wpseo_breadcrumb_single_link( $link_output, $link ){
		if( false === strpos( $link_output, 'breadcrumb_last' ) ){
			$link_output = $link_output . '</li>';
		} else {
			preg_match( '/(.?<span(?:.*?)>(?:.*?)<\/span>)/', $link_output, $matches );
			if( count( $matches ) && 1 < count( $matches ) ){
				$link_output = '<li>' . $matches[ 1 ] . '</li>';
			} else {
				$link_output = '<li>' . $link_output;
			}
		}
		return $link_output;
	}

	function wpseo_breadcrumb_single_link_wrapper( $wrapper ){
		return 'li';
	}

	function wpseo_metabox_prio(){
		return 'low';
	}

}

$MakespaceFramework = new MakespaceFramework();