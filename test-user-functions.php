<?php
/*
Plugin Name: WP Test User  
Plugin URI: https://imstefan.co.uk/wp-test-user/
Description: Test User Generator
Version: 1.0
Author: Stefan Vujic
Author URI: https://imstefan.co.uk/
License: GPLv2 or later 
*/


function test_user_activation() {
global $wpdb;

	include 'male-first-names.php';
	include 'female-first-names.php';
	$table_name = 'test_user_names';

	//check if table exists then if not, create it and insert data
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		
		//create table
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
		  id mediumint(11) NOT NULL AUTO_INCREMENT,
		  user_name varchar(255) NOT NULL,
		  user_gender varchar(255) DEFAULT '' NOT NULL,
		  name_type varchar(255) DEFAULT '' NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		//add data
		foreach ($local_male_names_array as $male_name_key => $male_name) {
			$wpdb->insert('test_user_names', array(
			    'user_name' => $male_name,
			    'user_gender' => 'male',
			    'name_type' => 'firstname'
			));
		}
		foreach ($local_female_names_array as $female_name_key => $female_name) {
			$wpdb->insert('test_user_names', array(
			    'user_name' => $female_name,
			    'user_gender' => 'female',
			    'name_type' => 'firstname'
			));
		}			
	}	
}
register_activation_hook(__FILE__, 'test_user_activation');

function my_plugin_remove_database() {
global $wpdb;
    $table_name = 'test_user_names';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}
register_deactivation_hook( __FILE__, 'my_plugin_remove_database' );