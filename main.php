<?php

/*
Plugin Name: Smart Custom Login
Plugin URI: http://www.gadepressen.dk/
Description: A nice custom login plugin
Tags: custom login, login, register
Version: 1.0.0
Author: Kjeld Hansen
Author URI: #
Requires at least: 4.0
Tested up to: 4.7.3
Text Domain: smart-custom-login
*/

//function prefix btwp

if ( ! defined( 'ABSPATH' ) ) exit; 
 

add_shortcode('scl-custom-login', 'scl_login_reg_fun');

function scl_login_reg_fun(){ 
	
	echo '
<div class="riquickContact">
	<p> </p>';
	if(is_user_logged_in()){
		$current_user = wp_get_current_user(); $redirect = '';
		echo '<div id="loged_in_scl">';
		
		echo 'Hi '.$current_user->display_name;
		
		 echo '<a href="'.wp_logout_url( $redirect ).'" style="float:right;" >Log Out</a>';
		
		echo '</div>';
	}else{
		echo scl_login_fun();
	
		scl_reg_fun();
	
	}
	
	echo '</div>';
}




function scl_login_fun(){ 
$resp='';
	
$scl_loginOp = '
<div class="sclwrapper login">
	<p> </p>';
	
	if(is_user_logged_in()){
		
	}else{
		$args = array(
			'echo'           => false,
			'remember'       => true,
			'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'form_id'        => 'loginform_scl',
			'id_username'    => 'user_login1',
			'id_password'    => 'user_pass1',
			'id_remember'    => 'rememberme1',
			'id_submit'      => 'wp-submit1',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Log In' ),
			'value_username' => '',
			'value_remember' => false
		);
		$scl_loginOp .= wp_login_form( $args );
		//$scl_loginOp .= scl_reg_fun();
	}
	
	
	$scl_loginOp .= $resp.'
</div>

';
/*<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript" src="'.plugins_url( '/spr.js', __FILE__).'"></script>*/


return $scl_loginOp;
}



function scl_reg_fun(){ 
	$scl_reg_form = '';
	$scl_reg_form .= '';
	//add_action( 'register_form', 'myplugin_register_form' );
?>
	<div class="sclwrapper regform">
	
	<?php
	$err = '';
	$success = '';
 
	global $wpdb, $PasswordHash, $current_user, $user_ID;
 if(wp_verify_nonce( $_REQUEST['_wpnonce'], 'scl_reg_user_')){
	if(isset($_POST['task']) && $_POST['task'] == 'register' ) {
 
		
		$pwd1 = $wpdb->escape(trim(sanitize_text_field($_POST['pwd1'])));
		$pwd2 = $wpdb->escape(trim(sanitize_text_field($_POST['pwd2'])));
		$first_name = $wpdb->escape(trim(sanitize_text_field($_POST['first_name'])));
		$last_name = $wpdb->escape(trim(sanitize_text_field($_POST['last_name'])));
		$email = $wpdb->escape(trim(sanitize_email($_POST['email'])));
		$username = $wpdb->escape(trim(sanitize_user($_POST['username'])));
		
		if( $email == "" || $pwd1 == "" || $pwd2 == "" || $username == "" || $first_name == "" || $last_name == "") {
			$err = 'Please don\'t leave the required fields.';
		} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$err = 'Invalid email address.';
		} else if(email_exists($email) ) {
			$err = 'Email already exist.';
		} else if($pwd1 <> $pwd2 ){
			$err = 'Password do not match.';		
		} else {
 
			$user_id = wp_insert_user( array ('first_name' => apply_filters('pre_user_first_name', $first_name), 'last_name' => apply_filters('pre_user_last_name', $last_name), 'user_pass' => apply_filters('pre_user_user_pass', $pwd1), 'user_login' => apply_filters('pre_user_user_login', $username), 'user_email' => apply_filters('pre_user_user_email', $email), 'role' => 'subscriber' ) );
			if( is_wp_error($user_id) ) {
				$err = 'Error on user creation.';
			} else {
				do_action('user_register', $user_id);
				
				$success = 'You\'re successfully register';
			}
			
		}
		
	}
	?>
 
        <!--display error/success message-->
	<div id="message">
		<?php 
			if(! empty($err) ) :
				echo '<p class="error">'.$err.'';
			endif;
		?>
		
		<?php 
			if(! empty($success) ) :
				echo '<p class="error">'.$success.'';
			endif;
		?>
	</div>
 <?php } ?>
	<form method="post" action="">
		<h3>Don't have an account?<br /> Create one now.</h3>
		<p><label>Last Name</label></p>
		<p><input type="text" value="" name="last_name" id="last_name" /></p>
		<p><label>First Name</label></p>
		<p><input type="text" value="" name="first_name" id="first_name" /></p>
		<p><label>Email</label></p>
		<p><input type="text" value="" name="email" id="email" /></p>
		<p><label>Username</label></p>
		<p><input type="text" value="" name="username" id="username" /></p>
		<p><label>Password</label></p>
		<p><input type="password" value="" name="pwd1" id="pwd1" /></p>
		<p><label>Password again</label></p>
		<p><input type="password" value="" name="pwd2" id="pwd2" /></p>
        <?php wp_nonce_field( 'scl_reg_user_' ); ?>
		<div class="alignleft"><p><?php if($sucess != "") { echo $sucess; } ?> <?php if($err != "") { echo $err; } ?></p></div>
		<button type="submit" name="btnregister" class="button" >Submit</button>
		<input type="hidden" name="task" value="register" />
	</form>
 
</div>
<?php
	return $scl_reg_form;
}



