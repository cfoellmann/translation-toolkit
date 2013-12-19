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
 * FEATURE REMOVED: Script Guard
 */

// add_action('admin_enqueue_scripts', 'csp_start_protection', 0);
// add_action('in_admin_footer', 'csp_start_protection', 0);
// add_action('admin_head', 'csp_self_script_protection_head', 9999);
// add_action('admin_print_footer_scripts', 'csp_self_script_protection_footer', 9999);

// add_filter('print_scripts_array', 'csp_filter_print_scripts_array', 0);

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

function csp_start_protection($hook_suffix) {
	ob_start();
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
		$num = count( $scripts[0]);
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
	if (count( $dirty_index) > 0) {
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
	if (count( $csp_external_scripts['cdn']['tokens']) > 0 || count( $csp_external_scripts['dubious']['tokens']) > 0)
		echo '<script type="text/javascript">csp_self_protection.externals = '.json_encode($csp_external_scripts).";</script>\n";
	else
		echo "<script type=\"text/javascript\">csp_self_protection.externals = { 'cdn' : { 'tokens' : [], 'scripts' : [] }, 'dubious' : { 'tokens' : [], 'scripts' : [] } };</script>\n";
	
	global $csp_traced_php_errors;
	if(count( $csp_traced_php_errors['messages'])) {
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

<style type="text/css">
.self-protection {
	background: #ffebe8 url('../images/self-protection.png') 5px 5px no-repeat;
	padding: 8px 20px;
	padding-left: 40px;
	border: solid 1px #666;
}

#self-protection-details {
	background-color: #fffad8;
	padding: 5px;
	border: solid 1px #666;
}

#self-protection-details ol {
	list-style-type: circle;
	margin-left: 80px;
}

#self-protection-details ol li {
	font-size: 10px;
	margin-bottom: 0;
}

#self-protection-details > div {
	padding: 5px
}

#self-protection-details > div img {
	margin-right: 10px
}

</style>
<?php	
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
		$num = count( $scripts[0]);
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
	if (count( $dirty_index) > 0) {
		foreach($dirty_index as $i) {
			$content = str_replace($scripts[0][$i], '', $content);
		}
	}
	//4th - define our protection
	echo '<script type="text/javascript">var csp_self_protection = { "dirty_theme" : '.json_encode($dirty_theme).', "dirty_plugins" : ' . json_encode($dirty_plugins). ", \"runtime\" : [] };</script>\n";
	echo $content;
}

add_action('wp_ajax_csp_self_protection_result', 'csp_handle_csp_self_protection_result' );
function csp_handle_csp_self_protection_result() {
	csp_po_check_security();
	load_plugin_textdomain('translation-toolkit', PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );	
	$incidents = 0;
	if (isset($_POST['data']['dirty_enqueues'])) $incidents += count( $_POST['data']['dirty_enqueues']);
	if (isset($_POST['data']['dirty_theme'])) $incidents += count( $_POST['data']['dirty_theme']);
	if (isset($_POST['data']['dirty_plugins'])) $incidents += count( $_POST['data']['dirty_plugins']);
	if (isset($_POST['data']['runtime'])) $incidents += count( $_POST['data']['runtime']);
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['cdn'])) $incidents += count( $_POST['data']['externals']['cdn']['tokens']);
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['dubious'])) $incidents += count( $_POST['data']['externals']['dubious']['tokens']);
	if (isset($_POST['data']['php'])) $incidents += count( $_POST['data']['php']);
