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

		if ( is_admin() ) {
			
			$translationtoolkit_admin = new TranslationToolkit_Admin;
			
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
 




//////////////////////////////////////////////////////////////////////////////////////////
//	constant definition
//////////////////////////////////////////////////////////////////////////////////////////

//Enable this only for debugging reasons. 
//Attention: the strict logging may prevent WP from proper working because of many not handled issues.
//error_reporting(E_ALL|E_STRICT);
//@unlink(dirname(__FILE__).'/.htaccess' );

if (!defined('E_RECOVERABLE_ERROR'))
	define('E_RECOVERABLE_ERROR', 4096);
if (!defined('E_DEPRECATED'))
	define('E_DEPRECATED', 8192);
if (!defined('E_USER_DEPRECATED '))
	define('E_USER_DEPRECATED ', 16384);

function csp_split_url($url) {
  $parsed_url = parse_url($url);
  $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
  $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
  $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
  $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
  $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
  $pass     = ($user || $pass) ? "$pass@" : '';
  $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
  $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
  $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
  return array("$scheme$user$pass$host$port","$path$query$fragment"); 
}

if (function_exists('add_action')) {
	if ( !defined('WP_CONTENT_URL') )
	    define('WP_CONTENT_URL', get_site_url() . '/wp-content' );
	if ( !defined('WP_CONTENT_DIR') )
	    define('WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( !defined('WP_PLUGIN_URL') ) 
		define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins' );
	if ( !defined('WP_PLUGIN_DIR') ) 
		define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins' );
	if ( !defined('PLUGINDIR') )
		define( 'PLUGINDIR', 'wp-content/plugins' ); // Relative to ABSPATH.  For back compat.
		
	if ( !defined('WP_LANG_DIR') )
		define('WP_LANG_DIR', WP_CONTENT_DIR . '/languages' );
		
	//WPMU definitions
	if ( !defined('WPMU_PLUGIN_DIR') )
		define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' ); // full path, no trailing slash
	if ( !defined('WPMU_PLUGIN_URL') )
		define( 'WPMU_PLUGIN_URL', WP_CONTENT_URL . '/mu-plugins' ); // full url, no trailing slash
	if( defined( 'MUPLUGINDIR' ) == false ) 
		define( 'MUPLUGINDIR', 'wp-content/mu-plugins' ); // Relative to ABSPATH.  For back compat.

	define("CSP_PO_PLUGINPATH", "/" . dirname(plugin_basename( __FILE__ )));

    define('CSP_PO_TEXTDOMAIN', 'codestyling-localization' );
    define('CSP_PO_BASE_URL', plugins_url(CSP_PO_PLUGINPATH));
		
	//Bugfix: ensure valid JSON requests at IDN locations!
	//Attention: Google Chrome and Safari behave in different way (shared WebKit issue or all other are wrong?)!
	list($csp_domain, $csp_target) = csp_split_url( ( function_exists("admin_url") ? rtrim(admin_url(), '/') : rtrim(get_site_url().'/wp-admin/', '/') ) );
	define('CSP_SELF_DOMAIN', $csp_domain);
	if (
		stripos($_SERVER['HTTP_USER_AGENT'], 'chrome') !== false 
		|| 
		stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false
		||
		version_compare(phpversion(), '5.2.1', '<') //IDNA class requires PHP 5.2.1 or higher
	) {
		define('CSP_PO_ADMIN_URL', strtolower($csp_domain).$csp_target);
	}
	else{
		if (!class_exists('idna_convert'))
			require_once('includes/idna_convert.class.php' );
		$idn = new idna_convert();
		define('CSP_PO_ADMIN_URL', $idn->decode(strtolower($csp_domain), 'utf8').$csp_target);
	}
	
    define('CSP_PO_BASE_PATH', WP_PLUGIN_DIR . CSP_PO_PLUGINPATH);
	
	define('CSP_PO_MIN_REQUIRED_WP_VERSION', '2.5' );
	define('CSP_PO_MIN_REQUIRED_PHP_VERSION', '4.4.2' );
		
	register_activation_hook(__FILE__, 'csp_po_install_plugin' );

	add_action('plugins_loaded', 'csp_trace_php_errors', 0);
}

function csp_is_multisite() {
	return (
		isset($GLOBALS['wpmu_version'])
		||
		(function_exists('is_multisite') && is_multisite())
		||
		(function_exists('wp_get_mu_plugins') && count(wp_get_mu_plugins()) > 0)
	);
}

if (function_exists('csp_po_install_plugin')) {
	//rewrite and extend the error messages displayed at failed activation
	//fall trough, if it's a real code bug forcing the activation error to get the appropriated message instead
	if (isset($_GET['action']) && isset($_GET['plugin']) && ($_GET['action'] == 'error_scrape') && ($_GET['plugin'] == plugin_basename(__FILE__) )) {
		if (
			(!version_compare($wp_version, CSP_PO_MIN_REQUIRED_WP_VERSION, '>=')) 
			|| 
			(!version_compare(phpversion(), CSP_PO_MIN_REQUIRED_PHP_VERSION, '>='))
			||
			!function_exists('token_get_all')
		) {
			load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
			echo "<table>";
			echo "<tr style=\"font-size: 12px;\"><td><strong style=\"border-bottom: 1px solid #000;\">Codestyling Localization</strong></td><td> | ".__('required', CSP_PO_TEXTDOMAIN)."</td><td> | ".__('actual', CSP_PO_TEXTDOMAIN)."</td></tr>";			
			if (!version_compare($wp_version, CSP_PO_MIN_REQUIRED_WP_VERSION, '>=')) {
				echo "<tr style=\"font-size: 12px;\"><td>WordPress Blog Version:</td><td align=\"center\"> &gt;= <strong>".CSP_PO_MIN_REQUIRED_WP_VERSION."</strong></td><td align=\"center\"><span style=\"color:#f00;\">".$wp_version."</span></td></tr>";
			}
			if (!version_compare(phpversion(), CSP_PO_MIN_REQUIRED_PHP_VERSION, '>=')) {
				echo "<tr style=\"font-size: 12px;\"><td>PHP Interpreter Version:</td><td align=\"center\"> &gt;= <strong>".CSP_PO_MIN_REQUIRED_PHP_VERSION."</strong></td><td align=\"center\"><span style=\"color:#f00;\">".phpversion()."</span></td></tr>";
			}
			if (!function_exists('token_get_all')) {
				echo "<tr style=\"font-size: 12px;\"><td>PHP Tokenizer Module:</td><td align=\"center\"><strong>active</strong></td><td align=\"center\"><span style=\"color:#f00;\">not installed</span></td></tr>";			
			}
			echo "</table>";
		}
	}
}


function csp_po_install_plugin(){
	global $wp_version;
	if (
		(!version_compare($wp_version, CSP_PO_MIN_REQUIRED_WP_VERSION, '>=')) 
		|| 
		(!version_compare(phpversion(), CSP_PO_MIN_REQUIRED_PHP_VERSION, '>='))
		|| 
		!function_exists('token_get_all')
	){
		$current = get_option('active_plugins' );
		array_splice($current, array_search( plugin_basename(__FILE__), $current), 1 );
		update_option('active_plugins', $current);
		exit;
	}
}


//////////////////////////////////////////////////////////////////////////////////////////
//	general purpose methods
//////////////////////////////////////////////////////////////////////////////////////////

function has_subdirs($base='') {
  if (!is_dir($base) || !is_readable($base)) return $false;
  $array = array_diff(scandir($base), array('.', '..'));
  foreach($array as $value) : 
    if (is_dir($base.$value)) return true; 
  endforeach;
  return false;
}

function lscandir($base='', $reg='', &$data) {
  if (!is_dir($base) || !is_readable($base)) return $data;
  $array = array_diff(scandir($base), array('.', '..')); 
  foreach($array as $value) : 
		if (is_file($base.$value) && preg_match($reg, $value) ) : 
			$data[] = str_replace("\\","/",$base.$value); 
		endif;
  endforeach;  
  return $data; 
}

function rscandir($base='', $reg='', &$data) {
  if (!is_dir($base) || !is_readable($base)) return $data;
  $array = array_diff(scandir($base), array('.', '..')); 
  foreach($array as $value) : 
    if (is_dir($base.$value)) : 
      $data = rscandir($base.$value.'/', $reg, $data); 
    elseif (is_file($base.$value) && preg_match($reg, $value) ) : 
      $data[] = str_replace("\\","/",$base.$value); 
    endif;
  endforeach;
  return $data; 
}		

function rscanpath($base='', &$data) {
  if (!is_dir($base) || !is_readable($base)) return $data;
  $array = array_diff(scandir($base), array('.', '..')); 
  foreach($array as $value) : 
    if (is_dir($base.$value)) : 
	  $data[] = str_replace("\\","/",$base.$value);
      $data = rscanpath($base.$value.'/', $data); 
    endif;
  endforeach;
  return $data; 
}		


function rscandir_php($base='', &$exclude_dirs, &$data) {
  if (!is_dir($base) || !is_readable($base)) return $data;
  $array = array_diff(scandir($base), array('.', '..')); 
  foreach($array as $value) : 
    if (is_dir($base.$value)) : 
      if (!in_array($base.$value, $exclude_dirs)) : $data = rscandir_php($base.$value.'/', $exclude_dirs, $data); endif; 
    elseif (is_file($base.$value) && preg_match('/\.(php|phtml)$/', $value) ) : 
      $data[] = str_replace("\\","/",$base.$value); 
    endif;
  endforeach;
  return $data; 
}		

function file_permissions($filename) {
	static $R = array("---","--x","-w-","-wx","r--","r-x","rw-","rwx");
	$perm_o	= substr(decoct(fileperms( $filename )),3);
	return "[".$R[(int)$perm_o[0]] . '|' . $R[(int)$perm_o[1]] . '|' . $R[(int)$perm_o[2]]."]";
}

function csp_fetch_remote_content($url) {
	global $wp_version;
	$res = null;
	
	if(file_exists(ABSPATH . 'wp-includes/class-snoopy.php') && version_compare($wp_version, '3.0', '<')) {
		require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
		$s = new Snoopy();
		$s->fetch($url);	
		if($s->status == 200) {
			$res = $s->results;	
		}
	} else {
		$res = wp_remote_fopen($url);	
	}
	return $res;	
}

function csp_po_check_security() {
	if (!is_user_logged_in() || !current_user_can('manage_options')) {
		wp_die(__('You do not have permission to manage translation files.', CSP_PO_TEXTDOMAIN));
	}
}

function csp_find_translation_template(&$files) {
	$result = null;
	foreach($files as $tt) {
		if (preg_match('/\.pot$/',$tt)) {
			$result = $tt;
		}
	}
	return $result;
}

function csp_po_get_wordpress_capabilities() {
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;
	$data['locale'] = get_locale();
	$data['type'] = 'wordpress';
	$data['img_type'] = 'wordpress';
	if (csp_is_multisite()) $data['img_type'] .= "_mu";
	$data['type-desc'] = __('WordPress',CSP_PO_TEXTDOMAIN);
	$data['name'] = "WordPress";
	$data['author'] = "<a href=\"http://codex.wordpress.org/WordPress_in_Your_Language\">WordPress.org</a>";
	$data['version'] = $GLOBALS['wp_version'];
	if (csp_is_multisite()) $data['version'] .= " | ".(isset($GLOBALS['wpmu_version']) ? $GLOBALS['wpmu_version'] : $GLOBALS['wp_version']);
	$data['description'] = "WordPress is a state-of-the-art publishing platform with a focus on aesthetics, web standards, and usability. WordPress is both free and priceless at the same time.<br />More simply, WordPress is what you use when you want to work with your blogging software, not fight it.";
	$data['status'] =  __("activated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", ABSPATH);
	$data['special_path'] = '';
	$data['filename'] = str_replace(str_replace("\\","/",ABSPATH), '', str_replace("\\","/",WP_LANG_DIR));
	$data['is-simple'] = false;
	$data['simple-filename'] = '';
	$data['textdomain'] = array('identifier' => 'default', 'is_const' => false );
	$data['languages'] = array();
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = true;
	$data['translation_template'] = null;
	$tmp = array();
	$data['is_US_Version'] = !is_dir(WP_LANG_DIR);
	if (!$data['is_US_Version']) {
		$files = rscandir(str_replace("\\","/",WP_LANG_DIR).'/', "/(.\mo|\.po|\.pot)$/", $tmp);
		$data['translation_template'] = csp_find_translation_template($files);
		foreach($files as $filename) {
			$file = str_replace(str_replace("\\","/",WP_LANG_DIR).'/', '', $filename);
			preg_match("/^([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);
			if (empty($hits[1]) === false) {
				$data['languages'][$hits[1]][$hits[2]] = array(
					'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
					'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
				);
				$data['special_path'] = '';
			}
		}

		$data['base_file'] = (empty($data['special_path']) ? '' : $data['special_path'].'/') . $data['filename'].'/';
	}
	return $data;
}

function csp_po_get_plugin_capabilities($plug, $values) {
	$data = array();
	$data['dev-hints'] 		= null;
	$data['dev-security'] 	= null;
	$data['deny_scanning'] 	= false;
	$data['locale'] = get_locale();
	$data['type'] = 'plugins';	
	$data['img_type'] = 'plugins';	
	$data['type-desc'] = __('Plugin',CSP_PO_TEXTDOMAIN);	
	$data['name'] = $values['Name'];
	if (isset($values['AuthorURI'])) {
		$data['author'] = "<a href='".$values['AuthorURI']."'>".$values['Author']."</a>";
	}else{
		$data['author'] = $values['Author'];
	}
	$data['version'] = $values['Version'];
	$data['description'] = $values['Description'];
	$data['status'] = is_plugin_active($plug) ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", WP_PLUGIN_DIR.'/'.dirname($plug).'/' );
	$data['special_path'] = '';
	$data['filename'] = "";
	$data['is-simple'] = (dirname($plug) == '.' );
	$data['simple-filename'] = '';
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = false;
	$data['translation_template'] = null;
	if ($data['is-simple']) {
		$files = array(WP_PLUGIN_DIR.'/'.$plug);
		$data['simple-filename'] = str_replace("\\","/",WP_PLUGIN_DIR.'/'.$plug);
		$data['base_path'] = str_replace("\\","/", WP_PLUGIN_DIR.'/' );
	}
	else{
		$tmp = array();
		$files = rscandir(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug)."/", "/.(php|phtml)$/", $tmp);
	}
	$const_list = array();
	foreach($files as $file) {	
		$content = file_get_contents($file);
		if (preg_match("/[^_^!]load_(|plugin_)textdomain\s*\(\s*(\'|\"|)([\w\d\-_]+|[A-Z\d\-_]+)(\'|\"|)\s*(,|\))\s*([^;]+)\)/", $content, $hits)) {
			$data['textdomain'] = array('identifier' => $hits[3], 'is_const' => empty($hits[2]) );
			$data['gettext_ready'] = true;
			$data['php-path-string'] = $hits[6];
		}
		else if(preg_match("/[^_^!]load_(|plugin_)textdomain\s*\(/", $content, $hits)) {
			//ATTENTION: it is gettext ready but we don't realy know the textdomain name! Assume it's equal to plugin's name.
			//TODO: let's think about it in future to find a better solution.
			$data['textdomain'] = array('identifier' => substr(basename($plug),0,-4), 'is_const' => false );
			$data['gettext_ready'] = true;
			$data['php-path-string'] = '';	
		}
		if (isset($hits[1]) && $hits[1] != 'plugin_') 	$data['dev-hints'] = __("<strong>Loading Issue: </strong>Author is using <em>load_textdomain</em> instead of <em>load_plugin_textdomain</em> function. This may break behavior of WordPress, because some filters and actions won't be executed anymore. Please contact the Author about that.",CSP_PO_TEXTDOMAIN);
		if($data['gettext_ready'] && !$data['textdomain']['is_const']) break; //make it short :-)
		if (preg_match_all("/define\s*\(([^\)]+)\)/" , $content, $hits)) {
			$const_list = array_merge($const_list, $hits[1]);
		}
	}
	if ($data['gettext_ready']) {
		
		if ($data['textdomain']['is_const']) {
			foreach($const_list as $e) {
				$a = explode(',', $e);
				$c = trim($a[0], "\"' \t");
				if ($c == $data['textdomain']['identifier']) {
					$data['textdomain']['is_const'] = $data['textdomain']['identifier'];
					$data['textdomain']['identifier'] = trim($a[1], "\"' \t");
				}
			}
		}
		$data['filename'] = $data['textdomain']['identifier'];
		//check if const contains brackets, mostly by functional defined const
		if(preg_match("/(\(|\))/", $data['textdomain']['identifier'])) {
			$data['filename'] = str_replace('.php', '', basename($plug));
			$data['textdomain']['is_const'] = false;
			$data['textdomain']['identifier'] = str_replace('.php', '', basename($plug));
			//var_dump(str_replace('.php', '', basename($plug)));
		}
	}		
	
	if (!$data['gettext_ready']) {
		//lets check, if the plugin is a encrypted one could be translated or an unknow but with defined textdomain
		//ATTENTION: mark encrypted plugins as a high security risk!!!
		if (isset($values['TextDomain']) && !empty($values['TextDomain'])) {
			$data['textdomain'] = array('identifier' => $values['TextDomain'], 'is_const' => false );
			$data['gettext_ready'] = true;
			$data['filename'] = $data['textdomain']['identifier'];
			
			$inside = token_get_all(file_get_contents(WP_PLUGIN_DIR."/".$plug));
			$encrypted = false;
			foreach($inside as $token) {
				if (is_array($token)) {
					list($id, $text) = $token;
					if (T_EVAL == $id) {
						$encrypted =true;
						break;
					}
				}
			}
			if($encrypted) {
				$data['img_type'] = 'plugins_encrypted';
				$data['dev-security'] .= __("<strong>Full Encryped PHP Code: </strong>This plugin consists out of encryped code will be <strong>eval</strong>'d at runtime! It can't be checked against exploitable code pieces. That's why it will become potential target of hidden intrusion.",CSP_PO_TEXTDOMAIN);
				$data['deny_scanning'] = true;
			}
			else {
				$data['img_type'] = 'plugins_maybe';
				$data['dev-hints'] .= __("<strong>Textdomain definition: </strong>This plugin provides a textdomain definition at plugin header fields but seems not to load any translation file. If it doesn't show your translation, please contact the plugin Author.",CSP_PO_TEXTDOMAIN);
			}
		}
	}
	
	$data['languages'] = array();
	if($data['gettext_ready']){
		if ($data['is-simple']) { $tmp = array(); $files = lscandir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/', "/(\.mo|\.po|\.pot)$/", $tmp); }
		else { 	$tmp = array(); $files = rscandir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/', "/(\.mo|\.po|\.pot)$/", $tmp); }
		$data['translation_template'] = csp_find_translation_template($files);
			
		if ($data['is-simple']) { //simple plugin case
			//1st - try to find the assumed one files
			foreach($files as $filename) {
				$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
				preg_match("/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
				if (empty($hits[2]) === false) {				
					$data['languages'][$hits[1]][$hits[2]] = array(
						'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
						'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
					);
					$data['special_path'] = '';
				}
			}
			//2nd - try to re-construct, if nessessary, avoid multi textdomain issues
			if(count($data['languages']) == 0) {
				foreach($files as $filename) {
					//bugfix: uppercase filenames supported
					preg_match("/([A-Za-z0-9\-_]+)-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
					if (empty($hits[2]) === false) {				
						$data['filename'] = $hits[1];
						$data['textdomain']['identifier'] = $hits[1];
						$data['img_type'] = 'plugins_maybe';
						$data['dev-hints'] .= __("<strong>Textdomain definition: </strong>There are problems to find the used textdomain. It has been taken from existing translation files. If it doesn't work with your install, please contact the Author of this plugin.",CSP_PO_TEXTDOMAIN);
						
						$data['languages'][$hits[2]][$hits[3]] = array(
							'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
							'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
						);
						$data['special_path'] = '';
					}
				}
			}
		}
		else { //complex plugin case
			//1st - try to find the assumed one files
			foreach($files as $filename) {
				$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
				//bugfix: uppercase folders supported
				preg_match("/([\/A-Za-z0-9\-_]*)\/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
				if (empty($hits[2]) === false) {
					//bugfix: only accept those mathing known textdomain
					if ($data['textdomain']['identifier'] == $data['filename'])
					{
						$data['languages'][$hits[2]][$hits[3]] = array(
							'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
							'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
						);
					}
					$data['special_path'] = ltrim($hits[1], "/");
				}
			}
			//2nd - try to re-construct, if nessessary, avoid multi textdomain issues
			if(count($data['languages']) == 0) {
				foreach($files as $filename) {
					//try to re-construct from real file.
					//bugfix: uppercase folders supported, additional uppercased filenames!
					preg_match("/([\/A-Za-z0-9\-_]*)\/([\/A-Za-z0-9\-_]+)-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
					if (empty($hits[3]) === false) {
						$data['filename'] = $hits[2];
						$data['textdomain']['identifier'] = $hits[2];
						$data['img_type'] = 'plugins_maybe';
						$data['dev-hints'] .= __("<strong>Textdomain definition: </strong>There are problems to find the used textdomain. It has been taken from existing translation files. If it doesn't work with your install, please contact the Author of this plugin.",CSP_PO_TEXTDOMAIN);

						$data['languages'][$hits[3]][$hits[4]] = array(
							'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
							'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
						);
						$data['special_path'] = ltrim($hits[1], "/");
					}
				}			
			}
		}
		if (!$data['is-simple'] && ($data['special_path'] == '') && (count($data['languages']) == 0)) {
			$data['is-path-unclear'] = has_subdirs(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/' );
			if ($data['is-path-unclear'] && (count($files) > 0)) {
				$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $files[0]);
				//bugfix: uppercase folders supported
				preg_match("/^\/([\/A-Za-z0-9\-_]*)\//", $file, $hits);
				$data['is-path-unclear'] = false;
				if (empty($hits[1]) === false) { $data['special_path'] = $hits[1]; }
			}
		}
		//supporting the plugins suggestion for language path
		if ($data['is-path-unclear'] && isset($values['DomainPath']) && is_dir(dirname(WP_PLUGIN_DIR.'/'.$plug).'/'.trim($values['DomainPath'], "\\/")) )
		{
			$data['is-path-unclear'] = false;
			$data['special_path'] = trim($values['DomainPath'], "\\/");		
		}

		//DEBUG:  $data['php-path-string']  will contain real path part like: "false,'codestyling-localization'" | "'wp-content/plugins/' . NGGFOLDER . '/lang'" | "GENGO_LANGUAGES_DIR" | "$moFile"
		//this may be part of later excessive parsing to find correct lang file path even if no lang files exist as hint or implementation of directory selector, if 0 languages contained
		//if any lang files may be contained the qualified sub path will be extracted out of
		//will be handled in case of  $data['is-path-unclear'] == true by display of treeview at file creation dialog 
		//var_dump($data['php-path-string']);

	}
	$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/".$data['filename']).'-';	
	return $data;
}

function csp_po_get_plugin_mu_capabilities($plug, $values){
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;
	$data['locale'] = get_locale();
	$data['type'] = 'plugins_mu';	
	$data['img_type'] = 'plugins_mu';	
	$data['type-desc'] = __('μ Plugin',CSP_PO_TEXTDOMAIN);	
	$data['name'] = $values['Name'];
	if (isset($values['AuthorURI'])) {
		$data['author'] = "<a href='".$values['AuthorURI']."'>".$values['Author']."</a>";
	}else{
		$data['author'] = $values['Author'];
	}
	$data['version'] = $values['Version'];
	$data['description'] = $values['Description'];
	$data['status'] = __("activated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", WPMU_PLUGIN_DIR.'/' );
	$data['special_path'] = '';
	$data['filename'] = "";
	$data['is-simple'] = true;
	$data['simple-filename'] = str_replace("\\","/",WPMU_PLUGIN_DIR.'/'.$plug); 
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = false;
	$data['translation_template'] = null;
	$file = WPMU_PLUGIN_DIR.'/'.$plug;

	$const_list = array();
	$content = file_get_contents($file);
	if (preg_match("/[^_^!]load_(|plugin_|muplugin_)textdomain\s*\(\s*(\'|\"|)([\w\d\-_]+|[A-Z\d\-_]+)(\'|\"|)\s*(,|\))\s*([^;]+)\)/", $content, $hits)) {
		$data['textdomain'] = array('identifier' => $hits[3], 'is_const' => empty($hits[2]) );
		$data['gettext_ready'] = true;
		$data['php-path-string'] = $hits[6];
	}
	else if(preg_match("/[^_^!]load_(|plugin_|muplugin_)textdomain\s*\(/", $content, $hits)) {
		//ATTENTION: it is gettext ready but we don't realy know the textdomain name! Assume it's equal to plugin's name.
		//TODO: let's think about it in future to find a better solution.
		$data['textdomain'] = array('identifier' => substr(basename($plug),0,-4), 'is_const' => false );
		$data['gettext_ready'] = true;
		$data['php-path-string'] = '';			
	}
	if (!($data['gettext_ready'] && !$data['textdomain']['is_const'])) {
		if (preg_match_all("/define\s*\(([^\)]+)\)/" , $content, $hits)) {
			$const_list = array_merge($const_list, $hits[1]);
		}
	}

	if ($data['gettext_ready']) {
		
		if ($data['textdomain']['is_const']) {
			foreach($const_list as $e) {
				$a = split(',', $e);
				$c = trim($a[0], "\"' \t");
				if ($c == $data['textdomain']['identifier']) {
					$data['textdomain']['is_const'] = $data['textdomain']['identifier'];
					$data['textdomain']['identifier'] = trim($a[1], "\"' \t");
				}
			}
		}
		$data['filename'] = $data['textdomain']['identifier'];
	}		
	
	$data['languages'] = array();
	if($data['gettext_ready']){
		$tmp = array(); $files = lscandir(str_replace("\\","/",dirname(WPMU_PLUGIN_DIR.'/'.$plug)).'/', "/(\.mo|\.po|\.pot)$/", $tmp); 		
		$data['translation_template'] = csp_find_translation_template($files);
		foreach($files as $filename) {
			$file = str_replace(str_replace("\\","/",WPMU_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
			preg_match("/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);		
			if (empty($hits[2]) === false) {				
				$data['languages'][$hits[1]][$hits[2]] = array(
					'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
					'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
				);
				$data['special_path'] = '';
			}
		}
	}
	$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/".$data['filename']).'-';		
	return $data;
}

function csp_po_get_theme_capabilities($theme, $values, $active) {
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;

	//let's first check the whether we have a child or base theme
	if(is_object($values) && get_class($values) == 'WP_Theme') {
		//WORDPRESS Version 3.4 changes theme handling!
		$theme_root = trailingslashit(str_replace("\\","/", get_theme_root()));
		$firstfile = array_values($values['Template Files']);
		$firstfile = array_shift($firstfile);
		$firstfile = str_replace("\\","/", $firstfile);
		$firstfile = str_replace($theme_root, '', $firstfile);
		$firstfile = explode('/',$firstfile);
		$firstfile = reset($firstfile);
		$data['base_path'] = $theme_root.$firstfile.'/';
	}else{
		$data['base_path'] = str_replace("\\","/", WP_CONTENT_DIR.str_replace('wp-content', '', dirname($values['Template Files'][0])).'/' );
		if (file_exists($values['Template Files'][0])){
			$data['base_path'] = dirname(str_replace("\\","/",$values['Template Files'][0])).'/';
		}
	}
	$fc = explode('/',untrailingslashit($data['base_path']));
	$folder_filesys = end($fc);
	$folder_data = $values['Template']; 
	$is_child_theme = $folder_filesys != $folder_data;
	$data['theme-self'] = $folder_filesys;
	$data['theme-template'] = $folder_data;
	
	$data['locale'] = get_locale();
	$data['type'] = 'themes';
	$data['img_type'] = ($is_child_theme ? 'childthemes' : 'themes' );	
	$data['type-desc'] = ($is_child_theme ? __('Childtheme',CSP_PO_TEXTDOMAIN) : __('Theme',CSP_PO_TEXTDOMAIN));	
	$data['name'] = $values['Name'];
	$data['author'] = $values['Author'];
	$data['version'] = $values['Version'];
	$data['description'] = $values['Description'];
	$data['status'] = $values['Name'] == $active->name ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
//	$data['status'] = $theme == $active->name ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
	if ($is_child_theme) {
		$data['status'] .= ' / <b></i>'.__('child theme of',CSP_PO_TEXTDOMAIN).' '.$values['Parent Theme'].'</i></b>';
	}
	$data['special-path'] = '';
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = false;
	$data['translation_template'] = null;
	$data['is-simple'] = false;
	$data['simple-filename'] = '';
	
	//now scanning the child's own files
	$parent_files = array();
	$files = array();
	$const_list = array();
	$tmp = array();
	$files = rscandir($data["base_path"], "/\.(php|phtml)$/", $tmp);
	foreach($files as $themefile) {
		$main = file_get_contents($themefile);
		if (
			preg_match("/[^_^!]load_(child_theme_|theme_|)textdomain\s*\(\s*(\'|\"|)([\w\d\-_]+|[A-Z\d\-_]+)(\'|\"|)\s*(,|\))/", $main, $hits)
			||
			preg_match("/[^_^!]load_(child_theme_|theme_|)textdomain\s*\(\s*/", $main, $hits)			
		) {
			if (isset($hits[1]) && $hits[1] != 'child_theme_' && $hits[1] != 'theme_') 	$data['dev-hints'] = __("<strong>Loading Issue: </strong>Author is using <em>load_textdomain</em> instead of <em>load_theme_textdomain</em> or <em>load_child_theme_textdomain</em> function. This may break behavior of WordPress, because some filters and actions won't be executed anymore. Please contact the Author about that.",CSP_PO_TEXTDOMAIN);
		
			//fallback for variable names used to load textdomain, assumes theme name
			if(isset($hits[3]) && strpos($hits[3], '$') !== false) {
				unset($hits[3]);
				if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
				$data['dev-hints'] = __("<strong>Textdomain Naming Issue: </strong>Author uses a variable to load the textdomain. It will be assumed to be equal to theme name now.",CSP_PO_TEXTDOMAIN);
			}			
			//make it short
			$data['gettext_ready'] = true;
			if ($data['gettext_ready']) {
				if (!isset($hits[3])) {
					$data['textdomain'] = array('identifier' => $values['Template'], 'is_const' => false );
				}else {
					$data['textdomain'] = array('identifier' => $hits[3], 'is_const' => empty($hits[2]) );
				}
				$data['languages'] = array();
			}

			$dn = $data["base_path"];
			$tmp = array();
			$lng_files = rscandir($dn, "/(\.mo|\.po|\.pot)$/", $tmp);
			$data['translation_template'] = csp_find_translation_template($lng_files);
			$sub_dirs = array();
			$naming_convention_error = false;
			foreach($lng_files as $filename) {
				//somebody did place buddypress themes at sub folder hierarchy like:  themes/buddypress/bp-default
				//results at $values['Template'] to 'buddypress/bp-default' which damages the preg_match
				$v = explode('/',$values['Template']);
				$theme_langfile_check = end($v);
				preg_match("/\/(|".preg_quote($theme_langfile_check)."\-)([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $filename, $hits);
				if (empty($hits[1]) === false) {
					$naming_convention_error = true;

					$data['filename'] = '';
					$sd = dirname(str_replace($dn, '', $filename));
					if ($sd == '.') $sd = '';
					if (!in_array($sd, $sub_dirs)) $sub_dirs[] = $sd;
					
				}elseif (empty($hits[2]) === false) {
					$data['languages'][$hits[2]][$hits[3]] = array(
						'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
						'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
					);
					$data['filename'] = '';
					$sd = dirname(str_replace($dn, '', $filename));
					if ($sd == '.') $sd = '';
					if (!in_array($sd, $sub_dirs)) $sub_dirs[] = $sd;
				}
			}
			if($naming_convention_error && count($data['languages']) == 0) {
				if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
				$data['dev-hints'] .= sprintf(__("<strong>Naming Issue: </strong>Author uses unsupported language file naming convention! Instead of example <em>de_DE.po</em> the non theme standard version <em>%s</em> has been used. If you translate this Theme, only renamed language files will be working!",CSP_PO_TEXTDOMAIN), $values['Template'].'-de_DE.po' );
			}
			
			//completely other directories can be defined WP if >= 2.7.0
			global $wp_version;
			if (version_compare($wp_version, '2.7', '>=')) {
				if (count($data['languages']) == 0) {
					$data['is-path-unclear'] = has_subdirs($dn);
					if ($data['is-path-unclear'] && (count($lng_files) > 0)) {
						foreach($lng_files as $file) {
							$f = str_replace($dn, '', $file);
							if (
								preg_match("/^([a-z][a-z]_[A-Z][A-Z])\.(mo|po|pot)$/", basename($f))
								||
								preg_match("/\.po(t|)$/", basename($f))
							) {
								$data['special_path'] = (dirname($f) == '.' ? '' : dirname($f));
								$data['is-path-unclear'] = false;
								break;
							}
						}
					}
				}
				else{
					if ($sub_dirs[0] != '') {
						$data['special_path'] = ltrim($sub_dirs[0], "/");
					}
				}
			}

		}
		if($data['gettext_ready'] && !$data['textdomain']['is_const']) break; //make it short :-)
		if (preg_match_all("/define\s*\(([^\)]+)\)/" , $main, $hits)) {
			$const_list = array_merge($const_list, $hits[1]);
		}
	}
	$data['base_file'] = (empty($data['special_path']) ? '' : $data['special_path']."/");

	$constant_failed = false;
	if ($data['gettext_ready']) {	
		if ($data['textdomain']['is_const']) {
			foreach($const_list as $e) {
				$a = explode(',', $e);
				$c = trim($a[0], "\"' \t");
				if ($c == $data['textdomain']['identifier']) {
					$data['textdomain']['is_const'] = $data['textdomain']['identifier'];
					$data['textdomain']['identifier'] = trim($a[1], "\"' \t");
				}
			}
		}
		
		//fallback for constants defined by variables! assume the theme name instead
		if(
			(strpos($data['textdomain']['identifier'], '$') !== false) 
			||
			(strpos($data['textdomain']['identifier'], '"') !== false)
			||
			(strpos($data['textdomain']['identifier'], '\'') !== false)
		){
			$constant_failed = true;
			$data['textdomain']['identifier'] = $values['Template'];
			if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
			$data['dev-hints'] = __("<strong>Textdomain Naming Issue: </strong>Author uses a variable to define the textdomain constant. It will be assumed to be equal to theme name now.",CSP_PO_TEXTDOMAIN);
		}			

	}		
	//check now known issues for themes
	if(isset($data['textdomain']['identifier']) && $data['textdomain']['identifier'] == 'woothemes') {
		if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
		$data['dev-hints'] .= __("<strong>WooThemes Issue: </strong>The Author is known for not supporting a translatable backend. Please expect only translations for frontend or contact the Author for support!",CSP_PO_TEXTDOMAIN);
	}
	if(isset($data['textdomain']['identifier']) && $data['textdomain']['identifier'] == 'ares' && $constant_failed) {
		if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
		$data['dev-hints'] .= __("<strong>Ares Theme Issue: </strong>This theme uses a textdomain defined by string concatination code. The textdomain will be patched to 'AresLanguage', please contact the theme author to change this into a fix constant value! ",CSP_PO_TEXTDOMAIN);
		$data['textdomain']['identifier'] = 'AresLanguage';
	}
	
	
	return $data;
}

function csp_po_get_buddypress_capabilities($plug, $values) {
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;
	$data['locale'] = get_locale();
	$data['type'] = 'plugins';	
	$data['img_type'] = 'buddypress';	
	$data['type-desc'] = __('BuddyPress',CSP_PO_TEXTDOMAIN);	
	$data['name'] = $values['Name'];
	if (isset($values['AuthorURI'])) {
		$data['author'] = "<a href='".$values['AuthorURI']."'>".$values['Author']."</a>";
	}else{
		$data['author'] = $values['Author'];
	}
	$data['version'] = $values['Version'];
	$data['description'] = $values['Description'];
	$data['status'] = is_plugin_active($plug) ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", WP_PLUGIN_DIR.'/'.dirname($plug).'/' );
	$data['special_path'] = '';
	$data['filename'] = "buddypress";
	$data['is-simple'] = false;
	$data['simple-filename'] = '';
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = true;	
	$data['translation_template'] = null;
	$data['textdomain'] = array('identifier' => 'buddypress', 'is_const' => false );
	$data['special_path'] = 'bp-languages';
	$data['languages'] = array();
	$tmp = array(); 
	$files = lscandir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/bp-languages/', "/(\.mo|\.po|\.pot)$/", $tmp); 
	$data['translation_template'] = csp_find_translation_template($files);
	foreach($files as $filename) {
		$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
		preg_match("/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);		
		if (empty($hits[2]) === false) {				
			$data['languages'][$hits[1]][$hits[2]] = array(
				'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
				'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
			);
		}
	}
	$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/".$data['filename']).'-';	
	return $data;
}

function csp_po_get_bbpress_on_buddypress_capabilities($plug, $values) {
	$data = array();
	$data['dev-hints'] = null;
	$data['deny_scanning'] = false;
	$data['locale'] = get_locale();
	$data['type'] = 'plugins';	
	$data['img_type'] = 'buddypress-bbpress';	
	$data['type-desc'] = __('bbPress',CSP_PO_TEXTDOMAIN);	
	$data['name'] = "bbPress";
	$data['author'] = "<a href='http://bbpress.org/'>bbPress.org</a>";
	$data['version'] = '-n.a.-';
	$data['description'] = "bbPress is forum software with a twist from the creators of WordPress.";
	$data['status'] = is_plugin_active($plug) ? __("activated",CSP_PO_TEXTDOMAIN) : __("deactivated",CSP_PO_TEXTDOMAIN);
	$data['base_path'] = str_replace("\\","/", WP_PLUGIN_DIR.'/'.dirname($plug).'/bp-forums/bbpress/' );
	if (!is_dir($data['base_path'])) return false;
	$data['special_path'] = '';
	$data['filename'] = "";
	$data['is-simple'] = false;
	$data['simple-filename'] = '';
	$data['is-path-unclear'] = false;
	$data['gettext_ready'] = true;	
	$data['translation_template'] = null;
	$data['textdomain'] = array('identifier' => 'default', 'is_const' => false );
	$data['special_path'] = 'my-languages';
	$data['languages'] = array();
	$data['is_US_Version'] = !is_dir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/bp-forums/bbpress/my-languages' );
	if (!$data['is_US_Version']) {	
		$tmp = array(); 	
		$files = lscandir(str_replace("\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/bp-forums/bbpress/my-languages/', "/(\.mo|\.po|\.pot)$/", $tmp); 
		$data['translation_template'] = csp_find_translation_template($files);
		foreach($files as $filename) {
			$file = str_replace(str_replace("\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
			preg_match("/([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);		
			if (empty($hits[2]) === false) {				
				$data['languages'][$hits[1]][$hits[2]] = array(
					'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
					'stamp' => date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename)
				);
			}
		}
	}
	$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/");	
	return $data;
}


function csp_po_collect_by_type($type){
	$res = array();
	$do_compat_filter = ($type == 'compat' );
	$do_security_filter = ($type == 'security' );
	if ($do_compat_filter || $do_security_filter) $type = '';
	if (empty($type) || ($type == 'wordpress')) {
		if (!$do_compat_filter && !$do_security_filter)
			$res[] = csp_po_get_wordpress_capabilities();
	}
	if (empty($type) || ($type == 'plugins')) {
		//WARNING: Plugin handling is not well coded by WordPress core
		$err = error_reporting(0);
		$plugs = get_plugins(); 
		error_reporting($err);
		$textdomains = array();
		foreach($plugs as $key => $value) { 
			$data = null;
			if (dirname($key) == 'buddypress') {
				if ($do_compat_filter || $do_security_filter) continue;
				$data = csp_po_get_buddypress_capabilities($key, $value);
				$res[] = $data;
				$data = csp_po_get_bbpress_on_buddypress_capabilities($key, $value);
				if($data !== false) $res[] = $data;
			}else {
				$data = csp_po_get_plugin_capabilities($key, $value);
				if (!$data['gettext_ready']) continue;
				if (in_array($data['textdomain'], $textdomains)) {
					for ($i=0; $i<count($res); $i++) {
						if ($data['textdomain'] == $res[$i]['textdomain']) {
							$res[$i]['child-plugins'][] = $data;
							break;
						}
					}
				}
				else{
					if ($do_compat_filter && !isset($data['dev-hints'])) continue;
					elseif ($do_security_filter && !isset($data['dev-security'])) continue;
					array_push($textdomains, $data['textdomain']);
					$res[] = $data;
				}
			}
		}
	}
	if (csp_is_multisite()) {
		if (empty($type) || ($type == 'plugins_mu')) {
			$plugs = array();
			$textdomains = array();
			if( is_dir( WPMU_PLUGIN_DIR ) ) {
				if( $dh = opendir( WPMU_PLUGIN_DIR ) ) {
					while( ( $plugin = readdir( $dh ) ) !== false ) {
						if( substr( $plugin, -4 ) == '.php' ) {
							$plugs[$plugin] = get_plugin_data( WPMU_PLUGIN_DIR . '/' . $plugin );
						}
					}
				}
			}		
			foreach($plugs as $key => $value) { 
				$data = csp_po_get_plugin_mu_capabilities($key, $value);
				if (!$data['gettext_ready']) continue;
				if ($do_compat_filter && !isset($data['dev-hints'])) continue;
				elseif ($do_security_filter && !isset($data['dev-security'])) continue;
				if (in_array($data['textdomain'], $textdomains)) {
					for ($i=0; $i<count($res); $i++) {
						if ($data['textdomain'] == $res[$i]['textdomain']) {
							$res[$i]['child-plugins'][] = $data;
							break;
						}
					}
				}
				else{
					if ($do_compat_filter && !isset($data['dev-hints'])) continue;
					elseif ($do_security_filter && !isset($data['dev-security'])) continue;
					array_push($textdomains, $data['textdomain']);
					$res[] = $data;
				}
			}
		}
	}
	if (empty($type) || ($type == 'themes')) {
		$themes = function_exists('wp_get_themes') ? wp_get_themes() : get_themes();
		//WARNING: Theme handling is not well coded by WordPress core
		$err = error_reporting(0);
		$ct = function_exists('wp_get_theme') ? wp_get_theme() : current_theme_info();
		error_reporting($err);
		foreach($themes as $key => $value) { 
			$data = csp_po_get_theme_capabilities($key, $value, $ct);
			if (!$data['gettext_ready']) continue;
			if ($do_compat_filter && !isset($data['dev-hints'])) continue;
			elseif ($do_security_filter && !isset($data['dev-security'])) continue;
			$res[] = $data;
		}	
	}
	return $res;
}

//////////////////////////////////////////////////////////////////////////////////////////
//	Admin Ajax Handler
//////////////////////////////////////////////////////////////////////////////////////////

if (function_exists('add_action')) {
	add_action('wp_ajax_csp_po_dlg_new', 'csp_po_ajax_handle_dlg_new' );
	add_action('wp_ajax_csp_po_dlg_delete', 'csp_po_ajax_handle_dlg_delete' );
	add_action('wp_ajax_csp_po_dlg_rescan', 'csp_po_ajax_handle_dlg_rescan' );
	add_action('wp_ajax_csp_po_dlg_show_source', 'csp_po_ajax_handle_dlg_show_source' );
	
	add_action('wp_ajax_csp_po_merge_from_maintheme', 'csp_po_ajax_handle_merge_from_maintheme' );
	add_action('wp_ajax_csp_po_create', 'csp_po_ajax_handle_create' );
	add_action('wp_ajax_csp_po_destroy', 'csp_po_ajax_handle_destroy' );
	add_action('wp_ajax_csp_po_scan_source_file', 'csp_po_ajax_handle_scan_source_file' );	
	add_action('wp_ajax_csp_po_change_low_memory_mode', 'csp_po_ajax_csp_po_change_low_memory_mode' );
	add_action('wp_ajax_csp_po_change_translate_api', 'csp_po_ajax_change_translate_api' );
	add_action('wp_ajax_csp_po_change_permission', 'csp_po_ajax_handle_change_permission' );
	add_action('wp_ajax_csp_po_launch_editor', 'csp_po_ajax_handle_launch_editor' );
	add_action('wp_ajax_csp_po_translate_by_google', 'csp_po_ajax_handle_translate_by_google' );
	add_action('wp_ajax_csp_po_translate_by_microsoft', 'csp_po_ajax_handle_translate_by_microsoft' );
	add_action('wp_ajax_csp_po_save_catalog_entry', 'csp_po_ajax_handle_save_catalog_entry' );
	add_action('wp_ajax_csp_po_generate_mo_file', 'csp_po_ajax_handle_generate_mo_file' );
	add_action('wp_ajax_csp_po_create_language_path', 'csp_po_ajax_handle_create_language_path' );
	add_action('wp_ajax_csp_po_create_pot_indicator', 'csp_po_ajax_handle_create_pot_indicator' );

	add_action('wp_ajax_csp_self_protection_result', 'csp_handle_csp_self_protection_result' );
}

//WP 2.7 help extensions
//TODO: doesn't work as expected beginning at WP 3.0 (object now!) and never gets called while already object skipps filtering!
function csp_po_filter_screen_meta_screen($screen) {
	if (preg_match('/codestyling-localization$/', $screen)) return "codestyling-localization";
	return $screen;
}

//WP 2.7 help extensions
function csp_po_filter_help_list_filter($_wp_contextual_help) {

	global $wp_version;
	if (version_compare($wp_version, '3', '<')) {

		require_once(ABSPATH.'/wp-includes/rss.php' );
		$rss = fetch_rss('http://www.code-styling.de/online-help/plugins.php?type=config&locale='.get_locale().'&plug=codestyling-localization' );	
		if ( $rss ) {
			$_wp_contextual_help['codestyling-localization'] = '';
			foreach ($rss->items as $item ) {
				if ($item['category'] == 'thickbox') {
					$_wp_contextual_help['codestyling-localization'] .= '<a href="'. $item['link'] . '&amp;TB_iframe=true" class="thickbox" name="<strong>'. $item['title'] . '</strong>">'. $item['title'] . '</a> | ';
				} else {
					$_wp_contextual_help['codestyling-localization'] .= '<a target="_blank" href="'. $item['link'] . '" >'. $item['title'] . '</a> | ';
				}
			}
		}
		
	} else {
	
		//TODO: WP 3.0 introduces only accepts the new classes without depreciate, furthermore the screen key is handled different now (see function above!)
		require_once(ABSPATH.'/wp-includes/feed.php' );
		$rss = fetch_feed('http://www.code-styling.de/online-help/plugins.php?type=config&locale='.get_locale().'&plug=codestyling-localization' );
		if ( $rss && !is_wp_error($rss)) {
			$_wp_contextual_help['tools_page_codestyling-localization/codestyling-localization'] = '';
			foreach ($rss->get_items(0, 9999) as $item ) {		
				$cat = $item->get_category();
				if ($cat->get_term() == 'thickbox') {
					$_wp_contextual_help['tools_page_codestyling-localization/codestyling-localization'] .= '<a href="'. $item->get_link() . '&amp;TB_iframe=true" class="thickbox" name="<strong>'. $item->get_title() . '</strong>">'. $item->get_title() . '</a> | ';
				} else {
					$_wp_contextual_help['tools_page_codestyling-localization/codestyling-localization'] .= '<a target="_blank" href="'. $item->get_link() . '" >'. $item->get_title() . '</a> | ';
				}
			}
		}
		
	}
	return $_wp_contextual_help;
}

function csp_po_ajax_handle_dlg_new() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	require_once('includes/locale-definitions.php' );
?>
	<table class="widefat" cellspacing="2px">
		<tr>
			<td nowrap="nowrap"><strong><?php _e('Project-Id-Version',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td><?php echo strip_tags(rawurldecode($_POST['name'])); ?><input type="hidden" id="csp-dialog-name" value="<?php echo strip_tags(rawurldecode($_POST['name'])); ?>" /></td>
		</tr>
		<tr>
			<td><strong><?php _e('Creation-Date',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td><?php echo date("Y-m-d H:iO"); ?><input type="hidden" id="csp-dialog-timestamp" value="<?php echo date("Y-m-d H:iO"); ?>" /></td>
		</tr>
		<tr>
			<td style="vertical-align:middle;"><strong><?php _e('Last-Translator',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td><input style="width:330px;" type="text" id="csp-dialog-translator" value="<?php $myself = wp_get_current_user(); echo "$myself->user_nicename &lt;$myself->user_email&gt;"; ?>" /></td>
		</tr>
		<tr>
			<td valign="top"><strong><?php echo $csp_l10n_login_label[substr(get_locale(),0,2)]?>:</strong></td>
			<td>
				<div style="width:332px;height:300px; overflow:scroll;border:solid 1px #54585B;overflow-x:hidden;">
					<?php $existing = explode('|', ltrim($_POST['existing'],'|')); if(strlen($existing[0]) == 0) $existing=array(); ?>
					<input type="hidden" id="csp-dialog-row" value="<?php echo strip_tags($_POST['row']); ?>" />
					<input type="hidden" id="csp-dialog-numlangs" value="<?php echo count($existing)+1; ?>" />
					<input type="hidden" id="csp-dialog-language" value="" />
					<input type="hidden" id="csp-dialog-path" value="<?php echo strip_tags($_POST['path']); ?>" />
					<input type="hidden" id="csp-dialog-subpath" value="<?php echo strip_tags($_POST['subpath']); ?>" />
					<input type="hidden" id="csp-dialog-simplefilename" value="<?php echo strip_tags($_POST['simplefilename']); ?>" />			
					<input type="hidden" id="csp-dialog-transtemplate" value="<?php echo strip_tags($_POST['transtemplate']); ?>" />					
					<input type="hidden" id="csp-dialog-textdomain" value="<?php echo strip_tags($_POST['textdomain']); ?>" />					
					<input type="hidden" id="csp-dialog-denyscan" value="<?php echo ($_POST['denyscan'] ? "true" : "false"); ?>" />					
					<table style="font-family:monospace;">
					<?php
						$total = array_keys($csp_l10n_sys_locales);
						foreach($total as $key) {
							if (in_array($key, $existing)) continue;
							$values = $csp_l10n_sys_locales[$key];
							if (get_locale() == $key) { $selected = '" selected="selected'; } else { $selected=""; };
							?>
							<tr>
								<td><input type="radio" name="mo-locale" value="<?php echo $key; ?><?php echo $selected; ?>" onclick="$('submit_language').enable();$('csp-dialog-language').value = this.value;" /></td>
								<td><img alt="" title="locale: <?php echo $key ?>" src="<?php echo CSP_PO_BASE_URL."/images/flags/".$csp_l10n_sys_locales[$key]['country-www'].".gif\""; ?>" /></td>
								<td><?php echo $key; ?></td>
								<td style="padding-left: 5px;border-left: 1px solid #aaa;"><?php echo $values['lang-native']."<br />"; ?></td>
							</tr>
							<?php
						}
					?>
					</table>
				</div>
			</td>
		</tr>
	</table>
	<div style="text-align:center; padding-top: 10px"><input class="button" id="submit_language" type="submit" disabled="disabled" value="<?php _e('create po-file',CSP_PO_TEXTDOMAIN); ?>" onclick="return csp_create_new_pofile(this,<?php echo "'".strip_tags($_POST['type'])."'"; ?>);"/></div>
<?php
exit();
}

function csp_po_ajax_handle_dlg_delete() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	require_once('includes/locale-definitions.php' );
	$lang = isset($csp_l10n_sys_locales[$_POST['language']]) ? $csp_l10n_sys_locales[$_POST['language']]['lang-native'] : $_POST['language'];
?>
	<p style="text-align:center;"><?php echo sprintf(__('You are about to delete <strong>%s</strong> from "<strong>%s</strong>" permanently.<br/>Are you sure you wish to delete these files?', CSP_PO_TEXTDOMAIN), $lang, strip_tags(rawurldecode($_POST['name']))); ?></p>
	<div style="text-align:center; padding-top: 10px"><input class="button" id="submit_language" type="submit" value="<?php _e('delete files',CSP_PO_TEXTDOMAIN); ?>" onclick="csp_destroy_files(this,'<?php echo str_replace("'", "\\'", strip_tags(rawurldecode($_POST['name'])))."','".strip_tags($_POST['row'])."','".strip_tags($_POST['path'])."','".strip_tags($_POST['subpath'])."','".strip_tags($_POST['language'])."','".strip_tags($_POST['numlangs']);?>' );" /></div>
<?php
	exit();
}

function csp_po_ajax_handle_dlg_rescan() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	require_once('includes/locale-definitions.php' );	
	global $wp_version;
	if ($_POST['type'] == 'wordpress') {	
		$abs_root = rtrim(str_replace('\\', '/', ABSPATH), '/' );
		$excludes = array();
		$files = array(
			$abs_root.'/wp-activate.php',
			$abs_root.'/wp-app.php',
			$abs_root.'/wp-atom.php',
			$abs_root.'/wp-blog-header.php',
			$abs_root.'/wp-comments-post.php',
			$abs_root.'/wp-commentsrss2.php',
			$abs_root.'/wp-cron.php',
			$abs_root.'/wp-feed.php',
			$abs_root.'/wp-links-opml.php',
			$abs_root.'/wp-load.php',
			$abs_root.'/wp-login.php',
			$abs_root.'/wp-mail.php',
			$abs_root.'/wp-pass.php',
			$abs_root.'/wp-rdf.php',
			$abs_root.'/wp-register.php',
			$abs_root.'/wp-rss.php',
			$abs_root.'/wp-rss2.php',
			$abs_root.'/wp-settings.php',
			$abs_root.'/wp-signup.php',
			$abs_root.'/wp-trackback.php',
			$abs_root.'/xmlrpc.php',
			str_replace("\\", "/", WP_PLUGIN_DIR).'/akismet/akismet.php'
		);
		rscandir_php($abs_root.'/wp-admin/', $excludes, $files);
		rscandir_php($abs_root.'/wp-includes/', $excludes, $files);
		//do not longer rescan old themes prior hosted the the main localization file starting from WP 3.0!
		if (version_compare($wp_version, '3', '<')) {
			rscandir_php(str_replace("\\","/",WP_CONTENT_DIR)."/themes/default/", $excludes, $files);
			rscandir_php(str_replace("\\","/",WP_CONTENT_DIR)."/themes/classic/", $excludes, $files);
		}	
	}
	elseif ($_POST['type'] == 'plugins_mu') {
		$files[] = strip_tags($_POST['simplefilename']);
	}
	elseif ($_POST['textdomain'] == 'buddypress') {
		$files = array();
		$excludes = array(strip_tags($_POST['path']).'bp-forums/bbpress' );
		rscandir_php(strip_tags($_POST['path']), $excludes, $files);
	}
	else{
		$files = array();
		$excludes = array();
		if (isset($_POST['simplefilename']) && !empty($_POST['simplefilename'])) { $files[] = strip_tags($_POST['simplefilename']); }
		else { rscandir_php(strip_tags($_POST['path']), $excludes, $files); }
		if ($_POST['type'] == 'themes' && isset($_POST['themetemplate']) && !empty($_POST['themetemplate'])) {
			rscandir_php(str_replace("\\","/",WP_CONTENT_DIR).'/themes/'.strip_tags($_POST['themetemplate']).'/',$excludes, $files);
		}
	}
	$country_www = isset($csp_l10n_sys_locales[$_POST['language']]) ? $csp_l10n_sys_locales[$_POST['language']]['country-www'] : 'unknown';
	$lang_native = isset($csp_l10n_sys_locales[$_POST['language']]) ? $csp_l10n_sys_locales[$_POST['language']]['lang-native'] : $_POST['language'];
	$filename = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).".po";
?>	
	<input id="csp-dialog-source-file-json" type="hidden" value="{ <?php 
		echo "name: '".strip_tags($_POST['name'])."',";
		echo "row: '".strip_tags($_POST['row'])."',";
		echo "language: '".strip_tags($_POST['language'])."',";
		echo "textdomain: '".strip_tags($_POST['textdomain'])."',";
		echo "next : 0,";
		echo "path : '".strip_tags($_POST['path'])."',";
		echo "pofile : '".strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).".po',";
		echo "type : '".strip_tags($_POST['type'])."',";
		echo "files : ['".implode("','",$files)."']"
	?>}" />
	<table class="widefat" cellspacing="2px">
		<tr>
			<td nowrap="nowrap"><strong><?php _e('Project-Id-Version',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td colspan="2"><?php echo strip_tags(rawurldecode($_POST['name'])); ?><input type="hidden" name="name" value="<?php echo strip_tags(rawurldecode($_POST['name'])); ?>" /></td>
		</tr>
		<tr>
			<td nowrap="nowrap"><strong><?php _e('Language Target',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td><img alt="" title="locale: <?php echo strip_tags($_POST['language']); ?>" src="<?php echo CSP_PO_BASE_URL."/images/flags/".$country_www.".gif\""; ?>" /></td>			
			<td><?php echo $lang_native; ?></td>
		</tr>	
		<tr>
			<td nowrap="nowrap"><strong><?php _e('Affected Total Files',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td nowrap="nowrap" align="right"><?php echo count($files); ?></td>
			<td><em><?php echo "/".str_replace(str_replace("\\",'/',ABSPATH), '', strip_tags($_POST['path'])); ?></em></td>
		</tr>
		<tr>
			<td nowrap="nowrap" valign="top"><strong><?php _e('Scanning Progress',CSP_PO_TEXTDOMAIN); ?>:</strong></td>
			<td id="csp-dialog-progressvalue" nowrap="nowrap" valign="top" align="right">0</td>
			<td>
				<div style="height:13px;width:290px;border:solid 1px #333;"><div id="csp-dialog-progressbar" style="height: 13px;width:0%; background-color:#0073D9"></div></div>
				<div id="csp-dialog-progressfile" style="width:290px;white-space:nowrap;overflow:hidden;font-size:8px;font-family:monospace;padding-top:3px;">&nbsp;</div>
			</td>
		<tr>
	</table>
	<div style="text-align:center; padding-top: 10px"><input class="button" id="csp-dialog-rescan" type="submit" value="<?php _e('scan now',CSP_PO_TEXTDOMAIN); ?>" onclick="csp_scan_source_files(this);"/><span id="csp-dialog-scan-info" style="display:none"><?php _e('Please standby, files presently being scanned ...',CSP_PO_TEXTDOMAIN); ?></span></div>
<?php
	exit();
}

function csp_po_convert_js_input_for_source($str) {
	$search = array('\\\\\"','\\\\n', '\\\\t', '\\\\$','\\0', "\\'", '\\\\' );
	$replace = array('"', "\n", "\\t", "\\$", "\0", "'", "\\");
	$str = str_replace( $search, $replace, $str );
	return $str;
}

function csp_po_ajax_handle_dlg_show_source() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	list($file, $match_line) = explode(':', $_POST['file']);
	$l = filesize(strip_tags($_POST['path']).$file);
	$handle = fopen(strip_tags($_POST['path']).$file,'rb' );
	$content = str_replace(array("\r","\\$"),array('','$'), fread($handle, $l));
	fclose($handle);

	$msgid = $_POST['msgid'];
	$msgid = csp_po_convert_js_input_for_source($msgid);	
	if (strlen($msgid) > 0) {
		if (strpos($msgid, "\00") > 0)
			$msgid = explode("\00", $msgid);
		else
			$msgid = explode("\01", $msgid); //opera fix
		foreach($msgid as $item) {	
			if (strpos($content, $item) === false) {
				//difficult to separate between real \n notation and LF brocken strings also \t 
				$test = str_replace("\n", '\n', $item);
				if (strpos($content, $test) === false) {
					$test2 = str_replace('\t', "\t", $item);
					if (strpos($content, $test2) === false) {
						$test2 = str_replace('\t', "\t", $test);
						if (strpos($content, $test2) === true) {
							$item = $test2;
						}
					}else{
						$item = $test2;
					}
				}else {
					$item = $test;
				}
			}
			$content = str_replace($item, "\1".$item."\2", $content);
		}
	}
	$tmp = htmlentities($content, ENT_COMPAT, 'UTF-8' );
	if (empty($tmp)) $tmp = htmlentities($content, ENT_COMPAT);
	$content = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$tmp);
	$content = preg_split("/\n/", $content);
	$c=0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
<body style="margin:0; padding:0;font-family:monospace;font-size:13px;">
	<table id="php_source" cellspacing="0" width="100%" style="padding:0; margin:0;">
<?php	
	$open = 0;
	$closed = 0;
	foreach($content as $line) {
		$c++;
		$style = $c % 2 == 1 ? "#fff" : "#eee";
		
		$open += substr_count($line,"\1");
		$closed += substr_count($line,"\2");
		$contained = preg_match("/(\1|\2)/", $line) || ($c == $match_line) || ($open != $closed);
		if ($contained) $style="#FFEF3F";
		
		if (!preg_match("/(\1|\2)/", $line) && $contained) $line = "<span style='background-color:#f00; color:#fff;padding:0 3px;'>".$line."</span>";
		if((substr_count($line,"\1") < substr_count($line,"\2")) && ($open == $closed)) $line = "<span style='background-color:#f00; color:#fff;padding:0 3px;'>".$line;
		if(substr_count($line,"\1") > substr_count($line,"\2")) $line .= "</span>";
		$line = str_replace("\1", "<span style='background-color:#f00; color:#fff;padding:0 3px;'>", $line);
		$line = str_replace("\2", "</span>", $line);
		
		echo "<tr id=\"l-$c\" style=\"background-color:$style;\"><td align=\"right\" style=\"background-color:#888;padding-right:5px;\">$c</td><td nowrap=\"nowrap\" style=\"padding-left:5px;\">$line</td></tr>\n";
	}
?>
	</table>
	<script type="text/javascript">
	/* <![CDATA[ */
function init() {
	try{
		window.scrollTo(0,document.getElementById('l-'+<?php echo max($match_line-15,1); ?>).offsetTop);
	}catch(e) {
		//silently kill errors if *.po files line numbers comes out of an outdated file and exceed the line range
	}
}
	
if (typeof Event == 'undefined') Event = new Object();
Event.domReady = {
	add: function(fn) {
		//-----------------------------------------------------------
		// Already loaded?
		//-----------------------------------------------------------
		if (Event.domReady.loaded) return fn();

		//-----------------------------------------------------------
		// Observers
		//-----------------------------------------------------------
	
		var observers = Event.domReady.observers;
		if (!observers) observers = Event.domReady.observers = [];
		// Array#push is not supported by Mac IE 5
		observers[observers.length] = fn;
 
		//-----------------------------------------------------------
		// domReady function
		//-----------------------------------------------------------
		if (Event.domReady.callback) return;
		Event.domReady.callback = function() {
			if (Event.domReady.loaded) return;
			Event.domReady.loaded = true;
			if (Event.domReady.timer) {
				clearInterval(Event.domReady.timer);
				Event.domReady.timer = null;
			}

			var observers = Event.domReady.observers;
			for (var i = 0, length = observers.length; i < length; i++) {
				var fn = observers[i];
				observers[i] = null;
				fn(); // make 'this' as window
			}
			Event.domReady.callback = Event.domReady.observers = null;
		};

		//-----------------------------------------------------------
		// Emulates 'onDOMContentLoaded'
		//-----------------------------------------------------------
		var ie = !!(window.attachEvent && !window.opera);
		var webkit = navigator.userAgent.indexOf('AppleWebKit/') > -1;
 
		if (document.readyState && webkit) {
 
			// Apple WebKit (Safari, OmniWeb, ...)
			Event.domReady.timer = setInterval(function() {
				var state = document.readyState;
				if (state == 'loaded' || state == 'complete') {
					Event.domReady.callback();
				}
			}, 50);
 
		} else if (document.readyState && ie) {
 
			// Windows IE
			var src = (window.location.protocol == 'https:') ? '://0' : 'javascript:void(0)';
			document.write(
				'<script type="text/javascript" defer="defer" src="' + src + '" ' +
				'onreadystatechange="if (this.readyState == \'complete\') Event.domReady.callback();"' +
				'><\/script>' );
 
		} else {
 
			if (window.addEventListener) {
				// for Mozilla browsers, Opera 9
				document.addEventListener("DOMContentLoaded", Event.domReady.callback, false);
				// Fail safe
				window.addEventListener("load", Event.domReady.callback, false);
			} else if (window.attachEvent) {
				window.attachEvent('onload', Event.domReady.callback);
			} else {
				// Legacy browsers (e.g. Mac IE 5)
				var fn = window.onload;
				window.onload = function() {
					Event.domReady.callback();
					if (fn) fn();
				}
			}
		}
	}
}	
	Event.domReady.add(init);
	/* ]]> */
	</script>	
</body>
</html>
<?php
	exit();
}

function csp_po_ajax_handle_merge_from_maintheme() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	require_once('includes/locale-definitions.php' );
	require_once('includes/class-filesystem-translationfile.php' );
	
	//source|dest|basepath|textdomain|molist
	$tmp = array();
	$files = rscandir(str_replace("\\","/",WP_CONTENT_DIR).'/themes/'.strip_tags($_POST['source']).'/', "/(\.po|\.mo)$/", $tmp);
	foreach($files as $file) {
		$pofile = new CspFileSystem_TranslationFile();
		$target = strip_tags($_POST['basepath']).basename($file);
		if(preg_match('/\.mo/', $file)) {
			$pofile->read_mofile($file, $csp_l10n_plurals, false, strip_tags($_POST['textdomain']));
			$pofile->write_mofile($target, strip_tags($_POST['textdomain']));
		}else{
			$pofile->read_pofile($file);
			if (file_exists($target)) {
				//merge it now
				$pofile->read_pofile($target);
			}
			$pofile->write_pofile($target, true, strip_tags($_POST['textdomain']));
		}
	}
	exit();
}

function csp_po_ajax_handle_create() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	require_once('includes/locale-definitions.php' );
	require_once('includes/class-filesystem-translationfile.php' );
	
	$pofile = new CspFileSystem_TranslationFile();
	$filename = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).'.po';
	$pofile->new_pofile(
		$filename, 
		strip_tags($_POST['subpath']),
		strip_tags($_POST['name']), 
		strip_tags($_POST['timestamp']), 
		$_POST['translator'], 
		$csp_l10n_plurals[substr($_POST['language'],0,2)], 
		$csp_l10n_sys_locales[$_POST['language']]['lang'], 
		$csp_l10n_sys_locales[$_POST['language']]['country']
	);
	if(!$pofile->write_pofile($filename)) {
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		echo sprintf(__("You do not have the permission to create the file '%s'.", CSP_PO_TEXTDOMAIN), $filename);
	}
	else{	
		header('Content-Type: application/json' );
?>
{
		name: '<?php echo strip_tags(rawurldecode($_POST['name'])); ?>',
		row : '<?php echo strip_tags($_POST['row']); ?>',
		head: '<?php echo sprintf(_n('<strong>%d</strong> Language', '<strong>%d</strong> Languages',(int)$_POST['numlangs'],CSP_PO_TEXTDOMAIN), $_POST['numlangs']); ?>',
		path: '<?php echo strip_tags($_POST['path']); ?>',
		subpath: '<?php echo strip_tags($_POST['subpath']); ?>',
		language: '<?php echo strip_tags($_POST['language']); ?>',
		lang_native: '<?php echo $csp_l10n_sys_locales[strip_tags($_POST['language'])]['lang-native']; ?>',
		image: '<?php echo CSP_PO_BASE_URL."/images/flags/".$csp_l10n_sys_locales[strip_tags($_POST['language'])]['country-www'].".gif";?>',
		type: '<?php echo strip_tags($_POST['type']); ?>',
		simplefilename: '<?php echo strip_tags($_POST['simplefilename']); ?>',
		transtemplate: '<?php echo strip_tags($_POST['transtemplate']); ?>',
		permissions: '<?php echo date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename); ?>',
		denyscan: <?php echo strip_tags($_POST['denyscan']); ?>,
		google: "<?php echo $csp_l10n_sys_locales[$_POST['language']]['google-api'] ? 'yes' : 'no'; ?>",
		microsoft: "<?php echo $csp_l10n_sys_locales[$_POST['language']]['microsoft-api'] ? 'yes' : 'no'; ?>"
}
<?php		
	}
	exit();
}

function csp_po_ajax_handle_destroy() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	$pofile = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).'.po';
	$mofile = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).'.mo';
	$error = false;
	
	require_once('includes/class-filesystem-translationfile.php' );
	$transfile = new CspFileSystem_TranslationFile();
	
	$transfile->destroy_pofile($pofile);
	$transfile->destroy_mofile($mofile);
	
	$num = (int)$_POST['numlangs'] - 1;
	header('Content-Type: application/json' );
?>
{
	row : '<?php echo strip_tags($_POST['row']); ?>',
	head: '<?php echo sprintf(_n('<strong>%d</strong> Language', '<strong>%d</strong> Languages',$num,CSP_PO_TEXTDOMAIN), $num); ?>',
	language: '<?php echo strip_tags($_POST['language']); ?>'
}
<?php	
	exit();
}
function csp_po_ajax_csp_po_change_low_memory_mode() {
	csp_po_check_security();
	update_option('codestyling-localization.low-memory', ($_POST['mode'] == 'true' ? true : false));
	exit();
}

function csp_po_ajax_change_translate_api() {
	csp_po_check_security();
	$api_type = 'none';
	if (in_array($_POST['api_type'], array('google','microsoft'))) {
		$api_type = $_POST['api_type'];
	}
	update_option('codestyling-localization.translate-api', $api_type);
	exit();
}

function csp_po_ajax_handle_scan_source_file() {
	csp_po_check_security();

	$low_mem_scanning = (bool)get_option('codestyling-localization.low-memory', false);
	
	require_once('includes/class-filesystem-translationfile.php' );
	require_once('includes/locale-definitions.php' );
	$textdomain = $_POST['textdomain'];
	//TODO: give the domain into translation file as default domain
	$pofile = new CspFileSystem_TranslationFile($_POST['type']);
	//BUGFIX: 1.90 - may be, we have only the mo but no po, so we dump it out as base po file first
	if (!file_exists($_POST['pofile'])) {
		//try implicite convert first and reopen as po second
		if($pofile->read_mofile(substr($_POST['pofile'],0,-2)."mo", $csp_l10n_plurals, false, $textdomain)) {
			$pofile->write_pofile($_POST['pofile'],false,false, ($_POST['type'] == 'wordpress' ? 'no' : 'yes'));
		}
		//check, if we have to reverse all the other *.mo's too
		if($_POST['type'] == 'wordpress') {
			$root_po = basename($_POST['pofile']);
			$root_mo = substr($root_po,0,-2)."mo";
			$part = str_replace($root_po, '', $_POST['pofile']);
			if($pofile->read_mofile($part.'continents-cities-'.$root_mo, $csp_l10n_plurals, $part.'continents-cities-'.$root_mo, $_POST['textdomain'])) {
				$pofile->write_pofile($part.'continents-cities-'.$root_po,false,false,'no' );
			}
			if($pofile->read_mofile($part.'ms-'.$root_mo, $csp_l10n_plurals, $part.'ms-'.$root_mo, $_POST['textdomain'])) {		
				$pofile->write_pofile($part.'ms-'.$root_po,false,false,'no' );
			}
			global $wp_version;			
			if (version_compare($wp_version, '3.4-alpha', ">=")) {
				if($pofile->read_mofile($part.'admin-'.$root_mo, $csp_l10n_plurals, $part.'admin-'.$root_mo, $_POST['textdomain'])) {
					$pofile->write_pofile($part.'admin-'.$root_po,false,false,'no' );
				}
				if($pofile->read_mofile($part.'admin-network-'.$root_mo, $csp_l10n_plurals, $part.'admin-network-'.$root_mo, $_POST['textdomain'])) {
					$pofile->write_pofile($part.'admin-network-'.$root_po,false,false,'no' );
				}
			}
		}
	}		
	$pofile = new CspFileSystem_TranslationFile($_POST['type']);
	if ($pofile->read_pofile($_POST['pofile'])) {
		if ((int)$_POST['num'] == 0) { 
		
			if (!$pofile->supports_textdomain_extension() && $_POST['type'] == 'wordpress'){
				//try to merge up first all splitted translations.
				$root = basename($_POST['pofile']);
				$part = str_replace($root, '', $_POST['pofile']);
				//load existing files for backward compatibility if existing
				$pofile->read_pofile($part.'continents-cities-'.$root, $csp_l10n_plurals, $part.'continents-cities-'.$root);
				$pofile->read_pofile($part.'ms-'.$root, $csp_l10n_plurals, $part.'ms-'.$root);
				global $wp_version;			
				if (version_compare($wp_version, '3.4-alpha', ">=")) {
					$pofile->read_pofile($part.'admin-'.$root, $csp_l10n_plurals, $part.'admin-'.$root);
					$pofile->read_pofile($part.'admin-network-'.$root, $csp_l10n_plurals, $part.'admin-network-'.$root);
				}
				//again read it to get the right header overwritten last
				$pofile->read_pofile($_POST['pofile']);
				//overwrite with full imploded sparse file contents now
				$pofile->write_pofile($_POST['pofile'],false,false,'no' );
			}		
		
			$pofile->parsing_init(); 
		}
		
		$php_files = explode("|", $_POST['php']);
		$s = (int)$_POST['num'];
		$e = min($s + (int)$_POST['cnt'], count($php_files));
		$last = ($e >= count($php_files));
		for ($i=$s; $i<$e; $i++) {
			if ($low_mem_scanning) {
				$options = array(
					'type' => $_POST['type'],
					'path' => $_POST['path'],
					'textdomain' => $_POST['textdomain'],
					'file' => $php_files[$i]
				);
				$r = wp_remote_post(CSP_PO_BASE_URL.'/includes/low-memory-parsing.php', array('body' => $options));
				$data = unserialize(base64_decode($r['body']));
				$pofile->add_messages($data);
			}else{
				$pofile->parsing_add_messages($_POST['path'], $php_files[$i], $textdomain);
			}
		}	
		if ($last) { $pofile->parsing_finalize($textdomain, strip_tags(rawurldecode($_POST['name']))); }
		if ($pofile->write_pofile($_POST['pofile'], $last)) {
			header('Content-Type: application/json' );
			echo '{ title: "'.date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($_POST['pofile']))." ".file_permissions($_POST['pofile']).'" }';
		}
		else{
			header('Status: 404 Not Found' );
			header('HTTP/1.1 404 Not Found' );
			echo sprintf(__("You do not have the permission to write to the file '%s'.", CSP_PO_TEXTDOMAIN), $_POST['pofile']);
		}
	}
	else{
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		echo sprintf(__("You do not have the permission to read the file '%s'.", CSP_PO_TEXTDOMAIN), $_POST['pofile']);
	}
	exit();
}

function csp_po_ajax_handle_change_permission() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	$filename = strip_tags($_POST['file']);
	$error = false;
	
	require_once('includes/class-filesystem-translationfile.php' );
	$transfile = new CspFileSystem_TranslationFile();
	
	$transfile->change_permission($filename);

	header('Content-Type: application/json' );
	echo '{ title: "'.date(__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($filename))." ".file_permissions($filename).'" }';
	exit();
}

function csp_po_ajax_handle_launch_editor() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	require_once('includes/locale-definitions.php' );
//	require_once('includes/class-translationfile.php' );
	require_once('includes/class-filesystem-translationfile.php' );
	$f = new CspFileSystem_TranslationFile($_POST['type']);
	if (!file_exists($_POST['basepath'].$_POST['file'])) {
		//try implicite convert first
		if($f->read_mofile(substr($_POST['basepath'].$_POST['file'],0,-2)."mo", $csp_l10n_plurals, $_POST['file'], $_POST['textdomain'])) {
			$f->write_pofile($_POST['basepath'].$_POST['file'],false,false,'no' );
		}
		//check, if we have to reverse all the other *.mo's too
		if($_POST['type'] == 'wordpress') {
			$root_po = basename($_POST['file']);
			$root_mo = substr($root_po,0,-2)."mo";
			$part = str_replace($root_po, '', $_POST['file']);
			if($f->read_mofile($_POST['basepath'].$part.'continents-cities-'.$root_mo, $csp_l10n_plurals, $part.'continents-cities-'.$root_mo, $_POST['textdomain'])) {
				$f->write_pofile($_POST['basepath'].$part.'continents-cities-'.$root_po,false,false,'no' );
			}
			if($f->read_mofile($_POST['basepath'].$part.'ms-'.$root_mo, $csp_l10n_plurals, $part.'ms-'.$root_mo, $_POST['textdomain'])) {		
				$f->write_pofile($_POST['basepath'].$part.'ms-'.$root_po,false,false,'no' );
			}
			global $wp_version;			
			if (version_compare($wp_version, '3.4-alpha', ">=")) {
				if($f->read_mofile($_POST['basepath'].$part.'admin-'.$root_mo, $csp_l10n_plurals, $part.'admin-'.$root_mo, $_POST['textdomain'])) {
					$f->write_pofile($_POST['basepath'].$part.'admin-'.$root_po,false,false,'no' );
				}
				if($f->read_mofile($_POST['basepath'].$part.'admin-network-'.$root_mo, $csp_l10n_plurals, $part.'admin-network-'.$root_mo, $_POST['textdomain'])) {
					$f->write_pofile($_POST['basepath'].$part.'admin-network-'.$root_po,false,false,'no' );
				}
			}
		}
	}
	$f = new CspFileSystem_TranslationFile($_POST['type']);
	$f->read_pofile($_POST['basepath'].$_POST['file'], $csp_l10n_plurals, $_POST['file']);
	if (!$f->supports_textdomain_extension() && $_POST['type'] == 'wordpress'){
		//try to merge up first all splitted translations.
		$root = basename($_POST['file']);
		$part = str_replace($root, '', $_POST['file']);
		//load existing files for backward compatibility if existing
		$f->read_pofile($_POST['basepath'].$part.'continents-cities-'.$root, $csp_l10n_plurals, $part.'continents-cities-'.$root);
		$f->read_pofile($_POST['basepath'].$part.'ms-'.$root, $csp_l10n_plurals, $part.'ms-'.$root);
		global $wp_version;			
		if (version_compare($wp_version, '3.4-alpha', ">=")) {
			$f->read_pofile($_POST['basepath'].$part.'admin-'.$root, $csp_l10n_plurals, $part.'admin-'.$root);
			$f->read_pofile($_POST['basepath'].$part.'admin-network-'.$root, $csp_l10n_plurals, $part.'admin-network-'.$root);
		}
		//again read it to get the right header overwritten last
		$f->read_pofile($_POST['basepath'].$_POST['file'], $csp_l10n_plurals, $_POST['file']);
		//overwrite with full imploded sparse file contents now
		$f->write_pofile($_POST['basepath'].$_POST['file'],false,false,'no' );
	}
	if ($f->supports_textdomain_extension() || $_POST['type'] == 'wordpress'){
		if (!defined('TRANSLATION_API_PER_USER_DONE')) csp_po_init_per_user_trans();
		$f->echo_as_json($_POST['basepath'], $_POST['file'], $csp_l10n_sys_locales, csp_get_translate_api_type());
	}else {
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		_e("Your translation file doesn't support the <em>multiple textdomains in one translation file</em> extension.<br/>Please re-scan the related source files at the overview page to enable this feature.",CSP_PO_TEXTDOMAIN);
		?>&nbsp;<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="translationformat"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a><?php
	}
	exit();
}

function csp_po_ajax_handle_translate_by_google() {
	csp_po_check_security();
	if (!defined('TRANSLATION_API_PER_USER_DONE')) csp_po_init_per_user_trans();
	// reference documentation: http://code.google.com/intl/de-DE/apis/ajaxlanguage/documentation/reference.html
	// example API v1 - 'http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=hello%20world&langpair=en%7Cit'
	// example API v2 - [ GET https://www.googleapis.com/language/translate/v2?key=INSERT-YOUR-KEY&source=en&target=de&q=Hello%20world ]
	$msgid = $_POST['msgid'];
	$search = array('\\\\\\\"', '\\\\\"','\\\\n', '\\\\r', '\\\\t', '\\\\$','\\0', "\\'", '\\\\' );
	$replace = array('\"', '"', "\n", "\r", "\\t", "\\$", "\0", "'", "\\");
	$msgid = str_replace( $search, $replace, $msgid );
	add_filter('https_ssl_verify', '__return_false' );
	//OLD: $res = csp_fetch_remote_content("http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&format=html&q=".urlencode($msgid)."&langpair=en%7C".$_POST['destlang']);
	$res = csp_fetch_remote_content("https://www.googleapis.com/language/translate/v2?key=".(defined('GOOGLE_TRANSLATE_KEY') ? GOOGLE_TRANSLATE_KEY : '')."&source=en&target=".$_POST['destlang']."&q=".urlencode($msgid));
	if ($res) {
		header('Content-Type: application/json' );
		echo $res;
	}
	else{
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
		_e("Sorry, Google Translation is not available.", CSP_PO_TEXTDOMAIN);	
	}
	exit();
}

function csp_po_ajax_handle_translate_by_microsoft() {
	csp_po_check_security();
	if (!defined('TRANSLATION_API_PER_USER_DONE')) csp_po_init_per_user_trans();
	$msgid = $_POST['msgid'];
	$search = array('\\\\\\\"', '\\\\\"','\\\\n', '\\\\r', '\\\\t', '\\\\$','\\0', "\\'", '\\\\' );
	$replace = array('\"', '"', "\n", "\r", "\\t", "\\$", "\0", "'", "\\");
	$msgid = str_replace( $search, $replace, $msgid );	
	
	require_once('includes/translation-api-microsoft.php' );
	header('Content-Type: text/plain' );
	try {
		//Client ID of the application.
		$clientID     = defined('MICROSOFT_TRANSLATE_CLIENT_ID') ? MICROSOFT_TRANSLATE_CLIENT_ID : '';
		//Client Secret key of the application.
		$clientSecret = defined('MICROSOFT_TRANSLATE_CLIENT_SECRET') ? MICROSOFT_TRANSLATE_CLIENT_SECRET : '';
		//OAuth Url.
		$authUrl      = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
		//Application Scope Url
		$scopeUrl     = "http://api.microsofttranslator.com";
		//Application grant type
		$grantType    = "client_credentials";

		//Create the AccessTokenAuthentication object.
		$authObj      = new AccessTokenAuthentication();
		//Get the Access token.
		$accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
		//Create the authorization Header string.
		$authHeader = "Authorization: Bearer ". $accessToken;

		//Set the params.//
		$fromLanguage = "en";
		$toLanguage   = strip_tags($_POST['destlang']);
		$inputStr     = $msgid;
		$contentType  = 'text/plain';
		$category     = 'general';
		
		$params = "text=".urlencode($inputStr)."&to=".$toLanguage."&from=".$fromLanguage;
		$translateUrl = "http://api.microsofttranslator.com/v2/Http.svc/Translate?$params";
		
		//Create the Translator Object.
		$translatorObj = new HTTPTranslator();
		
		//Get the curlResponse.
		$curlResponse = $translatorObj->curlRequest($translateUrl, $authHeader);
		
		//Interprets a string of XML into an object.
		$xmlObj = simplexml_load_string($curlResponse);
		foreach((array)$xmlObj[0] as $val){
			$translatedStr = $val;
		}
		echo $translatedStr;
	} catch(Exception $e) {
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		echo $e->getMessage();
	}	

	exit();
}

function csp_po_ajax_handle_save_catalog_entry() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
//	require_once('includes/class-translationfile.php' );
	require_once('includes/class-filesystem-translationfile.php' );
	$f = new CspFileSystem_TranslationFile();
	//opera bugfix: replace embedded \1 with \0 because Opera can't send embeded 0
	$_POST['msgid'] = str_replace("\1", "\0", $_POST['msgid']);
	$_POST['msgstr'] = str_replace("\1", "\0", $_POST['msgstr']);
	if ($f->read_pofile($_POST['path'].$_POST['file'])) {
		if (!$f->update_entry($_POST['msgid'], $_POST['msgstr'])) {
			header('Status: 404 Not Found' );
			header('HTTP/1.1 404 Not Found' );
			echo sprintf(__("You do not have the permission to write to the file '%s'.", CSP_PO_TEXTDOMAIN), $_POST['file']);
		}
		else{
			$f->write_pofile($_POST['path'].$_POST['file']);
			header('Status: 200 Ok' );
			header('HTTP/1.1 200 Ok' );
			header('Content-Length: 1' );	
			echo "0";
		}
	}
	else{
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		echo sprintf(__("You do not have the permission to read the file '%s'.", CSP_PO_TEXTDOMAIN), $_POST['file']);
	}
	exit();
}

function csp_po_ajax_handle_generate_mo_file(){
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
//	require_once('includes/class-translationfile.php' );
	require_once('includes/class-filesystem-translationfile.php' );
	$pofile = (string)$_POST['pofile'];
	$textdomain = (string)$_POST['textdomain'];
	$f = new CspFileSystem_TranslationFile();
	if (!$f->read_pofile($pofile)) {
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		echo sprintf(__("You do not have the permission to read the file '%s'.", CSP_PO_TEXTDOMAIN), $pofile);
		exit();
	}
	//lets detected, what we are about to be writing:
	$mo = substr($pofile,0,-2).'mo';

	$wp_dir = str_replace("\\","/",WP_LANG_DIR);
	$pl_dir = str_replace("\\","/",WP_PLUGIN_DIR);
	$plm_dir = str_replace("\\","/",WPMU_PLUGIN_DIR);
	$parts = pathinfo($mo);
	//dirname|basename|extension
	if (preg_match("|^".$wp_dir."|", $mo)) {
		//we are WordPress itself
		if ($textdomain != 'default') {
			$mo	= $parts['dirname'].'/'.$textdomain.'-'.$parts['basename'];
		}
	}elseif(preg_match("|^".$pl_dir."|", $mo)|| preg_match("|^".$plm_dir."|", $mo)) {
		//we are a normal or wpmu plugin
		if ((strpos($parts['basename'], $textdomain) === false) && ($textdomain != 'default')) {
			preg_match("/([a-z][a-z]_[A-Z][A-Z]\.mo)$/", $parts['basename'], $h);
			if (!empty($textdomain)) {
				$mo	= $parts['dirname'].'/'.$textdomain.'-'.$h[1];
			}else {
				$mo	= $parts['dirname'].'/'.$h[1];
			}
		}
	}else{
		//we are a theme plugin, could be tested but skipped for now.
	}
	
	if ($f->is_illegal_empty_mofile($textdomain)) {
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		_e("You are trying to create an empty mo-file without any translations. This is not possible, please translate at least one entry.", CSP_PO_TEXTDOMAIN);
		exit();
	}
	
	if (!$f->write_mofile($mo,$textdomain)) {
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		echo sprintf(__("You do not have the permission to write to the file '%s'.", CSP_PO_TEXTDOMAIN), $mo);
		exit();
	}

	header('Content-Type: application/json' );
?>
{
	filetime: '<?php echo date (__('m/d/Y H:i:s',CSP_PO_TEXTDOMAIN), filemtime($mo)); ?>'
}
<?php		
	exit();
}

function csp_po_ajax_handle_create_language_path() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	require_once('includes/locale-definitions.php' );
	require_once('includes/class-filesystem-translationfile.php' );
	
	$path = strip_tags($_POST['path']);
	
	$pofile = new CspFileSystem_TranslationFile();
	
	if (!$pofile->create_directory($path)) {
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		_e("You do not have the permission to create a new Language File Path.<br/>Please create the appropriated path using your FTP access.", CSP_PO_TEXTDOMAIN);
	}
	else{
			header('Status: 200 ok' );
			header('HTTP/1.1 200 ok' );
			header('Content-Length: 1' );	
			print 0;
	}
	exit();
}

function csp_po_ajax_handle_create_pot_indicator() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
	require_once('includes/locale-definitions.php' );
	require_once('includes/class-filesystem-translationfile.php' );
	
	$locale = 'en_US';
	
	$pofile = new CspFileSystem_TranslationFile();
	$filename = strip_tags($_POST['potfile']);
	$pofile->new_pofile(
		$filename, 
		'/',
		'PlaceHolder', 
		date("Y-m-d H:iO"), 
		'none', 
		$csp_l10n_plurals[substr($locale,0,2)], 
		$csp_l10n_sys_locales[$locale]['lang'], 
		$csp_l10n_sys_locales[$locale]['country']
	);
	if(!$pofile->write_pofile($filename)) {
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		echo sprintf(__("You do not have the permission to create the file '%s'.", CSP_PO_TEXTDOMAIN), $filename);
	}
	else{	
		header('Status: 200 ok' );
		header('HTTP/1.1 200 ok' );
		header('Content-Length: 1' );	
		print 0;
	}
/*	
	$handle = @fopen(strip_tags($_POST['potfile']), "w");
	
	if ($handle === false) {
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		_e("You do not have the permission to choose the translation file directory<br/>Please upload at least one language file (*.mo|*.po) or an empty template file (*.pot) at the appropriated folder using FTP.", CSP_PO_TEXTDOMAIN);
	}
	else{
		@fwrite($handle, 
			"msgid \"\"\n".
			"msgstr \"\"\n".
			"\"MIME-Version: 1.0\"\n".
			"\"Content-Type: text/plain; charset=UTF-8\"\n".
			"\"Content-Transfer-Encoding: 8bit\"\n"
		);
		@fclose($handle);
		header('Status: 200 ok' );
		header('HTTP/1.1 200 ok' );
		header('Content-Length: 1' );	
		print 0;
	}
	exit();
*/	
}

//////////////////////////////////////////////////////////////////////////////////////////
//	Admin Initialization ad Page Handler
//////////////////////////////////////////////////////////////////////////////////////////
if (function_exists('add_action')) {
	if (is_admin() && !defined('DOING_AJAX')) {
		add_action('admin_init', 'csp_po_init' );
		add_action('admin_head', 'csp_po_admin_head' );
		require_once('includes/locale-definitions.php' );
	}
	if(is_admin()) {
		add_action('admin_init', 'csp_po_init_per_user_trans' );	
		add_action('admin_init', 'csp_check_filesystem' );
	}
}

function csp_check_filesystem() {
	//file system investigation
	if (function_exists('get_filesystem_method')) {
		$fsm = get_filesystem_method(array());
		define("CSL_FILESYSTEM_DIRECT", $fsm == 'direct' );
	}else{
		define("CSL_FILESYSTEM_DIRECT", true);
	}
}

function csp_po_init_per_user_trans() {
	//process per user settings
	if (is_user_logged_in() && defined('TRANSLATION_API_PER_USER') && (TRANSLATION_API_PER_USER === true) && current_user_can('manage_options')) {
		$myself = wp_get_current_user();
		$func = function_exists('get_user_meta') ? 'get_user_meta' : 'get_usermeta';
		$g = call_user_func($func, $myself->ID, 'csp-google-api-key', true);
		if (!empty($g) && !defined('GOOGLE_TRANSLATE_KEY'))  define('GOOGLE_TRANSLATE_KEY', $g);
		$m1 = call_user_func($func, $myself->ID, 'csp-microsoft-api-client-id', true);
		if (!empty($m1) && !defined('MICROSOFT_TRANSLATE_CLIENT_ID'))  define('MICROSOFT_TRANSLATE_CLIENT_ID', $m1);
		$m2 = call_user_func($func, $myself->ID, 'csp-microsoft-api-client-secret', true);
		if (!empty($m2) && !defined('MICROSOFT_TRANSLATE_CLIENT_SECRET'))  define('MICROSOFT_TRANSLATE_CLIENT_SECRET', $m2);
	}		
	if (!defined('TRANSLATION_API_PER_USER_DONE')) define('TRANSLATION_API_PER_USER_DONE', true);
}

function csp_try_jquery_document_ready_hardening_pattern($content, $pattern) {
	$pieces = explode($pattern, $content);
	if (count($pieces) > 1) {
		for ($loop=1; $loop<count($pieces); $loop++) {
			$counter = 0;
			$startofready = -1;
			$endofready = -1;
			$script  = $pieces[$loop];
			for($i=0; $i < strlen($script); $i++) {
				switch(substr($script, $i, 1)) {
					case '{':
						$counter++;
						if ($counter == 1) {
							$startofready = $i;
						}
						break;
					case '}';
						$counter--;
						if ($counter == 0) {
							$endofready = $i;
							$i = strlen($script);
						}
						break;
					default:
						break;
				}
			}
			if ($startofready != -1 && $endofready != -1) {
				if ($script[$endofready+1] == ')') $endofready++;
				$sub = substr($script, $startofready+1, $endofready-$startofready-2);
				$pieces[$loop] = str_replace($sub, "\ntry{\n".$sub."\n}catch(e){csp_self_protection.runtime.push(e.message);}" , $script);
			}			
		}
	}
	return implode($pattern, $pieces);
}

function csp_try_jquery_document_ready_hardening($content) {
	$script = csp_try_jquery_document_ready_hardening_pattern($content, '(document).ready(' );
	return csp_try_jquery_document_ready_hardening_pattern($script, 'jQuery(function()' );	
}

$csp_traced_php_errors = array(
	'suppress_errors' => false,
	'old_handler' => null,
	'messages' => array()
);

$csp_external_scripts = array(
	'cdn' => array(
		'tokens' => array(),
		'scripts' => array()
	),
	'dubious' => array(
		'tokens' => array(),
		'scripts' => array()
	)
);

$csp_known_wordpress_externals = array(
	//none wordpress own files
	'colorpicker', 'prototype', 'scriptaculous-root', 'scriptaculous-builder', 'scriptaculous-dragdrop', 'scriptaculous-effects',
	'scriptaculous-slider', 'scriptaculous-sound', 'scriptaculous-controls', 'scriptaculous', 'cropper', 'jquery',
	'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-blind', 'jquery-effects-bounce', 'jquery-effects-clip', 
	'jquery-effects-drop', 'jquery-effects-explode', 'jquery-effects-fade', 'jquery-effects-fold', 'jquery-effects-highlight',
	'jquery-effects-pulsate', 'jquery-effects-scale', 'jquery-effects-shake', 'jquery-effects-slide', 'jquery-effects-transfer',
	'jquery-ui-accordion', 'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-datepicker', 'jquery-ui-dialog', 'jquery-ui-draggable',
	'jquery-ui-droppable', 'jquery-ui-mouse', 'jquery-ui-position', 'jquery-ui-progressbar', 'jquery-ui-resizable', 'jquery-ui-selectable',
	'jquery-ui-slider', 'jquery-ui-sortable', 'jquery-ui-tabs', 'jquery-ui-widget', 'jquery-form', 'jquery-color', 'suggest',
	'schedule', 'jquery-query', 'jquery-serialize-object', 'jquery-hotkeys', 'jquery-table-hotkeys', 'jquery-touch-punch',
	'thickbox', 'jcrop', 'swfobject', 'plupload', 'plupload-html5', 'plupload-flash', 'plupload-silverlight', 'plupload-html4',
	'plupload-all', 'plupload-handlers', 'swfupload', 'swfupload-swfobject', 'swfupload-queue', 'swfupload-speed','swfupload-all',
	'swfupload-handlers', 'json2', 'farbtastic',
	//wordpress admin files
	'utils', 'common', 'sack', 'quicktags', 'editor', 'wp-fullscreen', 'wp-ajax-response', 'wp-pointer', 'autosave',
	'wp-lists', 'comment-reply', 'imgareaselect', 'password-strength-meter', 'user-profile', 'user-search', 'site-search',
	'admin-bar', 'wplink', 'wpdialogs', 'wpdialogs-popup', 'word-count', 'media-upload', 'hoverIntent', 'customize-base',
	'customize-loader', 'customize-preview', 'customize-controls', 'ajaxcat', 'admin-categories', 'admin-tags', 'admin-custom-fields',
	'admin-comments', 'xfn', 'postbox', 'post', 'link', 'comment', 'admin-gallery', 'admin-widgets', 'theme', 'theme-preview',
	'inline-edit-post', 'inline-edit-tax', 'plugin-install', 'dashboard', 'list-revisions', 'media', 'image-edit', 'set-post-thumbnail',
	'nav-menu', 'custom-background', 'media-gallery'
);

function csp_known_and_valid_cdn($url) {
	return preg_match("/^https?:\/\/[^\.]*\.wp\.com/", $url);
}

function csp_plugin_denied_by_guard($url)
{
	$valid = array(
		'/codestyling-localization/',
		'/wp-native-dashboard/',
		'/debug-bar/',
		'/debug-bar-console/',
		'/localization/',
		'/wp-piwik/'
	);
	foreach($valid as $slug)
	{
		if(stripos($url, $slug) !== false)
		{
			 return false;
		}
	}
	return true;
}

function csp_filter_print_scripts_array($scripts) {
	//detect CDN script redirecting
	global $wp_scripts, $csp_external_scripts, $csp_known_wordpress_externals;
	if (is_object($wp_scripts)) {
		foreach($scripts as $token) {
			if(isset($wp_scripts->registered[$token])) {
				if (isset($wp_scripts->registered[$token]->src) && !empty($wp_scripts->registered[$token]->src)) {
					if (preg_match('|^http|', $wp_scripts->registered[$token]->src)) {
						if(!preg_match('|^'.str_replace('.','\.',CSP_SELF_DOMAIN).'|', $wp_scripts->registered[$token]->src)) {
							if (in_array($token, $csp_known_wordpress_externals) || csp_known_and_valid_cdn($wp_scripts->registered[$token]->src)) {
								if (!in_array($token, $csp_external_scripts['cdn']['tokens'])) {
									$csp_external_scripts['cdn']['tokens'][] = $token;
									$csp_external_scripts['cdn']['scripts'][] = $wp_scripts->registered[$token]->src;
								}
							} else {
								if (!in_array($token, $csp_external_scripts['dubious']['tokens'])) {
									$csp_external_scripts['dubious']['tokens'][] = $token;
									$csp_external_scripts['dubious']['scripts'][] = $wp_scripts->registered[$token]->src;
								}
							}
						}
					}
				}
			}
		}
	}
	
	//protect against injected media upload script, modifies thickbox for media uploads not required here!
	if (in_array('media-upload', $scripts)) {
		if (!defined('CSL_MEDIA_UPLOAD_STRIPPED')) define('CSL_MEDIA_UPLOAD_STRIPPED', true);
		$scripts = array_diff($scripts, array('media-upload'));
	}
	//protect against "dubious" scripts !
	$scripts = array_diff($scripts, $csp_external_scripts['dubious']['tokens']);
	return $scripts;
}

function csp_php_error_handler($errno, $errstr, $errfile, $errline) {
	global $csp_traced_php_errors;
	$errorType = array (  
         E_ERROR                => 'ERROR',  
         E_CORE_ERROR           => 'CORE ERROR',  
         E_COMPILE_ERROR        => 'COMPILE ERROR',  
         E_USER_ERROR           => 'USER ERROR',  
         E_RECOVERABLE_ERROR  	=> 'RECOVERABLE ERROR',  
         E_WARNING              => 'WARNING',  
         E_CORE_WARNING         => 'CORE WARNING',  
         E_COMPILE_WARNING      => 'COMPILE WARNING',  
         E_USER_WARNING         => 'USER WARNING',  
         E_NOTICE               => 'NOTICE',  
         E_USER_NOTICE          => 'USER NOTICE',  
         E_DEPRECATED           => 'DEPRECATED',  
         E_USER_DEPRECATED      => 'USER_DEPRECATED',  
         E_PARSE                => 'PARSING ERROR',
		 E_STRICT				=> 'STRICT'
    );  
  
    if (array_key_exists($errno, $errorType)) {  
        $errname = $errorType[$errno];  
    } else {  
        $errname = 'UNKNOWN ERROR';  
    }  
	$csp_traced_php_errors['messages'][] = "$errname <strong>Error: [$errno] </strong> $errstr <strong>$errfile</strong> on line <strong>$errline</strong>";
	if ($csp_traced_php_errors['old_handler'] != null && !$csp_traced_php_errors['suppress_errors']) {
		return call_user_func($csp_traced_php_errors['old_handler'], $errno, $errstr, $errfile, $errline);
	}
	return $csp_traced_php_errors['suppress_errors'];
}

function csp_trace_php_errors() {
	global $csp_traced_php_errors;
	
	$csp_traced_php_errors['suppress_errors'] = (is_admin() && isset($_REQUEST['page']) && ($_REQUEST['page'] == 'codestyling-localization/codestyling-localization.php'));
	if(defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action'])) {
		$actions = array(
			'csp_po_dlg_new',
			'csp_po_dlg_delete',
			'csp_po_dlg_rescan',
			'csp_po_dlg_show_source',
			'csp_po_merge_from_maintheme',
			'csp_po_create',
			'csp_po_destroy',
			'csp_po_scan_source_file',
			'csp_po_change_low_memory_mode',
			'csp_po_change_translate_api',
			'csp_po_change_permission',
			'csp_po_launch_editor',
			'csp_po_translate_by_google',
			'csp_po_translate_by_microsoft',
			'csp_po_save_catalog_entry',
			'csp_po_generate_mo_file',
			'csp_po_create_language_path',
			'csp_po_create_pot_indicator',
			'csp_self_protection_result'
		);
		if (in_array($_POST['action'], $actions))
			$csp_traced_php_errors['suppress_errors'] = true;
	}
	
	if (function_exists('set_error_handler'))
		$csp_traced_php_errors['old_handler'] = set_error_handler("csp_php_error_handler", E_ALL);
}

function csp_start_protection($hook_suffix) {
	ob_start();
}

function csp_self_script_protection_head() {
	$content = ob_get_clean();
	//1st - unify script tags
	$content = preg_replace("/(<script[^>]*)(\/\s*>)/i", '$1></script>', $content);
	$scripts = array();
	$dirty_plugins = array();
	$dirty_theme = array();
	$dirty_scripts = array();
	$dirty_index = array();
	//2nd - analyse scripts
	if (preg_match_all("/<script[^>]*>([\s\S]*?)<\/script>/i", $content, $scripts)) {	
		$num = count($scripts[0]);
		for($i=0; $i<$num; $i++) {
			if (empty($scripts[1][$i])) {
				//url based scripts - mark as dirty if required
				preg_match("/src=[\"']([^\"^']*\.js|[^\"^']*\.php)(\?[^\"^']*[\"']|[\"'])/", $scripts[0][$i], $url);
				if (isset($url[1]) && !empty($url[1])){
					global $csp_external_scripts;				
					if(	stripos($url[1], content_url()) !== false && csp_plugin_denied_by_guard($url[1]) ) {
						//internal scripts
						$dirty_scripts[] = $url[1];
						$dirty_index[] = $i;
						if (stripos($url[1], plugins_url()) !== false || stripos($url[1], content_url().'/mu-plugins') !== false) {
							$dirty_plugins[] = $url[1];
						}else{
							$dirty_theme[] = $url[1];
						}
					}
					elseif (stripos($url[1], CSP_SELF_DOMAIN) === false && !in_array($url[1], $csp_external_scripts['cdn']['scripts'])) {
						//external
						$dirty_index[] = $i;			
						$csp_external_scripts['dubious']['tokens'][] = "hook:admin_head#$i";
						$csp_external_scripts['dubious']['scripts'][] = $url[1];
					}
				}
			}else{
				//embedded scripts - wrap within exception handler
				$content = str_replace($scripts[0][$i], '<script type="text/javascript">'."\n//<![CDATA[\ntry {\n".csp_try_jquery_document_ready_hardening($scripts[1][$i])."\n}catch (e) {\n\tcsp_self_protection.runtime.push(e.message); \n}\n//]]>\n</script>", $content);
			}
		}
	}
	//3rd - remove critical injected scripts
	if (count($dirty_index) > 0) {
		foreach($dirty_index as $i) {
			$content = str_replace($scripts[0][$i], '', $content);
		}
	}
	//4th - define our protection
	echo '<script type="text/javascript">var csp_self_protection = { "dirty_theme" : '.json_encode($dirty_theme).', "dirty_plugins" : ' . json_encode($dirty_plugins). ", \"runtime\" : [] };</script>\n";
	echo $content;
}

function csp_self_script_protection_footer() {
	$content = ob_get_clean();
	//1st - unify script tags
	$content = preg_replace("/(<script[^>]*)(\/\s*>)/i", '$1></script>', $content);
	$scripts = array();
	$dirty_plugins = array();
	$dirty_theme = array();
	$dirty_scripts = array();
	$dirty_index = array();
	//2nd - analyse scripts
	if (preg_match_all("/<script[^>]*>([\s\S]*?)<\/script>/i", $content, $scripts)) {	
		$num = count($scripts[0]);
		for($i=0; $i<$num; $i++) {
			if (empty($scripts[1][$i])) {
				//url based scripts - mark as dirty if required
				preg_match("/src=[\"']([^\"^']*\.js|[^\"^']*\.php)(\?[^\"^']*[\"']|[\"'])/", $scripts[0][$i], $url);
				if (isset($url[1]) && !empty($url[1])){
					global $csp_external_scripts;				
					if(stripos($url[1], content_url()) !== false && csp_plugin_denied_by_guard($url[1])) {
						//internal scripts
						$dirty_scripts[] = $url[1];
						$dirty_index[] = $i;
						if (stripos($url[1], plugins_url()) !== false || stripos($url[1], content_url().'/mu-plugins') !== false) {
							$dirty_plugins[] = $url[1];
						}else{
							$dirty_theme[] = $url[1];
						}
					}
					elseif (stripos($url[1], CSP_SELF_DOMAIN) === false && !in_array($url[1], $csp_external_scripts['cdn']['scripts'])) {
						//external
						$dirty_index[] = $i;			
						$csp_external_scripts['dubious']['tokens'][] = "hook:admin_footer#$i";
						$csp_external_scripts['dubious']['scripts'][] = $url[1];
					}
				}
			}else{
				//embedded scripts - wrap within exception handler
				$content = str_replace($scripts[0][$i], '<script type="text/javascript">'."\ntry {\n".csp_try_jquery_document_ready_hardening($scripts[1][$i])."\n }\ncatch(e) {\n\tcsp_self_protection.runtime.push(e.message); \n};\n</script>", $content);
			}
		}
	}
	//3rd - remove critical injected scripts
	if (count($dirty_index) > 0) {
		foreach($dirty_index as $i) {
			$content = str_replace($scripts[0][$i], '', $content);
		}
	}
	//4th - define our protection
	echo '<script type="text/javascript">csp_self_protection.dirty_theme = csp_self_protection.dirty_theme.concat('.json_encode($dirty_theme).");</script>\n";
	echo '<script type="text/javascript">csp_self_protection.dirty_plugins = csp_self_protection.dirty_plugins.concat('.json_encode($dirty_plugins).");</script>\n";
	$media_upload = ((defined('CSL_MEDIA_UPLOAD_STRIPPED') && CSL_MEDIA_UPLOAD_STRIPPED === true) ? ( function_exists("admin_url") ? admin_url('js/media-upload.js') : get_site_url().'/wp-admin/js/media-upload.js' ) : '' );
	if (!empty($media_upload))
		echo '<script type="text/javascript">csp_self_protection.dirty_enqueues = ["'.$media_upload."\"];</script>\n";
	else
		echo "<script type=\"text/javascript\">csp_self_protection.dirty_enqueues = [];</script>\n";

	global $csp_external_scripts;
	if (count($csp_external_scripts['cdn']['tokens']) > 0 || count($csp_external_scripts['dubious']['tokens']) > 0)
		echo '<script type="text/javascript">csp_self_protection.externals = '.json_encode($csp_external_scripts).";</script>\n";
	else
		echo "<script type=\"text/javascript\">csp_self_protection.externals = { 'cdn' : { 'tokens' : [], 'scripts' : [] }, 'dubious' : { 'tokens' : [], 'scripts' : [] } };</script>\n";
	
	global $csp_traced_php_errors;
	if(count($csp_traced_php_errors['messages'])) {
		echo '<script type="text/javascript">csp_self_protection.php = '.json_encode($csp_traced_php_errors['messages']).";</script>\n";
	}else{
		echo "<script type=\"text/javascript\">csp_self_protection.php = []; </script>\n";
	}
		
	echo $content;
?>
<script type="text/javascript">
	jQuery(document).ready(function($) { 
		if (
			csp_self_protection.dirty_theme.length 
			|| 
			csp_self_protection.dirty_plugins.length 
			|| 
			csp_self_protection.runtime.length 
			|| 
			csp_self_protection.dirty_enqueues.length
			||
			csp_self_protection.externals.cdn.tokens.length
			||
			csp_self_protection.externals.dubious.tokens.length
			||
			csp_self_protection.php.length
		) {
			$.post("<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>", { "action" : "csp_self_protection_result" , "data" :  csp_self_protection }, function(data) {
				$('#csp-wrap-main h2').after(data);
				$('.self-protection-details').live('click', function(event) {
					event.preventDefault();
					$('#self-protection-details').toggle();
				});
			});
		}
	});
</script>
<?php	
}

function csp_handle_csp_self_protection_result() {
	csp_po_check_security();
	load_plugin_textdomain(CSP_PO_TEXTDOMAIN, PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );	
	$incidents = 0;
	if (isset($_POST['data']['dirty_enqueues'])) $incidents += count($_POST['data']['dirty_enqueues']);
	if (isset($_POST['data']['dirty_theme'])) $incidents += count($_POST['data']['dirty_theme']);
	if (isset($_POST['data']['dirty_plugins'])) $incidents += count($_POST['data']['dirty_plugins']);
	if (isset($_POST['data']['runtime'])) $incidents += count($_POST['data']['runtime']);
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['cdn'])) $incidents += count($_POST['data']['externals']['cdn']['tokens']);
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['dubious'])) $incidents += count($_POST['data']['externals']['dubious']['tokens']);
	if (isset($_POST['data']['php'])) $incidents += count($_POST['data']['php']);
