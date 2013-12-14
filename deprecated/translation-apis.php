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
 * FEATURE REMOVED: Translation APIs
 */

?>
<script type="text/javascript">
/* <![CDATA[ */
	jQuery('#explain-apis').click(function(event) {
		event.preventDefault();
		jQuery('.translation-apis-info').slideToggle();
	});
	jQuery('.translation-apis input').click(function(event) {
		new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', {  
				parameters: {
					action: 'csp_po_change_translate_api',
					api_type: jQuery(this).val()
				}
		});	
	});
	
	function csp_translate_google(elem, source, dest) {
		$(elem).blur();
		$(elem).down().show();
		//resulting V1 API: {"responseData": {"translatedText":"Kann nicht öffnen zu schreiben!"}, "responseDetails": null, "responseStatus": 200}
		//resulting V2 API: { "data": { "translations" : [ { "translatedText": "Hallo Welt" } ] } }
		//TODO: can't handle google errors by own error dialog, because Thickbox is not multi instance ready (modal over modal) !!!
		new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
			{
				parameters: {
					action: 'csp_po_translate_by_google',
					msgid: $(source).value,
					destlang: csp_destlang
				},
				onSuccess: function(transport) {
					if (transport.responseJSON) {
						if (!transport.responseJSON.error) {
							//V1: $(dest).value = transport.responseJSON.responseData.translatedText;
							//V2:
							$(dest).value = transport.responseJSON.data.translations[0].translatedText;
						}else{
							//V1: alert(transport.responseJSON.responseDetails);
							//V2:
							alert(transport.responseJSON.error.errors[0].reason);
						}
					}else{
						alert(transport.responseText);
					}
					$(elem).down().hide();
				},
				onFailure: function(transport) {
					$(elem).down().hide();
					if (transport.responseJSON && transport.responseJSON.error)
						alert(transport.responseJSON.error.errors[0].reason); 
					else
						alert(transport.responseText);
				}
			}
		);
	}

	function csp_translate_microsoft(elem, source, dest) {
		$(elem).blur();
		$(elem).down().show();
		new Ajax.Request('<?php echo CSP_PO_ADMIN_URL.'/admin-ajax.php' ?>', 
			{
				parameters: {
					action: 'csp_po_translate_by_microsoft',
					msgid: $(source).value,
					destlang: csp_destlang
				},
				onSuccess: function(transport) {
					$(dest).value = transport.responseText;
					$(elem).down().hide();
				},
				onFailure: function(transport) {
					$(elem).down().hide();
					alert(transport.responseText); 
				}
			}
		);
	}
/* ]]> */
</script>

<p class="translation-apis">
	<label class="alignleft"><strong><?php _e('Translation Service-APIs:','translation-toolkit'); ?></strong></label> 
	<img class="alignleft" alt="" title="API: not used" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/off.png"; ?>" /><input id="translate-api-none" class="translate-api-none alignleft" name="translate-api" value="none" type="radio" autocomplete="off" <?php checked('none', $api_type); ?>/> <label class="alignleft" for="translate-api-none"><?php _e('None','translation-toolkit'); ?></label>
	<img class="alignleft" alt="" title="API: Google Translate" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/google.png"; ?>" /><input id="translate-api-google" class="translate-api-google alignleft" name="translate-api" value="google" type="radio" autocomplete="off" <?php checked('google', $api_type); ?> <?php disabled(false, $google_api); ?>/> <label class="alignleft<?php if(!$google_api) echo ' disabled'; ?>" for="translate-api-google"><?php _e('Google','translation-toolkit'); ?></label>
	<img class="alignleft" alt="" title="API: Microsoft Translate" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/bing.gif"; ?>" /><input id="translate-api-microsoft" class="translate-api-microsoft alignleft" name="translate-api" value="microsoft" type="radio" autocomplete="off" <?php checked('microsoft', $api_type); ?> <?php disabled(false, $microsoft_api); ?>/> <label class="alignleft<?php if(!$microsoft_api) echo ' disabled'; ?>" for="translate-api-microsoft"><?php _e('Microsoft','translation-toolkit'); ?></label>
	<?php if(defined('TRANSLATION_PROVIDER_MODE') && TRANSLATION_PROVIDER_MODE === true) : ?>
		<?php if(defined('TRANSLATION_API_PER_USER') && TRANSLATION_API_PER_USER === true) : ?>
		<a class="alignright" href="profile.php?#translations"><?php _e('User Profile settings...','translation-toolkit'); ?></a><img class="alignright" alt="" title="API: How to use" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/user.gif"; ?>" />
		<?php endif; ?>
	<?php else: ?>
	<a class="alignright" id="explain-apis" href="#"><?php _e('How to use translation API services...','translation-toolkit'); ?></a><img class="alignright" alt="" title="API: How to use" src="<?php echo plugin_dir_path( TranslationToolkit::get_file() ) . "/images/question.png"; ?>" />
	<?php endif; ?>
