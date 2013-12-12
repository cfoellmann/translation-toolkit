<?php
/*
Plugin Name: Translation Toolkit
Plugin URI: https://github.com/wp-repository/translation-toolkit/
Description: @TODO
Version: 0.1-beta
Author: Project Contributors
Author URI: https://github.com/wp-repository/translation-toolkit/graphs/contributors
Text Domain: translation-toolkit
Domain Path: /languages

	Translation Toolkit
	Copyright 2013 Translation Toolkit Project Contributors
    Copyright 2008-2013 Heiko Rabe  (email : info@code-styling.de)

    This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @author Translation Toolkit Contributors <https://github.com/wp-repository/translation-toolkit/graphs/contributors>
 * @license GPLv2 <http://www.gnu.org/licenses/gpl-2.0.html>
 * @package Translation Toolkit
 */

//avoid direct calls to this file
if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// temporary includes
require_once( dirname(__FILE__) . '/deprecated.php' );

/** Register autoloader */
spl_autoload_register( 'TranslationToolkit::autoload' );

class TranslationToolkit {
	
	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Current version of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '0.1-beta';
	// public $db_version = '1';

	/**
	 * Holds a copy of the main plugin filepath.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private static $file = __FILE__;
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		self::$instance = $this;
		
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		// initialize config and data on activation
		register_activation_hook( __FILE__, array( 'Translation_Toolkit', 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( 'Translation_Toolkit', 'deactivate_plugin' ) );

	} // END __construct()

	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	public function init() {
		
		if ( true == apply_filters( 'tt_dev', WP_DEBUG ) ) {
			define( 'TT_DEV', true );
		}

		if ( is_admin() ) {
			
			$translationtoolkit_admin = new TranslationToolkit_Admin;
			$translationtoolkit_ajax = new TranslationToolkit_Ajax;

		}

	} // END init()
	
	/**
	 * PSR-0 compliant autoloader to load classes as needed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classname The name of the class
	 * @return null Return early if the class name does not start with the correct prefix
	 */
	public static function autoload( $classname ) {

		if ( 'TranslationToolkit' !== mb_substr( $classname, 0, 18 ) )
			return;

		$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . str_replace( '_', DIRECTORY_SEPARATOR, $classname ) . '.php';
		if ( file_exists( $filename ) )
			require $filename;

	} // END autoload()
	
	/**
	 * Getter method for retrieving the object instance.
	 *
	 * @since 1.0.0
	 */
	public static function get_instance() {

		return self::$instance;

	} // END get_instance()

	/**
	 * Getter method for retrieving the main plugin filepath.
	 *
	 * @since 1.0.0
	 */
	public static function get_file() {

		return self::$file;

	} // END get_file()
	
	/**
	 * Load the plugin's textdomain hooked to 'plugins_loaded'.
	 *
	 * @since 1.0.0
	 */
	function load_plugin_textdomain() {
		
		load_plugin_textdomain( 'translation-toolkit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
	}
	
	/**
	 * Desc.
	 *
	 * @since 1.0.0
	 */
	function activate_plugin() {
		
		if ( !function_exists( 'token_get_all' ) ) {
			$current = get_option( 'active_plugins' );
			array_splice( $current, array_search( plugin_basename(__FILE__), $current ), 1 );
			update_option( 'active_plugins', $current );
			exit;
		}
		
	} // END activate_plugin()
	
	/**
	 * Desc.
	 *
	 * @since 1.0.0
	 */
	function deactivate_plugin() {
		
	} // END deactivate_plugin()

} // END class TranslationToolkit

/** Instantiate the init class */
$translationtoolkit = new TranslationToolkit();
