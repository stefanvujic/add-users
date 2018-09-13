<?php
class Atu_user_info_generator {

	function atu_generate_user_name() {
	global $wpdb;

		//User Name
		if (isset($_POST['generate_info']) && $_POST['gender'] == 'male') {

			$get_male_names = $wpdb->get_results('SELECT user_name FROM atu_test_user_names WHERE user_gender = "male" AND name_type = "firstname"');

			$male_names_array = array();
			foreach ($get_male_names as $male_name_key1 => $male_name1) {
				foreach ($male_name1 as $male_name_key2 => $male_name) {
					array_push($male_names_array, $male_name);
				}
			}
			$key = array_rand($male_names_array, 1);
			$random_male_name = $male_names_array[$key];
		}

		elseif (isset($_POST['generate_info']) && $_POST['gender'] == 'female') {

			$get_female_names = $wpdb->get_results('SELECT user_name FROM atu_test_user_names WHERE user_gender = "female" AND name_type = "firstname"');

			$female_names_array = array();
			foreach ($get_female_names as $female_name_key1 => $female_name1) {
				foreach ($female_name1 as $female_name_key2 => $female_name) {
					array_push($female_names_array, $female_name);
				}
			}
			$key = array_rand($female_names_array, 1);
			$random_female_name = $female_names_array[$key];	
		}

		elseif (isset($_POST['generate_info']) && !isset($_POST['gender'])) {

			$get_all_names = $wpdb->get_results('SELECT user_name FROM atu_test_user_names WHERE name_type = "firstname"');

			$all_names_array = array();
			foreach ($get_all_names as $all_name_key1 => $all_name1) {
				foreach ($all_name1 as $all_name_key2 => $all_name) {
					array_push($all_names_array, $all_name);
				}
			}
			$key = array_rand($all_names_array, 1);
			$no_gender_picked = $all_names_array[$key];	
		}

		if (isset($_POST['generate_info'])) {

			$get_surnames = $wpdb->get_results('SELECT user_name FROM atu_test_user_names WHERE name_type = "surname"');

			$surnames_array = array();
			foreach ($get_surnames as $surname_key1 => $surname1) {
				foreach ($surname1 as $surname_key2 => $random_surname) {
					array_push($surnames_array, $random_surname);
				}
			}
			$key = array_rand($surnames_array, 1);
			$surname = $surnames_array[$key];
		}

		$names = array();
		if (!empty($random_male_name)) {array_push($names, $random_male_name);}
		if (!empty($random_female_name)) {array_push($names, $random_female_name);}
		if (!empty($no_gender_picked)) {array_push($names, $no_gender_picked);}
		if (!empty($surname)) {array_push($names, $surname);}

		return $names;
	}

	function atu_generate_user_email($names) {
		return $names[0] . $names[1] . '@' . strtolower($names[0]) . '_' . strtolower($names[1]) . '_test.com';
	}

	function atu_generate_user_password($names) {
	    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    $pass = array();
	    $alphaLength = strlen($alphabet) - 1;
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    if ($_POST['hash_pass'] && !isset($_POST['export_users'])) {
	    	return md5($names[0] . $names[1] . '_' . implode($pass));
	    }else {
	    	return $names[0] . $names[1] . '_' . implode($pass);
	    }
	}

	function atu_insert_user($names) {
	global $wpdb;

		if (isset($_POST['generate_info'])) {

			$user_email = $this->atu_generate_user_email($names);
			$user_password = $this->atu_generate_user_password($names);

			$wpdb->insert('wp_users', array(
			    'user_login'  	=>  $names[0],
			    'user_pass'     =>  $user_password,
			    'user_nicename' =>  $names[0] . '_*',
			    'display_name'  =>  $names[0] . ' ' . $names[1],
			    'user_email'    =>  $user_email
			));
		}			
	}

	function atu_export_users($file_output) {
		if(isset($_POST['export_users']) && isset($_POST['generate_info'])) {

			$timestamp = date("Y-m-d", time());

			file_put_contents('../wp-content/plugins/add-users/exports/user_export_'.$timestamp.'.txt', $file_output);

			$filepath = '../wp-content/plugins/add-users/exports/user_export_'.$timestamp.'.txt';
		    if(file_exists($filepath)) {
		        header('Content-Description: Wp Uest User Output');
		        header('Content-Type: application/octet-stream');
		        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
		        header('Expires: 0');
		        header('Cache-Control: must-revalidate');
		        header('Pragma: public');
		        flush();
		        readfile($filepath);
		        exit;
		    }
		}
	}	

	function atu_generate_users() {
		if ($_POST['number_of_users'] < 2001) {

			//$file_output array is for user export
			$file_output = array();
			for ($i=0; $i < $_POST['number_of_users']; $i++) {

				$names = $this->atu_generate_user_name();
				$user_email = $this->atu_generate_user_email($names);
				$user_password = $this->atu_generate_user_password($names);

				array_push($file_output, $i . '. user name: '. $names[0] . '_' . $names[1] ."\r\n".'   user email: ' . $user_email . "\r\n".'   user password: '. $user_password ."\r\n \r\n");

				$this->atu_insert_user($names);
			}		
		}
		$this->atu_export_users($file_output);
	}	
}