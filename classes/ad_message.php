<?php
/*-----
	Ad Messages Inbox
-----*/

add_action( 'rest_api_init', 'carspotAPI_messages_inbox_api_hooks_get', 0 );
function carspotAPI_messages_inbox_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/message/inbox/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_messages_inbox_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
    register_rest_route( 'carspot/v1', '/message/inbox/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_messages_inbox_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );	
}

if (!function_exists('carspotAPI_messages_inbox_get'))
{
	function carspotAPI_messages_inbox_get( $request )
	{
		$json_data = $request->get_json_params();
		$receiver_id = (isset( $json_data['receiver_id'] ) && $json_data['receiver_id'] != "" ) ? $json_data['receiver_id'] : '';		
		
		
		
		global $carspotAPI;
		/*For Redux*/
		global $wpdb;
		$user    = wp_get_current_user();	
		$user_id = @$user->data->ID;	
		/*Offers on my ads starts */
		
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} else if ( isset( $json_data['page_number'] ) ) {
			// This will occur if on front page.
			$paged = $json_data['page_number'];
		} else {
			$paged = 1;
		}		
		$posts_per_page =   get_option( 'posts_per_page' );
		$args	        =	array( 'post_type' => 'ad_post', 'author' => $user_id, 'post_status' => 'publish', 'posts_per_page' => get_option( 'posts_per_page' ), 'paged' => $paged, 'order'=> 'DESC', 'orderby' => 'date' );
		$ads = new WP_Query( $args );
		
		
		$myOfferAds = array();
		if ( $ads->have_posts() )
		{
			while ( $ads->have_posts() )
			{
				$ads->the_post();
				$ad_id	  =	get_the_ID();				
				$args     = array( 'number' => '1', 'post_id' => $ad_id, 'post_type' => 'ad_post' );
				$comments = get_comments($args);	
				
				/*$typeData = ( $type != "sent" ) ? $sender_id : $receiver_id;*/
							
				//if(count($comments) > 0 ){
					$offerAds['ad_id'] 		= 	$ad_id;
					$offerAds['message_ad_title'] 	= 	esc_html( carspotAPI_convert_uniText(get_the_title( $ad_id ) ));
					$offerAds['message_ad_img'] 	= 	carspotAPI_get_ad_image($ad_id, 1, 'thumb');				
					$is_unread_msgs = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->commentmeta WHERE comment_id = '".get_current_user_id()."' AND meta_value = '0' AND meta_key like '".$ad_id."_%' "  );
					$offerAds['message_read_status'] 	= 	( $is_unread_msgs > 0 ) ? false : true;
					$myOfferAds[] = $offerAds;

				//}
			}
			
		}
		$data['received_offers']['items'] = $myOfferAds;
		/*Offers on my ads ends */

		$data['title']['main']    = __("Messages", "carspot-rest-api");
		$data['title']['sent']    = __("Sent Offers", "carspot-rest-api");
		$data['title']['receive'] = __("Offers on Ads", "carspot-rest-api");		
		$nextPaged = $paged + 1;
		$has_next_page = ( $nextPaged <= (int)$ads->max_num_pages ) ? true : false;
	
	$data['pagination'] = array("max_num_pages" => (int)$ads->max_num_pages,"current_page" => (int)$paged, "next_page" => (int)$nextPaged, "increment" => (int)$posts_per_page , "current_no_of_ads" =>  (int)count($ads->posts), "has_next_page" => $has_next_page );		
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => '');
	}
}
	
 

/*-----
	Ad Messages Main
-----*/
add_action( 'rest_api_init', 'carspotAPI_messages_api_hooks_get', 0 );
function carspotAPI_messages_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/message/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_messages_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
    register_rest_route( 'carspot/v1', '/message_post/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_messages_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );	
}

