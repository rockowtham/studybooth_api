<?php /* Get Bids Query Starts */
if( !function_exists('carspotAPI_get_biddings' ) )
{
	function carspotAPI_get_biddings( $ad_id )
	{
		global $wpdb;
		$biddings = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE post_id = '$ad_id' AND  meta_key like  '_carspot_bid_%' ORDER BY meta_id DESC", OBJECT );
		return $biddings;
	
	}
}
/* Get Bids Query Ends */

/* Get Bids List Starts */
function carspotAPI_bids($ad_id)
{
	
	$biddings	=	carspotAPI_get_biddings( $ad_id );
	$arr	=	'';
	$bidArr = array();
	if( count( $biddings ) > 0 )
	{
		foreach( $biddings as $bid )
		{
			/*date - comment - user - offer*/
			$data_array		=	explode( '_separator_', $bid->meta_value );
			$date			=	$data_array[0];
			$comments		=	$data_array[1];
			$user			=	$data_array[2];
			$offer			=	'';
			$user_info  	= 	get_user_by( 'ID', $user );
			if( $user_info  ) {
			$current_user	=	get_current_user_id();
			$ad_owner		=	get_post_field( 'post_author', $ad_id );
			$cls			=	'';
			$admin_html		= 	'';
			
			$bidder_name 	= $user_info->display_name;
			
			$phoneContact	= '';
			if( $current_user == $ad_owner && $user_info->_sb_contact != "" )
			{
				$phoneContact	= $user_info->_sb_contact;
			}
			$date  			= 	date(get_option('date_format'), strtotime($date));
			$comments 		= 	trim(preg_replace('/\s+/',' ', $comments));
			$profile_img 	= 	carspotAPI_user_dp($user_info->ID);
				
			$final_price 	= 	carspotAPI_get_price( $data_array[3], '', $ad_id );			
			
			$arr = array("name" => $bidder_name, "profile" => $profile_img, "date" => $date, "comment" => $comments, "price" => $final_price, "phone" => $phoneContact, "bid_by" => $user);
			
			$bidArr[] = $arr;
			}
		}
	}

	return $bidArr;
	
}
/* Get Bids List Ends */
/* Get Bids List Starts */

