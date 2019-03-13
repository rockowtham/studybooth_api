<?php /**
  * Add REST API support to an already registered post type.
  */
add_action( 'rest_api_init', 'carspotAPI_post_ad_get_hooks', 0 );
function carspotAPI_post_ad_get_hooks() {

    register_rest_route(
        'carspot/v1', '/post_ad/', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_post_ad_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );	
    register_rest_route(
        'carspot/v1', '/post_ad/is_update/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_post_ad_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );	
}  
  
if ( ! function_exists( 'carspotAPI_shift_arr_key' ) ) 
{
	function carspotAPI_shift_arr_key($key, $arr)
	{
		if(in_array($key, $arr)) {
			$value = $arr[$key];
			unset($arr[$key]);
			array_unshift($arr, $value);
		}
	}  
}
 
if ( ! function_exists( 'carspotAPI_post_ad_get' ) ) 
{
function carspotAPI_post_ad_get( $request )
{
	
	global $carspotAPI;
	
	$message = '';
	if( !$carspotAPI['admin_allow_unlimited_ads'] )
		$message = carspotAPI_check_ads_validity();

	if ( !is_super_admin( get_current_user_id() ) )
		$message = carspotAPI_check_ads_validity();

	if( $message != "" )
	{
		$response = array( 'success' => false, 'data' => '' , 'message' => $message );	
		return $response;
	}
		
		
		
		
	$json_data = $request->get_json_params();		
	$is_update = (isset( $json_data['is_update'] ) && $json_data['is_update'] != "" ) ? $json_data['is_update'] : '';
	$user = wp_get_current_user();	
	$user_id = $user->data->ID;		
	//delete_user_meta($user_id, 'ad_in_progress');	
	$pid	=	get_user_meta ( $user_id, 'ad_in_progress', true );	
	
	if( $is_update != "" )
	{
		$pid = (int)$is_update;
	}
	else if( get_post_status ( $pid ) && $pid != "")
	{
		/*else if( $pid != "" )*/
		$pid = (int)$pid;	
				
	}
	else
	{
		// Gather post data.
		$my_post = array( 'post_status'   => 'private', 'post_author'   => $user_id, 'post_type' => 'ad_post' );
		$id	=  wp_insert_post( $my_post );
		if( $id )
		{
			update_user_meta( $user_id, 'ad_in_progress', $id );	
			update_post_meta($id, '_carspot_ad_status_', 'active' );
		}

		$pid = (int)$id;
	}
	
	
	
	$display_name = $user->data->display_name;
	
	$sb_contact  = get_user_meta( $user_id, '_sb_contact', true );
	$sb_location = get_user_meta( $user_id, '_sb_address', true );
	
	
	$customFields = get_option("_carspotAPI_customFields");
	$customFields = json_decode( $customFields, true );
	
	$form_type = ( isset( $customFields['form_type'] ) && $customFields['form_type'] == 'yes' ) ? $customFields['form_type'] : 'no';
	if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true )
	{
		$form_type = 'yes';
	}
	else
	{
		$form_type = 'no';
	}

	$data['is_update'] = $is_update;
	$data['ad_id']     =   (int)$pid;

	if( $is_update != "" )
	{	
		$data['title'] = __("Edit Add", "carspot-rest-api");
	}
	else
	{
		$data['title'] = __("Add Post", "carspot-rest-api");
	}
	$data['hide_price'] = array("ad_price","ad_price_type");
	
	$ad_currency_count  = wp_count_terms( 'ad_currency' );
	$data['hide_currency'] = array("ad_price","ad_currency");
	$data['title_field_name'] = 'ad_title';
	
	//$pid
	$adTitle = '';
	$ad_content = $ad_yvideo = $ad_tags = '';
	$map_lat  = $map_long  = $ad_price = $ad_condition = $ad_warranty = $ad_type = $ad_price_type = $ad_currency = $ad_bidding_time = '';
	$ad_cats_lvl = '';
	$ad_cats = array();
	$dynamicData = array();
	$ad_price_typeVal = '';
	$images = '';
	$ad_bidding = 0;
	
	
	
	if( $is_update != "" )
	{
		$pid = (int)$pid;		
		$adData = @get_post($pid);
		
		$adTitle = @$adData->post_title;
		$ad_content = trim( @$adData->post_content );
		
		$display_name 		= get_post_meta($pid, '_carspot_poster_name', true );
		$sb_contact 		= get_post_meta($pid, '_carspot_poster_contact', true );
		$sb_location 		= get_post_meta($pid, '_carspot_ad_location', true );
		
		$map_lat 			= get_post_meta($pid, '_carspot_ad_map_lat', true );
		$map_long 			= get_post_meta($pid, '_carspot_ad_map_long', true );		
		
		$ad_type 			= get_post_meta($pid, '_carspot_ad_type', true);
		$ad_condition 		= get_post_meta($pid, '_carspot_ad_condition', true );
		$ad_warranty		= get_post_meta($pid, '_carspot_ad_warranty', true );
		$ad_price			= get_post_meta($pid, '_carspot_ad_price', true );
		$ad_price_typeVal  	= get_post_meta($pid, '_carspot_ad_price_type', true );	
		$ad_yvideo			= get_post_meta($pid, '_carspot_ad_yvideo', true );

		$ad_bidding 		= get_post_meta($pid, '_carspot_ad_bidding', true );
		$ad_bidding_time	= get_post_meta($pid, '_carspot_ad_bidding_date', true );
		
		$ad_currency		= get_post_meta($pid, '_carspot_ad_currency', true);
		
		$tags_array 		= wp_get_object_terms( $pid,  'ad_tags', array('fields' => 'names') );
		$ad_tags			=	@implode( ',', $tags_array );	
		
		$ad_cats			=	wp_get_object_terms( $pid,  'ad_cats', array('fields' => 'ids') );	
		$ad_term_id = '';
		if( count( $ad_cats ) > 0)
		{
			$term_id = @end($ad_cats);
			$ad_term_id = $term_id;
			if( $term_id != "" )
			{
				//$dynamicData = carspotAPI_post_ad_fields( '', $term_id, $pid );
			}
		}
	}
			
	
	if( isset($dynamicData) && count($dynamicData) > 0 && $dynamicData != "" && $form_type != 'no')
	{
		$data['fields'] = $dynamicData;
	}
	
	$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_title', '', '', __("Ad Title", "carspot-rest-api"), '', '', '1', true, $adTitle);
	$data['fields'][] = carspotAPI_getPostAdFields('select', 'ad_cats1', 'ad_cats', 0, __("Categories", "carspot-rest-api"),'','','1', true, '', $is_update);
	
	//$data['fields'][] = carspotAPI_getPostAdFields('image'	 , 'ad_img', '', 0, __("Add Images", "carspot-rest-api"),'', '', 2);	
	$custom_fields = ( isset( $customFields['custom_fields'] ) && $form_type == 'no' ) ? $customFields['custom_fields'] : array();	
	if( isset( $custom_fields ) && count( $custom_fields ) > 0 )
	{
		foreach($custom_fields as $fields)
		{
			$title = $fields['title'];
			$slug = '_sb_extra_'.$fields['slug'];
			
			if( $fields['type'] == 'text')
			{
				$data['fields'][] = carspotAPI_getPostAdFields('textfield'  , $slug, $slug, 0, $title, '', '', '1', false,'', $is_update);
			}
			if( $fields['type'] == 'select' && $fields['option_values'] != '')
			{
				$option_values = explode(",", $fields['option_values'] );
				$data['fields'][] = carspotAPI_getPostAdFields('select',  $slug, $option_values, 0, $title, '', '', '1', false,'', $is_update);
			}
		}
	}	
	
		
	if( $form_type == 'no' )
	{
		if(isset( $carspotAPI['allow_price_type'] ) && $carspotAPI['allow_price_type'] == 1 )
		{	
			$ad_price_type  	= carspotAPI_adPrice_types($ad_price_typeVal);
			$data['fields'][] 	= carspotAPI_getPostAdFields('select', 'ad_price_type', $ad_price_type, 0, __("Price Type", "carspot-rest-api"), '', '', '1', false, $ad_price_typeVal, $is_update);
		}
	$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_price', '', 0, __("Ad Price", "carspot-rest-api"), '', '', '1', true, $ad_price);

	/*$ad_currency_count  = wp_count_terms( 'ad_currency' );*/
	
	
		if(isset( $carspotAPI['allow_tax_condition'] ) && $carspotAPI['allow_tax_condition'] == 1 )
		{	
			$data['fields'][] = carspotAPI_getPostAdFields('select','ad_condition','ad_condition',0, __("Condition", "carspot-rest-api"),'', '', 1, false, '', $is_update);
		}	
		if(isset( $carspotAPI['allow_tax_warranty'] ) && $carspotAPI['allow_tax_warranty'] == 1 )
		{	
			$data['fields'][] = carspotAPI_getPostAdFields('select', 'ad_warranty', 'ad_warranty', 0, __("Warranty", "carspot-rest-api"),'', '', 1, false, '', $is_update);
		}

	  $data['fields'][] = carspotAPI_getPostAdFields('select'	 , 'ad_type', 'ad_type', 0, __("Ad Type", "carspot-rest-api"),'', '', 1, false, '', $is_update);	
	$data['fields'][] = carspotAPI_getPostAdFields('textfield'  , 'ad_yvideo', '', 0, __("Youtube Video Link", "carspot-rest-api"), '', '', 1, false, $ad_yvideo);
	  	
	
	}	
	
	$data['fields'][] = carspotAPI_getPostAdFields('textarea'  , 'ad_description', '', 0, __("Description", "carspot-rest-api"), '', '', '2', true, $ad_content);
	
	if( isset( $carspotAPI['sb_enable_comments_offer_user'] ) && $carspotAPI['sb_enable_comments_offer_user'] )
	{
		/*Enabled only if by admin*/
	
	$ad_biddingArr_on = array("key" => "1", 		"val" => __('On','carspot-rest-api'), 			"is_show" => true);
	$ad_biddingArr_off = array("key" => "0", 		"val" => __('Off','carspot-rest-api'), 		"is_show" => true);
		
		
	if( $ad_bidding == 1 )
	{
		$ad_biddingArr[] = $ad_biddingArr_on;
		$ad_biddingArr[] = $ad_biddingArr_off;
		
	}
	else
	{
		$ad_biddingArr[] = $ad_biddingArr_off;
		$ad_biddingArr[] = $ad_biddingArr_on;
	}
	
	$data['fields'][] = carspotAPI_getPostAdFields('select' , 'ad_bidding', $ad_biddingArr, 0, __("Bidding", "carspot-rest-api"),'', '', '2', false, '', $is_update);		


	$bidding_timer_show = ( isset( $carspotAPI['bidding_timer'] ) && $carspotAPI['bidding_timer'] ) ? true : false;
	/*$top_bidder_limit_show = ( isset( $carspotAPI['top_bidder_limit'] ) && $carspotAPI['top_bidder_limit'] > 0) ? true : false;*/

	if( $bidding_timer_show ){
		
		$data['fields'][] = carspotAPI_getPostAdFields('textfield', 'ad_bidding_time', '', '', __("Bidding Time", "carspot-rest-api"), '', '', '2', false, $ad_bidding_time);
		
	}


	}
	

	
	$data['profile']['name'] = carspotAPI_getPostAdFields('textfield'  , 'name', '', 0, __("Name", "carspot-rest-api"), '', $display_name, '3', true, $display_name);
	
	$sb_change_ph       = ( isset( $carspotAPI['sb_change_ph'] ) && $carspotAPI['sb_change_ph'] == false) ? false : true;
	$is_verification_on = ( isset( $carspotAPI['sb_phone_verification']	) && $carspotAPI['sb_phone_verification'] ) ? true : false;
	
	$data['profile']['is_phone_verification_on'] = $is_verification_on;
	$data['profile']['phone_editable'] = $sb_change_ph;
	$data['profile']['phone'] = carspotAPI_getPostAdFields('textfield'  , 'ad_phone', '', 0, __("Phone Number", "carspot-rest-api"), '', $sb_contact, '3', true, $sb_contact);
	
	
	/*Start Editing Here*/
	$data['profile']['ad_country_show'] = false;
	$is_show_location  = wp_count_terms( 'ad_country' );
	if( isset($is_show_location) && (int)$is_show_location > 0 )
	{	
		$data['profile']['ad_country_show'] = true;
		$data['profile']['ad_country'] = carspotAPI_getPostAdFields('select', 'ad_country', 'ad_country', 0, __("Location", "carspot-rest-api"),'','','3', true, '', $is_update);		
	}
	//ad_country		
	if(isset( $json_data['is_update'] ) && $json_data['is_update'] != "" ) 
	{	
		/**/
	}
	else
	{
		$sb_location = '';	
		$map_lat  = ( $map_lat  != "" ) ? $map_lat  : $carspotAPI['sb_default_lat'];
		$map_long = ( $map_long != "" ) ? $map_long : $carspotAPI['sb_default_long'];

	}
	
	$data['profile']['location'] = carspotAPI_getPostAdFields('glocation_textfield', 'ad_location', '', 0, __("Address", "carspot-rest-api"), '', $sb_location, '3', true, $sb_location);
			
	if(isset( $carspotAPI['allow_lat_lon'] ) && $carspotAPI['allow_lat_lon'] == 1 )
	{				
		$data['profile']['map']['on_off'] = true;
	}
	else
	{
		$data['profile']['map']['on_off'] = false;	
	}

	$data['profile']['map']['location_lat'] = carspotAPI_getPostAdFields('textfield', 'location_lat', '', 0, __("Latitude", "carspot-rest-api"), '', $map_lat,'3', true, $map_lat);
	$data['profile']['map']['location_long'] = carspotAPI_getPostAdFields('textfield', 'location_long', '', 0, __("Longitude", "carspot-rest-api"), '', $map_long,'3' , true, $map_long);

	
	if( $pid != "" )
	{
		$images = carspotAPI_get_ad_image($pid, -1, 'thumb', false);
	}
	
	$data['ad_images'] = $images;
	
	

	$maxLimit = ( isset( $carspotAPI['sb_upload_limit'] ) ) ? $carspotAPI['sb_upload_limit'] : 5;
	$remaningImages = $maxLimit - count( $images );
	if( $remaningImages <= 0 ) 
	{
		$remaningImages = 0;
		$moer_message = __("you can not upload more images", "carspot-rest-api");
	}
	else
	{
		$moer_message = __("your can upload", "carspot-rest-api") .' '. $remaningImages .' '. __("more image", "carspot-rest-api");
	}		
		$data['ad_images'] = $images;
		$data['images']['is_show'] = true;
		$data['images']['numbers'] = $remaningImages;
		$data['images']['message'] = $moer_message;
		
		$data['btn_submit'] = __("Post Ad", "carspot-rest-api");
		$data['ad_cat_id'] = (isset( $ad_term_id ) && $ad_term_id != "") ? $ad_term_id : '';
		$data["update_notice"] = '';
		if(isset( $json_data['is_update'] ) && $json_data['is_update'] != "" ) 
		{
			if(isset( $carspotAPI['sb_ad_update_notice'] ) && $carspotAPI['sb_ad_update_notice'] != "" )
			{
				$data["update_notice"] = $carspotAPI['sb_ad_update_notice'];
			}
			
			
		}
	/*Bump Ads starts here */
	$bump_ad_is_show	= false;
	$message_title = '';
		
	if( isset( $carspotAPI['sb_allow_free_bump_up']	) && $carspotAPI['sb_allow_free_bump_up']  )
	{
		
		$bump_ad_is_show = false;
		$message_title = __("Bump it up on the top of the list. Ads remaining: Unlimited", "carspot-rest-api");
	}
	else if( get_user_meta( $user_id, '_sb_expire_ads', true ) == '-1' || get_user_meta( $user_id, '_sb_expire_ads', true ) >= date('Y-m-d') )
		{
			$bump_count = get_user_meta( $user_id, '_sb_bump_ads', true );
			if(  $bump_count > 0  || $bump_count == '-1')
			{
				if( $bump_count == '-1' )
				{
					$bump_ad_is_show = true;
					$message_title = __("Bump it up on the top of the list. Ads remaining: Unlimited", "carspot-rest-api");
				}
				else{
					$bump_ad_is_show = true;
					$message_title = __("Bump it up on the top of the list. Ads remaining: ", "carspot-rest-api") . get_user_meta( $user_id, '_sb_bump_ads', true );
				}
			}
		}
	$data['profile']['bump_ad_is_show']	= $bump_ad_is_show;
	if( $bump_ad_is_show )
	{
		$data['profile']['bump_ad_text']	=  array(	
			"title" => __("Confirmation", "carspot-rest-api"),
			"text" => __("Are you sure you want to bumup this ad.", "carspot-rest-api"),
			"btn_no" => __("Cancel", "carspot-rest-api"),
			"btn_ok" => __("Confirm", "carspot-rest-api"),
		);
		

		
		$data['profile']['bump_ad'] = carspotAPI_getPostAdFields('checkbox', 'ad_bump_ad', 'ad_bump_ad', 0, $message_title,'','','3', true, '', $is_update);		
	}
	/*Bump Ads ends here */
	
	
	/*Featured Ads starts here */
	$featured_ad_is_show = false;
	$featured_ad_title = '';
	
	$is_feature_ad	= get_post_meta($pid, '_carspot_is_feature', true);
	$is_feature_ad  = ( $is_feature_ad ) ? $is_feature_ad : 0;
	//$is_feature_ad = 0;
	$is_show_package = false;
	if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	{
		$featured_ad_title = __("If you want to make it feature then please have a look on", "carspot-rest-api");
		
		if( isset($carspotAPI['allow_featured_on_ad'] ) && $carspotAPI['allow_featured_on_ad'] )
		{	
		
			$isFeature = get_post_meta( $pid, '_carspot_is_feature', true );
			$isFeature = ( $isFeature ) ? $isFeature : 0;
			if( $isFeature != 1 )
			{
				$sb_expire_ads   = get_user_meta( $user_id, '_sb_expire_ads', true );
				$sb_featured_ads = get_user_meta( $user_id, '_sb_featured_ads', true );
				if ($is_feature_ad == 0 && ( $sb_expire_ads == '-1' || $sb_expire_ads >= date('Y-m-d') ) )
				{
					if( $sb_featured_ads == '-1' || $sb_featured_ads > 0 )
					{
						$featured_ad_title	= __('Featured ads remaining: Unlimited','carspot-rest-api');
						$featured_ad_is_show = true;
						if(  get_user_meta( $user_id, '_sb_featured_ads', true ) > 0 )
						{
							$featured_ad_title	= __('Featured ads remaining: ','carspot-rest-api') . get_user_meta( $user_id, '_sb_featured_ads', true );
						}
						$feature_text	=	'';
						if( isset( $carspotAPI['sb_feature_desc'] ) && $carspotAPI['sb_feature_desc'] != "" )
						{
							$feature_text = $carspotAPI['sb_feature_desc'];
						}					
						
					}
					else
					{
						
						$featured_notify = carspotAPI_adFeatured_notify( $pid );
						
						$is_show_package = true;
					}
				}
				else
				{
					
					$featured_notify = carspotAPI_adFeatured_notify( $pid );
					$is_show_package = true;
				}
			}
		}
		
	}
	$data['profile']['featured_ad_buy']	= $is_show_package;
	if( $is_show_package )
	{
		$data['profile']['featured_ad_notify']	= $featured_notify;
	}
	
	$data['profile']['featured_ad_is_show']	= $featured_ad_is_show;
	if( $featured_ad_is_show )
	{

	$data['profile']['featured_ad_text']	= array(	
	 	"title" => __("Confirmation", "carspot-rest-api"),
		"text" => __("Are you sure you want to make this ad featured.", "carspot-rest-api"),
		"btn_no" => __("Cancel", "carspot-rest-api"),
		"btn_ok" => __("Confirm", "carspot-rest-api"),
	);	
	

	$data['profile']['featured_ad'] = carspotAPI_getPostAdFields('checkbox', 'ad_featured_ad', 'ad_featured_ad', 0, $featured_ad_title,'','','3', true, '', $is_update);			
		
	}

	    /*Featured Ads starts ends */	
		$extra['image_text']     = __("Select Images", "carspot-rest-api");
		$extra['user_info']      = __("User Information", "carspot-rest-api");
		$extra['sort_image_msg'] = __("You can re-arange image by draging them.", "carspot-rest-api");
		$extra['dialog_send']    = __("Submit", "carspot-rest-api");
		$extra['dialg_cancel']   = __("Cancel", "carspot-rest-api");
		$extra['price_type_data']  	= carspotAPI_adPrice_types($ad_price_typeVal);
		$bidding_timer_show = ( $ad_bidding ) ? true : false;
		$extra['is_show_bidtime']  	= $bidding_timer_show;
		/*$request_from = carspotAPI_getSpecific_headerVal('carspot-Request-From');*/
		$cat_template_on = false;
		if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] )
		{
			$cat_template_on = true;
		}
		$data['cat_template_on'] = $cat_template_on;
		
		$response = array( 'success' => true, 'data' => $data , 'message' => '', 'extra' => $extra );	
		return $response;
}
}
   
   
if( !function_exists('carspotAPI_templating_func' ) )
{
	 function carspotAPI_templating_func()
	 {
		global $carspotAPI;
		if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true )
		{
			$form_type = 'yes';
		}
		else
		{
			$form_type = 'no';
		}
	 }
}
 
 
  
