<?php
/*
Plugin Name: WP Test User  
Plugin URI: https://imstefan.co.uk/wp-test-user/
Description: Test User Generator
Version: 1.0
Author: Stefan Vujic
Author URI: https://imstefan.co.uk/
License: GPLv2 or later 

Navigation:
1. install/uninstall, activate, deactivate plugin
2. Admin interface
3. Generate User Name
4. Generate User Email
5. Generate User Password
6. Insert Users
7. Delete Users
8. Write Users To File
*/

// --- install/uninstall, activate, deactivate plugin ---- //
function test_user_activation() {
global $wpdb;

	$table_name = 'test_user_names';

	//check if table exists then if not, create it and insert data
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

		include 'male-first-names.php';
		include 'female-first-names.php';

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
	}	
}
register_activation_hook(__FILE__, 'test_user_activation');

function test_user_remove_database() {
global $wpdb;
    $table_name = 'test_user_names';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}
register_deactivation_hook(__FILE__, 'test_user_remove_database');


// --- Admin interface ---- //
function test_user_menu_item() {
	add_options_page('WP Test User', 'WP Test User', 'manage_options', 'wp-test-user_slug', 'test_user_interface');
}
add_action( 'admin_menu', 'test_user_menu_item' );

function test_user_interface() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die(__( 'You do not have sufficient permissions to access this page.'));
	}
    if (isset($_POST['clear_all'])) {
    	update_option('user_number', '');
    }

	$get_number_of_users = get_option('user_number');

	//Form
	echo '<div class="wrap">';
	?>
		<h1 style="padding-bottom: 30px;">WP Test User</h1>

		<form class="generate" method="post">
			<div>
				Male <input type="checkbox" class="gender" name="gender" value="male" <?php if($_POST['gender'] == 'male' && !isset($_POST['clear_all'])){echo 'checked';} ?>>
				Female <input type="checkbox" class="gender" name="gender" value="female" <?php if($_POST['gender'] == 'female' && !isset($_POST['clear_all'])){echo 'checked';} ?>>
			</div>
			<br>
			<div>
				Hash Password <input type="checkbox" class="hash_pass" name="hash_pass" <?php if(isset($_POST['hash_pass']) && !isset($_POST['clear_all'])){echo 'checked';} ?>>
			</div>
			<br>
			<div>
				Number Of Users <input style="width: 77px;" type="number" maxlength="2000" class="user_number" name="number_of_users" value="<?php if(!isset($_POST['clear_all'])){echo $_POST['number_of_users'];} ?>">
			</div>
			<br>
			<div>
				Export Users <input type="checkbox" class="export" name="export_users" <?php if(isset($_POST['export_users']) && !isset($_POST['clear_all'])){echo 'checked';} ?>>
			</div>
			<br>
			<input type="submit" value="Generate" class="generate_butt" name="generate_info">
			<input type="submit" value="Clear All" class="clear_all_butt" name="clear_all">
			<input type="submit" value="Delete All Created Users" class="delete_all_butt" name="delete_all_users">
		</form>
	<?php
	echo '</div>';
}


// --- Generate User Name ---- //
function generate_user_name() {
global $wpdb;

	//User Name
	if (isset($_POST['generate_info']) && $_POST['gender'] == 'male') {

		$get_male_names = $wpdb->get_results('SELECT user_name FROM test_user_names WHERE user_gender = "male" AND name_type = "firstname"');

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

		$get_female_names = $wpdb->get_results('SELECT user_name FROM test_user_names WHERE user_gender = "female" AND name_type = "firstname"');

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

		$get_all_names = $wpdb->get_results('SELECT user_name FROM test_user_names WHERE name_type = "firstname"');

		$all_names_array = array();
		foreach ($get_all_names as $all_name_key1 => $all_name1) {
			foreach ($all_name1 as $all_name_key2 => $all_name) {
				array_push($all_names_array, $all_name);
			}
		}
		$key = array_rand($all_names_array, 1);
		$no_gender_picked = $all_names_array[$key];	
	}

	return $random_male_name . $random_female_name . $no_gender_picked;
}


