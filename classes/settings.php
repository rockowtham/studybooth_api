<?php
/* ----
	Settings Starts Here
----*/
add_action( 'rest_api_init', 'carspotAPI_settings_api_hooks_get', 0 );
function carspotAPI_settings_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/settings/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_settings_me_get',
				/*'permission_callback' => function () { return carspotAPI_basic_auth();  },*/
        	)
    );
}

if( !function_exists('carspotAPI_settings_me_get' ) )
{
	function carspotAPI_settings_me_get()
	{
		global $carspotAPI;
		
		
		
		$app_is_open = (isset( $carspotAPI['app_is_open'] ) && $carspotAPI['app_is_open'] == true) ? true : false;
		$data['is_app_open']					= $app_is_open;
		$data['heading']						= __("Register With Us!", "carspot-rest-api");		
		$data['internet_dialog']['title']		= __("Error", "carspot-rest-api");
		$data['internet_dialog']['text']		= __("Internet not found", "carspot-rest-api");
		$data['internet_dialog']['ok_btn']		= __("Ok", "carspot-rest-api");
		$data['internet_dialog']['cancel_btn']	= __("Cancel", "carspot-rest-api");
		
		$data['alert_dialog']['message']		= __("Are you sure you want to do this?", "carspot-rest-api");
		$data['alert_dialog']['title']			= __("Alert!", "carspot-rest-api");
		
		$data['search']['text']					=  __("Search Here", "carspot-rest-api");
		$data['search']['input']				=  'ad_title';/*Static name For field name*/		
		$data['cat_input']						=  'ad_cats1';/*Static name For categories*/
		$data['message']						= __("Please wait!", "carspot-rest-api");
		
		/*Options Coming From Theme Options*/
		
		$gmap_lang 				= (isset( $carspotAPI['gmap_lang'] ) && $carspotAPI['gmap_lang'] == true) ? $carspotAPI['gmap_lang'] : 'en';
		$data['gmap_lang']		= $gmap_lang;

		$is_rtl 				= (isset( $carspotAPI['app_settings_rtl'] ) && $carspotAPI['app_settings_rtl'] == true) ? true : false;
		$data['is_rtl']			= $is_rtl;

		$app_color  			= (isset( $carspotAPI['app_settings_color'] ) ) ? $carspotAPI['app_settings_color'] : '#f58936';
		$data['main_color']		= $app_color;

		$sb_location_type  			= (isset( $carspotAPI['sb_location_type'] ) ) ? $carspotAPI['sb_location_type'] : 'cities';
		$data['location_type']		= $sb_location_type;

		/*Some App Keys From Theme Options 
		$data['appKey']['stripe']		= (isset( $carspotAPI['appKey_stripeKey'] ) ) ? $carspotAPI['appKey_stripeKey'] : '';		
		$data['appKey']['paypal']		= (isset( $carspotAPI['appKey_paypalKey'] ) ) ? $carspotAPI['appKey_paypalKey'] : '';*/
		
		$data['registerBtn_show']['google'] = (isset( $carspotAPI['app_settings_google_btn'] ) && $carspotAPI['app_settings_google_btn'] == true ) ? true : false;
		$data['registerBtn_show']['facebook']	= (isset( $carspotAPI['app_settings_fb_btn'] ) && $carspotAPI['app_settings_fb_btn'] == true ) ? true : false;
		
		
		$data['dialog']['confirmation']	=  array(	
			"title" => __("Confirmation", "carspot-rest-api"),
			"text" => __("Are you sure you want to do this.", "carspot-rest-api"),
			"btn_no" => __("Cancel", "carspot-rest-api"),
			"btn_ok" => __("Confirm", "carspot-rest-api"),
		);
		
		$data['notLogin_msg']	= __("Please login to perform this action.", "carspot-rest-api");
		
		$enable_featured_slider_scroll = (isset( $carspotAPI['sb_enable_featured_slider_scroll'] ) && $carspotAPI['sb_enable_featured_slider_scroll'] == true ) ? true : false;
		
		$data['featured_scroll_enabled'] = $enable_featured_slider_scroll;
		if($enable_featured_slider_scroll)
		{
			$data['featured_scroll']['duration'] = (isset( $carspotAPI['sb_enable_featured_slider_duration'] ) && $carspotAPI['sb_enable_featured_slider_duration'] == true ) ? $carspotAPI['sb_enable_featured_slider_duration'] : 40;
			
			$data['featured_scroll']['loop'] = (isset( $carspotAPI['sb_enable_featured_slider_loop'] ) && $carspotAPI['sb_enable_featured_slider_loop'] == true ) ? $carspotAPI['sb_enable_featured_slider_loop'] : 2000;
		}
		
		$data['location_popup']['slider_number'] = 250;
		$data['location_popup']['slider_step'] = 5;
		$data['location_popup']['text'] = __("Select distance in (KM)", "carspot-rest-api");
		$data['location_popup']['btn_submit'] = __("Submit", "carspot-rest-api");
		$data['location_popup']['btn_clear'] = __("Clear", "carspot-rest-api");

		/*App GPS Section Starts */
		$allow_near_by = (isset( $carspotAPI['allow_near_by'] ) && $carspotAPI['allow_near_by'] ) ? true : false;
		$data['show_nearby'] = $allow_near_by;
		$data['gps_popup']['title'] = __("GPS Settings", "carspot-rest-api");
		$data['gps_popup']['text'] = __("GPS is not enabled. Do you want to go to settings menu?", "carspot-rest-api");
		$data['gps_popup']['btn_confirm'] = __("Settings", "carspot-rest-api");
		$data['gps_popup']['btn_cancel'] = __("Cancel", "carspot-rest-api");
		/*App GPS Section Ends */

		/*App Rating Section Starts */
		$allow_app_rating 		= (isset( $carspotAPI['allow_app_rating'] ) && $carspotAPI['allow_app_rating'] ) ? true : false;
		
		$allow_app_rating_title 	= (isset( $carspotAPI['allow_app_rating_title'] ) && $carspotAPI['allow_app_rating_title'] != "" ) ? $carspotAPI['allow_app_rating_title'] : __("App Store Rating", "carspot-rest-api");
		
		$allow_app_rating_url 	= (isset( $carspotAPI['allow_app_rating_url'] ) && $carspotAPI['allow_app_rating_url'] != "" ) ? $carspotAPI['allow_app_rating_url'] : '';
				
		$data['app_rating']['is_show'] 		= $allow_app_rating;
		$data['app_rating']['title'] 		= $allow_app_rating_title;
		
		$data['app_rating']['btn_confirm'] 	= __("Maybe Later", "carspot-rest-api");
		$data['app_rating']['btn_cancel'] 	= __("Never", "carspot-rest-api");
		$data['app_rating']['url'] 			= $allow_app_rating_url;
		/*App Rating Section Ends */


		/*App Share Section Starts */
		$allow_app_share = (isset( $carspotAPI['allow_app_share'] ) && $carspotAPI['allow_app_share'] ) ? true : false;		
		$allow_app_share_title 	= (isset( $carspotAPI['allow_app_share_title'] ) && $carspotAPI['allow_app_share_title'] != "" ) ? $carspotAPI['allow_app_share_title'] : __("Share this", "carspot-rest-api");
		$allow_app_share_text 	= (isset( $carspotAPI['allow_app_share_text'] ) && $carspotAPI['allow_app_share_text'] != "" ) ? $carspotAPI['allow_app_share_text'] : '';
		$allow_app_share_url = (isset( $carspotAPI['allow_app_share_url'] ) && $carspotAPI['allow_app_share_url'] != "" ) ? $carspotAPI['allow_app_share_url'] : '';
		
		$data['app_share']['is_show'] 		= $allow_app_share;
		$data['app_share']['title'] 		= $allow_app_share_title;
		$data['app_share']['text'] 			= $allow_app_share_text;
		$data['app_share']['url'] 			= $allow_app_share_url;
		/*App Share Section Ends */
		
		$sb_user_guest_dp = CARSPOT_API_PLUGIN_URL."images/user.jpg";
		if( carspotAPI_getReduxValue('sb_user_guest_dp', 'url', true) )
		{
			$sb_user_guest_dp = carspotAPI_getReduxValue('sb_user_guest_dp', 'url', false);	
		}

		$data['guest_image'] = $sb_user_guest_dp;
		$data['guest_name']  = __("Guest", "carspot-rest-api");
		
		
		$has_value = false;
		$array_sortable = array();
		if(isset( $carspotAPI['home-screen-sortable'] ) && $carspotAPI['home-screen-sortable'] > 0 )
		{
			
			$array_sortable = $carspotAPI['home-screen-sortable'];
			foreach( $array_sortable as $key => $val )
			{
				if( isset($val)  && $val != "" )
				{
					$has_value = true;
				}
			}
		}
		$data['ads_position_sorter'] =  $has_value;

		$data['menu'] 	= carspotAPI_appMenu_settings();
		
		
		
		
		
		
		$data['messages_screen']['main_title'] 		= __("Messages", "carspot-rest-api");
		$data['messages_screen']['sent'] 			= __("Sent Offers", "carspot-rest-api");
		$data['messages_screen']['receive']			= __("Offers on Ads", "carspot-rest-api");
				
				
	$data['gmap_has_countries'] = false;
	if( isset( $carspotAPI['sb_location_allowed'] ) && $carspotAPI['sb_location_allowed'] == false && isset ($carspotAPI['sb_list_allowed_country'] ) )
	{
		$data['gmap_has_countries'] = true;
		$lists = $carspotAPI['sb_list_allowed_country'];
		/*$countries = array(); foreach( $lists as $list ) { $countries[] = $list; }*/
		$data['gmap_countries'] = $lists;
	}				
			
			
		$data['app_show_languages'] = false;		
		$languages = array();	
		/*$languages[] = array("key" => "en", "value" => "English", "is_rtl" => false);
		$languages[] = array("key" => "ar", "value" => "Arabic", "is_rtl" => true);
		$languages[] = array("key" => "ro_RO", "value" => "RO Lang", "is_rtl" => false);*/
		if( count($languages) > 0 )
		{
			$data['app_text_title'] = __("Select or Search Language", "carspot-rest-api");	
			$data['app_text_close'] = __("Close", "carspot-rest-api");	
			$data['app_show_languages'] = true;	
			$data['app_languages'] = $languages;	
		}	
	
		$data['allow_block'] = (isset( $carspotAPI['sb_user_allow_block'] ) && $carspotAPI['sb_user_allow_block']) ? true : false;		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => ''  );		
		
	}
}

if( !function_exists('carspotAPI_is_app_open' ) )
{
	function carspotAPI_is_app_open()
	{
		global $carspotAPI;
		
		$app_is_open = (isset( $carspotAPI['app_is_open'] ) && $carspotAPI['app_is_open'] == true) ? true : false;
		$data['is_app_open']					= $app_is_open;
	}
}