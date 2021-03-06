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

class TranslationToolkit_FileSystem extends TranslationToolkit_TranslationFile { // class CspFileSystem_TranslationFile extends CspTranslationFile

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 1.0.0
	 */
	function __construct( $type = 'unknown' ) {

		parent::__construct( $type );
		//backward compatibility
		$this->supports_filesystem = function_exists( 'request_filesystem_credentials' );
		$this->real_abspath = str_replace( '\\', '/', ABSPATH );

	} // END __construct()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 * @param string $pofile
	 */
	function destroy_pofile( $pofile ) {

		global $wp_filesystem, $parent_file;

		if ( $this->supports_filesystem) {

			$current_parent  = $parent_file;
			$parent_file 	 = 'tools.php'; //needed for screen icon :-)
			if (function_exists( 'set_current_screen' )) set_current_screen( 'tools' ); //WP 3.0 fix @todo still needed?

			//check the file system
			ob_start();
			$url = 'admin-ajax.php';
			if ( false === ( $credentials = request_filesystem_credentials( $url ) ) ) {
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}

			if ( !WP_Filesystem( $credentials) ) {
				request_filesystem_credentials( $url, '', true ); //Failed to connect, Error and request again
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}
			ob_end_clean();
			$parent_file = $current_parent;
		}

		$error = false;
		if ( !$this->supports_filesystem || $wp_filesystem->method == 'direct' ) {
			if (file_exists( $pofile)) if ( !@unlink( $pofile)) $error = sprintf( __( "You do not have the permission to delete the file '%s'.", 'translation-toolkit' ), $mofile );
		} else {
			$target_file = str_replace( '//', '/', $wp_filesystem->abspath().str_replace( $this->real_abspath, '',$pofile));
			if ( $wp_filesystem->is_file( $target_file ) ) if ( !$wp_filesystem->delete( $target_file ) ) $error = sprintf( __( "You do not have the permission to delete the file '%s'.", 'translation-toolkit' ), $pofile );
		}
		if ( $error ) {
			header( 'Status: 404 Not Found' );
			header( 'HTTP/1.1 404 Not Found' );
			echo $error;
			exit();
		}

	} // END destroy_pofile()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 * @param string $mofile
	 */
	function destroy_mofile( $mofile ) {
		global $wp_filesystem, $parent_file;

		if ( $this->supports_filesystem) {

			$current_parent  = $parent_file;
			$parent_file 	 = 'tools.php'; //needed for screen icon :-)
			if ( function_exists( 'set_current_screen' ) ) set_current_screen( 'tools' ); //WP 3.0 fix @todo still needed?

			//check the file system
			ob_start();
			$url = 'admin-ajax.php';
			if ( false === ( $credentials = request_filesystem_credentials( $url)) ) {
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}

			if ( !WP_Filesystem( $credentials ) ) {
				request_filesystem_credentials( $url, '', true ); //Failed to connect, Error and request again
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}
			ob_end_clean();
			$parent_file = $current_parent;
		}

		$error = false;
		if ( !$this->supports_filesystem || $wp_filesystem->method == 'direct' ) {
			if (file_exists( $mofile)) if ( !@unlink( $mofile)) $error = sprintf( __( "You do not have the permission to delete the file '%s'.", 'translation-toolkit' ), $mofile );
		} else {
			$target_file = str_replace( '//', '/', $wp_filesystem->abspath().str_replace( $this->real_abspath, '',$mofile ) );
			if ( $wp_filesystem->is_file( $target_file ) ) {
				if ( !$wp_filesystem->delete( $target_file ) ) {
					$error = sprintf( __( "You do not have the permission to delete the file '%s'.", 'translation-toolkit' ), $mofile );
				}
			}
		}
		if ( $error ) {
			header( 'Status: 404 Not Found' );
			header( 'HTTP/1.1 404 Not Found' );
			echo $error;
			exit();
		}

	} // END destroy_mofile()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 * @param string $path
	 */
	function create_directory( $path ) {

		global $wp_filesystem, $parent_file;

		if ( $this->supports_filesystem) {

			$current_parent  = $parent_file;
			$parent_file 	 = 'tools.php'; //needed for screen icon :-)
			if (function_exists( 'set_current_screen' )) set_current_screen( 'tools' ); //WP 3.0 fix @todo still needed?

			//check the file system
			ob_start();
			$url = 'admin-ajax.php';
			if ( false === ( $credentials = request_filesystem_credentials( $url)) ) {
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}

			if ( !WP_Filesystem( $credentials ) ) {
				request_filesystem_credentials( $url, '', true ); //Failed to connect, Error and request again
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}
			ob_end_clean();
			$parent_file = $current_parent;
		}

		if ( !$this->supports_filesystem || $wp_filesystem->method == 'direct' ) {
			return @mkdir( $path );
		} else {
			$target_dir = str_replace( '//', '/', $wp_filesystem->abspath().str_replace( $this->real_abspath, '', $path ) );
			if ( !$wp_filesystem->mkdir( $target_dir, FS_CHMOD_DIR ) && !$wp_filesystem->is_dir( $target_dir ) ) {
				return false;
			} else {
				return true;
			}
		}

	} // END create_directory()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 * @param string $filename
	 */
	function change_permission( $filename ) {

		global $wp_filesystem, $parent_file;

		if ( $this->supports_filesystem ) {

			$current_parent  = $parent_file;
			$parent_file 	 = 'tools.php'; //needed for screen icon :-)
			if (function_exists( 'set_current_screen' )) set_current_screen( 'tools' ); //WP 3.0 fix

			//check the file system
			ob_start();
			$url = 'admin-ajax.php';
			if ( false === ( $credentials = request_filesystem_credentials( $url)) ) {
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}

			if ( !WP_Filesystem( $credentials) ) {
				request_filesystem_credentials( $url, '', true); //Failed to connect, Error and request again
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}
			ob_end_clean();
			$parent_file = $current_parent;
		}

		$error = false;
		if ( !$this->supports_filesystem || $wp_filesystem->method == 'direct' || stripos( php_uname( 's' ), 'windows' ) !== false ) {
			if (file_exists( $filename ) ) {
				@chmod( $filename, 0644);
				if ( !is_writable( $filename ) ) {
					@chmod( $filename, 0664);
					if ( !is_writable( $filename ) ) {
						@chmod( $filename, 0666);
					}
					if ( !is_writable( $filename ) ) $error = __( 'Server Restrictions: Changing file rights is not permitted.', 'translation-toolkit' );
				}
			} else {
				$error = sprintf( __( "You do not have the permission to modify the file rights for a not existing file '%s'.", 'translation-toolkit' ), $filename );
			}
		} else {
			$target_file = str_replace( '//', '/', $wp_filesystem->abspath().str_replace( $this->real_abspath, '',$filename ) );
			if ( $wp_filesystem->is_file( $target_file ) ) {
				$wp_filesystem->chmod( $target_file, 0644 );
				if ( !is_writable( $filename ) ) {
					$wp_filesystem->chmod( $target_file, 0664 );
					if ( !is_writable( $filename ) ) {
						$wp_filesystem->chmod( $target_file, 0666 );
					}
					if ( !is_writable( $filename ) ) $error = __( 'Server Restrictions: Changing file rights is not permitted.', 'translation-toolkit' );
				}
			} else {
				$error = sprintf( __( "You do not have the permission to modify the file rights for a not existing file '%s'.", 'translation-toolkit' ), $filename );
			}
		}

		if ( $error ) {
			header( 'Status: 404 Not Found' );
			header( 'HTTP/1.1 404 Not Found' );
			echo $error;
			exit();
		}

	} // END change_permission()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	function write_pofile( $pofile, $last = false, $textdomain = false, $tds = 'yes' ) {

