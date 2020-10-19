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
class Switchboard_db_Public_Resources {

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
	 * Array of resources matching current filters
	 * 
	 * @since	1.0.0
	 * @access	private
	 * @var		array		$resources
	 */
	private $resources;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name     The name of the plugin.
	 * @param    string    $version    		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Generate string used to search Switchboard DB using search bar
	 * 
	 * @since	1.0.0
	 * @param	string		$id			Filter to apply initially 	default - NULL
	 * @param	string		$idVall		Value to apply to filter	default - NULL
	 */
	public function prefilter_resources() {

		$args = $_GET;
		if ( isset( $args['stage'] ) ) {
			$id = 'stageID';
			$idVal = $args['stage'];
		}
		elseif ( isset( $args['provider'] ) ) {
			$id = 'r.organizationID';
			$idVal = $args['provider'];
		}
		elseif ( isset( $args['keyword'] ) ) {
		    $id = 'resourceID';
		    $idVal = $args['keyword'];
        }

		global $wpdb;
        $whereClause = "";
        if ( isset( $id ) ) {

			$valArray = explode(',', $idVal );
			$stringcount = count($valArray);
			$stringPlaceholders = array_fill(0, $stringcount, '%s');
			$placeholdersForVal = implode(',', $stringPlaceholders);

			$whereClause = $wpdb->prepare( " WHERE " . $id ." IN (". $placeholdersForVal .") ", $valArray );
        }

		$this->perform_search($whereClause);

        //load resources into JSON?

        $this->display_resources();
	}

	/**
	 * Perform search on Switchboard DB
	 * 
	 * @since	1.0.0
	 * @param	string		$whereString	Portion of query containing any applicable WHERE clause		default - NULL
	 */
	private function perform_search($whereString=NULL) {
		global $wpdb;

		$searchQuery = "SELECT 
			resourceID,
			resourceName,
			resourceURL,
			resourceEmail, 
			organizationName,
			organizationWebsite,
			organizationLogo,
			departmentName,
			(SELECT GROUP_CONCAT(stageName SEPARATOR '*')
				FROM resources res
				LEFT JOIN resources_has_businessstages USING (resourceID)
				LEFT JOIN businessstages USING (stageID)
				WHERE res.resourceID = r.resourceID
				GROUP BY res.resourceID) AS stageList,
			resourceDescription, 
			supportName,
			(SELECT GROUP_CONCAT(categoryName SEPARATOR '*')
				FROM resources res
				LEFT JOIN resources_has_supportcategories USING (resourceID)
				LEFT JOIN supportcategories USING (categoryID)
				WHERE res.resourceID = r.resourceID
				GROUP BY res.resourceID) AS categoryList,
			(SELECT GROUP_CONCAT(costDescription SEPARATOR '*')
				FROM resources res
                LEFT JOIN resources_has_coststructure USING (resourceID)
                LEFT JOIN coststructure USING (costID)
                WHERE res.resourceID = r.resourceID
				GROUP BY res.resourceID) AS costList,
			(SELECT GROUP_CONCAT(regionName SEPARATOR ', ')
				FROM resources res
                LEFT JOIN resources_has_regions USING (resourceID)
                LEFT JOIN regions ON resources_has_regions.regionID = regions.regionID
                WHERE res.resourceID = r.resourceID
				GROUP BY res.resourceID) AS regionList,
			coordinatorEmail
			FROM resources r
			JOIN organizations USING (organizationID)
			LEFT JOIN departments USING (departmentID)
			LEFT JOIN coordinators USING (coordinatorID)
			JOIN supporttypes USING (supportID)
			LEFT JOIN resources_has_businessstages USING (resourceID)
			LEFT JOIN resources_has_coststructure USING (resourceID)
			LEFT JOIN resources_has_supportcategories USING (resourceID)"
			. $whereString . 
			" GROUP BY resourceID
			ORDER BY resourceName";

		$this->resources = $wpdb->get_results($searchQuery);
	}

	/**
	 * Display all resources
	 * 
	 * @since	1.0.0
	 * @access	public
	 * 
	 */
	public function display_resources() {
		?>

		<?php
        foreach ( $this->resources as $resource ) {
            include plugin_dir_path( dirname( __FILE__ ) ) .  'public/partials/switchboard_db-public-resource.php';
        }
	}

