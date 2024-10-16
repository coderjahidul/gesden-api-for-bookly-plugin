<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/coderjahidul/
 * @since      1.0.0
 *
 * @package    Gesden_Api_For_Bookly_Plugin
 * @subpackage Gesden_Api_For_Bookly_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Gesden_Api_For_Bookly_Plugin
 * @subpackage Gesden_Api_For_Bookly_Plugin/public
 * @author     Jahidul islam Sabuz <sobuz0349@gmail.com>
 */
class Gesden_Api_For_Bookly_Plugin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gesden_Api_For_Bookly_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gesden_Api_For_Bookly_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gesden-api-for-bookly-plugin-public.css', array(), $this->version, 'all' );

		// include jquery-ui stylesheet link
		wp_enqueue_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gesden_Api_For_Bookly_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gesden_Api_For_Bookly_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gesden-api-for-bookly-plugin-public.js', array( 'jquery' ), $this->version, false );

		// Include jquery script file link 
		wp_enqueue_script( 'jquery', 'https://code.jquery.com/jquery-3.6.0.min.js' );

		// Include jquery-ui script file link
		wp_enqueue_script( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js' );

	}

}
