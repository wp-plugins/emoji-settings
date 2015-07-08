<?php
/**
 * Plugin Name: Emoji Settings
 * Plugin URI: https://wordpress.org/plugins/emoji-settings/
 * Description: Adds the option for the user in Writing Settings to enable or disable emoji output. Just like the "convert emoticons" setting. This option is enabled by default.
 * Author: Sybre Waaijer
 * Author URI: https://cyberwire.nl/
 * Version: 1.0.1
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
	 * Settings array, providing defaults.
	 *
	 * @since 1.0.1
	 *
	 * @var array Holds emoji settings
	 */
	protected $options = array();
	
	/**
	 * Constructor. Makes my life brighter every day :)
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'admin_init' , array( &$this, 'register_fields' ) );
		add_action( 'init', array( &$this, 'disable_emojis' ), 11 );
		
		//* Default settings
		$this->options = array(
			'default' 	=> '1',
			'enable'	=> false,
			'disable'	=> false,
			);		
	}
	
	/**
	 * Return the compiled options.
	 *
	 * @since 1.0.1
	 * 
	 * @param array $options The options
	 * @return array The Emoji options
	 */
	public function get_option( $options = array() ) {
		
		/**
		 * Filter the Emoji options.
		 *
		 * @since 1.0.1
		 *
		 * @param array $options {
		 *      Arguments for Emoji settings.
		 *
		 *      @type string 	$default		Turn global emoji output on or off by default before settings applied.
		 *      @type bool 		$enable			Override the settings and turn the emojis on anyway.
		 *      @type bool 		$disable		Override the settings and turn the emojis off anyway.
		 * }
		 */
		$this->options = apply_filters( 'the_emoji_options', wp_parse_args( $options, $this->options ) );
				
		return $this->options;
	}
	
	/**
	 * Sanitize the options.
	 * Prevents wrong filters. Overkill? Maybe. But makes sure the checks in the options below will work as intended.
	 *
	 * @since 1.0.1
	 *
	 * @param array $options The options
	 * return array Sanitized the emoji options
	 */
	protected function option( $options = array()) {
		
		$this->options = wp_parse_args( $options, $this->get_option() );
				
		if ( $this->options['default'] === '1' || $this->options['default'] === '0' ) {
			// leave them as be
		} else if ( $this->options['default'] === true ) {
			$this->options['default'] = '1';
		} else if ( $this->options['default'] === false ) {
			$this->options['default'] = '0';
		} else {
			$this->options['default'] = '1';
		}
		
		if ( empty( $this->options['enable'] ) ) {
			$this->options['enable'] = false;
		} else if ( $this->options['enable'] !== false ) {
			$this->options['enable'] = true;
		} else {
			$this->options['enable'] = false;
		}
		
		if ( empty( $this->options['disable'] ) ) {
			$this->options['disable'] = false;
		} else if ( $this->options['disable'] !== false ) {
			$this->options['disable'] = true;
		} else {
			$this->options['disable'] = false;
		}
		
		return $this->options;
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
		
		$option = get_option( 'enable_emoji', $this->option()['default'] );
		
		?>
		<fieldset><legend class="screen-reader-text"><span><?php _e( 'Emoji Support', 'emojisettings' ) ?></span></legend>
		<label for="enable_emoji">
			<input name="enable_emoji" type="checkbox" id="enable_emoji" value="1" <?php checked( '1', $option ); ?> />
			<?php _e( 'Enable emoji support', 'emojisettings' ) ?>
		</label>
		</fieldset>
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
	public function disable_emojis( $options = array() ) {
				
		/**
		 * Default the option to true if it's a new blog or the option page of the
		 * blog hasn't been visited yet when this plugin has been activated so 
		 * this doesn't undesireably prevent/'unprevent' the emojis from being output.
		 */
		$option = get_option( 'enable_emoji', $default = $this->option()['default'] );
		
		/**
		 * Enable it anyway if true (Default is false)
		 */
		$enable = $this->option()['enable']; 
		
		/**
		 * Disable it anyway if true (Default is false)
		 */
		$disable = $this->option()['disable'];
		
		/**
		 * If the emoji settings is set to off:	remove the emoji scripts and other settings.
		 *
		 * If the enable value is set to true: 	Keep the emoji scripts output.
		 * If the disable value is set to true: Remove the emoji scripts output.
		 *
		 * @since 1.0.0
		 */		
		if ( $disable === true || ( $enable === false && $option !== '1' ) ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); // Front-end browser support detection script
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' ); // Admin browser support detection script
			remove_action( 'wp_print_styles', 'print_emoji_styles' ); // Emoji styles
			remove_action( 'admin_print_styles', 'print_emoji_styles' ); // Admin emoji styles
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' ); // Remove from feed, this is bad behaviour!
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); // Remove from feed, this is bad behaviour!
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' ); // Remove from mail
			add_filter( 'tiny_mce_plugins', array( &$this, 'disable_emojis_tinymce' ) ); // Remove from tinymce
		}
	
		/* 
		//Debugging
		echo '<!--'; 
		print_r( 'enable = ' . $enable . "\r\n" . 'disable = ' . $disable . "\r\n" . 'default = ' . $default . "\r\n" );
		echo 'enable: '; var_dump( $enable );
		echo 'disable: '; var_dump( $disable );
		echo 'default: '; var_dump( $default );
		echo 'option: '; var_dump( $option );
		echo 'debugging: 9';
		echo '-->';
		*/
	
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
