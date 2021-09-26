<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       PixCut remove background for WooCommerce
 * Description:       Remove/change background of WooCommerce product images.
 * Plugin URI:		  https://pixcut.wondershare.com/
 * Version:           1.0
 * Author:            Wondershare Pixcut
 * Author URI:        https://pixcut.wondershare.com/
 * Requires at least: 4.1
 * Tested up to:      5.3.2
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pixcut-remove-bg
 * Domain Path:       /languages


 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'PIXCUT_REMOVE_BACKGROUND_VERSION', '1.0' );
define( 'PIXCUT_ROOT_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pixcut-remove-bg-activator.php
 */
function activate_pixcut_remove_bg() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pixcut-remove-bg-activator.php';
	Pixcut_Remove_BG_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pixcut-remove-bg-deactivator.php
 */
function deactivate_pixcut_remove_bg() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pixcut-remove-bg-deactivator.php';
	Pixcut_Remove_BG_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_pixcut_remove_bg' );
register_deactivation_hook( __FILE__, 'deactivate_pixcut_remove_bg' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pixcut-remove-bg.php';




/** 
**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pixcut_remove_bg() {

	$plugin = new Pixcut_Remove_BG();
	$plugin->run();

}
run_pixcut_remove_bg();

//TODO
add_filter( 'plugin_row_meta', 'pixcut_support_and_contact_links', 10, 4 );
function pixcut_support_and_contact_links( $links_array, $plugin_file_name, $plugin_data, $status )
{

  if( strpos( $plugin_file_name, basename(__FILE__) ))
  {
    $links_array[] = 'Support: support@slazzer.com';
  }
 
  return $links_array;
}