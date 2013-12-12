<?php
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

//////////////////////////////////////////////////////////////////////////////////////////
//	constant definition
//////////////////////////////////////////////////////////////////////////////////////////

//Enable this only for debugging reasons. 
//Attention: the strict logging may prevent WP from proper working because of many not handled issues.
//error_reporting(E_ALL|E_STRICT);
//@unlink(dirname(__FILE__).'/.htaccess' );

//if (!defined('E_RECOVERABLE_ERROR'))
//	define('E_RECOVERABLE_ERROR', 4096);
//if (!defined('E_DEPRECATED'))
//	define('E_DEPRECATED', 8192);
//if (!defined('E_USER_DEPRECATED '))
//	define('E_USER_DEPRECATED ', 16384);

define("CSP_PO_PLUGINPATH", "/" . dirname(plugin_basename( __FILE__ )));

define('CSP_PO_BASE_URL', plugins_url( CSP_PO_PLUGINPATH ));

//Bugfix: ensure valid JSON requests at IDN locations!
//Attention: Google Chrome and Safari behave in different way (shared WebKit issue or all other are wrong?)!
list($csp_domain, $csp_target) = csp_split_url( rtrim( admin_url(), '/' ) );

define('CSP_SELF_DOMAIN', $csp_domain);

if ( stripos($_SERVER['HTTP_USER_AGENT'], 'chrome') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false ) {
	define('CSP_PO_ADMIN_URL', strtolower( $csp_domain ) . $csp_target );
} else {
	if ( !class_exists('idna_convert') )
		require_once('includes/idna_convert.class.php' );
	$idn = new idna_convert();
	define('CSP_PO_ADMIN_URL', $idn->decode(strtolower($csp_domain), 'utf8').$csp_target);
}


// @TODO Implement differently
//if ( function_exists( 'csp_po_install_plugin' ) ) {
//	//rewrite and extend the error messages displayed at failed activation
//	//fall trough, if it's a real code bug forcing the activation error to get the appropriated message instead
//	if ( isset($_GET['action']) && isset($_GET['plugin']) && ($_GET['action'] == 'error_scrape') && ($_GET['plugin'] == plugin_basename(__FILE__) ) ) {
//		if ( !function_exists('token_get_all') ) {
//			echo "<table>";
//			echo "<tr style=\"font-size: 12px;\"><td><strong style=\"border-bottom: 1px solid #000;\">Codestyling Localization</strong></td><td> | ".__('required', 'translation-toolkit')."</td><td> | ".__('actual', 'translation-toolkit')."</td></tr>";			
//			echo "<tr style=\"font-size: 12px;\"><td>PHP Tokenizer Module:</td><td align=\"center\"><strong>active</strong></td><td align=\"center\"><span style=\"color:#f00;\">not installed</span></td></tr>";			
//			echo "</table>";
//		}
//	}
//}

/**
 * HELPERS
 */
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

function has_subdirs( $base='' ) {
  if ( !is_dir($base) || !is_readable($base) ) {
	  return $false;
  }
  $array = array_diff( scandir( $base ), array( '.', '..' ) );
  foreach( $array as $value ) { 
    if ( is_dir( $base . $value ) ) {
		return true;
	}
  };
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

/**
 * INIT
 */


/**
 * ADMIN
 */
