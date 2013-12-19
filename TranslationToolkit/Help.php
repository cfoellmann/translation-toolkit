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

class TranslationToolkit_Help {
	
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
	static function helptab_about() { ?>

		<p>
			<strong>Codestyling Localization </strong> - <em>"<?php _e( '... translate your WordPress, Plugins and Themes', 'translation-toolkit' ); ?>"</em>
		</p>
		<p>
		<?php _e( 'While get in touch with WordPress you will find out, that the initial delivery package comes only with english localization. If you want WordPress to show your native language, you have to provide the appropriated language file at languages folder. This files will be used to replace the english text phrases during the process of page generation. This translation capability has the origin at the gettext functionality which currently been used across a wide range of open source projects.', 'translation-toolkit' ); ?>
		</p>
		<p style="margin-top: 50px;padding-top:10px; border-top: solid 1px #ccc;">
			<small class="alignright" style="position:relative; margin-top: -30px; color: #aaa;">&copy; 2008 - 2012 by Heiko Rabe</small>
			<a href="http://wordpress.org/extend/plugins/codestyling-localization/" target="_blank">Plugin Directory</a> | 
			<a href="http://wordpress.org/extend/plugins/codestyling-localization/changelog/" target="_blank">Change Logs</a> | 
			<a class="alignright" href="http://wordpress.org/extend/plugins/wp-native-dashboard/" target="_blank"><?php _e( 'Dashboard in your Language', 'translation-toolkit' ); ?></a>
		</p>
		
	<?php
	} // END helptab_about()
	
	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function helptab_low_memory() {	?>
		
		<p>
			<strong><?php _e( 'PHP Memory Limit Problems', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
		<?php _e( 'If your Installation is running under low remaining memory conditions, you will face the memory limit error during scan process or opening catalog content. If you hitting your limit, you can enable this special mode. This will try to perform the actions in a slightly different way but that will lead to a considerably slower response times but nevertheless gives no warranty, that it will solve your memory related problems at all cases.', 'translation-toolkit' ); ?>
		</p>
		<p>
		<?php _e( 'It could be, that your provider confirms, that you have enough PHP memory for your installation but it is not. You can detect your real available memory limit using the plugin <a href="http://wordpress.org/extend/plugins/wp-system-health/" target="_blank">WP System Health</a>. It has a build in feature (called <em>Test Suite</em>) to evaluate correctly the memory limit the server will permit.', 'translation-toolkit' ); ?>
		</p>
		<p>
			<input id="enable_low_memory_mode" type="checkbox" name="enable_low_memory_mode" value="1" <?php if ( get_option( 'translation-toolkit.low-memory' ) == 1 ) { echo 'checked="checked"'; } ?>>
			<label for="enable_low_memory_mode"><?php _e( 'enable low memory mode', 'translation-toolkit' ); ?></label>
			<img id="enable_low_memory_mode_indicator" style="display:none;" alt="" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/loading-small.gif'; ?>" />
			<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e( 'What does that mean?', 'translation-toolkit' ); ?>" rel="lowmemory">
				<img src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/question.gif'; ?>" />
			</a>
		</p>
		
		<?php
	} // END helptab_low_memory()
	
	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function helptab_compatibility() { ?>
		
		<p>
			<strong><?php _e( 'Compatibility - Hints and Errors', 'translation-toolkit' ); ?></strong>
		</p>
		<p> 
			<?php _e("If you get compatibility warnings, than they are often related to a wrong usage of WordPress core functionality by the authors of the affected Themes or Plugins.", 'translation-toolkit' ); ?> 
			<?php _e("There are several reason for such reports, but in each of this cases only the original author can solve it:", 'translation-toolkit' ); ?>
		</p>
		<p>
			<ul>
				<li>
				<?php _e("Loading of translation files will be performed beside the WordPress standard functionality.", 'translation-toolkit' ); ?>
				</li>
				<li>
				<?php _e("Textdomains can not be parsed from source files because of used coding syntax.", 'translation-toolkit' ); ?>
				</li>
				<li>
				<?php _e("Component seems to be translatable but doesn't use a translation file load call.", 'translation-toolkit' ); ?>
				</li>
			</ul>
		</p>
		<p>
			<?php _e("Reported issues are not a problem of <em>Codestyling Localization</em>, it's caused by the author of the affected component within it's code.", 'translation-toolkit' ); ?>
		</p>
		
	<?php
	} // END helptab_compatibility()
	
	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function helptab_textdomain() { ?>
		
		<p>
			<strong><?php _e( 'What is a textdomain?', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
			<?php _e( 'Textdomains are used to specified the context for the translation file to be loaded and processed. If a component tries to load a translation file using a textdomain, all texts assigned to this domain gets translated during page creation.', 'translation-toolkit' ); ?>
		</p>
		<p>
			<?php _e( 'The extended feature for textdomain separation shows at dropdown box <i>Textdomain</i> the pre-selected primary textdomain.', 'translation-toolkit' ); ?><br/>
			<?php _e( 'All other additional contained textdomains occur at the source but will not be used, if not explicitely supported by this component!', 'translation-toolkit' ); ?><br/>
			<?php _e( 'Please contact the author, if some of the non primary textdomain based phrases will not show up translated at the required position!', 'translation-toolkit' ); ?><br/>
			<?php _e( 'The Textdomain <i><b>default</b></i> always stands for the WordPress main language file, this could be either intentionally or accidentally!', 'translation-toolkit' ); ?><br/>
		</p>
		<p>
			<strong><?php _e( 'Warning Messages', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
			<?php _e( 'If you get warnings either at the overview page or at the editor page, somethings is wrong within the analysed component.', 'translation-toolkit' ); ?>
			<?php _e( 'The overview page will show warnings, if the textdomain can not be found clearly. In this case the author has written the components code in a way make it hard to detect.', 'translation-toolkit' ); ?>
		</p>
		<p>
			<?php _e( 'Warnings at the editors view will show up, if the component is using badly coded textdomains. This could be either by integration of other plugins code or accidentally by typing mistakes.', 'translation-toolkit' ); ?>
		</p>
		<p>
			<?php _e("Reported issues are not a problem of <em>Codestyling Localization</em>, it's caused by the author of the affected component within it's code.", 'translation-toolkit' ); ?>
		</p>
		
	<?php
	} // END helptab_textdomain()
	
	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function helptab_filepermissions() { ?>
		
		<p>
			<strong><?php _e( 'File Permission and Access Rights', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
			<?php _e( 'Your provider does not permit the ability to modify files at your installation by executed scripts. This translation plugins requires this permission to work properly. WordPress solves this at updates by presenting a dialog for your FTP parameters. This plugin will prompt for your FTP credentials if they are required.', 'translation-toolkit' ); ?>
		</p>
		<p>
			<strong><?php _e( 'Permit File Modifications without prompting for User Credentials', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
			<?php _e( 'You can define the necessary constants at your <em>wp-config.php</em> file as described at the <a href="http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank">WordPress Codex Page - Upgrade Constants</a> to get it working at your installation without recurrently occuring credential requests. If your constants are properly defined, this plugin will work smoothly and the WordPress Automatic Updates will work without any further question about FTP User Credentials too.', 'translation-toolkit' ); ?>
		</p>
		
	<?php
	} // END helptab_filepermissions()
	
	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function helptab_translationformat() { ?>
		
		<p>
			<strong><?php _e( 'Extended Translation File Format', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
			<?php _e( 'You may get an error message if you try to open a translation file for editing. The reason behind is the necessary separation of contained textdomains within your components code to be translated.', 'translation-toolkit' ); ?>
		</p>
		<p>
			<?php _e( 'Many authors do not care, if they mix up textdomains during code writing. Furthermore the textdomain <b><em>default</em></b> will be used by WordPress itself only. Any text assigned to the textdomain <b><em>default</em></b> will become untranslated at output even if you would translate it. Thats why this plugin separates this textdomains to show up possible mistakes.', 'translation-toolkit' ); ?>
		</p>
		<p>
			<strong><?php _e( 'How to edit files with this error message ?', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
			<?php _e( 'Just go back the the overview page, search your affected plugin/theme and re-scan the translation content. Afterwards it will be possible to open the translation file for editing.', 'translation-toolkit' ); ?>
		</p>
	<?php
	
	} // END helptab_translationformat()
	
	/**
	 * @todo
	 *
	 * @since 1.0.0
	 */
	static function helptab_workonchildthemes() { ?>
		
		<p>
			<strong><?php _e( 'Working with Child Theme Translations', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
			<?php _e( 'Child Themes are using in normal cases the translation files of the main theme. In some cases it could be necessary to have a separate language file handling at the Child Theme itself.', 'translation-toolkit' ); ?>
		</p>
		<p>
			<strong><?php _e( 'How to make your Child Theme ready to use its own translation files?', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
			<?php _e( 'First of all you have to modify your Child Themes <em>functions.php</em> file and call the appropriated load method as shown below. Assume the textdomain is defined at the Main Theme as <b>supertheme</b> the load function should look like:', 'translation-toolkit' ); ?>
		</p>
		<pre>load_child_theme_textdomain('supertheme', get_stylesheet_directory().'/languages' );</pre>
		<p>
			<?php _e( 'The path has been defined as subdirectory within the Child Themes directory but you can skip the directory parameter and place the language files at the Child Themes main folder.', 'translation-toolkit' ); ?>
		</p>
		<p>
			<strong><?php _e( '(Re)scan process and Synchronization at Child Themes', 'translation-toolkit' ); ?></strong>
		</p>
		<p>
			<?php _e( 'Scanning a Child Theme always includes the files from Main Theme too. So you always get the mixed translation from Main and Child Theme. Doing a Synchronization with the Main Theme will preserve the texts from Child Theme and will attach new texts from Main Theme only.', 'translation-toolkit' ); ?>
		</p>
		
	<?php
	} // END helptab_workonchildthemes()
	
} // END class TranslationToolkit_AdminHelp