</p>
	
<?php if(!defined('TRANSLATION_PROVIDER_MODE') || TRANSLATION_PROVIDER_MODE === false) : ?>
<div class="translation-apis-info">
	<h5><?php _e("a) Global Unique Keys - single user configuration", 'translation-toolkit'); ?></h5>
	<div style="margin-left: 25px;">
		<small style="color: #f33;">
		<strong><?php _e('Attention:', 'translation-toolkit'); ?></strong> <?php _e('Keep in mind, that any WordPress administrator can use the service for translation purpose and may raise your costs in case of paid option used.', 'translation-toolkit'); ?>
		</small>
		<br/><br/>
		<h5>Google Translate API | <small><a target="_blank" href="https://developers.google.com/translate/v2/faq">FAQ</a></small></h5>
		<p>
			<small>
			<strong><?php _e('Attention:', 'translation-toolkit'); ?></strong>
			<?php echo sprintf(__( 'This API is not longer a free service, Google has relaunched the API in version 2 as a pay per use service. Please read the explantions at %s first.', 'translation-toolkit' ), '<a target="_blank" href="https://developers.google.com/translate/v2/terms">Terms of Service</a>' ); ?>
			<?php _e('Using this API within <em>Codestyling Localization</em> requires an API Key to be created at your Google account first. Once you have such a key, you can activate this API by defining a new constant at your <b>wp-config.php</b> file:', 'translation-toolkit'); ?>
			</br/>
			<textarea class="google" readonly="readonly">define('GOOGLE_TRANSLATE_KEY', 'enter your key here' );</textarea>
			</small>
		</p>
		<h5>Microsoft Translate API | <small><a target="_blank" href="http://social.msdn.microsoft.com/Forums/en-US/microsofttranslator/thread/c71aeddd-cc90-4228-93cc-51fb969fde09">FAQ</a></small></h5>
		<p>
			<small>
			<?php  echo sprintf(__( 'Microsoft provides the translation services with a free option of 2 million characters per month. But this also requires a subscription at %s either for free or for extended payed service volumes.', 'translation-toolkit' ), '<a target="_blank" href="http://go.microsoft.com/?linkid=9782667">Azure Marketplace</a>' ); ?>
			<?php _e('Using this API within <em>Codestyling Localization</em> requires <em>client_id</em> and <em>client_secret</em> to be created at your Azure subscription first. Once you have this values, you can activate this API by defining new constants at your <b>wp-config.php</b> file:', 'translation-toolkit'); ?>
			</br/>
			<textarea class="microsoft" readonly="readonly">
define('MICROSOFT_TRANSLATE_CLIENT_ID', 'enter your client id here' );
define('MICROSOFT_TRANSLATE_CLIENT_SECRET', 'enter your secret here' );
			</textarea>
			<br/>
			<strong><?php _e('Attention:', 'translation-toolkit'); ?></strong> <?php _e('This API additionally requires PHP curl functions and will not be available without. Current curl version:', 'translation-toolkit'); ?>
			&nbsp;<b><i><?php if (function_exists('curl_version')) { $ver = curl_version(); echo $ver['version']; } else _e('not installed','translation-toolkit'); ?></i></b>
			</small>
		</p>
	</div>
	<h5><?php _e("b) User Dedicated Keys - multiple user configurations", 'translation-toolkit'); ?></h5>
	<div style="margin-left: 25px;">
		<small style="color: #f33;">
		<strong><?php _e('Attention:', 'translation-toolkit'); ?></strong> <?php _e('This will extends all <em>User Profile</em> pages with a new section to enter all required translation key data. Keep im mind, that this data are stored at the database and are contained at SQL backups.', 'translation-toolkit'); ?>
		</small>
		<p>
		<small>
			<?php _e('You can activate the per user behavoir, if you define only a single constant at your <b>wp-config.php</b> file. This enables the new section at each <a target="_blank" href="profile.php?#translations">User Profile</a> with sufficiant permissions and is only editable by the releated logged in user.','translation-toolkit'); ?>
			<textarea class="google" readonly="readonly">define('TRANSLATION_API_PER_USER', true);</textarea>
		</small>
		</p>
	</div>
	<h5 style="border-top: 1px dashed gray;padding-top: 5px;"><?php _e("Special Hosting Configuration", 'translation-toolkit'); ?></h5>
	<div style="margin-left: 25px;">
		<small>
			<?php _e('If your are a provider and you are hosting WordPress installations for your customer, it is possible to deactivate this help information using an additional constant at your <b>wp-config.php</b> file. At single user mode (a) this simply does not show any help for API configuration, at multiuser mode (b) it shows the link to the profile page.', 'translation-toolkit'); ?>
			<textarea class="google" readonly="readonly">define('TRANSLATION_PROVIDER_MODE', true);</textarea>
		</small>
	</div>