?>
<p class="self-protection"><strong><?php _e( 'Scripting Guard', 'translation-toolkit' ); ?></strong> [ <a class="self-protection-details" href="javascript:void(0)"><?php _e( 'details', 'translation-toolkit' ); ?></a> ]&nbsp;&nbsp;&nbsp;<?php echo sprintf( __( 'The Plugin <em>Codestyling Localization</em> was forced to protect its own page rendering process against <b>%s</b> %s !', 'translation-toolkit' ), $incidents, _n('incident', 'incidents', $incidents, 'translation-toolkit' )); ?>&nbsp;<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?", 'translation-toolkit' ) ?>" rel="selfprotection"><img src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/question.gif"; ?>" /></a></p>
<div id="self-protection-details" style="display:none; ?>
<?php
	if (isset($_POST['data']['php']) && count( $_POST['data']['php'])) : ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/php-core.gif"; ?>" />
		<strong style="color:#800; ?><?php _e( 'PHP runtime error reporting detected !', 'translation-toolkit' ); ?></strong><br/>
		<?php _e( 'Reason:', 'translation-toolkit' ); ?> <strong><?php _e( 'some executed PHP code is not written proper', 'translation-toolkit' ); ?></strong> | 
		<?php _e( 'Originator:', 'translation-toolkit' ); ?> <strong><?php _e( 'unknown', 'translation-toolkit' ); ?></strong> <small>(<?php _e( 'probably by Theme or Plugin', 'translation-toolkit' ); ?>)</small><br/>
		<?php _e( 'Below listed error reports has been traced and removed during page creation:', 'translation-toolkit' ); ?><br/>
		<ol>
		<?php foreach($_POST['data']['php'] as $message) : ?>
			<li><?php echo $message; ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['dirty_enqueues']) && count( $_POST['data']['dirty_enqueues'])) : ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/wordpress.gif"; ?>" />
		<strong style="color:#800; ?><?php _e( 'Malfunction at admin script core detected !', 'translation-toolkit' ); ?></strong><br/>
		<?php _e( 'Reason:', 'translation-toolkit' ); ?> <strong><?php _e( 'misplaced core file(s) enqueued', 'translation-toolkit' ); ?></strong> | 
		<?php _e( 'Polluter:', 'translation-toolkit' ); ?> <strong><?php _e( 'unknown', 'translation-toolkit' ); ?></strong> <small>(<?php _e( 'probably by Theme or Plugin', 'translation-toolkit' ); ?>)</small><br/>
		<?php _e( 'Below listed scripts has been dequeued because of injection:', 'translation-toolkit' ); ?><br/>
		<ol>
		<?php foreach($_POST['data']['dirty_enqueues'] as $script) : ?>
			<li><?php echo strip_tags($script); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['dirty_theme']) && count( $_POST['data']['dirty_theme'])) : $ct = function_exists('wp_get_theme') ? wp_get_theme() : current_theme_info(); ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/themes.gif"; ?>" />
		<strong style="color:#800; ?><?php _e( 'Malfunction at current Theme detected!', 'translation-toolkit' ); ?></strong><br/>
		<?php _e( 'Name:', 'translation-toolkit' ); ?> <strong><?php echo $ct->name; ?></strong> | 
		<?php _e( 'Author:', 'translation-toolkit' ); ?> <strong><?php echo $ct->author; ?></strong><br/>
		<?php _e( 'Below listed scripts has been automatically stripped because of injection:', 'translation-toolkit' ); ?><br/>
		<ol>
		<?php foreach($_POST['data']['dirty_theme'] as $script) : ?>
			<li><?php echo strip_tags($script); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['dirty_plugins']) && count( $_POST['data']['dirty_plugins'])) : 
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
			if (count( $affected) == 0) continue;
?>
		<div>
		<img class="alignleft" alt="" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/plugins.gif"; ?>" />
		<strong style="color:#800; ?><?php _e( 'Malfunction at 3rd party Plugin detected!' , 'translation-toolkit' ); ?></strong><br/>
		<?php _e( 'Name:', 'translation-toolkit' ); ?> <strong><?php echo $data['Name']; ?></strong> | 
		<?php _e( 'Author:', 'translation-toolkit' ); ?> <strong><?php echo $data['Author']; ?></strong><br/>
		<?php _e( 'Below listed scripts has been automatically stripped because of injection:', 'translation-toolkit' ); ?><br/>
		<ol>
		<?php foreach($affected as $script) : ?>
			<li><?php echo strip_tags($script); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>
<?php
	if (isset($_POST['data']['runtime']) && count( $_POST['data']['runtime'])) : ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/badscript.png"; ?>" />
		<strong style="color:#800; ?><?php _e( 'Malfunction at 3rd party inlined Javascript(s) detected!' , 'translation-toolkit' ); ?></strong><br/>
		<?php _e( 'Reason:', 'translation-toolkit' ); ?> <strong><?php _e( 'javascript runtime exception', 'translation-toolkit' ); ?></strong> | 
		<?php _e( 'Polluter:', 'translation-toolkit' ); ?> <strong><?php _e( 'unknown', 'translation-toolkit' ); ?></strong><br/>
		<?php _e( 'Below listed exception(s) has been caught and traced:', 'translation-toolkit' ); ?><br/>
		<ol>
		<?php foreach($_POST['data']['runtime'] as $script) : ?>
			<li><?php echo strip_tags(stripslashes($script)); ?></li>
		<?php endforeach; ?>
		</ol>
		</div>
	<?php endif; ?>	
