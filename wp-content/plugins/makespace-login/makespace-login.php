<?php
/*
Plugin Name: Makespace Login
Description: Allows Makespace staff to log into this website as administrators. You should not deactivate this plugin unless you want to revoke Makespace's access to your website.
Version: 1.0.0
Author: Makespace Web
Author URI: https://www.makespaceweb.com
*/

if( !defined( 'ABSPATH' ) ){
	exit( 'This plugin can only be used by WordPress.' );
}

class MakespaceGSuiteLogin {

	private $client_id;

	function __construct(){
		include_once( plugin_dir_path( __FILE__ ) . '/plugin-updater.php' );

		new MakespacePluginUpdater( __FILE__ );

		$this->client_id = '623420679931-15u7drbn3bgurajbmrp290a50umvn82b.apps.googleusercontent.com';

		add_action( 'admin_head', array( $this, 'enqueue_scripts_google' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'login_footer', array( $this, 'enable_google_login_for_makespace' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_scripts_login' ) );
		add_action( 'login_head', array( $this, 'enqueue_scripts_google' ) );
		add_action( 'wp_ajax_msw_google_signin', array( $this, 'ajax_msw_google_signin' ) );
		add_action( 'wp_ajax_msw_google_signout', array( $this, 'ajax_msw_google_signout' ) );
		add_action( 'wp_ajax_nopriv_msw_google_signin', array( $this, 'ajax_msw_google_signin' ) );
		add_action( 'wp_head', array( $this, 'enqueue_scripts_google' ) );
	}

	function ajax_msw_google_signin(){
		require_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );
		$token = sanitize_text_field( $_POST[ 'token' ] );
		$client = new Google_Client( array(
			'client_id' => $this->client_id
		) );
		$payload = $client->verifyIdToken( $token );
		$result = array(
			'success' => 0,
			'message' => 'The G Suite login is only available for Makespace users.',
		);
		add_filter( 'send_email_change_email', '__return_false' );
		add_filter( 'send_password_change_email', '__return_false' );
		if( is_array( $payload ) && array_key_exists( 'hd', $payload ) && 'makespaceweb.com' == $payload[ 'hd' ] ){
			$user = get_user_by( 'email', $payload[ 'email' ] );
			$user_data = array(
				'user_url' => 'https://www.makespaceweb.com',
				'user_pass' => wp_generate_password( 18, true ),
				'role' => 'administrator'
			);
			if( array_key_exists( 'name', $payload ) ){
				$user_data[ 'display_name' ] = $payload[ 'name' ];
			}
			if( array_key_exists( 'given_name', $payload ) ){
				$user_data[ 'first_name' ] = $payload[ 'given_name' ];
			}
			if( array_key_exists( 'family_name', $payload ) ){
				$user_data[ 'last_name' ] = $payload[ 'family_name' ];
			}
			if( $user ){
				$user_data[ 'ID' ] = $user->ID;
				wp_update_user( $user_data );
			} else {
				$user_data[ 'user_email' ] = $payload[ 'email' ];
				$user_data[ 'user_login' ] = $payload[ 'email' ];
				if( array_key_exists( 'name', $payload ) && !get_user_by( 'login', $payload[ 'name' ] ) ){
					$user_data[ 'user_login' ] = $payload[ 'name' ];
				}
				$user_id = wp_insert_user( $user_data );
				$user = get_user_by( 'id', $user_id );
			}
			wp_set_current_user( $user->ID, $user->user_login );
			wp_set_auth_cookie( $user->ID );
			do_action( 'wp_login', $user->user_login );
			$result = array(
				'success' => 1,
				'redirect' => admin_url()
			);
		}
		add_filter( 'send_email_change_email', '__return_true' );
		add_filter( 'send_password_change_email', '__return_true' );
		exit( json_encode( $result ) );
	}

	function ajax_msw_google_signout(){
		wp_logout();
		exit( json_encode( array( 'redirect' => admin_url() ) ) );
	}

	function enable_google_login_for_makespace(){
		echo '<div id="msw-google-signin-container">
			<p>or</p>
			<div id="msw-google-signin"></div>
		</div>';
	}

	function enqueue_scripts(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'msw-google-signin', plugin_dir_url( __FILE__ ) . 'makespace-login.js' );
		wp_localize_script( 'msw-google-signin', 'msw_google_signin', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'client_id' => $this->client_id
		) );
	}

	function enqueue_scripts_login(){
		wp_enqueue_style( 'msw-google-signin', plugin_dir_url( __FILE__ ) . 'makespace-login.css' );
	}

	function enqueue_scripts_google(){
		echo '<script src="https://apis.google.com/js/platform.js?onload=MakespaceGSuiteLoginInit" async defer></script>';
	}

}

new MakespaceGSuiteLogin();
