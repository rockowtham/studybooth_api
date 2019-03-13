<?php /*** Add REST API support to an already registered post type. */
add_action( 'init', 'my_custom_post_type_rest_support', 25 );
  function my_custom_post_type_rest_support() {
  	global $wp_post_types;
  
  	//be sure to set this to the name of your post type!
  	$post_type_name = 'ad_post';
  	if( isset( $wp_post_types[ $post_type_name ] ) ) 
	{
  		$wp_post_types[$post_type_name]->show_in_rest = true;
  		$wp_post_types[$post_type_name]->rest_base = $post_type_name;
  		$wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
  	}
  
  }	
  
add_action( 'rest_api_init', 'carspotAPI_profile_api_ads_hooks_get', 0 );
function carspotAPI_profile_api_ads_hooks_get() {

    register_rest_route(
        'carspot/v1', '/ad_post/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_ad_posts_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
}  
  
if( !function_exists('carspotAPI_ad_posts_get' ) )
{
function carspotAPI_ad_posts_get( $request )
{
	global  $carspotAPI;
	
	$json_data = $request->get_json_params();	
	$ad_id 	   = (isset($json_data['ad_id'])) ? $json_data['ad_id'] : '';

	$user    = wp_get_current_user();
	$user_id = ( @$user ) ? @$user->data->ID : '';
		
	$post 			=  get_post( $ad_id );
	$ad_post_author = get_post_field( 'post_author', $post->ID );

	/*Expiration of ad starts */
	$has_ad_expired = false;
	if( isset($carspotAPI['simple_ad_removal']) && $carspotAPI['simple_ad_removal'] != '-1' )
	{
			$now = strtotime( current_time('mysql'));/*time(); // or your date as well*/
			$simple_date	= strtotime(get_the_date('Y-m-d', $post->ID));
			$simple_days	= carspotAPI_days_diff( $now, $simple_date );
			$expiry_days	= $carspotAPI['simple_ad_removal'];
			if( $simple_days > $expiry_days )
			{
				$has_ad_expired = true;
				wp_trash_post($ad_id);
			}
					
	}
	if( get_post_meta($ad_id, '_carspot_is_feature', true ) == '1' && $carspotAPI['featured_expiry'] != '-1' )
	{
		if(isset( $carspotAPI['featured_expiry'] ) &&  $carspotAPI['featured_expiry'] != '-1' )
		{
			$now = strtotime( current_time('mysql'));/*time(); // or your date as well*/
			$featured_date	= strtotime(get_post_meta( $ad_id, '_carspot_is_feature_date', true ));

			$featured_days	= carspotAPI_days_diff( $now, $featured_date );
			$expiry_days	=	$carspotAPI['featured_expiry'];
			if( $featured_days > $expiry_days )
			{
				update_post_meta( $ad_id, '_carspot_is_feature', 0 );
			}
		}
	}
	/*Expiration of ad ends */		
	$data = '';
	if( !$post && @count( $post ) == 0 ) 
		$response = array( 'success' => false, 'data' => $data, 'message'  => __("'Invalid post id'", "carspot-rest-api") );
	
	$post_categories = wp_get_object_terms( $ad_id,  array('ad_cats'), array('orderby' => 'term_group') );
	foreach($post_categories as $c)
	{
		$cat = get_term( $c );
		$cat_name = esc_html( $cat->name );
	}
	
	$description 					= trim(preg_replace('/\s+/',' ', $post->post_content ));
	$ad_detail['ad_author_id']		= get_post_field( 'post_author', $post->ID );
	$ad_detail['ad_id'] 			= $post->ID;
	$ad_detail['ad_cat'] 			= $cat_name;
	$ad_detail['ad_title'] 			= $post->post_title;
	$ad_detail['ad_desc'] 			= $description;
	$ad_detail['ad_date'] 			= get_the_date("", $post->ID);	
	$ad_detail['ad_price'] 			= carspotAPI_get_price( '', $post->ID );
	$ad_detail['phone'] 			= $poster_phone 	= get_post_meta( $post->ID, '_carspot_poster_contact', true );
	$ad_detail['name'] 				= $poster_name  	= get_post_meta( $post->ID, '_carspot_poster_name', true  );
	$ad_detail['ad_bidding'] 		= get_post_meta( $post->ID, '_carspot_ad_bidding', true  );
	$ad_detail['featured_ads'] 		= get_post_meta( $post->ID,  '_sb_featured_ads', true  );	
	$ad_detail['expire_date'] 		= get_post_meta( $post->ID,  '_sb_expire_ads', true  );		
	$ad_detail['ad_status'] 		= get_post_meta( $post->ID, '_carspot_ad_status_', true );
	$ad_detail['ad_timer'] 		    = carspotAPI_get_adTimer($post->ID);
	
	$ad_detail['ad_type_bar']['is_show'] = false;
	if( get_post_meta($post->ID, '_carspot_ad_type', true ) != "" ) {
		$ad_detail['ad_type_bar']['is_show'] = true;
		$ad_detail['ad_type_bar']['text'] = get_post_meta($post->ID, '_carspot_ad_type', true );
	}
	
	
	
	$ad_detail['is_feature'] = ( $is_feature_ads == 1 ) ? true : false;
	$ad_detail['is_feature_text'] = ( $is_feature_ads == 1 ) ? __("Featured", "carspot-rest-api") : '';
	/*setPostViews*/
	carspotAPI_setPostViews( $post->ID );
	$viewCount = get_post_meta($post->ID, "sb_post_views_count", true);
	$viewCount = ( $viewCount != "" ) ? $viewCount : 0;
	$ad_detail['ad_view_count'] 	= $viewCount;
	
	//$ad_currency_count  = wp_count_terms( 'ad_currency' );
	//if( isset($ad_currency_count) && $ad_currency_count > 0 )
	//{
		//$ad_detail['ad_currency'] 		= carspotAPI_get_ad_terms($post->ID, 'ad_currency','',  __("Ad Currency", "carspot-rest-api"));
	//}
	//$ad_detail['ad_cats'] 			= carspotAPI_get_ad_terms($post->ID, 'ad_cats','',  __("Categories", "carspot-rest-api"));
	//$ad_detail['ad_tags'] 			= carspotAPI_get_ad_terms($post->ID, 'ad_tags','',  __("Tags", "carspot-rest-api"));
	
	//$ad_tags_show	= carspotAPI_get_ad_terms_names($ad_id, 'ad_tags', '', '', $separator = ',');
	//$ad_tags_show_name = ( $ad_tags_show != "" ) ? __("Tags", "carspot-rest-api") : "";
	//$ad_detail['ad_tags_show'] 		= array("name" => $ad_tags_show_name, "value" => $ad_tags_show);
	
	$ad_detail['ad_video'] 			= carspotAPI_get_adVideo($post->ID);	
	$myAdLocation = carspotAPI_get_adAddress($post->ID);
	$ad_detail['location'] 			= $myAdLocation;
	
	$ad_myCountry1 = (isset($myAdLocation['address']) && $myAdLocation['address'] != "" ) ? $myAdLocation['address'] : '';
	$is_show_location  = wp_count_terms( 'ad_country' );
	$ad_myCountry2 = '';
	if( isset($is_show_location) && $is_show_location > 0 )
	{	
		/*Some Location Code Goes Here */
		$ad_myCountry2 = carspotAPI_get_ad_terms_names($ad_id, 'ad_country', '', '', $separator = ',');
		//carspotAPI_terms_seprates_by($ad_id , 'ad_cats',  ', ');		
		//$dynamicData[] =  array("key" => __("Location", "carspot-rest-api"), "value" => $ad_country, "type" => '');
		
	
	}	
	$ad_myCountry = ( $ad_myCountry2 != "" ) ? $ad_myCountry2 : $ad_myCountry1;
	//$ad_detail['location_top'] 		= $ad_myCountry;
	$ad_detail['fieldsData_column']	= (isset( $carspotAPI['api_ad_details_info_column'] )) ? $carspotAPI['api_ad_details_info_column'] : 2;	
	$ad_detail['fieldsData_feature_txt']	=  __("Overview", "carspot-rest-api");
	$ad_detail['fieldsData']		= carspotAPI_get_customFields((int)$post->ID);
	
	//Car Features
	$ad_detail['car_features_title']	= __("Features", "carspot-rest-api");
	$ad_detail['car_features']	= array();
	$adfeatures = get_post_meta($post->ID, '_carspot_ad_features', true );
	if( isset( $adfeatures ) && $adfeatures != "" )
	{
		$features = explode('|', $adfeatures );
		if( count((array) $features ) > 0 )
			{
			foreach( $features as $feature )
				{
					$tax_feature = get_term_by('name', $feature, 'ad_features');
					if($tax_feature == true)
						{
							$cat_meta =  get_option( "taxonomy_term_$tax_feature->term_id" );
							$ad_detail['car_features'][]	= esc_html( $feature );
						}
				}
			}
	}
	
	
		

	/* Ad Owner Id */
	$ad_detail['author_id'] = get_post_field( 'post_author', $post->ID );

	/* Get ads images */
	$ad_detail['images']        = carspotAPI_get_ad_image($post->ID);
	$ad_detail['images_count']  =  __("See ".count(carspotAPI_get_ad_image($post->ID))." photos", "carspot-rest-api"); 
	//$ad_detail['slider_images'] = carspotAPI_get_ad_image_slider($post->ID);
	/* Related Articles Started */	
	/*$ad_detail['related_ads'] = array();
	$static_text['related_posts_title'] 	=	'';
	$getSimilar = carspotApi_related_ads($post->ID, 1);
	if(isset( $carspotAPI['related_ads_on'] ) && $carspotAPI['related_ads_on'] == true && count($getSimilar) > 0)
	{
		$rtitle = ($carspotAPI['sb_related_ads_title'] != "" ) ? $carspotAPI['sb_related_ads_title'] :__("Related Posts", "carspot-rest-api"); 
		$static_text['related_posts_title'] 	=	$rtitle;
		$relatedAds = (isset( $carspotAPI['api_ad_details_related_posts'] )) ? $carspotAPI['api_ad_details_related_posts'] : 5;
		$getSimilar = carspotApi_related_ads($post->ID, $relatedAds);
		$ad_detail['related_ads'] = $getSimilar;
	}*/
	/* Related Articles Ends*/
	
	/*carspotAPI_bidding_stats($ad_id)*/

	$profile_detail	                =   carspotAPI_basic_profile_data(get_post_field( 'post_author', $post->ID ));
	$static_text['share_btn'] 		=	__("Share", "carspot-rest-api");
	$static_text['fav_btn'] 		=	__("Add To Favourites", "carspot-rest-api");
	$static_text['report_btn'] 		=	__("Report", "carspot-rest-api");

	$send_msg_btn_type = ( $user_id == 	get_post_field( 'post_author', $post->ID )) ? 'receive' : 'sent';
	$send_msg_btn = ( $user_id == 	get_post_field( 'post_author', $post->ID )) ? __("View Messages", "carspot-rest-api") : __("Send Message", "carspot-rest-api");
	
	$static_text['send_msg_btn_type'] 		=	$send_msg_btn_type;
	$static_text['send_msg_btn'] 			=	$send_msg_btn;
	$static_text['call_now_btn'] 			=	__("Call Now", "carspot-rest-api");
		
	$communication_mode = (isset( $carspotAPI['communication_mode'] )) ? $carspotAPI['communication_mode'] : 'both';
	if( $communication_mode == 'phone' )
	{
		$show_call_btn = true;
		$show_megs_btn = false;
	}
	else if( $communication_mode == 'message' )
	{
		$show_call_btn = false;
		$show_megs_btn = true;
	}
	else
	{
		$show_call_btn = true;
		$show_megs_btn = true;
	}
	
	$static_text['show_call_btn'] 			=	$show_call_btn;
	$static_text['show_megs_btn'] 			=	$show_megs_btn;
	
	$bid_now_txt = ($user_id != get_post_field('post_author', $post->ID)) ? __("Bid Now", "carspot-rest-api") : __("View Bids", "carspot-rest-api");
	
	$static_text['bid_now_btn'] 			=	$bid_now_txt;
	$static_text['bid_stats_btn'] 			=	__("Bid Statistics", "carspot-rest-api");
	$static_text['bid_tabs']['bid'] 		=	__("Bidding", "carspot-rest-api");
	$static_text['bid_tabs']['stats'] 		=	__("Bid Statistics", "carspot-rest-api");
	$static_text['get_direction'] 			=	__("Get Direction", "carspot-rest-api");
	$static_text['description_title'] 		=	__("Description", "carspot-rest-api");
	

	$allow_block = (isset( $carspotAPI['sb_user_allow_block'] ) && $carspotAPI['sb_user_allow_block']) ? true : false;
	$static_text['block_user']['is_show'] = $allow_block;
	if($allow_block)
	{
		$static_text['block_user']['text']        = __("Block User", "carspot-rest-api");
		$static_text['block_user']['popup_title'] = __("Block User?", "carspot-rest-api");
		$static_text['block_user']['popup_text']  = __("Are you sure you want to block user. You will not see this user ads anywhere.", "carspot-rest-api");
		$static_text['block_user']['popup_cancel'] = __("Cancel", "carspot-rest-api");
		$static_text['block_user']['popup_confirm'] = __("Confrim", "carspot-rest-api");
	}
	/*Bids*/
	//$ad_detail['ad_bids'] = array("stats" => carspotAPI_bid_stat($post->ID), "offers" => carspotAPI_bids($post->ID));
	$is_bid_enabled = false;	
	if( isset( $carspotAPI['sb_enable_comments_offer'] ) && $carspotAPI['sb_enable_comments_offer'] )
	{
		$is_bid_enabled = true;	
		if( isset( $carspotAPI['sb_enable_comments_offer_user'] ) && $carspotAPI['sb_enable_comments_offer_user'] )
		{
			$is_bid_enabled = true;		
			$is_exist 	    = get_post_meta( $post->ID, "_carspot_ad_bidding", true );	
			$is_bid_enabled = ( $is_exist == 1 ) ? true : false;
		}
	}	

	$static_text['ad_bids_enable']          =  $is_bid_enabled ;
	$static_text['ad_bids']                 =  carspotAPI_bid_stat($post->ID);
	//$static_text['ad_bids_btn'] = __("Make A Bid", "carspot-rest-api");
	$bid_popup['bid_section_title'] 		=	__("Biddings", "carspot-rest-api");
	$bid_popup['no_bid'] 		=	__("Be the first bidder", "carspot-rest-api");
	
	$bid_popup['bid_details'] 		        =	carspotAPI_get_ad_bids1($post->ID,true);
	
	$bid_popup['input_text'] 				=	__("Bid Amount", "carspot-rest-api");
	$bid_popup['input_textarea']			=	__("Bid description here", "carspot-rest-api");
	$bid_popup['btn_send'] 					=	__("Send", "carspot-rest-api");
	$bid_popup['btn_cancel'] 				=	__("Cancel", "carspot-rest-api");

	$report_popup['select']['key'] 			= __("Select Option", "carspot-rest-api");
	$report_popup['select']['text'] 		= __("Why are you reporting this ad?", "carspot-rest-api");
	$report_popup['select']['name'] 		= array("offensive", "Spam","Duplicate",);
	$report_popup['select']['value'] 		= array("offensive", "Spam","Duplicate",);
	$report_popup['input_textarea'] 		= __("You message here.", "carspot-rest-api");
	$report_popup['btn_send'] 				= __("Send", "carspot-rest-api");
	$report_popup['btn_cancel'] 			= __("Cancel", "carspot-rest-api");

	$send_message['input_textarea'] 		= __("You message here.", "carspot-rest-api");
	$send_message['btn_send'] 				= __("Send", "carspot-rest-api");
	$send_message['btn_cancel'] 			= __("Cancel", "carspot-rest-api");

	$call_now['text'] 						= __("Call Now", "carspot-rest-api");
	$call_now['btn_send'] 					= __("Call Now", "carspot-rest-api");
	$call_now['btn_cancel'] 				= __("Cancel", "carspot-rest-api");
	
	$phone_verification = (isset( $carspotAPI['sb_phone_verification'] ) && $carspotAPI['sb_phone_verification'] ) ? true : false;
	
	$call_now['phone_verification'] = $phone_verification;
	if( $phone_verification )
	{	
		$is_phone_verified = false;
		$verified_text = __("Not verified", "carspot-rest-api");
		$ad_post_author_id = get_post_field( 'post_author', $post->ID );
		$saved_ph = get_user_meta( $ad_post_author_id, '_sb_contact', true );
		
		$adNum  = get_user_meta( $ad_post_author_id, '_sb_is_ph_verified', true );
		$adNumV = ( $adNum == 1 ) ? true : false;		
		
		if( $saved_ph == $poster_phone && $adNum == 1)
		{
			$is_phone_verified = true;
			$verified_text = __("verified", "carspot-rest-api");
		}
		
		
		$call_now['is_phone_verified'] = $is_phone_verified;
		$call_now['is_phone_verified_text'] = $verified_text;
	}	
	
	
	$share_info['title'] = $post->post_title;
	$share_info['link'] = get_the_permalink($post->ID);
	$share_info['text'] = __("Share this", "carspot-rest-api");
	
	$post_status = ( get_post_status( $post->ID ) != "publish" ) ? __("Waiting for admin approval.", "carspot-rest-api") : "";
	
	$featured_notify = carspotAPI_adFeatured_notify( $post->ID );
	$is_featured_ad['is_show'] = ( isset( $featured_notify )  && count( $featured_notify) > 0 ) ? true : false;
	if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	{
		/*SomeTHingNew*/
	}
	else
	{
		$is_featured_ad['is_show'] = false;
	}
	$is_featured_ad['notification'] = $featured_notify;
	
	
	//$rating_data = carspotAPI_adDetails_rating_get( $post->ID, 1, false );
	$ad_rating = carspotAPI_adDetails_rating_get( $ad_id, 1, false );
	
	
	$data = array(
			//"notification" 		=> $post_status,
			//"is_featured" 		=> $is_featured_ad,
			"page_title" 		=> __("Ad Details", "carspot-rest-api"),
			"ad_detail" 		=> $ad_detail, 
			"profile_detail" 	=> $profile_detail,  
			"static_text" 		=> $static_text,
			"bid_popup" 		=> $bid_popup,
			"report_popup" 		=> $report_popup,
			"message_popup" 	=> $send_message,
			"call_now_popup" 	=> $call_now,
			"share_info" 	    => $share_info,
			//"ad_ratting" 	    => $ad_rating,
			
		);

	$message_text = '';
	$success_typle = true;
	if( get_post_status( $post->ID ) != "publish" && $ad_post_author != $user_id && $has_ad_expired)
	{	
		$success_typle = false;
		$message_text = __("This ad is expired.", "carspot-rest-api");
	}
	
	$response = array( 'success' => $success_typle, 'data' => $data, 'message'  => $message_text );
	
	return $response;
}
}
/*add_filter( 'rest_prepare_ad_post', 'carspotAPI_ad_posts_get', 10, 3 );*/

/*Fav Ad*/
add_action( 'rest_api_init', 'carspotAPI_ad_favourite_hook', 0 );
function carspotAPI_ad_favourite_hook() {

    register_rest_route(
        'carspot/v1', '/ad_post/favourite/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_ad_favourite',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
}  


if( !function_exists('carspotAPI_ad_favourite' ) )
{
	function carspotAPI_ad_favourite( $request )
	{
		
		$json_data  = $request->get_json_params();	
		$ad_id		= (isset($json_data['ad_id'])) 		? $json_data['ad_id'] : '';

		$current_user	 = wp_get_current_user();	
		$current_user_id = $current_user->data->ID;			

		if( get_user_meta( $current_user_id, '_sb_fav_id_' . $ad_id, true ) == $ad_id )
		{
			return  array( 'success' => false, 'data' => '', 'message'  => __("You have added already.", "carspot-rest-api") );
		}
		else
		{
			update_user_meta( $current_user_id, '_sb_fav_id_' . $ad_id, $ad_id )	;
			return  array( 'success' => true, 'data' => '', 'message'  => __("Added to your favourites.", "carspot-rest-api") );
		}
		
	}
}

/*Report ad*/
add_action( 'rest_api_init', 'carspotAPI_ad_report_hook', 0 );
function carspotAPI_ad_report_hook() {

    register_rest_route(
        'carspot/v1', '/ad_post/report/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_ad_report',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
}  


if( !function_exists('carspotAPI_ad_report' ) )
{
	function carspotAPI_ad_report( $request )
	{

		global $carspotAPI;
		$json_data = $request->get_json_params();	
		$ad_id		= (isset($json_data['ad_id'])) 		? $json_data['ad_id'] : '';
		$option		= (isset($json_data['option'])) 	? $json_data['option'] : '';
		$comments	= (isset($json_data['comments'])) 	? $json_data['comments'] : '';
		
		
		
		$ad_owser = get_post_field( 'post_author', $ad_id );
		
		
		$message = '';
		$current_user	 = wp_get_current_user();	
		$current_user_id = $current_user->data->ID;			

		if( $ad_owser == $current_user_id)
		{
			return  array( 'success' => false, 'data' => '', 'message'  => __("You can't report your own ad", "carspot-rest-api") );
		}

		if( get_post_meta( $ad_id, '_sb_user_id_' .$current_user_id , true ) == $current_user_id )
		{
			return  array( 'success' => false, 'data' => '', 'message'  => __("You have reported already.", "carspot-rest-api") );
	
		}
		else
		{
			update_post_meta( $ad_id, '_sb_user_id_' . $current_user_id, $current_user_id );
			update_post_meta( $ad_id, '_sb_report_option_' . $current_user_id, $option );
			update_post_meta( $ad_id, '_sb_report_comments_' . $current_user_id, $comments );
			
			$count	=	get_post_meta( $ad_id, '_sb_count_report', true );
			$count	=	(int)$count + 1;
			update_post_meta( $ad_id, '_sb_count_report', $count );
			
			
			if( $count >= $carspotAPI['report_limit'] )
			{
				$message = __("Reported successfully.", "carspot-rest-api") ;
				if( $carspotAPI['report_action'] == '1' )
				{
					$my_post = array( 'ID' => $ad_id, 'post_status'   => 'pending', );
					wp_update_post( $my_post );	
					$message = __("The ad you have reported has been removed.", "carspot-rest-api") ;	
				}
				else
				{
					/*Send Email Function */
					carspotAPI_sb_report_ad($ad_id, $option, $comments, $current_user_id );	
					$message = __("Successfully reported.", "carspot-rest-api") ;				
				}
			}
			
			return  array( 'success' => true, 'data' => '', 'message'  => $message);
		}
	}
	
}



add_action( 'rest_api_init', 'carspotAPI_ads_hooks_ad_search_template_get', 0 );
function carspotAPI_ads_hooks_ad_search_template_get() {
    register_rest_route( 
		'carspot/v1', '/ad_post/dynamic_widget/', array(
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'carspotAPI_ad_search_get1',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );
}

if( !function_exists('carspotAPI_ad_search_get1' ) )
{
	function carspotAPI_ad_search_get1( $request )
	{

		global $carspotAPI;
		$showcatData = false;
		$arrays	= array();
		if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true )
		{
			$showcatData = true;
		}
		if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == false )
		{
			return $response = array( 'success' => true, 'data' => $arrays, 'message'  => '' );			
		}

		
		$json_data = $request->get_json_params();	
		$term_id		= (isset($json_data['cat_id'])) ? $json_data['cat_id'] : '';
		$result 	= carspot_dynamic_templateID($term_id);
		$templateID = get_term_meta( $result , '_sb_dynamic_form_fields' , true);	
		
		$templateID = ( $showcatData == true )	? $templateID : '';
		
		/*New Code Starts Here*/
		$type 		= 	sb_custom_form_data($templateID, '_sb_default_cat_ad_type_show');		
		$price 		= 	sb_custom_form_data($templateID, '_sb_default_cat_price_show');	
		$priceType 	= 	sb_custom_form_data($templateID, '_sb_default_cat_price_type_show');
		$condition 	= 	sb_custom_form_data($templateID, '_sb_default_cat_condition_show');
		$warranty 	= 	sb_custom_form_data($templateID, '_sb_default_cat_warranty_show');
		//$tags 		= 	sb_custom_form_data($templateID, '_sb_default_cat_tags_show');
		//$video 		= 	sb_custom_form_data($templateID, '_sb_default_cat_video_show');
		/*New Code Ends Here*/
		
		if(isset($templateID) && $templateID != "")
		{
			$formData = sb_dynamic_form_data($templateID);	
			foreach($formData as $r)
			{
				
				if( isset($r['types']) && trim($r['types']) != "") {
					
					
					$in_search = (isset($r['in_search']) && $r['in_search'] == "yes") ? 1 : 0;
					
					if($r['titles'] != "" && $r['slugs'] != "" && $in_search == 1){	
					
						$mainTitle = $name = $r['titles'];							
						$fieldName = $r['slugs'];
						$fieldValue = (isset($_GET["custom"]) && isset($_GET['custom'][$r['slugs']])) ? $_GET['custom'][$r['slugs']] : '';
						/*Inputs*/
						if(isset($r['types'] ) && $r['types'] == 1)
						{
							$arrays[] = 	array("main_title" => $mainTitle, "field_type" => 'textfield', "field_type_name" => $fieldName,"field_val" => "", "field_name" => "", "title" => $name, "values" => $fieldValue);							
						}
						/*select option*/
						if(isset($r['types'] ) && $r['types'] == 2 || isset($r['types'] ) && $r['types'] == 3)
						{
							
						$varArrs  =  @explode("|", $r['values']);
						$termsArr =  array();	
						if($r['types'] == 2 )
						{	
							$termsArr[] = array
							(
								"id" => "", 
								"name" => __("Select Option", "carspot-rest-api"),
								"has_sub" => false,
								"has_template" => false,
							);									
						}
						foreach( $varArrs as $v )
							{
								$termsArr[] = array
									(
										"id" => $v, 
										"name" => $v,
										"has_sub" => false,
										"has_template" => false,
									);								
							}
					
				$ftype = ($r['types'] == 2 ) ? 'select' : 'radio';
				$arrays[] = 	array("main_title" => $mainTitle, "field_type" => $ftype, "field_type_name" => $fieldName,"field_val" => "", "field_name" => "", "title" => $name, "values" => $termsArr);	
						}	
															
					
					}
					
					
					
				}
			}
		}
		/*return $arrays;*/
	
		
		if( $condition == 1 && $templateID != "" && $showcatData == true )
		{
			$arrays[] = carspotAPI_getSearchFields('select', 'ad_condition', 'ad_condition', 0, __("Condition", "carspot-rest-api"),'', '', false);
		}	
		else if( $templateID != "" && $condition == 0){ }
		else if( $templateID == "" || $showcatData == true)
		{
			$arrays[] = carspotAPI_getSearchFields('select', 'ad_condition', 'ad_condition', 0, __("Condition", "carspot-rest-api"),'', '', false);
		}			
			
		if( $warranty == 1 && $templateID != "" && $showcatData == true )
		{
			$arrays[] = carspotAPI_getSearchFields('select', 'ad_warranty', 'ad_warranty', 0, __("Warranty", "carspot-rest-api"),'', '', false);
		}
		else if( $templateID != "" && $warranty == 0){ }
		else if( $templateID == "" || $showcatData == true)
		{
			$arrays[] = carspotAPI_getSearchFields('select', 'ad_warranty', 'ad_warranty', 0, __("Warranty", "carspot-rest-api"),'', '', false);
		}				
		/*Add Type*/
		if( $type == 1 && $templateID != "" && $showcatData == true )
		{		
			$arrays[] = carspotAPI_getSearchFields('select'	 , 'ad_type', 'ad_type', 0, __("Ad Type", "carspot-rest-api"),'', '', false);
		}
		else if( $templateID != "" && $type == 0){ }
		else if( $templateID == "" || $showcatData == true)
		{
			$arrays[] = carspotAPI_getSearchFields('select'	 , 'ad_type', 'ad_type', 0, __("Ad Type", "carspot-rest-api"),'', '', false);
		}			
		/*Add Price*/
		if( $priceType == 1 && $templateID != "" && $showcatData == true )
		{
			$fieldTitle 		= array(__("Min Price", "carspot-rest-api"), __("Max Price", "carspot-rest-api"),);
			$arrays[] = carspotAPI_getSearchFields('select', 'ad_currency', 'ad_currency', 0, __("Currency", "carspot-rest-api"),'', '', false);	
			$arrays[] = carspotAPI_getSearchFields('range_textfield', 'ad_price', '', 0, $fieldTitle, __("Price", "carspot-rest-api"));

						
		}
		else if( $templateID != "" && $priceType == 0){ }
		else if( $templateID == "" || $showcatData == true)
		{
			$fieldTitle = array(__("Min Price", "carspot-rest-api"), __("Max Price", "carspot-rest-api"));
			$arrays[] 	= carspotAPI_getSearchFields('select', 'ad_currency', 'ad_currency', 0, __("Currency", "carspot-rest-api"),'', '', false);	
			$arrays[] 	= carspotAPI_getSearchFields('range_textfield', 'ad_price', '', 0, $fieldTitle, __("Price", "carspot-rest-api"));

		}				
		return $response = array( 'success' => true, 'data' => $arrays, 'message'  => '' );
	}
}


add_action( 'rest_api_init', 'carspotAPI_ads_hooks_ad_search_get', 0 );
function carspotAPI_ads_hooks_ad_search_get() {
    register_rest_route( 
		'carspot/v1', '/ad_post/search/', array(
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'carspotAPI_ad_search_get',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );
}

if( !function_exists('carspotAPI_ad_search_get' ) )
{
	function carspotAPI_ad_search_get()
	{
		global $carspotAPI;
		$is_featured_data['-1'] = __("Select Option", "carspot-rest-api");
		$is_featured_data['0'] = __("Simple", "carspot-rest-api");
		$is_featured_data['1'] = __("Featured", "carspot-rest-api");	
		if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == false )
		{			
			$data[] = carspotAPI_getSearchFields('select'	 ,  'is_featured', $is_featured_data, 0, __("Ad Type", "carspot-rest-api"), '');
		}
		
		$data[] = carspotAPI_getSearchFields('textfield', 'ad_title', '', 0, __("Search", "carspot-rest-api"), '',__("Type keyword", "carspot-rest-api"));
		$data[] = carspotAPI_getSearchFields('select'	 ,  'ad_cats1', 'ad_cats', 0, __("Categories", "carspot-rest-api"), '');
		
		if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == false )
		{		
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_condition', 'ad_condition', 0, __("Condition", "carspot-rest-api"),'', '', false);
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_warranty', 'ad_warranty', 0, __("Warranty", "carspot-rest-api"),'', '', false);		
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_type', 'ad_type', 0, __("Ad Type", "carspot-rest-api"),'', '', false);
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_years', 'ad_years', 0, __("Year", "carspot-rest-api"),'', '', false);
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_body_types', 'ad_body_types', 0, __("Body Type", "carspot-rest-api"),'', '', false);
			
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_transmissions', 'ad_transmissions', 0, __("Transmission", "carspot-rest-api"),'', '', false);
			
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_engine_capacities', 'ad_engine_capacities', 0, __("Engine Size", "carspot-rest-api"),'', '', false);
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_engine_types', 'ad_engine_types', 0, __("Engine Type", "carspot-rest-api"),'', '', false);
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_assembles', 'ad_assembles', 0, __("Assembly", "carspot-rest-api"),'', '', false);
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_colors', 'ad_colors', 0, __("Colour", "carspot-rest-api"),'', '', false);
			$data[] = carspotAPI_getSearchFields('select'	 , 'ad_insurance', 'ad_insurance', 0, __("Insurance", "carspot-rest-api"),'', '', false);
			
		
			
			
			
			/*$data[] = carspotAPI_getSearchFields('glocation_textfield', 'ad_location', '', 0, __("Location", "carspot-rest-api"), '');*/
			$fieldTitle 		= array(__("Min Price", "carspot-rest-api"), __("Max Price", "carspot-rest-api"));
			
			$millageTitle 		= array(__("From", "carspot-rest-api"), __("To", "carspot-rest-api"));
				
			//$data[] = carspotAPI_getSearchFields('select', 'ad_currency', 'ad_currency', 0, __("Currency", "carspot-rest-api"),'', '', false);	
			$data[] = carspotAPI_getSearchFields('range_textfield', 'ad_price', '', 0, $fieldTitle, __("Price", "carspot-rest-api"));
			
			$data[] = carspotAPI_getSearchFields('range_textfield', 'ad_millage', '', 0, $millageTitle, __("Mileage (Km)", "carspot-rest-api"));
			
			
			
		}
		$is_show_location  = wp_count_terms( 'ad_country' );
		if( isset($is_show_location) && $is_show_location > 0 )
		{		
			$data[] = carspotAPI_getSearchFields('select'	 ,  'ad_country', 'ad_country', 0, __("Location", "carspot-rest-api"), '');
		}
		$data[] = carspotAPI_getSearchFields('glocation_textfield', 'ad_location', '', 0, __("Address", "carspot-rest-api"), '');
		/*For radious search only*/
		$data[] = carspotAPI_getSearchFields('seekbar', 'ad_seekbar', '', 0, __("Select Distance (KM)", "carspot-rest-api"), '');
		/*fields name will be sort */
		$topbar['sort_arr'][] = array("key"  => "desc", 		"value"  => __("DESC", "carspot-rest-api"));
		$topbar['sort_arr'][] = array("key"  => "asc", 			"value"  => __("ASC", "carspot-rest-api"));
		$topbar['sort_arr'][] = array("key"  => "price_desc", 	"value"  => __("Price: High to Low", "carspot-rest-api" ));
		$topbar['sort_arr'][] = array("key"  => "price_asc", 	"value"  => __("Price: Low to High", "carspot-rest-api" ));		
		
		$extra['field_type_name'] =  	'ad_cats1';
		$extra['title']           =  	__("Search Here", "carspot-rest-api");
		$extra['search_btn']      =  	__("Search Now", "carspot-rest-api");	
		$extra['dialog_send']     =  	__("Submit", "carspot-rest-api");
		$extra['dialg_cancel']    =  	__("Cancel", "carspot-rest-api");	
		$extra['range_value'] 	  =  	array(__("500", "carspot-rest-api"), __("100000", "carspot-rest-api"));
		$extra['from'] 	          = 	__("From", "carspot-rest-api");
		$extra['to'] 	          = 	__("To", "carspot-rest-api"); 
		$extra['select'] 	      = 	__("select option", "carspot-rest-api");
		$extra['location'] 	      = 	__("select location", "carspot-rest-api");
		
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => '', 'topbar' => $topbar, 'extra' => $extra );
	
	}
}
	


