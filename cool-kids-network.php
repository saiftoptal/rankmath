<?php

/**
 * Plugin Name: Cool Kids Network
 * Plugin URI: https://rankmath.saif.london/
 * Description: This plugin adds the crucial functionalities for the Cool Kids Network
 * Version: 1.0.1
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

define( 'RMS_VERSION', '1.0.1' );

/**
 * Enqueue plugin assets (styles, scripts, etc.).
 */
add_action( 'wp_enqueue_scripts', 'rms_enqueue_styles' );
function rms_enqueue_styles() {
	wp_enqueue_style( 'rms-style', plugin_dir_url( __FILE__ ) . 'assets/style.css', array(), RMS_VERSION );
}

/**
 * Include the plugin activator, which runs during plugin activation. It creates the necessary roles, and pages, if they don't exist already.
 */

function activate_rms():void{
	require_once plugin_dir_path(__FILE__) . 'includes/Rms_Activator.php';
	(new Rms_Activator())->activate();
}
register_activation_hook(__FILE__, 'activate_rms');



/**
 * This code allows to autoload PHP Classes from controllers folder. Each controller defines a class with a distinct responsibility.
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

/**
 * Filter the template for the plugin pages.
 * If a page has the custom template meta 'rms_template.php', load the plugin template.
 */
add_filter( 'template_include', 'rms_plugin_template', 99 );
function rms_plugin_template( $template ) {
	if ( is_page() ) {
		global $post;
		$plugin_template = get_post_meta( $post->ID, '_wp_page_template', true );
		if ( 'rms_template.php' === $plugin_template ) {
			return plugin_dir_path( __FILE__ ) . 'templates/rms_template.php';
		}
	}
	return $template;
}

/**
 * Instantiate our controller classes on 'plugins_loaded'.
 */
add_action( 'plugins_loaded', function() {
	new Rms_Signup_Form_Shortcode_Controller();
	new Rms_Profile_Shortcode_Controller();
	new Rms_Login_Form_Shortcode_Controller();
	new Rms_User_list_Shortcode_Controller();
	new Rms_Header_Menu_Shortcode_Controller();
});

/**
 * Hide the admin bar from the frontend of the site*
 */

add_filter('show_admin_bar', '__return_false');


/**
 * Register our REST routes
 */
add_action( 'rest_api_init', function() {
	// The Rms_Api_Controller defines and registers our "role-assignment" endpoint.
	$api_controller = new Rms_Api_Controller();
	$api_controller->register_routes();
});