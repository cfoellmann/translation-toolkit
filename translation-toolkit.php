<?php
/*
Plugin Name: Translation Toolkit
Plugin URI: https://github.com/wp-repository/translation-toolkit/
Description: @todo
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
 * @author		Translation Toolkit Contributors
 * @copyright	Copyright (c) 2014, Translation Toolkit Contributors
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @package		TranslationToolkit
 * @version		0.1-beta
 */

//avoid direct calls to this file
if ( !defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// @todo Implement differently
//if ( function_exists( 'csp_po_install_plugin' ) ) {
//	//rewrite and extend the error messages displayed at failed activation
//	//fall trough, if it's a real code bug forcing the activation error to get the appropriated message instead
//	if ( isset($_GET['action']) && isset($_GET['plugin']) && ($_GET['action'] == 'error_scrape') && ($_GET['plugin'] == plugin_basename(__FILE__) ) ) {
//		if ( !function_exists('token_get_all') ) {
//			echo "<table>";
//			echo "<tr style=\"font-size: 12px;\"><td><strong style=\"border-bottom: 1px solid #000;\">Codestyling Localization</strong></td><td> | ".__( 'required', 'translation-toolkit' )."</td><td> | ".__( 'actual', 'translation-toolkit' )."</td></tr>";
//			echo "<tr style=\"font-size: 12px;\"><td>PHP Tokenizer Module:</td><td align=\"center\"><strong>active</strong></td><td align=\"center\"><span style=\"color:#f00;\">not installed</span></td></tr>";
//			echo "</table>";
//		}
//	}
//}

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
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		self::$instance = $this;

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		// initialize config and data on activation
		register_activation_hook( __FILE__, array( 'Translation_Toolkit', 'activate_plugin' ) ); // @todo FIX THIS
		register_deactivation_hook( __FILE__, array( 'Translation_Toolkit', 'deactivate_plugin' ) );

	} // END __construct()

	/**
	 * @todo
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
	 * @todo
	 *
	 * @since 1.0.0
	 */
	function activate_plugin() {

		get_option( 'translation-toolkit.low-memory', 1 );

		if ( !function_exists( 'token_get_all' ) ) {
			$current = get_option( 'active_plugins' );
			array_splice( $current, array_search( plugin_basename(__FILE__), $current ), 1 );
			update_option( 'active_plugins', $current );
			exit;
		}

	} // END activate_plugin()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	function deactivate_plugin() {

	} // END deactivate_plugin()

} // END class TranslationToolkit

/** Instantiate the init class */
$translationtoolkit = new TranslationToolkit();

// HELPERS
class CspStringsAreAscii {
	function _strlen($string) { return strlen($string); }
	function _strpos($haystack, $needle, $offset = null) { return strpos($haystack, $needle, $offset); }
	function _substr($string, $offset, $length = null) { return (is_null($length) ? substr($string, $offset) : substr($string, $offset, $length)); }
	function _str_split($string, $chunkSize) { return str_split($string, $chunkSize); }
	function _substr_count( $haystack, $needle) { return substr_count( $haystack, $needle); }
	function _seems_utf8($string) { return seems_utf8($string); }
	function _utf8_encode($string) { return utf8_encode($string); }
}

class CspStringsAreMultibyte {
	function _strlen($string) { return mb_strlen($string, 'ascii'); }
	function _strpos($haystack, $needle, $offset = null) { return mb_strpos($haystack, $needle, $offset, 'ascii'); }

	/**
	 * @param integer $offset
	 */
	function _substr($string, $offset, $length = null) { return (is_null($length) ? mb_substr($string, $offset, 1073741824, 'ascii') : mb_substr($string, $offset, $length, 'ascii')); }
	function _str_split($string, $chunkSize) {
		//do not! break unicode / uft8 character in the middle of encoding, just at char border
		$length = $this->_strlen($string);
		$out = array();
		for ($i=0;$i<$length;$i+=$chunkSize) {
			$out[] = $this->_substr($string, $i, $chunkSize);
		}
		return $out;
	}
	function _substr_count( $haystack, $needle) { return mb_substr_count( $haystack, $needle, 'ascii'); }
	function _seems_utf8($string) { return mb_check_encoding($string, 'UTF-8'); }
	function _utf8_encode($string) { return mb_convert_encoding($string, 'UTF-8'); }
}