<?php
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['dubious']) && count( $_POST['data']['externals']['dubious']['tokens'])) : $errors = 0; ?>
		<div>
		<img class="alignleft" alt="" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/dubious-scripts.png"; ?>" />
		<strong style="color:#800; ?><?php _e( 'Malfunction at dubious external scripts detected !' , 'translation-toolkit' ); ?></strong><br/>
		<?php _e( 'Reason:', 'translation-toolkit' ); ?> <strong><?php _e( 'unknown external script has been enqueued or hardly attached.', 'translation-toolkit' ); ?></strong> | 
		<?php _e( 'Polluter:', 'translation-toolkit' ); ?> <strong><?php _e( 'unknown', 'translation-toolkit' ); ?></strong> <small>(<?php _e( 'probably by Theme or Plugin', 'translation-toolkit' ); ?>)</small><br/>
		<?php _e( 'Below listed external scripts have been traced, verified and automatically stripped because of injection:', 'translation-toolkit' ); ?><br/>
		<ol>
		<?php  
		for($i=0;$i<count( $_POST['data']['externals']['dubious']['tokens']); $i++) :
				$token = $_POST['data']['externals']['dubious']['tokens'][$i];
				$script = $_POST['data']['externals']['dubious']['scripts'][$i];
				$res = wp_remote_head($script, array('sslverify' => false));
				$style = (($res === false || (is_object($res) && get_class($res) == 'WP_Error') || $res['response']['code'] != 200) ? ' style="color: #800;"': '' ) ;
				if(!empty($style)) $errors += 1; 
		?>
			<li<?php echo $style; ?>>[<strong><?php echo strip_tags(stripslashes($token)); ?></strong>] - <span class="cdn-file"><?php echo strip_tags(stripslashes($script)); ?></span> <img src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/status-".(empty($style) ? '200' : '404').'.gif'; ?>" /></li>
		<?php endfor; ?>
		</ol>
		<?php if ($errors > 0) : ?>
		<p style="color:#800;font-weight:bold; ?><?php 
			$text = sprintf( _n('%d file', '%d files', $errors, 'translation-toolkit' ), $errors);
			echo sprintf( __( 'This page will not work as expected because %s could not be get from CDN. Check and update the Plugin doing your CDN redirection!', 'translation-toolkit' ), $text); 
		?></p>
		<?php endif; ?>
		</div>
	<?php endif; ?>		
<?php
	if (isset($_POST['data']['externals']) && isset($_POST['data']['externals']['cdn']) && count( $_POST['data']['externals']['cdn']['tokens'])) : $errors = 0; ?>
		<div style="border-top: 1px dashed gray; padding-top: 10px; ?>
		<img class="alignleft" alt="" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/cdn-scripts.png"; ?>" />
		<strong style="color:#008; ?><?php _e( 'CDN based script loading redirection detected!' , 'translation-toolkit' ); ?></strong><br/>
		<?php _e( 'Warning:', 'translation-toolkit' ); ?> <strong><?php _e( 'may break the dependency script loading feature within WordPress core files.', 'translation-toolkit' ); ?></strong><br/>
		<?php _e( 'Below listed redirects have been traced and verified but not revoked:', 'translation-toolkit' ); ?><br/>
		<ol>
		<?php  
		for($i=0;$i<count( $_POST['data']['externals']['cdn']['tokens']); $i++) :
				$token = $_POST['data']['externals']['cdn']['tokens'][$i];
				$script = $_POST['data']['externals']['cdn']['scripts'][$i];
				$res = wp_remote_head($script, array('sslverify' => false));
				$style = (($res === false || (is_object($res) && get_class($res) == 'WP_Error') || $res['response']['code'] != 200) ? ' style="color: #800;"': '' ) ;
				if(!empty($style)) $errors += 1; 
		?>
			<li<?php echo $style; ?>>[<strong><?php echo strip_tags(stripslashes($token)); ?></strong>] - <span class="cdn-file"><?php echo strip_tags(stripslashes($script)); ?></span> <img src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/status-".(empty($style) ? '200' : '404').'.gif'; ?>" /></li>
		<?php endfor; ?>
		</ol>
		<?php if ($errors > 0) : ?>
		<p style="color:#800;font-weight:bold; ?><?php 
			$text = sprintf( _n('%d file', '%d files', $errors, 'translation-toolkit' ), $errors);
			echo sprintf( __( 'This page will not work as expected because %s could not be get from CDN. Check and update the Plugin doing your CDN redirection!', 'translation-toolkit' ), $text); 
		?></p>
		<?php endif; ?>
		</div>
	<?php endif; ?>		
</div>
<?php
	exit();
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

function csp_try_jquery_document_ready_hardening_pattern($content, $pattern) {
	$pieces = explode($pattern, $content);
	if (count( $pieces) > 1) {
		for ($loop=1; $loop<count( $pieces); $loop++) {
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

function csp_filter_print_scripts_array($scripts) {
	//detect CDN script redirecting
	global $wp_scripts, $csp_external_scripts, $csp_known_wordpress_externals;
	if (is_object($wp_scripts)) {
		foreach($scripts as $token) {
			if(isset($wp_scripts->registered[$token])) {
				if (isset($wp_scripts->registered[$token]->src) && !empty($wp_scripts->registered[$token]->src)) {
					if (preg_match('|^http|', $wp_scripts->registered[$token]->src)) {
						if(!preg_match('|^'.str_replace( '.','\.',CSP_SELF_DOMAIN).'|', $wp_scripts->registered[$token]->src)) {
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

function csp_known_and_valid_cdn($url) {
	return preg_match("/^https?:\/\/[^\.]*\.wp\.com/", $url);
}

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

