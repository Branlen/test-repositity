<?php


/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://pixcut.wondershare.com
 * @since      1.0.0
 *
 * @package     Pixcut_Remove_BG
 * @subpackage  Pixcut_Remove_BG/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package     Pixcut_Remove_BG
 * @subpackage  Pixcut_Remove_BG/includes
 * @author     Pixcut Developers <developers@pixcut.com>
 */
class Pixcut_Remove_BG_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'pixcut-remove-bg',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
