<?php
/**
 * @author Translation Toolkit Contributors <https://github.com/wp-repository/translation-toolkit/graphs/contributors>
 * @license GPLv2 <http://www.gnu.org/licenses/gpl-2.0.html>
 * @package Translation Toolkit
 */

//avoid direct calls to this file
if ( !function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

class TranslationToolkit_Helpers {

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

	} // END __construct()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function check_security() {

		if ( !is_user_logged_in() || !current_user_can( apply_filters( 'tt_settings_cap', 'manage_options' ) ) ) {
			wp_die( __( 'You do not have permission to manage translation files.', 'translation-toolkit' ) );
		}

	} // END check_security()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function check_filesystem() {

		//file system investigation
		if ( function_exists( 'get_filesystem_method' ) ) {
			$fsm = get_filesystem_method( array() );
			define( "CSL_FILESYSTEM_DIRECT", $fsm == 'direct' );
		} else {
			define( "CSL_FILESYSTEM_DIRECT", true );
		}

	} // END check_filesystem()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function find_translation_template( &$files ) {

		$result = null;
		foreach( $files as $tt ) {
			if ( preg_match( '/\.pot$/', $tt ) ) {
				$result = $tt;
			}
		}

		return $result;

	} // END find_translation_template()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function convert_js_input_for_source( $str ) {

		$search = array( '\\\\\"', '\\\\n', '\\\\t', '\\\\$', '\\0', "\\'", '\\\\' );
		$replace = array( '"', "\n", "\\t", "\\$", "\0", "'", "\\" );
		$str = str_replace( $search, $replace, $str );

		return $str;

	} // END convert_js_input_for_source()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function get_packages( $type ) {

		$res = array();
		$do_compat_filter = ( $type == 'compat' );
		$do_security_filter = ( $type == 'security' );

		if ( $do_compat_filter || $do_security_filter ) {
			$type = '';
		}

		if ( empty( $type ) || ( $type == 'all' ) || ( $type == 'wordpress' ) ) {
			if ( !$do_compat_filter && !$do_security_filter ) {
				$res[] = self::get_wordpress_capabilities();
			}
		}
		if ( empty( $type ) || ( $type == 'all' ) || ( $type == 'plugins' ) ) {
			//WARNING: Plugin handling is not well coded by WordPress core
			$err = error_reporting(0);
			$plugs = get_plugins();
			error_reporting($err);
			$textdomains = array();

			foreach( $plugs as $key => $value ) {
				$data = null;
				if ( dirname( $key ) == 'buddypress' ) {

					if ( $do_compat_filter || $do_security_filter ) {
						continue;
					}

					$data = self::get_buddypress_capabilities( $key, $value );
					$res[] = $data;
					$data = self::get_bbpress_on_buddypress_capabilities( $key, $value );
					if ($data !== false) $res[] = $data;
				} else {
					$data = self::get_plugin_capabilities( $key, $value );
					if (!$data['gettext_ready']) continue;
					if (in_array($data['textdomain'], $textdomains)) {
						for ($i=0; $i<count( $res); $i++) {
							if ($data['textdomain'] == $res[$i]['textdomain']) {
								$res[$i]['child-plugins'][] = $data;
								break;
							}
						}
					} else {
						if ($do_compat_filter && !isset($data['dev-hints'])) continue;
						elseif ($do_security_filter && !isset($data['dev-security'])) continue;
						array_push($textdomains, $data['textdomain']);
						$res[] = $data;
					}
				}
			} // END foreach( $plugs as $key => $value )
		}

		if ( is_multisite() ) {
			if ( empty( $type ) || ( $type == 'all' ) || ( $type == 'plugins-mu' ) ) {
				$plugs = array();
				$textdomains = array();
				if ( is_dir( WPMU_PLUGIN_DIR ) ) {
					if ( $dh = opendir( WPMU_PLUGIN_DIR ) ) {
						while( ( $plugin = readdir( $dh ) ) !== false ) {
							if ( substr( $plugin, -4 ) == '.php' ) {
								$plugs[$plugin] = get_plugin_data( WPMU_PLUGIN_DIR . '/' . $plugin );
							}
						}
					}
				}
				foreach( $plugs as $key => $value ) {
					$data = self::get_plugin_mu_capabilities( $key, $value );

					if ( !$data['gettext_ready'] ) {
						continue;
					}

					if ( $do_compat_filter && !isset( $data['dev-hints']) ) {
						continue;
					} elseif ( $do_security_filter && !isset( $data['dev-security'] ) ) {
						continue;
					}

					if ( in_array($data['textdomain'], $textdomains) ) {
						for ( $i=0; $i<count( $res ); $i++ ) {
							if ( $data['textdomain'] == $res[$i]['textdomain'] ) {
								$res[$i]['child-plugins'][] = $data;
								break;
							}
						}
					} else {
						if ( $do_compat_filter && !isset( $data['dev-hints'] ) ) {
							continue;
						} elseif ( $do_security_filter && !isset( $data['dev-security'] ) ) {
							continue;
						}
						array_push( $textdomains, $data['textdomain'] );
						$res[] = $data;
					}
				}
			}
		}

		if ( empty( $type ) || ( $type == 'all' ) || ( $type == 'themes' ) ) {
			$themes = wp_get_themes();
			//WARNING: Theme handling is not well coded by WordPress core
			$err = error_reporting(0);
			$ct = wp_get_theme();
			error_reporting($err);
			foreach($themes as $key => $value) {
				$data = self::get_theme_capabilities($key, $value, $ct);
				if (!$data['gettext_ready']) continue;
				if ($do_compat_filter && !isset($data['dev-hints'])) continue;
				elseif ($do_security_filter && !isset($data['dev-security'])) continue;
				$res[] = $data;
			}
		}

		return $res;

	} // END get_packages()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function get_wordpress_capabilities() {

		$data = array();
		$data['dev-hints'] = null;
		$data['deny_scanning'] = false;
		$data['locale'] = get_locale();
		$data['type'] = 'wordpress';
		$data['img_type'] = 'wordpress';
		if ( is_multisite() ) {
			$data['img_type'] .= "_mu";
		}
		$data['type-desc'] = __( 'WordPress', 'translation-toolkit' );
		$data['name'] = "WordPress";
		$data['author'] = '<a href="http://codex.wordpress.org/WordPress_in_Your_Language\">WordPress.org</a>';
		$data['version'] = $GLOBALS['wp_version'];
		if ( is_multisite() ) {
			$data['version'] .= " | " . ( isset( $GLOBALS['wpmu_version'] ) ? $GLOBALS['wpmu_version'] : $GLOBALS['wp_version'] );
		}
		$data['description'] = "WordPress is a state-of-the-art publishing platform with a focus on aesthetics, web standards, and usability. WordPress is both free and priceless at the same time.<br />More simply, WordPress is what you use when you want to work with your blogging software, not fight it.";
		$data['status'] =  __("activated", 'translation-toolkit' );
		$data['base_path'] = str_replace( "\\","/", ABSPATH);
		$data['special_path'] = '';
		$data['filename'] = str_replace( str_replace( "\\","/", ABSPATH ), '', str_replace( "\\", "/", WP_LANG_DIR ) );
		$data['is-simple'] = false;
		$data['simple-filename'] = '';
		$data['textdomain'] = array( 'identifier' => 'default', 'is_const' => false );
		$data['languages'] = array();
		$data['is-path-unclear'] = false;
		$data['gettext_ready'] = true;
		$data['translation_template'] = null;
		$tmp = array();
		$data['is_US_Version'] = !is_dir( WP_LANG_DIR );
		if ( !$data['is_US_Version'] ) {
			$files = self::rscandir( str_replace( "\\","/", WP_LANG_DIR ).'/', "/(.\mo|\.po|\.pot)$/", $tmp);
			$data['translation_template'] = self::find_translation_template( $files );

			foreach( $files as $filename ) {
				$file = str_replace( str_replace( "\\" , "/", WP_LANG_DIR ) . '/', '', $filename );
				preg_match( "/^([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits );
				if ( empty( $hits[1] ) === false ) {
					$data['languages'][$hits[1]][$hits[2]] = array(
						'class' => "-".( is_readable( $filename ) ? 'r' : '' ) . ( is_writable( $filename ) ? 'w' : '' ),
						'stamp' => date( __( 'm/d/Y H:i:s', 'translation-toolkit' ), filemtime( $filename ) ) . " " . self::file_permissions( $filename )
					);
					$data['special_path'] = '';
				}
			}

			$data['base_file'] = (empty($data['special_path']) ? '' : $data['special_path'].'/') . $data['filename'].'/';
		}

		return $data;

	} // END get_wordpress_capabilities()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function get_buddypress_capabilities( $plug, $values ) {

		$data = array();
		$data['dev-hints'] = null;
		$data['deny_scanning'] = false;
		$data['locale'] = get_locale();
		$data['type'] = 'plugins';
		$data['img_type'] = 'buddypress';
		$data['type-desc'] = __( 'BuddyPress', 'translation-toolkit' );
		$data['name'] = $values['Name'];
		if ( isset( $values['AuthorURI'] ) ) {
			$data['author'] = '<a href="' . $values['AuthorURI'] . '">' . $values['Author'] . '</a>';
		} else {
			$data['author'] = $values['Author'];
		}
		$data['version'] = $values['Version'];
		$data['description'] = $values['Description'];
		$data['status'] = is_plugin_active($plug) ? __("activated", 'translation-toolkit' ) : __("deactivated", 'translation-toolkit' );
		$data['base_path'] = str_replace( "\\","/", WP_PLUGIN_DIR.'/'.dirname($plug).'/' );
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
		$files = self::lscandir(str_replace( "\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/bp-languages/', "/(\.mo|\.po|\.pot)$/", $tmp);
		$data['translation_template'] = self::find_translation_template( $files );
		foreach($files as $filename) {
			$file = str_replace(str_replace( "\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
			preg_match("/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);
			if (empty($hits[2]) === false) {
				$data['languages'][$hits[1]][$hits[2]] = array(
					'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
					'stamp' => date(__( 'm/d/Y H:i:s', 'translation-toolkit' ), filemtime($filename))." ". self::file_permissions($filename)
				);
			}
		}
		$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/".$data['filename']).'-';

		return $data;

	} // END get_buddypress_capabilities()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function get_bbpress_on_buddypress_capabilities( $plug, $values ) {

		$data = array();
		$data['dev-hints'] = null;
		$data['deny_scanning'] = false;
		$data['locale'] = get_locale();
		$data['type'] = 'plugins';
		$data['img_type'] = 'buddypress-bbpress';
		$data['type-desc'] = __( 'bbPress', 'translation-toolkit' );
		$data['name'] = "bbPress";
		$data['author'] = "<a href='http://bbpress.org/'>bbPress.org</a>";
		$data['version'] = '-n.a.-';
		$data['description'] = "bbPress is forum software with a twist from the creators of WordPress.";
		$data['status'] = is_plugin_active($plug) ? __("activated", 'translation-toolkit' ) : __("deactivated", 'translation-toolkit' );
		$data['base_path'] = str_replace( "\\","/", WP_PLUGIN_DIR.'/'.dirname($plug).'/bp-forums/bbpress/' );
		if ( !is_dir( $data['base_path'] ) ) {
			return false;
		}
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
		$data['is_US_Version'] = !is_dir(str_replace( "\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/bp-forums/bbpress/my-languages' );
		if ( !$data['is_US_Version'] ) {
			$tmp = array();
			$files = self::lscandir(str_replace( "\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/bp-forums/bbpress/my-languages/', "/(\.mo|\.po|\.pot)$/", $tmp);
			$data['translation_template'] = self::find_translation_template($files);
			foreach($files as $filename) {
				$file = str_replace(str_replace( "\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
				preg_match("/([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);
				if (empty($hits[2]) === false) {
					$data['languages'][$hits[1]][$hits[2]] = array(
						'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
						'stamp' => date(__( 'm/d/Y H:i:s', 'translation-toolkit' ), filemtime($filename))." ". self::file_permissions($filename)
					);
				}
			}
		}
		$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/");

		return $data;

	} // END get_bbpress_on_buddypress_capabilities

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function get_plugin_capabilities( $plug, $values ) {

		$data = array();
		$data['dev-hints'] 		= null;
		$data['dev-security'] 	= null;
		$data['deny_scanning'] 	= false;
		$data['locale'] = get_locale();
		$data['type'] = 'plugins';
		$data['img_type'] = 'plugins';
		$data['type-desc'] = __( 'Plugin', 'translation-toolkit' );
		$data['name'] = $values['Name'];
		if ( isset( $values['AuthorURI'] ) ) {
			$data['author'] = '<a href="' . $values['AuthorURI'] . '">' . $values['Author'] . '</a>';
		} else {
			$data['author'] = $values['Author'];
		}
		$data['version'] = $values['Version'];
		$data['description'] = $values['Description'];
		$data['status'] = is_plugin_active($plug) ? __("activated", 'translation-toolkit' ) : __("deactivated", 'translation-toolkit' );
		$data['base_path'] = str_replace( "\\","/", WP_PLUGIN_DIR.'/'.dirname($plug).'/' );
		$data['special_path'] = '';
		$data['filename'] = "";
		$data['is-simple'] = (dirname($plug) == '.' );
		$data['simple-filename'] = '';
		$data['is-path-unclear'] = false;
		$data['gettext_ready'] = false;
		$data['translation_template'] = null;
		if ( $data['is-simple'] ) {
			$files = array(WP_PLUGIN_DIR.'/'.$plug);
			$data['simple-filename'] = str_replace( "\\","/",WP_PLUGIN_DIR.'/'.$plug);
			$data['base_path'] = str_replace( "\\","/", WP_PLUGIN_DIR.'/' );
		} else {
			$tmp = array();
			$files = self::rscandir(str_replace( "\\","/",WP_PLUGIN_DIR).'/'.dirname($plug)."/", "/.(php|phtml)$/", $tmp);
		}
		$const_list = array();
		foreach( $files as $file ) {
			$content = file_get_contents($file);
			if (preg_match("/[^_^!]load_(|plugin_)textdomain\s*\(\s*(\'|\"|)([\w\d\-_]+|[A-Z\d\-_]+)(\'|\"|)\s*(,|\))\s*([^;]+)\)/", $content, $hits)) {
				$data['textdomain'] = array('identifier' => $hits[3], 'is_const' => empty($hits[2]) );
				$data['gettext_ready'] = true;
				$data['php-path-string'] = $hits[6];
			}
			else if (preg_match("/[^_^!]load_(|plugin_)textdomain\s*\(/", $content, $hits)) {
				//ATTENTION: it is gettext ready but we don't realy know the textdomain name! Assume it's equal to plugin's name.
				//TODO: let's think about it in future to find a better solution.
				$data['textdomain'] = array('identifier' => substr(basename($plug),0,-4), 'is_const' => false );
				$data['gettext_ready'] = true;
				$data['php-path-string'] = '';
			}
			if (isset($hits[1]) && $hits[1] != 'plugin_') 	$data['dev-hints'] = __("<strong>Loading Issue: </strong>Author is using <em>load_textdomain</em> instead of <em>load_plugin_textdomain</em> function. This may break behavior of WordPress, because some filters and actions won't be executed anymore. Please contact the Author about that.", 'translation-toolkit' );
			if ($data['gettext_ready'] && !$data['textdomain']['is_const']) break; //make it short :-)
			if (preg_match_all("/define\s*\(([^\)]+)\)/" , $content, $hits)) {
				$const_list = array_merge($const_list, $hits[1]);
			}
		}
		if ( $data['gettext_ready'] ) {

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
			if (preg_match("/(\(|\))/", $data['textdomain']['identifier'])) {
				$data['filename'] = str_replace( '.php', '', basename($plug));
				$data['textdomain']['is_const'] = false;
				$data['textdomain']['identifier'] = str_replace( '.php', '', basename($plug));
				//var_dump(str_replace( '.php', '', basename($plug)));
			}
		}

		if ( !$data['gettext_ready'] ) {
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
				if ($encrypted) {
					$data['img_type'] = 'plugins_encrypted';
					$data['dev-security'] .= __("<strong>Full Encryped PHP Code: </strong>This plugin consists out of encryped code will be <strong>eval</strong>'d at runtime! It can't be checked against exploitable code pieces. That's why it will become potential target of hidden intrusion.", 'translation-toolkit' );
					$data['deny_scanning'] = true;
				}
				else {
					$data['img_type'] = 'plugins_maybe';
					$data['dev-hints'] .= __("<strong>Textdomain definition: </strong>This plugin provides a textdomain definition at plugin header fields but seems not to load any translation file. If it doesn't show your translation, please contact the plugin Author.", 'translation-toolkit' );
				}
			}
		}

		$data['languages'] = array();
		if ($data['gettext_ready']){
			if ($data['is-simple']) { $tmp = array(); $files = self::lscandir(str_replace( "\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/', "/(\.mo|\.po|\.pot)$/", $tmp); }
			else { 	$tmp = array(); $files = self::rscandir(str_replace( "\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/', "/(\.mo|\.po|\.pot)$/", $tmp); }
			$data['translation_template'] = self::find_translation_template($files);

			if ($data['is-simple']) { //simple plugin case
				//1st - try to find the assumed one files
				foreach($files as $filename) {
					$file = str_replace(str_replace( "\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
					preg_match("/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
					if (empty($hits[2]) === false) {
						$data['languages'][$hits[1]][$hits[2]] = array(
							'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
							'stamp' => date(__( 'm/d/Y H:i:s', 'translation-toolkit' ), filemtime($filename))." ". self::file_permissions($filename)
						);
						$data['special_path'] = '';
					}
				}
				//2nd - try to re-construct, if nessessary, avoid multi textdomain issues
				if (count( $data['languages']) == 0) {
					foreach($files as $filename) {
						//bugfix: uppercase filenames supported
						preg_match("/([A-Za-z0-9\-_]+)-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
						if (empty($hits[2]) === false) {
							$data['filename'] = $hits[1];
							$data['textdomain']['identifier'] = $hits[1];
							$data['img_type'] = 'plugins_maybe';
							$data['dev-hints'] .= __("<strong>Textdomain definition: </strong>There are problems to find the used textdomain. It has been taken from existing translation files. If it doesn't work with your install, please contact the Author of this plugin.", 'translation-toolkit' );

							$data['languages'][$hits[2]][$hits[3]] = array(
								'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
								'stamp' => date(__( 'm/d/Y H:i:s', 'translation-toolkit' ), filemtime($filename))." ". self::file_permissions($filename)
							);
							$data['special_path'] = '';
						}
					}
				}
			}
			else { //complex plugin case
				//1st - try to find the assumed one files
				foreach($files as $filename) {
					$file = str_replace(str_replace( "\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
					//bugfix: uppercase folders supported
					preg_match("/([\/A-Za-z0-9\-_]*)\/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
					if (empty($hits[2]) === false) {
						//bugfix: only accept those mathing known textdomain
						if ($data['textdomain']['identifier'] == $data['filename'])
						{
							$data['languages'][$hits[2]][$hits[3]] = array(
								'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
								'stamp' => date(__( 'm/d/Y H:i:s', 'translation-toolkit' ), filemtime($filename))." ". self::file_permissions($filename)
							);
						}
						$data['special_path'] = ltrim($hits[1], "/");
					}
				}
				//2nd - try to re-construct, if nessessary, avoid multi textdomain issues
				if (count( $data['languages']) == 0) {
					foreach($files as $filename) {
						//try to re-construct from real file.
						//bugfix: uppercase folders supported, additional uppercased filenames!
						preg_match("/([\/A-Za-z0-9\-_]*)\/([\/A-Za-z0-9\-_]+)-([a-z][a-z]_[A-Z][A-Z])\.(mo|po)$/", $file, $hits);
						if (empty($hits[3]) === false) {
							$data['filename'] = $hits[2];
							$data['textdomain']['identifier'] = $hits[2];
							$data['img_type'] = 'plugins_maybe';
							$data['dev-hints'] .= __("<strong>Textdomain definition: </strong>There are problems to find the used textdomain. It has been taken from existing translation files. If it doesn't work with your install, please contact the Author of this plugin.", 'translation-toolkit' );

							$data['languages'][$hits[3]][$hits[4]] = array(
								'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
								'stamp' => date(__( 'm/d/Y H:i:s', 'translation-toolkit' ), filemtime($filename))." ".self::file_permissions($filename)
							);
							$data['special_path'] = ltrim($hits[1], "/");
						}
					}
				}
			}
			if (!$data['is-simple'] && ($data['special_path'] == '') && (count( $data['languages']) == 0)) {
				$data['is-path-unclear'] = self::has_subdirs( str_replace( "\\","/",dirname(WP_PLUGIN_DIR.'/'.$plug)).'/' );
				if ($data['is-path-unclear'] && (count( $files) > 0)) {
					$file = str_replace(str_replace( "\\","/",WP_PLUGIN_DIR).'/'.dirname($plug), '', $files[0]);
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

	} // END get_plugin_capabilities()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function get_plugin_mu_capabilities( $plug, $values ) {

		$data = array();
		$data['dev-hints'] = null;
		$data['deny_scanning'] = false;
		$data['locale'] = get_locale();
		$data['type'] = 'plugins-mu';
		$data['img_type'] = 'plugins-mu';
		$data['type-desc'] = __( 'μ Plugin', 'translation-toolkit' );
		$data['name'] = $values['Name'];
		if ( isset( $values['AuthorURI'] ) ) {
			$data['author'] = '<a href="' . $values['AuthorURI'] . '">' . $values['Author'] . '</a>';
		} else {
			$data['author'] = $values['Author'];
		}
		$data['version'] = $values['Version'];
		$data['description'] = $values['Description'];
		$data['status'] = __("activated", 'translation-toolkit' );
		$data['base_path'] = str_replace( "\\","/", WPMU_PLUGIN_DIR.'/' );
		$data['special_path'] = '';
		$data['filename'] = "";
		$data['is-simple'] = true;
		$data['simple-filename'] = str_replace( "\\","/",WPMU_PLUGIN_DIR.'/'.$plug);
		$data['is-path-unclear'] = false;
		$data['gettext_ready'] = false;
		$data['translation_template'] = null;
		$file = WPMU_PLUGIN_DIR.'/'.$plug;

		$const_list = array();
		$content = file_get_contents($file);
		if ( preg_match("/[^_^!]load_(|plugin_|muplugin_)textdomain\s*\(\s*(\'|\"|)([\w\d\-_]+|[A-Z\d\-_]+)(\'|\"|)\s*(,|\))\s*([^;]+)\)/", $content, $hits)) {
			$data['textdomain'] = array('identifier' => $hits[3], 'is_const' => empty($hits[2]) );
			$data['gettext_ready'] = true;
			$data['php-path-string'] = $hits[6];
		} elseif ( preg_match("/[^_^!]load_(|plugin_|muplugin_)textdomain\s*\(/", $content, $hits)) {
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
		if ($data['gettext_ready']){
			$tmp = array(); $files = self::lscandir(str_replace( "\\","/",dirname(WPMU_PLUGIN_DIR.'/'.$plug)).'/', "/(\.mo|\.po|\.pot)$/", $tmp);
			$data['translation_template'] = self::find_translation_template( $files );
			foreach($files as $filename) {
				$file = str_replace(str_replace( "\\","/",WPMU_PLUGIN_DIR).'/'.dirname($plug), '', $filename);
				preg_match("/".$data['filename']."-([a-z][a-z]_[A-Z][A-Z]).(mo|po)$/", $file, $hits);
				if (empty($hits[2]) === false) {
					$data['languages'][$hits[1]][$hits[2]] = array(
						'class' => "-".(is_readable($filename) ? 'r' : '').(is_writable($filename) ? 'w' : ''),
						'stamp' => date(__( 'm/d/Y H:i:s', 'translation-toolkit' ), filemtime($filename))." ". self::file_permissions($filename)
					);
					$data['special_path'] = '';
				}
			}
		}
		$data['base_file'] = (empty($data['special_path']) ? $data['filename'] : $data['special_path']."/".$data['filename']).'-';

		return $data;

	} // END get_plugin_mu_capabilities()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function get_theme_capabilities( $theme, $values, $active ) {

		$data = array();
		$data['dev-hints'] = null;
		$data['deny_scanning'] = false;

		//let's first check the whether we have a child or base theme
		if ( is_object($values) && get_class($values) == 'WP_Theme') {
			//WORDPRESS Version 3.4 changes theme handling!
			$theme_root = trailingslashit(str_replace( "\\","/", get_theme_root()));
			$firstfile = array_values($values['Template Files']);
			$firstfile = array_shift($firstfile);
			$firstfile = str_replace( "\\","/", $firstfile);
			$firstfile = str_replace($theme_root, '', $firstfile);
			$firstfile = explode('/',$firstfile);
			$firstfile = reset($firstfile);
			$data['base_path'] = $theme_root.$firstfile.'/';
		} else {
			$data['base_path'] = str_replace( "\\","/", WP_CONTENT_DIR.str_replace( 'wp-content', '', dirname($values['Template Files'][0])).'/' );
			if (file_exists($values['Template Files'][0])){
				$data['base_path'] = dirname(str_replace( "\\","/",$values['Template Files'][0])).'/';
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
		$data['type-desc'] = ($is_child_theme ? __( 'Childtheme', 'translation-toolkit' ) : __( 'Theme', 'translation-toolkit' ));
		$data['name'] = $values['Name'];
		$data['author'] = $values['Author'];
		$data['version'] = $values['Version'];
		$data['description'] = $values['Description'];
		$data['status'] = $values['Name'] == $active->name ? __("activated", 'translation-toolkit' ) : __("deactivated", 'translation-toolkit' );
	//	$data['status'] = $theme == $active->name ? __("activated", 'translation-toolkit' ) : __("deactivated", 'translation-toolkit' );
		if ($is_child_theme) {
			$data['status'] .= ' / <b></i>'.__( 'child theme of', 'translation-toolkit' ).' '.$values['Parent Theme'].'</i></b>';
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
		$files = self::rscandir($data["base_path"], "/\.(php|phtml)$/", $tmp);
		foreach($files as $themefile) {
			$main = file_get_contents($themefile);
			if (
				preg_match("/[^_^!]load_(child_theme_|theme_|)textdomain\s*\(\s*(\'|\"|)([\w\d\-_]+|[A-Z\d\-_]+)(\'|\"|)\s*(,|\))/", $main, $hits)
				||
				preg_match("/[^_^!]load_(child_theme_|theme_|)textdomain\s*\(\s*/", $main, $hits)
			) {
				if (isset($hits[1]) && $hits[1] != 'child_theme_' && $hits[1] != 'theme_') 	$data['dev-hints'] = __("<strong>Loading Issue: </strong>Author is using <em>load_textdomain</em> instead of <em>load_theme_textdomain</em> or <em>load_child_theme_textdomain</em> function. This may break behavior of WordPress, because some filters and actions won't be executed anymore. Please contact the Author about that.", 'translation-toolkit' );

				//fallback for variable names used to load textdomain, assumes theme name
				if (isset($hits[3]) && strpos($hits[3], '$') !== false) {
					unset($hits[3]);
					if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
					$data['dev-hints'] = __("<strong>Textdomain Naming Issue: </strong>Author uses a variable to load the textdomain. It will be assumed to be equal to theme name now.", 'translation-toolkit' );
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
				$lng_files = self::rscandir($dn, "/(\.mo|\.po|\.pot)$/", $tmp);
				$data['translation_template'] = self::find_translation_template($lng_files);
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
							'stamp' => date(__( 'm/d/Y H:i:s', 'translation-toolkit' ), filemtime($filename))." ". self::file_permissions($filename)
						);
						$data['filename'] = '';
						$sd = dirname(str_replace($dn, '', $filename));
						if ($sd == '.') $sd = '';
						if (!in_array($sd, $sub_dirs)) $sub_dirs[] = $sd;
					}
				}
				if ($naming_convention_error && count( $data['languages']) == 0) {
					if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
					$data['dev-hints'] .= sprintf( __("<strong>Naming Issue: </strong>Author uses unsupported language file naming convention! Instead of example <em>de_DE.po</em> the non theme standard version <em>%s</em> has been used. If you translate this Theme, only renamed language files will be working!", 'translation-toolkit' ), $values['Template'].'-de_DE.po' );
				}

				//completely other directories can be defined WP if >= 2.7.0
				global $wp_version;
				if (version_compare($wp_version, '2.7', '>=')) {
					if (count( $data['languages']) == 0) {
						$data['is-path-unclear'] = self::has_subdirs($dn);
						if ($data['is-path-unclear'] && (count( $lng_files) > 0)) {
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
					} else {
						if ($sub_dirs[0] != '') {
							$data['special_path'] = ltrim($sub_dirs[0], "/");
						}
					}
				}

			}
			if ($data['gettext_ready'] && !$data['textdomain']['is_const']) break; //make it short :-)
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
			if (
				(strpos($data['textdomain']['identifier'], '$') !== false)
				||
				(strpos($data['textdomain']['identifier'], '"') !== false)
				||
				(strpos($data['textdomain']['identifier'], '\'') !== false)
			){
				$constant_failed = true;
				$data['textdomain']['identifier'] = $values['Template'];
				if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
				$data['dev-hints'] = __("<strong>Textdomain Naming Issue: </strong>Author uses a variable to define the textdomain constant. It will be assumed to be equal to theme name now.", 'translation-toolkit' );
			}

		}
		//check now known issues for themes
		if (isset($data['textdomain']['identifier']) && $data['textdomain']['identifier'] == 'woothemes') {
			if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
			$data['dev-hints'] .= __("<strong>WooThemes Issue: </strong>The Author is known for not supporting a translatable backend. Please expect only translations for frontend or contact the Author for support!", 'translation-toolkit' );
		}
		if (isset($data['textdomain']['identifier']) && $data['textdomain']['identifier'] == 'ares' && $constant_failed) {
			if (isset($data['dev-hints'])) $data['dev-hints'] .= "<br/><br/>";
			$data['dev-hints'] .= __("<strong>Ares Theme Issue: </strong>This theme uses a textdomain defined by string concatination code. The textdomain will be patched to 'AresLanguage', please contact the theme author to change this into a fix constant value! ", 'translation-toolkit' );
			$data['textdomain']['identifier'] = 'AresLanguage';
		}

		return $data;

	} // END get_theme_capabilities()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function has_subdirs( $base= '' ) {

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

	} // END has_subdirs()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function lscandir( $base = '', $reg = '', &$data ) {

		if ( !is_dir( $base ) || !is_readable( $base ) ) {
			return $data;
		}
		$array = array_diff(scandir($base), array('.', '..'));

		foreach( $array as $value ) {
			if ( is_file( $base.$value ) && preg_match( $reg, $value ) ) {
				$data[] = str_replace( "\\","/",$base.$value);
			}
		}

		return $data;

	} // END lscandir()

	static function rscandir( $base = '', $reg = '', &$data ) {

		if (!is_dir($base) || !is_readable($base)) return $data;
		$array = array_diff(scandir($base), array('.', '..'));
		foreach($array as $value) {
			if (is_dir($base.$value)) {
				$data = self::rscandir($base.$value.'/', $reg, $data);
			} elseif (is_file($base.$value) && preg_match($reg, $value) ) {
				$data[] = str_replace( "\\","/",$base.$value);
			}
		}

		return $data;

	} // END rscandir()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function rscanpath( $base = '', &$data ) {
		if (!is_dir($base) || !is_readable($base)) {
			return $data;
		}
		$array = array_diff(scandir($base), array('.', '..'));
		foreach($array as $value) {
			if (is_dir($base.$value)) {
				$data[] = str_replace( "\\","/",$base.$value);
				$data = self::rscanpath($base.$value.'/', $data);
			}
		}

		return $data;

	} // END rscanpath()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function rscandir_php( $base = '', &$exclude_dirs, &$data ) {

		if ( !is_dir( $base ) || !is_readable( $base ) ) {
			return $data;
		}
		$array = array_diff(scandir($base), array('.', '..'));
		foreach( $array as $value ) {
			if (is_dir($base.$value)) {
				if (!in_array($base.$value, $exclude_dirs)) {
					$data = self::rscandir_php($base.$value.'/', $exclude_dirs, $data);
				}
			} elseif (is_file($base.$value) && preg_match('/\.(php|phtml)$/', $value) ) {
				$data[] = str_replace( "\\","/",$base.$value);
			}
		}

		return $data;

	}

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function file_permissions( $filename ) {

		static $R = array("---","--x","-w-","-wx","r--","r-x","rw-","rwx");
		$perm_o	= substr(decoct(fileperms( $filename )),3);

		return "[".$R[(int)$perm_o[0]] . '|' . $R[(int)$perm_o[1]] . '|' . $R[(int)$perm_o[2]]."]";

	} // END file_permissions()

} // END class TranslationToolkit_Helpers