if (!function_exists('carspotAPI_messages_get'))
{
	function carspotAPI_messages_get( $request )
	{
		
		$json_data = $request->get_json_params();
		$receiver_id = (isset( $json_data['receiver_id'] ) && $json_data['receiver_id'] != "" ) ? $json_data['receiver_id'] : '';		
		
		$user    = wp_get_current_user();	
		$user_id = @$user->data->ID;	
		
		
		global $carspotAPI;/*For Redux*/
		global $wpdb;
		/*Messgae sent offer starts
		$rows = $wpdb->get_results(   "SELECT * FROM $wpdb->comments WHERE comment_type = 'ad_post' AND user_id = '$user_id' AND comment_parent = '$user_id' GROUP BY comment_post_ID ORDER BY comment_ID DESC" ); */	
		
		
		
		
		if ( get_query_var( 'paged' ) ) 
		{
			$paged = get_query_var( 'paged' );
		}
		 else if ( isset( $json_data['page_number'] ) ) {
			// This will occur if on front page.
			$paged = $json_data['page_number'];
		} else {
			$paged = 1;
		}		
		$posts_per_page = get_option( 'posts_per_page' );
		$start 			= ($paged-1) * $posts_per_page;
		
		$rows = $wpdb->get_results(   "SELECT comment_ID FROM $wpdb->comments WHERE comment_type = 'ad_post' AND user_id = '$user_id' AND comment_parent = '$user_id' GROUP BY comment_post_ID ORDER BY comment_ID DESC" );
		
		$total_posts   = $wpdb->num_rows; 
		$max_num_pages = ceil($total_posts/$posts_per_page);
		$max_num_pages = ( $max_num_pages < 1 ) ? 1 : $max_num_pages;
		
		$rows = $wpdb->get_results(   "SELECT * FROM $wpdb->comments WHERE comment_type = 'ad_post' AND user_id = '$user_id' AND comment_parent = '$user_id' GROUP BY comment_post_ID ORDER BY comment_ID DESC LIMIT $start, $posts_per_page" );		
		
		
	  $message = array();
	  $sentMessageData = array();
  	  foreach( $rows as $row )
	  {
				$ad_id 								=  $row->comment_post_ID;		 
				$message_receiver_id 				=  get_post_field( 'post_author', $row->comment_post_ID );
				$comment_author						=	@get_userdata( $message_receiver_id );
				$msg_status							=	get_comment_meta( $user_id, $ad_id ."_" .$message_receiver_id  , true );
				
				$msg_status_r = ( (int)$msg_status == 0 &&  $msg_status != "") ? false : true;
				
				$message['ad_id'] 					= $ad_id;
				$message['message_author_name']		= @$comment_author->display_name;
				$message['message_ad_img'] 			= carspotAPI_get_ad_image($ad_id, 1, 'thumb');	
				$message['message_ad_title'] 		= esc_html( carspotAPI_convert_uniText(get_the_title( $ad_id )) );
				$message['message_read_status'] 	= $msg_status_r;
				$message['message_sender_id'] 		= $user_id;
				$message['message_receiver_id'] 	= $message_receiver_id;
				$message['message_date'] 			= $row->comment_date;
				$sentMessageData[] = $message;
	  }
		
		
		$data['sent_offers']['items'] = $sentMessageData;
		/*Messgae sent offer ends */




		$data['title']['main']    = __("Messages", "carspot-rest-api");
		$data['title']['sent']    = __("Sent Offers", "carspot-rest-api");
		$data['title']['receive'] = __("Offers on Ads", "carspot-rest-api");

		$nextPaged = $paged + 1;
		$has_next_page = ( $nextPaged <= (int)$max_num_pages ) ? true : false;
	
	$data['pagination'] = array("max_num_pages" => (int)$max_num_pages, "current_page" => (int)$paged, "next_page" => (int)$nextPaged, "increment" => (int)$posts_per_page , "current_no_of_ads" =>  (int)($total_posts), "has_next_page" => $has_next_page );	
	 
		return $response = array( 'success' => true, 'data' => $data, 'message'  => ''  );
		
	}
}






/*-----
	Ad Messages Get offers on ads
-----*/
add_action( 'rest_api_init', 'carspotAPI_messages_offers_api_hooks_get', 0 );
function carspotAPI_messages_offers_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/message/offers/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_messages_offers_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
	
    register_rest_route( 'carspot/v1', '/message/offers/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_messages_offers_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
	
	
}

