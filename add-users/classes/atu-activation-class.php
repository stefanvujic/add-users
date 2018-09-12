<?php
class Atu_activate {
	
    function __construct() {
    	register_activation_hook(__FILE__, 'atu_test_user_activate');
    }

    // --- install/uninstall, activate, deactivate plugin ---- //
	public function atu_test_user_activate() {

		$table_name = 'atu_test_user_names';

		//check if table exists then if not, create it and insert data
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

			include '../names/atu-male-first-names.php';
			include '../names/atu-female-first-names.php';
			include '../names/atu-surnames.php';

			//create table
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
			  id mediumint(11) NOT NULL AUTO_INCREMENT,
			  user_name varchar(255) NOT NULL,
			  user_gender varchar(255) DEFAULT '' NOT NULL,
			  name_type varchar(255) DEFAULT '' NOT NULL,
			  PRIMARY KEY  (id)
			) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			//add data
			foreach ($local_male_names_array as $male_name_key => $male_name) {
				$wpdb->insert('test_user_names', array(
				    'user_name'   =>  $male_name,
				    'user_gender' =>  'male',
				    'name_type'   =>  'firstname'
				));
			}
			foreach ($local_female_names_array as $female_name_key => $female_name) {
				$wpdb->insert('test_user_names', array(
				    'user_name'   =>  $female_name,
				    'user_gender' =>  'female',
				    'name_type'   =>  'firstname'
				));
			}
			foreach ($local_surnames_array as $surname_key => $surname) {
				$wpdb->insert('test_user_names', array(
				    'user_name'   =>  ucfirst(strtolower($surname)),
				    'name_type'   =>  'surname'
				));
			}				
		}	
	}
}