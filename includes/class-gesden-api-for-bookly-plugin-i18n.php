<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/coderjahidul/
 * @since      1.0.0
 *
 * @package    Gesden_Api_For_Bookly_Plugin
 * @subpackage Gesden_Api_For_Bookly_Plugin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gesden_Api_For_Bookly_Plugin
 * @subpackage Gesden_Api_For_Bookly_Plugin/includes
 * @author     Jahidul islam Sabuz <sobuz0349@gmail.com>
 */
class Gesden_Api_For_Bookly_Plugin_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gesden-api-for-bookly-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
