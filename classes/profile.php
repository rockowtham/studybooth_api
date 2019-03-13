<?php
/*----- 	Profile Starts Here	 -----*/
add_action( 'rest_api_init', 'carspotAPI_profile_api_update_img', 0 );
function carspotAPI_profile_api_update_img() {
    register_rest_route(
        		'carspot/v1', '/profile/image/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_profile_update_img',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
} 

function my_custom_upload_mimes_here($mimes = array()) {

	// Add a key and value for the CSV file type
	$mimes['image'] = "image/jpeg";

	return $mimes;
}

add_action('upload_mimes', 'my_custom_upload_mimes_here');

if (!function_exists('carspotAPI_profile_update_img'))
{
	function carspotAPI_profile_update_img( $request )
	{ 
						
		$user    = wp_get_current_user();	
		$user_id = @$user->data->ID;
		
		
		if($user){
		
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';					

		define('ALLOW_UNFILTERED_UPLOADS', true);
		
		$attach_id = media_handle_upload( 'profile_img', 0 );	
			
		/******* Assign image to user ************/

		if ( is_wp_error( $attach_id ) ) {
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Something went wrong while uploading image.", "carspot-rest-api"),);
		} else {
			

			update_user_meta($user_id, '_sb_user_pic', $attach_id );
			$image_link = wp_get_attachment_image_src( $attach_id, 'carspot-user-profile' );

			$profile_arr = array();
			$profile_arr['id']				= $user->ID;
			$profile_arr['user_email']		= $user->user_email;
			$profile_arr['display_name']	= $user->display_name;
			$profile_arr['phone']			= get_user_meta($user->ID, '_sb_contact', true );
			$profile_arr['profile_img']		= $image_link[0];			
					
			$response = array( 'success' => true, 'data' => $profile_arr , 'message' => __("Profile image updated successfully", "carspot-rest-api"));				
			
		}
		}
		else
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("You must be login to update the profile image.", "carspot-rest-api"), "extra" => '' );
		}
		
		return $response;
	}
}

if (!function_exists('carspotAPI_profile_update_img11'))
{
	function carspotAPI_profile_update_img11( $request )
	{ 
		$user    =  wp_get_current_user();	
		$user_id =  $user->data->ID;
		//if ( ! function_exists( 'wp_handle_upload' ) ){
			 require_once ABSPATH . 'wp-admin/includes/image.php';
			 require_once ABSPATH . 'wp-admin/includes/file.php';
			 require_once ABSPATH . 'wp-admin/includes/media.php';					
		//}
		$uploadedfile = $_FILES['profile_img'];
		/******* user_photo Upload code ************/
		$upload_overrides = array( 'test_form' => false );
		$movefile = media_handle_upload( $uploadedfile, $upload_overrides );

		/******* Assign image to user ************/
		$filename 		= $movefile['url'];
		$absolute_file	= $movefile['file'];
		
		$extraData = wp_read_image_metadata( $filename );
		
		$parent_post_id = 0;
		$filetype = wp_check_filetype( basename( $filename ), null );
		$wp_upload_dir = wp_upload_dir();
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		/*Insert the attachment.*/
		$attach_id = wp_insert_attachment( $attachment, $absolute_file, $parent_post_id );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $absolute_file );
		//$attach_data = wp_get_attachment_image( $attach_id );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		set_post_thumbnail( $parent_post_id, $attach_id );
		update_user_meta($user_id, '_sb_user_pic', $attach_id );
		
		$idata['profile_img'] = $movefile['url'];
		$response = array( 'success' => true, 'data' => $idata , 'message' => __("Profile image updated successfully", "carspot-rest-api"), "extraData" => $extraData );
		return $response;
	}
}
/*Edit Profile */
add_action( 'rest_api_init', 'carspotAPI_profile_api_ads_hooks_post', 0 );
function carspotAPI_profile_api_ads_hooks_post() {

    register_rest_route(
			'carspot/v1', '/profile/', array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_profile_post',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
}  
 
if (!function_exists('carspotAPI_profile_post'))
{
	function carspotAPI_profile_post( $request )
	{ 

		$user               = wp_get_current_user();	
		$user_id            = $user->data->ID;
		$json_data			= $request->get_json_params();			
		$name				= (isset($json_data['user_name'])) 			? trim($json_data['user_name']) : '';
		$location			= (isset($json_data['location'])) 			? trim($json_data['location']) : '';
		$phone				= (isset($json_data['phone_number'])) 		? trim($json_data['phone_number']) : '';
		$accountType 		= (isset($json_data['account_type'])) 		? trim($json_data['account_type']) : '';
		$user_introduction 	= (isset($json_data['user_introduction'])) 	? trim($json_data['user_introduction']) : '';

		if( $name == "" )
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Please enter name.", "carspot-rest-api") );
			return $response;
		}			
				
		if( $phone == "" )
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Please enter phone number.", "carspot-rest-api") );
			return $response;
		}				
		
					
		$saved_ph = get_user_meta( $user_id, '_sb_contact', true );
		
		if( $saved_ph != $phone )
		{
			update_user_meta( $user_id, '_sb_is_ph_verified', '0' );
		}	
		if( $name != "" )
		{
			$user_name = wp_update_user( array( 'ID' => $user_id, 'display_name' => $name ) );
			
		} 	
		if( $phone != "" )
		{
			update_user_meta( $user_id, '_sb_contact', $phone );
		}
	
		if( $accountType != "" )
			update_user_meta( $user_id, '_sb_user_type', $accountType );

		if( $location != "" )
			update_user_meta( $user_id, '_sb_address', $location );
				
		
		
		
		//update user info here
		update_user_meta( $user_id, '_sb_user_intro', $user_introduction );
		
		/*Social profile Starts */
		$social_profiles = carspotAPI_social_profiles();
		if( isset( $social_profiles ) && count($social_profiles) > 0 )
		{
			foreach( $social_profiles as $key => $val )
			{
				$keyName = '';
				$keyName = "_sb_profile_".$key;
				/*$keyVal  = get_user_meta( $user->ID, $keyName, true );*/
				$social	= (isset($json_data['social_icons'][$keyName])) 		? trim($json_data['social_icons'][$keyName]) : '';				
				update_user_meta( $user_id, $keyName, sanitize_textarea_field($social) );
			}
		}
		
		/*Social Profile Ends */
		$data = carspotAPI_basic_profile_data( $user_id );
			
		$page_title['page_title'] = __("Edit Profile", "carspot-rest-api");
		$response = array( 'success' => true, 'data' => $data , 'message' => __("Profile Updated.", "carspot-rest-api"), 'page_title' => $page_title);
		return $response;			
	}
}
/*Edit Profile Ends */
add_action( 'rest_api_init', 'carspotAPI_profile_reset_pass_hooks_post', 0 );
function carspotAPI_profile_reset_pass_hooks_post() {

    register_rest_route(
			'carspot/v1', '/profile/reset_pass/', array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_profile_reset_pass_post',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
	
	
	register_rest_route(
			'carspot/v1', '/profile/reset_pass/', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'carspotAPI_reset_password_get',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
	
	
	
}  
 
if (!function_exists('carspotAPI_profile_reset_pass_post'))
{
	function carspotAPI_profile_reset_pass_post( $request )
	{ 

		if( CARSPOT_API_ALLOW_EDITING == false )
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Editing Not Allowed In Demo", "carspot-rest-api") );
			return $response;
		}		

		$json_data		= $request->get_json_params();	
		$old_pass		= (isset($json_data['old_pass'])) 				? trim($json_data['old_pass']) : '';
		$new_pass		= (isset($json_data['new_pass'])) 			    ? trim($json_data['new_pass']) : '';
		$new_pass_con	= (isset($json_data['new_pass_con'])) 			? trim($json_data['new_pass_con']) : '';

		if( $old_pass == "" )
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Please enter current password", "carspot-rest-api") );
			return $response;
		}
		if( $new_pass == "" )
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Please enter new password", "carspot-rest-api") );
			return $response;
		}	

		if( $new_pass != $new_pass_con )
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Password confirm password mismatched", "carspot-rest-api") );
			return $response;
		}			
			
		$user = get_user_by( 'ID', get_current_user_id() );
		if( $user && wp_check_password( $old_pass, $user->data->user_pass, $user->ID) )
		{
			wp_set_password( $new_pass, $user->ID );
			$response = array( 'success' => true, 'data' => '' , 'message' => __("Password successfully chnaged", "carspot-rest-api") );
			return $response;

		}
		else
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Invalid old password", "carspot-rest-api") );
			return $response;
		}
		
		die();		
	
	}
	
}


