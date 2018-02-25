<?php
/*
Template Name: Random Identity Generator
*/

global $wpdb;

//check if fields checked after posting
if ($_POST['gender'] === 'female') {
	$checked_female = 'checked';
}
if ($_POST['gender'] === 'male') {
	$checked_male = 'checked';
}
if ($_POST['age'] === 'young') {
	$checked_young = 'checked';
}
if ($_POST['age'] === 'middle') {
	$checked_middle = 'checked';
}
if ($_POST['age'] === 'old') {
	$checked_old = 'checked';
}

//names/gender
$second_names = array('Bronson', 'Stoke', 'Mayweather', 'Chelton', 'Smith');

require 'female-first-names.php';
require 'male-first-names.php';

//add names to db
// $columne = 'male_names';
// foreach ($local_male_names_array as $local_name_key => $local_name) {
	// $wpdb->insert( 'names', array($columne => $local_name), array('%s'));
// }
// $columne = 'female_names';
// foreach ($local_female_names_array as $local_name_key => $local_name) {
// 	$wpdb->insert( 'names', array($columne => $local_name), array('%s'));
// }

//get names
$male_names_obj = $wpdb->get_results('SELECT male_names FROM names');
$female_names_obj = $wpdb->get_results('SELECT female_names FROM names');

//putting sql array values into our own arrays for better structure
$male_names = array();
foreach ($male_names_obj as $male_name_key => $male_name_val) {
	foreach($male_name_val as $male_name_key2 => $male_name_val2) {
		if ($male_name_val2 !== '') {
			array_push($male_names, $male_name_val2);
		}
	}
}
$female_names = array();
foreach ($female_names_obj as $female_name_key => $female_name_val) {
	foreach($female_name_val as $female_name_key2 => $female_name_val2) {
		if ($female_name_val2 !== '') {
			array_push($female_names, $female_name_val2);
		}
	}
}
// echo "<pre>";
// print_r($male_name_key2);
// echo "<pre>";
//this returns one random index number from an array.
$random_female_first_name = array_rand($female_names, 1);
$random_male_first_name = array_rand($male_names, 1);
$random_second_name = array_rand($second_names, 1);
//merge male and female name arrays if gender not picked 
$male_and_female_names = array_merge($male_names, $female_names);
$random_male_and_female_names = array_rand($male_and_female_names, 1);

//set gender and name values
if ($_POST['generate_info'] && $_POST['gender'] && $_POST['gender'] === 'female') {
	$female_first_name = $female_names[$random_female_first_name];
}
else if ($_POST['generate_info'] && $_POST['gender'] && $_POST['gender'] === 'male') {
	$male_first_name =  $male_names[$random_male_first_name];
}
else if ($_POST['generate_info'] && !isset($_POST['gender'])) {
	$no_name_set =  $male_and_female_names[$random_male_and_female_names];
}

//set ages
$random_young_age = mt_rand(1,25);
$random_middle_age = mt_rand(25,50);
$random_old_age = mt_rand(50,110);
$random_no_age_picked = mt_rand(1,110);

if ($_POST['age'] === 'young') {
	$age = $random_young_age;
}
elseif ($_POST['age'] === 'middle') {
	$age = $random_middle_age;
}
elseif ($_POST['age'] === 'old') {
	$age = $random_old_age;
}
elseif (!isset($_POST['age'])) {
	$age = $random_no_age_picked;
}

//save user?
// if ($_POST('save')) {
// 	update_user_meta('', '');
// }
// if ($_POST['generate_info']) {
// 	$save_identity = '<form class="save" method="post"><input type="button" class="save_identity" value="save"></input></form>';
// }
?>

<form class="generate" method="post">
	<div>
		Male <input type="radio" class="gender" name="gender" value="male" <?php echo $checked_male; ?>>
		Female <input type="radio" class="gender" name="gender" value="female" <?php echo $checked_female; ?>>
	</div>
	<br>
	<div>
		Younger <input type="radio" class="younger" name="age" value="young" <?php echo $checked_young; ?>>
		Middle age <input type="radio" class="middle" name="age" value="middle" <?php echo $checked_middle ?>>
		Older <input type="radio" class="older" name="age" value="old"<?php echo $checked_old ?>>
	</div>
	<br>
	<input type="submit" value="generate" class="generate_butt" name="generate_info">
</form>

<!-- <?php echo $save_identity; ?> -->

<p>First Name: <?php echo $female_first_name; echo $male_first_name; echo $no_name_set; ?></p>
<p>Second Name: <?php echo $second_names[$random_second_name]; ?></p>

<p>Age: <?php echo $age; ?></p>