	public function filter_resources() {
		$searchTerms = [];

		global $wpdb;
		$data = $wpdb->get_results( "SELECT * FROM switchboard_options WHERE primaryFilter=1 OR secondaryFilter=1" );
		foreach ( $data as $key => $filter ){
			if ( $_POST[$filter->filterTag] != "" ){
				$filterID = $filter->filterIDName == 'organizationID' ? 'r.' . $filter->filterIDName : $filter->filterIDName;

				$valArray = explode(',', $_POST[$filter->filterTag] );
				$stringcount = count($valArray);
				$stringPlaceholders = array_fill(0, $stringcount, '%s');
				$placeholdersForVal = implode(',', $stringPlaceholders);

				$searchVal = $wpdb->prepare( $filterID .' IN ('. $placeholdersForVal .')', $valArray );
				$searchTerms[] = $searchVal;
			}
		}

		$whereClause = "";
		
		$searchLength = count( $searchTerms );
		if ( $searchLength>0 ){
			$whereClause .= " WHERE ";
			for ($i = 0; $i < $searchLength; $i++){
				$whereClause .= $searchTerms[$i];
				if ($i < $searchLength-1){
					$whereClause .= " AND ";
				}
			}
		}


		if ( $_POST[ 'free' ] == 'true' ) {
			$whereClause .= $whereClause != '' ? ' AND costID = 1' : ' WHERE costID = 1';
		}
		
		$this->perform_search( $whereClause );
		
		$response = json_encode( $this->resources );
		
		wp_die( $response );
	
	}

	public function search_resources() {
		global $wpdb;

		$input = isset( $_POST['search']) ? stripslashes( $_POST['search'] ) : stripslashes( $_GET['field'] ) ;

		$like = "%" . $wpdb->esc_like( $input ) . "%";

		$free = "";
		//$free = $_POST[ 'free' ] == 'true' ? ' AND costID = 1 ' : '';

		$searchQuery = "SELECT 
		resourceID,
		resourceName,
		resourceURL,
		resourceEmail, 
		organizationName,
		organizationWebsite,
		organizationLogo,
		departmentName,
		(SELECT GROUP_CONCAT(stageName SEPARATOR '*')
			FROM resources res
			LEFT JOIN resources_has_businessstages USING (resourceID)
			LEFT JOIN businessstages USING (stageID)
			WHERE res.resourceID = r.resourceID
			GROUP BY res.resourceID) AS stageList,
		resourceDescription, 
		supportName,
		(SELECT GROUP_CONCAT(categoryName SEPARATOR '*')
			FROM resources res
			LEFT JOIN resources_has_supportcategories USING (resourceID)
			LEFT JOIN supportcategories USING (categoryID)
			WHERE res.resourceID = r.resourceID
			GROUP BY res.resourceID) AS categoryList,
		(SELECT GROUP_CONCAT(costDescription SEPARATOR '*')
				FROM resources res
                LEFT JOIN resources_has_coststructure USING (resourceID)
                LEFT JOIN coststructure USING (costID)
                WHERE res.resourceID = r.resourceID
				GROUP BY res.resourceID) AS costList,
		(SELECT GROUP_CONCAT(regionName SEPARATOR ', ')
				FROM resources res
                LEFT JOIN resources_has_regions USING (resourceID)
                LEFT JOIN regions ON resources_has_regions.regionID = regions.regionID
                WHERE res.resourceID = r.resourceID
				GROUP BY res.resourceID) AS regionList,
		coordinatorEmail
		FROM resources r
		JOIN organizations USING (organizationID)
		LEFT JOIN departments USING (departmentID)
		LEFT JOIN coordinators USING (coordinatorID)
		JOIN supporttypes USING (supportID)
		LEFT JOIN resources_has_businessstages USING (resourceID)
		LEFT JOIN resources_has_coststructure USING (resourceID)
		WHERE (resourceName LIKE %s OR resourceDescription LIKE %s OR organizationName LIKE %s) " .
		$free .
		"GROUP BY resourceID 
		ORDER BY resourceName";

		$sql = $wpdb->prepare( $searchQuery, $like, $like, $like );

		$this->resources = $wpdb->get_results( $sql );

		if( isset( $_GET['field'] ) ) {
			$ids = "";

		    foreach ( $this->resources as $resource ) {
				$ids .= $resource->resourceID;
				$ids .= ",";
			}
			$ids = rtrim($ids, ',');
		    $url = get_site_url() . "/resources/?keyword=" . $ids;
            wp_safe_redirect( $url );
        }
		else {
            $response = json_encode( $this->resources );

            wp_die( $response );
        }

		
	}

}
