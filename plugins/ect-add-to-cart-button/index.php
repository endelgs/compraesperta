<?php 
/*
	Plugin Name:ECT Buy Button
	
	Description:This plugin will generate shortcode for the ECT add to cart buttons
	
	Author:Andy Chapman
	
	Author URI:http://www.ecommercetemplates.com
	
	Version:1.0
*/

add_action('admin_menu','ad_nav_ec');
function ad_nav_ec()
{
	add_menu_page('ECT Buy Button','ECT Buy Button','manage_options','ec','ec_fun',plugin_dir_url(__FILE__).'img/ect28x28.png',989);
	add_submenu_page('ec','Add New','Add New','manage_options','add_ect','add_ect');
	/*add_submenu_page('ec','Settings','Settings','manage_options','set_ec','set_ec_fun');*/
}
function add_ect()
{
	require_once 'add_ect_button.php';
}
function set_ec_fun()
{
	echo '<h2>Settings</h2>';
	if(isset($_GET['msg']) && $_GET['msg']==1)
		echo '<h4>Setttings saved !</h4>';
	echo '<form method="post" enctype="multipart/form-data">
		<ul>
			<li><input type="file" name="add_to_cart" /></li>
			<li><img src="'.site_url('/').'wp-content/'.get_option('ec_cart_img').'" id="img"/></li>
			<li><input type="submit" value="Upload" /></li>
		</ul>
	</form>';
	if(!empty($_FILES['add_to_cart']['name']))
	{
		global $wpdb;
		if(!empty($_FILES))
		{
			$FName=time.'_'.$_FILES["add_to_cart"]["name"];
			$upload = wp_upload_bits($FName, null, file_get_contents($_FILES["add_to_cart"]["tmp_name"]));
			$upload_dir = wp_upload_dir();
			$img='uploads'.$upload_dir['subdir'].'/'.$FName;
			update_option('ec_cart_img',$img);
			echo '<script type="text/javascript">window.location="admin.php?page=set_ec&msg=1"</script>';
		}
	}
}
function ec_fun()
{
	require_once 'ect_list.php';
}
add_shortcode('view_add_to_cart','cart_fun_ec');
function cart_fun_ec($atts)
{
	return stripslashes(get_option('shortcode_ec_'.$atts['id']));
}

//echo do_shortcode('[view_add_to_cart id=pc002]');
?>