<?php
/*-----	Login Starts Here	-----*/

add_action( 'rest_api_init', 'carspotAPI_login_api_hooks_post', 0 );
function carspotAPI_login_api_hooks_post() {

    register_rest_route( 'carspot/v1', '/login/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_loginMe_post',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
				
        	)
    );
}


if (!function_exists('carspotAPI_loginMe_post'))
{
	function carspotAPI_loginMe_post($request)
	{
			
		$json_data = $request->get_json_params();
		
		$email 			= (isset($json_data['email'])) 		? $json_data['email'] : '';
		$password 		= (isset($json_data['password'])) 	? $json_data['password'] : '';
		$remember 		= (isset($json_data['remember'])) 	? $json_data['remember'] : '';
		$type 			= (isset($json_data['type'])) 		? $json_data['type'] : 'normal';
		
		$creds 					= array();
		$creds['remember'] 		= $remember;
		$creds['user_login'] 	= $email;
		$creds['user_password'] = $password;
		
		if( $type == 'social' )
		{
			
			$user = get_user_by( 'email', $email );
			if( $user )
			{
				
				$user_id = $user->ID;			
				$profile_arr = array();
				$profile_arr['id']				= $user->ID;
				$profile_arr['user_email']		= $user->user_email;
				$profile_arr['display_name']	= $user->display_name;
				$profile_arr['phone']			= get_user_meta($user->ID, '_sb_contact', true );
				$profile_arr['profile_img']		= carspotAPI_user_dp( $user->ID);
				$response = array( 'success' => true, 'data' => $profile_arr, 'message'  => __("Login Successfull", "carspot-rest-api")  );					
			}
			else
			{
				$response = array( 'success' => true, 'data' => '', 'message'  => __("Something went wrong", "carspot-rest-api")  );
			}
			
		}
		else
		{
			$user = wp_signon( $creds, false );
			if ( is_wp_error($user) )
			{
				$response = array( 'success' => false, 'data' => '' , 'message'  => __("Invalid Login Details", "carspot-rest-api") );
			}
		else
		{
			$profile_arr = array();
			$profile_arr['id']				= $user->ID;
			$profile_arr['user_email']		= $user->user_email;
			$profile_arr['display_name']	= $user->display_name;
			$profile_arr['phone']			= get_user_meta($user->ID, '_sb_contact', true );
			$profile_arr['profile_img']		= carspotAPI_user_dp( $user->ID);
			$profile_arr['is_account_confirm']		= true;
			carspotAPI_setLastLogin2( $user->ID );
			global $carspotAPI;
			if( isset( $carspotAPI['sb_new_user_email_verification'] ) && $carspotAPI['sb_new_user_email_verification'] )
			{
				$token = get_user_meta($user->ID, 'sb_email_verification_token', true);
				if( $token && $token != "" ){
				$profile_arr['is_account_confirm']		= false;	
				return array( 'success' => true, 'data' => $profile_arr, 'message'  => __("Please verify your email address to login.", "carspot-rest-api") );
				}
			}
			$response = array( 'success' => true, 'data' => $profile_arr, 'message'  => __("Login Successfull", "carspot-rest-api")  );
							
		}
		}
		return $response;	
	}
}

/*add_action('wp_login', 'carspotAPI_setLastLogin');*/
if (!function_exists('carspotAPI_setLastLogin'))
{
	function carspotAPI_setLastLogin($login, $user ) {
	   $cur_user = get_user_by( 'login', $login );
	   update_user_meta( $cur_user->ID, '_sb_last_login', time() );		
	}
}

if (!function_exists('carspotAPI_setLastLogin2'))
{
	function carspotAPI_setLastLogin2( $userID = '') {
	   update_user_meta( $userID, '_sb_last_login', time() );
	}
}



add_action( 'rest_api_init', 'carspotAPI_login_api_hooks_get', 0 );
function carspotAPI_login_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/login/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_loginMe_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
}

if (!function_exists('carspotAPI_loginMe_get'))
{
	function carspotAPI_loginMe_get()
	{		
		global $carspotAPI;
		
		$data['bg_color']				=  '#FFFFFF';
		$data['logo']					=  carspotAPI_appLogo();
		$data['heading']				=  __("Welcome Back", "carspot-rest-api");
		$data['email_placeholder']		=  __("Your Email Address", "carspot-rest-api");
		$data['password_placeholder']	=  __("Your Password", "carspot-rest-api");
		$data['forgot_text']			=  __("Forgot Password", "carspot-rest-api");
		$data['form_btn']				=  __("Submit", "carspot-rest-api");
		$data['separator']				=  __("OR", "carspot-rest-api");
		$data['facebook_btn']			=  __("Facebook", "carspot-rest-api");
		$data['google_btn']				=  __("Google+", "carspot-rest-api");
		$data['register_text']			=  __("Not a Member Yet? Register with us.", "carspot-rest-api");
		$data['remember']			    =  __("Remember me", "carspot-rest-api");
		
		$data['guest_login']			=  __("Guest Login", "carspot-rest-api");
		$data['guest_text']				=  __("Guest", "carspot-rest-api");
		$data['tagline_text']		    =  (isset($carspotAPI['app_register_tagline'])) ? $carspotAPI['app_register_tagline'] : '';
		
		$verified = (isset($carspotAPI['sb_new_user_email_verification']) && $carspotAPI['sb_new_user_email_verification'] == false) ? false : true;
		$data['is_verify_on']			=  $verified;
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => ''  );
	}
}


