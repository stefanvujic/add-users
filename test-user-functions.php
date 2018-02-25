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
6. Insert User
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

	$get_male_or_female  = get_option('male_or_female');
	$get_number_of_users = get_option('user_number');

	//Form
	echo '<div class="wrap">';
	?>
		<h1 style="padding-bottom: 30px;">WP Test User</h1>

		<form class="generate" method="post">
			<div>
				Male <input type="radio" class="gender" name="gender" value="male" <?php if($get_male_or_female == 'male' || $_POST['gender'] == 'male'){echo 'checked';} ?>>
				Female <input type="radio" class="gender" name="gender" value="female" <?php if($get_male_or_female == 'female' || $_POST['gender'] == 'female'){echo 'checked';} ?>>
			</div>
			<br>
			<div>
				Number Of Users <input type="number" class="user_number" name="number_of_users" value="<?php if(!isset($_POST['number_of_users'])){echo $get_number_of_users;} else{echo $_POST['number_of_users'];} ?>">
			</div>
			<br>
			<input type="submit" value="generate" class="generate_butt" name="generate_info">
		</form>
	<?php
	echo '</div>';

	//Update options
	if ($_POST['gender'] == 'male') {
		update_option('male_or_female', 'male');
	}
	elseif ($_POST['gender'] == 'female') {
		update_option('male_or_female', 'female');
	}
	update_option('user_number', $_POST['number_of_users']);
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
	return $random_male_name . $random_female_name;
}
$user_name = generate_user_name();


// --- Generate User Email ---- //
function generate_user_email($user_name) {
	return $user_name . '@' . strtolower($user_name) . 'test.com';
}


// --- Generate User Password ---- // //does user want hashed passwords or not, implement functionality later
function generate_user_password($user_name) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return $user_name . '_' . implode($pass);
}


// --- Insert User ---- //
function insert_user($user_name, $user_email, $user_password) {
global $wpdb;

	if (isset($_POST['generate_info'])) {
		$user_email = generate_user_email($user_name);
		$user_password = generate_user_password($user_name);

		$wpdb->insert('wp_users', array(
		    'user_login'  =>  $user_name,
		    'user_pass'   =>  $user_password,
		    'user_nicename' => $user_name,
		    'display_name' => $user_name,
		    'user_email' => $user_email
		));
	}		
}
insert_user($user_name, generate_user_email($user_name), generate_user_password($user_name));
?>