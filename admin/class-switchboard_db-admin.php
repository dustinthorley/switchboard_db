<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.sparkslc.ca/
 * @since      1.0.0
 *
 * @package    Switchboard_db
 * @subpackage Switchboard_db/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Switchboard_db
 * @subpackage Switchboard_db/admin
 * @author     Dustin Thorley - Spark SLC <dustin@sparkslc.ca>
 */
class Switchboard_db_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/switchboard_db-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/switchboard_db-admin.js', array( 'jquery' ), $this->version, false );

	}

	private function verify_reCaptcha($response) {
		//reCaptcha verification
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = array( 'secret' => '6LcmxtQZAAAAAHvhi6HMX63HNF5JUbzUCFrcld_v', 'response' => $response );

		// use key 'http' even if you send the request to https://...
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($data)
		    )
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE) { /* Handle error */ }

		$captcha = json_decode($result);
		return $captcha->success;
	}

	public function send_salesforce() {

		if ( $this->verify_reCaptcha($_POST['g-recaptcha-response']) ) {
			$url="https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8";
			//add post generation and send

			$data = $_POST;

			// use key 'http' even if you send the request to https://...
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data)
				)
			);
			$context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($result === FALSE) { /* Handle error */ }

            //redirect back to contact form with success message
            wp_safe_redirect( $data['retURL'] );



		} else {
			$headerString = "Location: " . get_site_url() . "/contact/?contact=general&message=captcha";
			header( $headerString );
		}

	}

	public function send_general_email() {
		
		if ( $this->verify_reCaptcha($_POST['g-recaptcha-response']) ) {

			//handle sending email
			$values = $_POST;

			if ( isset( $values['toField'] ) ) {
                $to = $values['toField'];
            } else {
			    $to = 'dustin@sparkslc.ca';
            }

			$subject = "Switchboard General Question";

			$cleanFName = sanitize_text_field( $values['first_name'] );
			$cleanLName = sanitize_text_field( $values['last_name'] );
			$cleanCompany = sanitize_text_field( $values['company'] );
			$cleanEmail = sanitize_email( $values['email'] );
			$cleanmessage = sanitize_text_field( $values['message'] );

			$name = $cleanFName . " " . $cleanLName;
			$message = "Name: " . $name . "
			Company: " . $cleanCompany . "
			Email: " . $cleanEmail . "
			Message: " . $cleanmessage . "
			CASL Consent: " . ($values['casl'] ? 'Recieved' : 'Declined') ;

			$headers[] = "From: Switchboard Submission <contact@myswitchboard.ca>";
			$headers[] = "Reply-To: " . $name . "<" . $cleanEmail . ">";

			wp_mail( $to, $subject, $message, $headers );

			//redirect back to contact form with success message
			$headerString = "Location: " . get_site_url() . "/contact/?contact=general&message=success";
			header( $headerString );

		} else {
			$headerString = "Location: " . get_site_url() . "/contact/?contact=general&message=captcha";
			header( $headerString );
		}
	}

	public function load_csv() {
		$nonce = sanitize_text_field($_POST['security']);
		if(!wp_verify_nonce($nonce,'switchboard_read_sheets_nonce') || !current_user_can( 'administrator' )){
			header('Location:'.$_SERVER["HTTP_REFERER"].'?error=unauthenticated');
			exit();
	   	}

		if( isset( $_POST["load_resources"] ) && $_FILES['file']['name'] )  { 	//checks that the load_resources button was clicked (can't run script directly) and a file was selected
			$filename = explode(".",$_FILES['file']['name']); 					//split the file name to be able to access the filetype
			if( $filename[1] == "csv" ) {     									//check that the file selected was a csv
				$handle = fopen( $_FILES['file']['tmp_name'], "r");  			//open the file and read the contents
				$ctr = 0; //will use to not load headers into database
				while( $data = fgetcsv($handle)) {
					//load one row of csv data to sanitize
					$timestamp = $data[0];				//not currently used
					$organizationName = $data[1];		//organizations.organizataionName
					$departmentName = $data[2];			//departments.departmentName
					$organizationDesc = $data[3];		//organizations.organizationDescription
					$organizationWebsite = $data[4];    //organizations.organizationWebsite
					$organizationEmail = $data[5];		//coordinators.coordinatorEmail
					$resourceName = $data[6];			//resources.resourceName
					$resourceDesc = $data[7];			//resources.resourceDescription
					$resourceWebsite = $data[8];		//resources.resourceURL
					$resourceManager = $data[9];		//coordinators.coordinatorName
					$resourceEmail = $data[10];			//resources.resourceEmail
					$supportType = $data[11];			//supporttypes.supportName (explode)
					$supportCategory = $data[12];		//supportcategories.categoryName (explode)
					$deadline = $data[13];				//not currently used
					$businessStage = $data[14];			//businessstages.stageName (explode?)
					$region = $data[15];				//regions.regionName
					$industry = $data[16];				//targetindustries.industryDescription (explode)
					$underrepresented = $data[17];		//underrepresentedgroups.groupName
					$cost = $data[18];					//coststructure.costDescription (explode)
					$date = $data[19];					//resources.resourceWhenOffered
					
					if ( $ctr > 0 ) { //only load data if not header row
						//get id's for tables
						$organizationID = $this->checkOrganization($organizationName, $organizationDesc, $organizationWebsite);
						$departmentID = $this->checkDepartment($departmentName, $organizationID);
						$regionID = $this->checkRegion($region);
					}
					
					$ctr++;
				}
				//redirect back to contact form with success message
				$headerString = "Location: " . get_site_url() . "/admin/?update=success";
				header( $headerString );
			} else { //redirect back to contact form with not csv message
				$headerString = "Location: " . get_site_url() . "/admin/?update=nocsv";
				header( $headerString );
			}
		} else { //redirect back to contact form with no file message
			$headerString = "Location: " . get_site_url() . "/admin/?update=nofile";
			header( $headerString );
		}
	}

	private function checkOrganization($newOrganizationName, $newOrganizationDesc, $newOrganizationWebsite) {
		global $wpdb;
		
		$organizationID = $wpdb->get_results( $wpdb->prepare( "SELECT organizationID FROM organizations WHERE organizationName = %s", $newOrganizationName ) );

		//if not found -> add organization

		return $organizationID;
	}

    private function checkDepartment($newDepartmentName, $organizationID) {
        global $wpdb;

        $departmentID = $wpdb->get_results( $wpdb->prepare( "SELECT departmentID FROM departments WHERE departmentName = %s", $newDepartmentName ) );

        //if not found -> add department

        return $departmentID;
	}
	
	private function checkRegion($fileRegions) {
		global $wpdb;
		// add region check logic
		$allRegions = $wpdb->get_results( $wpdb->prepare( "SELECT regionID, regionnName FROM regions") );
		$selectedRegions = [];

		foreach ($allRegions as $region) {
			if ( strpos($fileRegions, $region->regionName) !== false ) {
				array_push($selectedRegions, $region->regionID);
			}
		}

		return $selectedRegions;
	}

}
