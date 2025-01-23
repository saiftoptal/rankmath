<?php

/**
 * This class handles everything that needs to run during plugin activation
 */

class Rms_Activator {

	/**
	 * This is the main method that gets called every time the plugin is activated
	 * */

	public static function activate() : void {

		/**
		 * Create or ensure roles exist
		 */

		add_role('cool_kid', 'Cool Kid', [
			'read' => true,
		]);
		add_role('cooler_kid', 'Cooler Kid', [
			'read' => true,
			// Optionally add more capabilities
		]);
		add_role('coolest_kid', 'Coolest Kid', [
			'read' => true,
			// Possibly more capabilities
		]);

		/**
		 * Create the necessary pages
		 */

		// Registration page
		self::rms_page_create(
			array(
				'title' => 'Register',
				'slug' => 'register',
				'template' => 'rms_register_template.php'
			)
		);

		// Profile page
		self::rms_page_create(
			array(
				'title' => 'Profile',
				'slug' => 'profile',
				'template' => 'rms_profile_template.php'
			)
		);

		// User list page
		self::rms_page_create(
			array(
				'title' => 'Cool Kids\' List',
				'slug' => 'cool-kids-list',
				'template' => 'rms_user_list_template.php'
			)
		);

	}

	/**
	 * This method helps create page in accordance to the supplied title, slug, and template.
	 * */

	private function rms_page_create($data) : void {
		$slug = $data['slug'];
		$title = $data['title'];
		if ( null == Rms_Helper_Controller::get_page_by_title($title)) {
			$uploader_page = array(
				'comment_status'        => 'closed',
				'ping_status'           => 'closed',
				'post_name'             => $slug,
				'post_title'            => $title,
				'post_status'           => 'publish',
				'post_type'             => 'page'
			);
			$post_id = wp_insert_post( $uploader_page );
			if ( !$post_id ) {
				wp_die( 'Error creating template page' );
			} else {
				update_post_meta( $post_id, '_wp_page_template', $data['template'].'.php' );
			}
		}
	}

}