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

class TranslationToolkit_Admin {

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

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		global $tt_tabs;

		// NEEDS a helper function for input of new tabs and output of tabs on specific pages
		$tt_tabs['translation-toolkit'] = array(
			'all' => array(
				'label' => __( 'All Translations', 'translation-toolkit' ),
			),
			'wordpress' => array(
				'label' => __( 'WordPress', 'translation-toolkit' ),
			),
			'plugins-mu' => array(
				'label' => __( 'MU Plugins', 'translation-toolkit' ),
			),
			'plugins' => array(
				'label' => __( 'Plugins', 'translation-toolkit' ),
			),
			'themes' => array(
				'label' => __( 'Themes', 'translation-toolkit' ),
			),
			'compat' => array(
				'label' => __( 'Compatibility', 'translation-toolkit' ),
			),
		);

	} // END __construct()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	function admin_init() {

		// currently not used, subject of later extension
		//$low_mem_mode = (bool)get_option( 'translation-toolkit.low-memory', 1 );
		//define( 'TT_LOW_MEMORY', $low_mem_mode ); //@todo needed?

		TranslationToolkit_Helpers::check_filesystem();

	} // END admin_init()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	function admin_menu() {

		$hook = add_submenu_page(
			apply_filters( 'tt_page_parent', 'tools.php' ),
			__( 'WordPress Localization', 'translation-toolkit' ),
			__( 'Localization', 'translation-toolkit' ),
			apply_filters( 'tt_settings_cap', 'manage_options' ),
			'translation-toolkit',
			array( &$this, 'main_page' )
		);

		add_action( 'load-' . $hook, array( $this, 'load_assets' ) ); //only load the scripts and stylesheets by hook, if this admin page will be shown

	} // END admin_menu()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	function load_assets() {

		// Register css files
		$dev = defined( 'TT_DEV' ) && TT_DEV ? '' : '.min';

		wp_register_style( 'translation-toolkit', plugin_dir_url( TranslationToolkit::get_file() ) . 'css/tt.' . $dev . 'css', array(), TranslationToolkit::get_instance()->version );
		wp_register_style( 'translation-toolkit-ui', plugin_dir_url( TranslationToolkit::get_file() ) . 'css/tt-ui.' . $dev . 'css', array(), TranslationToolkit::get_instance()->version );
		wp_register_style( 'translation-toolkit-rtl', plugin_dir_url( TranslationToolkit::get_file() ) . 'css/tt-rtl.' . $dev . 'css', array(), TranslationToolkit::get_instance()->version );

		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'prototype' );
		wp_enqueue_script( 'scriptaculous-effects' );

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'translation-toolkit' );
		wp_enqueue_style( 'translation-toolkit-ui' );

		if ( is_rtl() ) {
			wp_enqueue_style( 'translation-toolkit-rtl' );
		}

		$screen = get_current_screen();
		//$request = unserialize(csp_fetch_remote_content('http://api.wordpress.org/plugins/info/1.0/codestyling-localization'));

		$screen->add_help_tab(
			array(
				'title'    => __( 'Low Memory Mode', 'translation-toolkit' ),
				'id'       => 'lowmemory',
				'content'  => '',
				'callback' => array( 'TranslationToolkit_Help', 'helptab_low_memory' ),
			)
		);

		$content = array();
		$screen->add_help_tab(
			array(
				'title'    => __( 'Compatibility', 'translation-toolkit' ),
				'id'       => 'compatibility',
				'content'  => '',
				'callback' => array( 'TranslationToolkit_Help', 'helptab_compatibility' ),
			)
		);
		$screen->add_help_tab(
			array(
				'title'    => __( 'Textdomains', 'translation-toolkit' ),
				'id'       => 'textdomain',
				'content'  => '',
				'callback' => array( 'TranslationToolkit_Help', 'helptab_textdomain' ),
			)
		);
		$screen->add_help_tab(
			array(
				'title'    => __( 'Translation Format', 'translation-toolkit' ),
				'id'       => 'translationformat',
				'content'  => '',
				'callback' => array( 'TranslationToolkit_Help', 'helptab_translationformat' ),
			)
		);
		if ( CSL_FILESYSTEM_DIRECT !== true ) {
			$screen->add_help_tab(
				array(
					'title'    => __( 'File Permissions', 'translation-toolkit' ),
					'id'       => 'filepermissions',
					'content'  => '',
					'callback' => array( 'TranslationToolkit_Help', 'helptab_filepermissions' ),
				)
			);
		}
		$screen->add_help_tab(
			array(
				'title'    => __( 'Child Themes', 'translation-toolkit' ),
				'id'       => 'workonchildthemes',
				'content'  => '',
				'callback' => array( 'TranslationToolkit_Help', 'helptab_workonchildthemes' ),
			)
		);
//		$screen->add_help_tab(
//			array(
//				'title'    => __( 'About', 'translation-toolkit' ),
//				'id'       => 'about',
//				'content'  => '',
//				'callback' => array( 'TranslationToolkit_Help', 'helptab_about' ),
//			)
//		);

	} // END load_assets()

	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	function main_page() {
		TranslationToolkit_Helpers::check_security();

		global $tt_tabs;

		$sys_locales = TranslationToolkit_Locale::sys_locales();
		$mo_list_counter = 0;

		include_once( 'views/admin-main-page.php' );
		include_once( 'ajax/admin-ajax-main-page.php' );

	} // END main_page()

} // END class TranslationToolkit_Admin
