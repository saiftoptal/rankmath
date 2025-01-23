<?php

/**
 * This class handles everything that needs to run during plugin activation
 */

class Rms_Activator {

	public static function activate() : void {
		// Create or ensure roles exist:
		add_role('cool_kid', 'Cool Kid', [
			'read' => true,
		]);
		add_role('cooler_kid', 'Cooler Kid', [
			'read' => true,
			// Optionally add more capabilities
		]);
		add_role('coolest_kid', 'Coolest Kid', [
			'read' => true,
			// Possibly more capabilities
		]);
	}
}