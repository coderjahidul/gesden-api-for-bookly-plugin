<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/coderjahidul/
 * @since             1.0.0
 * @package           Gesden_Api_For_Bookly_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Gesden API For Bookly Plugin
 * Plugin URI:        https://github.com/coderjahidul/gesden-api-for-bookly-plugin
 * Description:       Gesden API For Bookly Plugin
 * Version:           1.0.0
 * Author:            Jahidul islam Sabuz
 * Author URI:        https://github.com/coderjahidul//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gesden-api-for-bookly-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GESDEN_API_FOR_BOOKLY_PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gesden-api-for-bookly-plugin-activator.php
 */
function activate_gesden_api_for_bookly_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gesden-api-for-bookly-plugin-activator.php';
	Gesden_Api_For_Bookly_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gesden-api-for-bookly-plugin-deactivator.php
 */
function deactivate_gesden_api_for_bookly_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gesden-api-for-bookly-plugin-deactivator.php';
	Gesden_Api_For_Bookly_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gesden_api_for_bookly_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_gesden_api_for_bookly_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gesden-api-for-bookly-plugin.php';


// include fetch-api-response.php file
require plugin_dir_path( __FILE__ ) . '/admin/fetch-api-response.php';

// include custom function file
require plugin_dir_path( __FILE__ ) . '/admin/custom-function.php';

// enqueue scripts

add_action( 'wp_enqueue_scripts', 'gesden_api_for_bookly_plugin_scripts' );

function gesden_api_for_bookly_plugin_scripts() {
	wp_enqueue_script( 'custom-js', plugin_dir_url( __FILE__ ) . '/admin/js/custom.js', array( 'jquery' ), '1.0.0', true );
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gesden_api_for_bookly_plugin() {

	$plugin = new Gesden_Api_For_Bookly_Plugin();
	$plugin->run();

}
run_gesden_api_for_bookly_plugin();
