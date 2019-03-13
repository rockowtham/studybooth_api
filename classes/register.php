<?php
/* ----	Register Starts Here ----*/
	
	add_action( 'rest_api_init', 'carspotAPI_register_api_hooks_post', 0 );
	function carspotAPI_register_api_hooks_post() {
	
		register_rest_route( 'carspot/v1', '/register/',
			array(
					'methods'  => WP_REST_Server::EDITABLE,
					'callback' => 'carspotAPI_register_me_post',
					/*'permission_callback' => function () { return adforestAPI_basic_auth();  },*/
				)
		);
	}

    function carspotAPI_register_me_post( $request ) {		
		$json_data = $request->get_json_params();		
		if( empty( $json_data ) || !is_array( $json_data ) )
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Please fill out all fields.", "carspot-rest-api") );
			return rest_ensure_response( $response );

		}		
        $output = array();
		
		$from	 	= (isset($json_data['from']))     ? $json_data['from'] : '';
		$name 		= (isset($json_data['name']))     ? $json_data['name'] : '';
		$email 		= (isset($json_data['email']))    ? $json_data['email'] : '';
		$phone 		= (isset($json_data['phone']))    ? $json_data['phone'] : '';
		$password 	= (isset($json_data['password'])) ? $json_data['password'] : '';
		
		if( $name == "" )
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Please enter name.", "carspot-rest-api") );
			return $response;
		}
		if( $email == "" )
		{
			$response = array( 'success' => false, 'data' => '' , 'message'  => __("Please enter email.", "carspot-rest-api") );
			return $response;
		}
		if( $password == "" )
		{
			$response = array( 'success' => false, 'data' => '' , 'message'  => __("Please enter password.", "carspot-rest-api") );
			return $response;
		}
				
		
		if( email_exists( $email ) == true )
		{
			$response = array( 'success' => false, 'data' => '' , 'message'  => __("Email Already Exists.", "carspot-rest-api") );
			return $response;
		}
		
		$username	=	stristr($email, "@", true);
		/*Generate Username*/
		$u_name		= 	carspotAPI_check_username($username);
		/* Register User With WP */
		$uid =	wp_create_user( $u_name, $password, $email );
		
		global $carspotAPI;
		global $carspot_theme;

			wp_update_user( array( 'ID' => $uid, 'display_name' => $name ) );
			update_user_meta($uid, '_sb_contact', $phone );
			
			if(isset($carspot_theme['carspot_package_type']) && $carspot_theme['carspot_package_type'] == 'package_based')
			{ 
				if( isset( $carspotAPI['sb_allow_ads'] ) && $carspotAPI['sb_allow_ads'] )
				{
					
					$freeAds 	= carspotAPI_getReduxValue('sb_free_ads_limit', '', false);
					$freeAds 	= ( isset( $carspotAPI['sb_allow_ads'] ) && $carspotAPI['sb_allow_ads'] ) ? $freeAds : 0;
					
					$featured 	= carspotAPI_getReduxValue('sb_featured_ads_limit', '', false);
					$featured 	= ( isset( $carspotAPI['sb_allow_featured_ads'] ) && $carspotAPI['sb_allow_featured_ads'] ) ? $featured : 0;
					
					$bump 		= carspotAPI_getReduxValue('sb_bump_ads_limit', '', false);
					$bump 		= ( isset( $carspotAPI['sb_allow_bump_ads'] ) && $carspotAPI['sb_allow_bump_ads'] ) ? $bump : 0;
					
					$validity 	= carspotAPI_getReduxValue('sb_package_validity', '', false);
					
					update_user_meta( $uid, '_sb_simple_ads',   $freeAds );
					update_user_meta( $uid, '_carspot_featured_ads', $featured );
					update_user_meta( $uid, '_carspot_bump_ads', $bump );
					
					if( $validity == '-1' )
					{
						update_user_meta( $uid, '_carspot_expire_ads', $validity );
					}
					else
					{
						$expiry_date	=	date('Y-m-d', strtotime("+$validity days"));
						update_user_meta( $uid, '_carspot_expire_ads', $expiry_date );		
					}
				}
			}
			else
			{
				
				update_user_meta( $uid, '_sb_simple_ads', 0 );
				update_user_meta( $uid, '_carspot_featured_ads', 0 );
				update_user_meta( $uid, '_carspot_bump_ads', 0 );
				update_user_meta( $uid, '_carspot_expire_ads', date('Y-m-d') );
			}
			
			update_user_meta( $uid, '_sb_pkg_type', 'free' );	
		
			$user_info 						= get_userdata( $uid );		
			$profile_arr                    = array();
			$profile_arr['id']				= $user_info->ID;
			$profile_arr['user_email']		= $user_info->user_email;
			$profile_arr['display_name']	= $user_info->display_name;
			$profile_arr['phone']			= get_user_meta($user_info->ID, '_sb_contact', true );
			$profile_arr['profile_img']		= carspotAPI_user_dp( $user_info->ID);
					
			$message_text = __("Registered successfully.", "carspot-rest-api");
			if( isset( $carspotAPI['sb_new_user_email_verification'] ) && $carspotAPI['sb_new_user_email_verification'] )
			{
				
				carspotAPI_email_on_new_user($uid, '');								
				$message_text = __("Registered successfully. Please verify your email address.", "carspot-rest-api");
				/*Remove User Role For Email Verifications*/
				$user = new WP_User($uid);
				foreach($user->roles as $role){ $user->remove_role($role); }				
			}
								
			$response = array( 'success' => true, 'data' => $profile_arr, 'message' => $message_text );	
			return $response;		
        
    }
	