if( !function_exists('carspotAPI_ad_post_extra_fields' ) )
{
	function carspotAPI_ad_post_extra_fields( $ad_id = '')
	{  
	
	
		
		return '';

  		$extra_fields_html	=	'';
		// Making fields
		$arrays = array();
		
		$atts = WPBMap::getParam( 'ad_post_short_base', 'fields' );		
		
		return $atts['params'];
		//return $rows = vc_param_group_parse_atts( $atts['params'] );
		
		return $atts['params'];
		//if(isset($atts['fields'])){
		if( true ){	
		$rows = vc_param_group_parse_atts( $atts );
		if( count( $rows[0] ) > 0 && count( $rows ) > 0 )
		{
			$extra_section_title;
			
			
			foreach($rows as $row )
			{
				
				$has_page_number  = 2;
				$is_required = true; //( isset( $row['is_req'] ) && $row['is_req'] == 1  ) ? true : false;
				if( isset( $row['is_req'] ) && isset( $row['type'] ) && isset( $row['slug'] ) )
				{

					$name 		= esc_html( $row['title'] );
					$fieldName = 'sb_extra_'.$total_fileds;
					if( $row['type'] == 'text' )
					{
						$fieldValue = ( $ad_id != "" ) ? get_post_meta($ad_id, '_sb_extra_' . $row['slug'], true ) : "";
												
$arrays["$fieldName"] = 	array("main_title" => $name, "field_type" => 'textfield', "field_type_name" => $fieldName,"field_val" => "", "field_name" => "", "title" => $name, "values" => $fieldValue,"has_page_number" => $has_page_number, "is_required" => $is_required);	
					
					}
					if( $row['type'] == 'select' && isset( $row['option_values'] ) )
					{
						
						
						$termsArr[] = array ( "id" => "",  "name" => __("Select Option", "carspot-rest-api"), "has_sub" => false, "has_template" => false, );							
						
						$options	=	@explode( ',', $row['option_values'] );
						foreach( $options as $key => $value )
						{
							$is_select	=	'';
							if( $ad_id != "" )
							{
								$is_select ( $value == get_post_meta($ad_id, '_sb_extra_' . $row['slug'], true ) )? 'selected' :'';
							}
							
							$termsArr[] = array("id" => $value, "name" => $value, "has_sub" => false, "has_template" => false, );								
						
						}
					
			$arrays["$fieldName"] = array("main_title" => $name, "field_type" => 'textfield', "field_type_name" => $fieldName,"field_val" => "", "field_name" => "", "title" => $name, "values" => $fieldValue,"has_page_number" => $has_page_number, "is_required" => $is_required);	
					
					
					}
					
					$total_fileds++;
				}
			}
			
		}
		}
  		return $arrays;
		
	}
}
  

