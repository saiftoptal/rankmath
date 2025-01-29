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
		]);
		add_role('coolest_kid', 'Coolest Kid', [
			'read' => true,
		]);

		/**
		 * Create the necessary pages
		 */

		// Registration page
		self::rms_page_create(
			array(
				'title' => 'Register',
				'slug' => 'register',
				'shortcode' => '[rms_signup_form]'
			)
		);

		// Login page
		self::rms_page_create(
			array(
				'title' => 'Login',
				'slug' => 'login',
				'shortcode' => '[rms_login_form]'
			)
		);

		// Profile page
		self::rms_page_create(
			array(
				'title' => 'Profile',
				'slug' => 'profile',
				'shortcode' => '[rms_profile_view]'
			)
		);

		// User list page
		self::rms_page_create(
			array(
				'title' => 'Cool Kids\' List',
				'slug' => 'cool-kids-list',
				'shortcode' => '[rms_user_list]'
			)
		);

	}

	/**
	 * This method helps create page in accordance to the supplied title, slug, and content.
	 * */

	private function rms_page_create($data) : void {
		$slug = $data['slug'];
		$title = $data['title'];
		$shortcode = $data['shortcode'];
		if ( !get_page_by_path($slug) ) {
			$uploader_page = array(
				'comment_status'        => 'closed',
				'ping_status'           => 'closed',
				'post_name'             => $slug,
				'post_title'            => $title,
				'post_status'           => 'publish',
				'post_type'             => 'page',
				'post_content'          => $shortcode
			);
			$post_id = wp_insert_post( $uploader_page );
			if ( !$post_id ) {
				wp_die( 'Error creating template page' );
			}
		}
	}

}