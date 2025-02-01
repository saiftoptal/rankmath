<?php

/**
 * Handles our custom REST API endpoint for role assignment.
 * Endpoint: /wp-json/cool-kids-network/v1/role-assignment
 */
class Rms_Api_Controller {

	/**
	 * Registers the route with the WordPress REST API.
	 */
	public function register_routes(): void {
		register_rest_route(
			'cool-kids-network/v1',
			'/role-assignment',
			[
				[
					'methods'             => 'POST',
					'callback'            => [ $this, 'handle_role_assignment' ],
					'permission_callback' => [ $this, 'api_permissions_check' ],
				],
			]
		);
	}

	/**
	 * A simple check for a shared secret in the header.
	 * In production, you may want a more secure approach.
	 */
	public function api_permissions_check( $request ) {
		$secret = $request->get_header( 'authorization' );
		return ( $secret === 'Bearer SuperSecretAPIAuthenticationToken' );
	}

	/**
	 * Processes the incoming POST request to change a user's role.
	 * Accepts either "email" OR "first_name"+"last_name" to identify the user,
	 * and "role" to specify the new role.
	 */
	public function handle_role_assignment( $request ) {
		$new_role = $request->get_param( 'role' );
		$email    = $request->get_param( 'email' );
		$first    = $request->get_param( 'first_name' );
		$last     = $request->get_param( 'last_name' );

		// Validate that the role is one of our known ones.
		$valid_roles = [ 'cool_kid', 'cooler_kid', 'coolest_kid' ];
		if ( ! in_array( $new_role, $valid_roles, true ) ) {
			return new WP_Error(
				'invalid_role',
				'Role must be one of: cool_kid, cooler_kid, coolest_kid',
				[ 'status' => 400 ]
			);
		}

		// Find the user by email or by first+last name.
		$user = null;
		if ( ! empty( $email ) ) {
			$user = get_user_by( 'email', $email );
		} elseif ( ! empty( $first ) && ! empty( $last ) ) {
			$args = [
				'meta_query' => [
					'relation' => 'AND',
					[
						'key'   => 'first_name',
						'value' => $first,
					],
					[
						'key'   => 'last_name',
						'value' => $last,
					]
				]
			];
			$users = get_users( $args );
			if ( ! empty( $users ) ) {
				$user = $users[0];
			}
		} else {
			return new WP_Error(
				'missing_identifiers',
				'Please provide an email OR (first_name and last_name).',
				[ 'status' => 400 ]
			);
		}

		// If no user was found, return an error.
		if ( ! $user ) {
			return new WP_Error(
				'user_not_found',
				'No matching user found.',
				[ 'status' => 404 ]
			);
		}

		// Assign the new role to this user.
		if (in_array(Rms_Helper_Controller::user_highest_role($user), array('Cool Kid', 'Cooler Kid', 'Coolest Kid'))){
			$wp_user = new WP_User( $user->ID );
			$wp_user->set_role( $new_role );

			return [
				'success'  => true,
				'message'  => 'User role has been updated.',
				'user_id'  => $user->ID,
				'new_role' => $new_role
			];
		} else {
			return new WP_Error(
				'user_not_authorized',
				'You are not authorized to change role for this user.',
				[ 'status' => 403 ]
			);
		}
	}
}