add_action( 'rest_api_init', 'carspotAPI_profile_forgot_pass_hooks_post', 0 );
function carspotAPI_profile_forgot_pass_hooks_post() {

    register_rest_route(
			'carspot/v1', '/profile/forgot_pass/', array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_profile_forgot_pass_post',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
}  
 
if (!function_exists('carspotAPI_profile_forgot_pass_post'))
{
	function carspotAPI_profile_forgot_pass_post( $request )
	{ 
		$user     = wp_get_current_user();	
		$user_id = $user->data->ID;	

		$json_data		= $request->get_json_params();	
		$old_pass		= (isset($json_data['old_pass']))  ? trim($json_data['old_pass']) : '';
		
	}
}


/*API custom endpoints for WP-REST API*/

add_action( 'rest_api_init', 'carspotAPI_profile_api_hooks_get', 0 );
function carspotAPI_profile_api_hooks_get() {
    register_rest_route(
		'carspot/v1', '/profile/', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'carspotAPI_myProfile_get',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
	
	 register_rest_route(
		'carspot/v1', '/profile_ads/', array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_myProfile_get',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
}


if (!function_exists('carspotAPI_myProfile_get'))
{
function carspotAPI_myProfile_get($request)
{

global $carspotAPI;
$json_data			= $request->get_json_params();
$paged              = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';

$user = wp_get_current_user();
$profile_arr['id']				= $user->ID;



$profile_arr['user_email']		= array("key" => __("Email", "carspot-rest-api"), "value" => $user->user_email, "placeholder" => __("Enter Email", "carspot-rest-api"), "field_name" => "user_email");




$profile_arr['display_name']	= array("key" => __("Name", "carspot-rest-api"), "value" => $user->display_name,  "placeholder" => __("Enter Name", "carspot-rest-api"),"field_name" => "user_name");



$profile_arr['phone']			= array("key" => __("Phone Number", "carspot-rest-api"), "value" => get_user_meta($user->ID, '_sb_contact', true ),"placeholder" => __("Enter phone number", "carspot-rest-api"), "field_name" => "phone_number");



$social_profiles = carspotAPI_social_profiles();
$profile_arr['is_show_social'] = false;
if( isset( $social_profiles ) && count($social_profiles) > 0 )
{
	$profile_arr['is_show_social'] = true;
	foreach( $social_profiles as $key => $val )
	{
		$keyName = '';
		$keyName = "_sb_profile_".$key;
		$keyVal  = get_user_meta( $user->ID, $keyName, true );
		$keyVal  = ( $keyVal ) ? $keyVal : '';
		$profile_arr['social_icons'][] 	= array("key" => $val, "value" => $keyVal, "field_name" => $keyName);
	}
}

$sb_user_type_text = '';

$package_type	=	get_user_meta( $user->ID, '_sb_pkg_type', true );
$package_type   =   ( $package_type == 'free' || $package_type == "") ? __('Free', 'carspot-rest-api' ) : __('Paid', 'carspot-rest-api' );	

$profile_arr['package_type'] 	= array("key" => __("Package Type", "carspot-rest-api"), "value" => $package_type, "field_name" => "package_type");		
$profile_arr['account_type'] 	= array("key" => __("Account Type", "carspot-rest-api"), "value" => $sb_user_type_text, "field_name" => "account_type");
$profile_arr['location'] 		= array("key" => __("Location", "carspot-rest-api"), "value" => get_user_meta( $user->ID, '_sb_address', true), "field_name" => "location");	

$profile_arr['profile_img']		= array("key" => __("Image", "carspot-rest-api"), "value" => carspotAPI_user_dp( $user->ID), "field_name" => "profile_img");	

$sb_expire_ads =  get_user_meta( $user->ID, '_carspot_expire_ads', true );
$expiery_date  = ( $sb_expire_ads != '-1' ) ? $sb_expire_ads : __("Never", "carspot-rest-api");


$profile_arr['expire_date'] = array("key" => __("Expire Date", "carspot-rest-api"), "value" => $expiery_date, "field_name" => "expire_date");

$profile_arr['blocked_users_show'] = (isset( $carspotAPI['sb_user_allow_block'] ) && $carspotAPI['sb_user_allow_block']) ? true : false;
$profile_arr['blocked_users'] = array("key" => __("Blocked Users", "carspot-rest-api"), "value" => __("Click Here", "carspot-rest-api"), "field_name" => "blocked_users");		

$sb_simple_ads = get_user_meta( $user->ID, '_sb_simple_ads', true );
$sb_simple_ads = ( $sb_simple_ads != "" ) ? $sb_simple_ads : 0;
$sb_simple_ads  = ( $sb_simple_ads >= 0 ) ? $sb_simple_ads : __("Unlimited", "carspot-rest-api");
$profile_arr['simple_ads'] = array("key" => __("Simple Ads", "carspot-rest-api"), "value" => $sb_simple_ads, "field_name" => "simple_ads");

$sb_featured_ads  = get_user_meta( $user->ID, '_carspot_featured_ads', true );
$sb_featured_ads  = ( $sb_featured_ads != "" ) ? $sb_featured_ads : 0;
$sb_featured_ads  = ( $sb_featured_ads >= 0 ) ? $sb_featured_ads : __("Unlimited", "carspot-rest-api");

$profile_arr['featured_ads'] = array("key" => __("Featured Ads", "carspot-rest-api"), "value" => $sb_featured_ads, "field_name" => "featured_ads");

$sb_bump_ads = get_user_meta( $user->ID, '_carspot_bump_ads', true );
$sb_bump_ads = ( $sb_bump_ads != "" ) ? $sb_bump_ads : 0;
$sb_bump_ads  = ( $sb_bump_ads >= 0 ) ? $sb_bump_ads : __("Unlimited", "carspot-rest-api");

$bump_ad_is_show = false;
if( isset( $carspotAPI['sb_allow_free_bump_up']	) && $carspotAPI['sb_allow_free_bump_up'] == true )
{
	$bump_ad_is_show = true;
}
else if( isset( $carspotAPI['sb_allow_bump_ads']	) && $carspotAPI['sb_allow_bump_ads'] == true )
{
	$bump_ad_is_show = true;
}	
	$profile_arr['bump_ads_is_show'] = $bump_ad_is_show;
	$profile_arr['bump_ads']         = array("key" => __("Bump Ads", "carspot-rest-api"), "value" => $sb_bump_ads, "field_name" => "bump_ads");		


	 $profile_arr['profile_extra']= carspotAPI_basic_profile_data();
	 $profile_arr['active_add']   = carspotApi_userAds($user->ID, '', '', $paged);
	 $profile_arr['inactive_add'] = carspotApi_userAds($user->ID, 'active', '', $paged, 'pending');
	 $profile_arr['expire_add']   = carspotApi_userAds($user->ID, 'expired', '', $paged);
	 $profile_arr['sold_add']     = carspotApi_userAds($user->ID, 'sold', '', $paged);
	 $profile_arr['featured_add'] = carspotApi_userAds($user->ID, 'active', '1', $paged);
	 $profile_arr['favourite_add']= carspotApi_userAds_fav($user->ID, '', '', $paged);
	
	

	
	
	
	$extra_arr['profile_title'] 	= __("My Profile", "carspot-rest-api");
	$extra_arr['ads_title']     	= __("Ads", "carspot-rest-api");
	$extra_arr['my_title']      	= __("My", "carspot-rest-api");
	$extra_arr['active_title']  	= __("Active", "carspot-rest-api");
	$extra_arr['inactive_title']  	= __("Inactive", "carspot-rest-api");
	$extra_arr['feature_title'] 	= __("Featured", "carspot-rest-api");
	$extra_arr['sold_title'] 		= __("Sold", "carspot-rest-api");
	$extra_arr['fav_title'] 		= __("Favourite", "carspot-rest-api");
	
	
	$extra_arr['status_text']       = carspotAPI_user_ad_strings();
	
	$extra_arr['profile_edit_title'] = __("Edit Profile", "carspot-rest-api");
	$extra_arr['save_btn'] = __("Update", "carspot-rest-api");
	$extra_arr['cancel_btn'] = __("Cancel", "carspot-rest-api");
	$extra_arr['select_image'] = __("Select Image", "carspot-rest-api");
	$extra_arr['change_pass']['heading'] = __("Forgot your password", "carspot-rest-api");
	$extra_arr['change_pass']['title'] = __("Change Password?", "carspot-rest-api");
	$extra_arr['change_pass']['old_pass'] = __("Old Password", "carspot-rest-api");
	$extra_arr['change_pass']['new_pass'] = __("New Password", "carspot-rest-api");
	$extra_arr['change_pass']['new_pass_con'] = __("Confirm New Password", "carspot-rest-api");
	$extra_arr['change_pass']['err_pass'] = __("Password Not Matched", "carspot-rest-api");

	$extra_arr['select_pic']['title'] = __("Add Photo!", "carspot-rest-api");
	$extra_arr['select_pic']['camera'] = __("Take Photo", "carspot-rest-api");
	$extra_arr['select_pic']['library'] = __("Choose From Gallery", "carspot-rest-api");
	$extra_arr['select_pic']['cancel'] = __("Cancel", "carspot-rest-api");
	$extra_arr['select_pic']['no_camera'] = __("camera Not Available", "carspot-rest-api");

	$profile_arr['page_title'] = __("My Profile", "carspot-rest-api");
	$profile_arr['page_title_edit'] = __("Edit Profile", "carspot-rest-api");
	$is_verification_on = false;
	if( isset( $carspotAPI['sb_phone_verification']	) && $carspotAPI['sb_phone_verification'] == true )
	{
		$is_verification_on = true;
		$number_verified = get_user_meta( $user->ID, '_sb_is_ph_verified', '1' );
		$number_verified_text = ( $number_verified && $number_verified == 1 ) ? __("verified", "carspot-rest-api") : __("Not verified", "carspot-rest-api");
		$extra_arr['is_number_verified'] = ( $number_verified && $number_verified == 1 ) ? true : false;
		$extra_arr['is_number_verified_text'] = $number_verified_text;
		
		$extra_arr['phone_dialog'] = array(
				"text_field" => __("Verify Your Code", "carspot-rest-api"),
				"btn_cancel" => __("Cancel", "carspot-rest-api"),
				"btn_confirm" => __("Confirm", "carspot-rest-api"),
				"btn_resend" => __("Resend", "carspot-rest-api"),
			);
			
			
		$extra_arr['send_sms_dialog'] = array(
				"title" => __("Confirmation", "carspot-rest-api"),
				"text" =>__("Send SMS verification code.", "carspot-rest-api"),
				"btn_send" =>__("Send", "carspot-rest-api"),
				"btn_cancel" =>__("Cancel.", "carspot-rest-api"),
			);
		
	}
	
	
	$extra_arr['is_verification_on'] = $is_verification_on;

	$delete_profile = (isset($carspotAPI['sb_new_user_delete_option']) && $carspotAPI['sb_new_user_delete_option'] ) ? true : false;
	$profile_arr['can_delete_account'] = $delete_profile;
	if( $delete_profile )
	{
		$profile_arr['delete_account']['text']	= __("Delete Account?", "carspot-rest-api");
		
		$delete_profile_text = (isset($carspotAPI['sb_new_user_delete_option_text']) && $carspotAPI['sb_new_user_delete_option_text'] != "" ) ? 					$carspotAPI['sb_new_user_delete_option_text'] :  __("Are you sure you want to delete the account.", "carspot-rest-api");
		
		$profile_arr['delete_account']['popuptext']	    = $delete_profile_text;
		$profile_arr['delete_account']['btn_cancel']	= __("Cancel", "carspot-rest-api");
		$profile_arr['delete_account']['btn_submit']	= __("Confirm", "carspot-rest-api");
	}

	$response = array( 'success' => true, 'data' => $profile_arr, "message" => "" , "extra_text" => $extra_arr);	
	return $response;	
}
}



/*Public profile starts */
add_action( 'rest_api_init', 'carspotAPI_userPublicProfile_hooks_get', 0 );
function carspotAPI_userPublicProfile_hooks_get() {
    register_rest_route(
		'carspot/v1', '/profile/public/', array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_userPublicProfile_get',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
}
if (!function_exists('carspotAPI_userPublicProfile_get'))
{
	function carspotAPI_userPublicProfile_get($request)
	{
	
		$json_data =  $request->get_json_params();
		$user_id   =  (isset( $json_data['user_id'] ) ) ?  $json_data['user_id'] : '';
		$user      =  get_userdata( $user_id );	
		
		
		if(!$user )
		{
			$response = array( 'success' => false, 'data' => '', "message" => __("User doest not exists", "carspot-rest-api"));	
			return $response;	
		}
		
		$profile_arr['id']              	= $user->ID;		
		$paged 				            	= (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';
		$adsData 			            	= carspotApi_userAds($user->ID, '', '', $paged);
		$profile_arr['ads'] 	 	    	= $adsData['ads'];
		$profile_arr['pagination'] 	 		= $adsData['pagination'];		
		$profile_arr['text']['ad_type'] 	= 'myads';
		$profile_arr['text']['editable'] 	= '0';
		$profile_arr['text']['show_dropdown'] = '0';

		$message = (count($profile_arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		$user_intro = ( get_user_meta( $user->ID, '_sb_user_intro', true ) );
		$profile_arr['introduction']			= array("key" => __("Introduction", "carspot-rest-api"), "value" => $user_intro, "field_name" => "user_introduction");
	
		$social_profiles = carspotAPI_social_profiles();
		$profile_arr['is_show_social'] = false;
		if( isset( $social_profiles ) && count($social_profiles) > 0 )
		{
			$profile_arr['is_show_social'] = true;
			foreach( $social_profiles as $key => $val )
			{
				$keyName = '';
				$keyName = "_sb_profile_".$key;
				$keyVal  = get_user_meta( $user->ID, $keyName, true );
				$keyVal  = ( $keyVal ) ? $keyVal : '';
				
				
				$profile_arr['social_icons'][] 	= array("key" => $val, "value" => $keyVal, "field_name" => $keyName);
			}
		}
		
		
		$profile_arr['profile_extra'] = carspotAPI_basic_profile_data( $user_id );		
		$profile_arr['page_title'] = __("User Profile", "carspot-rest-api");
			
		$response = array( 'success' => true, 'data' => $profile_arr, "message" => $message);	
		return $response;	
	}
}

/*Public profile ends */

add_action( 'rest_api_init', 'carspotAPI_user_ads_get', 0 );
function carspotAPI_user_ads_get() {
	
	/*Routs*/
    register_rest_route(
        		'carspot/v1', '/ad/', array( 'methods'  => WP_REST_Server::READABLE, 'callback' => 'carspotAPI_ad_all_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	));	
    register_rest_route(
        		'carspot/v1', '/ad/', array( 'methods'  => WP_REST_Server::EDITABLE, 'callback' => 'carspotAPI_ad_all_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	));				
	
	/*Routs*/
    register_rest_route(
        		'carspot/v1', '/ad/active/', array( 'methods'  => WP_REST_Server::READABLE, 'callback' => 'carspotAPI_ad_active_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	));
    register_rest_route(
        		'carspot/v1', '/ad/active/', array( 'methods'  => WP_REST_Server::EDITABLE, 'callback' => 'carspotAPI_ad_active_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	));			
	/*Routs*/
    register_rest_route(
		'carspot/v1', '/ad/expired/', array( 'methods'  => WP_REST_Server::READABLE, 'callback' => 'carspotAPI_ad_expired_get',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));
    register_rest_route(
		'carspot/v1', '/ad/expired/', array( 'methods'  => WP_REST_Server::EDITABLE, 'callback' => 'carspotAPI_ad_expired_get',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));	
	/*Routs*/
    register_rest_route(
		'carspot/v1', '/ad/sold/', array( 'methods'  => WP_REST_Server::READABLE, 'callback' => 'carspotAPI_ad_sold_get',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));	
    register_rest_route(
		'carspot/v1', '/ad/sold/', array( 'methods'  => WP_REST_Server::EDITABLE, 'callback' => 'carspotAPI_ad_sold_get',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));		
	/*Routs*/
    register_rest_route(
		'carspot/v1', '/ad/featured/', array( 'methods'  => WP_REST_Server::READABLE, 'callback' => 'carspotAPI_ad_featured_get',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));	
    register_rest_route(
		'carspot/v1', '/ad/featured/', array( 'methods'  => WP_REST_Server::EDITABLE, 'callback' => 'carspotAPI_ad_featured_get',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));				
	/*Routs*/
    register_rest_route(
		'carspot/v1', '/ad/inactive/', array( 'methods'  => WP_REST_Server::READABLE, 'callback' => 'carspotAPI_ad_inactive_get',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));	
    register_rest_route(
		'carspot/v1', '/ad/inactive/', array( 'methods'  => WP_REST_Server::EDITABLE, 'callback' => 'carspotAPI_ad_inactive_get',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));				
	/*Routs*/
    register_rest_route(
		'carspot/v1', '/ad/update/status/', array( 'methods'  => WP_REST_Server::EDITABLE, 'callback' => 'carspotAPI_change_user_ad_status',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
     ));
	 
	/*Routs*/
    register_rest_route(
		'carspot/v1', '/ad/delete/', array( 
			'methods'  => WP_REST_Server::DELETABLE,
			'callback' => 'carspotAPI_change_user_ad_delete',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
			'args'     => array( 'force' => array( 'default' => true, ), ),
     ));
    register_rest_route(
		'carspot/v1', '/ad/delete/', array( 
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_change_user_ad_delete',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
			'args'     => array( 'force' => array( 'default' => true, ), ),
     ));	 	 										
	
	/* favourite */
    register_rest_route(
		'carspot/v1', '/ad/favourite/', array( 
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'carspotAPI_user_ad_favourite',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
     ));
    register_rest_route(
		'carspot/v1', '/ad/favourite/', array( 
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_user_ad_favourite',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
     ));	 
	/* favourite remove*/
    register_rest_route(
		'carspot/v1', '/ad/favourite/remove', array( 
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_user_ad_favourite_remove',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
     ));
}

/*Active Ads*/
if (!function_exists('carspotAPI_ad_all_get'))
{
	function carspotAPI_ad_all_get($request)
	{

		$json_data = $request->get_json_params();
		$paged = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';
				
		$userID 	            = wp_get_current_user();
		$adsData                = carspotApi_userAds($userID->ID, '', '', $paged);
		$arr['ads'] 	 	    = $adsData['ads'];
		$arr['pagination'] 	 	= $adsData['pagination'];		
		
		$arr['page_title']	= __( 'My Ads','carspot-rest-api');
		$arr['text'] = carspotAPI_user_ad_strings();
		$arr['text']['ad_type'] = 'myads';
		$arr['text']['editable'] = '1';
		$arr['text']['show_dropdown'] = '1';
		$arr['profile'] = carspotAPI_basic_profile_data();
		$message = (count($arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		$response = array( 'success' => true, 'data' => $arr, 'message' => $message );	
		return $response;	
	}
	
}

/*inActive Ads*/
if (!function_exists('carspotAPI_ad_inactive_get'))
{
	function carspotAPI_ad_inactive_get($request)
	{
		$json_data = $request->get_json_params();
		$paged = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';
		$userID = wp_get_current_user();
		$adsData = carspotApi_userAds($userID->ID, 'active', '', $paged, 'pending');
		
		$arr['notification'] 	= __("Waiting for admin approval.", "carspot-rest-api");
		$arr['ads'] 	 	    = $adsData['ads'];
		$arr['pagination'] 	 	= $adsData['pagination'];		
		$arr['page_title']	= __( 'Inactive Ads','carspot-rest-api');
		$arr['text'] = carspotAPI_user_ad_strings();
		$arr['text']['ad_type'] = 'inactive';
		$arr['text']['editable'] = '0';
		$arr['text']['show_dropdown'] = '0';
		$arr['profile'] = carspotAPI_basic_profile_data();
		$message = (count($arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		$response = array( 'success' => true, 'data' => $arr, 'message' => $message );	
		return $response;	
	}
	
}

/*Active Ads*/
if (!function_exists('carspotAPI_ad_active_get'))
{
	function carspotAPI_ad_active_get($request)
	{

		$json_data = $request->get_json_params();
		$paged = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';

		$userID = wp_get_current_user();		
		$arr['page_title']	= __( 'Active Ads','carspot-rest-api');
		$adsData = carspotApi_userAds($userID->ID, 'active', '', $paged);
		$arr['ads'] 	 	    = $adsData['ads'];
		$arr['pagination'] 	 	= $adsData['pagination'];
		$arr['text'] = carspotAPI_user_ad_strings();
		$arr['text']['ad_type'] = 'active';
		$arr['text']['editable'] = '1';
		$arr['text']['show_dropdown'] = '1';
		$arr['profile'] = carspotAPI_basic_profile_data();
		$message = (count($arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		$response = array( 'success' => true, 'data' => $arr, 'message' => $message );	
		return $response;	
	}
	
}
/*expired Ads*/
if (!function_exists('carspotAPI_ad_expired_get'))
{
	function carspotAPI_ad_expired_get($request)
	{
		
		$arr['page_title']	= __( 'Expired Ads','carspot-rest-api');
		$json_data = $request->get_json_params();
		$paged = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';
		
		$userID = wp_get_current_user();		
		$adsData = carspotApi_userAds($userID->ID, 'expired', '', $paged);
		$arr['ads'] 	 	    = $adsData['ads'];
		$arr['pagination'] 	 	= $adsData['pagination'];		
		$arr['page_title']	    = __( 'Expired Ads','carspot-rest-api');
		$arr['text'] = carspotAPI_user_ad_strings();
		$arr['text']['ad_type'] = 'expired';
		$arr['text']['editable'] = '1';
		$arr['text']['show_dropdown'] = '1';
		$arr['profile'] = carspotAPI_basic_profile_data();
		$message = (count($arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		$response = array( 'success' => true, 'data' => $arr, 'message' => $message );	

		return $response;	
	}
	
}
/*sold Ads*/
if (!function_exists('carspotAPI_ad_sold_get'))
{
	function carspotAPI_ad_sold_get($request)
	{
		$arr['page_title']	= __( 'Sold Ads','carspot-rest-api');
		$json_data = $request->get_json_params();
		$paged = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';
		$userID = wp_get_current_user();		
		$adsData = carspotApi_userAds($userID->ID, 'sold', '', $paged);
		$arr['ads'] 	 	    = $adsData['ads'];
		$arr['pagination'] 	 	= $adsData['pagination'];		
		$arr['page_title']	= __( 'Sold Ads','carspot-rest-api');
		$arr['text'] = carspotAPI_user_ad_strings();
		$arr['text']['ad_type'] = 'sold';
		$arr['text']['editable'] = '1';
		$arr['text']['show_dropdown'] = '1';
		$arr['profile'] = carspotAPI_basic_profile_data();
		$message = (count($arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		$response = array( 'success' => true, 'data' => $arr, 'message' => $message );	

		return $response;	
	}
	
}
/*featured Ads*/
if (!function_exists('carspotAPI_ad_featured_get'))
{
	function carspotAPI_ad_featured_get( $request )
	{
		$arr['page_title']	= __( 'Featured Ads','carspot-rest-api');
		$json_data = $request->get_json_params();
		$paged = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';	
		
		$userID 			= wp_get_current_user();		
		$adsData 			= carspotApi_userAds($userID->ID, 'active', '1', $paged);
		$arr['ads'] 	 	= $adsData['ads'];
		$arr['pagination'] 	= $adsData['pagination'];
		$arr['page_title']	= __( 'Featured Ads','carspot-rest-api');
		$arr['text']		= carspotAPI_user_ad_strings();
		$arr['text']['ad_type'] = 'featured';
		$arr['text']['editable'] = '1';
		$arr['text']['show_dropdown'] = '0';
		
		$arr['profile'] = carspotAPI_basic_profile_data();
		
		$message = (count($arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		$response = array( 'success' => true, 'data' => $arr, 'message' => $message );	
		return $response;	
	}
	
}

/*favourite Ads - Remove to favourites */
if (!function_exists('carspotAPI_user_ad_favourite_remove'))
{
	function carspotAPI_user_ad_favourite_remove($request)
	{
	
	$json_data = $request->get_json_params();
	$ad_id     = (isset( $json_data['ad_id'] ) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';		
	
	$user 		= wp_get_current_user();	
	$user_id 	= $user->data->ID;		
		
	if ( delete_user_meta($user_id, '_sb_fav_id_' . $ad_id) )
	{
		return  array( 'success' => true, 'data' => '', 'message'  => __("Ad removed successfully.", "carspot-rest-api") );
	}
	else
	{
		return  array( 'success' => false, 'data' => '', 'message'  => __("There'is some problem, please try again later.", "carspot-rest-api") );
	}
	
	}
}

/*favourite Ads*/
if (!function_exists('carspotAPI_user_ad_favourite'))
{
	function carspotAPI_user_ad_favourite($request)
	{
		$arr['page_title']	= __( 'Favourite Ads','carspot-rest-api');
		$json_data = $request->get_json_params();
		$paged = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';
		
		$userID 			    = wp_get_current_user();		
		$adsData 			    = carspotApi_userAds_fav($userID->ID, '', '', $paged);
		$arr['ads'] 	 	    = $adsData['ads'];
		$arr['pagination'] 	 	= $adsData['pagination'];		
		$arr['page_title']	= __( 'Favourite Ads','carspot-rest-api');
		$arr['text'] = carspotAPI_user_ad_strings();
		$arr['text']['ad_type'] = 'favourite';
		$arr['text']['editable'] = '0';
		$arr['text']['show_dropdown'] = '0';
				
		$arr['profile'] = carspotAPI_basic_profile_data();
		
		$message = (count($arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		
		$response = array( 'success' => true, 'data' => $arr, 'message' => $message );	
		
		return $response;	
	}
	
}


if (!function_exists('carspotAPI_user_ad_strings'))
{
	function carspotAPI_user_ad_strings()
	{
	
			$status_dropdown_value = array( "active","expired","sold", );
			$status_dropdown_name = array( 
									__("Active", "carspot-rest-api"), 
									__("Expired", "carspot-rest-api"), 
									__("Sold", "carspot-rest-api"), 
								);
																						
			$string["status_dropdown_value"] = $status_dropdown_value;
			$string["status_dropdown_name"] = $status_dropdown_name;
			$string["edit_text"] = __("Edit", "carspot-rest-api");
			$string["delete_text"] = __("Delete", "carspot-rest-api");
		
			
			return $string;
	}
}

if (!function_exists('carspotAPI_change_user_ad_status'))
{
	function carspotAPI_change_user_ad_status($request)
	{
		$userID = wp_get_current_user();
		if( empty($userID) )
		{
			$message = __("Invalid Access", "carspot-rest-api");		
			return $response = array( 'success' => true, 'data' => '', 'message' => $message );
		}
		$json_data = $request->get_json_params();		
		$ad_id = (isset($json_data['ad_id'])) 		? $json_data['ad_id'] : '';
		$ad_status = (isset($json_data['ad_status'])) ? $json_data['ad_status'] : '';
		$post_tmp = get_post($ad_id);
		if( isset( $post_tmp ) && $post_tmp != "")
		{
			$author_id = $post_tmp->post_author;		
			if( isset($userID) && $author_id == $userID->ID  && $ad_id != "" && $ad_status != "")
			{
				update_post_meta($ad_id, "_carspot_ad_status_", $ad_status);
				$message = __("Ad Status Updated", "carspot-rest-api");			
			}	
			else
			{
				$message = __("Some error occured.", "carspot-rest-api");
			}		
		}
		else
		{
				$message = __("Invalid Post Id", "carspot-rest-api");		
		}		
		$response = array( 'success' => true, 'data' => '', 'message' => $message );
		return $response;	
	}
}

/*Delete ad*/
if (!function_exists('carspotAPI_change_user_ad_delete'))
{
	function carspotAPI_change_user_ad_delete($request)
	{	
		$userID = wp_get_current_user();

		$json_data 	= $request->get_json_params();	
		$ad_id 		= (isset($json_data['ad_id'])) ? $json_data['ad_id'] : '';
		$post_data  = get_post($ad_id);

		if( empty($userID) )
		{
			$message = __("Invalid Access", "carspot-rest-api");		
			return $response = array( 'success' => false, 'data' => '', 'message' => $message );
		}	

		$status = get_post_status( $ad_id );
		
		if( get_post_status( $ad_id ) != "publish" )
		{
			$message = __("You can't delete this ad.", "carspot-rest-api");		
			return $response = array( 'success' => false, 'data' => $request, 'message' => $message );
		}			

		if( isset( $post_data ) && $post_data != "")
		{
			$author_id = $post_data->post_author;	
				
			if( $author_id == $userID->ID  && $post_data->ID != "" )
			{				
				$query = array(  'ID' => $post_data->ID, 'post_status' => 'trash', );
				wp_update_post( $query, true );				
				$message = __("Ad Deleted Successfully", "carspot-rest-api");			
			}	
			else
			{
				$message = __("Some error occured.", "carspot-rest-api");
			}		
		}
		else
		{
				$message = __("Invalid Post Id", "carspot-rest-api");		
		}		
		$response = array( 'success' => true, 'data' => $query, 'message' => $message );
		return $response;	
	}
}

/*Add To favs*/
if (!function_exists('carspotAPI_ad_add_to_fav'))
{
	function carspotAPI_ad_add_to_fav($request)
	{	
		$userID = wp_get_current_user();
		
		$json_data 	= $request->get_json_params();	
		$ad_id 		= (isset($json_data['ad_id'])) ? $json_data['ad_id'] : '';
		$post_data  = get_post($ad_id);

		if( empty($userID) || $userID == "")
		{
			$message = __("Invalid Access", "carspot-rest-api");		
			return $response = array( 'success' => false, 'data' => '', 'message' => $message );
		}	
		
		if( isset( $post_data ) && $post_data != "")
		{
			$author_id = $post_data->post_author;	
				
			if( $author_id == $userID->ID  && $post_data->ID != "" )
			{				
				$query = array(  'ID' => $post_data->ID, 'post_status' => 'trash', );
						
				$message = __("Added To Favourites", "carspot-rest-api");			
			}	
			else
			{
				$message = __("Already Added To Favourites", "carspot-rest-api");
			}		
		}
		else
		{
				$message = __("Invalid Post Id", "carspot-rest-api");		
		}		
		$response = array( 'success' => true, 'data' => '', 'message' => $message );
		
		return $response;	
	}
}
/*Add To favs ends */

if (!function_exists('carspotAPI_basic_profile_data'))
{
	function carspotAPI_basic_profile_data($user_id = '')
	{
			
			if( $user_id == "" )
			{	
				$user 	 = wp_get_current_user();
				$user_id = $user->ID;
			}
			else
			{
				$user = get_userdata( $user_id );	
			}
			
			if(!$user_id) return '';
			
			$profile_arr['id']				= $user_id;
			$profile_arr['user_email']		= $user->user_email;
			$profile_arr['display_name']	= $user->display_name;
			$profile_arr['pro_text']	    = __("My profile", "carspot-rest-api");
			$profile_arr['phone']			= get_user_meta($user_id, '_sb_contact', true );
			$profile_arr['profile_img']		= carspotAPI_user_dp( $user_id, 'carspot-user-profile');
	
			/*all active ads*/
			
			$ads_total_text = __("All Ads", "carspot-rest-api");
			$profile_arr['ads_total'] =  array("key" => $ads_total_text, "value" => carspotAPI_countPostsHere( 'publish', '_carspot_ad_status_', 'active', $user_id));
			
								
			$ads_inactive_text = __("Inactive Ads", "carspot-rest-api");
			$profile_arr['ads_inactive'] =  array("key" => $ads_inactive_text, "value" => carspotAPI_countPostsHere( 'pending', '', '', $user_id));
			
			
			
			$ads_sold_text = __("Sold Ads", "carspot-rest-api");
			$profile_arr['ads_solds'] =  array("key" => $ads_sold_text, "value" => carspotAPI_countPostsHere( 'publish', '_carspot_ad_status_', 'sold', $user_id));
			
	            $ads_expired_text = __("Expired Ads", "carspot-rest-api");
				$profile_arr['ads_expired'] =  array("key" => $ads_expired_text, "value" => carspotAPI_countPostsHere( 'publish', '_carspot_ad_status_', 'expired', $user_id));
			
				$ads_featured_text = __("Featured Ads", "carspot-rest-api");
				$profile_arr['ads_featured'] =  array("key" => $ads_featured_text, "value" => carspotAPI_countPostsHere( 'publish', '_carspot_is_feature', '1', $user_id));
				
				
			
			
			$profile_arr['expire_ads'] = array("key" => __("Expiry Ads", "carspot-rest-api"), "value" => get_user_meta( $user_id, '_sb_expire_ads', true ));
			$profile_arr['simple_ads'] = array("key" => __("Simple Ads", "carspot-rest-api"), "value" => get_user_meta( $user_id, '_sb_simple_ads', true ));
						
			$profile_arr['featured_ads'] = array("key" => __("Featured Ads", "carspot-rest-api"), "value" => get_user_meta( $user_id, '_sb_featured_ads', true ));
			
			
			$profile_arr['featured_ads'] 			= get_user_meta( $user_id, '_sb_featured_ads', true );
			$profile_arr['package_type'] 			= get_user_meta( $user_id, '_sb_pkg_type', true);
			if(get_user_meta( $user_id, '_carspot_expire_ads', true) == -1)
			{
				$profile_arr['package_expiry'] 			= array("key" => __('Package Expire', "carspot-rest-api"), "value" => __('Never Expire', "carspot-rest-api"));
			}
			else 
			{
				$profile_arr['package_expiry'] 			= array("key" => __("Package Expire", "carspot-rest-api"), "value" => get_user_meta( $user_id, '_sb_featured_ads', true ));
			}
			
			
			
			$profile_arr['last_login'] 				=   carspotAPI_getLastLogin( $user_id, true );
			$profile_arr['edit_text'] 				=   __("Edit Profile", "carspot-rest-api");
			$profile_arr['manage_text'] 			=   __("Manage your ", "carspot-rest-api");
			$profile_arr['manage_text2'] 			=   __("account settings", "carspot-rest-api");
			
				$badge_text = esc_attr( get_the_author_meta( '_sb_badge_text', $user_id ) );
				global $carspotAPI;
				if( isset( $carspotAPI['sb_new_user_email_verification'] ) && $carspotAPI['sb_new_user_email_verification'] )
				{
					$token = get_user_meta($user_id, 'sb_email_verification_token', true);
					if( $token && $token != "" )
					{
						$badge_text = __('Not Verified', "carspot-rest-api");
					}
				}
				
				
			
			
			
			$badge_text    =   ( $badge_text ) ? $badge_text : __('Verified', "carspot-rest-api");
			$badge_color   =   '#8ac249';
			$sb_badge_type =   esc_attr( get_the_author_meta( '_sb_badge_type', $user_id ) );
			if( $sb_badge_type == 'label-success' ) $badge_color = '#8ac249';
			else if( $sb_badge_type == 'label-warning' ) $badge_color = '#fe9700';
			else if( $sb_badge_type == 'label-info' ) $badge_color = '#02a8f3';
			else if( $sb_badge_type == 'label-danger' ) $badge_color = '#f34235';
	
			//$badge_text = 
			$profile_arr['verify_buton']['text'] 	= $badge_text;
			$profile_arr['verify_buton']['color'] 	= $badge_color;
			$profile_arr['rate_bar']['number'] 		= carspotAPI_user_ratting_info($user_id, 'stars');
			$profile_arr['rate_bar']['text'] 		= carspotAPI_user_ratting_info($user_id, 'count');
			
			
			$sb_userType = get_user_meta( $user->ID, '_sb_user_type', true );
			$sb_userType = ($sb_userType) ? $sb_userType : __('Individual', "carspot-rest-api");
			$profile_arr['userType_buton']['text'] 	= $sb_userType;
			$profile_arr['userType_buton']['color'] 	= '#8ac249';
			
			
			$social_profiles = carspotAPI_social_profiles();
			$profile_arr['is_show_social'] = false;
			if( isset( $social_profiles ) && count($social_profiles) > 0 )
			{
				$profile_arr['is_show_social'] = true;
				foreach( $social_profiles as $key => $val )
				{
					$keyName = '';
					$keyName = "_sb_profile_".$key;
					$keyVal  = get_user_meta( $user_id, $keyName, true );
					$keyVal  = ( $keyVal ) ? $keyVal : '';
					$profile_arr['social_icons'][] 	= array("key" => $val, "value" => $keyVal, "field_name" => $keyName);
				}
			}
			
			
			return $profile_arr;
		
	}
}


if (!function_exists('carspotAPI_user_ratting_info'))
{
	function carspotAPI_user_ratting_info($user_id = '', $type = 'stars')
	{
		$stars	= get_user_meta($user_id, "_carspot_rating_avg", true );
		$info["stars"]  = ( $stars == "" ) ? "0" : $stars;
		$starsCount = get_user_meta($user_id, "_carspot_rating_count", true );
		$info["count"] = ( $starsCount != "" ) ? $starsCount : "0";		
		return $info["$type"];
	}
}

if (!function_exists('carspotAPI_countPostsHere'))
{
function carspotAPI_countPostsHere($status = 'publish', $meta_key = '', $meta_val = '',$postAuthor = '')
{
	if( $meta_key != "" )
	{
		
		$args = array("author" => $postAuthor, 'post_type' => 'ad_post', 'post_status' => $status, 'meta_key' => $meta_key, 'meta_value' => $meta_val );
		$query = new WP_Query( $args );
	}
	else
	{
		$args = array( "author" => $postAuthor, 'post_type' => 'ad_post', 'post_status' => $status );
		$query = new WP_Query( $args );
	}
	
	wp_reset_postdata();
	return $query->found_posts;	
	
	
}
}

add_action( 'rest_api_init', 'carspotAPI_user_public_profile_hook', 0 );
function carspotAPI_user_public_profile_hook() {
	
	/*Routs*/
    register_rest_route(
		'carspot/v1', '/profile/public/', 
		array( 'methods'  => WP_REST_Server::READABLE, 
		'callback' => 'carspotAPI_user_public_profile',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));	
    register_rest_route(
		'carspot/v1', '/profile/public/', 
		array( 'methods'  => WP_REST_Server::EDITABLE, 
		'callback' => 'carspotAPI_user_public_profile',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));		
}

if (!function_exists('carspotAPI_user_public_profile'))
{
	function carspotAPI_user_public_profile( $request )
	{
		
		$json_data	= $request->get_json_params();	
		$user_id	= (isset($json_data['user_id'])) ? $json_data['user_id'] : '';
		
		if( $user_id == "" )
		{
			$user 		= wp_get_current_user();	
			$user_id 	= $user->data->ID;			
		}

		$json_data = $request->get_json_params();
		$paged = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';
		
		$adsData = carspotApi_userAds($userID->ID, '', '', $paged);
		$arr['ads'] 	 	    = $adsData['ads'];
		$arr['pagination'] 	 	= $adsData['pagination'];

		$arr['text'] = carspotAPI_user_ad_strings();
		$arr['text']['ad_type'] = 'myads';
		$arr['text']['editable'] = 0;
		$arr['text']['show_dropdown'] = 0;
		$arr['profile'] = carspotAPI_basic_profile_data( $user_id );
		$message = (count($arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		$response = array( 'success' => true, 'data' => $arr, 'message' => $message );	
		return $response;	
		
		
		
	}
}


add_action( 'rest_api_init', 'carspotAPI_user_ratting_hook', 0 );
function carspotAPI_user_ratting_hook() {
	
	/*Routs*/
    register_rest_route(
		'carspot/v1', '/profile/ratting/', 
		array( 'methods'  => WP_REST_Server::READABLE, 
		'callback' => 'carspotAPI_user_ratting_list',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));	
    register_rest_route(
		'carspot/v1', '/profile/ratting_get/', 
		array( 'methods'  => WP_REST_Server::EDITABLE, 
		'callback' => 'carspotAPI_user_ratting_list',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));		
		
}

if (!function_exists('carspotAPI_user_ratting_list')){
		function carspotAPI_user_ratting_list( $request = '' )
		{
			$json_data	= $request->get_json_params();	
			$author_id	= (isset($json_data['author_id'])) ? $json_data['author_id'] : '';
			
			if( $author_id == "" )
			{
				$author 		= wp_get_current_user();	
				$author_id 	= $author->data->ID;			
			}			
			return carspotAPI_user_ratting_list1( $author_id );
		}
	}

if (!function_exists('carspotAPI_user_ratting_list1')){
	function carspotAPI_user_ratting_list1( $author_id = '', $return_arr  = true )
	{
		

		$rating_user	= wp_get_current_user();	
		$rating_user_id = $rating_user->data->ID;			
		
		$ratings 	=	carspotAPI_get_all_ratings($author_id);		
		$rateArr 	= array();
		$rate = array();
		$message = '';
		$rdata = array();
		if( count( $ratings ) > 0 )
		{

			foreach( $ratings as $rating )
			{
				$data		=	explode( '_separator_', $rating->meta_value );
				$rated		=	trim( (int)$data[0]);
				$comments	=	trim($data[1]);
				$date		=	$data[2];
				$reply		= ( isset( $data[3] ) ) ? $data[3]  : '';
				$reply_date	= ( isset( $data[4] ) ) ? $data[4] : '';
				
				
				$_arr	=	explode( '_user_', $rating->meta_key );
				$rator	=	$_arr[1];
				
				$user = get_user_by( 'ID', $rator );
				if( $user )
				{
					$img = carspotAPI_user_dp( $user->ID );
					$can_reply = ( $reply == "" && $rating_user_id == $author_id ) ? true : false;
					$has_reply = ( $reply == ""  ) ? false : true;
					
					$rate['reply_id'] 	= $rator;
					$rate['name'] 		= $user->display_name;
					$rate['img']  		= $img;
					$rate['stars'] 		=  (int)( $rated != "" ) ? $rated : 0;
					$rate['date']  		= date(get_option('date_format'), strtotime($date));
					$rate["can_reply"] 	= $can_reply;
					$rate["has_reply"] 	= $has_reply;
					$rate["reply_txt"] 	= __( 'Reply', 'carspot-rest-api' );
					$rate["comments"] 	= $comments;
					
					$rate2 = array();
					if( $reply != "" )
					{
						$userR = get_user_by( 'ID', $author_id );
						$img2 = carspotAPI_user_dp( $author_id );
						$rate2['name'] 		= $userR->display_name;
						$rate2['img']  		= $img2;
						$rate2['stars'] 		= 0;
						$rate2['date']  		= date(get_option('date_format'), strtotime($reply_date));
						$rate2["can_reply"] 	= false;
						$rate2["has_reply"] 	= true;
						$rate2["reply_txt"] 	= __( 'Reply', 'carspot-rest-api' );
						$rate2["comments"] 	= trim($reply);					
					}
					
					$rate["reply"] 	= $rate2;
					
					$rateArr[] = $rate;
				}
				
				if( count( $rateArr ) == 0 )
				{
					$message = ( $author_id != $rating_user_id ) ? __( 'Be the first one to rate this user.','carspot-rest-api') : __( 'Currently no rating available..','carspot-rest-api');
				}
			}
			
		}
		else
		{
			
			$message = ( $author_id != $rating_user_id ) ? __( 'Be the first one to rate this user.','carspot-rest-api') : __( 'Currently no rating available..','carspot-rest-api');
			
		}
		$can_rate = ($author_id == $rating_user_id) ? false : true;
		/*User Ratting Form Info*/
		$rdata['page_title'] 				= __( 'User Rating','carspot-rest-api');
		$rdata['rattings'] 					= $rateArr;
		$rdata['can_rate'] 					= $can_rate;
		$rdata['form']['title'] 			= __( 'Rate Here','carspot-rest-api');
		$rdata['form']['select_text'] 		= __( 'Rating','carspot-rest-api');
		$rdata['form']['select_value'] 		= array(1,2,3,4,5);
		$rdata['form']['textarea_text'] 	= __( 'Comments','carspot-rest-api');
		$rdata['form']['textarea_value'] 	= '';
		$rdata['form']['tagline'] 			= __( 'You can not edit it later.','carspot-rest-api');
		$rdata['form']['btn'] 				= __( 'Submit Your Rating','carspot-rest-api');
		
		if( $return_arr == true )
		{
			$response = array( 'success' => true, 'data' => $rdata , 'message' => $message, "ratings " => $ratings );	
			return $response;
		}
		else
		{
			return $rateArr;
		}
	}
	
}

add_action( 'rest_api_init', 'carspotAPI_post_ratting_hook', 0 );
function carspotAPI_post_ratting_hook() {
	
    register_rest_route(
		'carspot/v1', '/profile/ratting/', 
		array( 'methods'  => WP_REST_Server::EDITABLE, 
		'callback' => 'carspotAPI_post_user_ratting',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
	));		
}

if (!function_exists('carspotAPI_post_user_ratting')){
function carspotAPI_post_user_ratting( $request )
{

	
	$json_data	= $request->get_json_params();	
	
	$ratting	= (isset($json_data['ratting'])) ? (int)$json_data['ratting'] : '';
	$comments	= (isset($json_data['comments'])) ? trim($json_data['comments']) : '';
	$author	    = (isset($json_data['author_id'])) ? (int)$json_data['author_id'] : '';
	$is_reply   = (isset($json_data['is_reply']) && $json_data['is_reply'] == true) ? true : false;

	$authorData 		= wp_get_current_user();	
	$rator 				= $authorData->data->ID;			
	$cUser 				= $authorData->data->ID;			
	
	if( $author == $rator )
		return  array( 'success' => false, 'data' => '', 'message'  => __("You can't rate yourself.", "carspot-rest-api") );

		//delete_user_meta($rator, '_carspot_rating_avg');
		//delete_user_meta($rator, '_carspot_rating_count');
		
	if( $is_reply == true) 
	{
		$rdata			= array();
		$rator			=	$author;
		$got_ratting	=	$rator;
		
		$ratting = get_user_meta( $cUser, "_user_" . $rator, true );
		$data_arr	=	explode( '_separator_', $ratting );
		if( count( $data_arr ) > 3 )
		{
			return  array( 'success' => false, 'data' => '', 'message'  => __("You already replied to this user.", "carspot-rest-api") );
		}
		else
		{
			$ratting = $ratting .  "_separator_" . $comments . "_separator_" . date('Y-m-d');
			update_user_meta( $cUser, '_user_' . $rator, $ratting );
			
			$rdata['rattings'] = carspotAPI_user_ratting_list1( $cUser, false );
			return  array( 'success' => true, 'data' => $rdata, 'message'  => __("You're reply has been posted.", "carspot-rest-api") );
		}		
		
		
	}		
			
	else{	
	if( get_user_meta( $author, "_user_" . $rator, true ) == "" )
	{ 
		$rdata = array();
		update_user_meta($author, "_user_" . $rator, $ratting ."_separator_" . $comments ."_separator_" . date('Y-m-d'));
		$ratings	=	carspotAPI_get_all_ratings($author);
		$all_rattings	=	0;
		$got	=	0;
		if( count( $ratings ) > 0 )
		{
			foreach( $ratings as $rating )
			{
				$data	=	explode( '_separator_', $rating->meta_value );
				$got	=	$got + $data[0];
				$all_rattings++;
			}
			$avg	=	 $got/$all_rattings;
		}
		else
		{
			$avg = $ratting;
		}
		
		
		update_user_meta($author, "_carspot_rating_avg", $avg );
		$total	=	get_user_meta( $author, "_carspot_rating_count", true );
		if( $total == "" ){ $total = 0;}
		$total	=	$total + 1;
		update_user_meta($author, "_carspot_rating_count", $total  );
		
		// Send email if enabled
		global $carspotAPI;
		if( isset( $carspotAPI['email_to_user_on_rating'] ) && $carspotAPI['email_to_user_on_rating'] )
		{
			carspot_send_email_new_rating( $rator, $author, $ratting, $comments );
		}
		$rdata['rattings'] = carspotAPI_user_ratting_list1( $author, false);
		return  array( 'success' => true, 'data' => $rdata, 'message'  => __("You've rated this user.", "carspot-rest-api") );
	}
	else
	{
		return  array( 'success' => false, 'data' => '', 'message'  => __("You already rated this user.", "carspot-rest-api") );
	}
	}
}
}

/*API custom endpoints for WP-REST API*/
add_action( 'rest_api_init', 'carspotAPI_profile_nearby', 0 );
function carspotAPI_profile_nearby() {
    register_rest_route(
		'carspot/v1', '/profile/nearby/', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'carspotAPI_profile_nearby_get',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
	
    register_rest_route(
		'carspot/v1', '/profile/nearby/', array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_profile_nearby_get',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
}

if (!function_exists('carspotAPI_profile_nearby_get'))
{
	function carspotAPI_profile_nearby_get($request)
	{
		$data = array();
		$user_id = get_current_user_id();
		$success = false;
		if($user_id)
		{
			
			if(isset($request) )
			{				
				$json_data	= $request->get_json_params();
				$latitude	= (isset($json_data['nearby_latitude'])) ? $json_data['nearby_latitude'] : '';
				$longitude	= (isset($json_data['nearby_longitude'])) ? $json_data['nearby_longitude'] : '';
				$distance	= (isset($json_data['nearby_distance'])) ? $json_data['nearby_distance'] : '20';
				if( $latitude != "" && $longitude != "" )
				{ 
					$data_array = array("latitude" => $latitude, "longitude" => $longitude, "distance" => $distance );
					update_user_meta($user_id, '_sb_user_nearby_data', $data_array );
					$success = true;
				}
				else
				{
					update_user_meta($user_id, '_sb_user_nearby_data', '' );	
					$success = false;
				}
			}

			$data = carspotAPI_determine_minMax_latLong();
			
		}
		
		$message = ( $success ) ? 	__("Nearby option turned on", "carspot-rest-api") : __("Nearby option turned of", "carspot-rest-api");
		
		return  array( 'success' => $success, 'data' => $data, 'message'  => $message );
	}
}
/*NearByAdsStarts*/
add_action( 'rest_api_init', 'carspotAPI_nearby_ads_hook', 0 );
function carspotAPI_nearby_ads_hook() {
	/*Routs*/
    register_rest_route(
        		'carspot/v1', '/ad/nearby/', array( 'methods'  => WP_REST_Server::EDITABLE, 'callback' => 'carspotAPI_nearby_ads_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	));	
}


/*Active Ads*/
if (!function_exists('carspotAPI_nearby_ads_get'))
{
	function carspotAPI_nearby_ads_get($request)
	{
		$json_data = $request->get_json_params();
		$paged = (isset( $json_data['page_number'] ) ) ?  $json_data['page_number'] : '1';
				
		$userID 	 = wp_get_current_user();
		$adsData 	 = carspotApi_userAds('', '', '', $paged,'publish', 'near_me');
		$arr['ads'] 	 	    = $adsData['ads'];
		$arr['pagination'] 	 	= $adsData['pagination'];		
		
		$arr['page_title']	= __( 'Near By ads','carspot-rest-api');
		$arr['text'] = carspotAPI_user_ad_strings();
		$arr['text']['ad_type'] = 'nearby';
		$arr['text']['editable'] = '1';
		$arr['text']['show_dropdown'] = '1';
		$arr['profile'] = carspotAPI_basic_profile_data();
		$message = (count($arr['ads'] ) == 0 ) ? __("No ad found", "carspot-rest-api") : "";
		$response = array( 'success' => true, 'data' => $arr, 'message' => $message );	
		return $response;	
	}
	
}
/*NearByAdsENds*/

add_action( 'rest_api_init', 'carspotAPI_profile_package_details_hook', 0 );
function carspotAPI_profile_package_details_hook() {

    register_rest_route(
			'carspot/v1', '/profile/purchases/', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'carspotAPI_profile_package_details',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
}  
 
if (!function_exists('carspotAPI_profile_package_details'))
{
	function carspotAPI_profile_package_details()
	{ 
	
		$user_id	 =	get_current_user_id();
  		$args = array( 'customer_id' => $user_id, );
		
		$order_hostory = array();
		$order_hostory[] = array(
								"order_number" 	=> __('Order #','carspot-rest-api'),
								"order_name" 	=> __('Package(s)','carspot-rest-api'),
								"order_status" 	=> __('Status','carspot-rest-api'),
								"order_date" 	=> __('Date','carspot-rest-api'),
								"order_total" 	=> __('Order total','carspot-rest-api'),								
							  );

 $orders = wc_get_orders( $args );
 $message = '';
 if( count( $orders ) > 0 )
 {
	 foreach( $orders as $order )
	 {
		 
		$order_id = $order->get_id();
		$items = $order->get_items();
		$product_name = array();
	
		  foreach ( $items as $item )
		  {
			  $product_name[] = $item->get_name();
		  }
			$product_names   = implode(",", $product_name);
			$order_hostory[] = array(
									"order_number" 	=> $order_id,
									"order_name" 	=> $product_names,
									"order_status" 	=> wc_get_order_status_name($order->get_status()),
									"order_date" 	=> date_i18n(get_option('date_format'), strtotime($order->get_date_created()) ),
									"order_total" 	=> $order->get_total(),								
								  );
	
	 }	 
  } 
 else
 {
	$message = 	__('No Order Found','carspot-rest-api');
 }
 	$data['page_title']    = __('Packages History','carspot-rest-api');
	$data['order_hostory'] = $order_hostory;		
	return array( 'success' => true, 'data' => $data, 'message' => $message );	
	}
}

/*-----  Ad rating And Comments Starts  -----*/
add_action( 'rest_api_init', 'carspotAPI_profile_gdpr_delete_user_hook', 0 );
function carspotAPI_profile_gdpr_delete_user_hook() {

    register_rest_route( 'carspot/v1', '/profile/delete/user_account/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_profile_gdpr_delete_user',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
}


if (!function_exists('carspotAPI_profile_gdpr_delete_user'))
{
	function carspotAPI_profile_gdpr_delete_user( $request )
	{
		global $carspotAPI;/*For Redux*/
		$json_data   = $request->get_json_params();
		$user_id 	     = (isset( $json_data['user_id'] ) && $json_data['user_id'] != "" ) ? $json_data['user_id'] : '';
		
		$current_user = get_current_user_id();

		$success  = false;
		$message  = __("Something went wrong.", "carspot-rest-api");		
		$if_user_exists = carspotAPI_user_id_exists($user_id);
		if( $current_user == $user_id && $if_user_exists )
		{
			if( current_user_can('administrator') ) {
			
				$success  = false;
				$message  = __("Admin can not delete his account from here.", "carspot-rest-api");		
			}
			else
			{
				carspotAPI_delete_userComments($user_id);
				$user_delete = wp_delete_user( $user_id );	
				if($user_delete )
				{
					
					$success  = true;
					$message  = __("You account has been delete successfully.", "carspot-rest-api");
				}	
			}
		}
		
		return array( 'success' => $success, 'data' => '', 'message'  => $message);
	}
}
if (!function_exists('carspotAPI_user_id_exists'))
{
	function carspotAPI_user_id_exists($user){
	
		global $wpdb;
	
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user));
	
		if($count == 1){ return true; }else{ return false; }
	
	}
}

if (!function_exists('carspotAPI_delete_userComments'))
{
	function carspotAPI_delete_userComments($user_id) {
		$user = get_user_by('id', $user_id);
	
		$comments = get_comments('author_email='.$user->user_email);
		if($comments && count($comments) > 0 ){
		foreach($comments as $comment) :
			@wp_delete_comment($comment->comment_ID, true);
		endforeach;
		}
		
		$comments = get_comments('user_id='.$user_id);
		if($comments && count($comments) > 0 ){
		foreach($comments as $comment) :
			@wp_delete_comment($comment->comment_ID, true);
		endforeach;	
		}
	}
}