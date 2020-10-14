<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.sparkslc.ca/
 * @since      1.0.0
 *
 * @package    Switchboard_db
 * @subpackage Switchboard_db/includes
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
 * @package    Switchboard_db
 * @subpackage Switchboard_db/includes
 * @author     Dustin Thorley - Spark SLC <dustin@sparkslc.ca>
 */
class Switchboard_db {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Switchboard_db_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'SWITCHBOARD_DB_VERSION' ) ) {
			$this->version = SWITCHBOARD_DB_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'switchboard_db';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Switchboard_db_Loader. Orchestrates the hooks of the plugin.
	 * - Switchboard_db_i18n. Defines internationalization functionality.
	 * - Switchboard_db_Admin. Defines all hooks for the admin area.
	 * - Switchboard_db_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-switchboard_db-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-switchboard_db-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-switchboard_db-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-switchboard_db-public.php';

		$this->loader = new Switchboard_db_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Switchboard_db_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Switchboard_db_i18n();

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

		$plugin_admin = new Switchboard_db_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'admin_post_general_email', $plugin_admin, 'send_general_email');
		$this->loader->add_action( 'admin_post_nopriv_general_email', $plugin_admin, 'send_general_email');

		$this->loader->add_action( 'admin_post_salesForce_form', $plugin_admin, 'send_salesforce');
        $this->loader->add_action( 'admin_post_nopriv_salesForce_form', $plugin_admin, 'send_salesforce');

		$this->loader->add_action( 'admin_post_load_csv', $plugin_admin, 'load_csv' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Switchboard_db_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin = new Switchboard_db_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_resource = new Switchboard_db_Public_Resources( $this->get_plugin_name(), $this->get_version() );
		$plugin_filter = new Switchboard_db_Public_Filters( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'set_js_var' );

		$this->loader->add_action( 'switchboard_display_filters', $plugin_filter, 'display_filters', 10, 1 );
		$this->loader->add_action( 'wp_ajax_get_switchboard_filter', $plugin_filter, 'display_filters', 10, 1 );
		$this->loader->add_action( 'wp_ajax_nopriv_get_switchboard_filter', $plugin_filter, 'display_filters', 10, 1 );

		$this->loader->add_action( 'switchboard_get_providers', $plugin_public, 'get_providers' );
		$this->loader->add_action( 'switchboard_get_provider_cards', $plugin_public, 'get_provider_cards' );

		$this->loader->add_action( 'switchboard_display_resources', $plugin_resource, 'prefilter_resources');

		$this->loader->add_action( 'wp_ajax_filter_switchboard_resources', $plugin_resource, 'filter_resources' );
		$this->loader->add_action( 'wp_ajax_nopriv_filter_switchboard_resources', $plugin_resource, 'filter_resources' );

		$this->loader->add_action( 'wp_ajax_search_switchboard', $plugin_resource, 'search_resources' );
		$this->loader->add_action( 'wp_ajax_nopriv_search_switchboard', $plugin_resource, 'search_resources' );

		


		//possibly not used anymore
		$this->loader->add_action("wp_ajax_read_switchboard_resources", $plugin_public, "read_switchboard_resources");
		$this->loader->add_action("wp_ajax_nopriv_read_switchboard_resources", $plugin_public, "read_switchboard_resources");
	}

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
	 * @return    Switchboard_db_Loader    Orchestrates the hooks of the plugin.
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
