<?php
/**
 * Header Menu Controller
 *
 * Registers the [rms_header_menu] shortcode which outputs the navigation menu.
 * The menu shows different links depending on whether the user is logged in.
 */
class Rms_Header_Menu_Shortcode_Controller {

	/**
	 * Constructor.
	 * Hooks the shortcode registration on 'init'.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'rms_register_header_menu_shortcode' ] );
	}

	/**
	 * Registers the [rms_header_menu] shortcode.
	 */
	public function rms_register_header_menu_shortcode(): void {
		add_shortcode( 'rms_header_menu', [ $this, 'rms_render_header_menu_shortcode' ] );
	}

	/**
	 * Renders the header menu.
	 *
	 * @return string HTML markup for the header menu.
	 */
	public function rms_render_header_menu_shortcode(): string {
		ob_start();
		?>
		<nav class="rms-header-menu">
			<ul>
				<?php if ( ! is_user_logged_in() ) : ?>
					<li><a href="<?php echo esc_url( site_url( '/register' ) ); ?>">Register</a></li>
					<li><a href="<?php echo esc_url( site_url( '/login' ) ); ?>">Login</a></li>
				    <?php else : ?>
					<li><a href="<?php echo esc_url( site_url( '/profile' ) ); ?>">Profile</a></li>
                    <?php
                        $current_user = wp_get_current_user();
                        $current_user_role = Rms_Helper_Controller::user_highest_role($current_user);
                        if ( in_array( $current_user_role, [ 'Cooler Kid', 'Coolest Kid' ], true ) ) :
                    ?>
                        <li><a href="<?php echo esc_url( site_url( '/cool-kids-list' ) ); ?>">Cool Kids List</a></li>
                    <?php endif; ?>
					<li><a href="<?php echo esc_url( wp_logout_url( site_url( '/login' ) ) ); ?>">Logout</a></li>
				<?php endif; ?>
			</ul>
		</nav>
		<?php
		return ob_get_clean();
	}
}