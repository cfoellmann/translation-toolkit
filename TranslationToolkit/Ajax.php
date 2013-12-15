<?php
/**
 * @author Translation Toolkit Contributors <https://github.com/wp-repository/translation-toolkit/graphs/contributors>
 * @license GPLv2 <http://www.gnu.org/licenses/gpl-2.0.html>
 * @package Translation Toolkit
 */

//avoid direct calls to this file
if ( !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden'  );
	header( 'HTTP/1.1 403 Forbidden'  );
	exit( );
}

class TranslationToolkit_Ajax {
	
	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Getter method for retrieving the object instance.
	 *
	 * @since 1.0.0
	 */
	public static function get_instance() {

		return self::$instance;

	} // END get_instance()

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		self::$instance = $this;
		
		add_action( 'wp_ajax_dlg_new', array( $this, 'dlg_new' ) );
		add_action( 'wp_ajax_dlg_delete', array( $this, 'dlg_delete' ) );
		add_action( 'wp_ajax_dlg_rescan', array( $this, 'dlg_rescan' ) );
		add_action( 'wp_ajax_dlg_show_source', array( $this, 'dlg_show_source' ) );

		add_action( 'wp_ajax_merge_from_maintheme', array( $this, 'merge_from_maintheme' ) );
		add_action( 'wp_ajax_create', array( $this, 'create' ) );
		add_action( 'wp_ajax_destroy', array( $this, 'destroy' ) );
		add_action( 'wp_ajax_scan_source_file', array( $this, 'scan_source_file' ) );	
		add_action( 'wp_ajax_change_low_memory_mode', array( $this, 'change_low_memory_mode' ) );
		
