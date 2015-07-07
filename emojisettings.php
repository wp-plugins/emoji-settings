<?php
/**
 * Plugin Name: Emoji Settings
 * Plugin URI: https://wordpress.org/plugins/emoji-settings/
 * Description: Adds the option for the user in Writing Settings to enable or disable emoji output. Just like the "convert emoticons" setting. This option is enabled by default.
 * Author: Sybre Waaijer
 * Author URI: https://cyberwire.nl/
 * Version: 1.0.0
 * Text Domain: emojisettings
 * License: GLPv2 or later
 */

/**
* Plugin locale 'emojisettings'
*
* File located in plugin folder emojisettings/language/
*
* @since 1.0.0
*/
function cw_emoji_settings_locale() {
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain( 'emojisettings', false, $plugin_dir . '/language/');
}
add_action('plugins_loaded', 'cw_emoji_settings_locale');
 
/**
 * Emoji Settings class
 *
 * @since 1.0.0
 */
class Emoji_Settings_Field {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_filter( 'admin_init' , array( &$this, 'register_fields' ) );
		add_action( 'init', array( &$this, 'disable_emojis' ) );
	}

	/**
	 * Add new fields to wp-admin/options-writing.php page
	 *
	 * @since 1.0.0
	 */
	public function register_fields() {
		register_setting( 'writing', 'enable_emoji', '' );
		add_settings_field(
			'enable_emoji',
			__( 'Emoji Support', 'emojisettings'),
			array( &$this, 'fields_html' ),
			'writing'
		);
	}

	/**
	 * HTML output for settings
	 *
	 * @since 1.0.0
	 */
	public function fields_html() {
		
		$option = get_option( 'enable_emoji', '1' );
		
		?>
			<fieldset><legend class="screen-reader-text"><span><?php _e( 'Emoji Support', 'emojisettings' ) ?></span></legend>
			<label for="enable_emoji">
				<input name="enable_emoji" type="checkbox" id="enable_emoji" value="1" <?php checked( '1', $option ); ?> />
				<?php _e( 'Enable emoji support', 'emojisettings' ) ?>
			</label>
			</fieldset>
		</tr>
		<?php
	}
	
	/**
	 * Disable the emoji output based on option
	 *
	 * @since 1.0.0
	 * 
	 * @credits https://wordpress.org/plugins/disable-emojis/
	 *
	 * @uses disable_emojis_tinymce
	 */	
	public function disable_emojis() {
		
		/**
		 * Default the option to true if it's a new blog or the option page of the
		 * blog hasn't been visited yet when this plugin has been activated so 
		 * this doesn't undesireably prevent the emojis from being output.
		 *
		 * Overwrite this with the following filter, somewhere in your themes or plugins:
		 * WARNING: do not use the following filter. It will render the option useless.
		 * add_filter( 'pre_option_enable_emoji', array( 'Emoji_Settings_Field', '__return_false' );
		 * The filter above doesn't work as intended. Will create a better filter in a future update.
		 */		
		$option = get_option( 'enable_emoji', '1' );
				
		/**
		 * If the emoji settings is set to off: remove the emoji scripts and other settings.
		 *
		 * @since 1.0.0
		 */
		if ( $option !== '1' ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); // Front-end browser support detection script
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' ); // Admin browser support detection script
			remove_action( 'wp_print_styles', 'print_emoji_styles' ); // Emoji styles
			remove_action( 'admin_print_styles', 'print_emoji_styles' ); // Admin emoji styles
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' ); // Remove from feed, this is bad behaviour!
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); // Remove from feed, this is bad behaviour!
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' ); // Remove from mail
			add_filter( 'tiny_mce_plugins', array( &$this, 'disable_emojis_tinymce' ) ); // Remove from tinymce
		}
	}
	
	/**
	 * Filter function used to remove the tinymce emoji plugin.
	 * 
	 * @since 1.0.0
	 *
	 * @credits https://wordpress.org/plugins/disable-emojis/
	 *
	 * @param    array  $plugins
	 * @return   array	Difference betwen the two arrays
	 */
	public function disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}

}
new Emoji_Settings_Field();
