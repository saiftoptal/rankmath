<?php

/**
 * This class handles everything that needs to run during plugin activation
 */

class Rms_Activator {

	/**
	 * This is the main method that gets called every time the plugin is activated
	 * */

	public static function activate() : void {
		self::create_cool_kids_network_roles();
		self::create_cool_kids_network_pages();
		flush_rewrite_rules();
	}

	/**
	 * Create or ensure roles exist
	 */

	private static function create_cool_kids_network_roles() : void {
		add_role('cool_kid', 'Cool Kid', [
			'read' => true,
		]);
		add_role('cooler_kid', 'Cooler Kid', [
			'read' => true,
		]);
		add_role('coolest_kid', 'Coolest Kid', [
			'read' => true,
		]);
	}

	/**
	 * Create the necessary pages
	 */

	private static function create_cool_kids_network_pages() : void {
		// Registration page
		self::rms_page_create(
			array(
				'title'     => 'Register',
				'slug'      => 'register',
				'shortcode' => '[rms_signup_form]',
				'template'  => 'rms_template.php'
			)
		);

		// Login page
		self::rms_page_create(
			array(
				'title'     => 'Login',
				'slug'      => 'login',
				'shortcode' => '[rms_login_form]',
				'template'  => 'rms_template.php'
			)
		);

		// Profile page
		self::rms_page_create(
			array(
				'title'     => 'Profile',
				'slug'      => 'profile',
				'shortcode' => '[rms_profile_view]',
				'template'  => 'rms_template.php'
			)
		);

		// User list page
		self::rms_page_create(
			array(
				'title'     => 'Cool Kids\' List',
				'slug'      => 'cool-kids-list',
				'shortcode' => '[rms_user_list]',
				'template'  => 'rms_template.php'
			)
		);
	}


	/**
	 * This method helps create page in accordance to the supplied title, slug, content, and template.
	 * */

	private static function rms_page_create($data) : void {
		$existing_page = get_page_by_path( $data['slug'] );
		if ( $existing_page ) {
			// Update the page content to ensure the shortcode is present.
			$existing_page->post_content = $data['shortcode'];
			wp_update_post( $existing_page );

			// Attempt to update the page template meta.
			$result = update_post_meta( $existing_page->ID, '_wp_page_template', $data['template'] );
			if ( false === $result ) {
				error_log( 'Rms_Activator: Failed to update _wp_page_template for page ' . $data['title'] );
			}
		} else {
			// Create a new page with the given title, slug, and shortcode content.
			$page_id = wp_insert_post( [
				'post_title'   => $data['title'],
				'post_name'    => $data['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $data['shortcode'],
			] );

			if ( is_wp_error( $page_id ) ) {
				error_log( 'Rms_Activator: Failed to create page -> ' . $data['title'] );
			} else {
				// Attempt to update the meta key.
				$result = update_post_meta( $page_id, '_wp_page_template', $data['template'] );
				if ( false === $result ) {
					// If update_post_meta fails, try adding the meta.
					$add_result = add_post_meta( $page_id, '_wp_page_template', $data['template'], true );
					if ( false === $add_result ) {
						error_log( 'Rms_Activator: Failed to add _wp_page_template for page ' . $data['title'] );
					}
				}
			}
		}
	}

}