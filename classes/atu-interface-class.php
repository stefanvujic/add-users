<?php
class Atu_interface {

	function __construct() {
		add_action('admin_menu', array( $this, 'atu_admin_menu_item' ));
	}

	function atu_admin_menu_item() {
		add_options_page(
			'Test User Generator',
			'Test User Generator',
			'manage_options',
			'test-user-generator',
			array(
				$this,
				'atu_settings_page'
			)
		);
	}

	function atu_settings_page() {

		if (!current_user_can('manage_options'))  {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
	    if (isset($_POST['clear_all'])) {
	    	update_option('user_number', '');
	    }
	    
		//Check if loop went through all iterations, if yes display success message
		if ($_POST['number_of_users'] == $i && $i !== 0 && !isset($_POST['clear_all'])) {
			echo '<div style="color: green; position: absolute; top: 286px; left: 192px; font-weight: bold; margin-top: 30px;">';
				echo $user_count . ' Users successfully added';
			echo '</div>';
		}
		elseif ($_POST['number_of_users'] > 2001 && !isset($_POST['clear_all'])) {
			echo '<div style="color: red; position: absolute; top: 286px; left: 192px; font-weight: bold;">';
				echo '<p>ABORTED</p>';
				echo '<p class="bold red">Cannot generate more than 2000 users at once.</p>';
			echo '</div>';	
		}
		elseif ($_POST['number_of_users'] == 0 && !isset($_POST['clear_all']) && $_GET['page'] == 'wp-test-user_slug') {
			echo '<div style="color: red; position: absolute; top: 316px; left: 192px; font-weight: bold; margin-top: 30px;">';
				echo '<p class="bold red">Please select number of users</p>';
			echo '</div>';
		}	    

		$get_number_of_users = get_option('user_number');

		//Form
		echo '<div class="wrap">';
		?>
			<h1 style="padding-bottom: 30px;">Test User Generator</h1>

			<form class="generate" method="post">
				<div>
					Male <input type="checkbox" class="gender" name="gender" value="male" <?php if($_POST['gender'] == 'male' && !isset($_POST['clear_all'])){echo 'checked';} ?>>
					Female <input type="checkbox" class="gender" name="gender" value="female" <?php if($_POST['gender'] == 'female' && !isset($_POST['clear_all'])){echo 'checked';} ?>>
				</div>
				<br>
				<div>
					Hash Password <input type="checkbox" class="hash_pass" name="hash_pass" <?php if(isset($_POST['hash_pass']) && !isset($_POST['clear_all'])){echo 'checked';} ?>>
				</div>
				<!-- add comment under checkbox: Password will never be hashed in exported document -->
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
}