add_action( 'rest_api_init', 'carspotAPI_profile_forgotpass_hooks_post', 0 );
function carspotAPI_profile_forgotpass_hooks_post() {

    register_rest_route(
			'carspot/v1', '/forgot/', array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_profile_forgotpass_post',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
}  
 
if (!function_exists('carspotAPI_profile_forgotpass_post'))
{
	function carspotAPI_profile_forgotpass_post( $request )
	{ 
		//$user = wp_get_current_user();	
		//$user_id = $user->data->ID;	
		$json_data		= $request->get_json_params();	
		$email		    = (isset($json_data['email'])) ? trim($json_data['email']) : '';

		if( CARSPOT_API_ALLOW_EDITING == false )
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Editing Not Alloded In Demo", "carspot-rest-api") );
			return $response;
		}		
		
		if( email_exists( $email ) == true )
		{
			$my_theme = wp_get_theme();
			if( $my_theme->get( 'Name' ) != 'carspot' && $my_theme->get( 'Name' ) != 'carspot child' )
			{			
				$response = 	carspotAPI_forgot_pass_email_text($email);
			}
			else
			{
				$response = 	carspotAPI_forgot_pass_email_link($email);
			}

		}
		else
		{
			$success = false;
			$message =  __( 'Email is not resgistered with us.', 'carspot-rest-api' );	
			$response = array( 'success' => $success, 'data' => '' , 'message' => $message );			
		}
		return $response;
		
	}
}

/* Account Confirmation */
add_action( 'rest_api_init', 'carspotAPI_login_confirm_api_hooks_get', 0 );
function carspotAPI_login_confirm_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/login/confirm/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_login_confirm_get',
				/*'permission_callback' => function () { return carspotAPI_basic_auth();  },*/
        	)
    );
    register_rest_route( 'carspot/v1', '/login/confirm/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_login_confirm_post',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );	
}
if( !function_exists('carspotAPI_login_confirm_get' ) )
{
	function carspotAPI_login_confirm_get()
	{		
		$data['bg_color']			= '#000';
		$data['logo']				= carspotAPI_appLogo();
		$data['heading']			=  __("Account Confirmation", "carspot-rest-api");
		$data['text']				=  __("Please enter your confirmation code below.", "carspot-rest-api");
		$data['confirm_placeholder']=  __("Confirmation Code Here", "carspot-rest-api");		
		$data['submit_text']		=  __("Confirm Account", "carspot-rest-api");
		$data['back_text']			=  __("Back", "carspot-rest-api");
				
		return $response = array( 'success' => true, 'data' => $data, 'message'  => ''  );		
	}
}

if( !function_exists('carspotAPI_login_confirm_post' ) )
{
	function carspotAPI_login_confirm_post($request)
	{	
			
		$json_data = $request->get_json_params();
		$confirm_code 			= (isset($json_data['confirm_code'])) 		? $json_data['confirm_code'] : '';
		$user_id 				= (isset($json_data['user_id'])) 		? $json_data['user_id'] : '';

		if( $user_id == "" )
		{
			$message =  __( 'Invalid Access', 'carspot-rest-api' );	
			return $response = array( 'success' => false, 'data' => '', 'message'  => $message  );
		}

		if( $confirm_code == "" )
		{
			$message =  __( 'Please enter the confirmation code.', 'carspot-rest-api' );	
			return $response = array( 'success' => false, 'data' => '', 'message'  => $message  );
		}
		
		$token = get_user_meta($user_id, 'sb_email_verification_token', true);
		
		
		if( $token && $confirm_code != $token )
		{
			$message =  __( 'You eneter invalid confirmation code.', 'carspot-rest-api' );	
			return $response = array( 'success' => false, 'data' => '', 'message'  => $message  );
		}
		else if(  $token && $confirm_code == $token )
		{
			update_user_meta($user_id, 'sb_email_verification_token', '');

			/*Set the user's role after email verification .*/
			$user = new WP_User( $user_id );
			$user->set_role( 'subscriber' );
				
			$message =  __( 'You account confirmed successfully.', 'carspot-rest-api' );	
			return $response = array( 'success' => true, 'data' => '', 'message'  => $message  );
		}		
		else
		{
			$message =  __( 'Invalid Access or token code.', 'carspot-rest-api' );	
			return $response = array( 'success' => false, 'data' => '', 'message'  => $message  );
		}

	}
}