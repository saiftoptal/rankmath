<?php
/**
 * Shortcode controller for the login process.
 * Renders the [rms_login_form] shortcode and processes login submissions.
 */
class Rms_Login_Form_Shortcode_Controller {

	/**
	 * Constructor.
	 * Hooks login processing and shortcode registration to the init action.
	 */
	public function __construct() {
		// Process login submissions early to ensure no output has been sent.
		add_action( 'init', [ $this, 'process_login_submission' ] );
		// Register the login form shortcode.
		add_action( 'init', [ $this, 'rms_register_login_form_shortcode' ] );
	}

	/**
	 * Registers the [rms_login_form] shortcode.
	 */
	public function rms_register_login_form_shortcode(): void {
		add_shortcode( 'rms_login_form', [ $this, 'rms_render_login_form_shortcode' ] );
	}

	/**
	 * Processes the login submission.
	 *
	 * This method runs on the 'init' hook so that header modifications
	 * (such as redirection) occur before any output is sent.
	 */
	public function process_login_submission(): void {
		if ( isset( $_POST['rms_login_submit'] ) && ! empty( $_POST['rms_login_email'] ) ) {
			$email = sanitize_email( $_POST['rms_login_email'] );
			$user  = get_user_by( 'email', $email );

			// Check if user exists and is not an administrator.
			if ( $user && ! in_array( 'administrator', $user->roles, true ) ) {
				// Log the user in.
				wp_set_current_user( $user->ID );
				wp_set_auth_cookie( $user->ID, true );
				// Redirect to the profile page.
				wp_safe_redirect( site_url( '/profile' ) );
				exit;
			} else {
				// Stop processing and display an error.
				wp_die( 'No user found with that email. Please <a href="' . site_url( '/register' ) . '">register</a> first.' );
			}
		}
	}

	/**
	 * Renders the login form.
	 *
	 * Note: The form submission is processed by process_login_submission() on init.
	 *
	 * @return string The HTML output of the login form.
	 */
	public function rms_render_login_form_shortcode(): string {
		if ( is_user_logged_in() ) {
			return '<p>You are already logged in.</p>';
		}

		ob_start();
		?>
        <form method="post"">
            <label for="rms_login_email">Email</label><br>
            <input type="email" name="rms_login_email" required><br><br>
            <input type="submit" name="rms_login_submit" value="Log In">
        </form>
		<?php
		return ob_get_clean();
	}
}