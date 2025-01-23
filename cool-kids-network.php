<?php

/**
 * Plugin Name: Cool Kids Network
 * Plugin URI: https://rankmath.saif.london/
 * Description: This plugin adds the crucial functionalities for the Cool Kids Network
 * Version: 1.0.0
 * Author: Saif Bin Alam
 * Author URI: https://saif.london/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rankmath
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Use prefix "rms_" everywhere in the plugin for security and compatibility

/**
 * Currently plugin version.
 */

define( 'RMS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation. It creates the necessary roles, and pages, if they don't exist already.
 */

function activate_rms():void{
	require_once plugin_dir_path(__FILE__) . 'includes/Rms_Activator.php';
	(new Rms_Activator())->activate();
}
register_activation_hook(__FILE__, 'activate_rms');



/**
 * This code allows to autoload PHP Classes from controllers folder.
 */

$plugin_path = plugin_dir_path(__FILE__);
$classes = [glob($plugin_path . 'controllers/*.php')];
if ($classes) {
	foreach ($classes as $class) {
		foreach ($class as $file) {
			require_once $file;
		}
	}
}