if (!function_exists('carspotAPI_messages_offers_get'))
{
	function carspotAPI_messages_offers_get( $request )
	{
		$json_data = $request->get_json_params();		
		$ad_id   = (isset( $json_data['ad_id'] ) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
		
		$user = wp_get_current_user();	
		$user_id = $user->data->ID;	
		
	  	global $wpdb;
	  
	  
		
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} else if ( isset( $json_data['page_number'] ) ) {
			// This will occur if on front page.
			$paged = $json_data['page_number'];
		} else {
			$paged = 1;
		}		
		$posts_per_page = get_option( 'posts_per_page' );
		$start = ($paged-1) * $posts_per_page;
		
		$rows = $wpdb->get_results( "SELECT comment_author, user_id, comment_date FROM $wpdb->comments WHERE comment_post_ID = '$ad_id'  GROUP BY user_id ORDER BY MAX(comment_date) DESC" );
		
		$total_posts = $wpdb->num_rows; 
		$max_num_pages = ceil($total_posts/$posts_per_page);
	  
	  
		$rows = $wpdb->get_results( "SELECT comment_author, user_id, comment_date FROM $wpdb->comments WHERE comment_post_ID = '$ad_id'  GROUP BY user_id ORDER BY MAX(comment_date) DESC LIMIT $start, $posts_per_page" );		
		
		$message     = array();
		$myOfferAds  = array();
			$success = false;
			
			
			if( count( $rows ) > 0 ){
				$success = true;
				foreach( $rows as $r )
				{
					if( $user_id == $r->user_id ) continue;
					
					$msg_status	=	get_comment_meta( get_current_user_id(), $ad_id."_" . $r->user_id, true );
					$message['ad_id'] 			= 	$ad_id;
					$message['message_author_name']		= 	$r->comment_author;
					$message['message_ad_img'] 			= 	carspotAPI_user_dp( $r->user_id);
					$message['message_ad_title'] 		= 	esc_html( carspotAPI_convert_uniText(get_the_title( $ad_id ) ) );
					$message['message_read_status'] 	= 	( $msg_status == 0 || $msg_status == '0' ) ? false : true;
					$message['message_sender_id'] 		= 	$r->user_id;	
					$message['message_receiver_id'] 	= 	$user_id;
					$message['message_date'] 			= 	$r->comment_date;
					
					$myOfferAds[] = $message;
				}
				
			}
			$data['received_offers']['items'] = $myOfferAds;
			

			$nextPaged     = $paged + 1;
			$has_next_page = ( $nextPaged <= (int)$max_num_pages ) ? true : false;
		
		$data['pagination'] = array("max_num_pages" => (int)$max_num_pages, "current_page" => (int)$paged, "next_page" => (int)$nextPaged, "increment" => (int)$posts_per_page , "current_no_of_ads" =>  (int)count((array)$total_posts), "has_next_page" => $has_next_page );	
			
			$extra['page_title'] = esc_html( get_the_title( $ad_id ) );
			
			$message = ( $success == false ) ? __("No Message Found", "carspot-rest-api") : '';
			return $response = array( 'success' => $success, 'data' => $data, 'message'  => $message, "extra" => $extra  );	
				
		}


	
}



