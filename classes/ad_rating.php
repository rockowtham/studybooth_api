<?php
/*-----
	Ad rating And Comments Starts 
-----*/
add_action( 'rest_api_init', 'carspotAPI_adDetails_rating_hook', 0 );
function carspotAPI_adDetails_rating_hook() {

    register_rest_route( 'carspot/v1', '/ad_post/ad_rating/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_adDetails_rating_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
    register_rest_route( 'carspot/v1', '/ad_post/ad_rating/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_adDetails_rating1_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );	

    register_rest_route( 'carspot/v1', '/ad_post/ad_rating/new/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_adDetails_add_rating',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );		
}


if (!function_exists('carspotAPI_adDetails_add_rating'))
{
	function carspotAPI_adDetails_add_rating( $request )
	{
		global $carspotAPI;/*For Redux*/
		$json_data   = $request->get_json_params();
		$sender_id	 =	get_current_user_id();
		$sender	     =	get_userdata($sender_id);
		$json_data   = $request->get_json_params();
				
		$ad_id 	     = (isset( $json_data['ad_id'] ) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : '';
		//$ad_id 	         = (isset( $json_data['rating_comments'] ) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : 0;
		$rating_stars 	 = (isset( $json_data['rating'] ) && $json_data['rating'] != "" ) ? $json_data['rating'] : 1;
		$rating_comments = (isset( $json_data['rating_comments'] ) && $json_data['rating_comments'] != "" ) ? $json_data['rating_comments'] : '';
		$page_number = (isset( $json_data['page_number'] ) && $json_data['page_number'] != "" ) ? $json_data['page_number'] : 1;
		
		$rply_comment_id = (isset( $json_data['comment_id'] ) && $json_data['comment_id'] != "" ) ? $json_data['comment_id'] : '';
		$is_ratingReply  = ( $rply_comment_id == "" ) ? true : false;
		$poster_id	     =	get_post_field( 'post_author', $ad_id );

		
		if( $sender_id == 0 || $sender_id == "" || $ad_id == "")
		{
			return array( 'success' => false, 'data' => '', 'message'  => __( "Something went wrong", 'carspot-rest-api' ));
		}	
			
		if( $is_ratingReply )
		{	
				
					
			if( $sender_id == $poster_id)
			{
				return array( 'success' => false, 'data' => '', 'message'  => __( "Ad author can't post rating", 'carspot-rest-api' ));
			}
		
			if( !$carspotAPI['sb_update_rating'] && get_user_meta( $sender_id, 'ad_ratting_' . $sender_id, true ) == $ad_id )
			{
				return array( 'success' => false, 'data' => '', 'message'  => __( "You've posted rating already", 'carspot-rest-api' ));
			}	
			/* Rating update starts starts */	
			if( isset($carspotAPI['sb_update_rating']) && $carspotAPI['sb_update_rating'] )
			{
			
				$args = array(
					'type__in' => array('ad_post_rating'),
					'post_id' => $ad_id,
					'user_id' => $sender_id,
					'number' => 1,
					'parent' => 0,
				);
				$comment_exist = get_comments($args);
				if( count( $comment_exist ) > 0  )
				{
					$comment = array();
					$comment['comment_ID'] 		= $comment_exist[0]->comment_ID;
					$comment['comment_content'] = $rating_comments;
					wp_update_comment( $comment );
					update_comment_meta($comment_exist[0]->comment_ID, 'review_stars', $rating_stars);
					if( isset($carspotAPI['sb_rating_email_author'] ) && $carspotAPI['sb_rating_email_author'] )
					{
						carspotAPI_email_ad_rating($ad_id, $sender_id, $rating_stars, $rating_comments);
					}
					return array( 'success' => true, 'data' => '', 'message'  => __( "Your rating has been updated", 'carspot-rest-api' ));
				}				
			}
			/* Rating update starts ends */
	
			/* New Rating posts starts */
			$time = current_time('mysql');
			$data = array(
				'comment_post_ID' => $ad_id,
				'comment_author' => $sender->display_name,
				'comment_author_email' => $sender->user_email,
				'comment_author_url' => '',
				'comment_content' => $rating_comments,
				'comment_type' => 'ad_post_rating',
				'user_id' => $sender_id,
				'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
				'comment_date' => $time,
				'comment_approved' => 1
			);
			
			$comment_id  =  wp_insert_comment($data);
			if($comment_id)
			{
				update_comment_meta($comment_id, 'review_stars', $rating_stars);
				if( isset($carspotAPI['sb_rating_email_author'] ) && $carspotAPI['sb_rating_email_author'] )
				{
					carspotAPI_email_ad_rating($ad_id, $sender_id, $rating_stars, $rating_comments );
				}
				return array( 'success' => true, 'data' => '', 'message'  => __( "Your rating has been posted", 'carspot-rest-api' ));
			}		
			/* New Rating posts ends */
		}
		else
		{
			/* Rating Reply posts starts */	
			if( $rply_comment_id == "")
			{
				return array( 'success' => false, 'data' => '', 'message'  => __( "Something went wrong", 'carspot-rest-api' ));
			}	
			
			
			if( $sender_id != $poster_id)
			{
				return array( 'success' => false, 'data' => '', 'message'  => __( "Only ad author can reply rating", 'carspot-rest-api' ));
			}
			
			$args = array(
				'type__in' => array('ad_post_rating'),
				'post_id' => $ad_id,
				'user_id' => $sender_id,
				'number' => 1,
				'parent' => $rply_comment_id,
			);
			$comment_exist = get_comments($args);
			if( count( $comment_exist ) > 0  )
			{
				$comment = array();
				$comment['comment_ID'] = $comment_exist[0]->comment_ID;
				$comment['comment_content'] = $rating_comments;
				wp_update_comment( $comment );
				
				if( isset($carspot_theme['sb_rating_reply_email'] ) && $carspot_theme['sb_rating_reply_email'] )
				{
					$comment_data	=	get_comment( $rply_comment_id );
					$rating			=	get_comment_meta($rply_comment_id, 'review_stars', true);
					carspotAPI_email_ad_rating_reply($ad_id, $comment_data->user_id, $rating_comments, $rating, $comment_data->comment_content);
				}
				
				$data = carspotAPI_adDetails_rating_get( $ad_id, $page_number, false );	
				return array( 'success' => true, 'data' => $data, 'message'  => __( "Your reply has been updated", 'carspot-rest-api' ));

			}			
		
			$time = current_time('mysql');
			$data = array(
				'comment_post_ID' 		=> $ad_id,
				'comment_author' 		=> $sender->display_name,
				'comment_author_email' 	=> $sender->user_email,
				'comment_author_url' 	=> '',
				'comment_content' 		=> $rating_comments,
				'comment_type' 			=> 'ad_post_rating',
				'user_id' 				=> $sender_id,
				'comment_author_IP' 	=> $_SERVER['REMOTE_ADDR'],
				'comment_date' 			=> $time,
				'comment_parent'		=> $rply_comment_id,
				'comment_approved' 		=> 1
			);
			
			$comment_id  =  wp_insert_comment($data);
			if($comment_id)
			{
				update_user_meta( $sender_id, 'ad_comment_reply'.$rply_comment_id, $comment_id );
				if( isset($carspotAPI['sb_rating_reply_email'] ) && $carspotAPI['sb_rating_reply_email'] )
				{
					$comment_data	=	get_comment( $rply_comment_id );
					$rating			=	get_comment_meta($rply_comment_id, 'review_stars', true);
					carspotAPI_email_ad_rating_reply($ad_id, $comment_data->user_id, $rating_comments, $rating, $comment_data->comment_content);
				}
				$data = carspotAPI_adDetails_rating_get( $ad_id, $page_number, false );
				return array( 'success' => true, 'data' => $data, 'message'  => __( "Your reply has been posted", 'carspot-rest-api' ));
			}			
			/* Rating Reply posts ends */			
		}
		
		return array( 'success' => false, 'data' => '', 'message'  => __( "Something went please try again.", 'carspot-rest-api' ));
		
	}
}


if (!function_exists('carspotAPI_adDetails_rating1_get'))
{
	function carspotAPI_adDetails_rating1_get( $request )
	{
		
		$json_data   = $request->get_json_params();
		$post_id 	 = (isset( $json_data['ad_id'] ) && $json_data['ad_id'] != "" ) ? $json_data['ad_id'] : 0;
		$page_number 	 = (isset( $json_data['page_number'] ) && $json_data['page_number'] != "" ) ? $json_data['page_number'] : 1;
		
		$data = carspotAPI_adDetails_rating_get( $post_id, $page_number, true );
		return $data;
		
	}
}

if (!function_exists('carspotAPI_adDetails_rating_get'))
{
	function carspotAPI_adDetails_rating_get( $post_id = 0, $page_number = 1, $return_arr = false )
	{
		global $carspotAPI;/*For Redux*/
		
		
		$user_id     = get_current_user_id();
		/*Load Required Data Starts */
		$poster_id	=	get_post_field( 'post_author', $post_id );
		
		/*Load Required Data Ends */		
		$limit_number = (isset($carspotAPI['sb_rating_max']) && $carspotAPI['sb_rating_max']  ) ? $carspotAPI['sb_rating_max'] : 10;
		/* Pagination Settings */
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} else if ( isset( $page_number ) ) {
			$paged = $page_number;
		} else {
			$paged = 1;
		}			
		$page   = $paged;
		$limit	= $limit_number;
		$offset = ($page * $limit) - $limit;
		$args = array(
			'type__in' => array('ad_post_rating'),
			'number' => $limit,
			'offset' => $offset,
			'parent' => 0, // parent only
			'post_id' => $post_id, // use post_id, not post_ID
			
		);
		
		$count_comments = count( get_comments( array( 'post_id' => $post_id, 'type' => 'ad_post_rating' ,'parent' => 0) ) );		
		
		$comments = get_comments($args);
		$ratings = array();
		$count = 0;
		if( count( $comments ) > 0 )
		{
			$can_reply  = ( get_post_meta($post_id, '_carspot_ad_status_', true ) == 'active' && get_current_user_id() == $poster_id ) ? true : false;
			$reply_text = __("Reply", "carspot-rest-api");
			foreach($comments as $comment)
			{
				$commenter	=	get_userdata($comment->user_id);
				if($commenter)
				{	
					
					$ratings[$count]['rating_id']       	= $comment->comment_ID;
					$ratings[$count]['rating_author']       = $comment->user_id;
					$ratings[$count]['rating_author_name']  = $commenter->display_name;
					$ratings[$count]['rating_author_image'] = carspotAPI_user_dp( $comment->user_id);
					$ratings[$count]['rating_date']         = get_comment_date( get_option('date_format'), $comment->comment_ID );
					$ratings[$count]['rating_text']         = esc_html($comment->comment_content);
					$ratings[$count]['rating_stars']        = get_comment_meta($comment->comment_ID, 'review_stars', true);
					$ratings[$count]['can_reply']           = $can_reply;
					$ratings[$count]['reply_text']          = $reply_text;
					$ratings[$count]['current_page']        = $paged;
						
						/*Reply Settings Starts */
						$args_reply = array(
							'type__in' => array('ad_post_rating'),
							'number' => 1,
							'parent' => $comment->comment_ID, // parent only
							'post_id' => $post_id, // use post_id, not post_ID
						);
						$rate_rply = array();
						$has_reply = false;
						$replies = get_comments($args_reply);
						if( count( $replies ) > 0 )
						{
							$rcount = 0;
							$ad_author	=	get_userdata($poster_id);
							$has_reply = true;
							foreach( $replies as $reply )
							{
								$rate_rply[$rcount]['rating_id']       	   = $reply->comment_ID;
								$rate_rply[$rcount]['rating_author']       = $reply->user_id;
								$rate_rply[$rcount]['rating_author_name']  = $ad_author->display_name;
								$rate_rply[$rcount]['rating_author_image'] = carspotAPI_user_dp( $reply->user_id);;
								$rate_rply[$rcount]['rating_date']         = get_comment_date( get_option('date_format'), $reply->comment_ID );
								$rate_rply[$rcount]['rating_text']         = esc_html($reply->comment_content);
								$rate_rply[$rcount]['rating_user_stars']   = get_comment_meta($reply->comment_ID, 'review_stars', true);
								$rate_rply[$rcount]['can_reply']           = false;
								$rate_rply[$rcount]['reply_text']          = $reply_text;
								$rate_rply[$rcount]['current_page']         = $paged;
								
								$rcount++;
	
							}
						}
					$ratings[$count]['has_reply']       = $has_reply;
					$ratings[$count]['reply']           = $rate_rply;
					
						/*Reply Settings Ends */
					$count++;
				}
			}
		}
		
		/*Offers on my ads starts */
		$section_title = (isset($carspotAPI['sb_ad_rating_title']) && $carspotAPI['sb_ad_rating_title'] != "" ) ? $carspotAPI['sb_ad_rating_title'] : __("Ad Rating and Reviews", "carspot-rest-api");
		
		
		$email_author 			= (isset($carspotAPI['sb_rating_email_author']) && $carspotAPI['sb_rating_email_author']  ) ? true : false;
		$sb_rating_reply_email  = (isset($carspotAPI['sb_rating_reply_email']) && $carspotAPI['sb_rating_reply_email']  ) ? true : false;
		
		
		
		$data["ad_id"]   			= $post_id;
		$data["section_title"]   	= $section_title;
		/*$data["page_title"]      	= __("Ad Rating and Reviews", "carspot-rest-api");*/
		$data["ratings"]        	= $ratings;
		
		
		$data["rating_show"] 				=  (isset($carspotAPI['sb_ad_rating']) && $carspotAPI['sb_ad_rating']  ) ? true : false;;
		$data["title"] 						=  __("Ad Rating Here", "carspot-rest-api");
		$data["textarea_text"] 				=  __("Rating Comments", "carspot-rest-api");
		$data["textarea_value"] 			=  "";
		$data["tagline"] 					=  __("You can not edit it later.", "carspot-rest-api");
		$data["is_editable"]				= (isset($carspotAPI['sb_update_rating']) && $carspotAPI['sb_update_rating']  ) ? true : false;
		$data["can_rate"]        			= ( get_post_meta($post_id, '_carspot_ad_status_', true ) == 'active' ) ? true : false;
		$data["can_rate_msg"]       		= __("You can't rate this ad", "carspot-rest-api");
		$data["no_rating"]       			= __("Be the first one to rate this ad", "carspot-rest-api");
		$data["no_rating_message"]  		= __("No rating found.", "carspot-rest-api");
		$data["btn"]						= __("Submit Your Rating", "carspot-rest-api");
		$data["loadmore_btn"]				= __("Load More", "carspot-rest-api");
		$data["loadmore_btn_show"]			= ( @$limit_number < @$count_comments ) ? true : false;
		
		
		$data["rply_dialog"]['text'] 		= __("Your reply text here", "carspot-rest-api");
		$data["rply_dialog"]['send_btn'] 	= __("Submit", "carspot-rest-api");
		$data["rply_dialog"]['cancel_btn'] 	= __("Cancel", "carspot-rest-api");
		
		$data["pagination"]['has_next_page'] = ($paged <= ceil(count((array)$count_comments)/$limit_number) && count((array)$ratings) > 0) ? true : false;
		$data["pagination"]['next_page'] 	 = $paged+1;
		
		
		
		$m =  "";
		
		return $response = ($return_arr) ? array( 'success' => true, 'data' => $data, 'message'  => $m) : $data;
	}
}