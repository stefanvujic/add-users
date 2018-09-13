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
global $wpdb;

require 'classes/atu-activation-class.php';
require 'classes/atu-deactivation-class.php';
require 'classes/atu-interface-class.php';
require 'classes/atu-generate-and-export-class.php';
require 'classes/atu-delete-users-class.php';

$interface = new Atu_interface();

$user_info_generator = new Atu_user_info_generator();
$user_info_generator->atu_generate_users();

$delete_users = new Atu_delete_users();
$delete_users->atu_initiate_delete();

register_activation_hook(__FILE__, array('Atu_activate', 'atu_test_user_activate'));
register_deactivation_hook(__FILE__, array('Atu_deactivate', 'atu_test_user_deactivate'));