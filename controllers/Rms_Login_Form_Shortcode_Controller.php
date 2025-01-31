<?php

/**
 * Shortcode controller for the login process.
 * Renders the [rms_login_form] shortcode.
 */
class Rms_Login_Form_Shortcode_Controller {

	public function __construct() {
		add_action( 'init', [ $this, 'register_shortcode' ] );
	}

	public function register_shortcode(): void {
		add_shortcode( 'rms_login_form', [ $this, 'render_shortcode' ] );
	}

	/**
	 * Outputs a simple email-only login form and handles authentication
	 * by email only, ignoring password checks as a proof-of-concept.
	 */
	public function render_shortcode(): string {
		if ( is_user_logged_in() ) {
			return '<p>You are already logged in.</p>';
		}

		$this->handle_login();

		ob_start();
		?>
		<form method="post" style="max-width: 400px;">
			<h2>Login</h2>
			<label for="rms_login_email">Email</label><br>
			<input type="email" name="rms_login_email" required><br><br>
			<input type="submit" name="rms_login_submit" value="Log In">
		</form>
		<?php
		return ob_get_clean();
	}

	/**
	 * Attempts to find a user by email, then logs them in without verifying a password
	 * Does not allow users with "administrator" role login this way, as a basic security measure
	 */
	private function handle_login(): void {
		if ( isset($_POST['rms_login_submit']) && ! empty($_POST['rms_login_email']) ) {
			$email = sanitize_email( $_POST['rms_login_email'] );
			$user  = get_user_by( 'email', $email );

			if ( $user && !in_array('administrator', $user->roles) ) {
				wp_set_current_user( $user->ID );
				wp_set_auth_cookie( $user->ID, true );
				wp_safe_redirect( site_url( '/profile' ) );
				exit;
			} else {
				/* Keeping this small and simple, not handling error messaging in a nice way. */
				wp_die( 'No user found with that email. Please <a href="'.site_url().'/register">register</a> first.' );
			}
		}
	}
}