// --- Generate User Email ---- //
function generate_user_email($user_name) {
	return $user_name . '@' . strtolower($user_name) . 'test.com';
}


// --- Generate User Password ---- //
function generate_user_password($user_name) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    if ($_POST['hash_pass'] && $_POST['export_users']) {
    	return md5($user_name . '_' . implode($pass));
    }else {
    	return $user_name . '_' . implode($pass);
    }
}

// --- Insert Users ---- //
function insert_user($user_name, $user_email, $user_password) {
global $wpdb;

	if (isset($_POST['generate_info'])) {
		$user_email = generate_user_email($user_name);
		$user_password = generate_user_password($user_name);

		$wpdb->insert('wp_users', array(
		    'user_login'  	=>  $user_name,
		    'user_pass'     =>  $user_password,
		    'user_nicename' =>  $user_name,
		    'display_name'  =>  $user_name,
		    'user_email'    =>  $user_email
		));
	}			
}

if ($_POST['number_of_users'] < 2001) {

	$file_output = array();
	for ($i=0; $i < $_POST['number_of_users']; $i++) {

		$user_name = generate_user_name();
		$user_email = generate_user_email($user_name);
		$user_password = generate_user_password($user_name);

		// --- Write Users To File ---- //
		array_push($file_output, $i . '. user name: '.$user_name."\r\n".'   user email: '.$user_email."\r\n".'   user password: '.$user_password."\r\n \r\n");
		insert_user($user_name, $user_email, $user_password);
		$user_count++;
	}

	if(isset($_POST['export_users']) && isset($_POST['generate_info'])) {

		$timestamp = date("Y-m-d", time());

		file_put_contents('../wp-content/plugins/wp-test-user/wp-test-users_'.$timestamp.'.txt', $file_output);
		// Process download
		$filepath = '../wp-content/plugins/wp-test-user/wp-test-users_'.$timestamp.'.txt';
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
	if ($_POST['number_of_users'] == $i && $i !== 0 && !isset($_POST['clear_all'])) {
		echo '<div style="color: green; position: absolute; top: 371px; left: 192px; font-weight: bold; margin-top: 30px;">';
			echo $user_count . ' Users successfully added';
		echo '</div>';
	}
}else {
	echo '<div style="color: red; position: absolute; top: 371px; left: 192px; font-weight: bold;">';
		echo '<p>ABORTED</p>';
		echo '<p class="bold red">Cannot generate more than 2000 users at once.</p>';
	echo '</div>';	
}


// ---- Delete Users ---- //
function delete_created_users() {
global $wpdb;

	$get_all_first_names = $wpdb->get_results('SELECT user_name FROM test_user_names WHERE name_type = "firstname"');

	$user_id_array = array();
	foreach ($get_all_first_names as $first_name_key => $first_name) {
		$get_matched_user_id = $wpdb->get_results("SELECT ID FROM wp_users WHERE display_name = '$first_name->user_name'");
		if (!empty($get_matched_user_id[0]->ID)) {
			array_push($user_id_array, $get_matched_user_id[0]->ID);
		}
	}
	foreach ($user_id_array as $user_id_key => $user_id) {
		$wpdb->delete('wp_users', array('ID' => $user_id));
	}
}

//Run function 4 times to make sure db is cleaned properly
if (isset($_POST['delete_all_users'])) {
	for ($times=0; $times < 4; $times++) { 
		delete_created_users();
	}
	if ($times == 4) {
		echo '<div style="color: green; position: absolute; top: 371px; left: 192px; font-weight: bold; margin-top: 44px;">';
			echo '<p>All Test Users Successfully Deleted</p>';
		echo '</div>';
	}
}
?>