?>
<p class="self-protection"><strong><?php _e('Scripting Guard',CSP_PO_TEXTDOMAIN);?></strong> [ <a class="self-protection-details" href="javascript:void(0)"><?php _e('details',CSP_PO_TEXTDOMAIN); ?></a> ]&nbsp;&nbsp;&nbsp;<?php echo sprintf(__('The Plugin <em>Codestyling Localization</em> was forced to protect its own page rendering process against <b>%s</b> %s !', CSP_PO_TEXTDOMAIN), $incidents, _n('incident', 'incidents', $incidents, CSP_PO_TEXTDOMAIN)); ?>&nbsp;<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",CSP_PO_TEXTDOMAIN) ?>" rel="selfprotection"><img src="<?php echo CSP_PO_BASE_URL."/images/question.gif"; ?>" /></a></p>
<div id="self-protection-details" style="display:none;">
<?php
	if (isset($_POST['data']['php']) && count($_POST['data']['php'])) : ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/php-core.gif"; ?>" />
		<strong style="color:#800;"><?php _e('PHP runtime error reporting detected !',CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Reason:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('some executed PHP code is not written proper',CSP_PO_TEXTDOMAIN); ?></strong> | 
		<?php _e('Originator:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown', CSP_PO_TEXTDOMAIN); ?></strong> <small>(<?php _e('probably by Theme or Plugin',CSP_PO_TEXTDOMAIN); ?>)</small><br/>
		<?php _e('Below listed error reports has been traced and removed during page creation:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($_POST['data']['php'] as $message) : ?>
			<li><?php echo $message; ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['dirty_enqueues']) && count($_POST['data']['dirty_enqueues'])) : ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/wordpress.gif"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at admin script core detected !',CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Reason:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('misplaced core file(s) enqueued',CSP_PO_TEXTDOMAIN); ?></strong> | 
		<?php _e('Polluter:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown', CSP_PO_TEXTDOMAIN); ?></strong> <small>(<?php _e('probably by Theme or Plugin',CSP_PO_TEXTDOMAIN); ?>)</small><br/>
		<?php _e('Below listed scripts has been dequeued because of injection:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($_POST['data']['dirty_enqueues'] as $script) : ?>
			<li><?php echo strip_tags($script); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['dirty_theme']) && count($_POST['data']['dirty_theme'])) : $ct = function_exists('wp_get_theme') ? wp_get_theme() : current_theme_info(); ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/themes.gif"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at current Theme detected!',CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Name:',CSP_PO_TEXTDOMAIN);?> <strong><?php echo $ct->name; ?></strong> | 
		<?php _e('Author:',CSP_PO_TEXTDOMAIN);?> <strong><?php echo $ct->author; ?></strong><br/>
		<?php _e('Below listed scripts has been automatically stripped because of injection:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($_POST['data']['dirty_theme'] as $script) : ?>
			<li><?php echo strip_tags($script); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['dirty_plugins']) && count($_POST['data']['dirty_plugins'])) : 
		//WARNING: Plugin handling is not well coded by WordPress core
		$err = error_reporting(0);
		$plugs = get_plugins(); 
		error_reporting($err);

		foreach($plugs as $slug => $data) :
			list($slug) = explode('/', $slug);
			$affected = array();
			foreach($_POST['data']['dirty_plugins'] as $script) {
				if (stripos($script, $slug) !== false) $affected[] = $script;
			}
			if (count($affected) == 0) continue;
