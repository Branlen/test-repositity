<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 *
 * @link       https://pixcut.wondershare.com
 * @since      1.0.0
 *
 * @package     Pixcut_Remove_BG
 * @subpackage  Pixcut_Remove_BG/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package     Pixcut_Remove_BG
 * @subpackage  Pixcut_Remove_BG/includes
 * @author     Pixcut Developers <developers@pixcut.com>
 */
class Pixcut_Remove_BG {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pixcut_Remove_BG_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PIXCUT_REMOVE_BACKGROUND_VERSION' ) ) {
			$this->version = PIXCUT_REMOVE_BACKGROUND_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'pixcut-remove-bg';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		// $this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Pixcut_Remove_BG_Loader. Orchestrates the hooks of the plugin.
	 * - Pixcut_Remove_BG_i18n. Defines internationalization functionality.
	 * - Pixcut_Remove_BG_Admin. Defines all hooks for the admin area.
	 * - Pixcut_Remove_BG_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pixcut-remove-bg-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pixcut-remove-bg-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pixcut-remove-bg-admin.php';

		// /**
		//  * The class responsible for defining all actions that occur in the public-facing
		//  * side of the site.
		//  */
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pixcut-remove-bg-public.php';

		$this->loader = new Pixcut_Remove_BG_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Pixcut_Remove_BG_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Pixcut_Remove_BG_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Pixcut_Remove_BG_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_pixcut_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_pixcut_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_remove_bg' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'Pixcut_Remove_BG_admin_notice' );
		$this->loader->add_action( 'wp_ajax_Remove_BG_processing', $plugin_admin, 'Pixcut_Remove_BG_processing' );
        $this->loader->add_action( 'wp_ajax_Remove_BG_Restore_Backup', $plugin_admin, 'Pixcut_Remove_BG_Restore_Backup' );
		$this->loader->add_action( 'wp_ajax_Delete_backup', $plugin_admin, 'Pixcut_Delete_backup' );
		$this->loader->add_action( 'wp_ajax_Preview_BG_Images', $plugin_admin, 'Pixcut_Preview_BG_Images' );
		$this->loader->add_action( 'wp_ajax_User_Aborted', $plugin_admin, 'Pixcut_User_Aborted' );

	}

	// /**
	//  * Register all of the hooks related to the public-facing functionality
	//  * of the plugin.
	//  *
	//  * @since    1.0.0
	//  * @access   private
	//  */
	// private function define_public_hooks() {

	// 	$plugin_public = new Pixcut_Remove_BG_Public( $this->get_plugin_name(), $this->get_version() );

	// 	$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
	// 	$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	// }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Remove_BG_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}