</div>
<?php endif; ?>

<?php


function main_page() {
// [..]
	$google_api = defined('GOOGLE_TRANSLATE_KEY' );
	$microsoft_api = defined('MICROSOFT_TRANSLATE_CLIENT_ID') && defined('MICROSOFT_TRANSLATE_CLIENT_SECRET') && function_exists('curl_version' );
	$api_type = csp_get_translate_api_type();
// [..]
}



add_action('admin_init', 'csp_po_init_per_user_trans' );
	
//User Profile extension if necessary
if ( defined('TRANSLATION_API_PER_USER') && ( TRANSLATION_API_PER_USER === true ) /*&& current_user_can('manage_options')*/ ) {
	add_action( 'show_user_profile', 'csp_extend_user_profile' );
	add_action( 'personal_options_update', 'csp_save_user_profile' );
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

function csp_extend_user_profile($profileuser) {
	if (!@is_object($profiluser)) {
		$profileuser = wp_get_current_user();
	}
	$func = function_exists('get_user_meta') ? 'get_user_meta' : 'get_usermeta';
?>
<h3 id="translations"><?php _e('Translation API Keys', 'translation-toolkit'); ?><br/><small><em>(Codestyling Localization)</em></small></h3>
<table class="form-table">
<tr>
<th><label for="google-api-key"><?php _e('Google Translate API Key', 'translation-toolkit'); ?></label></th>
<td><input type="text" class="regular-text" name="csp-google-api-key" id="csp-google-api-key" value="<?php echo call_user_func($func, $profileuser->ID, 'csp-google-api-key', true); ?>" autocomplete="off" />
</tr>
<tr>
<th><label for="microsoft-api-client-id"><?php _e('Microsoft Translator - Client ID', 'translation-toolkit'); ?></label></th>
<td><input type="text" class="regular-text" name="csp-microsoft-api-client-id" id="csp-microsoft-api-client-id" value="<?php echo call_user_func($func, $profileuser->ID, 'csp-microsoft-api-client-id', true); ?>" autocomplete="off" />
</tr>
<tr>
<th><label for="microsoft-api-client-secret"><?php _e('Microsoft Translator - Client Secret', 'translation-toolkit'); ?></label></th>
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
	//OLD: $res = wp_remote_fopen("http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&format=html&q=".urlencode($msgid)."&langpair=en%7C".$_POST['destlang']);
	$res = cwp_remote_fopen("https://www.googleapis.com/language/translate/v2?key=".(defined('GOOGLE_TRANSLATE_KEY') ? GOOGLE_TRANSLATE_KEY : '')."&source=en&target=".$_POST['destlang']."&q=".urlencode($msgid));
	if ($res) {
		header('Content-Type: application/json' );
		echo $res;
	}
	else{
		header('Status: 404 Not Found' );
		header('HTTP/1.1 404 Not Found' );
		load_plugin_textdomain('translation-toolkit', PLUGINDIR.'/codestyling-localization/languages','codestyling-localization/languages' );
		_e("Sorry, Google Translation is not available.", 'translation-toolkit');	
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

add_action( 'wp_ajax_csp_po_change_translate_api', array( $this, 'csp_po_ajax_change_translate_api' ) );
function csp_po_ajax_change_translate_api() {
	TranslationToolkit_Helpers::check_security();
	$api_type = 'none';
	if (in_array($_POST['api_type'], array('google','microsoft'))) {
		$api_type = $_POST['api_type'];
	}
	update_option('codestyling-localization.translate-api', $api_type);
	exit();
}