?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/plugins.gif"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at 3rd party Plugin detected!' ,CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Name:',CSP_PO_TEXTDOMAIN);?> <strong><?php echo $data['Name']; ?></strong> | 
		<?php _e('Author:',CSP_PO_TEXTDOMAIN);?> <strong><?php echo $data['Author']; ?></strong><br/>
		<?php _e('Below listed scripts has been automatically stripped because of injection:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($affected as $script) : ?>
			<li><?php echo strip_tags($script); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['runtime']) && count($_POST['data']['runtime'])) : ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/badscript.png"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at 3rd party inlined Javascript(s) detected!' ,CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Reason:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('javascript runtime exception', CSP_PO_TEXTDOMAIN); ?></strong> | 
		<?php _e('Polluter:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown', CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Below listed exception(s) has been caught and traced:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php foreach($_POST['data']['runtime'] as $script) : ?>
			<li><?php echo strip_tags(stripslashes($script)); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>	
<?php
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['dubious']) && count($_POST['data']['externals']['dubious']['tokens'])) : $errors = 0; ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/dubious-scripts.png"; ?>" />
		<strong style="color:#800;"><?php _e('Malfunction at dubious external scripts detected !' ,CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Reason:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown external script has been enqueued or hardly attached.', CSP_PO_TEXTDOMAIN); ?></strong> | 
		<?php _e('Polluter:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('unknown', CSP_PO_TEXTDOMAIN); ?></strong> <small>(<?php _e('probably by Theme or Plugin',CSP_PO_TEXTDOMAIN); ?>)</small><br/>
		<?php _e('Below listed external scripts have been traced, verified and automatically stripped because of injection:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php  
		for($i=0;$i<count($_POST['data']['externals']['dubious']['tokens']); $i++) :
				$token = $_POST['data']['externals']['dubious']['tokens'][$i];
				$script = $_POST['data']['externals']['dubious']['scripts'][$i];
				$res = wp_remote_head($script, array('sslverify' => false));
				$style = (($res === false || (is_object($res) && get_class($res) == 'WP_Error') || $res['response']['code'] != 200) ? ' style="color: #800;"': '' ) ;
				if(!empty($style)) $errors += 1; 
		?>
			<li<?php echo $style; ?>>[<strong><?php echo strip_tags(stripslashes($token)); ?></strong>] - <span class="cdn-file"><?php echo strip_tags(stripslashes($script));?></span> <img src="<?php echo CSP_PO_BASE_URL."/images/status-".(empty($style) ? '200' : '404').'.gif'; ?>" /></li>
		<?php endfor; ?>
		</ol>
		<?php if ($errors > 0) : ?>
		<p style="color:#800;font-weight:bold;"><?php 
			$text = sprintf(_n('%d file', '%d files', $errors, CSP_PO_TEXTDOMAIN), $errors);
			echo sprintf(__('This page will not work as expected because %s could not be get from CDN. Check and update the Plugin doing your CDN redirection!',CSP_PO_TEXTDOMAIN), $text); 
		?></p>
		<?php endif; ?>
		</div>
	<?php endif; ?>		
<?php
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['cdn']) && count($_POST['data']['externals']['cdn']['tokens'])) : $errors = 0; ?>
		<div style="border-top: 1px dashed gray; padding-top: 10px;">
		<img class="alignleft" alt="" src="<?php echo CSP_PO_BASE_URL."/images/cdn-scripts.png"; ?>" />
		<strong style="color:#008;"><?php _e('CDN based script loading redirection detected!' ,CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Warning:',CSP_PO_TEXTDOMAIN);?> <strong><?php _e('may break the dependency script loading feature within WordPress core files.', CSP_PO_TEXTDOMAIN); ?></strong><br/>
		<?php _e('Below listed redirects have been traced and verified but not revoked:',CSP_PO_TEXTDOMAIN); ?><br/>
		<ol>
		<?php  
		for($i=0;$i<count($_POST['data']['externals']['cdn']['tokens']); $i++) :
				$token = $_POST['data']['externals']['cdn']['tokens'][$i];
				$script = $_POST['data']['externals']['cdn']['scripts'][$i];
				$res = wp_remote_head($script, array('sslverify' => false));
				$style = (($res === false || (is_object($res) && get_class($res) == 'WP_Error') || $res['response']['code'] != 200) ? ' style="color: #800;"': '' ) ;
				if(!empty($style)) $errors += 1; 
		?>
			<li<?php echo $style; ?>>[<strong><?php echo strip_tags(stripslashes($token)); ?></strong>] - <span class="cdn-file"><?php echo strip_tags(stripslashes($script));?></span> <img src="<?php echo CSP_PO_BASE_URL."/images/status-".(empty($style) ? '200' : '404').'.gif'; ?>" /></li>
		<?php endfor; ?>
		</ol>
		<?php if ($errors > 0) : ?>
		<p style="color:#800;font-weight:bold;"><?php 
			$text = sprintf(_n('%d file', '%d files', $errors, CSP_PO_TEXTDOMAIN), $errors);
			echo sprintf(__('This page will not work as expected because %s could not be get from CDN. Check and update the Plugin doing your CDN redirection!',CSP_PO_TEXTDOMAIN), $text); 
		?></p>
		<?php endif; ?>
		</div>
	<?php endif; ?>		
</div>
<?php
	exit();
}
function csp_redirect_prototype_js($src, $handle) {
	global $wp_version;
	if (version_compare($wp_version, '3.5-alpha', '>=')) {
		$handles = array(
			'prototype' 			=> 'prototype',
			'scriptaculous-root' 	=> 'wp-scriptaculous',
			'scriptaculous-effects' => 'effects'
		);
		//load own older versions of the scripts that are working!
		if (isset($handles[$handle])) {
			return CSP_PO_BASE_URL.'/js/'.$handles[$handle].'.js';
		}
	}
	return $src;
}

function csp_po_admin_head() {
	if (!function_exists('wp_enqueue_style') 
		&& 
		preg_match("/^codestyling\-localization\/codestyling\-localization\.php/", $_GET['page'])
	) {
		print '<link rel="stylesheet" href="'.get_site_url()."/wp-includes/js/thickbox/thickbox.css".'" type="text/css" media="screen"/>';
		print '<link rel="stylesheet" href="'.CSP_PO_BASE_URL.'/css/ui.all.css'.'" type="text/css" media="screen"/>';
		print '<link rel="stylesheet" href="'.CSP_PO_BASE_URL.'/css/plugin.css'.'" type="text/css" media="screen"/>';
		if(function_exists('is_rtl') && is_rtl())
			print '<link rel="stylesheet" href="'.CSP_PO_BASE_URL.'/css/plugin-rtl.css'.'" type="text/css" media="screen"/>';
	}
}

function csp_extend_user_profile($profileuser) {
	if (!@is_object($profiluser)) {
		$profileuser = wp_get_current_user();
	}
	$func = function_exists('get_user_meta') ? 'get_user_meta' : 'get_usermeta';
?>
<h3 id="translations"><?php _e('Translation API Keys', CSP_PO_TEXTDOMAIN); ?><br/><small><em>(Codestyling Localization)</em></small></h3>
<table class="form-table">
<tr>
<th><label for="google-api-key"><?php _e('Google Translate API Key', CSP_PO_TEXTDOMAIN); ?></label></th>
<td><input type="text" class="regular-text" name="csp-google-api-key" id="csp-google-api-key" value="<?php echo call_user_func($func, $profileuser->ID, 'csp-google-api-key', true); ?>" autocomplete="off" />
</tr>
<tr>
<th><label for="microsoft-api-client-id"><?php _e('Microsoft Translator - Client ID', CSP_PO_TEXTDOMAIN); ?></label></th>
<td><input type="text" class="regular-text" name="csp-microsoft-api-client-id" id="csp-microsoft-api-client-id" value="<?php echo call_user_func($func, $profileuser->ID, 'csp-microsoft-api-client-id', true); ?>" autocomplete="off" />
</tr>
<tr>
<th><label for="microsoft-api-client-secret"><?php _e('Microsoft Translator - Client Secret', CSP_PO_TEXTDOMAIN); ?></label></th>
<td><input type="text" class="regular-text" name="csp-microsoft-api-client-secret" id="csp-microsoft-api-client-secret" value="<?php echo call_user_func($func, $profileuser->ID, 'csp-microsoft-api-client-secret', true); ?>" autocomplete="off" />
</tr>
</table>
<?php
}

function csp_save_user_profile() {
	$myself = wp_get_current_user();
	$func = function_exists('update_user_meta') ? 'update_user_meta' : 'update_usermeta';
	if (isset($_POST['csp-google-api-key'])) {
		call_user_func($func, $myself->ID, 'csp-google-api-key', $_POST['csp-google-api-key']);
	}
	if (isset($_POST['csp-microsoft-api-client-id'])) {
		call_user_func($func, $myself->ID, 'csp-microsoft-api-client-id', $_POST['csp-microsoft-api-client-id']);
	}
	if (isset($_POST['csp-microsoft-api-client-secret'])) {
		call_user_func($func, $myself->ID, 'csp-microsoft-api-client-secret', $_POST['csp-microsoft-api-client-secret']);
	}
}

function csp_get_translate_api_type() {
	$api_type = (string)get_option('codestyling-localization.translate-api', 'none' );
	switch($api_type) {
		case 'google':
			if(!defined('GOOGLE_TRANSLATE_KEY')) $api_type = 'none';
			break;
		case 'microsoft':
			if(!defined('MICROSOFT_TRANSLATE_CLIENT_ID') || !defined('MICROSOFT_TRANSLATE_CLIENT_SECRET') || !function_exists('curl_version')) $api_type = 'none';
			break;
		default:
			$api_type = 'none';
			break;
	}
	return $api_type;
}

