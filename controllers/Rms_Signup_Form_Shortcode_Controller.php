<?php
/**
 * Shortcode controller responsible for displaying and processing
 * the sign-up form.
 * Renders the [rms_signup_form] shortcode.
 */
class Rms_Signup_Form_Shortcode_Controller {

	/**
	 * Constructor.
	 *
	 * Hooks:
	 * - process_signup_submission(): to process the signup form on 'init' early.
	 * - rms_register_sign_up_form_shortcode(): to register the shortcode.
	 */
	public function __construct() {
		// Process sign-up submissions before output is sent.
		add_action( 'init', [ $this, 'process_signup_submission' ] );
		// Register the sign-up form shortcode.
		add_action( 'init', [ $this, 'rms_register_sign_up_form_shortcode' ] );
	}

	/**
	 * Registers the [rms_signup_form] shortcode.
	 */
	public function rms_register_sign_up_form_shortcode(): void {
		add_shortcode( 'rms_signup_form', [ $this, 'rms_render_sign_up_form_shortcode' ] );
	}

	/**
	 * Processes the sign-up form submission.
	 *
	 * This method is hooked to 'init' so that it runs before any output is sent,
	 * which avoids potential issues with redirecting.
	 */
	public function process_signup_submission(): void {
		if ( isset( $_POST['rms_signup_submit'] ) && ! empty( $_POST['rms_signup_email'] ) ) {
			$email = sanitize_email( $_POST['rms_signup_email'] );

			// If this email is already taken, display an error and halt execution.
			if ( email_exists( $email ) ) {
				wp_die( 'This email is already registered. Please <a href="' . site_url( '/login' ) . '">log in</a> instead.' );
			}

			// Create the user with a random password.
			$user_id = wp_create_user( $email, wp_generate_password(), $email );
			if ( is_wp_error( $user_id ) ) {
				wp_die( 'User creation failed: ' . $user_id->get_error_message() );
			}

			// Assign the default role 'cool_kid'.
			$user = new WP_User( $user_id );
			$user->set_role( 'cool_kid' );

			// Fetch random user data and update user meta.
			$random_data = Rms_Helper_Controller::fetch_random_user_data();
			if ( $random_data ) {
				update_user_meta( $user_id, 'first_name', $random_data['first_name'] );
				update_user_meta( $user_id, 'last_name',  $random_data['last_name'] );
				update_user_meta( $user_id, 'country',    $random_data['country'] );
			}

			// Log the new user in.
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id, true );

			// Redirect to the profile page.
			wp_safe_redirect( site_url( '/profile' ) );
			exit;
		}
	}

	/**
	 * Renders the sign-up form.
	 *
	 * @return string The HTML output of the sign-up form.
	 */
	public function rms_render_sign_up_form_shortcode(): string {
		// If the user is already logged in, show a message.
		if ( is_user_logged_in() ) {
			return '<p>You are already registered and logged in.</p>';
		}

		// Output the sign-up form.
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
}