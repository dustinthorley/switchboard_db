<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.sparkslc.ca/
 * @since      1.0.0
 *
 * @package    Switchboard_db
 * @subpackage Switchboard_db/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Switchboard_db
 * @subpackage Switchboard_db/public
 * @author     Dustin Thorley - Spark SLC <dustin@sparkslc.ca>
 */
class Switchboard_db_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Array of active filters
	 * 
	 * @since	1.0.0
	 * @access	private
	 * @var		object 	$filters	Filters object
	 */
	private $filters;

	private $resources;

	private $activeCategories;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_dependencies();

		$this->filters = New Switchboard_db_Public_Filters($plugin_name, $version);
		$this->resources = New Switchboard_db_Public_Resources($plugin_name, $version);

	}

	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'public/class-switchboard_db-public-filters.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .  'public/class-switchboard_db-public-resources.php';


	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Switchboard_db_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Switchboard_db_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/switchboard_db-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Switchboard_db_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Switchboard_db_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/switchboard_db-public.js', array( 'jquery' ), $this->version, true );

		if( is_page( array( 32, 'resources', 'Resources' ) ) ) { //or post 32
			wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/switchboard_resources.js', array( 'jquery' ), $this->version, false );
		}

		wp_localize_script( $this->plugin_name, 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
		wp_enqueue_script( $this->plugin_name );

	}

	/**
	 * This function makes the site url and template directory available in JavaScript
	 * 
	 * @since	1.0.0
	 */
	function set_js_var() {
		$translation_array = array( 
			'theme_uri' => get_template_directory_uri(),
			'url' => get_site_url(),
			'filters' => $this->active_filters()
		);
  		wp_localize_script( 'jquery', 'switchboard_data', $translation_array );
	}

	/**
	 * This function queries the Switchboard database and returns information relating to resource providers
	 * 
	 * @access	private
	 * @since 	1.0.0
	 */
	public function get_providers() {
		global $wpdb;
		$providers = $wpdb->get_results( "SELECT organizationName, organizationWebsite, organizationLogo FROM organizations ORDER BY organizationName" );

		echo '<div class="provider-logos">';
        foreach ( $providers as $provider ) {
			include plugin_dir_path( dirname( __FILE__ ) ) .  'public/partials/switchboard_db-public-provider.php';
		}
		echo '</div>';
	}

	public function get_provider_cards() {
		global $wpdb;
		$providers = $wpdb->get_results( "SELECT organizationID, organizationName, organizationDescription, organizationWebsite, organizationLogo FROM organizations ORDER BY organizationName" );

        foreach ( $providers as $provider ) {
			include plugin_dir_path( dirname( __FILE__ ) ) .  'public/partials/switchboard_db-public-provider-cards.php';
        }
	}

	private function active_filters() {
		global $wpdb;
		$filters = $wpdb->get_results( "SELECT filterTag FROM switchboard_options WHERE primaryFilter=1 OR secondaryFilter=1" );
		//return json_encode( $filters );
        return ( $filters );
	}



}