		add_action( 'wp_ajax_change_permission', array( $this, 'change_permission' ) );
		add_action( 'wp_ajax_launch_editor', array( $this, 'launch_editor' ) );
		add_action( 'wp_ajax_translate_by_google', array( $this, 'translate_by_google' ) );
		add_action( 'wp_ajax_translate_by_microsoft', array( $this, 'translate_by_microsoft' ) );
		add_action( 'wp_ajax_save_catalog_entry', array( $this, 'save_catalog_entry' ) );
		add_action( 'wp_ajax_generate_mo_file', array( $this, 'generate_mo_file' ) );
		add_action( 'wp_ajax_create_language_path', array( $this, 'create_language_path' ) );
		add_action( 'wp_ajax_create_pot_indicator', array( $this, 'create_pot_indicator' ) );
		
	} // END __construct()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function dlg_new() {
		TranslationToolkit_Helpers::check_security();
		
		$login_label = TranslationToolkit_Locale::login_label();
		$sys_locales = TranslationToolkit_Locale::sys_locales();
		?>
		<table class="widefat" cellspacing="2px">
			<tr>
				<td nowrap="nowrap"><strong><?php _e( 'Project-Id-Version', 'translation-toolkit' ); ?>:</strong></td>
				<td><?php echo strip_tags( rawurldecode( $_POST['name'] ) ); ?><input type="hidden" id="csp-dialog-name" value="<?php echo strip_tags( rawurldecode( $_POST['name'] ) ); ?>" /></td>
			</tr>
			<tr>
				<td><strong><?php _e('Creation-Date','translation-toolkit'); ?>:</strong></td>
				<td><?php echo date("Y-m-d H:iO"); ?><input type="hidden" id="csp-dialog-timestamp" value="<?php echo date("Y-m-d H:iO"); ?>" /></td>
			</tr>
			<tr>
				<td style="vertical-align:middle;"><strong><?php _e('Last-Translator','translation-toolkit'); ?>:</strong></td>
				<td><input style="width:330px;" type="text" id="csp-dialog-translator" value="<?php $myself = wp_get_current_user(); echo "$myself->user_nicename &lt;$myself->user_email&gt;"; ?>" /></td>
			</tr>
			<tr>
				<td valign="top"><strong><?php echo $login_label[ substr( get_locale(), 0, 2 ) ]?>:</strong></td>
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
							$total = array_keys( $sys_locales );
							foreach( $total as $key ) {
								if ( in_array( $key, $existing ) ) {
									continue;
								}
								$values = $sys_locales[ $key ];
								
								if ( get_locale() == $key ) {
									$selected = '" selected="selected';
								} else {
									$selected="";
								};
								?>
								<tr>
									<td><input type="radio" name="mo-locale" value="<?php echo $key; ?><?php echo $selected; ?>" onclick="$('submit_language').enable();$('csp-dialog-language').value = this.value;" /></td>
									<td><img alt="" title="locale: <?php echo $key ?>" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/flags/' . $sys_locales[ $key ]['country-www'].'.gif'; ?>" /></td>
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
		<div style="text-align:center; padding-top: 10px">
			<input class="button" id="submit_language" type="submit" disabled="disabled" value="<?php _e('create po-file','translation-toolkit'); ?>" onclick="return csp_create_new_pofile(this,<?php echo "'".strip_tags($_POST['type'])."'"; ?>);"/>
		</div>
	<?php
	exit();
	}
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function dlg_delete() {
		TranslationToolkit_Helpers::check_security();

		$sys_locales = TranslationToolkit_Locale::sys_locales();
		$lang = isset( $sys_locales[$_POST['language']] ) ? $sys_locales[$_POST['language']]['lang-native'] : $_POST['language'];
		?>
		<p style="text-align:center;">
			<?php echo sprintf( __( 'You are about to delete <strong>%s</strong> from "<strong>%s</strong>" permanently.<br/>Are you sure you wish to delete these files?', 'translation-toolkit'), $lang, strip_tags( rawurldecode( $_POST['name'] ) ) ); ?>
		</p>
		<div style="text-align:center; padding-top: 10px">
			<input class="button" id="submit_language" type="submit" value="<?php _e('delete files','translation-toolkit'); ?>" onclick="csp_destroy_files(this,'<?php echo str_replace("'", "\\'", strip_tags(rawurldecode($_POST['name'])))."','".strip_tags($_POST['row'])."','".strip_tags($_POST['path'])."','".strip_tags($_POST['subpath'])."','".strip_tags($_POST['language'])."','".strip_tags($_POST['numlangs']);?>' );" />
		</div>
		<?php
		
		exit();
		
	}
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function dlg_rescan() {
		TranslationToolkit_Helpers::check_security();
		
		$login_label = TranslationToolkit_Locale::login_label();
		$sys_locales = TranslationToolkit_Locale::sys_locales();	
		
		if ( $_POST['type'] == 'wordpress' ) {	
			$abs_root = rtrim( str_replace( '\\', '/', ABSPATH ), '/' );
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
				str_replace( "\\", "/", WP_PLUGIN_DIR ) . '/akismet/akismet.php'
			);
			TranslationToolkit_Helpers::rscandir_php($abs_root.'/wp-admin/', $excludes, $files);
			TranslationToolkit_Helpers::rscandir_php($abs_root.'/wp-includes/', $excludes, $files);
			//do not longer rescan old themes prior hosted the the main localization file starting from WP 3.0!
		} elseif ( $_POST['type'] == 'plugins_mu' ) {
			$files[] = strip_tags( $_POST['simplefilename'] );
		} elseif ( $_POST['textdomain'] == 'buddypress' ) {
			$files = array();
			$excludes = array(strip_tags($_POST['path']).'bp-forums/bbpress' );
			TranslationToolkit_Helpers::rscandir_php(strip_tags($_POST['path']), $excludes, $files);
		}
		else{
			$files = array();
			$excludes = array();
			if (isset($_POST['simplefilename']) && !empty($_POST['simplefilename'])) { $files[] = strip_tags($_POST['simplefilename']); }
			else { TranslationToolkit_Helpers::rscandir_php(strip_tags($_POST['path']), $excludes, $files); }
			if ($_POST['type'] == 'themes' && isset($_POST['themetemplate']) && !empty($_POST['themetemplate'])) {
				TranslationToolkit_Helpers::rscandir_php(str_replace("\\","/",WP_CONTENT_DIR).'/themes/'.strip_tags($_POST['themetemplate']).'/',$excludes, $files);
			}
		}
		$country_www = isset($sys_locales[$_POST['language']]) ? $sys_locales[$_POST['language']]['country-www'] : 'unknown';
		$lang_native = isset($sys_locales[$_POST['language']]) ? $sys_locales[$_POST['language']]['lang-native'] : $_POST['language'];
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
				<td nowrap="nowrap"><strong><?php _e('Project-Id-Version','translation-toolkit'); ?>:</strong></td>
				<td colspan="2"><?php echo strip_tags(rawurldecode($_POST['name'])); ?><input type="hidden" name="name" value="<?php echo strip_tags(rawurldecode($_POST['name'])); ?>" /></td>
			</tr>
			<tr>
				<td nowrap="nowrap"><strong><?php _e('Language Target','translation-toolkit'); ?>:</strong></td>
				<td><img alt="" title="locale: <?php echo strip_tags($_POST['language']); ?>" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/flags/' . $country_www . '.gif'; ?>" /></td>			
				<td><?php echo $lang_native; ?></td>
			</tr>	
			<tr>
				<td nowrap="nowrap"><strong><?php _e('Affected Total Files','translation-toolkit'); ?>:</strong></td>
				<td nowrap="nowrap" align="right"><?php echo count($files); ?></td>
				<td><em><?php echo "/".str_replace(str_replace("\\",'/',ABSPATH), '', strip_tags($_POST['path'])); ?></em></td>
			</tr>
			<tr>
				<td nowrap="nowrap" valign="top"><strong><?php _e('Scanning Progress','translation-toolkit'); ?>:</strong></td>
				<td id="csp-dialog-progressvalue" nowrap="nowrap" valign="top" align="right">0</td>
				<td>
					<div style="height:13px;width:290px;border:solid 1px #333;"><div id="csp-dialog-progressbar" style="height: 13px;width:0%; background-color:#0073D9"></div></div>
					<div id="csp-dialog-progressfile" style="width:290px;white-space:nowrap;overflow:hidden;font-size:8px;font-family:monospace;padding-top:3px;">&nbsp;</div>
				</td>
			<tr>
		</table>
		<div style="text-align:center; padding-top: 10px"><input class="button" id="csp-dialog-rescan" type="submit" value="<?php _e('scan now','translation-toolkit'); ?>" onclick="csp_scan_source_files(this);"/><span id="csp-dialog-scan-info" style="display:none"><?php _e('Please standby, files presently being scanned ...','translation-toolkit'); ?></span></div>
		<?php
	
		exit();
		
	} // END dlg_rescan()
	

	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function dlg_show_source() {
		TranslationToolkit_Helpers::check_security();
		
		list($file, $match_line) = explode(':', $_POST['file']);
		$l = filesize(strip_tags($_POST['path']).$file);
		$handle = fopen(strip_tags($_POST['path']).$file,'rb' );
		$content = str_replace(array("\r","\\$"),array('','$'), fread($handle, $l));
		fclose($handle);

		$msgid = $_POST['msgid'];
		$msgid = TranslationToolkit_Helpers::convert_js_input_for_source( $msgid );	
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
		foreach( $content as $line ) {
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
	} // END dlg_show_source()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function merge_from_maintheme() {
		TranslationToolkit_Helpers::check_security();
		
		$plurals = TranslationToolkit_Locale::plurals();
		//source|dest|basepath|textdomain|molist
		$tmp = array();
		$files = TranslationToolkit_Helpers::rscandir(str_replace("\\","/",WP_CONTENT_DIR).'/themes/'.strip_tags($_POST['source']).'/', "/(\.po|\.mo)$/", $tmp);
		foreach( $files as $file ) {
			$pofile = new TranslationToolkit_FileSystem();
			$target = strip_tags($_POST['basepath']).basename($file);
			if(preg_match('/\.mo/', $file)) {
				$pofile->read_mofile( $file, $plurals, false, strip_tags( $_POST['textdomain'] ) );
				$pofile->write_mofile( $target, strip_tags( $_POST['textdomain'] ) );
			}else{
				$pofile->read_pofile($file);
				if ( file_exists( $target ) ) {
					//merge it now
					$pofile->read_pofile( $target );
				}
				$pofile->write_pofile( $target, true, strip_tags( $_POST['textdomain'] ) );
			}
		}
		
		exit();
		
	} // END merge_from_maintheme()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function create() {
		TranslationToolkit_Helpers::check_security();

		$sys_locales = TranslationToolkit_Locale::sys_locales();
		$plurals = TranslationToolkit_Locale::plurals();
		$pofile = new TranslationToolkit_FileSystem();
		$filename = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).'.po';
		
		$pofile->new_pofile(
			$filename, 
			strip_tags($_POST['subpath']),
			strip_tags($_POST['name']), 
			strip_tags($_POST['timestamp']), 
			$_POST['translator'], 
			$_plurals[substr($_POST['language'],0,2)], 
			$sys_locales[$_POST['language']]['lang'], 
			$sys_locales[$_POST['language']]['country']
		);
		
		if ( !$pofile->write_pofile( $filename ) ) {
			header('Status: 404 Not Found' );
			header('HTTP/1.1 404 Not Found' );
			echo sprintf(__("You do not have the permission to create the file '%s'.", 'translation-toolkit'), $filename);
		} else {
			header('Content-Type: application/json' );
		?>
		{
			name: '<?php echo strip_tags(rawurldecode($_POST['name'])); ?>',
			row : '<?php echo strip_tags($_POST['row']); ?>',
			head: '<?php echo sprintf(_n('<strong>%d</strong> Language', '<strong>%d</strong> Languages',(int)$_POST['numlangs'],'translation-toolkit'), $_POST['numlangs']); ?>',
			path: '<?php echo strip_tags($_POST['path']); ?>',
			subpath: '<?php echo strip_tags($_POST['subpath']); ?>',
			language: '<?php echo strip_tags($_POST['language']); ?>',
			lang_native: '<?php echo $sys_locales[strip_tags($_POST['language'])]['lang-native']; ?>',
			image: '<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/flags/' . $sys_locales[ strip_tags( $_POST['language'] ) ]['country-www'] . '.gif'; ?>',
			type: '<?php echo strip_tags($_POST['type']); ?>',
			simplefilename: '<?php echo strip_tags($_POST['simplefilename']); ?>',
			transtemplate: '<?php echo strip_tags($_POST['transtemplate']); ?>',
			permissions: '<?php echo date(__('m/d/Y H:i:s','translation-toolkit'), filemtime($filename))." ". TranslationToolkit_Helpers::file_permissions($filename); ?>',
			denyscan: <?php echo strip_tags($_POST['denyscan']); ?>,
			google: "<?php echo $sys_locales[$_POST['language']]['google-api'] ? 'yes' : 'no'; ?>",
			microsoft: "<?php echo $sys_locales[$_POST['language']]['microsoft-api'] ? 'yes' : 'no'; ?>"
		}
		<?php		
		}
		exit();
		
	} // END create()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function destroy() {
		TranslationToolkit_Helpers::check_security();
		
		$pofile = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).'.po';
		$mofile = strip_tags($_POST['path'].$_POST['subpath'].$_POST['language']).'.mo';
		$error = false;

		$transfile = new TranslationToolkit_FileSystem();

		$transfile->destroy_pofile($pofile);
		$transfile->destroy_mofile($mofile);

		$num = (int)$_POST['numlangs'] - 1;
		header('Content-Type: application/json' );
		?>
		{
			row : '<?php echo strip_tags($_POST['row']); ?>',
			head: '<?php echo sprintf(_n('<strong>%d</strong> Language', '<strong>%d</strong> Languages',$num,'translation-toolkit'), $num); ?>',
			language: '<?php echo strip_tags($_POST['language']); ?>'
		}
		<?php	
		exit();
	} // END destroy()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function change_low_memory_mode() {
		TranslationToolkit_Helpers::check_security();
		update_option( 'codestyling-localization.low-memory', ( $_POST['mode'] == 'true' ? true : false ) );
		
		exit();
		
	} // END change_low_memory_mode()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function scan_source_file() {
		TranslationToolkit_Helpers::check_security();

		$low_mem_scanning = (bool)get_option( 'codestyling-localization.low-memory', false );
		$plurals = TranslationToolkit_Locale::plurals();
		$textdomain = $_POST['textdomain'];
		//TODO: give the domain into translation file as default domain
		$pofile = new TranslationToolkit_FileSystem($_POST['type']);
		
		//BUGFIX: 1.90 - may be, we have only the mo but no po, so we dump it out as base po file first
		if ( !file_exists( $_POST['pofile'] ) ) {
			//try implicite convert first and reopen as po second
			if($pofile->read_mofile(substr($_POST['pofile'],0,-2)."mo", $plurals, false, $textdomain)) {
				$pofile->write_pofile($_POST['pofile'],false,false, ($_POST['type'] == 'wordpress' ? 'no' : 'yes'));
			}
			//check, if we have to reverse all the other *.mo's too
			if($_POST['type'] == 'wordpress') {
				$root_po = basename($_POST['pofile']);
				$root_mo = substr($root_po,0,-2)."mo";
				$part = str_replace($root_po, '', $_POST['pofile']);
				if($pofile->read_mofile($part.'continents-cities-'.$root_mo, $plurals, $part.'continents-cities-'.$root_mo, $_POST['textdomain'])) {
					$pofile->write_pofile($part.'continents-cities-'.$root_po,false,false,'no' );
				}
				if($pofile->read_mofile($part.'ms-'.$root_mo, $plurals, $part.'ms-'.$root_mo, $_POST['textdomain'])) {		
					$pofile->write_pofile($part.'ms-'.$root_po,false,false,'no' );
				}
				global $wp_version;			
				if (version_compare($wp_version, '3.4-alpha', ">=")) {
					if($pofile->read_mofile($part.'admin-'.$root_mo, $plurals, $part.'admin-'.$root_mo, $_POST['textdomain'])) {
						$pofile->write_pofile($part.'admin-'.$root_po,false,false,'no' );
					}
					if($pofile->read_mofile($part.'admin-network-'.$root_mo, $plurals, $part.'admin-network-'.$root_mo, $_POST['textdomain'])) {
						$pofile->write_pofile($part.'admin-network-'.$root_po,false,false,'no' );
					}
				}
			}
		}		
		$pofile = new TranslationToolkit_FileSystem($_POST['type']);
		if ($pofile->read_pofile($_POST['pofile'])) {
			if ((int)$_POST['num'] == 0) { 

				if (!$pofile->supports_textdomain_extension() && $_POST['type'] == 'wordpress'){
					//try to merge up first all splitted translations.
					$root = basename($_POST['pofile']);
					$part = str_replace($root, '', $_POST['pofile']);
					//load existing files for backward compatibility if existing
					$pofile->read_pofile($part.'continents-cities-'.$root, $plurals, $part.'continents-cities-'.$root);
					$pofile->read_pofile($part.'ms-'.$root, $plurals, $part.'ms-'.$root);
					global $wp_version;			
					if (version_compare($wp_version, '3.4-alpha', ">=")) {
						$pofile->read_pofile($part.'admin-'.$root, $plurals, $part.'admin-'.$root);
						$pofile->read_pofile($part.'admin-network-'.$root, $plurals, $part.'admin-network-'.$root);
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
			for ( $i = $s; $i < $e; $i++ ) {
				if ( $low_mem_scanning ) {
					$options = array(
						'type' => $_POST['type'],
						'path' => $_POST['path'],
						'textdomain' => $_POST['textdomain'],
						'file' => $php_files[$i]
					);
					$r = wp_remote_post( plugin_dir_path( TranslationToolkit::get_file() ) . '/includes/low-memory-parsing.php', array( 'body' => $options ) ); // @TODO Why remote?
					$data = unserialize( base64_decode( $r['body'] ) );
					$pofile->add_messages($data);
				} else {
					$pofile->parsing_add_messages( $_POST['path'], $php_files[$i], $textdomain );
				}
			}	
			if ( $last ) {
				$pofile->parsing_finalize( $textdomain, strip_tags( rawurldecode( $_POST['name'] ) ) );
			}
			if ( $pofile->write_pofile( $_POST['pofile'], $last ) ) {
				header('Content-Type: application/json' );
				echo '{ title: "'.date(__('m/d/Y H:i:s','translation-toolkit'), filemtime($_POST['pofile']))." ". TranslationToolkit_Helpers::file_permissions($_POST['pofile']).'" }';
			} else {
				header('Status: 404 Not Found' );
				header('HTTP/1.1 404 Not Found' );
				echo sprintf(__("You do not have the permission to write to the file '%s'.", 'translation-toolkit'), $_POST['pofile']);
			}
		} else {
			header('Status: 404 Not Found' );
			header('HTTP/1.1 404 Not Found' );
			echo sprintf(__("You do not have the permission to read the file '%s'.", 'translation-toolkit'), $_POST['pofile']);
		}
		exit();
		
	} // END scan_source_file()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function change_permission() {
		TranslationToolkit_Helpers::check_security();
		
		$filename = strip_tags($_POST['file']);
		$error = false;
		$transfile = new TranslationToolkit_FileSystem();
		$transfile->change_permission( $filename );

		header('Content-Type: application/json' );
		echo '{ title: "'.date(__('m/d/Y H:i:s','translation-toolkit'), filemtime($filename))." ". TranslationToolkit_Helpers::file_permissions($filename).'" }';
		exit();
	}
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function launch_editor() {
		TranslationToolkit_Helpers::check_security();

		$plurals = TranslationToolkit_Locale::plurals();
		$f = new TranslationToolkit_FileSystem( $_POST['type'] );
		
		if ( !file_exists( $_POST['basepath'] . $_POST['file'] ) ) {
			//try implicite convert first
			if ( $f->read_mofile(substr($_POST['basepath'].$_POST['file'],0,-2)."mo", $plurals, $_POST['file'], $_POST['textdomain']) ) {
				$f->write_pofile($_POST['basepath'].$_POST['file'],false,false,'no' );
			}
			//check, if we have to reverse all the other *.mo's too
			if($_POST['type'] == 'wordpress') {
				$root_po = basename($_POST['file']);
				$root_mo = substr($root_po,0,-2)."mo";
				$part = str_replace($root_po, '', $_POST['file']);
				if($f->read_mofile($_POST['basepath'].$part.'continents-cities-'.$root_mo, $plurals, $part.'continents-cities-'.$root_mo, $_POST['textdomain'])) {
					$f->write_pofile($_POST['basepath'].$part.'continents-cities-'.$root_po,false,false,'no' );
				}
				if($f->read_mofile($_POST['basepath'].$part.'ms-'.$root_mo, $plurals, $part.'ms-'.$root_mo, $_POST['textdomain'])) {		
					$f->write_pofile($_POST['basepath'].$part.'ms-'.$root_po,false,false,'no' );
				}
				global $wp_version;			
				if (version_compare($wp_version, '3.4-alpha', ">=")) {
					if($f->read_mofile($_POST['basepath'].$part.'admin-'.$root_mo, $plurals, $part.'admin-'.$root_mo, $_POST['textdomain'])) {
						$f->write_pofile($_POST['basepath'].$part.'admin-'.$root_po,false,false,'no' );
					}
					if($f->read_mofile($_POST['basepath'].$part.'admin-network-'.$root_mo, $plurals, $part.'admin-network-'.$root_mo, $_POST['textdomain'])) {
						$f->write_pofile($_POST['basepath'].$part.'admin-network-'.$root_po,false,false,'no' );
					}
				}
			}
		}
		
		$f = new TranslationToolkit_FileSystem($_POST['type']);
		$f->read_pofile($_POST['basepath'].$_POST['file'], $plurals, $_POST['file']);
		
		if ( !$f->supports_textdomain_extension() && $_POST['type'] == 'wordpress' ) {
			//try to merge up first all splitted translations.
			$root = basename($_POST['file']);
			$part = str_replace($root, '', $_POST['file']);
			//load existing files for backward compatibility if existing
			$f->read_pofile($_POST['basepath'].$part.'continents-cities-'.$root, $plurals, $part.'continents-cities-'.$root);
			$f->read_pofile($_POST['basepath'].$part.'ms-'.$root, $plurals, $part.'ms-'.$root);
			global $wp_version;			
			if (version_compare($wp_version, '3.4-alpha', ">=")) {
				$f->read_pofile($_POST['basepath'].$part.'admin-'.$root, $plurals, $part.'admin-'.$root);
				$f->read_pofile($_POST['basepath'].$part.'admin-network-'.$root, $plurals, $part.'admin-network-'.$root);
			}
			//again read it to get the right header overwritten last
			$f->read_pofile($_POST['basepath'].$_POST['file'], $plurals, $_POST['file']);
			//overwrite with full imploded sparse file contents now
			$f->write_pofile($_POST['basepath'].$_POST['file'],false,false,'no' );
		}
		/**
		if ($f->supports_textdomain_extension() || $_POST['type'] == 'wordpress'){
			if (!defined('TRANSLATION_API_PER_USER_DONE')) csp_po_init_per_user_trans();
			$f->echo_as_json($_POST['basepath'], $_POST['file'], $csp_l10n_sys_locales, csp_get_translate_api_type());
		}else {
			header('Status: 404 Not Found' );
			header('HTTP/1.1 404 Not Found' );
			_e("Your translation file doesn't support the <em>multiple textdomains in one translation file</em> extension.<br/>Please re-scan the related source files at the overview page to enable this feature.",'translation-toolkit');
			?>&nbsp;<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e("What does that mean?",'translation-toolkit') ?>" rel="translationformat"><img src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . "images/question.gif"; ?>" /></a><?php
		}
		 * 
		 */
		exit();
	} // END launch_editor()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function save_catalog_entry() {
		TranslationToolkit_Helpers::check_security();

		$f = new TranslationToolkit_FileSystem();
		
		//opera bugfix: replace embedded \1 with \0 because Opera can't send embeded 0
		$_POST['msgid'] = str_replace("\1", "\0", $_POST['msgid']);
		$_POST['msgstr'] = str_replace("\1", "\0", $_POST['msgstr']);
		
		if ($f->read_pofile($_POST['path'].$_POST['file'])) {
			if (!$f->update_entry($_POST['msgid'], $_POST['msgstr'])) {
				header('Status: 404 Not Found' );
				header('HTTP/1.1 404 Not Found' );
				echo sprintf(__("You do not have the permission to write to the file '%s'.", 'translation-toolkit'), $_POST['file']);
			} else {
				$f->write_pofile($_POST['path'].$_POST['file']);
				header('Status: 200 Ok' );
				header('HTTP/1.1 200 Ok' );
				header('Content-Length: 1' );	
				echo "0";
			}
		} else {
			header('Status: 404 Not Found' );
			header('HTTP/1.1 404 Not Found' );
			echo sprintf(__("You do not have the permission to read the file '%s'.", 'translation-toolkit'), $_POST['file']);
		}
		
		exit();
		
	} // END save_catalog_entry()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function generate_mo_file() {
		TranslationToolkit_Helpers::check_security();
		
		$pofile = (string)$_POST['pofile'];
		$textdomain = (string)$_POST['textdomain'];
		$f = new TranslationToolkit_FileSystem();
		
		if ( !$f->read_pofile( $pofile ) ) {
			header('Status: 404 Not Found' );
			header('HTTP/1.1 404 Not Found' );
			echo sprintf( __("You do not have the permission to read the file '%s'.", 'translation-toolkit' ), $pofile );
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
		} elseif ( preg_match( "|^" . $pl_dir . "|", $mo )|| preg_match( "|^" . $plm_dir . "|", $mo ) ) {
			//we are a normal or wpmu plugin
			if ((strpos($parts['basename'], $textdomain) === false) && ($textdomain != 'default')) {
				preg_match("/([a-z][a-z]_[A-Z][A-Z]\.mo)$/", $parts['basename'], $h);
				if (!empty($textdomain)) {
					$mo	= $parts['dirname'].'/'.$textdomain.'-'.$h[1];
				}else {
					$mo	= $parts['dirname'].'/'.$h[1];
				}
			}
		} else {
			//we are a theme plugin, could be tested but skipped for now.
		}

		if ( $f->is_illegal_empty_mofile( $textdomain ) ) {
			header( 'Status: 404 Not Found' );
			header( 'HTTP/1.1 404 Not Found' );
			_e( "You are trying to create an empty mo-file without any translations. This is not possible, please translate at least one entry.", 'translation-toolkit' );
			exit();
		}

		if ( !$f->write_mofile( $mo,$textdomain ) ) {
			header( 'Status: 404 Not Found' );
			header( 'HTTP/1.1 404 Not Found' );
			echo sprintf( __( "You do not have the permission to write to the file '%s'.", 'translation-toolkit' ), $mo );
			exit();
		}

		header( 'Content-Type: application/json' );
		?>
		{
			filetime: '<?php echo date (__('m/d/Y H:i:s','translation-toolkit'), filemtime($mo)); ?>'
		}
		<?php
		
		exit();
		
	} // END generate_mo_file()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function create_language_path() {
		TranslationToolkit_Helpers::check_security();

		$path = strip_tags( $_POST['path'] );
		$pofile = new TranslationToolkit_FileSystem();

		if ( !$pofile->create_directory( $path ) ) {
			header( 'Status: 404 Not Found' );
			header( 'HTTP/1.1 404 Not Found' );
			_e( "You do not have the permission to create a new Language File Path.<br/>Please create the appropriated path using your FTP access.", 'translation-toolkit' );
		} else {
			header( 'Status: 200 ok' );
			header( 'HTTP/1.1 200 ok' );
			header( 'Content-Length: 1' );	
			print 0;
		}
		
		exit();
		
	} // END create_language_path()
	
	/**
	 * @TODO
	 *
	 * @since 1.0.0
	 */
	function create_pot_indicator() {
		TranslationToolkit_Helpers::check_security();
		
		$sys_locales = TranslationToolkit_Locale::sys_locales();
		$plurals = TranslationToolkit_Locale::plurals();
		$pofile = new TranslationToolkit_FileSystem();
		$filename = strip_tags($_POST['potfile']);
		$locale = 'en_US';
		
		$pofile->new_pofile(
			$filename, 
			'/',
			'PlaceHolder', 
			date("Y-m-d H:iO"), 
			'none', 
			$plurals[substr($locale,0,2)], 
			$sys_locales[$locale]['lang'], 
			$sys_locales[$locale]['country']
		);
		
		if( !$pofile->write_pofile( $filename ) ) {
			header( 'Status: 404 Not Found' );
			header( 'HTTP/1.1 404 Not Found' );
			echo sprintf( __( "You do not have the permission to create the file '%s'.", 'translation-toolkit' ), $filename );
		}
		else{	
			header( 'Status: 200 ok' );
			header( 'HTTP/1.1 200 ok' );
			header( 'Content-Length: 1' );	
			print 0;
		}
	/*	
		$handle = @fopen(strip_tags($_POST['potfile']), "w");

		if ($handle === false) {
			header('Status: 404 Not Found' );
			header('HTTP/1.1 404 Not Found' );
			_e("You do not have the permission to choose the translation file directory<br/>Please upload at least one language file (*.mo|*.po) or an empty template file (*.pot) at the appropriated folder using FTP.", 'translation-toolkit');
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
	} // END create_pot_indicator()
	
} // END class TranslationToolkit_Ajax
