<?php

/**
 * Shortcode controller for displaying the current user's profile.
 * Renders the [rms_profile_view] shortcode.
 */
class Rms_Profile_Shortcode_Controller {

	public function __construct() {
		add_action( 'init', [ $this, 'rms_register_profile_shortcode' ] );
	}

	public function rms_register_profile_shortcode(): void {
		add_shortcode( 'rms_profile_view', [ $this, 'rms_render_profile_shortcode' ] );
	}

	/**
	 * Shows the logged-in user's identity data:
	 * first name, last name, country, email, and role.
	 */
	public function rms_render_profile_shortcode(): string {
		if ( ! is_user_logged_in() ) {
			return '<p>You must be logged in to view your profile. <a href="'. site_url() .'/login">Login here</a>.</p>';
		}

		$user_id = get_current_user_id();
		$user    = get_userdata( $user_id );

		$first_name = get_user_meta( $user_id, 'first_name', true );
		$last_name  = get_user_meta( $user_id, 'last_name', true );
		$country    = get_user_meta( $user_id, 'country', true );
		$email      = $user->user_email;
		$role       = Rms_Helper_Controller::user_highest_role($user);

		ob_start();
		?>
		<ul class="rms-user-profile">
			<li><strong>First Name:</strong> <?php echo esc_html( $first_name ); ?></li>
			<li><strong>Last Name:</strong> <?php echo esc_html( $last_name ); ?></li>
			<li><strong>Country:</strong> <?php echo esc_html( $country ); ?></li>
			<li><strong>Email:</strong> <?php echo esc_html( $email ); ?></li>
			<li><strong>Role:</strong> <?php echo esc_html( $role ); ?></li>
		</ul>
		<?php
		return ob_get_clean();
	}
}