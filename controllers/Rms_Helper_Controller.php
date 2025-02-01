<?php

/**
 * This class provides necessary helper methods for the entire plugin
 */

class Rms_Helper_Controller {
	/**
	 * Fetches a single random user record from randomuser.me and
	 * returns simplified data for first name, last name, and country.
	 */
	public static function fetch_random_user_data(): ?array {
		$response = wp_remote_get( 'https://randomuser.me/api/' );

		if ( is_wp_error( $response ) ) {
			error_log( 'Rms_Helper_Controller: randomuser.me API request failed.' );
			return null;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['results'][0] ) ) {
			$person = $data['results'][0];
			return [
				'first_name' => ucfirst( $person['name']['first'] ?? '' ),
				'last_name'  => ucfirst( $person['name']['last'] ?? '' ),
				'country'    => $person['location']['country'] ?? 'Unknown'
			];
		}

		return null;
	}

	/**
	 * Sometimes WP users end up with multiple roles.
	 * This method determines the highest level of role for a user in the cool kids network.
	 * */
	public static function user_highest_role($user): string {
		if(in_array('coolest_kid', $user->roles)){
			$role = 'Coolest Kid';
		} elseif (in_array('cooler_kid', $user->roles)){
			$role = 'Cooler Kid';
		} else {
			$role = 'Cool Kid';
		}
		return $role;
	}
}