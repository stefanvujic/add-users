<?php
class Atu_delete_users {

	function atu_delete_created_users() {
	global $wpdb;

		$get_all_first_names = $wpdb->get_results('SELECT user_name FROM atu_test_user_names WHERE name_type = "firstname"');

		$user_id_array = array();
		foreach ($get_all_first_names as $first_name_key => $first_name) {
			$get_matched_user_id = $wpdb->get_results("SELECT ID FROM wp_users WHERE user_nicename = CONCAT('$first_name->user_name', '_*')");
			if (!empty($get_matched_user_id[0]->ID)) {
				array_push($user_id_array, $get_matched_user_id[0]->ID);
			}
		}
		foreach ($user_id_array as $user_id_key => $user_id) {
			$wpdb->delete('wp_users', array('ID' => $user_id));
		}
	}

	function atu_initiate_delete() {
		//Run function 4 times to make sure db is cleaned properly
		if (isset($_POST['delete_all_users'])) {
			for ($times=0; $times < 4; $times++) { 
				$this->atu_delete_created_users();
			}
			if ($times == 4) {
				echo '<div style="color: green; position: absolute; top: 286px; left: 192px; font-weight: bold; margin-top: 30px;">';
					echo '<p>All Test Users Successfully Deleted</p>';
				echo '</div>';
			}
		}		
	}
}