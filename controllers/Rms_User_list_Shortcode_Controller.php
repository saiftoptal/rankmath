<?php

/**
 * Shortcode controller for listing all users.
 * Behavior is role-based:
 *  - 'cooler_kid' sees name + country of all users.
 *  - 'coolest_kid' also sees email + role for each user.
 *  - 'cool_kid' or logged-out user cannot see the list.
 * Renders the [rms_user_list] shortcode.
 */
class Rms_User_list_Shortcode_Controller {

	public function __construct() {
		add_action( 'init', [ $this, 'rms_register_user_list_shortcode' ] );
	}

	public function rms_register_user_list_shortcode(): void {
		add_shortcode( 'rms_user_list', [ $this, 'rms_render_user_list_shortcode' ] );
	}

	/**
	 * Displays the table of users. Only available for roles 'cooler_kid' and 'coolest_kid'.
	 */
	public function rms_render_user_list_shortcode(): string {
		if ( ! is_user_logged_in() ) {
			return '<p>You must be logged in to see this list.</p>';
		}

		$current_user = wp_get_current_user();
		$current_user_role = Rms_Helper_Controller::user_highest_role($current_user);

		if ( ! in_array( $current_user_role, [ 'Cooler Kid', 'Coolest Kid' ], true ) ) {
			return '<p>You do not have permission to see this list.</p>';
		}

		// Gather all users that has one of the three roles of the network
		$all_users = get_users(
            array(
                'role__in'      => array(
                    'cool_kid',
                    'cooler_kid',
                    'coolest_kid',
                )
            )
        );

		ob_start();
		?>
		<table class="rms-user-list-table">
			<thead>
			<tr>
				<th>Name</th>
				<th>Country</th>
				<?php if ( 'Coolest Kid' === $current_user_role ) : ?>
					<th>Email</th>
					<th>Role</th>
				<?php endif; ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $all_users as $user_obj ): ?>
				<?php
				$uid        = $user_obj->ID;
				$first_name = get_user_meta( $uid, 'first_name', true );
				$last_name  = get_user_meta( $uid, 'last_name', true );
				$country    = get_user_meta( $uid, 'country', true );
				$user_email = $user_obj->user_email;
				$role       = Rms_Helper_Controller::user_highest_role($user_obj);
				?>
				<tr>
					<td><?php echo esc_html( $first_name . ' ' . $last_name ); ?></td>
					<td><?php echo esc_html( $country ); ?></td>
					<?php if ( 'Coolest Kid' === $current_user_role ) : ?>
						<td><?php echo esc_html( $user_email ); ?></td>
						<td><?php echo esc_html( $role ); ?></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		return ob_get_clean();
	}
}