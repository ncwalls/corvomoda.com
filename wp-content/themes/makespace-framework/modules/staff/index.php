<?php

class MakespaceFrameworkStaffModule {

	// The field(s) listed here are unique to this module
	// and will be enabled/disabled when the module is
	private $acf_fields = array(
		'group_5a7e3a5f65e84',
		'group_5a7f14f16d5d1'
	);

	function __construct(){
		add_action( 'after_setup_theme', array( $this, 'check_module_theme_support' ), 15 );
		add_action( 'after_setup_theme', array( $this, 'toggle_acf_fields' ), 20 );
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
	}

	function admin_menu(){
		// Adds an ACF options menu under the new post type's menu
		$post_type_single_name = apply_filters( 'staff_module_single_name', 'Staff Member' );
		acf_add_options_sub_page( array(
			'page_title' => $post_type_single_name . ' Archive &amp; Global Settings',
			'menu_title' => $post_type_single_name . ' Settings',
			'menu_slug' => 'makespace-staff_module-archive-settings',
			'parent_slug' => 'edit.php?post_type=staff_module'
		) );
	}

	function check_module_theme_support(){
		// If the theme doesn't support this module, do nothing. Otherwise...
		if( current_theme_supports( 'staff-module' ) ){
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'init', array( $this, 'init' ) );
		}
	}

	function enter_title_here(){
		// Make a friendlier placeholder for new posts
		return 'Enter name here';
	}

	function init(){
		// Register post types and taxonomies for this module
		$post_type_slug = apply_filters( 'staff_module_slug', 'staff' );
		$post_type_single_name = apply_filters( 'staff_module_single_name', 'Staff Member' );
		$post_type_plural_name = apply_filters( 'staff_module_plural_name', 'Staff Members' );
		$post_type_menu_icon = apply_filters( 'staff_module_menu_icon', 'dashicons-businessman' );
		register_post_type( 'staff_module', array(
			'label' => $post_type_plural_name,
			'labels' => array(
				'name' => $post_type_plural_name,
				'singular_name' => $post_type_single_name,
				'add_new' => 'Add New',
				'add_new_item' => 'Add New ' . $post_type_single_name,
				'edit_item' => 'Edit ' . $post_type_single_name,
				'new_item' => 'New ' . $post_type_single_name,
				'view_item' => 'View '  .$post_type_single_name,
				'view_items' => 'View ' . $post_type_plural_name,
				'search_items' => 'Search ' . $post_type_plural_name,
				'not_found' => 'No ' . strtolower( $post_type_plural_name ) . ' found',
				'not_found_in_trash' => 'No ' . strtolower( $post_type_plural_name ) . ' found in trash',
				'parent_item_colon' => 'Parent ' . $post_type_single_name,
				'all_items' => 'All ' . $post_type_plural_name,
				'archives' => $post_type_single_name . ' Archives',
				'attributes' => $post_type_single_name . ' Attributes',
				'insert_into_item' => 'Insert into ' . $post_type_single_name . ' page',
				'uploaded_to_this_item' => 'Uploaded to ' . $post_type_single_name . ' page'
			),
			'has_archive' => true,
			'hierarchical' => true,
			'public' => true,
			'menu_icon' => $post_type_menu_icon,
			'supports' => array( 'title', 'editor', 'revisions', 'page-attributes' ),
			'rewrite' => array(
				'slug' =>  $post_type_slug
			)
		) );

		$taxonomy_slug = apply_filters( 'staff_module_taxonomy_slug', 'department' );
		$taxonomy_single_name = apply_filters( 'staff_module_taxonomy_single_name', 'Department' );
		$taxonomy_plural_name = apply_filters( 'staff_module_taxonomy_plural_name', 'Departments' );

		register_taxonomy( 'staff_module_department', 'staff_module', array(
			'label' => $taxonomy_plural_name,
			'labels' => array(
				'name' => $taxonomy_plural_name,
				'singular_name' => $taxonomy_single_name,
				'menu_name' => $taxonomy_plural_name,
				'all_items' => 'All ' . $taxonomy_plural_name,
				'edit_item' => 'Edit ' . $taxonomy_single_name,
				'view_item' => 'View ' . $taxonomy_single_name,
				'update_item' => 'Update ' . $taxonomy_single_name,
				'add_new_item' => 'Add New ' . $taxonomy_single_name,
				'new_item_name' => 'New ' . $taxonomy_single_name . ' Name',
				'parent_item' => 'Parent ' . $taxonomy_single_name,
				'search_items' => 'Search ' . $taxonomy_plural_name,
				'popular_items' => 'Popular ' . $taxonomy_plural_name,
				'separate_items_with_commas' => 'Separate with commas',
				'add_or_remove_items' => 'Add or remove ' . strtolower( $taxonomy_plural_name ),
				'choose_from_most_used' => 'Choose from most used ' . strtolower( $taxonomy_plural_name ),
				'not_found' => 'No ' . strtolower( $taxonomy_plural_name ) . ' found',
			),
			'hierarchical' => true,
			'rewrite' => array(
				'slug' => $taxonomy_slug
			),
			'show_admin_column' => true
		) );
	}

	function toggle_acf_fields(){
		// Enable/disable acf fields for this module
		$enabled = current_theme_supports( 'staff-module' );
		foreach( $this->acf_fields as $field_group ){
			$field_group_json_file = $field_group . '.json';
			if( true == $enabled && !file_exists( get_stylesheet_directory() . '/acf-json/' . $field_group_json_file ) ){
				copy( dirname( __FILE__ ) . '/' . $field_group_json_file, get_stylesheet_directory() . '/acf-json/' . $field_group_json_file );
				update_option( 'msw_admin_notice_acf_sync', true, 'no' );
			}
			if( false == $enabled && file_exists( get_stylesheet_directory() . '/acf-json/' . $field_group_json_file ) ){
				unlink( get_stylesheet_directory() . '/acf-json/' . $field_group_json_file );
				update_option( 'msw_admin_notice_acf_delete', true, 'no' );
			}
		}
	}
}

new MakespaceFrameworkStaffModule();
