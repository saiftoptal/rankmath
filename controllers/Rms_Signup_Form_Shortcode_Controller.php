<?php

/**
 * Shortcode controller responsible for displaying and processing the sign-up form.
 * Renders the [rms_signup_form] shortcode.
 */

class Rms_Signup_Form_Shortcode_Controller {

	/**
	 * Constructor hooks into 'init' to register the shortcode.
	 */
	public function __construct() {
		// When WordPress reaches 'init', we register the shortcode.
		add_action( 'init', [ $this, 'rms_register_sign_up_form_shortcode' ] );
	}

	/**
	 * Registers the shortcode with a handler method.
	 */
	public function rms_register_sign_up_form_shortcode(): void {
		add_shortcode( 'rms_signup_form', [ $this, 'rms_render_sign_up_form_shortcode' ] );
	}

	/**
	 * Renders the sign-up form (HTML) and handles the form submission.
	 */
	public function rms_render_sign_up_form_shortcode(): string {
		// If user is already logged in, provide a simple message.
		if ( is_user_logged_in() ) {
			return '<p>You are already registered and logged in.</p>';
		}

		// Handle the form submission if present in $_POST.
		$this->handle_sign_up_form_submission();

		// Output the basic sign-up form.
		ob_start();
		?>
		<form method="post" style="max-width: 400px;">
			<label for="rms_signup_email">Email</label><br>
			<input type="email" name="rms_signup_email" required><br><br>
			<input type="submit" name="rms_signup_submit" value="Sign Up">
		</form>
		<?php
		return ob_get_clean();
	}

	/**
	 * Handles creating a new user, generating random data,
	 * assigning the 'cool_kid' role, and logging them in.
	 */
	private function handle_sign_up_form_submission(): void {
		if ( isset($_POST['rms_signup_submit']) && ! empty($_POST['rms_signup_email']) ) {
			$email = sanitize_email( $_POST['rms_signup_email'] );

			// If this email is already taken, stop processing.
			if ( email_exists( $email ) ) {
				wp_die( 'This email is already registered. Please <a href="'.site_url().'/login">log in</a> instead.' );
			}

			// Create the user with a random password.
			$user_id = wp_create_user( $email, wp_generate_password(), $email );
			if ( is_wp_error( $user_id ) ) {
				wp_die( 'User creation failed: ' . $user_id->get_error_message() );
			}

			// Assign the default role 'cool_kid'.
			$user = new WP_User( $user_id );
			$user->set_role( 'cool_kid' );

			// Fetch random user data to store in usermeta.
			$random_data = Rms_Helper_Controller::fetch_random_user_data();
			if ( $random_data ) {
				update_user_meta( $user_id, 'first_name', $random_data['first_name'] );
				update_user_meta( $user_id, 'last_name',  $random_data['last_name'] );
				update_user_meta( $user_id, 'country',    $random_data['country'] );
			}

			// Log this new user in immediately.
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id, true );

			// Redirect to their profile after successful signup.
			wp_safe_redirect( site_url( '/profile' ) );
			exit;
		}
	}
}