add_action( 'rest_api_init', 'carspotAPI_register_api_hooks_get', 0 );
function carspotAPI_register_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/register/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_register_me_get',
				/*'permission_callback' => function () { return carspotAPI_basic_auth();  },*/
        	)
    );
}

if( !function_exists('carspotAPI_register_me_get' ) )
{
	function carspotAPI_register_me_get()
	{
		global $carspotAPI;
		$data['bg_color']				= 	'#FFFFFF';
		$data['logo']					= 	carspotAPI_appLogo();
		$data['heading']				=  __("Register With Us!", "carspot-rest-api");		
		$data['name_placeholder']		=  __("Full Name", "carspot-rest-api");
		$data['email_placeholder']		=  __("Email Address", "carspot-rest-api");
		$data['phone_placeholder']		=  __("Phone Number", "carspot-rest-api");
		$data['password_placeholder']	=  __("Password", "carspot-rest-api");
		
		$data['form_btn']				=  __("Register", "carspot-rest-api");
		$data['separator']				=  __("OR", "carspot-rest-api");
		$data['facebook_btn']			=  __("Facebook", "carspot-rest-api");
		$data['google_btn']				=  __("Google+", "carspot-rest-api");
		$data['login_text']				=  __("Already Have Account? Login Here", "carspot-rest-api");
		$data['tagline_text']		    =  (isset($carspotAPI['app_register_tagline'])) ? $carspotAPI['app_register_tagline'] : '';
		
		$verified = (isset($carspotAPI['sb_new_user_email_verification']) && $carspotAPI['sb_new_user_email_verification'] == false) ? false : true;
		$data['is_verify_on']			=  $verified;
		$data['term_page_id']           = (isset($carspotAPI['sb_new_user_register_policy'])) ? $carspotAPI['sb_new_user_register_policy'] : '';
		
		//
		$checkbox_text = (isset($carspotAPI['sb_new_user_register_checkbox_text']) && $carspotAPI['sb_new_user_register_checkbox_text'] != "") ? $carspotAPI['sb_new_user_register_checkbox_text'] : __("Agree With Our Term and Conditions.", "carspot-rest-api");
		$data['terms_text']				=  $checkbox_text;
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => ''  );				
	}
}

/*Forgot*/
add_action( 'rest_api_init', 'carspotAPI_forgot_api_hooks_get', 0 );
function carspotAPI_forgot_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/forgot/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_forgot_me_get',
				/*'permission_callback' => function () { return carspotAPI_basic_auth();  },*/
        	)
    );
}
if( !function_exists('carspotAPI_forgot_me_get' ) )
{
	function carspotAPI_forgot_me_get()
	{		
		$data['bg_color']			=  '#FFFFFF';
		$data['logo']				=  carspotAPI_appLogo();
		$data['heading']			=  __("Forgot Password?", "carspot-rest-api");
		$data['text']				=  __("Please enter your email address below.", "carspot-rest-api");
		$data['email_placeholder']	=  __("Email Address", "carspot-rest-api");		
		$data['submit_text']		=  __("Submit", "carspot-rest-api");
		$data['back_text']			=  __("Back", "carspot-rest-api");
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => ''  );		
	}
}

/************************/
/* Reset password       */
/************************/
if( !function_exists('carspotAPI_reset_password_get' ) )
{
	function carspotAPI_reset_password_get()
	{		
		$data['bg_color']			=  '#FFFFFF';
		$data['heading']			=  __("Set your password", "carspot-rest-api");
		$data['current']			=  __("Enter current password", "carspot-rest-api");
		$data['new']	            =  __("Enter New password", "carspot-rest-api");		
		$data['confirm']		    =  __("Confirm new password", "carspot-rest-api");
		$data['btn']			    =  __("Change password", "carspot-rest-api");
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => ''  );		
	}
}