add_action( 'rest_api_init', 'carspotAPI_ad_subcats_get', 0 );
function carspotAPI_ad_subcats_get() {
    register_rest_route( 
		'carspot/v1', '/ad_post/subcats/', array(
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'carspotAPI_ad_subcats',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );
}

if( !function_exists( 'carspotAPI_ad_subcats' ) )
{
	function carspotAPI_ad_subcats( $request ) {
		
		$json_data = $request->get_json_params();	
		
		$subcat		= (isset($json_data['subcat'])) ? $json_data['subcat'] : '';
		$mainTermName = '';
		if( $subcat != "" )
		{
			$mainTerm = get_term( $subcat );
			$mainTermName =  htmlspecialchars_decode($mainTerm->name, ENT_NOQUOTES);
		}
		$data = carspotAPI_getSubCats('select',  'ad_cats1', 'ad_cats', $subcat, $mainTermName, '', false);
		return $response = array( 'success' => true, 'data' => $data, 'message'  => '' );		
	}
}



add_action( 'rest_api_init', 'carspotAPI_ad_sublocations_get', 0 );
function carspotAPI_ad_sublocations_get() {
    register_rest_route( 
		'carspot/v1', '/ad_post/sublocations/', array(
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'carspotAPI_ad_sublocations',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );
}

if( !function_exists( 'carspotAPI_ad_sublocations' ) )
{
	function carspotAPI_ad_sublocations( $request ) {
		
		$json_data = $request->get_json_params();	
		
		$subcat		= (isset($json_data['ad_country'])) 		? $json_data['ad_country'] : '';
		$mainTermName = '';
		if( $subcat != "" )
		{
			$mainTerm = get_term( $subcat );
			$mainTermName =  htmlspecialchars_decode($mainTerm->name, ENT_NOQUOTES);
		}
		$data = carspotAPI_getSubCats('select',  'ad_country', 'ad_country', $subcat, $mainTermName, '', false);
		return $response = array( 'success' => true, 'data' => $data, 'message'  => '' );
		
	}
}


add_action( 'rest_api_init', 'carspotAPI_ads_hooks_get_all', 0 );
function carspotAPI_ads_hooks_get_all() {
    register_rest_route( 
		'carspot/v1', '/ad_post/search/', array(
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'carspotAPI_ad_posts_get_all',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );
	
    register_rest_route( 
		'carspot/v1', '/ad_post/category/', array(
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'carspotAPI_ad_posts_get_all',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );	
}



if( !function_exists( 'carspotAPI_ad_posts_get_all' ) ){
function carspotAPI_ad_posts_get_all( $request ) 
{
 
	global $carspotAPI;
	
	$json_data  = $request->get_json_params();	
	$ad_id		= (isset($json_data['ad_id'])) 		? $json_data['ad_id'] : '';
	$meta		=	array(  'key' => 'post_id', 'value'   => '0', 'compare' => '!=', );
	
	
	/*For Near By Ads */
	$allow_near_by = (isset( $carspotAPI['allow_near_by'] ) && $carspotAPI['allow_near_by'] ) ? true : false;	
	$lat_lng_meta_query = array();	
	if($allow_near_by  )
	{		
	
		$latitude	= (isset($json_data['nearby_latitude'])) ? $json_data['nearby_latitude'] : '';
		$longitude	= (isset($json_data['nearby_longitude'])) ? $json_data['nearby_longitude'] : '';
		$distance	= (isset($json_data['nearby_distance'])) ? $json_data['nearby_distance'] : '20';
		$data_array = array("latitude" => $latitude, "longitude" => $longitude, "distance" => $distance );
		
		if( $latitude != "" && $longitude != "" ){
			$lats_longs  = carspotAPI_determine_minMax_latLong($data_array, false);
			if(isset($lats_longs) && count($lats_longs) > 0 )
			{
				 if( isset($lats_longs['lat']['original']) && $lats_longs['lat']['original'] > 0 )
				 {
					$lat_lng_meta_query[] =
					 array(
					  'key' => '_carspot_ad_map_lat',
					  'value' => array($lats_longs['lat']['min'], $lats_longs['lat']['max']),
					  'compare' => 'BETWEEN',
					 );
				 }
				 else
				 {
					$lat_lng_meta_query[] =
					 array(
					  'key' => '_carspot_ad_map_lat',
					  'value' => array($lats_longs['lat']['max'], $lats_longs['lat']['min']),
					  'compare' => 'BETWEEN',
					 );
				 }
				 
				  if( isset($lats_longs['long']['original']) && $lats_longs['long']['original'] > 0 )
				 {
					 $lat_lng_meta_query[] = array(
					  'key' => '_carspot_ad_map_long',
					  'value' => array($lats_longs['long']['min'], $lats_longs['long']['max']),
					  'compare' => 'BETWEEN',
					  
					 );
				 }
				 else
				 {
					 $lat_lng_meta_query[] = array(
					  'key' => '_carspot_ad_map_long',
					  'value' => array($lats_longs['long']['max'], $lats_longs['long']['min']),
					  'compare' => 'BETWEEN',
					 );
				 }
	   }
		}
	}
	/*For Near By Ads Ends */
	
	
	/* Done Stars */	
	$title	=	'';
	if( isset($json_data['ad_title']) && $json_data['ad_title'] != "" )
	{
		$title	=	$json_data['ad_title'];
	}
	$price	=	array();
	
	$priceVal = ( isset( $json_data['ad_price'] ) && $json_data['ad_price'] != "" ) ? $json_data['ad_price'] : '';
	
	$priceValue = @explode("-", $priceVal);
	$minPrice = ( isset($priceValue[0]) && $priceValue[0] != "" ) ? (int)$priceValue[0] : "";
	$maxPrice = ( isset($priceValue[1]) && $priceValue[1] != "" ) ? (int)$priceValue[1] : "";
	
	if( $minPrice != "" )
	{
		$price	=	array(
			'key'     => '_carspot_ad_price',
			'value'   => array( $minPrice, $maxPrice ),
			'type'    => 'numeric',
			'compare' => 'BETWEEN',
		);	
	}	
	
	$location	=	array();
	if( isset( $json_data['ad_location'] ) && $json_data['ad_location'] != "" )
	{
		$location	=	array(
			'key'     => '_carspot_ad_location',
			'value'   => @trim($json_data['ad_location']),
			'compare' => '=',
		);	
	}	
	
	$ad_currency	=	array();
	if( isset( $json_data['ad_currency'] ) && $json_data['ad_currency'] != "" )
	{
		$ad_currency	=	array(
			'key'     => '_carspot_ad_currency',
			'value'   => @trim($json_data['ad_currency']),
			'compare' => '=',
		);	
	}

	
	$category	=	array();
	
	if( isset( $json_data['ad_cats1'] ) && $json_data['ad_cats1'] != ""  )
	{
		$category	=	array(
			array(
			'taxonomy' => 'ad_cats',
			'field'    => 'term_id',
			'terms'    => (int)$json_data['ad_cats1'],
			),
		);	
	}	
	$category = (isset( $category ) && count( $category ) > 0 ) ? $category : '';
	//
	$ad_country	=	array();
	
	if( isset( $json_data['ad_country'] ) && $json_data['ad_country'] != ""  )
	{
		$ad_country	=	array(
			array(
			'taxonomy' => 'ad_country',
			'field'    => 'term_id',
			'terms'    => (int)$json_data['ad_country'],
			),
		);	
	}	

	$ad_country = (isset( $ad_country ) && count( $ad_country ) > 0 ) ? $ad_country : '';

	
	
	$custom_search = array();
	if( isset( $json_data['custom_fields']) )
	{
		foreach((array)$json_data['custom_fields'] as $key => $val)
		{
			if( is_array($val) )
			{
				$arr = array();
				$metaKey = '_carspot_tpl_field_'.$key;
				
				foreach ($val as $v)
				{ 
					if( $v != "" )
					{
						 $custom_search[] = array(
						  'key'     => $metaKey,
						  'value'   => $v,
						  'compare' => 'LIKE',
						 ); 
					}
				}
			}
			else
			{
				if(trim( $val ) == "0" ) { continue; }
				if( $val != "" )
				{
					$val =  stripslashes_deep($val);
				
					$metaKey = '_carspot_tpl_field_'.$key;
					$custom_search[] = array(
						 'key'     => $metaKey,
						 'value'   => $val,
						 'compare' => 'LIKE',
					); 
				}
			}
   		}
	}	
	


	$feature_or_simple	=	array();
	if( isset( $json_data['is_featured'] ) && $json_data['is_featured'] != ""  && $json_data['is_featured'] != -1)
	{
		$feature_or_simple	=	array(
			'key'     => '_carspot_is_feature',
			'value'   => (int)$json_data['is_featured'],
			'compare' => '=',
		);	
	}
	$ad_type	=	array();
	if( isset( $json_data['ad_type'] ) && $json_data['ad_type'] != "" )
	{
		$ad_type	=	array(
			'key'     => '_carspot_ad_type',
			'value'   => $json_data['ad_type'],
			'compare' => '=',
		);	
	}

	$condition	=	array();
	if( isset( $json_data['ad_condition'] ) && $json_data['ad_condition'] != "" )
	{
		$condition	=	array(
			'key'     => '_carspot_ad_condition',
			'value'   => @trim($json_data['ad_condition']),
			'compare' => '=',
		);	
	}	
	$warranty	=	array();
	if( isset( $json_data['ad_warranty'] ) && $json_data['ad_warranty'] != "" )
	{
		$warranty	=	array(
			'key'     => '_carspot_ad_warranty',
			'value'   => @trim($json_data['ad_warranty']),
			'compare' => '=',
		);	
	}	
	//Transmission
	$transmission	=	'';
	if( isset( $json_data['transmission'] ) && $json_data['transmission'] != "" )
	{
		$transmission	=	array(
			'key'     => '_carspot_ad_transmissions',
			'value'   => $json_data['transmission'],
			'compare' => '=',
		);	
	}	
	
	
    /* Carspot search taxonomies start */	
    $year	      =	 '';
	$year_from    =  '';
	$year_to      =  '';
	if( isset( $json_data['year_from'] ) && $json_data['year_from']  != "")
	{
		$year_from =  $_GET['year_from'];
		$year	=	array(
			'key'     => '_carspot_ad_years',
			'value'   => $json_data['year_from'],
			'compare' => '=',
		);	
	}
	
	if( isset( $json_data['year_to']  ) && $json_data['year_to'] != "")
	{
		$year_to =  $json_data['year_to'];
	}
	
	if($json_data['year_from'] != "" && $json_data['year_to'] !="")
	{
		$year	=	array(
				'key'     => '_carspot_ad_years',
				'value'   => array( $year_from, $year_to ),
				'type'    => 'numeric',
				'compare' => 'BETWEEN',
			);
	}
	/*Price*/
	$price	=	'';
	if( isset( $json_data['min_price'] ) && $json_data['min_price'] != "" )
	{
		$price	=	array(
			'key'     => '_carspot_ad_price',
			'value'   => array( $json_data['min_price'], $json_data['max_price'] ),
			'type'    => 'numeric',
			'compare' => 'BETWEEN',
		);	
	}
	
	/*Body Type*/
	$body_type	=	'';
	if( isset( $json_data['body_type'] ) && $json_data['body_type'] != "" )
	{
		$body_type	=	array(
			'key'     => '_carspot_ad_body_types',
			'value'   => $json_data['body_type'],
			'compare' => '=',
		);	
	}
	//Transmission
	$transmission	=	'';
	if( isset( $json_data['transmission'] ) && $json_data['transmission'] != "" )
	{
		$transmission	=	array(
			'key'     => '_carspot_ad_transmissions',
			'value'   => $json_data['transmission'],
			'compare' => '=',
		);	
	}
	//Engine Type
	$engine_type	=	'';
	if( isset( $json_data['engine_type'] ) && $json_data['engine_type'] != "" )
	{
		$engine_type	=	array(
			'key'     => '_carspot_ad_engine_types',
			'value'   => $json_data['engine_type'],
			'compare' => '=',
		);	
	}
	//Engine Capacity
	$engine_capacity	=	'';
	if( isset( $json_data['engine_capacity'] ) && $json_data['engine_capacity'] != "" )
	{
		$engine_capacity	=	array(
			'key'     => '_carspot_ad_engine_capacities',
			'value'   => $json_data['engine_capacity'],
			'compare' => '=',
		);	
	}
	//Assembly
	$assembly	=	'';
	if( isset( $json_data['assembly'] ) && $json_data['assembly'] != "" )
	{
		$assembly	=	array(
			'key'     => '_carspot_ad_assembles',
			'value'   => $json_data['assembly'],
			'compare' => '=',
		);	
	}
	//Color Family
	$color_family	=	'';
	if( isset( $_GET['color_family'] ) && $_GET['color_family'] != "" )
	{
		$color_family	=	array(
			'key'     => '_carspot_ad_colors',
			'value'   => $_GET['color_family'],
			'compare' => '=',
		);	
	}
	//Insurance
	$ad_insurance	=	'';
	if( isset( $json_data['insurance'] ) && $json_data['insurance'] != "" )
	{
		$ad_insurance	=	array(
			'key'     => '_carspot_ad_insurance',
			'value'   => $json_data['insurance'],
			'compare' => '=',
		);	
	}
	//Mileage
	$mileage	=	''; $milage_from = ''; $mileage_to = '';
	if( isset( $json_data['mileage_from'] ) && $json_data['mileage_from'] != "" )
	{
		$milage_from = $json_data['mileage_from'];
	}
	if( isset( $json_data['mileage_to'] ) && $json_data['mileage_to'] != "" )
	{
		 $mileage_to = $json_data['mileage_to'];
	}
	if($milage_from != '' &&  $mileage_to != '')
	{
		$mileage	=	array(
			'key'     => '_carspot_ad_mileage',
			'value'   => array( $milage_from , $mileage_to),
			'type'    => 'numeric',
			'compare' => 'BETWEEN',
		);		
	}	
	
/* Carspot search taxonomies End */		
	

	/* Done Ends , ,  */	

if ( get_query_var( 'paged' ) ) {
	$paged = get_query_var( 'paged' );
} else if ( isset( $json_data['page_number'] ) ) {
	// This will occur if on front page.
	$paged = $json_data['page_number'];
} else {
	$paged = 1;
}
	
	$is_active	=	array(
		'key'     => '_carspot_ad_status_',
		'value'   => 'active',
		'compare' => '=',
	);		
	
	
	$order	=	'desc';
	$orderBy = 'date';	

	if( isset( $json_data['sort'] ) && $json_data['sort'] != "" )
	{
		$order_val	=	$json_data['sort'];
		if( $order_val == 'asc' || $order_val == 'price_asc' ) { $order	=	'asc'; }
		if( $order_val == 'price_desc' || $order_val == 'price_asc' ) { $orderBy = 'meta_value_num'; }
	}	
	
	
	$author_not_in = carspotAPI_get_authors_notIn_list();
	
	$args	=	array(
	's' => $title,
	'post_type' => 'ad_post',
	'post_status' => 'publish',
	'posts_per_page' => get_option( 'posts_per_page' ),
	'tax_query' => array(
		$category,
		$ad_country,
		$ad_transmissions,
	),
	'meta_key' => '_carspot_ad_price',
	'meta_query' => array(
		$is_active,
		$condition,
		$ad_type,
		$warranty,
		$feature_or_simple,
		$price,
		$year,
		$body_type,
		$transmission,
		$engine_type,
		$engine_capacity,
		$assembly,
		$color_family,
		$ad_insurance,
		$mileage,
		
	),
	'order'=> $order,
	'orderby' => $orderBy,	
	'paged' => $paged,
	'author__not_in' => $author_not_in
);

	$results = new WP_Query( $args ); 
	$count = 0;
	$ad_detail = array();
	foreach( $results->posts as $r )
	{
		$ad_detail[$count]['ad_id'] 		= $r->ID;
		$ad_detail[$count]['ad_title'] 		= $r->post_title;
		$ad_detail[$count]['ad_author_id']	= get_post_field( 'post_author', $r->ID );
		$ad_detail[$count]['ad_date'] 		= get_the_date("", $r->ID);	
		$ad_detail[$count]['ad_price'] 		= carspotAPI_get_price( '', $r->ID );
		$ad_detail[$count]['images'] 		= carspotAPI_get_ad_image($r->ID, 1, 'thumb');	
		$ad_detail[$count]['ad_video'] 		= carspotAPI_get_adVideo($r->ID);	
		$ad_detail[$count]['location'] 		= carspotAPI_get_adAddress($r->ID);
		$ad_detail[$count]['ad_cats_name']	= carspotAPI_get_ad_terms_names($r->ID,  'ad_cats');	
		$ad_detail[$count]['ad_cats'] 		= carspotAPI_get_ad_terms($r->ID, 'ad_cats','',  __("Categories", "carspot-rest-api"));	
		$ad_detail[$count]['ad_status']     =  carspotAPI_adStatus( $r->ID );
		$ad_detail[$count]['ad_views']      =  get_post_meta($r->ID, "sb_post_views_count", true);
		$ad_detail[$count]['ad_saved']      =  array("is_saved" => 0, "text" => __("Save Ad", "carspot-rest-api"));	
		$ad_detail[$count]['ad_timer']      =  carspotAPI_get_adTimer($r->ID);
		$count++;
	
	}

		wp_reset_postdata();
	
	$fads['text'] = '';
	$fads['ads']  = array();
	$extra['is_show_featured'] = (isset( $carspotAPI['feature_on_search'] ) && $carspotAPI['feature_on_search'] == 1 ) ? true :false;
	if( isset( $carspotAPI['feature_on_search'] ) && $carspotAPI['feature_on_search'] == 1 )
	{
		$featuredAdsCount = ( $carspotAPI['search_related_posts_count'] != "" ) ?  $carspotAPI['search_related_posts_count'] : 5; 
		$featuredAdsTitle = ( $carspotAPI['sb_search_ads_title'] != "" ) ? $carspotAPI['sb_search_ads_title'] : __("Featured Ads", "carspot-rest-api");

		$featured_termID = ( isset( $json_data['ad_cats1'] ) && $json_data['ad_cats1'] != ""  ) ? $json_data['ad_cats1'] : '';
		$featuredAds = carspotApi_featuredAds_slider( '', 'active', '1', $featuredAdsCount, $featured_termID, 'publish');	
		if( isset( $featuredAds ) && count( $featuredAds ) > 0 )
		{		
			$fads['text'] = $featuredAdsTitle;
			$fads['ads']  = $featuredAds;
			$extra['is_show_featured'] = true;
		}
		else
		{
			$extra['is_show_featured'] = false;	
		}
	}
	$topbar['count_ads']	= __("No of Ads Found", "carspot-rest-api") . ': '. $results->found_posts;

	$nextPaged = $paged + 1;
	
	$has_next_page = ( $nextPaged <= (int)$results->max_num_pages ) ? true : false;
	
	$pagination = array("max_num_pages" => (int)$results->max_num_pages,"current_page" => (int)$paged, "next_page" => (int)$nextPaged, "increment" => (int)get_option( 'posts_per_page') , "current_no_of_ads" =>  (int)count($results->posts), "has_next_page" => $has_next_page );

	$data	= array("featured_ads" =>  $fads, "ads" => $ad_detail, "sidebar" => "");
	
		/*fields name will be sort */
		$sort_arr_desc = array("key"  => "desc", "value"  => __("DESC", "carspot-rest-api" ));
		$sort_arr_asc = array("key"  => "asc", "value"  => __("ASC", "carspot-rest-api" ));

		$sort_arr_price_desc = array("key"  => "price_desc", "value"  => __("Price: High to Low", "carspot-rest-api" ));
		$sort_arr_price_asc = array("key"  => "price_asc", "value"  => __("Price: Low to High", "carspot-rest-api" ));		
		if( $order == 'desc')
		{
			$topbar['sort_arr_key'] = $sort_arr_desc;
			$topbar['sort_arr'][] = $sort_arr_desc;
			$topbar['sort_arr'][] = $sort_arr_asc;
			$topbar['sort_arr'][] = $sort_arr_price_desc;
			$topbar['sort_arr'][] = $sort_arr_price_asc;
			
		}
		if( $order == 'asc')
		{
			$topbar['sort_arr_key'] = $sort_arr_asc;
			$topbar['sort_arr'][] = $sort_arr_asc;
			$topbar['sort_arr'][] = $sort_arr_desc;
			$topbar['sort_arr'][] = $sort_arr_price_desc;
			$topbar['sort_arr'][] = $sort_arr_price_asc;
			
		}
		if( $order == 'price_desc')
		{
			$topbar['sort_arr_key'] = $sort_arr_price_desc;
			$topbar['sort_arr'][] = $sort_arr_price_desc;			
			$topbar['sort_arr'][] = $sort_arr_asc;
			$topbar['sort_arr'][] = $sort_arr_desc;
			$topbar['sort_arr'][] = $sort_arr_price_asc;
			
		}		
		if( $order == 'price_asc')
		{
			$topbar['sort_arr_key'] = $sort_arr_price_asc;
			$topbar['sort_arr'][] = $sort_arr_price_asc;
			$topbar['sort_arr'][] = $sort_arr_asc;
			$topbar['sort_arr'][] = $sort_arr_desc;
			$topbar['sort_arr'][] = $sort_arr_price_desc;						
			
			
		}
		
		$searchTitle = __("Category", "carspot-rest-api");	
		if( isset( $json_data['ad_cats1'] ) && $json_data['ad_cats1'] != ""  )
		{
			$term = get_term( $json_data['ad_cats1'], 'ad_cats' );
			$searchTitle = htmlspecialchars_decode(@$term->name, ENT_NOQUOTES);
		}
		
		$extra['field_type_name'] =  'ad_cats1';
		$extra['title'] =  $searchTitle;
		
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => '', 'extra'  => $extra, "topbar" => $topbar, "pagination" => $pagination );
}
}

if( !function_exists( 'carspotAPI_ad_dynamic_fields_data' ) )
{
	function carspotAPI_ad_dynamic_fields_data( $term_id = '' )
	{
		$result 	= carspot_dynamic_templateID($term_id);
		$templateID = get_term_meta( $result , '_sb_dynamic_form_fields' , true);	
		$arrays	= array();
		if(isset($templateID) && $templateID != "")
		{
				$formData = sb_dynamic_form_data($templateID);	
				foreach($formData as $r)
				{
					if( isset($r['types']) && trim($r['types']) != "") {
						
						$in_search = (isset($r['in_search']) && $r['in_search'] == "yes") ? 1 : 0;
						if($r['titles'] != "" && $r['slugs'] != "" && $in_search == 1){	
						
							$mainTitle = $name = $r['titles'];							
							/*$fieldName = "custom[".$r['slugs']."]";*/
							$fieldName = $r['slugs'];
							$fieldValue = (isset($_GET["custom"]) && isset($_GET['custom'][$r['slugs']])) ? $_GET['custom'][$r['slugs']] : '';
							/*Inputs*/
							if(isset($r['types'] ) && $r['types'] == 1)
							{								
								$arrays[] = array("key" => $name, "value" => $fieldValue);
							}
							/*select option*/
							if(isset($r['types'] ) && $r['types'] == 2 || isset($r['types'] ) && $r['types'] == 3)
							{
								$varArrs = @explode("|", $r['values']);
								$termsArr = array();	
								foreach( $varArrs as $v )
								{
									$termsArr[] = array( "id" => $v,  "name" => $v, );								
								}
								$arrays[] = array("key" => $name, "value" => $termsArr);
							}	
						}
					}
				}
		}
		
		return $arrays;
	}
}

/* Single Ad Featured Notification */
if ( ! function_exists( 'carspotAPI_adFeatured_notify' ) ) { 
function carspotAPI_adFeatured_notify($ad_id = '', $check_statuc = false)
{
	$user = wp_get_current_user();	
	$uid = @$user->data->ID;	
	$pid = $uid;
	
	$data = array();
	
	
	$isFeature = get_post_meta( $ad_id, '_carspot_is_feature', true );
	$isFeature = ( $isFeature ) ? $isFeature : 0;
	
	
	if( get_post_meta( $ad_id, '_carspot_ad_status_', true ) == 'active' && $check_statuc == false)
	{
		
	}
	
	/*//&& get_post_meta( $ad_id, '_carspot_ad_status_', true ) == 'active' */
	if( $uid != "" && $isFeature == 0 )
	 {		
		 if( get_post_field( 'post_author', $ad_id ) == $uid )
		 {
			 $featured_ads_count = get_user_meta( $uid, '_carspot_featured_ads', true );
			if($featured_ads_count  != 0 )
			{
				$expire_ads_time = get_user_meta( $uid, '_sb_expire_ads', true );
				if( $expire_ads_time != '-1' )
				{
					if( $expire_ads_time < date('Y-m-d') )
					{
						$data['text'] = __('Your package has been expired, please subscribe the package to make it feature AD. ','carspot-rest-api');
						$data['link'] = $ad_id;
						$data['make_feature'] = false;
						$data['btn'] = __('Buy','carspot-rest-api');
					}
					else
					{
						$data['text'] 		    = __('Click Here To make this ad featured.','carspot-rest-api');	
						$data['link'] 			= $ad_id;
						$data['make_feature'] 	= true;
						$data['btn']            = __('Make Featured','carspot-rest-api');
					}
				}
				else
				{
					$data['text'] 		    =  __('Click Here To make this ad featured.','carspot-rest-api');	
					$data['link'] 			=  $ad_id;
					$data['make_feature'] 	=  true;
					$data['btn']            =  __('Make Featured','carspot-rest-api');
				}
			}
			else
			{
				$data['text'] 		    =  __('To make ad featured buy package.','carspot-rest-api');	
				$data['link'] 			=  $ad_id;
				$data['make_feature'] 	=  false;
				$data['btn']            =  __('Buy','carspot-rest-api');
	
			}
		 }
	 }
	 
	
	 
 	return $data;
}
}

add_action( 'rest_api_init', 'carspotAPI_makeAd_featured_hook', 0 );
function carspotAPI_makeAd_featured_hook() {
    register_rest_route( 
		'carspot/v1', '/ad_post/featured/', array(
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'carspotAPI_makeAd_featured',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );
}
if ( ! function_exists( 'carspotAPI_makeAd_featured' ) )
 { 
	function carspotAPI_makeAd_featured( $request )
		{
	
		$json_data		= $request->get_json_params();	
		$ad_id			= (isset($json_data['ad_id'])) ? trim($json_data['ad_id']) : '';
		$user           = wp_get_current_user();	
		$user_id        = $user->data->ID;
		$success        = false;
		if( get_post_field( 'post_author', $ad_id ) == $user_id )
		{
			if( get_post_meta( $ad_id, '_carspot_is_feature', true ) == 0 )
			{
				if( get_user_meta( $user_id, '_carspot_featured_ads', true ) > 0 || get_user_meta( $user_id, '_carspot_featured_ads', true ) == '-1')
				{
						if( get_user_meta( $user_id, '_sb_expire_ads', true ) != '-1' )
						{
							if( get_user_meta( $user_id, '_sb_expire_ads', true ) < date('Y-m-d') )
							{
								$message =  __( "Your package has been expired.", 'carspot-rest-api' );
							}
						}
						$feature_ads	=	get_user_meta($user_id, '_carspot_featured_ads', true);
						$feature_ads2	=   $feature_ads;
						$feature_ads	=	$feature_ads - 1;
						if( $feature_ads2 != "-1" )
						{
							update_user_meta( $user_id, '_carspot_featured_ads', $feature_ads );
						}
						update_post_meta( $ad_id, '_carspot_is_feature', '1' );
						update_post_meta( $ad_id, '_carspot_is_feature_date', date( 'Y-m-d' ) );
						$message =   __( "This ad has been featured successfully.", 'carspot-rest-api' );
						$success = true;
				}
				else
				{
					$message =  __( "Get package in order to make it feature.", 'carspot-rest-api' );
				}
			}
			else
			{
				$message =   __( "Ad already featured.", 'carspot-rest-api' );
			}
		}
		else
		{
			$message =   __( "You must be Ad owner to make it feature.", 'carspot-rest-api' );
		}
			$response = array( 'success' => $success, 'data' => '', 'message' => $message );
			
			return $response;
	}
}