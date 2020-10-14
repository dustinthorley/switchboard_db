<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.sparkslc.ca/
 * @since      1.0.0
 *
 * @package    Switchboard_db
 * @subpackage Switchboard_db/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Switchboard_db
 * @subpackage Switchboard_db/includes
 * @author     Dustin Thorley - Spark SLC <dustin@sparkslc.ca>
 */
class Switchboard_db_Activator {

	/**
	 * Creates Switchboard Database tables.
	 *
	 * This function creates all tables required for Switchboard and populates them with data.
	 *
	 * @since    1.0.0
	 */
	public function activate() {

		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

		$this->drop_tables($wpdb);
		$this->create_tables($wpdb);
		$this->create_index($wpdb);
		
		$this->add_organization_data($wpdb);
		$this->add_supportTypes_data($wpdb);
		$this->add_businessStages_data($wpdb);
		$this->add_coordinators_data($wpdb);
		$this->add_departments_data($wpdb);
		$this->add_regions_data($wpdb);
		$this->add_resources_data($wpdb);
		$this->add_resource_has_busninessStages_data($wpdb);
		$this->add_supportCategories_data($wpdb);
		$this->add_resource_has_supportCategories_data($wpdb);
		$this->add_targetIndustries_data($wpdb);
		$this->add_resource_has_targetIndustries_data($wpdb);
		$this->add_underrepresentedGroups_data($wpdb);
		$this->add_resource_has_groups_data($wpdb);
		$this->add_delivery_data($wpdb);
		$this->add_costStructure_data($wpdb);
		$this->add_resource_has_costStructure_data($wpdb);
		$this->add_switchboard_options_data($wpdb);
	}

