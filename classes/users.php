<?php
/* Adds all user meta to the /wp-json/wp/v2/user/[id] endpoint */
function sb_user_meta_update( $data, $field_name, $request ) 
{
	if( $data['id'] )
	{ 
		$user_meta = get_user_meta( $data['id'] ); 
	}
	$data = array("email", "profile");
	
	return $field_name;
}
function sb_user_meta1( $data, $field_name, $request ) 
{
	
	$profile_arr = array();
	if( $data['id'] ){
		
		$userID	   = $data['id'];
		$profile_arr = array();
		$profile_arr['phone']			= get_user_meta( $userID, '_sb_contact', true );
		$profile_arr['profile_img']		= carspotAPI_user_dp( $userID );
		$profile_arr['expire_ads'] 		= get_user_meta( $userID, '_sb_expire_ads', true );				
		$profile_arr['simple_ads'] 		= get_user_meta( $userID, '_sb_simple_ads', true );
		$profile_arr['featured_ads'] 	= get_user_meta( $userID, '_sb_featured_ads', true );
		$profile_arr['package_type'] 	= get_user_meta( $userID, '_sb_pkg_type', true);	
		$profile_arr['last_login'] 		= carspotAPI_getLastLogin( $userID );		
		
		$profile_arr['active'] 	 		= carspotApi_userAds( $userID, 'active', '', -1);
		$profile_arr['expired']	 		= carspotApi_userAds( $userID, 'expired', '', -1);
		$profile_arr['sold'] 	 		= carspotApi_userAds( $userID, 'sold', '', -1);
		$profile_arr['featured'] 		= carspotApi_userAds( $userID, 'active', 1, -1);
		
	}
	
	return $profile_arr;
}



add_action( 'rest_api_init', 'carspotAPI_sb_user_meta_update_hook');
function carspotAPI_sb_user_meta_update_hook() {
	
	
    register_rest_field( 'user', 'meta', array(
            'get_callback'    => 'sb_user_meta1',
            'update_callback' => 'sb_user_meta_update',
            'schema'          => null,
        )
    );
} 


add_action( 'rest_api_init', 'carspotAPI_sb_user_email_hook');
function carspotAPI_sb_user_email_hook() {
	
   register_rest_field( 'user', 'info', array(
            'get_callback'    => 'sb_user_email',
            'update_callback' => null,
            'schema'          => null,
        )
    );
} 



function sb_user_email($data, $field_name, $request)
{
	return $data['email'];	
}
/*add_filter( 'rest_prepare_user', 'get_all_users', 10, 3 );*/
function get_all_users( $data, $field_name, $request ) {
	
		$user_info 								= wp_get_current_user();
		$userID	   								= $user_info->ID;
		/*User Profile*/
		$profile['profile']['userID']  	  		= $userID;
		$profile['profile']['user_login']  	  	= $user_info->user_login;
		$profile['profile']['user_email']  		= $user_info->user_email;
		$profile['profile']['display_name']  	= $user_info->display_name;
		$profile['profile']['user_nicename']  	= $user_info->user_nicename;		
		$profile['profile']['user_registered']  = $user_info->user_registered;
		/*User Meta*/
		$profile['meta']['phone']				= get_user_meta($userID, '_sb_contact', true );
		$profile['meta']['profile_img']			= carspotAPI_user_dp( $userID);
		$profile['meta']['expire_ads'] 			= get_user_meta( $userID, '_sb_expire_ads', true );				
		$profile['meta']['simple_ads'] 			= get_user_meta( $userID, '_sb_simple_ads', true );
		$profile['meta']['featured_ads'] 		= get_user_meta( $userID, '_sb_featured_ads', true );
		$profile['meta']['package_type'] 		= get_user_meta( $userID, '_sb_pkg_type', true);	
		$profile['meta']['last_login'] 			= carspotAPI_getLastLogin( $userID );		
		$response = array( 'success' => true, 'data' => $profile );	
		return $response;					
}