		global $wp_filesystem, $parent_file;

		if ( $this->supports_filesystem) {
			$current_parent  = $parent_file;
			$parent_file 	 = 'tools.php'; //needed for screen icon :-)
			if (function_exists( 'set_current_screen' )) set_current_screen( 'tools' ); //WP 3.0 fix

			//check the file system
			ob_start();
			$url = 'admin-ajax.php';
			if ( false === ( $credentials = request_filesystem_credentials( $url)) ) {
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}

			if ( !WP_Filesystem( $credentials) ) {
				request_filesystem_credentials( $url, '', true); //Failed to connect, Error and request again
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}
			ob_end_clean();
			$parent_file = $current_parent;
		}

		if ( !$this->supports_filesystem || $wp_filesystem->method == 'direct' ) {
			return parent::write_pofile( $pofile, $last, $textdomain, $tds);
		} else {
			$target_file = str_replace( '//', '/', $wp_filesystem->abspath().str_replace( $this->real_abspath, '',$pofile));
			return $wp_filesystem->put_contents( $target_file, parent::ftp_get_pofile_content( $pofile, $last, $textdomain, $tds), FS_CHMOD_FILE);
		}

	} // END write_pofile()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 * @param string $mofile
	 * @param string $textdomain
	 */
	function write_mofile( $mofile, $textdomain ) {

		global $wp_filesystem, $parent_file;

		if ( $this->supports_filesystem) {
			$current_parent  = $parent_file;
			$parent_file 	 = 'tools.php'; //needed for screen icon :-)
			if ( function_exists( 'set_current_screen' ) ) {
				set_current_screen( 'tools' ); //WP 3.0 fix - @todo still needed?
			}

			//check the file system
			ob_start();
			$url = 'admin-ajax.php';
			if ( false === ( $credentials = request_filesystem_credentials( $url)) ) {
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}

			if ( !WP_Filesystem( $credentials) ) {
				request_filesystem_credentials( $url, '', true); //Failed to connect, Error and request again
				$data = ob_get_contents();
				ob_end_clean();
				if ( !empty( $data ) ){
					header( 'Status: 401 Unauthorized' );
					header( 'HTTP/1.1 401 Unauthorized' );
					echo $data;
					exit;
				}
				return;
			}
			ob_end_clean();
			$parent_file = $current_parent;
		}

		if ( !$this->supports_filesystem || $wp_filesystem->method == 'direct' ) {
			return parent::write_mofile( $mofile, $textdomain );
		} else {
			$target_file = str_replace( '//', '/', $wp_filesystem->abspath().str_replace( $this->real_abspath, '',$mofile));
			return $wp_filesystem->put_contents( $target_file, parent::ftp_get_mofile_content( $mofile, $textdomain ), FS_CHMOD_FILE);
		}

	} // END write_mofile()

} // END class TranslationToolkit_FileSystem