/*-----
	Ad Messages Users Chat
-----*/
add_action( 'rest_api_init', 'carspotAPI_messages_chat_api_hooks_get', 0 );
function carspotAPI_messages_chat_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/message/chat/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_messages_chat_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );

    register_rest_route( 'carspot/v1', '/message/chat/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_messages_chat_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );		
    register_rest_route( 'carspot/v1', '/message/chat/post/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_messages_chat_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );	
	
}

if (!function_exists('carspotAPI_messages_chat_get'))
{
	function carspotAPI_messages_chat_get( $request )
	{
			
			
		$json_data = $request->get_json_params();		
		$ad_id   		= (isset( $json_data['ad_id'] ) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
		
		$sender_id     	= (isset( $json_data['sender_id'] ) && $json_data['ad_id'] != "" ) ? (int)$json_data['sender_id'] : '';
		$receiver_id   	= (isset( $json_data['receiver_id'] ) && $json_data['receiver_id'] != "" ) ? (int)$json_data['receiver_id'] : '';
		$type   		= (isset( $json_data['type'] ) && $json_data['type'] != "" ) ? $json_data['type'] : 'sent';		
		$message   		= (isset( $json_data['message'] ) && $json_data['message'] != "" ) ? $json_data['message'] : '';
		
		
		$user = wp_get_current_user();	
		$user_id = (int)$user->data->ID;	
		
		$authors	=	array( $sender_id, $user_id );

		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} else if ( isset( $json_data['page_number'] ) ) {
			// This will occur if on front page.
			$paged = $json_data['page_number'];
		} else {
			$paged = 1;
		}	
			
		/*get_option( 'posts_per_page' );*/	
		$posts_per_page = 10;
		$start = ($paged-1) * $posts_per_page;
		
		if( $type == 'sent' )
		{
			$authors	=	array( $receiver_id, $user_id );
			$queryID = $user_id;
		}
		else
		{
			$authors	=	array( $sender_id, $user_id );
			$queryID = $sender_id;			
		}
		
		$message2 = '';
		if( $ad_id != "" && $sender_id != "" && $receiver_id != "" &&  $message != "" )
		{
			if (function_exists('carspotAPI_add_messages_get'))
			$message2 = carspotAPI_add_messages_get( $ad_id, $queryID,  $sender_id, $receiver_id,$type, $message );
			
			
		}
		
		$cArgs = array( 'author__in' => $authors, 'post_id' => $ad_id, 'parent' => $queryID, 'orderby' => 'comment_date', 'order' => 'DESC', );
		
		
		
		$commentsData	=	get_comments( $cArgs );	
		$total_posts    = count( $commentsData ); 
		$max_num_pages = ceil($total_posts/$posts_per_page);					
		$args = array(
						'author__in' => $authors,
						'post_id' => $ad_id,
						'parent' => $queryID,
						'orderby' => 'comment_date',
						'order' => 'DESC',
						'paged' => $paged,
						'offset' => $start,
						'number' => $posts_per_page,
					);
						
		$comments	=	get_comments( $args );
		
		
		$chat = array();
		$chatHistory = array();
		$success = false;
		
		//$receiver_id
		
		$get_other_user_name = ( $type == 'sent' ) ? $receiver_id : $sender_id;
		
		$author_obj = @get_user_by('id', $get_other_user_name);
		
		$page_title = ($author_obj) ? $author_obj->display_name : __("Chat Box", "carspot-rest-api");
		
		$data['page_title'] 	= $page_title;
		$data['ad_title'] 		= get_the_title($ad_id);
		$data['ad_img']   		= carspotAPI_get_ad_image($ad_id, 1, 'thumb');
		$data['ad_date']  		= get_the_date("", $ad_id);
		
		$sender_img    			= carspotAPI_user_dp( $sender_id);
		$receiver_img  			= carspotAPI_user_dp( $receiver_id);
		$data['ad_price']	   	= carspotAPI_get_price( '', $ad_id );
		/*Add Read Status Here Starts*/	
		update_comment_meta( get_current_user_id(), $ad_id."_".$get_other_user_name, 1 );
		/*Add Read Status Here Ends*/	
		if( count( $comments ) > 0 )
		{
			$success = true;
			foreach( $comments as $comment)
			{
				if( $type == 'sent' )
				{
					$messageType 	= ( $comment->comment_parent != $comment->user_id ) ? 'reply' : 'message';						
				}
				else
				{
					
					$messageType 	= ( $comment->comment_parent != $comment->user_id ) ? 'message' : 'reply';
				}
				$chat['img'] 	= ( $comment->comment_parent != $comment->user_id ) ? $receiver_img : $sender_img;
				$chat['id'] 	= $comment->comment_ID;
				$chat['ad_id'] 	= $comment->comment_post_ID;
				$chat['text'] 	= $comment->comment_content;
				$chat['date'] 	= carspotAPI_timeago( $comment->comment_date );
				$chat['type'] 	= $messageType;
				$chatHistory[] 	= $chat;
			}
		}

		$data['chat'] = $chatHistory;
		
		$data['is_typing'] = __("is typing", "carspot-rest-api");
		
		/*array_reverse*/
		$nextPaged = $paged + 1;
		$has_next_page = ( $nextPaged <= (int)$max_num_pages ) ? true : false;
	
	$data['pagination'] = array("max_num_pages" => (int)$max_num_pages, "current_page" => (int)$paged, "next_page" => (int)$nextPaged, "increment" => (int)$posts_per_page , "current_no_of_ads" =>  (int)count($commentsData), "has_next_page" => $has_next_page );				
			
			$message = ( $success == false ) ? __("No Chat Found", "carspot-rest-api") : $message2;
			
			return $response = array( 'success' => $success, 'data' => $data, 'message'  => $message  );	
		
	}
}




add_action( 'rest_api_init', 'carspotAPI_messages_chat_api_hooks_popup', 0 );
function carspotAPI_messages_chat_api_hooks_popup() {

    register_rest_route( 'carspot/v1', '/message/popup/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_messages_chat_submit_popup',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
	
    register_rest_route( 'carspot/v1', '/message/popup/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_messages_chat_submit_popup',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );	
	
}


if (!function_exists('carspotAPI_messages_chat_submit_popup'))
{
	function carspotAPI_messages_chat_submit_popup( $request )
	{
		
		$json_data = $request->get_json_params();		
		$ad_id = (isset( $json_data['ad_id'] ) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
		$message = (isset( $json_data['message'] ) && $json_data['message'] != "" ) ? $json_data['message'] : '';
		
		$user 		= wp_get_current_user();	
		$sender_id 	= $user->data->ID;		
		
		$receiver_id = get_post_field('post_author', $ad_id );
		
		$queryID	= $sender_id;
		
		$message2 = __("Something went wrong", "carspot-rest-api");
		$success = false;
		if( $ad_id != "" && $sender_id != "" && $receiver_id != "" &&  $message != "" )
		{
			if (function_exists('carspotAPI_add_messages_get'))
			$message2  = carspotAPI_add_messages_get( $ad_id, $queryID,  $sender_id, $receiver_id, 'sent', $message );
			$success  = true;
			//
		}
		
		
		return $response = array( 'success' => $success, 'data' => '', 'message'  => $message2  );		
		
	}
}

//carspotAPI_messages_chat_submit_popup

//$message2 = carspotAPI_add_messages_get( $ad_id, $queryID,  $sender_id, $receiver_id,$type, $message );


if (!function_exists('carspotAPI_add_messages_get'))
{
	function carspotAPI_add_messages_get( $ad_id = '', $queryID = '', $sender_id = '', $receiver_id = '', $type = 'sent', $message = '' )
	{
		
		$user = wp_get_current_user();	
		$user_id = (int)$user->data->ID;	

		$user_email = $user->data->user_email;
		$display_name = $user->data->display_name;
		
		$time = current_time('mysql');
		$data = array(
			'comment_post_ID' => $ad_id,
			'comment_author' => $display_name,
			'comment_author_email' => $user_email,
			'comment_author_url' => '',
			'comment_content' => $message,
			'comment_type' => 'ad_post',
			'comment_parent' => $queryID,
			'user_id' => $user_id,
			'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
			'comment_date' => $time,
			'comment_approved' => 1,
		);		
				
		$comment_id	=	wp_insert_comment($data);
		if( $comment_id )
		{
			$typeData = ( $type != "sent" ) ? $sender_id : $receiver_id;
			update_comment_meta( $typeData, $ad_id."_".$user_id, 0 );
			/*Send Email When Message On Ad*/
			carspotAPI_get_notify_on_ad_message($ad_id, $receiver_id, $message, $display_name );
			
			carspotAPI_messages_sent_func( $type,  $receiver_id, $sender_id, $user_id, $comment_id, $ad_id, $message, $time );
			$messageString =  __( "Message sent successfully .", 'carspot-rest-api' );	
		}
		else
		{
			$messageString =  __( "Message not sent, please try again later.", 'carspot-rest-api' );
		}
		
		return $messageString;	
		
		
	}
}

if (!function_exists('carspotAPI_messages_sent_func'))
{
	function carspotAPI_messages_sent_func( $type,  $receiver_id, $sender_id, $user_id, $comment_id, $ad_id, $message, $time)
	{
			global $carspotAPI;
			if( isset( $carspotAPI['app_settings_message_firebase'] ) && $carspotAPI['app_settings_message_firebase'] == true )
			{
				$chat = array();
				$fbuserid = ( $type == "sent" ) ? $receiver_id : $sender_id;
				
				$queryID = ( $type == 'sent' ) ? $user_id : $sender_id;
				
				$f_reg_id  = get_user_meta($fbuserid, '_sb_user_firebase_id', true );
				
				if( $f_reg_id != "" )
				{
					$fbuserid_message_type = ( $type == "sent" ) ? "receive" : "sent";					
					$messager_img  	= ( $type == "sent" ) ?  carspotAPI_user_dp( $sender_id) :  carspotAPI_user_dp( $receiver_id);
					if( $type == 'sent' )
					{
						$messageType 	= ( $queryID != $user_id  ) ? 'message' : 'reply';						
					}
					else
					{
						$messageType 	= ( $queryID != $user_id  ) ? 'reply' : 'message';
					}							
									
					
					$chat['img'] 	= $messager_img;
					$chat['id'] 	= $comment_id;
					$chat['ad_id'] 	= $ad_id;
					$chat['text'] 	= $message;
					$chat['date'] 	= carspotAPI_timeago( $time );
					$chat['type'] 	= $messageType;
					
					$message_data = array
						(
							'topic' 		=> 'chat',
							'message' 		=> $message,
							'title'			=> get_the_title($ad_id),
							'adId' 			=> $ad_id,
							'senderId' 		=> $sender_id,
							'recieverId' 	=> $receiver_id,
							'type' 			=> $fbuserid_message_type,
							'chat' 			=> $chat,
						);	
						
						carspotAPI_firebase_notify_func($f_reg_id, $message_data);	
				}
				
			}
			
			
			
					
		
	}
	
}


/*-----
	Ad Messages Users Chat
-----*/
add_action( 'rest_api_init', 'carspotAPI_messages_sent_api_hooks_get', 0 );
function carspotAPI_messages_sent_api_hooks_get() {
		
    register_rest_route( 'carspot/v1', '/message/sent/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_messages_sent_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );	
	
}

if (!function_exists('carspotAPI_messages_sent_get'))
{
	function carspotAPI_messages_sent_get( $request )
	{

		$json_data = $request->get_json_params();		
		
	    $ad_id         = (isset( $json_data['ad_id'] ) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
		$sender_id     = (isset( $json_data['sender_id'] ) && $json_data['ad_id'] != "" ) ? (int)$json_data['sender_id'] : '';
		$receiver_id   = (isset( $json_data['receiver_id'] ) && $json_data['receiver_id'] != "" ) ? (int)$json_data['receiver_id'] : '';
		
		$message   = (isset( $json_data['message'] ) && $json_data['message'] != "" ) ? (int)$json_data['message'] : '';
		
//sb_data:ad_post_id=242&name=username&email=username.msc%40gmail.com&usr_id=25&rece_id=1&msg_receiver_id=1&message=lol
		
		$user    =   wp_get_current_user();	
		$user_id =   (int)$user->data->ID;	
		
		$authors	=	array( $sender_id, $user_id );

		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} else if ( isset( $json_data['page_number'] ) ) {
			// This will occur if on front page.
			$paged = $json_data['page_number'];
		} else {
			$paged = 1;
		}	
			
		$posts_per_page = 10;//get_option( 'posts_per_page' );
		$start = ($paged-1) * $posts_per_page;
		
		
		$cArgs = array( 'author__in' => $authors, 'post_id' => $ad_id, 'parent' => $user_id, 'orderby' => 'comment_date', 'order' => 'ASC', );
	
		
		$commentsData	=	get_comments( $cArgs );	
		$total_posts = count( $commentsData ); 
		$max_num_pages = ceil($total_posts/$posts_per_page);			
		
		$args = array(
			'author__in' => $authors,
			'post_id' => $ad_id,
			'parent' => $user_id,
			'orderby' => 'comment_date',
			'order' => 'ASC',
			'paged' => $paged,
			'offset' => $start,
			'number' => $posts_per_page,
		);
			
			
			$comments	=	get_comments( $args );
			
			$chat = array();
			$chatHistory = array();
			$success = false;
			
				
			if( count( $comments ) > 0 )
			{
				$success = true;
				foreach( $comments as $comment)
				{
					$messageType   = ( $comment->comment_parent != $comment->user_id ) ? 'reply' : 'message';
					$chat['id']    = $comment->comment_ID;
					$chat['ad_id'] = $comment->comment_post_ID;
					$chat['text']  = $comment->comment_content;
					$chat['date']  = $comment->comment_date;
					$chat['type']  = $messageType;
					$chatHistory[] = $chat;
				}
			}

			$data['chat'] = $chatHistory;

		$nextPaged      =  $paged + 1;
		$has_next_page  = ( $nextPaged <= (int)$max_num_pages ) ? true : false;
	
	$data['pagination'] = array("max_num_pages" => (int)$max_num_pages, "current_page" => (int)$paged, "next_page" => (int)$nextPaged, "increment" => (int)$posts_per_page , "current_no_of_ads" =>  (int)count($commentsData), "has_next_page" => $has_next_page );				
			
			$message = ( $success == false ) ? __("No Chat Found", "carspot-rest-api") : '';
			return $response = array( 'success' => $success, 'data' => $data, 'message'  => $message  );	
		
		
	}
}


if (!function_exists('carspotAPI_messages_get'))
{
	function carspotAPI_count_ad_messages( $ad_id = '', $user_id = '' )
	{
		global $wpdb;
		$total  = 0;
		if( $ad_id != '' && $user_id != '' )
		{		
			$total = $wpdb->get_var("SELECT COUNT(DISTINCT(comment_author)) as total FROM $wpdb->comments WHERE comment_post_ID = '".$ad_id."' AND user_id != '".$user_id."'");
		}
		return $total;
	}
}