add_action( 'rest_api_init', 'carspotAPI_profile_block_user_hook', 0 );
function carspotAPI_profile_block_user_hook() {

    register_rest_route(
			'carspot/v1', '/user/block/', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => 'carspotAPI_profile_block_user_get',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );
    register_rest_route(
			'carspot/v1', '/user/block/', array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_profile_block_user_post',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );

    register_rest_route(
			'carspot/v1', '/user/unblock/', array(
			'methods'  => WP_REST_Server::EDITABLE,
			'callback' => 'carspotAPI_profile_unblock_user_post',
			'permission_callback' => function () { return carspotAPI_basic_auth();  },
		)
    );	
}  
 
if (!function_exists('carspotAPI_profile_block_user_get'))
{
	function carspotAPI_profile_block_user_get()
	{ 
	    $get_current_user_id = get_current_user_id();
		$blocked = get_user_meta($get_current_user_id, '_sb_carspot_block_users', true );
		
		$users = array();
		if(isset($blocked) && $blocked != "" && count( (array)$blocked) > 0 )
		{
			$count_row = 0;
			$blocked = array_reverse($blocked);			
			foreach( (array)$blocked as $block )
			{
				$userdata = get_user_by( 'ID', $block );
				if($userdata)
				{
					$users[$count_row]['id'] = $block; 
					$users[$count_row]['name'] = $userdata->display_name; 
					$users[$count_row]['image'] = carspotAPI_user_dp( $block);
					$users[$count_row]['text'] = __("Unblock", "carspot-rest-api");
					$users[$count_row]['location'] = get_user_meta( $block, '_sb_address', true);
					$count_row++;
				}
				
			}
		
		}
		$data = array();
		$data['page_title'] = __("Blocked Users List", "carspot-rest-api");;
		$data['users'] = $users;
		
		$message = (count($users) > 0 ) ? '' : __("No user in block list", "carspot-rest-api");
		
		return array( 'success' => true, 'data' => $data, 'message'  => $message);
	}
}

if (!function_exists('carspotAPI_profile_block_user_post'))
{
	function carspotAPI_profile_block_user_post($request)
	{ 

		$get_current_user_id = get_current_user_id();
		$json_data   = $request->get_json_params();
		$user_id 	     = (isset( $json_data['user_id'] ) && $json_data['user_id'] != "" ) ? $json_data['user_id'] : '';
		
		/*if( true )*/
		if( $get_current_user_id != $user_id )
		{
		
			$blocked_list = get_user_meta($get_current_user_id, '_sb_carspot_block_users', true);
			if(isset($blocked_list) && $blocked_list != "" )
			{
				if(!in_array($user_id, $blocked_list) )
				{
					array_push($blocked_list, $user_id);
				}
			}
			else
			{
				$blocked_list = array($user_id);
			}
		
			$blocked = update_user_meta($get_current_user_id, '_sb_carspot_block_users', $blocked_list );
			if( $blocked )
			{
				$success  = true;
				$message  = __("User blocked successfully. You can unblock this user from profile.", "carspot-rest-api");
			}
			else
			{
				$success  = false;
				$message  = __("Something went wrong or user already in blok list.", "carspot-rest-api");			
			}
		}
		else
		{
				$success  = false;
				$message  = __("You can not block yourself.", "carspot-rest-api");			
		}
		
		return array( 'success' => $success, 'data' => '', 'message'  => $message);
	}
}


if (!function_exists('carspotAPI_profile_unblock_user_post'))
{
	function carspotAPI_profile_unblock_user_post($request)
	{ 

		$get_current_user_id = get_current_user_id();
		$json_data   = $request->get_json_params();
		$user_id 	     = (isset( $json_data['user_id'] ) && $json_data['user_id'] != "" ) ? $json_data['user_id'] : '';
		
		$blocked_list = get_user_meta($get_current_user_id, '_sb_carspot_block_users', true);
		if(isset($blocked_list) && $blocked_list != "" )
		{
			if(in_array($user_id, $blocked_list) )
			{
				foreach($blocked_list as $key => $list )
				{
					if( $user_id == $list )
					{
						unset($blocked_list[$key]);
						break;
					}
				}
			}
		}
		
		$update_saved = 
		$blocked      = update_user_meta($get_current_user_id, '_sb_carspot_block_users', $blocked_list );
		if( $blocked )
		{
			$success  = true;
			$message  = __("User unblocked successfully.", "carspot-rest-api");
		}
		else
		{
			$success  = false;
			$message  = __("Something went wrong or user already unbloked.", "carspot-rest-api");			
		}
		
		
		return array( 'success' => $success, 'data' => '', 'message'  => $message);
	}
}