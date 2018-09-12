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

require '/classes/atu-activation-class.php';
require '/classes/atu-deactivation-class.php';

$activation = new Atu_activate();
$deactivation = new Atu_deactivate();