<?php
class Atu_deactivate {
	
	function atu_test_user_deactivate() {
	global $wpdb;
	    $table_name = 'atu_test_user_names';
	    $sql = "DROP TABLE IF EXISTS $table_name";
	    $wpdb->query($sql);
	}
}