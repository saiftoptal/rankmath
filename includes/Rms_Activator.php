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

	private static function rms_page_create($data) : void {
		$existing_page = get_page_by_path( $data['slug'] );

		if ( $existing_page ) {
			// If page already exists, update its content to ensure the shortcode is present.
			$existing_page->post_content = $data['shortcode'];
			wp_update_post( $existing_page );
		} else {
			// Create a new page with the given title, slug, and shortcode content.
			$page_id = wp_insert_post( [
				'post_title'   => $data['title'],
				'post_name'    => $data['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $data['shortcode']
			] );

			if ( is_wp_error( $page_id ) ) {
				error_log( 'Rms_Activator: Failed to create page -> ' . $data['title'] );
			}
		}
	}

}