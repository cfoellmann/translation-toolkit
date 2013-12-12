<?php
/**
 * @author Translation Toolkit Contributors <https://github.com/wp-repository/translation-toolkit/graphs/contributors>
 * @license GPLv2 <http://www.gnu.org/licenses/gpl-2.0.html>
 * @package Translation Toolkit
 */

//avoid direct calls to this file - Period!
header( 'Status: 403 Forbidden' );
header( 'HTTP/1.1 403 Forbidden' );
exit();

/**
 * Functions to kill of without substitution
 * BELOW
 */

if (!function_exists('get_site_url')) {
	function get_site_url() { return get_option('site_url'); }
}

if (!function_exists('plugins_url')) {
	function plugins_url($plugin) {  
		return WP_PLUGIN_URL . $plugin; 
	}
}

if (!function_exists('_n')) {
	function _n() {
		$args = func_get_args();
		return call_user_func_array('__ngettext', $args);
	}
}

if (!function_exists('_n_noop')) {
	function _n_noop() {
		$args = func_get_args();
		return call_user_func_array('__ngettext_noop', $args);
	}
}

if (!function_exists('_x')) {
	function _x() {
		$args = func_get_args();
		$what = array_shift($args); 
		$args[0] = $what.'|'.$args[0];
		return call_user_func_array('_c', $args);
	}
}

if (!function_exists('esc_js')) {
	function esc_js() {
		$args = func_get_args();
		return call_user_func_array('js_escape', $args);
	}
}

if (!function_exists('__checked_selected_helper')) {
	function __checked_selected_helper( $helper, $current, $echo, $type ) {
		if ( (string) $helper === (string) $current )
			$result = " $type='$type'";
		else
			$result = '';

		if ( $echo )
			echo $result;

		return $result;
	}
}

if (!function_exists('disabled')) {
	function disabled( $disabled, $current = true, $echo = true ) {
		return __checked_selected_helper( $disabled, $current, $echo, 'disabled' );
	}	
}

if (!function_exists('file_get_contents')) {
	function file_get_contents($filename, $incpath = false, $resource_context = null) {
		if (false === $fh = fopen($filename, 'rb', $incpath)) {
			user_error('file_get_contents() failed to open stream: No such file or directory', E_USER_WARNING);
			return false;
		}
		
		clearstatcache();
		if ($fsize = @filesize($filename)) {
			$data = fread($fh, $fsize);
		} else {
			$data = '';
			while (!feof($fh)) {
				$data .= fread($fh, 8192);
			}
		}
		
		fclose($fh);
		return $data;
	}	
}

if (!function_exists('scandir')) {
	function scandir($dir) {
		$files = array();
		$dh  = @opendir($dir);
		while (false !== ($filename = @readdir($dh))) {
		    $files[] = $filename;
		}
		@closedir($dh);
		return $files;
	}
}