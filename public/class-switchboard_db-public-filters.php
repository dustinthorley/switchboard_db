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
class Switchboard_db_Public_Filters {

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
	 * @since 	1.0.0
	 * @access	private
	 * @var		array		$active_filters		Current active filters
	 */
	private $primary_filters;
	private $secondary_filters;
	
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

		$this->load_filters();
	}

	/**
	 * Retrieve filter values from Switchboard_db
	 * 
	 * @since 	1.0.0
	 */
	private function load_filters() {
		global $wpdb;
		$this->primary_filters = $wpdb->get_results( "SELECT filterIDName, filterName, filterTable, filterTitle, filterTag FROM switchboard_options WHERE primaryFilter = '1'", OBJECT );
		$this->secondary_filters = $wpdb->get_results( "SELECT filterIDName, filterName, filterTable, filterTitle, filterTag FROM switchboard_options WHERE secondaryFilter = '1'", OBJECT );
	}

	/**
	 * Display filters
	 * 
	 * @since 	1.0.0
	 */
	public function display_filters($type) {
		global $wpdb;

		$args = $_GET;
		$activeFilter = '';
		$activeValue = '';
		if ( isset( $args['stage'] ) ) {
			$activeFilter='stageID';
			$activeValue=$args['stage'];
		}
		elseif ( isset( $args['provider'] ) ) {
			$activeFilter='organizationID';
			$activeValue=$args['provider'];
		}
		$filterType = $type == 'primary' ? $this->primary_filters : $this->secondary_filters ;

		foreach( $filterType as $key => $filter ){

			$searchQuery = "SELECT " . $filter->filterIDName . " AS id, " . $filter->filterName . " AS name FROM " . $filter->filterTable; 
			$items = $wpdb->get_results( $searchQuery );

			$class="";
			if ( $filter->filterTag == "categories" ) {
				$class = ' class="' . $filter->filterTag . '"';
			}
			?>
			<div class="filter_column<?php if ( $filter->filterTag == "categories") {echo ' category_column';}?>">
				<div class="filter-title-container">
					<label class="filter-title"><?php echo $filter->filterTitle ?></label>
				</div>
				<div class="options-container<?php if ( $filter->filterTag == "categories") {echo ' category_options';}?>">
					<?php
					foreach( $items as $option ){
						?>
						<label class="w-checkbox">
							<input type="checkbox" <?php if ( $filter->filterTag == 'categories' ) { echo 'onChange="updateSelect()"';} ?> id="<?php echo $filter->filterTag . $option->id ?>" name="<?php echo $filter->filterTag ?>" class="w-checkbox-input checkbox" value="<?php echo $option->id ?>" <?php if ($activeFilter == $filter->filterIDName && $activeValue == $option->id ){echo " checked";} ?>>
							<span class="filter-item w-form-label"><?php echo $option->name ?></span>
							</label>
						<?php
						if ( $filter->filterTag == "categories" && $option->id == "6" ) {
							?>
								</div>
								<div class="options-container category_options">
							<?php
						}

					}
				?>
				</div>
			</div>
		<?php
		}
	}

}
