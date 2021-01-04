<?php

if( !class_exists( 'MakespacePluginUpdater' ) ){

	class MakespacePluginUpdater {

		private $latest_release;
		private $plugin_data;
		private $plugin_directory;
		private $plugin_directory_url;
		private $plugin_file;
		private $plugin_slug;
		private $update_url;

		function __construct( $file ){
			$this->plugin_file = $file;
			$this->update_url = 'https://us-central1-eco-codex-202901.cloudfunctions.net/pluginUpdates';

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins' ) );
			add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );
			add_filter( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 3 );
		}

		private function init(){
			$this->plugin_data = get_plugin_data( $this->plugin_file );
			$this->plugin_directory = dirname( plugin_basename( $this->plugin_file ) );
			$this->plugin_directory_url = plugin_dir_url( $this->plugin_file );
			$this->plugin_slug = plugin_basename( $this->plugin_file );
			$this->get_release_info();
		}

		private function get_release_info(){
			$url = add_query_arg( array(
				'plugin' => $this->plugin_directory,
				'site' => home_url()
			), $this->update_url );
			$response = wp_remote_retrieve_body( wp_remote_get( $url ) );
			if( !is_wp_error( $response ) && !empty( $response ) ){
				$this->latest_release = json_decode( $response );
			}
		}

		function plugins_api( $false, $action, $response ){
			$this->init();
			if( empty( $response->slug ) || $response->slug != $this->plugin_directory ){
				return false;
			}
			$response->slug = $this->plugin_directory;
			$response->plugin_name  = $this->plugin_data[ 'Name' ];
			$response->version = $this->latest_release->version;
			$response->author = $this->plugin_data[ 'AuthorName' ];
			$response->homepage = $this->plugin_data[ 'PluginURI' ];
			$response->download_link = $this->latest_release->download_url;
			return $response;
		}

		function pre_set_site_transient_update_plugins( $transient ){
			if( empty( $transient->checked ) ){
				return $transient;
			}
			$this->init();
			$should_update = version_compare( $this->latest_release->version, $transient->checked[ $this->plugin_slug ] );
			if( 1 == $should_update ){
				$update = new stdClass();
				$update->icons = array(
					'2x' => $this->plugin_directory_url . 'assets/icon-256x256.png',
					'1x' => $this->plugin_directory_url . 'assets/icon-128x128.png'
				);
				$update->new_version = $this->latest_release->version;
				$update->package = $this->latest_release->download_url;
				$update->plugin = $this->plugin_slug;
				$update->slug = $this->plugin_directory;
				$update->tested = get_bloginfo( 'version' );
				$update->url = $this->plugin_data[ 'PluginURI' ];
				$transient->response[ $this->plugin_slug ] = $update;
			}
			return $transient;
		}

		public function upgrader_post_install( $true, $hook_extra, $result ) {
			$this->init();
			$was_activated = is_plugin_active( $this->plugin_slug );
			global $wp_filesystem;
			$plugin_abs_directory = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->plugin_directory;
			$wp_filesystem->move( $result[ 'destination' ], $plugin_abs_directory );
			$result[ 'destination' ] = $plugin_abs_directory;
			if( $was_activated ){
				$activate = activate_plugin( $this->plugin_slug );
			}
			return $result;
		}

	}

}