if ( ! function_exists( 'carspotAPI_get_all_biddings_array' ) )
{
	function carspotAPI_get_all_biddings_array( $ad_id )
	{
		global $wpdb;
		$biddings = $wpdb->get_results( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '$ad_id' AND  meta_key like  '_carspot_bid_%' ORDER BY meta_id DESC", OBJECT  );
		$bid_array	=	array();
		if( count( $biddings ) > 0 )
		{
			foreach( $biddings as $bid )
			{
				/*date - comment - user - offer*/
				$data_array	=	explode( '_separator_', $bid->meta_value );
				$bid_array[$data_array[2] .'_' . $data_array[0]] = 	$data_array[3];
			}
		}
		return $bid_array;
	}
}

function carspotAPI_bids_users($ad_id)
{
	
	//$biddings = carspotAPI_get_all_biddings_array($ad_id);
		
	$biddings	=	carspotAPI_get_biddings( $ad_id );
	
	$data_sort = array();
	if( count( $biddings ) > 0 )
	{	
		foreach( $biddings as $b)
		{
			$pricInfo = @end( explode("_separator_", $b->meta_value));
			$data_sort["$b->meta_value"] = ( $pricInfo);
		}
	}
	$arr	=	'';
	$bidArr = array();
	if( count( $data_sort ) > 0 )
	{
		arsort( $data_sort );

		foreach( $data_sort as $bid => $price  )
		{
			/*date - comment - user - offer*/
			$data_array		=	explode( '_separator_', $bid );
			$date			=	$data_array[0];
			$comments		=	$data_array[1];
			$user			=	$data_array[2];
			$offer			=	'';
			$user_info  	= 	get_user_by( 'ID', $user );
			if( $user_info  ) {
			$current_user	=	get_current_user_id();
			$ad_owner		=	get_post_field( 'post_author', $ad_id );
			$cls			=	'';
			$admin_html		= 	'';
			
			$bidder_name 	= $user_info->display_name;
			
			$phoneContact	= '';
			if( $current_user == $ad_owner && $user_info->_sb_contact != "" )
			{
				$phoneContact	= $user_info->_sb_contact;
			}
			$date  			= 	date(get_option('date_format'), strtotime($date));
			$comments 		= 	trim(preg_replace('/\s+/',' ', $comments));
			$profile_img 	= 	carspotAPI_user_dp($user_info->ID);
				
			$final_price 	= 	carspotAPI_get_price( $data_array[3], '', $ad_id );			
			
			
			$offer_posted = __("Posted An Offer", "carspot-rest-api");
			
			$arr = array(
						"name" => $bidder_name, 
						"offer_by" => $offer_posted ,
						"profile" => $profile_img, 
						"date" => $date, 
						"price" => $final_price['price'], 
					 );
			
			$bidArr[] = $arr;
			}
		}
	}

	return $bidArr;
	
}
/* Get Bids List Ends */


add_action( 'rest_api_init', 'carspotAPI_get_ad_bids_hook', 0 );
function carspotAPI_get_ad_bids_hook() {

    register_rest_route(
        'carspot/v1', '/ad_post/bid/', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_get_ad_bids',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
    register_rest_route(
        'carspot/v1', '/ad_post/bid/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_get_ad_bids',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );	
    register_rest_route(
        'carspot/v1', '/ad_post/bid1/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_get_ad_bids',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );

}  

if( !function_exists('carspotAPI_get_ad_bids' ) )
{
	function carspotAPI_get_ad_bids( $request )
	{
		global $carspotAPI;
		$json_data  = $request->get_json_params();	
		$ad_id		= (isset($json_data['ad_id'])) 		? $json_data['ad_id'] : '';
		
		return carspotAPI_get_ad_bids1( $ad_id );
	}
	
}

if( !function_exists('carspotAPI_get_ad_bids1' ) )
{
	function carspotAPI_get_ad_bids1( $ad_id = '', $return_arr = true )
	{
		global $carspotAPI;			
		//profile.php 1390 
		 if( $return_arr == true )
		 {
			$allBids 						= carspotAPI_bids( $ad_id );
			$bids_users						= carspotAPI_bids_users( $ad_id );
			$top_no_bidders                 = (count($bids_users) == 0 ) ? __("No top bidder yet. Be the first one.", "carspot-rest-api") : '';
			$data['ad_timer'] 				= carspotAPI_get_adTimer($ad_id);
			$data['bids'] 					= $allBids;
			$data['top_bidders'] 			= $bids_users;
			$data['no_top_bidders'] 		= $top_no_bidders;
			$data['page_title'] 			= esc_html( $carspotAPI['sb_enable_comments_offer_user_title']);
			$data['form']['bid_amount'] 	= __("Bid Amount", "carspot-rest-api");
			$data['form']['bid_textarea'] 	= __("Comments", "carspot-rest-api");
			$data['form']['bid_btn'] 		= __("Submit", "carspot-rest-api");
			$data['form']['bid_info'] 		= esc_html( $carspotAPI['sb_comments_section_note']);

			$user 							= wp_get_current_user();	
			$user_id 						= @$user->data->ID;			
			$offer_by						= $user_id;
			$ad_author 						= get_post_field( 'post_author', $ad_id );
			
			$message = '';
			$can_bid = true;
			if( $offer_by == $ad_author )
			{		
				$message_data = ( count( $allBids ) == 0 ) ? __("You can not bid on your item.", "carspot-rest-api") : '';
				$message = $message_data;
				$can_bid = false;
				/*, "can_bid" => $can_bid, "message" => $message*/
			}
			else if( isset($allBids) && count( $allBids ) > 0 ){$message = '';}
			else
			{ 
				$message = __("Be the first one to bid on this ad.", "carspot-rest-api");
			}

			$response = array( 'success' => true, 'data' => $data, 'message'  => $message , "can_bid" => $can_bid );		
		 }
		 else
		 {
			 $response  = carspotAPI_bids( $ad_id );
		 }
		return $response;
	}
	
}


/* Get Bids Starts */
if (!function_exists('carspotAPI_bid_stat'))
{
	function carspotAPI_bid_stat($ad_id)
	{
		global $wpdb;
		$biddings = $wpdb->get_results( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '$ad_id' AND  meta_key like  '_carspot_bid_%' ORDER BY meta_id DESC", OBJECT  );
		$bid_array	=	array();
		if( count( $biddings ) > 0 )
		{
			foreach( $biddings as $bid )
			{
				/*date - comment - user - offer*/
				$data_array	=	explode( '_separator_', $bid->meta_value );
				$bid_array[] = 	$data_array[3]; 
				/*Get Bid Price Only*/
			}
		}

		$total_bids	=	count( $bid_array );
		
		$max =  $min =	0;
			
		if($total_bids > 0)
		{
			$max	=	max( $bid_array );	
			$min	=	min( $bid_array );	
		}
				
		$max_price 	= 	carspotAPI_get_price($max,'',$ad_id);	
		$min_price 	= 	carspotAPI_get_price($min,'',$ad_id);	
	
		return array("total_text" => __("Total", "carspot-rest-api"), "total" => $total_bids,"max_text" => __("Highest", "carspot-rest-api"), "max" => $max_price,"min_text" => __("Lowest", "carspot-rest-api"), "min" => $min_price);
		
	}
}
/* Get Bids Ends */


add_action( 'rest_api_init', 'carspotAPI_submit_bid_hook', 0 );
function carspotAPI_submit_bid_hook() {

    register_rest_route(
        'carspot/v1', '/ad_post/bid/post/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_submit_bid',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
}  

/*Submit Bid starts*/
if ( ! function_exists( 'carspotAPI_submit_bid' ) ) {
function carspotAPI_submit_bid( $request )
{
	
	global $carspotAPI;
	
	$json_data  = $request->get_json_params();	
	$ad_id		= (isset($json_data['ad_id'])) 		 ? $json_data['ad_id'] : '';
	$offer		= (isset($json_data['bid_amount']))  ? $json_data['bid_amount'] : '';
	$comments	= (isset($json_data['bid_comment'])) ? $json_data['bid_comment'] : '';
	$bdata      = array();	
	if( $ad_id == "" || $offer == "" || $comments == "" )
	return  array( 'success' => false, 'data' => '', 'message'  => __("All Fields Are Required", "carspot-rest-api") );
	
	$user 		=   wp_get_current_user();	
	$user_id 	=   $user->data->ID;			
	$offer_by	=	$user_id;
	$ad_author 	=   get_post_field( 'post_author', $ad_id );
	
	if( $offer_by == $ad_author )
		return  array( 'success' => false, 'data' => '', 'message'  => __("Ad author can't post bid", "carspot-rest-api") );
	
	$bid =	'';
	if( $offer == "" )
	{
		$bid = date('Y-m-d H:i:s') .  "_separator_" . $comments . "_separator_" . $offer_by;
	}
	else
	{
		$bid = date('Y-m-d H:i:s') .  "_separator_" . $comments . "_separator_" . $offer_by . "_separator_" . $offer;
	}
	
	if( isset( $carspotAPI['sb_email_on_new_bid_on'] ) && $carspotAPI['sb_email_on_new_bid_on'] )
	{
		carspotAPI_send_email_new_bid($offer_by,$ad_author, $offer, $comments, $ad_id);
		
	}
	
	$is_exist = get_post_meta( $ad_id, "_carspot_bid_" . $offer_by, true );
	if( $is_exist != "" )
	{
		update_post_meta( $ad_id, "_carspot_bid_" . $offer_by, $bid );
		//$data['ad_bids'] = carspotAPI_bid_stat($ad_id);	
		
		$bdata['bids'] = carspotAPI_get_ad_bids1( $ad_id, false );
			
		return  array( 'success' => true, 'data' => $bdata, 'message'  => __("Updated successfully.", "carspot-rest-api") );
	}
	else
	{
		
		update_post_meta( $ad_id, "_carspot_bid_" . $offer_by, $bid );
		//$data['ad_bids'] = carspotAPI_bid_stat($ad_id);
		$bdata['bids'] = carspotAPI_get_ad_bids1( $ad_id, false );	
		 return  array( 'success' => true, 'data' => $bdata, 'message'  => __("Posted successfully.", "carspot-rest-api") );
	}
	
}
/*Submit Bid Ends */
}