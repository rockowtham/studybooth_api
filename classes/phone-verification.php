<?php /*----- 	phone_verification Starts Here	 -----*/
add_action( 'rest_api_init', 'adforestAPI_phone_verification_hook', 0 );
function adforestAPI_phone_verification_hook() {
    register_rest_route(
        		'adforest/v1', '/profile/phone_number/', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'adforestAPI_verification_system',
				'permission_callback' => function () { return adforestAPI_basic_auth();  },
        	)
    );
} 

if ( ! function_exists( 'adforestAPI_verification_system' ) ) 
{
	function adforestAPI_verification_system()
	{

		global $carspotAPI;
		$user 	 = wp_get_current_user();
		$user_id = $user->ID;
		$resend_text = '';
		$phone_numer = get_user_meta($user_id, '_sb_contact', true );
		$ph		=	sanitize_text_field($phone_numer);
		
		$code	=	mt_rand(100000, 500000);
		
		if(!preg_match("/\+[0-9]+$/", $ph)) 
		{
			$message = __('Please go to edit profile and update valid phone number +CountrycodePhonenumber.','adforest-rest-api');
			return  array( 'success' => false, 'data' => '', "message" => $message);	
		}
		$message_sent = true;
		if( isset( $carspotAPI['sb_resend_code'] ) && $carspotAPI['sb_resend_code'] != "" && get_user_meta($user_id, '_ph_code_', true) != "" )
		{
			$timeFirst  = strtotime(get_user_meta($user_id, '_ph_code_date_', true));
			$timeSecond = strtotime(date('Y-m-d H:i:s'));
			$differenceInSeconds = $timeSecond - $timeFirst;
			
			if( $carspotAPI['sb_resend_code'] > $differenceInSeconds )
			{
				$message_sent = false;
				$after_seconds	=	$carspotAPI['sb_resend_code'] - $differenceInSeconds;			
				$success = false;
				$message =  __( "You can't resend the verification code before", 'adforest-rest-api' ) . " " . $after_seconds .' ' . __( "seconds.", 'adforest-rest-api' );
				
				$data['code'] = $code;
				$data['resend'] = array("time" => $after_seconds,"text" => $message );
				
				$response = array( 'success' => $success, 'data' => $data, "message" => $message);	
				return $response;	
			}
			
		}
	
		$res	=	adforestAPI_send_sms($ph, $code);
		if( isset($res) && count((array)$res) > 0 && @$res->sid && $message_sent)
		{
			/*if( true ){*/
			update_user_meta($user_id, '_ph_code_', $code);
			update_user_meta( $user_id, '_sb_is_ph_verified', '0' );
			update_user_meta( $user_id, '_ph_code_date_', date('Y-m-d H:i:s') );
			$success = true;
			$message = __( "Verification code has been sent.", "adforest-rest-api" );
		}
		else
		{		
			$success = false;
			$message = __( "Server not responding.", "adforest-rest-api" );
			update_user_meta( $user_id, '_sb_is_ph_verified', '0' );
		}	
		
			$timeFirst  = strtotime(get_user_meta($user_id, '_ph_code_date_', true));
			$timeSecond = strtotime(date('Y-m-d H:i:s'));
			$differenceInSeconds = $timeSecond - $timeFirst;
			$sb_resend_code = (isset($carspotAPI['sb_resend_code']) && $carspotAPI['sb_resend_code'] ) ? $carspotAPI['sb_resend_code'] : 10;
			$resend_text = $after_seconds = '';
			if( $sb_resend_code > $differenceInSeconds )
			{
				$after_seconds	=	$sb_resend_code - $differenceInSeconds;			
				$resend_text =  __( "You can't resend the verification code before", 'adforest-rest-api' ) . " " . $after_seconds .' ' . __( "seconds.", 'adforest-rest-api' );
			}

		
		$data['code'] = $code;
		$data['resend'] = array("time" => $after_seconds,"text" => $resend_text );
		
		$response = array( 'success' => $success, 'data' => $data, "message" => $message);	
		return $response;	
	
	}
}

add_action( 'rest_api_init', 'adforestAPI_verification_code_hook', 0 );
function adforestAPI_verification_code_hook() {
    register_rest_route(
        		'adforest/v1', '/profile/phone_number/verify/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'adforestAPI_verification_code',
				'permission_callback' => function () { return adforestAPI_basic_auth();  },
        	)
    );
} 

if ( ! function_exists( 'adforestAPI_verification_code' ) ) 
{
	function adforestAPI_verification_code($request)
	{
		$user 	 	= wp_get_current_user();
		$user_id 	= $user->ID;
		
		$json_data 	= $request->get_json_params();
		$code 		= (isset( $json_data['verify_code'] ) ) ?  $json_data['verify_code'] : '';	
		$saved	=	get_user_meta($user_id, '_ph_code_', true);
		if( $saved == "" )
		{
			$success = false;
			$message = __( "Code not found.", "adforest-rest-api" );
			
		}
		else if( $code == $saved )
		{
			update_user_meta($user_id, '_ph_code_', '');
			update_user_meta( $user_id, '_sb_is_ph_verified', '1' );
			update_user_meta( $user_id, '_ph_code_date_', '' );
			$success = true;
			$message = __( "Phone number has been verified.", "adforest-rest-api" );
			
		}
		else
		{
			$success = false;
			$message = __( "Invalid code that you entered.", "adforest-rest-api" );
		}

		$number_verified = get_user_meta( $user_id, '_sb_is_ph_verified', '1' );
		$number_verified_text = ( $number_verified && $number_verified == 1 ) ? __("verified", "adforest-rest-api") : __("Not verified", "adforest-rest-api");
		
		$is_verification_on = true;
		$number_verified = get_user_meta( $user->ID, '_sb_is_ph_verified', '1' );
		$number_verified_text = ( $number_verified && $number_verified == 1 ) ? __("verified", "adforest-rest-api") : __("Not verified", "adforest-rest-api");
		
		$data['is_number_verified'] = ( $number_verified && $number_verified == 1 ) ? true : false;
		$data['is_number_verified_text'] = $number_verified_text;
		
		$response = array( 'success' => $success, 'data' => $data, "message" => $message);	
		return $response;	
	}
}
if ( !function_exists( 'adforestAPI_send_sms' ) ) 
{
	function adforestAPI_send_sms($receiver_ph, $code)
	{
		global $carspotAPI;
		
		$twl_data 				= get_option( 'twl_option' );
		if( isset( $twl_data  ) && $twl_data != "" )
		{
			$account_sid 		 = $twl_data['account_sid'];
			$auth_token 		 = $twl_data['auth_token'];
			$twilio_phone_number = $twl_data['number_from'];
			if( $account_sid != "" && $auth_token != "" && $twilio_phone_number != "" )
			{
				$message				= __('Your verification code is', 'adforest-rest-api') . " " . $code;
								
				try {
						$client = new Twilio\Rest\Client( $account_sid, $auth_token );
						$response	= $client->messages->create(
						$receiver_ph,
						array(
							"from" => $twilio_phone_number,
							"body" => $message
							)
						);
				} catch (Exception $e) {
					$response = array();	
					//echo 'Caught exception: ',  $e->getMessage(), "\n";
				}	
				return $response;
			}
			else
			{
				return __('Something went wrong please check later', 'adforest-rest-api') . " " . $code;	
			}
		}
		else
		{
			return __('Something went wrong please check later', 'adforest-rest-api') . " " . $code;	
		}		
	}
}