add_action( 'rest_api_init', 'carspotAPI_post_ad_post_hooks', 0 );
function carspotAPI_post_ad_post_hooks() {

    register_rest_route(
        'carspot/v1', '/post_ad/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_post_ad_post',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
}   
 
if ( ! function_exists( 'carspotAPI_post_ad_post' ) ) 
{
	function carspotAPI_post_ad_post( $request )
	{
		global $carspotAPI;
		$user = wp_get_current_user();	
		$user_id = $user->data->ID;			
				
		$json_data = $request->get_json_params();
		
		$ad_title 		 = isset( $json_data['ad_title'] ) ? trim($json_data['ad_title']) : '';
		$ad_cats  		 = isset( $json_data['ad_cats1'] ) ? $json_data['ad_cats1'] : '';
		$ad_country		 = isset( $json_data['ad_country'] ) ? $json_data['ad_country'] : '';
		
		
		$ad_description  = isset( $json_data['ad_description'] ) ? trim($json_data['ad_description']) : '';
				
								
		$ad_status = ( $carspotAPI['sb_ad_approval'] == 'manual' ) ? 'pending' : 'publish';
		
		$isUpdate	=	false;		
		if( ( isset( $json_data['ad_id'] ) && $json_data['ad_id'] != "" ) && ( isset( $json_data['is_update'] ) && $json_data['is_update'] != "" ) )
		{
			$pid	=	$json_data['ad_id'];
			$isUpdate	=	true;
			$ad_status = ( $carspotAPI['sb_update_approval'] == 'manual' ) ? 'pending' : 'publish';	
			
			
			if( carspot_API_ALLOW_EDITING == false )
			{
				$response = array( 'success' => false, 'data' => '' , 'message' => __("Editing Not Alloded In Demo", "carspot-rest-api") );
				return $response;
			}						
		}
		else
		{
			$pid	=	get_user_meta ( $user_id, 'ad_in_progress', true );	
			if($pid){} else{$pid	=	$json_data['ad_id'];}	
			delete_user_meta($user_id, 'ad_in_progress');	
			if( !is_super_admin( $user_id ) )
			{
				$simple_ads_check	=	get_user_meta($user_id, '_sb_simple_ads', true);
				$expiry_check	    =   get_user_meta( $user_id, '_sb_expire_ads', true );
				
				if( $simple_ads_check == -1 )
				{
					
				}
				else if( $simple_ads_check <= 0 )
				{
					$posted_msg = __("You have no more ads to post", "carspot-rest-api" );
					$response = array( 'success' => false, 'data' => '' , 'message' => $posted_msg, 'extra' => '' );
					return $response;
				}
				
				if( $expiry_check != '-1' )
				{
					if( $expiry_check < date('Y-m-d') )
					{
						
						$posted_msg = __("Your ad post date is expired", "carspot-rest-api" );
						$response = array( 'success' => false, 'data' => '' , 'message' => $posted_msg, 'extra' => '' );
						return $response;
						
					}
				}
			}
			
			$simple_ads	=	get_user_meta($user_id, '_sb_simple_ads', true);
			if( $simple_ads > 0 && !is_super_admin( $user_id ) )
			{
				$simple_ads	=	$simple_ads - 1;
				update_user_meta( $user_id, '_sb_simple_ads', $simple_ads );
			}
			
			
			update_post_meta($pid, '_carspot_ad_status_', 'active' );
			update_post_meta($pid, '_carspot_is_feature', '0' );
			carspotAPI_get_notify_on_ad_post($pid);			
			
		}
		
		
		$cats_arr =  carspotAPI_cat_ancestors( $ad_cats, 'ad_cats', false);
		/*$ad_country =  carspotAPI_cat_ancestors( $ad_country, 'ad_country', false);*/
		
		
		$is_imageallow = carspotAPI_CustomFieldsVals($pid, $cats_arr);  
		$media = get_attached_media( 'image', $pid );
		if($is_imageallow == 1 && count($media) == 0)
		{
			$response = array( 'success' => false, 'data' => '' , 'message' => __("Images are required", "carspot-rest-api") );
			return $response;

		}		

		global $wpdb;
		$qry = "UPDATE $wpdb->postmeta SET meta_value = '' WHERE post_id = '$pid' AND meta_key LIKE '_carspot_tpl_field_%'";
		@$wpdb->query( $qry );
		
		$words		=	@explode( ',', $carspotAPI['bad_words_filter'] );
		$replace	=	$carspotAPI['bad_words_replace'];
		$desc		=	carspotAPI_badwords_filter( $words, $ad_description, $replace );
		$title		=	carspotAPI_badwords_filter( $words, $ad_title, $replace );
				
		$my_post 	= array(
			'ID'           => $pid,
			'post_title'   => $title,
			'post_content'   => $desc,
			'post_type'   => 'ad_post',
			'post_name' => $title,
		);	
	
		$pid 		= wp_update_post( $my_post );				
		
		global $wpdb;
		$wpdb->query( "UPDATE $wpdb->posts SET post_status = '$ad_status' WHERE ID = '$pid' "  );
		$catsArr	=  carspotAPI_cat_ancestors( $ad_cats, 'ad_cats', true);
		wp_set_post_terms( $pid, $catsArr, 'ad_cats' );	
	
	$is_show_location  = wp_count_terms( 'ad_country' );
	if( isset($is_show_location) && $is_show_location > 0 )
	{
		$ad_countryArr	=  carspotAPI_cat_ancestors( $ad_country, 'ad_country', true);
		wp_set_post_terms( $pid, $ad_countryArr, 'ad_country' );	
	}

	/* ads  */
	$ad_yvideo = '';

	$custom_fields = @$json_data['custom_fields'];
	$request_from = carspotAPI_getSpecific_headerVal('carspot-Request-From');
	if( $request_from == 'ios' )
	{
			$custom_fields = json_decode(@$json_data['custom_fields'], true);
	}

	
	$custom_fields   = isset( $custom_fields ) ? $custom_fields : array();	
	$in_loop = false;
	
	$in_loop = $ad_yvideo = $ad_price = $ad_price_types = $ad_currency =  $ad_warranty = $ad_type =  $ad_condition = $ad_tags = '';
	if( isset($custom_fields) && count( $custom_fields ) > 0)
	{		
		if( $request_from == 'ios' )
		{
			
			$ad_condition   	= isset( $custom_fields['ad_condition'] ) ? $custom_fields['ad_condition'] : '';
			$ad_tags   			= isset( $custom_fields['ad_tags'] ) ? $custom_fields['ad_tags'] : '';	
			$ad_type   			= isset( $custom_fields['ad_type'] ) ? $custom_fields['ad_type'] : '';	
			$ad_warranty   		= isset( $custom_fields['ad_warranty'] ) ? $custom_fields['ad_warranty'] : '';	
			$ad_currency   		= isset( $custom_fields['ad_currency'] ) ? $custom_fields['ad_currency'] : '';	
			$ad_price_types   	= isset( $custom_fields['ad_price_types'] ) ? $custom_fields['ad_price_types'] : '';	
			$ad_price   		= isset( $custom_fields['ad_price'] ) ? $custom_fields['ad_price'] : '';	
			$ad_yvideo   		= isset( $custom_fields['ad_yvideo'] ) ? $custom_fields['ad_yvideo'] : '';		
		}
		else
		{
			$ad_tags 		= isset( $json_data['custom_fields']['ad_tags'] ) ? trim($json_data['custom_fields']['ad_tags']) : '';		
			$ad_condition   = isset( $json_data['custom_fields']['ad_condition'] ) ? $json_data['custom_fields']['ad_condition'] : '';	
			$ad_type  	    = isset( $json_data['custom_fields']['ad_type'] ) ? $json_data['custom_fields']['ad_type'] : '';	
			$ad_warranty   	= isset( $json_data['custom_fields']['ad_warranty'] ) ? $json_data['custom_fields']['ad_warranty'] : '';	
			$ad_currency   	= isset( $json_data['custom_fields']['ad_currency'] ) ? $json_data['custom_fields']['ad_currency'] : '';
			$ad_price_types	= isset( $json_data['custom_fields']['ad_price_type'] ) ? $json_data['custom_fields']['ad_price_type'] : '';
			$ad_price   	= isset( $json_data['custom_fields']['ad_price'] ) ? $json_data['custom_fields']['ad_price'] : '';
			$ad_yvideo   	= isset( $json_data['custom_fields']['ad_yvideo'] ) ? $json_data['custom_fields']['ad_yvideo'] : '';
		}
		$in_loop = $custom_fields;
	}
	else
	{
		$ad_tags 		= isset( $json_data['ad_tags'] ) ? trim($json_data['ad_tags']) : '';		
		$ad_condition   = isset( $json_data['ad_condition'] ) ? $json_data['ad_condition'] : '';	
		$ad_type  	    = isset( $json_data['ad_type'] ) ? $json_data['ad_type'] : '';	
		$ad_warranty   	= isset( $json_data['ad_warranty'] ) ? $json_data['ad_warranty'] : '';	
		$ad_currency   	= isset( $json_data['ad_currency'] ) ? $json_data['ad_currency'] : '';
		$ad_price_types	= isset( $json_data['ad_price_type'] ) ? $json_data['ad_price_type'] : '';
		$ad_price   	= isset( $json_data['ad_price'] ) ? $json_data['ad_price'] : '';
		$ad_yvideo   	= isset( $json_data['ad_yvideo'] ) ? $json_data['ad_yvideo'] : '';
	}
	/* ads  */
	
	
	$arrty = array($in_loop , $ad_yvideo, $ad_price, $ad_price_types, $ad_currency, $ad_warranty, $ad_type, $ad_condition, $ad_tags);

	
	$ad_yvideo			= update_post_meta($pid, '_carspot_ad_yvideo', $ad_yvideo );
	
	$tags	=	explode(',', $ad_tags );
	wp_set_object_terms($pid, $tags, 'ad_tags');
		
	$ad_bidding  = isset( $json_data['ad_bidding'] ) ? $json_data['ad_bidding'] : '';		
	update_post_meta($pid, '_carspot_ad_bidding', $ad_bidding );	

	$ad_bidding_time_save  = isset( $json_data['ad_bidding_time'] ) ? $json_data['ad_bidding_time'] : '';		
	update_post_meta($pid, '_carspot_ad_bidding_date', $ad_bidding_time_save );	
	
	$conditon_name = carspotAPI_adPost_update_terms($pid, $ad_condition, 'ad_condition' );
	update_post_meta($pid, '_carspot_ad_condition', $conditon_name );
	
	$ad_type_name  = carspotAPI_adPost_update_terms($pid, $ad_type, 'ad_type' );
	update_post_meta($pid, '_carspot_ad_type', $ad_type_name);
	
	$ad_warranty_name 	= carspotAPI_adPost_update_terms($pid, $ad_warranty, 'ad_warranty' );
	update_post_meta($pid, '_carspot_ad_warranty', $ad_warranty_name );	
	
	$ad_currency_name 	= carspotAPI_adPost_update_terms($pid, $ad_currency, 'ad_currency' );
	update_post_meta($pid, '_carspot_ad_currency', $ad_currency_name );	
	
	update_post_meta($pid, '_carspot_ad_price_type', $ad_price_types );
	
	
	if( $ad_price_types == "on_call" || $ad_price_types == "free" || $ad_price_types == "no_price" )
	{
		$ad_price = '';
	}
	update_post_meta($pid, '_carspot_ad_price', $ad_price );
		
	$ad_owner_name   		= isset( $json_data['name'] ) ? $json_data['name'] : '';
	$ad_owner_ad_phone		= isset( $json_data['ad_phone'] ) ? $json_data['ad_phone'] : '';
	$ad_owner_ad_location	= isset( $json_data['ad_location'] ) ? $json_data['ad_location'] : '';
	$ad_owner_location_lat	= isset( $json_data['location_lat'] ) ? $json_data['location_lat'] : '';
	$ad_owner_location_long	= isset( $json_data['location_long'] ) ? $json_data['location_long'] : '';
	
	/*Store image for sorting*/
	$images_arr	= isset( $json_data['images_arr'] ) ? $json_data['images_arr'] : '';
	if( $images_arr != "" )
	{
		  $img_ids	= trim($images_arr, ',');
		  update_post_meta( $pid, '_sb_photo_arrangement_', $img_ids );
		
	}
	else
	{
		  update_post_meta( $pid, '_sb_photo_arrangement_', '' );	
	}
	
	
	/* Update User Info */
	update_post_meta($pid, '_carspot_poster_name', $ad_owner_name );
	update_post_meta($pid, '_carspot_poster_contact', $ad_owner_ad_phone );
	update_post_meta($pid, '_carspot_ad_location', $ad_owner_ad_location );
	update_post_meta($pid, '_carspot_ad_map_lat', $ad_owner_location_lat );
	update_post_meta($pid, '_carspot_ad_map_long', $ad_owner_location_long );
	/* Ad extra fields post meta starts */
	/*
	$sb_extra_fields   = isset( $json_data['sb_extra_fields'] ) ? $json_data['sb_extra_fields'] : '';	
	if( isset( $sb_extra_fields ) && count( $sb_extra_fields ) > 0)
	{
		for( $i = 1; $i <= $params['sb_total_extra']; $i++ )
		{
			update_post_meta($pid, "_sb_extra_" . $params["title_$i"], $params["sb_extra_$i"] );
		}
	}	
	*/
	/* Ad extra fields post meta ends */
	/* Ad Dynamic ad post meta starts */
	//$custom_fields   = isset( $json_data['custom_fields'] ) ? (array)$json_data['custom_fields'] : array();	
	if( isset($custom_fields) && count( $custom_fields ) > 0)
	{
		$ios_array = array();

		foreach($custom_fields as $key => $data)
		{
			if( $request_from == 'ios' )
			{
				$is_chckbox = carspotAPI_post_ad_check_if_checkbox($ad_cats, 3, $key);
				if( $is_chckbox )
				{
					$data = @explode(",", $data);	
				}
			}
			

			if( is_array($data) )
			{
				$dataArr = array();
				foreach($data as $k ) $dataArr[] = $k; 
				$data = stripslashes(json_encode($dataArr, true));
			}

			
			$dataVal = ltrim( $data , ",");
			$dataKey = "_carspot_tpl_field_".$key;
			update_post_meta($pid, $dataKey, $dataVal );
		}
	}		
	/* Ad Dynamic ad post meta ends */
	
	if( $isUpdate == true )
	{
		delete_user_meta($user_id, 'ad_in_progress');//
	}
	
	/*Featured Ads Start*/
	$posted_featured = '';
	if( isset( $json_data['ad_featured_ad'] ) && $json_data['ad_featured_ad'] == 'true' )
	{
		// Uptaing remaining ads.
		$featured_ad	=	get_user_meta($user_id, '_sb_featured_ads', true);
		if( $featured_ad > 0 || $featured_ad == '-1' )
		{
			update_post_meta($pid, '_carspot_is_feature', '1' );
			update_post_meta( $pid, '_carspot_is_feature_date', date( 'Y-m-d' ) );
			$featured_ad2 = $featured_ad;
			$featured_ad	=	$featured_ad - 1;
			if($featured_ad2 != '-1' )
			{
				update_user_meta( $user_id, '_sb_featured_ads', $featured_ad );
			}
			
			$posted_featured = ' '.__("And Marked As Featured.", "carspot-rest-api");
		}

	}	
	/*Featured Ads Ends */
	
	
	if( ( isset( $json_data['ad_id'] ) && $json_data['ad_id'] != "" ) && ( isset( $json_data['is_update'] ) && $json_data['is_update'] != "" ))
	{
		
		$posted_bump = '';
		/*Bumping it up*/
		if( isset( $json_data['ad_bump_ad'] ) && $json_data['ad_bump_ad']  == 'true')
		{
			// Uptaing remaining ads.
			$bump_ads	=	get_user_meta($user_id, '_sb_bump_ads', true);
			$allow_unlimited = (isset( $carspotAPI['sb_allow_free_bump_up']	) && $carspotAPI['sb_allow_free_bump_up'] ) ? true : false;
			/*if( $bump_ads > 0 || isset( $carspotAPI['sb_allow_free_bump_up']	) && $carspotAPI['sb_allow_free_bump_up'] )*/
			if( $bump_ads > 0 || $allow_unlimited == true  || $bump_ads == '-1')
			{
				wp_update_post(
					array (
						'ID'            => $pid, // ID of the post to update
						'post_date'     => current_time('mysql'),
						'post_date_gmt' => get_gmt_from_date( current_time('mysql') )
					)
				);
				if( !$carspotAPI['sb_allow_free_bump_up'] && $bump_ads != "-1")
				{
					$bump_ads	=	$bump_ads - 1;
					update_user_meta( $user_id, '_sb_bump_ads', $bump_ads );
				}
				$posted_bump = ' '.__("And Marked As Bumped Ad.", "carspot-rest-api");
			}
	
		}	
		
		$posted = __("Ad Updated Successfully.", "carspot-rest-api") .$posted_featured. $posted_bump;
		
	}	
	else
	{
		$posted = __("Ad Posted Successfully", "carspot-rest-api"). $posted_featured;
	}
	
	
	$postdData['ad_id'] = $pid;
	$response = array( 'success' => true, 'data' => $postdData , 'message' => $posted, 'extra' => $json_data);
	return $response;
	}	
}


add_action( 'rest_api_init', 'carspotAPI_postad_image_delete', 0 );
function carspotAPI_postad_image_delete() {

    register_rest_route(
        'carspot/v1', '/post_ad/image/delete', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_delete_ad_image',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );	
	
} 

if ( ! function_exists( 'carspotAPI_delete_ad_image' ) ) 
{
	function carspotAPI_delete_ad_image($request )
	{
		
	$json_data = $request->get_json_params();		
	$attachmentID		= (isset($json_data['img_id'])) 		? $json_data['img_id'] : '';
	$ad_id		= (isset($json_data['ad_id'])) 		? $json_data['ad_id'] : '';			
		
		if( $attachmentID == '' || $ad_id == '')
		{
			$message = 	__("Something went wrong", "carspot-rest-api");
			$success = false;	
		}
		else
		{
			$deleteImg = wp_delete_attachment( $attachmentID );
			if( $deleteImg )
			{
				
				 if( get_post_meta( $ad_id, '_sb_photo_arrangement_', true ) != "" )
				 {
					  $ids 		= get_post_meta( $ad_id, '_sb_photo_arrangement_', true );
					  $res 		=  str_replace($attachmentID, "", $ids);
					  $res 		=  str_replace(',,', ",", $res);
					  $img_ids	= trim($res,',');
					  update_post_meta( $ad_id, '_sb_photo_arrangement_', $img_ids );
				 }				
				$message = 	__("Image deleted successfully.", "carspot-rest-api");		
				$success = true;			
			}
			else
			{
				$message = 	__("Something went wrong", "carspot-rest-api");
				$success = false;
			}
		}
		$images = array();
		if( $ad_id != "" )
		{
			$images = carspotAPI_get_ad_image($ad_id, 5, 'thumb', false);
		}
		
		$maxLimit = ( isset( $carspotAPI['sb_upload_limit'] ) ) ? $carspotAPI['sb_upload_limit'] : 5;
		$remaningImages = $maxLimit - count( $images );
		
		if( $remaningImages <= 0 ) 
		{
			$remaningImages = 0;
			$moer_message = __("your can not upload more images", "carspot-rest-api");
		}
		else
		{
			$moer_message = __("your can upload", "carspot-rest-api") .' '. $remaningImages .' '. __("more image", "carspot-rest-api");
		}		
				
		$data['ad_images'] = $images;
		$data['images']['is_show'] = true;
		$data['images']['numbers'] = $remaningImages;
		$data['images']['message'] = $moer_message;
		
		
		return $response = array( 'success' => $success, 'data' => $data, 'message'  => $message );		
		
	}
	
}

add_action( 'rest_api_init', 'carspotAPI_postad_image', 0 );
function carspotAPI_postad_image() {

    register_rest_route(
        'carspot/v1', '/post_ad/image/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_upload_ad_image',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );

	
} 


if (!function_exists('carspotAPI_upload_ad_limit'))
{
	function carspotAPI_upload_ad_limit( $ad_id = '' )
	{ 
	

		$images = array();
		if( $ad_id != "" )
		{
			global $carspotAPI;
			$maxLimit = ( isset( $carspotAPI['sb_upload_limit'] ) ) ? $carspotAPI['sb_upload_limit'] : 5;
				
			$images = carspotAPI_get_ad_image($ad_id, -1, 'carspot-single-small');
			
			$remaningImages = $maxLimit - count( $images );
			
			
		if( $remaningImages <= 0 ) 
		{
			$remaningImages = 0;
			$moer_message = __("your can not upload more images", "carspot-rest-api");
		}
		else
		{
			$moer_message = __("your can upload", "carspot-rest-api") .' '. $remaningImages .' '. __("more image", "carspot-rest-api");
		}			
			
			
			$data['ad_images'] = $images;
			$data['images']['is_show'] = true;
			$data['images']['numbers'] = $remaningImages;
			$data['images']['message'] = $moer_message;
	
		}
	

	}
}

if (!function_exists('carspotAPI_upload_ad_image'))
{
	function carspotAPI_upload_ad_image( $request )
	{ 
		$ad_id =  ( isset( $_POST['ad_id'] ) && $_POST['ad_id'] != "" ) ? $_POST['ad_id'] : '';

		$extra['ad_id'] = $ad_id ;
		$extra['images'] = $_FILES;

		global $carspotAPI;
  		if( $ad_id == '' ) 
		{
			
		}


		$success  = false;
		$imgArr = array();
		$is_size_error1 = false;

		if( isset($_FILES) && count( $_FILES ) > 0 )
		{
			global $wpdb;
			//if ( ! function_exists( 'wp_handle_upload' ) ) {
				 require_once ABSPATH . 'wp-admin/includes/image.php';
				 require_once ABSPATH . 'wp-admin/includes/file.php';
				 require_once ABSPATH . 'wp-admin/includes/media.php';					
			//}
			
			
			foreach($_FILES as $key => $val)
			{
				$user = wp_get_current_user();	
				$user_id = $user->data->ID;

				$uploadedfile = $_FILES["$key"];
				/******* user_photo Upload code ************/
				$upload_overrides = array( 'test_form' => false );

				if( isset( $carspotAPI['sb_standard_images_size'] ) && $carspotAPI['sb_standard_images_size'] )
				{
					$uploadedfile_tmp_name = $_FILES["$key"]["tmp_name"];
					list($width, $height) = ( getimagesize( $uploadedfile_tmp_name ) );
					$is_size_error = false;
					if( $width < 760 ){ $is_size_error = true; }
					if( $height < 410 ){ $is_size_error = true; }
					if($is_size_error)
					{
						$is_size_error1 = true;
						continue;
					}
				}

				$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
				/*Added On 3 March 2018*/
				 if ( $movefile && !isset( $movefile['error'] ) ) 
				 {
					/******* Assign image to ad ************/
					$filename 		= $movefile['url'];	
					$absolute_file	= $movefile['file'];
					$parent_post_id = $ad_id;
					$filetype = wp_check_filetype( basename( $filename ), null );
	
					$wp_upload_dir = wp_upload_dir();
					$attachment = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
						'post_mime_type' => $filetype['type'],
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
						'post_content'   => '',
						/*'post_status'    => 'inherit'*/
					);
	
					// Insert the attachment.
					$attach_id = wp_insert_attachment( $attachment, $absolute_file, $parent_post_id );
					
					
					$wpdb->query( "UPDATE $wpdb->posts SET post_status = 'inherit' WHERE ID = '$attach_id' "  );
					
					
					//require_once( ABSPATH . 'wp-admin/includes/image.php' );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $absolute_file );
					//$attach_data = wp_get_attachment_image( $attach_id );
					wp_update_attachment_metadata( $attach_id, $attach_data );
					set_post_thumbnail( $parent_post_id, $attach_id );
					$imgaes = get_post_meta( $ad_id, '_sb_photo_arrangement_', true );
					 if( $imgaes != "" )
					 {
					  $imgaes = $imgaes .',' . $attach_id;
					  update_post_meta( $ad_id, '_sb_photo_arrangement_', $imgaes ); 
					 }					
					$imgArr[] = $movefile['url'];
				 }
			}	
			$success  = true;
		} 

		$images = array();
		if( $ad_id != "" )
		{
			$images = carspotAPI_get_ad_image($ad_id, -1, 'thumb', false);
		}

		$maxLimit = ( isset( $carspotAPI['sb_upload_limit'] ) ) ? $carspotAPI['sb_upload_limit'] : 5;
		$remaningImages = $maxLimit - count( $images );
		if( $remaningImages <= 0 ) {$remaningImages = 0;}		
		$data['ad_images'] = $images;
		$data['images']['is_show'] = true;
		$data['images']['numbers'] = $remaningImages;
		$data['images']['message'] = __("your upload ad limit is ", "carspot-rest-api") . $remaningImages;		

		$message  = '';
		if($is_size_error1)
		{
			$message =  __( "Minimum image dimension should be", 'carspot-rest-api' ) . ' 760x410';
			$success  = false;
		}
		$response = array( 'success' => $success, 'data' => $data , 'message' => $message , "eaxtra" => $extra );
		return $response;
	}
}

if ( ! function_exists( 'carspotAPI_adPost_update_terms' ) ) 
{
	function carspotAPI_adPost_update_terms($pid = '', $term_val = '', $term_type = '' )
	{
		if( $pid == '' || $term_val == '' ) return '';
		$term = get_term( $term_val, $term_type );
		wp_set_post_terms( $pid, $term_val, $term_type );

		return ( isset( $term->name ) && $term->name != "" ) ? $term->name : '';
	}
	
}


if ( ! function_exists( 'carspotAPI_cat_ancestors' ) ) 
{
	function carspotAPI_cat_ancestors( $ad_cats = '', $term_type = 'ad_cats', $reverse_arr = true )
	{
		if( $ad_cats == "" ) return '';
		
		$ad_cats_ids = get_ancestors( $ad_cats,  $term_type);
		$adsID[] = (int)$ad_cats;
		if( isset( $ad_cats_ids ) && count( $ad_cats_ids ) > 0 )
		{
			foreach( $ad_cats_ids as $cid )
			{
				$adsID[] = $cid;
			}
			
		}	
		
		return ( $reverse_arr == false ) ? $adsID : array_reverse( $adsID );
	
	}
}
 
  
  
add_action( 'rest_api_init', 'carspotAPIpost_ad_subcats_get', 0 );
function carspotAPIpost_ad_subcats_get() {
    register_rest_route( 
		'carspot/v1', '/post_ad/subcats/', array(
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'carspotAPI_post_ad_subcats',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );
}

function carspotAPI_post_ad_subcats( $request ) {
	
	$json_data  = $request->get_json_params();	
	$subcat		= (isset($json_data['subcat'])) ? $json_data['subcat'] : '';
	$mainTerm   = get_term( $subcat );
	$mainTermName =  htmlspecialchars_decode($mainTerm->name, ENT_NOQUOTES);

	$data = carspotAPI_getSubCats('select',  'ad_cats1', 'ad_cats', $subcat, $mainTermName, '', false);
	return $response = array( 'success' => true, 'data' => $data, 'message'  => '' );	
}  
  
 add_action( 'rest_api_init', 'carspotAPIpost_ad_sublocations_get', 0 );
function carspotAPIpost_ad_sublocations_get() {
    register_rest_route( 
		'carspot/v1', '/post_ad/sublocations/', array(
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'carspotAPI_post_ad_sublocations',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );
} 
if( !function_exists('carspotAPI_post_ad_sublocations' ) )
{
	function carspotAPI_post_ad_sublocations( $request ) {
		
		$json_data = $request->get_json_params();	
		$subcat		= (isset($json_data['ad_country'])) ? $json_data['ad_country'] : '';
		$mainTerm = get_term( $subcat );
		$mainTermName =  htmlspecialchars_decode($mainTerm->name, ENT_NOQUOTES);
	
		$data = carspotAPI_getSubCats('select',  'ad_country', 'ad_country', $subcat, $mainTermName, '', false);
		return $response = array( 'success' => true, 'data' => $data, 'message'  => '' );
	
	}
}    
 
 
if( !function_exists('carspotAPI_post_ad_check_if_checkbox' ) )
{
	function carspotAPI_post_ad_check_if_checkbox($term_id = '', $check_type = '', $slugs = '')
	{ 
			if( $term_id == "" ) return '';
			$formData  = '';
 			$temp_term_id = carspot_dynamic_templateID($term_id);
			$template_result = get_term_meta( $temp_term_id , '_sb_dynamic_form_fields' , true);
			$myArray = array();
			if(isset($template_result) && $template_result != "")
			{
				$formData = sb_dynamic_form_data($template_result);
				foreach( $formData as $lists )
				{
					if( $lists['types'] != "" && $lists['slugs'] == $slugs && $check_type == $lists['types']  )
					{
						$myArray[] = $lists['types'];
					}
				}
			}
 		return in_array($check_type, $myArray);	
	}
}
 
add_action( 'rest_api_init', 'carspotAPIpost_ad_cat_fields_get', 0 );
function carspotAPIpost_ad_cat_fields_get() {
    register_rest_route( 
		'carspot/v1', '/post_ad/dynamic_fields/', array(
        'methods'  => WP_REST_Server::EDITABLE,
        'callback' => 'carspotAPI_post_ad_fields',
		'permission_callback' => function () { return carspotAPI_basic_auth();  },
    ) );
}  
  
 
  
if( !function_exists('carspotAPI_post_ad_fields' ) )
{
	function carspotAPI_post_ad_fields( $request, $is_termID = '', $ad_id = '' )
	{
		global $carspotAPI;
		
		
		if(isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == false)
		{
			return $response = array( 'success' => true, 'data' => '', 'message'  => '', 'extras' => '' );	
		}	

		if( $is_termID != "" )
		{
			$term_id = $is_termID;
		}
		else
		{
			$json_data  = $request->get_json_params();	
			$term_id	= (isset($json_data['cat_id'])) ? $json_data['cat_id'] : '';
			$ad_id		= (isset($json_data['ad_id']))  ? $json_data['ad_id']  : '';
		}

		
		$arrays	    = array();
		//$result2    = carspotAPI_categoryForm_data($ad_id);
		$result 	= carspot_dynamic_templateID($term_id);		
		$templateID = get_term_meta( $result , '_sb_dynamic_form_fields' , true);	
		
		
		$type 		= 	sb_custom_form_data($templateID, '_sb_default_cat_ad_type_show');		
		$price 		= 	sb_custom_form_data($templateID, '_sb_default_cat_price_show');	
		$priceType 	= 	sb_custom_form_data($templateID, '_sb_default_cat_price_type_show');
		$condition 	= 	sb_custom_form_data($templateID, '_sb_default_cat_condition_show');
		$warranty 	= 	sb_custom_form_data($templateID, '_sb_default_cat_warranty_show');
		$tags 		= 	sb_custom_form_data($templateID, '_sb_default_cat_tags_show');
		$video 		= 	sb_custom_form_data($templateID, '_sb_default_cat_video_show');
		
		$pid = $is_update = $ad_id;
		
		
		$ad_type 			= get_post_meta($pid, '_carspot_ad_type', true);
		$ad_condition 		= get_post_meta($pid, '_carspot_ad_condition', true );
		$ad_warranty		= get_post_meta($pid, '_carspot_ad_warranty', true );
		$ad_price			= get_post_meta($pid, '_carspot_ad_price', true );
		$ad_price_typeVal  	= get_post_meta($pid, '_carspot_ad_price_type', true );	
		$ad_yvideo			= get_post_meta($pid, '_carspot_ad_yvideo', true );
		$ad_bidding 		= get_post_meta($pid, '_carspot_ad_bidding', true );
		$ad_bidding_time	= get_post_meta($pid, '_carspot_ad_bidding_date', true );
		$ad_currency		= get_post_meta($pid, '_carspot_ad_currency', true);
		$tags_array 		= wp_get_object_terms( $pid,  'ad_tags', array('fields' => 'names') );
		$ad_tags			= @implode( ',', $tags_array );	
		
		$showcatData = false;
		if(isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true)
		{
			$showcatData = true;
		}
		
		
			
			if( $type == 1 && $templateID != "" && $showcatData == true )
			{		
				$arrays[] = carspotAPI_getPostAdFields('select' , 'ad_type', 'ad_type', 0, __("Ad Type", "carspot-rest-api"),'', '', '2', true, '', $is_update);	
			}
			else if( $templateID == "" || $showcatData == false)
			{
				$arrays[] = carspotAPI_getPostAdFields('select' , 'ad_type', 'ad_type', 0, __("Ad Type", "carspot-rest-api"),'', '', '2', true, '', $is_update);	
			}	
			
			
			if( $priceType == 1 && $templateID != "" && $showcatData == true )
			{
				$ad_price_type  	= carspotAPI_adPrice_types($ad_price_typeVal);
				$arrays[] 	= carspotAPI_getPostAdFields('select', 'ad_price_type', $ad_price_type, 0, __("Price Type", "carspot-rest-api"), 'Price Type', '', 2, false, $ad_price_typeVal, $is_update);
							
			}
			else if( $templateID == "" || $showcatData == false)
			{
				$ad_price_type  	= carspotAPI_adPrice_types($ad_price_typeVal);
				$arrays[] 	= carspotAPI_getPostAdFields('select', 'ad_price_type', $ad_price_type, 0, __("Price Type", "carspot-rest-api"), 'Price Type', '', 2, false, $ad_price_typeVal, $is_update);
			}				

			
			if( $condition == 1 && $templateID != "" && $showcatData == true )
			{
				$arrays[] = carspotAPI_getPostAdFields('select','ad_condition','ad_condition',0, __("Condition", "carspot-rest-api"),'', '', '2', false, '', $is_update);
			}	
			else if( $templateID == "" || $showcatData == false)
			{
				$arrays[] = carspotAPI_getPostAdFields('select','ad_condition','ad_condition',0, __("Condition", "carspot-rest-api"),'', '', '2', false, '', $is_update);
			}			
				
			if( $warranty == 1 && $templateID != "" && $showcatData == true )
			{
				$arrays[] = carspotAPI_getPostAdFields('select', 'ad_warranty', 'ad_warranty', 0, __("Warranty", "carspot-rest-api"),'', '', '2', false, '', $is_update);
			}
			else if( $templateID == "" || $showcatData == false)
			{
				$arrays[] = carspotAPI_getPostAdFields('select', 'ad_warranty', 'ad_warranty', 0, __("Warranty", "carspot-rest-api"),'', '', '2', false, '', $is_update);
			}			
			
			if( $tags == 1 && $templateID != "" && $showcatData == true )
			{
				$arrays[] = carspotAPI_getPostAdFields('textfield'  , 'ad_tags', '', 0, __("Tags Comma(,) separated", "carspot-rest-api"), '', '', '2', false, $ad_tags);
			}
			else if( $templateID == "" || $showcatData == false)
			{
				$arrays[] = carspotAPI_getPostAdFields('textfield'  , 'ad_tags', '', 0, __("Tags Comma(,) separated", "carspot-rest-api"), '', '', '2', false, $ad_tags);
			}	
						
			if( $video == 1 && $templateID != "" && $showcatData == true )
			{
				$arrays[] = carspotAPI_getPostAdFields('textfield'  , 'ad_yvideo', '', 0, __("Youtube Video Link", "carspot-rest-api"), '', '', '2', false, $ad_yvideo);
			}
			else if( $templateID == "" || $showcatData == false)
			{
				$arrays[] = carspotAPI_getPostAdFields('textfield'  , 'ad_yvideo', '', 0, __("Youtube Video Link", "carspot-rest-api"), '', '', '2', false, $ad_yvideo);
			}		
		
		
		$extras['hide_price']    = array("ad_price","ad_price_type");
		$extras['hide_currency'] = array("ad_price","ad_currency");
	
		/*if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true )
		if( isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] != true )
		{
			return $response = array( 'success' => true, 'data' => '', 'message'  => '' );
		}*/	
		
		if(isset($templateID) && $templateID != "" && isset($carspotAPI['adpost_cat_template']) && $carspotAPI['adpost_cat_template'] == true)
		{
			
				$formData = sb_dynamic_form_data($templateID);	
				foreach($formData as $r)
				{
					$is_required = ( isset( $r['requires'] ) && $r['requires'] == 1  ) ? true : false;
					if( isset($r['types']) && trim($r['types']) != "" && isset($r['status']) && trim($r['status']) == 1) {
						
						///////Make chnages here
						$in_search = (isset($r['in_search']) && $r['in_search'] == "yes") ? 1 : 0;
						if($r['titles'] != "" && $r['slugs'] != ""){	
						
							$mainTitle = $name = $r['titles'];
							$fieldName = $r['slugs'];
							$fieldValue = (isset($_GET["custom"]) && isset($_GET['custom'][$r['slugs']])) ? $_GET['custom'][$r['slugs']] : '';
							//Inputs
							
							
							
							
							$postMetaName  = '_carspot_tpl_field_'.$fieldName;
							$nameValue	= get_post_meta($ad_id, $postMetaName, true);
							$nameValue  = ( $nameValue ) ? $nameValue : '';
							if(isset($r['types'] ) && $r['types'] == 1)
							{
							
								
							
	$arrays[] = 	array("main_title" => $mainTitle, "field_type" => 'textfield', "field_type_name" => $fieldName,"field_val" => $nameValue, "field_name" => "", "title" => $name, "values" => $fieldValue,"has_page_number" => 2, "is_required" => $is_required);							
							
							
							}
							//select option
							if(isset($r['types'] ) && $r['types'] == 2 || isset($r['types'] ) && $r['types'] == 3)
							{
								
							$varArrs = @explode("|", $r['values']);
							
							$varArrs = carspotAPI_arraySearch($varArrs, '', $nameValue);
							
							$termsArr = array();	
							if($r['types'] == 2 && $nameValue == ""  )
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
								
								if( $r['types'] == 3) 
								{
									$is_checked = false;
									if( $nameValue == $v) { $is_checked = true;}
									
									$termsArr[] = array
										(
											"id" => $v, 
											"name" => $v,
											"has_sub" => false,
											"has_template" => false,
											"is_checked" => $is_checked
										);								
									
									
								}
								else
								{
									$termsArr[] = array
										(
											"id" => $v, 
											"name" => $v,
											"has_sub" => false,
											"has_template" => false,
										);								
									
								}
							}
						
							$ftype = ($r['types'] == 2 ) ? 'select' : 'checkbox';

							$arrays[] = 	array("main_title" => $mainTitle, "field_type" => $ftype, "field_type_name" => $fieldName,"field_val" => $nameValue, "field_name" => "", "title" => $name, "values" => $termsArr,"has_page_number" => 2, "is_required" => $is_required);	
							}	
						}
					}
				}
		}
		
		
		
		
		if( $is_termID != "" )
		{
			return $arrays;
		}
		else
		{
			$request_from = carspotAPI_getSpecific_headerVal('carspot-Request-From');
			if( $request_from == 'ios' )
			{
				$newArr['fields'] = $arrays;
				return $response = array( 'success' => true, 'data' => $newArr, 'message'  => '', 'extras' => $extras );
			}
			else
			{
				return $response = array( 'success' => true, 'data' => $arrays, 'message'  => '', 'extras' => $extras );
			}
		}
	}
	
}
/**
 * Auto Complete all WooCommerce orders.
 */
$my_theme = wp_get_theme();
if( $my_theme->get( 'Name' ) != 'carspot' && $my_theme->get( 'Name' ) != 'carspot child' )
{
	add_action( 'woocommerce_thankyou', 'carspotAPI_custom_woocommerce_auto_complete_order' );
}
if (!function_exists('carspotAPI_custom_woocommerce_auto_complete_order'))
{
	function carspotAPI_custom_woocommerce_auto_complete_order( $order_id ) 
	{ 
		if ( ! $order_id ) { return; }
		
		global $carspotAPI;
		if( isset( $carspotAPI['sb_order_auto_approve'] ) && $carspotAPI['sb_order_auto_approve'] )
		{
		  $order = wc_get_order( $order_id );
		  $order->update_status( 'completed' );
		}
	}
}