	private function drop_tables($wpdb) {
		$sql = array();

		$sql[] = "DROP TABLE IF EXISTS resources_has_businessStages;";
		$sql[] = "DROP TABLE IF EXISTS resources_has_supportCategories;";
		$sql[] = "DROP TABLE IF EXISTS resources_has_costStructure;";
		$sql[] = "DROP TABLE IF EXISTS resources_has_delivery;";
		$sql[] = "DROP TABLE IF EXISTS resources_has_groups";
		$sql[] = "DROP TABLE IF EXISTS resources_has_targetIndustries;";
		$sql[] = "DROP TABLE IF EXISTS resources;";
		$sql[] = "DROP TABLE IF EXISTS departments;";
		$sql[] = "DROP TABLE IF EXISTS organizations;";
		$sql[] = "DROP TABLE IF EXISTS supportTypes;";
		$sql[] = "DROP TABLE IF EXISTS businessStages;";
		$sql[] = "DROP TABLE IF EXISTS coordinators;";
		$sql[] = "DROP TABLE IF EXISTS regions;";
		$sql[] = "DROP TABLE IF EXISTS supportCategories;";
		$sql[] = "DROP TABLE IF EXISTS targetIndustries;";
		$sql[] = "DROP TABLE IF EXISTS underrepresentedGroups;";
		$sql[] = "DROP TABLE IF EXISTS delivery;";
		$sql[] = "DROP TABLE IF EXISTS costStructure;";
		$sql[] = "DROP TABLE IF EXISTS switchboard_options;";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function create_tables($wpdb) {
		$charset_collate = $wpdb->get_charset_collate();
		$sql = array();

		$sql[] = "CREATE TABLE IF NOT EXISTS organizations (
			organizationID INT(3) NOT NULL AUTO_INCREMENT,
			organizationName VARCHAR(45) NULL,
			organizationDescription TEXT NULL,
			organizationWebsite VARCHAR(45) NULL,
			organizationLogo VARCHAR(45) NULL,
			PRIMARY KEY  (organizationID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS supportTypes (
			supportID INT(2) NOT NULL AUTO_INCREMENT,
			supportName VARCHAR(45) NULL,
			PRIMARY KEY  (supportID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS businessStages (
			stageID INT(2) NOT NULL AUTO_INCREMENT,
			stageName VARCHAR(45) NULL,
			PRIMARY KEY  (stageID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS coordinators (
			coordinatorID INT(3) NOT NULL AUTO_INCREMENT,
			coordinatorName VARCHAR(45) NULL,
			coordinatorEmail VARCHAR(45) NULL,
			PRIMARY KEY  (coordinatorID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS departments ( 
			departmentID INT(3) NOT NULL AUTO_INCREMENT,
			departmentName VARCHAR(100) NULL,
			organizationID INT(3) NOT NULL,
			PRIMARY KEY  (departmentID),
			FOREIGN KEY  (organizationID) REFERENCES organizations (organizationID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS regions (
			regionID INT(2) NOT NULL AUTO_INCREMENT,
			regionName VARCHAR(75) NULL,
			PRIMARY KEY  (regionID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS resources (
			resourceID INT(4) NOT NULL AUTO_INCREMENT,
			resourceDateAdded DATE NULL,
			resourceName VARCHAR(80) NULL,
			resourceDescription TEXT NULL,
			resourceWhereDelivered VARCHAR(255) NULL,
			resourceURL VARCHAR(255) NULL,
			resourceEmail VARCHAR(75) NULL,
			resourceHowToApply VARCHAR(200) NULL,
			resourceWhenOffered VARCHAR(255) NULL,
			organizationID INT(3) NOT NULL,
			supportID INT(2) NOT NULL,
			coordinatorID INT(3) NULL,
			departmentID INT(3) NULL,
			regionID INT(2) NULL,
			PRIMARY KEY  (resourceID),
			CONSTRAINT fk_resources_organizations1
			FOREIGN KEY  (organizationID)
			REFERENCES organizations (organizationID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_supportTypes1
			FOREIGN KEY  (supportID)
			REFERENCES supportTypes (supportID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_coordinators1
			FOREIGN KEY  (coordinatorID)
			REFERENCES coordinators (coordinatorID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_departments1
			FOREIGN KEY  (departmentID)
			REFERENCES departments (departmentID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_regions1
			FOREIGN KEY  (regionID)
			REFERENCES regions (regionID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS resources_has_businessStages (
			resourceID INT(4) NOT NULL,
			stageID INT(2) NOT NULL,
			PRIMARY KEY  (resourceID, stageID),
			CONSTRAINT fk_resources_has_businessStages_resources1
			FOREIGN KEY (resourceID)
			REFERENCES resources (resourceID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_has_businessStages_businessStages1
			FOREIGN KEY (stageID)
			REFERENCES businessStages (stageID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS supportCategories (
			categoryID INT(2) NOT NULL AUTO_INCREMENT,
			categoryName VARCHAR(51) NULL,
			PRIMARY KEY  (categoryID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS resources_has_supportCategories (
			resourceID INT(4) NOT NULL,
			categoryID INT(2) NOT NULL,
			PRIMARY KEY  (resourceID, categoryID),
			CONSTRAINT fk_resources_has_supportCategories_resources1
			FOREIGN KEY  (resourceID)
			REFERENCES resources (resourceID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_has_supportCategories_supportCategories1
			FOREIGN KEY  (categoryID)
			REFERENCES supportCategories (categoryID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS targetIndustries (
			naicsCode INT(3) NOT NULL,
			industryDescription VARCHAR(115) NULL,
			PRIMARY KEY  (naicsCode)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS resources_has_targetIndustries (
			resourceID INT(4) NOT NULL,
			naicsCode INT(3) NOT NULL,
			PRIMARY KEY  (resourceID, naicsCode),
			CONSTRAINT fk_resources_has_targetIndustries_resources1
			FOREIGN KEY  (resourceID)
			REFERENCES resources (resourceID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_has_targetIndustries_targetIndustries1
			FOREIGN KEY  (naicsCode)
			REFERENCES targetIndustries (naicsCode)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS underrepresentedGroups (
			groupID INT(1) NOT NULL AUTO_INCREMENT,
			groupName VARCHAR(45) NULL,
			PRIMARY KEY  (groupID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS resources_has_groups (
			resourceID INT(4) NOT NULL,
			groupID INT(1) NOT NULL,
			PRIMARY KEY  (resourceID, groupID),
			CONSTRAINT fk_resources_has_groups_resources1
			FOREIGN KEY  (resourceID)
			REFERENCES resources (resourceID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_has_groups_groups1
			FOREIGN KEY  (groupID)
			REFERENCES underrepresentedGroups (groupID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS delivery (
			deliveryID INT(2) NOT NULL AUTO_INCREMENT,
			deliveryLocation VARCHAR(45) NULL,
			deliveryAddress VARCHAR(45) NULL,
			deliveryUnit VARCHAR(15) NULL,
			deliveryCity VARCHAR(45) NULL,
			deliveryProv VARCHAR(2) NULL,
			deliveryPostal VARCHAR(7) NULL,
			PRIMARY KEY  (deliveryID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS resources_has_delivery (
			resourceID INT(4) NOT NULL,
			deliveryID INT(2) NOT NULL,
			PRIMARY KEY  (resourceID, deliveryID),
			CONSTRAINT fk_resources_has_delivery_delivery1
			FOREIGN KEY (deliveryID)
			REFERENCES delivery (deliveryID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_has_delivery_resources1
			FOREIGN KEY  (resourceID)
			REFERENCES resources (resourceID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS costStructure (
			costID INT(2) NOT NULL AUTO_INCREMENT,
			costDescription VARCHAR(45) NULL,
			PRIMARY KEY  (costID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS resources_has_costStructure (
			resourceID INT NOT NULL,
			costID INT NOT NULL,
			PRIMARY KEY  (resourceID, costID),
			CONSTRAINT fk_resources_has_costStructure_costStructure1
			FOREIGN KEY  (costID)
			REFERENCES costStructure (costID)
			ON DELETE NO ACTION
			ON UPDATE NO ACTION,
			CONSTRAINT fk_resources_has_costStructure_resources1
			FOREIGN KEY  (resourceID)
			REFERENCES resources (resourceID)
			) ENGINE = InnoDB $charset_collate;";

		$sql[] = "CREATE TABLE IF NOT EXISTS switchboard_options (
			optionID INT(2) NOT NULL AUTO_INCREMENT,
			filterIDName VARCHAR(45) NULL,
			filterName VARCHAR(45) NULL,
			filterTable VARCHAR(45) NULL,
			filterTitle VARCHAR(45) NULL,
			primaryFilter TINYINT NULL,
			secondaryFilter TINYINT NULL,
			filterTag VARCHAR(45) NULL,
			PRIMARY KEY (`optionID`)
			) ENGINE = InnoDB  $charset_collate;";

		foreach ($sql as $statement) {
			dbDelta( $statement );
		}
	}

	private function create_index($wpdb) {

		$sql = array();

		$sql[] = "CREATE INDEX fk_departments_organizations1_idx ON departments (organizationID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_organizations1_idx ON resources (organizationID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_supportTypes1_idx ON resources (supportID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_coordinators1_idx ON resources (coordinatorID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_coordinators1_idx ON resources (coordinatorID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_departments1_idx ON resources (departmentID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_regions1_idx ON resources (regionID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_has_businessStages_businessStages1_idx ON resources_has_businessStages (stageID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_has_businessStages_resources1_idx ON resources_has_businessStages (resourceID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_has_supportCategories_supportCategories1_idx ON resources_has_supportCategories (categoryID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_has_supportCategories_resources1_idx ON resources_has_supportCategories (resourceID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_has_targetIndustries_targetIndustries1_idx ON resources_has_targetIndustries (naicsCode ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_has_targetIndustries_resources1_idx ON resources_has_targetIndustries (resourceID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_has_groups_groups1_idx ON resources_has_groups (groupID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_resources_has_groups_resources1_idx ON resources_has_groups (resourceID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_delivery_has_resources_resources1_idx ON resources_has_delivery (resourceID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_delivery_has_resources_delivery1_idx ON resources_has_delivery (deliveryID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_costStructure_has_resources_resources1_idx ON resources_has_costStructure (resourceID ASC) VISIBLE;";
		$sql[] = "CREATE INDEX fk_costStructure_has_resources_costStructure1_idx ON resources_has_costStructure (costID ASC) VISIBLE;";
		$sql[] = "CREATE UNIQUE INDEX optionID_UNIQUE ON switchboard_options (optionID ASC) VISIBLE;";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_organization_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `organizations` (`organizationID`, `organizationName`, `organizationDescription`, `organizationWebsite`, `organizationLogo`) VALUES (DEFAULT, 'Queen\'s University', 'Queen\'s University at Kingston is a public research university in Kingston, Ontario, Canada. Founded on 16 October 1841, via a royal charter issued by Queen Victoria, the university predates Canada\'s founding by 26 years.', 'www.queensu.ca', 'QueensWordmark.png');";
		$sql[] = "INSERT INTO `organizations` (`organizationID`, `organizationName`, `organizationDescription`, `organizationWebsite`, `organizationLogo`) VALUES (DEFAULT, 'St Lawrence College', 'St. Lawrence College is a College of Applied Arts and Technology with three campuses in Eastern Ontario, namely Brockville, Cornwall and Kingston.', 'www.stlawrencecollege.ca', 'SLC_Logo.svg');";
		$sql[] = "INSERT INTO `organizations` (`organizationID`, `organizationName`, `organizationDescription`, `organizationWebsite`, `organizationLogo`) VALUES (DEFAULT, 'Launch Lab', 'A team of entrepreneurs helping entrepreneurs across southeastern Ontario. We provide mentorship, strategic advice, connections, and support to new and existing tech-based businesses.', 'www.launchlab.ca', 'LaunchLab_logo.svg');";
		$sql[] = "INSERT INTO `organizations` (`organizationID`, `organizationName`, `organizationDescription`, `organizationWebsite`, `organizationLogo`) VALUES (DEFAULT, 'Kingston Economic Development Corporation', 'As the sales and marketing arm for the City of Kingston, Kingston\'s Economic Development Corporation is committed to the key issue of long-term economic sustainability. The Kingston Economic Development Corporation\'s success is based on the attraction of new business, the growth and retention of existing business and tourism opportunities as measured by the resulting economic impact on our community. Kingston\'s Economic Development Corporation oversees Attraction & Aftercare, Business Growth & Retention, Start-ups & Youth, Emerging Sectors and Workforce Development Portfolios.', 'https://www.kingstonecdev.com/', 'kedco_logo.jpg');";
		$sql[] = "INSERT INTO `organizations` (`organizationID`, `organizationName`, `organizationDescription`, `organizationWebsite`, `organizationLogo`) VALUES (DEFAULT, 'Southern Ontario Angel Network (SOAN)', 'SOAN is a group of accredited investors from Cornwall, Brockville, Kingston, Belleville and Trenton. There are opportunities to invest during our bi-monthly pitch nights where investors can learn more about exciting new companies in the region.', 'https://www.soangels.ca/', 'soan-logo.jpg');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_supportTypes_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `supportTypes` (`supportID`, `supportName`) VALUES (DEFAULT, 'Funding and Capital');";
		$sql[] = "INSERT INTO `supportTypes` (`supportID`, `supportName`) VALUES (DEFAULT, 'Programs and Services');";
		$sql[] = "INSERT INTO `supportTypes` (`supportID`, `supportName`) VALUES (DEFAULT, 'Workshops and Events');";
		$sql[] = "INSERT INTO `supportTypes` (`supportID`, `supportName`) VALUES (DEFAULT, 'Guides and Content');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_businessStages_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `businessStages` (`stageID`, `stageName`) VALUES (DEFAULT, 'All');";
		$sql[] = "INSERT INTO `businessStages` (`stageID`, `stageName`) VALUES (DEFAULT, 'Exploration');";
		$sql[] = "INSERT INTO `businessStages` (`stageID`, `stageName`) VALUES (DEFAULT, 'Idea');";
		$sql[] = "INSERT INTO `businessStages` (`stageID`, `stageName`) VALUES (DEFAULT, 'Startup');";
		$sql[] = "INSERT INTO `businessStages` (`stageID`, `stageName`) VALUES (DEFAULT, 'Established');";
		$sql[] = "INSERT INTO `businessStages` (`stageID`, `stageName`) VALUES (DEFAULT, 'Ready to Scale');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_coordinators_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Breanne Johnson', 'bre@sparkslc.ca');";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Rick Boswell', 'boswellr@queensu.ca');";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Stephen Scribner', 'stephen.scribner@queensu.ca');";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Garrett Elliott', 'garrett.elliott@launchlab.ca');";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Chris Morris', 'morris@kingstoncanada.com');";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Claire Bouvier', NULL);";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Shelley Hirstwood', 'hirstwood@kingstoncanada.com');";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Ian Murdoch', 'murdoch@kingstoncanada.com');";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Michael Tkautz', 'michael.tkautz@soangels.ca');";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'JP Shearer', 'john-paul.shearer@queensu.ca');";
		$sql[] = "INSERT INTO `coordinators` (`coordinatorID`, `coordinatorName`, `coordinatorEmail`) VALUES (DEFAULT, 'Ian Dick', 'idick@sl.on.ca');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_departments_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `departments` (`departmentID`, `departmentName`, `organizationID`) VALUES (DEFAULT, 'Dunin-Deshpande Queen\'s Innovation Centre', 1);";
		$sql[] = "INSERT INTO `departments` (`departmentID`, `departmentName`, `organizationID`) VALUES (DEFAULT, 'Faculty of Law', 1);";
		$sql[] = "INSERT INTO `departments` (`departmentID`, `departmentName`, `organizationID`) VALUES (DEFAULT, 'Innovation & Business Engagement', 2);";
		$sql[] = "INSERT INTO `departments` (`departmentID`, `departmentName`, `organizationID`) VALUES (DEFAULT, 'Office of Partnerships & Innovation', 1);";
		$sql[] = "INSERT INTO `departments` (`departmentID`, `departmentName`, `organizationID`) VALUES (DEFAULT, 'Smith School of Business', 1);";
		$sql[] = "INSERT INTO `departments` (`departmentID`, `departmentName`, `organizationID`) VALUES (DEFAULT, 'Innovation & Business Engagement: Corporate Learning & Performance Improvement (CLPI)', 2);";
		$sql[] = "INSERT INTO `departments` (`departmentID`, `departmentName`, `organizationID`) VALUES (DEFAULT, 'Innovation & Business Engagement: Employment Services', 2);";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_regions_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `regions` (`regionID`, `regionName`) VALUES (DEFAULT, 'Ontario');";
		$sql[] = "INSERT INTO `regions` (`regionID`, `regionName`) VALUES (DEFAULT, 'Eastern Ontario');";
		$sql[] = "INSERT INTO `regions` (`regionID`, `regionName`) VALUES (DEFAULT, 'Southeastern Ontario');";
		$sql[] = "INSERT INTO `regions` (`regionID`, `regionName`) VALUES (DEFAULT, 'Kingston');";
		$sql[] = "INSERT INTO `regions` (`regionID`, `regionName`) VALUES (DEFAULT, 'Canada');";
		$sql[] = "INSERT INTO `regions` (`regionID`, `regionName`) VALUES (DEFAULT, 'Rural areas outside the urban centre of Kingston including Frontenac County');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_resources_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'The Foundry', 'Together with the Office of Partnerships and Innovation, DDQIC has developed the Foundry program, which pairs researchers with promising intellectual property with students who are interested in pursuing entrepreneurship. In partnering these groups, the Foundry program provides the opportunity for students to build a business from ideas and intellectual property developed by researchers at Queen\'s. Successful QICSI candidates are automatically eligible for the Foundry program and will be given the opportunity to review ideas and research available for commercialization during QICSI.', 'Mitchell Hall - 69 Union St, Kingston, ON K7L 2N9', 'https://www.queensu.ca/innovationcentre/programs/foundry', 'innovation.centre@queensu.ca.', NULL, NULL, 1, 2, NULL, 1, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'QICSI', 'The Queen’s Innovation Centre Summer Initiative (QICSI) is a 16-week, fixed cohort incubator program where founders receive no-cost training, mentorship, and office space to launch their own venture. QICSI offers the opportunity to work on a venture full-time while receiving ongoing support from the Dunin-Deshpande Queen\'s Innovation Centre advisors, mentors, and staff. We admit enterprising post-secondary students and members of the community with a capacity for creativity, a tolerance for risk and a desire to pursue entrepreneurship, or social innovation as their chosen career path. Founders have the opportunity to work in a diverse team with bright individuals from across different faculties, backgrounds and experience levels. QICSI is eligible to students and community members from any program, year, or level of study. QICSI is industry agnostic and supports ventures across the full spectrum of not-for-profit to for-profit, and social to traditional enterprise.', 'Mitchell Hall - 69 Union St, Kingston, ON K7L 2N9', 'https://www.queensu.ca/innovationcentre/programs/qic-summer-initiative-program', 'innovation.centre@queensu.ca.', 'https://www.queensu.ca/innovationcentre/programs/qic-summer-initiative-program/apply-qicsi', NULL, 1, 2, NULL, 1, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'QyourVenture', 'QyourVenture is a program that helps students and community members turn their idea, technology, or inspiration into a venture. QyourVenture guides participants\' entrepreneurial journey in a tiered program that unlocks opportunities and resources as you and your team demonstrate your commitment and make progress in building your venture. Participation in QyourVenture is free and no equity is taken in your business in exchange for participation.', NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 1, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Legal Services', 'The Queen\'s Business Law Clinic (QBLC) provides legal services, via its Director and Queen’s Law students, to organizations and entrepreneurs in southeastern Ontario who would otherwise have difficulty affording legal counsel. The QBLC helps eligible entrepreneurs with ideas to incorporate a company and establish shareholder agreements, confidentiality agreements, employment agreements, and other legal documents as appropriate that provide a company with a solid foundation.', 'Queen\'s Law Clinics - 303 Bagot Street Suite 500, Kingston ON K7K 5W7', 'https://queenslawclinics.ca/business-law', 'qblc@queensu.ca', 'https://queenslawclinics.ca/business-law/apply', 'Ongoing', 1, 2, NULL, 2, 3);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Spark Creative Communications', 'Spark is a full-service creative communications agency solving marketing challenges for Kingston and the surrounding community. Based within St. Lawrence College we strive to shatter the perception of a student-driven agency. Fuelled by a multidisciplinary team of students and led by industry professionals, Spark’s areas of expertise include videography, marketing strategy, social media marketing, brand strategy, graphic design, web design, and much more.', 'St Lawrence College Kingston Campus', 'https://www.sparkslc.ca/', NULL, 'https://www.sparkslc.ca/startproject', 'Ongoing', 2, 2, 1, 3, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Queen\'s Startup Runway Incubation Program', 'Located in the Seaway Coworking building at 310 Bagot Street in Kingston, Ontario, the Queen\'s Startup Runway is a new physical incubation program, for entrepreneurs, startups, and small companies developing new technologies, that includes co-location with the Queen’s Partnerships and Innovation team, Launch Lab, the Southeastern Ontario Angel Network, and St. Lawrence College. The Startup Runway provides access to shared workspaces, meeting rooms, event spaces, and various amenities and services. Additionally, entrepreneurs can network with other entrepreneurs and connect and collaborate with members of the Startup Runway and the Seaway Coworking community.', 'Seaway Coworking building at 310 Bagot Street in Kingston, Ontario', 'https://www.queensu.ca/partnershipsandinnovation/entrepreneurs-startups-and-smes/entrepreneurs-incorporated-company/queens-startup-runway-incubation', 'opi.info@queensu.ca', 'https://fs29.formsite.com/QU-OPI/pwf1viqmev/index.html', 'Ongoing', 1, 2, 2, 4, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Intellectual Property Services', 'The Queen’s P&I team helps entrepreneurs with ideas understand the opportunity for commercializing their invention by researching the existing patent landscape and helping entrepreneurs to develop and implement an intellectual property and commercialization strategy.', 'Seaway Coworking building at 310 Bagot Street in Kingston, Ontario', NULL, 'opi.info@queensu.ca', 'Email: opi.info@queensu.ca Subject: Inquiry regarding IP services', 'Ongoing', 1, 2, 3, 4, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Wings Acceleration program for pre-revenue startups', 'The Wings program provides early stage founders with tools and guidance to help them assess the feasibility of their business idea, validate their proposed value proposition, and begin the development of a viable business model.', 'Seaway Coworking building at 310 Bagot Street in Kingston, Ontario', 'https://www.queensu.ca/partnershipsandinnovation/entrepreneurs-startups-and-smes/entrepreneurs-incorporated-company/wings-acceleration-program-pre', 'opi.info@queensu.ca', 'Email: opi.info@queensu.ca Subject: Inquiry regarding next Wings Acceleration Program', 'The program spans 10 weeks (mid-September to early November)  Participants must commit to attend all four day-long sessions and to work towards the program milestones between sessions', 1, 2, 2, 4, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Growth Acceleration program for post-revenue startups and SMEs poised to grow', 'The Queen’s Partnerships and Innovation (P&I) team’s inaugural 3-month acceleration program for post revenue startups and SMEs poised to grow will commence in March 2020. Participating companies can expect to: ', 'Seaway Coworking building at 310 Bagot Street in Kingston, Ontario', 'https://www.queensu.ca/partnershipsandinnovation/entrepreneurs-startups-and-smes/entrepreneurs-incorporated-company/growth-acceleration-program', 'opi.info@queensu.ca', 'Email: opi.info@queensu.ca Subject: Inquiry regarding Growth Acceleration Program', 'The Queen’s Partnerships and Innovation (P&I) team’s inaugural 3-month acceleration program for post revenue startups and SMEs poised to grow will commence in March 2020.', 1, 2, 2, 4, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Sales coaching services', 'The Queen’s P&I team has contracted with Les Magyar of Infinity Sales Solutions (ISS) to provide sales coaching workshops and 1:1 mentoring sessions to our startups. ISS is a boutique sales coaching and training firm specializing in helping startups and businesses with an emphasis on technology.', NULL, NULL, 'opi.info@queensu.ca', 'Email: opi.info@queensu.ca Subject: Inquiry regarding sales coaching services for startups', 'Ongoing', 1, 2, NULL, 4, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Research collaboration services', 'The Queen’s P&I team is outward facing and has a mandate to be a responsive institutional front door to companies seeking interactions and collaborations with Queen’s and our research community.  Contact us if you are interested in research, collaborating with Queen\'s on a project, related funding opportunities, talent attraction, or licensing opportunities, or for more information visit our page for external organizations.', NULL, NULL, 'opi.info@queensu.ca', NULL, 'Ongoing', 1, 2, NULL, 4, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-03-23', 'Entrepreneur Mentorship', 'We aim to fuel Business growth for entrepreneurs, small businesses, and existing businesses by connecting them with experienced entrepreneurs who can give advice, strategy, and connections.', NULL, 'https://www.launchlab.ca/who-we-are/', 'info@launchlab.ca', 'Email Garrett Elliott', 'Ongoing', 3, 2, 4, NULL, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-03-23', 'Launchpad', 'Learn the fundamentals of how to turn your business ideas into a flourishing full-time enterprise. This eight-week program teaches you the essentials you need in all key business disciplines to move from idea to revenue.', NULL, 'https://www.launchlab.ca/launchpad/', 'info@launchlab.ca', 'Email Garrett Elliott', 'Eight-week program', 3, 3, 4, NULL, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-03-23', 'Peer to Peer', 'These group sessions are facilitated and led by a Launch Lab Entrepreneur in Residence (EIR) and help provide companies with an environment of like-minded entrepreneurs facing similar challenges or at similar stages. ', NULL, 'https://www.launchlab.ca/peer-to-peer/', 'info@launchlab.ca', 'Email Garrett Elliott', 'Ongoing', 3, 2, 4, NULL, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-03-23', 'Amplify ', 'Launch Lab’s Amplify program accelerates and scales innovative Eastern Ontario companies selling into both business-to-business (B2B) or business-to-consumer (B2C) markets regional, nationally or internationally.', NULL, 'https://www.launchlab.ca/amplify/', 'info@launchlab.ca', 'Email Garrett Elliott', 'Ongoing', 3, 2, 4, NULL, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Go Digital', 'There are many ways to sell your products and service online. Strategies for how to get started will vary based on your industry and products. We\'ve listed some resources below to help get you started, and invite you to call on us for advice as you need it. We\'re here for you Kingston.', 'Ongoing', 'https://www.kingstonecdev.com/GoDigital', 'https://www.kingstonecdev.com/contact-us', 'Email: morris@kingstoncanada.com', 'Ongoing', 4, 2, 5, NULL, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Rural Mentorship', 'The Rural Mentorship Program will provide dedicated mentorship tailored to support the individual business challenges of the participating rural, women entrepreneurs. The suite of mentors will include regional business leaders and service providers selected for their specific expertise and experience. This program will break down the geographic barriers and develop a long-lasting mentorship and mastermind outreach series to support business growth. There will be two intakes per year delivered by Claire Bouvier, FEiST in partnership with Kingston Economic Development. This intake will welcome 10 participants.  Each participant will receive one-on-one mentorship with a paid professional along with participation in a mastermind series.  The mentors will be matched according to the specific goals of the individual businesses.', 'Virtual', 'https://www.kingstonecdev.com/we-can', 'wecan@kingstoncanada.com', 'https://www.kingstonecdev.com/sites/default/files/WE-CAN/WE-CAN%20Rural%20Mentorship%20Application%20Form.pdf Send application form to wecan@kingstoncanada.com', 'Deadline May 15th at 4:00 pm Intake 2: Fall 2020', 4, 2, 6, NULL, 6);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Fast Track Exporting', 'The Fast Track Exporting Program will provide dedicated business coaching with Peng-Sang Cau for women entrepreneurs who are seeking opportunities to scale their business through export opportunities. The business consultation will be complimented with workshops and events hosted by Kingston Economic Development focused on export, accessing new markets and international growth.', NULL, 'https://www.kingstonecdev.com/we-can', 'wecan@kingstoncanada.com', NULL, NULL, 4, 2, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Newcomer Bootcamp', 'The Newcomer Business Bootcamp will provide a comprehensive, two-week orientation available to international newcomer women who have been in Canada no longer then 5 years that are interested in starting a business in Kingston and surrounding region. The purpose of the program is to support women newcomers in starting their own business, strengthening their entrepreneurial skill set, and supporting them in the launch of their business. Bootcamps will be delivered twice a year by Chela Breckon, With Chela Inc. in partnership with Kingston Economic Development.', 'Virtual', 'https://www.kingstonecdev.com/we-can', 'https://www.kingstonecdev.com/contact-us', NULL, 'First Intake: August 2020 Twice Per Year', 4, 3, NULL, NULL, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'The Women in Leadership Think Tank', 'The Women in Leadership Think Tank will engage C-Suite Women executives, investors and women in senior leadership positions. The Think Tank is designed to encourage business growth and development through shared expertise. The goal is to create a network of creative, professional women to work on Kingston\'s To Do List improving opportunities for women in business through networking, new connections, informal mentoring, but most importantly setting the agenda to raise the bar. This program will be facilitated by Wanda Williams, Judith Pineault and Kingston Economic Development.', NULL, 'https://www.kingstonecdev.com/we-can', 'https://www.kingstonecdev.com/contact-us', NULL, NULL, 4, 3, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Summer Company', 'The Summer Company program, funded by the Ontario government, has been helping students, aged 15 to 29, start and run their own businesses since 2001. As a Summer Company entrepreneur, participants receive hands-on business mentoring from local business leaders and up to $3,000 to make their dream job a reality.', NULL, 'https://www.kingstonecdev.com/summercompany', 'https://www.kingstonecdev.com/contact-us', 'http://www.ontariocanada.com/screen/eligibility_questionaire.do?language=en', 'May 17th application Deadline Runs April 1st to Labour Day', 4, 1, NULL, NULL, 5);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Business Grant Support Program', 'The intent of the Business Grant Support Program to assist businesses access funding programs and assist with the cost of hiring a grant writer. The program will offer 50% up to $2000 to be used to cover costs associated with writing a grant application.', NULL, 'https://www.kingstonecdev.com/business-grant-support-program', 'https://www.kingstonecdev.com/contact-us', 'https://www.kingstonecdev.com/sites/default/files/Business%20Grant%20Support%20Program.pdf', 'Ongoing', 4, 2, 7, NULL, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Starter Company Plus', 'Starter Company Plus is a program offered through Kingston Economic Development Corporation with funding from the Government of Ontario. This program is designed to provide business training for entrepreneurs aged 18 and over who are launching a business or expanding an existing business. The program provides an opportunity to pitch for a grant up to $5,000 to start or grow your business.', NULL, 'https://www.kingstonecdev.com/starter-company-plus', 'https://www.kingstonecdev.com/contact-us', 'https://www.kingstonecdev.com/sites/default/files/2019-10/Starter%20Company%20Plus%20Application%20Form.pdf', 'Intake ends May 15 Notification of program participant registration May 20th Business bootcamp June 1-5th Pitch delivery June 17th Notification of grant award recipients June 19th', 4, 2, 8, NULL, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'SOAN Pitch Events', 'The Southeastern Ontario Angel Network (SOAN) has been created to facilitate the mobilization of investments from accredited investors into companies being developed in southeastern Ontario.  This Network is closely associated with our regional innovation centre, local economic development agencies, research institutions, the Ontario Network of Entrepreneurs and other recognized angel networks in Ontario, Canada and the US. The Southeastern Ontario Angel Network is a not-for-profit corporation, recognized by and registered with the Network of Angel Organizations – Ontario.', NULL, 'https://www.soangels.ca/about-soan', 'https://www.soangels.ca/contact-us', 'https://www.soangels.ca/contact-us', '', 5, 1, 9, NULL, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'DDQIC Regional Pitch Competition', 'The DDQIC hosts pitch competitions to give early-stage startups the opportunity to compete for seed funding for their venture, or to admit a team of co-founders into the Queen\'s Innovation Centre Summer Initiative (QICSI) program. Startups will be pitching to a panel of expert judges to gain valuable feedback on their business model while getting a chance to win significant funding and support for their company. This competition is open to ventures at Queen\'s and in the Kingston region. Applicants must be based in the Kingston region but do NOT have to be associated with Queen\'s University. In the adjudication process, the impact of the venture on the Kingston region will be taken into consideration for all regional applicants.', 'Mitchell Hall 69 Union St, Kingston, ON K7L 2N9', 'https://www.queensu.ca/innovationcentre/programs/regional-pitch-competition-series', 'innovation.centre@queensu.ca', 'https://www.queensu.ca/innovationcentre/programs/regional-pitch-competition-series', 'The Regional Pitch Competition Series is made up of two pitch competitions per year in the winter and summer. The next opportunity to pitch in the series will be in the Dunin-Deshpande Summer Pitch Competition (August 2020).', 1, 1, NULL, 1, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Konnect', 'Konnect is a 6 month program that provides a network, support, and personal and professional development opportunities for self-identifying women who are working or starting businesses in Kingston. Konnect is a program that facilitates accountability, support, community, skill building, and opportunities to amplify your success in building businesses or advancing professionally. The program of regular speakers and meet-ups will run in Kingston starting October 2019.', 'Mitchell Hall 69 Union St, Kingston, ON K7L 2N9', 'https://www.queensu.ca/innovationcentre/programs/konnect', 'innovation.centre@queensu.ca', NULL, 'Selection - July  2019  Start of Program -  October 2019  Mid-point program assessment - December 2019 Program commencement and review - March 2020', 1, 2, NULL, 1, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'The Hive', 'Are you a community member or student who is interested in starting your own venture? The Hive is for all self-identified women who are looking for a supportive community to explore entrepreneurship and innovation.  We particularly welcome self-identified women from underrepresented groups in The Hive. We believe we have a greater impact on society when we tackle problems from diverse perspectives. Our program participants come from all walks of life and our differences allow us to grow together. We uphold a commitment to inclusion and diversity so that we can support anyone who wants to pursue entrepreneurship.', 'Mitchell Hall 69 Union St, Kingston, ON K7L 2N9', 'https://www.queensu.ca/innovationcentre/programs/hive', 'innovation.centre@queensu.ca', 'Email: innovation.centre@queensu.ca Subject: Joining The Hive', NULL, 1, 2, NULL, 1, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'SparQ Studios', 'SparQ Studios is a makerspace and design studio that provides a wide range of tools, machinery, knowledge, and expertise so that you can bring your idea into a physical form.', 'Mitchell Hall 69 Union St, Kingston, ON K7L 2N9', 'https://www.queensu.ca/innovationcentre/sparq-studios-makerspace', '', 'https://www.eventbrite.ca/e/sparq-studios-membership-registration-winter-2020-tickets-86008406459', 'Ongoing', 1, 2, NULL, 1, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Compass North Accellerator', 'Compass North is a Kingston-based five-month accelerator program for women entrepreneurs operating a technology-based business in the Belleville-Kingston-Brockville region. Ignited by Queen\'s University and L-SPARK, the program features high-touch mentoring, tactical workshops and a tight-knit community of entrepreneurs who \'get it\'. This program is designed to help you grow your company with hustle and heart.', 'Kingston', 'https://www.compass-north.com/', 'info@l-spark.com', 'https://www.compass-north.com/apply-now/', NULL, 1, 2, NULL, 4, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Queen\'s Venture Network', 'Queen\'s Venture Network is a new initiative platform for Alumni, Students and Passionate Entrepreneurs that provides members with an enhanced opportunity to connect and network with one another. Join us to collaborate with entrepreneurs, brainstorm and test your ideas, share and gain a variety of information and receive expert mentorship from investors.', NULL, 'https://smith.queensu.ca/centres/business-venturing/venture-network/index.php', NULL, 'https://smith.queensu.ca/centres/business-venturing/venture-network/index.php', NULL, 1, 3, NULL, 5, 5);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'TriColour Venture Fund', 'TriColour Venture fund (TCVF) at Smith School of Business is Canada\'s first student-advised venture capital fund. This VC fund allows Full Time MBA and Commerce students to gain the ultimate experiential learning opportunity through managing a multi-million dollar fund.', NULL, 'https://smith.queensu.ca/centres/business-venturing/core-programs/index.php', NULL, 'submit an executive summary (no more than 2 pages) along with a business pitch deck presentation (please save your files with your business name) to JP Shearer at john-paul.shearer@queensu.ca', NULL, 1, 1, 10, 5, NULL);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'The Community Solutions Lab (CSL)', 'The Community Solutions Lab (CSL), the first initiative from the Collective Impact Launchpad, will give students a new venue to make a difference in the local community. The CSL will deploy multi-disciplinary teams of students to examine complex problems faced by community organizations. The teams will use a social innovation lab approach with people from diverse backgrounds collaborating to develop solutions quickly and determine their applicability in the real world.', NULL, 'https://smith.queensu.ca/centres/social-impact/social_innovation/community-solutions.php', 'csi@queensu.ca', 'https://apps.bus.queensu.ca/formassembly/5', 'Ongoing', 1, 2, NULL, 5, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Kingston Region Business Support Network', 'Local businesses, not-for-profits and social enterprises can tap into the expertise, and skills of Smith School of Business students. Participating students come from across the scope of Smith business programs from undergraduate to professional masters and graduate level research programs, and bring a diverse range of skills and experience. Services could include: research, strategic planning, building a digital presence, web site development, sales and marketing, e-commerce, how to innovate and pivot, design thinking, writing government grant applications, and more.', 'Online', 'https://smith.queensu.ca/centres/kingston-business/index.php', 'csi@queensu.ca', 'https://www.appliedworkexperience.com/employers/sign_up', 'Ongoing', 1, 2, NULL, 5, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Kingston Region Business Support Network', 'Local businesses, not-for-profits and social enterprises can tap into the expertise, and skills of Smith School of Business students. Participating students come from across the scope of Smith business programs from undergraduate to professional masters and graduate level research programs, and bring a diverse range of skills and experience. Services could include: research, strategic planning, building a digital presence, web site development, sales and marketing, e-commerce, how to innovate and pivot, design thinking, writing government grant applications, and more.', 'Online', 'https://smith.queensu.ca/centres/kingston-business/index.php#upcoming-webinars', 'csi@queensu.ca', NULL, 'Ongoing', 1, 4, NULL, 5, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Smith Business Insight', 'Smith School of Business also offers a rich selection of articles, videos and webinars about business best practices and latest research', 'Online', 'https://smith.queensu.ca/insight/index.php', 'https://smith.queensu.ca/insight/contact.php', NULL, 'Ongoing', 1, 4, NULL, 5, 5);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'The Career Advancement Centre', 'With 14 academic programs and almost 1500 graduates a year, Smith School of Business provides a greater breadth of talent than any other Canadian business school. From entry level to senior executives, we can help you create your company\'s future… now. Our Corporate Relations team partners with you to raise your company profile and engage students.', NULL, 'https://smith.queensu.ca/recruiting/index.php', 'https://smith.queensu.ca/recruiting/contact.php', 'https://smith.queensu.ca/recruiting/contact.php', 'Ongoing', 1, 2, NULL, 5, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Smith Business Consulting', 'Smith Business Consulting (SBC) is a student run management consulting firm that partners with businesses, start–ups, non-profits and public institutions each year. We have a team of well-trained student consultants who specialize in strategy, sales, marketing, data analysis and operational planning; having successfully performed a variety of projects in these areas for over 30 years.', NULL, 'https://smith.queensu.ca/centres/business-consulting/index.php', 'sbc@queensu.ca', 'https://smith.queensu.ca/centres/business-consulting/get-started.php', 'Ongoing', 1, 2, NULL, 5, 1);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Executive Decision Centre', 'The Executive Decision Centre at Smith School of Business has developed innovative ways for technology to support the planning and decision-making tasks of executive teams from both private and public sector organizations. The Centre is also used by Smith\'s faculty research, teaching, and administrative committees. The technology effectively creates an electronic meeting room containing a large screen projection system, and a microcomputer network.', NULL, 'https://smith.queensu.ca/centres/decision-centre/index.php', 'https://smith.queensu.ca/centres/decision-centre/contact_us.php', 'https://smith.queensu.ca/centres/decision-centre/contact_us.php', 'Ongoing', 1, 2, NULL, 5, NULL);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Executive Education', 'Queen\'s Executive Education programs provide powerful tools for facilitating meaningful organizational change.', NULL, 'https://smith.queensu.ca/executiveeducation/search/index.php', 'execed@queensu.ca', 'https://smith.queensu.ca/executiveeducation/enroll/index.php', NULL, 1, 2, NULL, 5, 5);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-04-28', 'Executive Education Custom Programs', 'Clients from around the world have engaged us to build customized solutions in important areas, including: improving strategic planning and implementation processes, creating high-performance culture, managing growth, fostering innovation, developing leadership, and many more.', NULL, 'https://smith.queensu.ca/executiveeducation/custom_programs.php', 'execed@queensu.ca', 'https://smith.queensu.ca/executiveeducation/custom_request.php', NULL, 1, 2, NULL, 5, 5);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Applied Research', 'Please contact our Industry Liaison if you: Have an idea for product or process development Have a problem with current systems Want to identify and capitalize on cost-saving opportunities Want to test product design or evaluate process implementation', 'St Lawrence College Kingston Campus', 'https://www.stlawrencecollege.ca/about/applied-research/', 'idick@sl.on.ca', 'Email: idick@sl.on.ca', 'Ongoing', 2, 2, 11, 3, 3);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Better Business Writing', 'Teach your workforce key skills such as technical writing, composing powerful correspondence, reports, and proposals, producing effective communications quickly and accurately, and writing letters and emails that will get you buy-in. Suitable for individuals who prepare reports and correspondence on a regular basis and want to ensure their writing style meets high professional standards. Also suitable for anyone who wishes a refresher course on business writing techniques or those who want to speed up the production and effectiveness of business documents. Who Should Take This Course? Managers Office Staff Supervisors Administrative Staff Communication and Public Relations Clerks Individuals responsible for business correspondence', NULL, 'http://www.slccorporatetraining.ca/programs/business-writing.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Custom Corporate Training', 'CLPI clients maximize the return on their training investment when The work with St. Lawrence to develop their teams. Relevance and smooth transfer of new skills is guaranteed during our custom corporate training expierence. To achieve this, CLPI customizes program content and instructional materials to reflect company-specific issues and realities. Our instructors meet with company stakeholders prior to finalizing their program content to incorporate templates, language, case studies and examples that are industry and company-specific, wherever possible.', NULL, 'http://www.slccorporatetraining.ca/programs/custom-training.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'LEAN Management Training', 'For years, LEAN concepts and tools have been applied extensively in manufacturing operations with impressive results. More recently, service based organizations, which account for over 85% of Eastern Ontario\'s business, are realizing the benefits of this huge opportunity to drive efficiencies and eradicate waste. Our LEAN Management Training is a specialized program on applying LEAN principles, tools and techniques to identify and remove any non-value-added activities in your everyday processes. Who Should Take This Course? Managers Business Owners Senior Leaders Entrepreneurs Continuous Improvement Facilitators Supply Chain, Production and Logistics Professionals', NULL, 'http://www.slccorporatetraining.ca/programs/lean-management-training.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Meetings That Work', 'Do you find meetings a waste of time, resources and energy? Do the meetings you attend often end in indecision, lack follow-up or involve the wrong people to ensure the necessary buy-in for success? Meetings are expensive; they require a large investment of money, time and effort. Learn how to run a successful meeting by using tools taught by our in-house professional that will get you the best return and productivity out of your meetings. This workshop will provide you with techniques to: Assess the expected ROI of a meeting to determine if needed Plan for and execute the meeting to maximize the ROI and the overall meeting satisfaction/results Manage your meetings, set and meet objectives and manage time Help your groups reach a consensus on objectives that is actionable and sustainable', NULL, 'http://www.slccorporatetraining.ca/programs/successful-meetings.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Microsoft Office Training', 'CLPI provides targeted training with a focus on minimizing your learning curve and streamlining business processes. Our hands-on Microsoft Office training is given by our certified MS Office instructors. We also offer customized programs designed to suit the needs of the varying user groups in your organization.', NULL, 'http://www.slccorporatetraining.ca/programs/microsoft-office-training.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Strategic Planning Implementation', 'At St. Lawrence College Corporate Learning and Performance Improvement, we believe a good Strategic Plan is a beacon bringing clarity and insight to your organization\'s true mission and values. Our solutions will guide you through your most difficult challenges delivering better decisions and setting the table for higher performance and long term sustainable results in the future. Trust us to guide you through this essential process with our unique 4 Step Approach.', NULL, 'http://www.slccorporatetraining.ca/programs/strategic-planning.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Leadership for Managers Program', 'St. Lawrence College, Corporate Learning and Performance Improvement, proudly presents The Leadership for Managers Program. This fast track professional development program is offered throughout the year in various formats. Our leadership courses are offered as a stand-alone module, or part of a five-day program. All content listed is customizable for company-specific programs of intact teams.', NULL, 'http://www.slccorporatetraining.ca/programs/leadership.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Health and Safety Training', 'Apart from the effective presentation of most typical \'certified\' health and safety training courses, our professional trainers are capable of developing programs to overcome a series of in-house, company-specific hazards related to an engineered mechanical or assembly process, then training into bottom-up compliance.', NULL, 'http://www.slccorporatetraining.ca/programs/health-safety-training.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Logistics Management Training Programs', 'For employers in the manufacturing and logistics industry, our partner, KPI Solutions is recognized as a qualified service provider for the Canadian Manufacturers and Exporters SMART program. These logistics management training programs are specifically designed to improve the areas of: Lean Manufacturing Kaizen Six Sigma 5S Productivity Improvements Quality Improvements Information/Management Systems Process Flow Human Resources', NULL, 'http://www.slccorporatetraining.ca/programs/logistics-management-training.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Workplace Wellness Program', 'Our workplace wellness program gives you the opportunity to custom build a health and wellness program for your organization. Using an evidence – based approach, we assess the health needs of your employees and design a customized program to help reduce the risks identified.', NULL, 'http://www.slccorporatetraining.ca/programs/workplace-wellness.html', 'corporatelearning@sl.on.ca', 'http://www.slccorporatetraining.ca/contact/', 'Ongoing', 2, 2, NULL, 6, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Employment Services for Businesses', 'We work with you to make the hiring process more streamlined and cost effective. Post your positions on our job board to connect with St. Lawrence College students, graduates and community job seekers. Increase your applications by leveraging social media to advertise your opportunities, collect resumes online and have them forwarded to an email address of your choice.', 'Kingston -- Ottawa -- Sharbot Lake', 'http://www.employmentservice.sl.on.ca/employers/programs/employment-service.html', 'ESK@sl.on.ca', NULL, 'Ongoing', 2, 2, NULL, 7, 4);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Apprenticeships', 'An apprentice is someone who learns a skilled trade on the job, under the direction of more experienced workers. Apprentices also complete classroom instruction as a part of their training. Apprentices are employees, and earn a salary while they learn a skilled trade. To become and apprentice, you must find an employer or sponsor who is willing to train you and engage in apprenticeship training. People who want to become apprentices usually apply directly to an employer or union.', NULL, NULL, NULL, NULL, NULL, 2, 2, NULL, 7, NULL);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Managing Human Resources', 'This guide outlines some of the fundamental aspects of human resource management issues an employer might encounter during and after the hiring process.', 'http://www.employmentservice.sl.on.ca/files/Employer-ManagingHumanResources.pdf', 'http://www.employmentservice.sl.on.ca/files/Employer-ManagingHumanResources.pdf', 'ESK@sl.on.ca', NULL, 'Ongoing', 2, 4, NULL, 7, 2);";
		$sql[] = "INSERT INTO `resources` (`resourceID`, `resourceDateAdded`, `resourceName`, `resourceDescription`, `resourceWhereDelivered`, `resourceURL`, `resourceEmail`, `resourceHowToApply`, `resourceWhenOffered`, `organizationID`, `supportID`, `coordinatorID`, `departmentID`, `regionID`) VALUES (DEFAULT, '2020-02-24', 'Onboarding Young Workers in Your Workplace', 'This guide outlines some of the challenges employers might face when integrating young workers into the workplace. It offers tips and strategies to ensure a smooth transition.', 'http://www.employmentservice.sl.on.ca/files/Young-Workers-in-Your-Workplace-A-Guide-for-Business-Owners.pdf', 'http://www.employmentservice.sl.on.ca/files/Young-Workers-in-Your-Workplace-A-Guide-for-Business-Owners.pdf', 'ESK@sl.on.ca', NULL, 'Ongoing', 2, 4, NULL, 7, 2);";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_resource_has_busninessStages_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (1, 2);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (1, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (1, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (2, 2);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (2, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (2, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (3, 2);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (3, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (3, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (4, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (4, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (5, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (5, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (5, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (6, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (6, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (6, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (7, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (7, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (7, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (7, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (8, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (8, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (9, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (9, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (9, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (10, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (10, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (10, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (11, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (11, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (11, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (12, 1);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (13, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (13, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (14, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (14, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (14, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (15, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (15, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (16, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (16, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (16, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (17, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (17, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (17, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (18, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (18, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (19, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (19, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (20, 1);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (21, 2);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (21, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (22, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (22, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (22, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (22, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (23, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (23, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (23, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (24, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (24, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (24, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (25, 1);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (26, 2);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (26, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (26, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (26, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (27, 2);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (27, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (27, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (28, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (28, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (29, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (29, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (29, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (30, 1);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (31, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (31, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (31, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (32, 1);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (33, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (33, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (33, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (33, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (34, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (34, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (34, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (34, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (35, 1);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (36, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (36, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (37, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (37, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (37, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (37, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (38, 3);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (38, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (38, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (38, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (39, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (39, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (39, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (40, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (40, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (40, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (41, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (41, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (41, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (42, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (42, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (43, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (43, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (44, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (44, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (45, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (45, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (46, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (46, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (47, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (47, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (48, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (48, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (49, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (49, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (50, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (50, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (51, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (51, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (52, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (52, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (52, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (53, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (53, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (53, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (54, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (54, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (54, 6);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (55, 4);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (55, 5);";
		$sql[] = "INSERT INTO `resources_has_businessStages` (`resourceID`, `stageID`) VALUES (55, 6);";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_supportCategories_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'All');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Business Strategy & Mentorship');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Financial Management & Strategy');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Sales & Exporting ');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Marketing & Customer Acquisition');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Funding');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Talent, HR & Training');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Space (office, labs, warehousing, coworking, event)');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Research & Development');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Information Technology');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Legal, Licensing & IP');";
		$sql[] = "INSERT INTO `supportCategories` (`categoryID`, `categoryName`) VALUES (DEFAULT, 'Manufacturing & Supply Chain Management');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_resource_has_supportCategories_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (1, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (1, 9);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (1, 11);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (2, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (2, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (2, 8);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (2, 9);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (3, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (3, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (3, 8);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (4, 11);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (5, 5);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (6, 1);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (7, 11);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (8, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (8, 3);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (8, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (8, 8);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (8, 9);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (8, 10);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (8, 11);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (9, 1);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (10, 4);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (11, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (11, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (11, 9);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (11, 11);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (12, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (12, 3);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (12, 5);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (12, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (12, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (12, 10);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (13, 1);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (14, 1);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (15, 1);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (16, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (16, 4);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (16, 5);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (16, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (16, 10);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (17, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (17, 3);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (18, 4);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (18, 5);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (18, 12);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (19, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (19, 3);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (20, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (21, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (21, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (22, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (22, 3);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (22, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (23, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (23, 3);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (23, 4);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (23, 5);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (23, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (24, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (25, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (26, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (27, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (27, 8);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (28, 8);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (28, 9);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (28, 12);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (29, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (30, 1);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (31, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (32, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (32, 9);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (33, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (34, 1);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (35, 1);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (36, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (37, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (37, 3);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (37, 4);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (37, 5);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (37, 9);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (38, 2);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (38, 8);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (38, 10);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (39, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (40, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (41, 6);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (41, 9);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (42, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (43, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (44, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (45, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (46, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (47, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (48, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (49, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (50, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (51, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (52, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (54, 7);";
		$sql[] = "INSERT INTO `resources_has_supportCategories` (`resourceID`, `categoryID`) VALUES (55, 7);";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_targetIndustries_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0000, 'All');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0011, 'AGRICULTURE/FORESTRY/FISHING AND HUNTING');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1100, 'AgriFood');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1111, 'Oilseed and Grain Farming');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1112, 'Vegetable and Melon Farming');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1113, 'Fruit and Tree Nut Farming');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1114, 'Greenhouse, Nursery, and Floriculture Production');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1119, 'Other Crop Farming');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1121, 'Cattle Ranching and Farming');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1122, 'Hog and Pig Farming');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1123, 'Poultry and Egg Production');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1124, 'Sheep and Goat Farming');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1125, 'Aquaculture');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1129, 'Other Animal Production');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1131, 'Timber Tract Operations');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1132, 'Forest Nurseries and Gathering of Forest Products');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1133, 'Logging');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1141, 'Fishing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1142, 'Hunting and Trapping');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1151, 'Support Activities for Crop Production');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1152, 'Support Activities for Animal Production');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (1153, 'Support Activities for Forestry');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0021, 'MINING');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2111, 'Oil and Gas Extraction');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2121, 'Coal Mining');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2122, 'Metal Ore Mining');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2123, 'Nonmetallic Mineral Mining and Quarrying');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2131, 'Support Activities for Mining');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0022, 'UTILITIES');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2211, 'Electric Power Generation, Transmission and Distribution');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2212, 'Natural Gas Distribution');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2213, 'Water, Sewage and Other Systems');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0023, 'CONSTRUCTION');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2361, 'Residential Building Construction');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2362, 'Nonresidential Building Construction');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2371, 'Utility System Construction');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2372, 'Land Subdivision');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2373, 'Highway, Street, and Bridge Construction');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2379, 'Other Heavy and Civil Engineering Construction');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2381, 'Foundation, Structure, and Building Exterior Contractors');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2382, 'Building Equipment Contractors');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2383, 'Building Finishing Contractors');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (2389, 'Other Specialty Trade Contractors');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0031, 'MANUFACTURING');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3100, 'Advanced Materials and Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3111, 'Animal Food Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3112, 'Grain and Oilseed Milling');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3113, 'Sugar and Confectionery Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3114, 'Fruit and Vegetable Preserving and Specialty Food Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3115, 'Dairy Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3116, 'Animal Slaughtering and Processing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3117, 'Seafood Product Preparation and Packaging');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3118, 'Bakeries and Tortilla Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3119, 'Other Food Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3121, 'Beverage Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3122, 'Tobacco Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3131, 'Fiber, Yarn, and Thread Mills');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3132, 'Fabric Mills');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3133, 'Textile and Fabric Finishing and Fabric Coating Mills');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3141, 'Textile Furnishings Mills');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3149, 'Other Textile Product Mills');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3151, 'Apparel Knitting Mills');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3152, 'Cut and Sew Apparel Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3159, 'Apparel Accessories and Other Apparel Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3161, 'Leather and Hide Tanning and Finishing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3162, 'Footwear Manufacturing	');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3169, 'Other Leather and Allied Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3211, 'Sawmills and Wood Preservation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3212, 'Veneer, Plywood, and Engineered Wood Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3219, 'Other Wood Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3221, 'Pulp, Paper, and Paperboard Mills');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3222, 'Converted Paper Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3231, 'Printing and Related Support Activities');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3241, 'Petroleum and Coal Products Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3251, 'Basic Chemical Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3252, 'Resin, Synthetic Rubber, and Artificial and Synthetic Fibers and Filaments Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3253, 'Pesticide, Fertilizer, and Other Agricultural Chemical Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3254, 'Pharmaceutical and Medicine Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3255, 'Paint, Coating, and Adhesive Manufacturing ');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3256, 'Soap, Cleaning Compound, and Toilet Preparation Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3259, 'Other Chemical Product and Preparation Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3261, 'Plastics Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3262, 'Rubber Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3271, 'Clay Product and Refractory Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3272, 'Glass and Glass Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3273, 'Cement and Concrete Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3274, 'Lime and Gypsum Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3279, 'Other Nonmetallic Mineral Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3311, 'Iron and Steel Mills and Ferroalloy Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3312, 'Steel Product Manufacturing from Purchased Steel');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3313, 'Alumina and Aluminum Production and Processing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3314, 'Nonferrous Metal (except Aluminum) Production and Processing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3315, 'Foundries');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3321, 'Forging and Stamping');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3322, 'Cutlery and Handtool Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3323, 'Architectural and Structural Metals Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3324, 'Boiler, Tank, and Shipping Container Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3325, 'Hardware Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3326, 'Spring and Wire Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3327, 'Machine Shops; Turned Product; and Screw, Nut, and Bolt Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3328, 'Coating, Engraving, Heat Treating, and Allied Activities');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3329, 'Other Fabricated Metal Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3331, 'Agriculture, Construction, and Mining Machinery Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3332, 'Industrial Machinery Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3333, 'Commercial and Service Industry Machinery Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3334, 'Ventilation, Heating, Air-Conditioning, and Commercial Refrigeration Equipment Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3335, 'Metalworking Machinery Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3336, 'Engine, Turbine, and Power Transmission Equipment Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3339, 'Other General Purpose Machinery Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3341, 'Computer and Peripheral Equipment Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3342, 'Communications Equipment Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3343, 'Audio and Video Equipment Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3344, 'Semiconductor and Other Electronic Component Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3345, 'Navigational, Measuring, Electromedical, and Control Instruments Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3346, 'Manufacturing and Reproducing Magnetic and Optical Media');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3351, 'Electric Lighting Equipment Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3352, 'Household Appliance Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3353, 'Electrical Equipment Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3359, 'Other Electrical Equipment and Component Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3361, 'Motor Vehicle Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3362, 'Motor Vehicle Body and Trailer Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3363, 'Motor Vehicle Parts Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3364, 'Aerospace Product and Parts Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3365, 'Railroad Rolling Stock Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3366, 'Ship and Boat Building');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3369, 'Other Transportation Equipment Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3371, 'Household and Institutional Furniture and Kitchen Cabinet Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3372, 'Office Furniture (including Fixtures) Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3379, 'Other Furniture Related Product Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3391, 'Medical Equipment and Supplies Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (3399, 'Other Miscellaneous Manufacturing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0042, 'WHOLESALE TRADE');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4231, 'Motor Vehicle and Motor Vehicle Parts and Supplies Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4232, 'Furniture and Home Furnishing Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4233, 'Lumber and Other Construction Materials Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4234, 'Professional and Commercial Equipment and Supplies Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4235, 'Metal and Mineral (except Petroleum) Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4236, 'Household Appliances and Electrical and Electronic Goods Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4237, 'Hardware, and Plumbing and Heating Equipment and Supplies Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4238, 'Machinery, Equipment, and Supplies Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4239, 'Miscellaneous Durable Goods Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4241, 'Paper and Paper Product Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4242, 'Drugs and Druggists\' Sundries Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4243, 'Apparel, Piece Goods, and Notions Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4244, 'Grocery and Related Product Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4245, 'Farm Product Raw Material Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4246, 'Chemical and Allied Products Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4247, 'Petroleum and Petroleum Products Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4248, 'Beer, Wine, and Distilled Alcoholic Beverage Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4249, 'Miscellaneous Nondurable Goods Merchant Wholesalers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4251, 'Wholesale Electronic Markets and Agents and Brokers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0044, 'RETAIL TRADE');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4411, 'Automobile Dealers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4412, 'Other Motor Vehicle Dealers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4413, 'Automotive Parts, Accessories, and Tire Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4421, 'Furniture Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4422, 'Home Furnishings Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4431, 'Electronics and Appliance Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4441, 'Building Material and Supplies Dealers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4442, 'Lawn and Garden Equipment and Supplies Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4451, 'Grocery Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4452, 'Specialty Food Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4453, 'Beer, Wine, and Liquor Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4461, 'Health and Personal Care Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4471, 'Gasoline Stations');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4481, 'Clothing Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4482, 'Shoe Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4483, 'Jewelry, Luggage, and Leather Goods Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4511, 'Sporting Goods, Hobby, and Musical Instrument Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4512, 'Book Stores and News Dealers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4522, 'Department Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4523, 'General Merchandise Stores, including Warehouse Clubs and Supercenters');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4531, 'Florists');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4532, 'Office Supplies, Stationery, and Gift Stores');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4539, 'Other Miscellaneous Store Retailers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4541, 'Electronic Shopping and Mail-Order Houses');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4542, 'Vending Machine Operators');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4543, 'Direct Selling Establishments');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0048, 'TRANSPORTATION AND WAREHOUSING');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4811, 'Scheduled Air Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4812, 'Nonscheduled Air Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4821, 'Rail Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4831, 'Deep Sea, Coastal, and Great Lakes Water Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4832, 'Inland Water Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4841, 'General Freight Trucking');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4842, 'Specialized Freight Trucking');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4851, 'Urban Transit Systems');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4852, 'Interurban and Rural Bus Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4853, 'Taxi and Limousine Service');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4854, 'School and Employee Bus Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4855, 'Charter Bus Industry');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4859, 'Other Transit and Ground Passenger Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4861, 'Pipeline Transportation of Crude Oil');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4862, 'Pipeline Transportation of Natural Gas');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4869, 'Other Pipeline Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4871, 'Scenic and Sightseeing Transportation, Land');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4872, 'Scenic and Sightseeing Transportation, Water');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4879, 'Scenic and Sightseeing Transportation, Other');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4881, 'Support Activities for Air Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4882, 'Support Activities for Rail Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4883, 'Support Activities for Water Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4884, 'Support Activities for Road Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4885, 'Freight Transportation Arrangement');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4889, 'Other Support Activities for Transportation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4911, 'Postal Service');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4921, 'Couriers and Express Delivery Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4922, 'Local Messengers and Local Delivery');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (4931, 'Warehousing and Storage');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0051, 'INFORMATION');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5111, 'Newspaper, Periodical, Book, and Directory Publishers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5112, 'Software Publishers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5121, 'Motion Picture and Video Industries');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5122, 'Sound Recording Industries');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5151, 'Radio and Television Broadcasting');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5152, 'Cable and Other Subscription Programming');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5173, 'Wired and Wireless Telecommunications Carriers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5174, 'Satellite Telecommunications');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5179, 'Other Telecommunications');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5182, 'Data Processing, Hosting, and Related Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5191, 'Other Information Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0052, 'FINANCE AND INSURANCE');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5211, 'Monetary Authorities-Central Bank');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5221, 'Depository Credit Intermediation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5222, 'Nondepository Credit Intermediation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5223, 'Activities Related to Credit Intermediation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5231, 'Securities and Commodity Contracts Intermediation and Brokerage');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5232, 'Securities and Commodity Exchanges');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5239, 'Other Financial Investment Activities');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5241, 'Insurance Carriers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5242, 'Agencies, Brokerages, and Other Insurance Related Activities');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5251, 'Insurance and Employee Benefit Funds');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5259, 'Other Investment Pools and Funds');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0053, 'REAL ESTATE RENTAL AND LEASING');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5311, 'Lessors of Real Estate');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5312, 'Offices of Real Estate Agents and Brokers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5313, 'Activities Related to Real Estate');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5321, 'Automotive Equipment Rental and Leasing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5322, 'Consumer Goods Rental');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5323, 'General Rental Centers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5324, 'Commercial and Industrial Machinery and Equipment Rental and Leasing');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5331, 'Lessors of Nonfinancial Intangible Assets (except Copyrighted Works)');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0054, 'PROFESSIONAL, SCIENTIFIC, AND TECHNICAL SERVICES');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5400, 'CleanTech & Energy');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5411, 'Legal Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5412, 'Accounting, Tax Preparation, Bookkeeping, and Payroll Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5413, 'Architectural, Engineering, and Related Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5414, 'Specialized Design Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5415, 'Computer Systems Design and Related Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5416, 'Management, Scientific, and Technical Consulting Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5417, 'Scientific Research and Development Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5418, 'Advertising, Public Relations, and Related Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5419, 'Other Professional, Scientific, and Technical Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0055, 'MANAGEMENT OF COMPANIES AND ENTERPRISES');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0056, 'ADMINISTRATIVE, SUPPORT, AND WASTE MANAGEMENT AND REMEDIATION SERVICES');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5611, 'Office Administrative Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5612, 'Facilities Support Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5613, 'Employment Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5614, 'Business Support Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5615, 'Travel Arrangement and Reservation Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5616, 'Investigation and Security Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5617, 'Services to Buildings and Dwellings');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5619, 'Other Support Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5621, 'Waste Collection');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5622, 'Waste Treatment and Disposal');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (5629, 'Remediation and Other Waste Management Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0061, 'EDUCATIONAL SERVICES');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6100, 'Education & Edtech');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6111, 'Elementary and Secondary Schools');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6112, 'Junior Colleges');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6113, 'Colleges, Universities, and Professional Schools');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6114, 'Business Schools and Computer and Management Training');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6115, 'Technical and Trade Schools');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6116, 'Other Schools and Instruction');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6117, 'Educational Support Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0062, 'HEALTH CARE AND SOCIAL ASSISTANCE');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6211, 'Offices of Physicians');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6212, 'Offices of Dentists');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6213, 'Offices of Other Health Practitioners');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6214, 'Outpatient Care Centers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6215, 'Medical and Diagnostic Laboratories');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6216, 'Home Health Care Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6219, 'Other Ambulatory Health Care Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6221, 'General Medical and Surgical Hospitals');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6222, 'Psychiatric and Substance Abuse Hospitals');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6223, 'Specialty (except Psychiatric and Substance Abuse) Hospitals');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6231, 'Nursing Care Facilities (Skilled Nursing Facilities)');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6232, 'Residential Intellectual and Developmental Disability, Mental Health, and Substance Abuse Facilities');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6233, 'Continuing Care Retirement Communities and Assisted Living Facilities for the Elderly');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6239, 'Other Residential Care Facilities');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6241, 'Individual and Family Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6242, 'Community Food and Housing, and Emergency and Other Relief Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6243, 'Vocational Rehabilitation Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (6244, 'Child Day Care Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0071, 'ARTS, ENTERTAINMENT, AND RECREATION');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7111, 'Performing Arts Companies');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7112, 'Spectator Sports');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7113, 'Promoters of Performing Arts, Sports, and Similar Events');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7114, 'Agents and Managers for Artists, Athletes, Entertainers, and Other Public Figures');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7115, 'Independent Artists, Writers, and Performers');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7121, 'Museums, Historical Sites, and Similar Institutions');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7131, 'Amusement Parks and Arcades');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7132, 'Gambling Industries');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7139, 'Other Amusement and Recreation Industries');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0072, 'ACCOMODATION AND FOOD SERVICES');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7211, 'Traveler Accommodation');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7212, 'RV (Recreational Vehicle) Parks and Recreational Camps');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7213, 'Rooming and Boarding Houses, Dormitories, and Workers\' Camps');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7223, 'Special Food Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7224, 'Drinking Places (Alcoholic Beverages)');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (7225, 'Restaurants and Other Eating Places');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0081, 'OTHER SERVICES (EXCEPT PUBLIC ADMINISTRATION');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8111, 'Automotive Repair and Maintenance');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8112, 'Electronic and Precision Equipment Repair and Maintenance');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8113, 'Commercial and Industrial Machinery and Equipment (except Automotive and Electronic) Repair and Maintenance');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8114, 'Personal and Household Goods Repair and Maintenance');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8121, 'Personal Care Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8122, 'Death Care Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8123, 'Drycleaning and Laundry Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8129, 'Other Personal Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8131, 'Religious Organizations');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8132, 'Grantmaking and Giving Services');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8133, 'Social Advocacy Organizations');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8134, 'Civic and Social Organizations');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8139, 'Business, Professional, Labor, Political, and Similar Organizations');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (8141, 'Private Households');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (0092, 'PUBLIC ADMINISTRATION');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (9211, 'Executive, Legislative, and Other General Government Support');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (9221, 'Justice, Public Order, and Safety Activities');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (9231, 'Administration of Human Resource Programs');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (9241, 'Administration of Environmental Quality Programs');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (9251, 'Administration of Housing Programs, Urban Planning, and Community Development');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (9261, 'Administration of Economic Programs');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (9271, 'Space Research and Technology');";
		$sql[] = "INSERT INTO `targetIndustries` (`naicsCode`, `industryDescription`) VALUES (9281, 'National Security and International Affairs');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_resource_has_targetIndustries_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (2, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (3, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (4, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (5, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (6, 0011);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (6, 1100);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (6, 3100);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (6, 5400);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (6, 0022);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (6, 6100);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (6, 0061);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (12, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (13, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (14, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (15, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (16, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (17, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (8, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (19, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (20, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (21, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (22, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (23, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (24, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (25, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (26, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (27, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (28, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (30, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (31, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (32, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (33, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (34, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (35, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (36, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (37, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (38, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (39, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (40, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (41, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (42, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (43, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (44, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (45, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (46, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (47, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (48, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (49, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (50, 0031);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (51, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (52, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (54, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (55, 0000);";
		$sql[] = "INSERT INTO `resources_has_targetIndustries` (`resourceID`, `naicsCode`) VALUES (1, 0000);";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_underrepresentedGroups_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'None');";
		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'Women');";
		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'Rural');";
		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'Indigenous');";
		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'Physical Disability');";
		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'Mental Disability');";
		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'LGBTQ2S');";
		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'Newcomers to Canada');";
		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'Students');";
		$sql[] = "INSERT INTO `underrepresentedGroups` (`groupID`, `groupName`) VALUES (DEFAULT, 'Community Organizations');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_resource_has_groups_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (2, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (3, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (4, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (5, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (6, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (7, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (8, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (9, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (10, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (11, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (12, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (13, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (14, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (15, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (16, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (17, 2);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (17, 3);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (18, 2);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (19, 2);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (19, 7);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (20, 2);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (21, 8);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (22, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (23, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (24, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (25, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (26, 2);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (27, 2);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (28, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (29, 2);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (30, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (31, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (32, 9);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (33, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (34, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (35, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (36, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (37, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (38, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (39, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (40, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (41, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (42, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (43, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (44, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (45, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (46, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (47, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (48, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (49, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (50, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (51, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (52, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (54, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (55, 1);";
		$sql[] = "INSERT INTO `resources_has_groups` (`resourceID`, `groupID`) VALUES (1, 1);";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_delivery_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, 'Ongoing', NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, 'Virtual', NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, 'Mitchell Hall', '69 Union St', NULL, 'Kingston', 'ON', 'K7L 2N9');";
		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, 'Queen\'s Law Clinics', '303 Bagot St', 'Suite 500', 'Kingston', 'ON', 'K7K 5W7');";
		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, 'Seaway Coworking Building', '310 Bagot St', '', 'Kingston', 'ON', NULL);";
		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, NULL, NULL, NULL, 'Kingston', 'ON', NULL);";
		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, 'Online', NULL, NULL, NULL, NULL, NULL);";
		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, 'St Lawrence College', NULL, NULL, 'Kingston', 'ON', NULL);";
		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, NULL, NULL, NULL, 'Ottawa', 'ON', NULL);";
		$sql[] = "INSERT INTO `delivery` (`deliveryID`, `deliveryLocation`, `deliveryAddress`, `deliveryUnit`, `deliveryCity`, `deliveryProv`, `deliveryPostal`) VALUES (DEFAULT, NULL, NULL, NULL, 'Sharbot Lake', 'ON', NULL);";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_costStructure_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `costStructure` (`costID`, `costDescription`) VALUES (DEFAULT, 'Free');";
		$sql[] = "INSERT INTO `costStructure` (`costID`, `costDescription`) VALUES (DEFAULT, 'In Kind');";
		$sql[] = "INSERT INTO `costStructure` (`costID`, `costDescription`) VALUES (DEFAULT, 'Fee for Service');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_resource_has_costStructure_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (12, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (13, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (14, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (15, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (19, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (21, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (22, 2);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (23, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (24, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (29, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (30, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (31, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (32, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (33, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (34, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (35, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (39, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (40, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (41, 2);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (41, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (42, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (43, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (44, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (45, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (46, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (47, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (48, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (49, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (50, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (51, 3);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (52, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (54, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (55, 1);";
		$sql[] = "INSERT INTO `resources_has_costStructure` (`resourceID`, `costID`) VALUES (5, 3);";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}

	private function add_switchboard_options_data($wpdb) {
		$sql = array();

		$sql[] = "INSERT INTO `switchboard_options` (`optionID`, `filterIDName`, `filterName`, `filterTable`, `filterTitle`, `primaryFilter`, `secondaryFilter`, `filterTag`) VALUES (default, 'stageID', 'stageName', 'businessstages', 'Business Stages', 1, 0, 'stages');";
		$sql[] = "INSERT INTO `switchboard_options` (`optionID`, `filterIDName`, `filterName`, `filterTable`, `filterTitle`, `primaryFilter`, `secondaryFilter`, `filterTag`) VALUES (default, 'supportID', 'supportName', 'supportTypes', 'Support Types', 1, 0. 'types');";
		$sql[] = "INSERT INTO `switchboard_options` (`optionID`, `filterIDName`, `filterName`, `filterTable`, `filterTitle`, `primaryFilter`, `secondaryFilter`, `filterTag`) VALUES (default, 'categoryID', 'categoryName', 'supportCategories', 'Support Categories', 1, 0, 'categories');";
		$sql[] = "INSERT INTO `switchboard_options` (`optionID`, `filterIDName`, `filterName`, `filterTable`, `filterTitle`, `primaryFilter`, `secondaryFilter`, `filterTag`) VALUES (default, 'organizationID', 'organizationName', 'organizations', 'Providers', 0, 1, 'providers');";
		$sql[] = "INSERT INTO `switchboard_options` (`optionID`, `filterIDName`, `filterName`, `filterTable`, `filterTitle`, `primaryFilter`, `secondaryFilter`, `filterTag`) VALUES (default, 'groupID', 'groupName', 'underrepresentedGroups', 'Under Represented Groups', 0, 0, 'groups');";

		foreach ($sql as $statement) {
			$wpdb->query($statement);
		}
	}
}
