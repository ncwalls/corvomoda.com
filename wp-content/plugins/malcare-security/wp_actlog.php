<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVWPActLog')) :

	class BVWPActLog {

		public static $actlog_table = 'activities_store';
		public $db;
		public $settings;
		public $bvinfo;

		public function __construct($db, $settings, $info, $config) {
			$this->db = $db;
			$this->settings = $settings;
			$this->bvinfo = $info;
			$this->request_id = MCAccount::randString(16);
			$this->ip_header = array_key_exists('ip_header', $config) ? $config['ip_header'] : false;
		}

		function init() {
			$this->add_actions_and_listeners();
		}

		function get_post($post_id) {
			$post = get_post($post_id);
			$data = array('id' => $post_id);
			if (!empty($post)) {
				$data['title'] = $post->post_title;
				$data['status'] = $post->post_status;
				$data['type'] = $post->post_type;
				$data['url'] = get_permalink($post_id);
				$data['date'] = $post->post_date;
			}
			return $data;
		}

		function get_comment($comment_id) {
			$comment = get_comment($comment_id);
			$data = array('id' => $comment_id);
			if (!empty($comment)) {
				$data['author'] = $comment->comment_author;
			}
			return $data;
		}

		function get_term($term_id) {
			$term = get_term($term_id);
			$data = array('id' => $term_id);
			if (!empty($term)) {
				$data['name'] = $term->name;
				$data['slug'] = $term->slug;
				$data['taxonomy'] = $term->taxonomy;
			}
			return $data;
		}

		function get_user($user_id) {
			$user = get_userdata($user_id);
			$data = array('id' => $user_id);
			if (!empty($user)) {
				$data['username'] = $user->user_login;
				$data['email'] = $user->user_email;
			}
			return $data;
		}

		function get_blog($blog_id) {
			$blog = get_blog_details($blog_id);
			$data = array('id' => $blog_id);
			if (!empty($blog)) {
				$data['name'] = $blog->blogname;
				$data['url'] = $blog->path;
			}
			return $data;
		}

		function add_activity($event_data) {
			$user = wp_get_current_user();
			$values = array();
			if (!empty($user)) {
				$values["user_id"] = $user->ID;
				$values["username"] = $user->user_login;
			}
			$values["request_id"] = $this->request_id;
			$values["site_id"] = get_current_blog_id();
			$values["ip"] = BVProtectBase::getIP($this->ip_header);
			$values["event_type"] = current_filter();
			$values["event_data"] = maybe_serialize($event_data);
			$values["time"] = time();
			$this->db->replaceIntoBVTable(BVWPActLog::$actlog_table, $values);
		}

		function user_login_handler($user_login, $user) {
			$event_data = array("user" => $this->get_user($user->ID));
			$this->add_activity($event_data);
		}

		function user_logout_handler($user_id) {
			$user = $this->get_user($user_id);
			$event_data = array("user" => $user);
			$this->add_activity($event_data);
		}

		function password_reset_handler($user, $new_pass) {
			if (!empty($user)) {
				$event_data = array("user" => $this->get_user($user->ID));
				$this->add_activity($event_data);
			}
		}

		function comment_handler($comment_id) {
			$comment = $this->get_comment($comment_id);
			$post = $this->get_post($comment->comment_post_ID);
			$event_data = array(
				"comment" => $comment,
				"post" => $post
			);
			$this->add_activity($event_data);
		}

		function comment_status_changed_handler($new_status, $old_status, $comment) {
			$post = $this->get_post($comment->comment_post_ID);
			$event_data = array(
				"comment" => $this->get_comment($comment->comment_ID),
				"post" => $post,
				"old_status" => $old_status,
				"new_status" => $new_status
			);
			$this->add_activity($event_data);
		}

		function post_handler($post_id) {
			$post = $this->get_post($post_id);
			$event_data = array(
				"post" => $post
			);
			$this->add_activity($event_data);
		}

		function post_saved_handler($post_id, $post, $update) {
			$post = $this->get_post($post_id);
			$event_data = array(
				"post" => $post,
				"updated" => $update
			);
			$this->add_activity($event_data);
		}

		function term_handler($term_id) {
			$term = $this->get_term($term_id);
			$event_data = array(
				"term" => $term,
			);
			$this->add_activity($event_data);
		}

		function term_updation_handler($data, $term_id) {
			$term = $this->get_term($term_id);
			$event_data = array(
				"old_term" => $term,
				"term" => $data
			);
			$this->add_activity($event_data);
			return $data;
		}

		function term_deletion_handler($term_id) {
			$event_data = array(
				"term" => array("id" => $term_id)
			);
			$this->add_activity($event_data);
		}

		function user_handler($user_id) {
			$user = $this->get_user($user_id);
			$event_data = array(
				"user" => $user,
			);
			$this->add_activity($event_data);
		}

		function user_update_handler($user_id, $old_userdata) {
			$new_userdata = $this->get_user($user_id);
			$event_data = array(
				"old_user" => $this->get_user($old_userdata->ID),
				"user" => $new_userdata,
			);
			$this->add_activity($event_data);
		}

		function plugin_action_handler($plugin) {
			$event_data = array("plugin" => $plugin);
			$this->add_activity($event_data);
		}

		function theme_action_handler($theme_name) {
			$event_data = array("theme" => $theme_name);
			$this->add_activity($event_data);
		}

		function mu_handler($blog_id) {
			$blog = $this->get_blog($blog_id);
			$event_data = array(
				"blog" => $blog
			);
			$this->add_activity($event_data);
		}

		function mu_delete_handler($blog) {
			$event_data = array(
				"blog" => $this->get_blog($blog->blog_id)
			);
			$this->add_activity($event_data);
		}

		/* ADDING ACTION AND LISTENERS FOR SENSING EVENTS. */
		public function add_actions_and_listeners() {
			/* SENSORS FOR POST AND PAGE CHANGES */
			add_action('pre_post_update', array($this, 'post_handler'));
			add_action('save_post', array($this, 'post_saved_handler'), 10, 3);
			add_action('post_stuck', array($this, 'post_handler'));
			add_action('post_unstuck', array($this, 'post_handler'));
			add_action('delete_post', array($this, 'post_handler'));

			/* SENSORS FOR COMMENTS */
			add_action('comment_post', array($this, 'comment_handler'));
			add_action('edit_comment', array($this, 'comment_handler'));
			add_action('transition_comment_status', array($this, 'comment_status_changed_handler'), 10, 3);

			/* SENSORS FOR TAG AND CATEGORY CHANGES */
			add_action('create_term', array($this, 'term_handler'));
			add_action('pre_delete_term', array($this, 'term_handler'));
			add_action('delete_term', array($this, 'term_deletion_handler'));
			add_filter('wp_update_term_data', array($this, 'term_updation_handler'), 10, 2);

			/* SENSORS FOR USER CHANGES*/
			add_action('user_register', array($this, 'user_handler'));
			add_action('wpmu_new_user', array($this, 'user_handler'));
			add_action('profile_update', array($this, 'user_update_handler'), 10, 2);
			add_action('delete_user', array($this, 'user_handler'));
			add_action('wpmu_delete_user', array($this, 'user_handler'));

			/* SENSORS FOR PLUGIN AND THEME*/
			add_action('activate_plugin', array($this, 'plugin_action_handler'));
			add_action('deactivate_plugin', array($this, 'plugin_action_handler'));
			add_action('switch_theme', array($this, 'theme_action_handler'));

			/* SENSORS FOR MULTISITE CHANGES */
			add_action('wpmu_new_blog', array($this, 'mu_handler'));
			add_action('archive_blog', array($this, 'mu_handler'));
			add_action('unarchive_blog', array( $this, 'mu_handler'));
			add_action('activate_blog', array($this, 'mu_handler'));
			add_action('deactivate_blog', array($this, 'mu_handler'));
			add_action('wp_delete_site', array($this, 'mu_delete_handler'));

			/* SENSORS USER ACTIONS AT FRONTEND */
			add_action('wp_login', array($this, 'user_login_handler'), 10, 2);
			add_action('wp_logout', array( $this, 'user_logout_handler'), 5, 1);
			add_action('password_reset', array( $this, 'password_reset_handler'), 10, 2);
		}
	}
endif;