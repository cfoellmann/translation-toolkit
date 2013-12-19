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
 * FEATURE REMOVED: trace_php_errors
 */

// add_action('plugins_loaded', 'csp_trace_php_errors', 0);

$csp_traced_php_errors = array(
	'suppress_errors' => false,
	'old_handler' => null,
	'messages' => array()
);

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
  
    if ( array_key_exists($errno, $errorType)) {  
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
