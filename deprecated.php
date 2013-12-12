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

function csp_po_init() {
	//currently not used, subject of later extension
	$low_mem_mode = (bool)get_option('codestyling-localization.low-memory', false);
	define('CSL_LOW_MEMORY', $low_mem_mode);	
}
