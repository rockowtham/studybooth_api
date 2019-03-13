<?php
/*carspot Ad Post Email Template */
if( !function_exists('carspotAPI_get_notify_on_ad_post' ) )
{
	function carspotAPI_get_notify_on_ad_post($pid)
	{
		global  $carspotAPI;
		if( isset( $carspotAPI['sb_send_email_on_ad_post'] ) && $carspotAPI['sb_send_email_on_ad_post'] )
		{
			$to = $carspotAPI['ad_post_email_value'];
			$subject = __('New Ad', 'carspot-rest-api') . '-' . get_bloginfo( 'name' );
			$body = '<html><body><p>'.__('Got new ad','carspot-rest-api'). ' <a href="'.get_edit_post_link($pid).'">' . get_the_title($pid) .'</a></p></body></html>';
			$from		=	get_bloginfo( 'name' );
			if( isset( $carspotAPI['sb_msg_from_on_new_ad'] ) && $carspotAPI['sb_msg_from_on_new_ad'] != "" )
			{
				$from	=	$carspotAPI['sb_msg_from_on_new_ad'];
			}
			
			$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
			if( isset( $carspotAPI['sb_msg_on_new_ad'] ) &&  $carspotAPI['sb_msg_on_new_ad'] != "" )
			{
				
				$author_id = get_post_field ('post_author', $pid);
				$user_info = get_userdata($author_id);
				
				$subject_keywords  = array('%site_name%', '%ad_owner%', '%ad_title%');
				$subject_replaces  = array(get_bloginfo( 'name' ), $user_info->display_name, get_the_title($pid));
				
				$subject = str_replace($subject_keywords, $subject_replaces, $carspotAPI['sb_msg_subject_on_new_ad']);
	
				$msg_keywords  = array('%site_name%', '%ad_owner%', '%ad_title%', '%ad_link%');
				$msg_replaces  = array(get_bloginfo( 'name' ), $user_info->display_name, get_the_title($pid), get_the_permalink($pid) );
				
				$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['sb_msg_on_new_ad']);
	
			}
				wp_mail( $to, $subject, $body, $headers );
	
		
		}
	}
}


/*carspot Ad Post Email Template */
if( !function_exists('carspotAPI_get_notify_on_ad_approval' ) )
{
	function carspotAPI_get_notify_on_ad_approval($pid)
	{
		global $carspotAPI;
		$from	=	get_bloginfo( 'name' );
		if( isset( $carspotAPI['sb_active_ad_email_from'] ) && $carspotAPI['sb_active_ad_email_from'] != "" )
		{
			$from	=	$carspotAPI['sb_active_ad_email_from'];
		}
		$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
		if( isset( $carspotAPI['sb_active_ad_email_message'] ) &&  $carspotAPI['sb_active_ad_email_message'] != "" )
		{
			
			$author_id = get_post_field ('post_author', $pid);
			$user_info = get_userdata($author_id);
			
			$subject = $carspotAPI['sb_active_ad_email_subject'];
	
			$msg_keywords  = array('%site_name%', '%user_name%', '%ad_title%', '%ad_link%');
			$msg_replaces  = array(get_bloginfo( 'name' ), $user_info->display_name, get_the_title($pid), get_the_permalink($pid) );
			
			$to = $user_info->user_email;
			$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['sb_active_ad_email_message']);
			wp_mail( $to, $subject, $body, $headers );
		}
	}
}


/*carspot Message on ad*/
if( !function_exists('carspotAPI_get_notify_on_ad_message' ) )
{
	
	function carspotAPI_get_notify_on_ad_message($pid = '', $msg_receiver_id = '',$ad_message = '',$name = '')
	{
		global  $carspotAPI;
		if( isset($carspotAPI['sb_send_email_on_message']) && $carspotAPI['sb_send_email_on_message'] )
		{
			$author_obj = get_user_by('id', $msg_receiver_id);
			$to 	    = @$author_obj->user_email;
			$subject = __('New Message', 'carspot-rest-api');
			$title 	= get_the_title($pid);
			$body 	= '<html><body><p>'.__('Got new message on ad','carspot-rest-api').' '.$title.'</p><p>'.$ad_message.'</p></body></html>';
			$from	=	get_bloginfo( 'name' );
			if( isset( $carspotAPI['sb_message_from_on_new_ad'] ) && $carspotAPI['sb_message_from_on_new_ad'] != "" )
			{
				$from	=	$carspotAPI['sb_message_from_on_new_ad'];
			}
			$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
			if( isset( $carspotAPI['sb_message_on_new_ad'] ) &&  $carspotAPI['sb_message_on_new_ad'] != "" )
			{
				$subject_keywords  = array('%site_name%', '%ad_title%');
				$subject_replaces  = array(get_bloginfo( 'name' ),  get_the_title($pid));				
				$subject = str_replace($subject_keywords, $subject_replaces, $carspotAPI['sb_message_subject_on_new_ad']);
				$msg_keywords  = array('%site_name%', '%ad_title%', '%ad_link%', '%message%', '%sender_name%');
				$msg_replaces  = array(get_bloginfo( 'name' ),  get_the_title($pid), get_the_permalink($pid), $ad_message, $name );				
				$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['sb_message_on_new_ad']);
			}
			wp_mail( $to, $subject, $body, $headers );
		
		}
	}
}


/*carspot Message on ad*/
if( !function_exists('carspotAPI_sb_report_ad' ) )
{
	function carspotAPI_sb_report_ad($ad_id = '', $option = '', $comments = '', $author_id = '')
	{
		global  $carspotAPI;

		$to 	 	= $carspotAPI['report_email'];
		$subject 	= __('Ad Reported', 'carspot-rest-api');
		$body 		= '<html><body><p>'.__('Users reported this ad, please check it. ','carspot-rest-api').'<a href="'.get_the_permalink( $ad_id ).'">' . get_the_title( $ad_id )  . '</a></p></body></html>';
				
		$from		=	get_bloginfo( 'name' );
		if( isset( $carspotAPI['sb_report_ad_from'] ) && $carspotAPI['sb_report_ad_from'] != "" )
			$from	=	$carspotAPI['sb_report_ad_from'];

		$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
		if( isset( $carspotAPI['sb_report_ad_message'] ) &&  $carspotAPI['sb_report_ad_message'] != "" )
		{
			$subject_keywords  = array('%site_name%', '%ad_title%');
			$subject_replaces  = array(get_bloginfo( 'name' ),  get_the_title($ad_id));
			$subject = str_replace($subject_keywords, $subject_replaces, $carspotAPI['sb_report_ad_subject']);
			$author_id = get_post_field ('post_author', $ad_id);
			$user_info = get_userdata($author_id);
			$msg_keywords  = array('%site_name%', '%ad_title%', '%ad_link%', '%ad_owner%');
			$msg_replaces  = array(get_bloginfo( 'name' ),  get_the_title($ad_id), get_the_permalink($ad_id), $user_info->display_name  );
			$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['sb_report_ad_message']);
		}
			wp_mail( $to, $subject, $body, $headers );
	
	}
}


/*carspot Message on ad*/
if( !function_exists('carspotAPI_email_on_new_user' ) )
{
	function carspotAPI_email_on_new_user($user_id, $social = '')
	{
		global $carspotAPI;
		
		if( isset( $carspotAPI['sb_new_user_email_to_admin'] ) && $carspotAPI['sb_new_user_email_to_admin'] )
		{
			if( isset( $carspotAPI['sb_new_user_admin_message'] ) &&  $carspotAPI['sb_new_user_admin_message'] != "" && isset( $carspotAPI['sb_new_user_admin_message_from'] ) && $carspotAPI['sb_new_user_admin_message_from'] != "" )
			{
				$to		 =	get_option( 'admin_email' );
				$subject = $carspotAPI['sb_new_user_admin_message_subject'];
				$from	 =	$carspotAPI['sb_new_user_admin_message_from'];
				$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
				
				// User info
				$user_info = get_userdata( $user_id );
				
				
				$msg_keywords  = array('%site_name%', '%display_name%', '%email%');
				$msg_replaces  = array(get_bloginfo( 'name' ), $user_info->display_name, $user_info->user_email );
				
				
				$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['sb_new_user_admin_message']);
				wp_mail( $to, $subject, $body, $headers );
			}
			
		}
		
		if( isset( $carspotAPI['sb_new_user_email_to_user'] ) && $carspotAPI['sb_new_user_email_to_user'] )
		{
			if( isset( $carspotAPI['sb_new_user_message'] ) &&  $carspotAPI['sb_new_user_message'] != "" && isset( $carspotAPI['sb_new_user_message_from'] ) && $carspotAPI['sb_new_user_message_from'] != "" )
			{
				// User info
				$user_info = get_userdata( $user_id );
				
				$to	=	$user_info->user_email;
				$subject = $carspotAPI['sb_new_user_message_subject'];
				$from	=	$carspotAPI['sb_new_user_message_from'];
				$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
				
				$user_name	=	$user_info->user_email;
				if( $social != '' )
					$user_name .= "(Password: $social )";
				
				$token 	=	 carspot_randomString(6);
				$verification_link	=	$token;
				if( isset( $carspotAPI['sb_new_user_email_verification'] ) && $carspotAPI['sb_new_user_email_verification'] )
				{
					update_user_meta($user_id, 'sb_email_verification_token', $token);
				}
				
				$msg_keywords  = array('%site_name%', '%user_name%', '%display_name%', '%verification_link%');
				$msg_replaces  = array(get_bloginfo( 'name' ), $user_name, $user_info->display_name, $verification_link );
				
				$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['sb_new_user_message']);
				wp_mail( $to, $subject, $body, $headers );
			}
		}
	
	}
}

/*Send Email On Forgot pass*/
if( !function_exists('carspotAPI_forgot_pass_email_link' ) )
{
function carspotAPI_forgot_pass_email_link($email = '')
{
			global $carspotAPI;
			$from	=	get_bloginfo( 'name' );	
			if( isset( $carspotAPI['sb_forgot_password_from'] ) && $carspotAPI['sb_forgot_password_from'] != "" )
			{
				$from	=	$carspotAPI['sb_forgot_password_from'];
			}
			$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
			if( isset( $carspotAPI['sb_forgot_password_message'] ) &&  $carspotAPI['sb_forgot_password_message'] != "" )
			{
				
				$subject_keywords  = array('%site_name%');
				$subject_replaces  = array(get_bloginfo( 'name' ));
				
				$subject = str_replace($subject_keywords, $subject_replaces, $carspotAPI['sb_forgot_password_subject']);
	
				$token 	=	 carspot_randomString(50);
				
				$user = get_user_by( 'email', $email );
				$msg_keywords  = array('%site_name%', '%user%', '%reset_link%');
				$reset_link	=	trailingslashit( get_home_url() ) . '?token=' . $token . '-sb-uid-'. $user->ID;
				$msg_replaces  = array(get_bloginfo( 'name' ),  $user->display_name, $reset_link );
				
				$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['sb_forgot_password_message']);
				
				$to = $email;
				$mail = wp_mail( $to, $subject, $body, $headers );
				if( $mail )
				{
					update_user_meta($user->ID, 'sb_password_forget_token', $token);
					$success = true;
					$message =   __( 'Email sent', 'carspot-rest-api' );	
			
				}
				else
				{
					$success = false;
					$message =   __( 'Email server not responding', 'carspot-rest-api' );	
				}
	
	
			}	

			$response = array( 'success' => $success, 'data' => '' , 'message' => $message );
			return $response;
			
	
}
}


/*Send Email On Forgot pass*/
if( !function_exists('carspotAPI_forgot_pass_email_text' ) )
{
function carspotAPI_forgot_pass_email_text($email = '')
{
	global $carspotAPI;
	$params = array();

	if( email_exists( $email ) == true )
	{		

		// lets generate our new password
		$random_password = wp_generate_password( 12, false );		
		$to = $email;
		$subject = __( 'Your new password', 'carspot-rest-api' );
		
		$body = __( 'Your new password is: ', 'carspot-rest-api' ) .$random_password;
		$from	=	get_bloginfo( 'name' );	
		if( isset( $carspotAPI['sb_forgot_password_from'] ) && $carspotAPI['sb_forgot_password_from'] != "" )
		{
			$from	=	$carspotAPI['sb_forgot_password_from'];
		}
		$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
		if( isset( $carspotAPI['sb_forgot_password_message'] ) &&  $carspotAPI['sb_forgot_password_message'] != "" )
		{
			$subject_keywords  = array('%site_name%');
			$subject_replaces  = array(get_bloginfo( 'name' ));
			
			$subject = str_replace($subject_keywords, $subject_replaces, $carspotAPI['sb_forgot_password_subject']);

			$user = get_user_by( 'email', $email );
			$msg_keywords  = array('%site_name%', '%user%', '%reset_link%');
			$msg_replaces  = array(get_bloginfo( 'name' ),  $user->display_name, $random_password   );
			
			$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['sb_forgot_password_message']);

		}
			$mail = wp_mail( $to, $subject, $body, $headers );
			if( $mail )
			{
				// Get user data by field and data, other field are ID, slug, slug and login
				$update_user = wp_update_user( array (
						'ID' => $user->ID, 
						'user_pass' => $random_password
					)
				);
					$success = true;
					$message =   __( 'Email sent', 'carspot-rest-api' );	

			}
			else
			{
					$success = false;
					$message =   __( 'Email server not responding', 'carspot-rest-api' );	
			}
				
	}
	else
	{
		$success = false;
		$message =   __( 'Email is not resgistered with us.', 'carspot-rest-api' );	
	}
	
			$response = array( 'success' => $success, 'data' => '' , 'message' => $message );
			return $response;
	
}
}



/*Send Email On Forgot pass*/
if( !function_exists('carspotAPI_send_email_new_bid' ) )
{
function carspotAPI_send_email_new_bid( $sender_id, $receiver_id, $bid = '', $comments = '', $aid )
{
		global $carspotAPI;
		$receiver_info = get_userdata($receiver_id);
		$to = $receiver_info->user_email;
		$from	=	'';
		if( isset( $carspotAPI['sb_new_bid_from'] ) && $carspotAPI['sb_new_bid_from'] != "" )
		{
			$from	=	$carspotAPI['sb_new_bid_from'];
		}
			$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
		if( isset( $carspotAPI['sb_new_bid_message'] ) &&  $carspotAPI['sb_new_bid_message'] != "" )
		{
			$subject_keywords  = array('%site_name%');
			$subject_replaces  = array(get_bloginfo( 'name' ));			
			$subject = str_replace($subject_keywords, $subject_replaces, $carspotAPI['sb_new_bid_subject']);
			// Bidder info
			$sender_info = get_userdata( $sender_id );

			$msg_keywords  = array('%site_name%', '%receiver%', '%bidder%', '%bid%', '%comments%' , '%bid_link%' );
			$msg_replaces  = array(get_bloginfo( 'name' ), $receiver_info->display_name, $sender_info->display_name , $bid, $comments, get_the_permalink($aid).'#tab2default' );
			
			$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['sb_new_bid_message']);
			wp_mail( $to, $subject, $body, $headers );
		}
}
}


/*Email on Ad rating*/
if( !function_exists('carspotAPI_email_ad_rating' ) ){
function carspotAPI_email_ad_rating($pid,$sender_id, $rating, $comments)
{
	global $carspotAPI;
	$from	=	get_bloginfo( 'name' );
	if( isset( $carspotAPI['ad_rating_email_from'] ) && $carspotAPI['ad_rating_email_from'] != "" )
	{
		$from	=	$carspotAPI['ad_rating_email_from'];
	}
	$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
	if( isset( $carspotAPI['ad_rating_email_message'] ) &&  $carspotAPI['ad_rating_email_message'] != "" )
	{
		
		$author_id = get_post_field ('post_author', $pid);
		$user_info = get_userdata($author_id);
		
		$subject = $carspotAPI['ad_rating_email_subject'];

		$msg_keywords  = array('%site_name%', '%ad_title%', '%ad_link%', '%rating%', '%rating_comments%', '%author_name%');
		$msg_replaces  = array(get_bloginfo( 'name' ), get_the_title($pid), get_the_permalink($pid) . '#ad-rating', $rating, $comments, $user_info->display_name );
		
		$to = $user_info->user_email;
		$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['ad_rating_email_message']);
		$mail  = wp_mail( $to, $subject, $body, $headers );
		if($mail)
		{
			return 'sent';
		}
		else
		{
			return 'not sent';
		}
	}
}
}

/*Email on Ad rating reply*/
if( !function_exists('carspotAPI_email_ad_rating_reply' ) ){
function carspotAPI_email_ad_rating_reply($pid,$receiver_id, $reply, $rating, $rating_comments)
{
	global $carspotAPI;
	$from	=	get_bloginfo( 'name' );
	if( isset( $carspotAPI['ad_rating_reply_email_from'] ) && $carspotAPI['ad_rating_reply_email_from'] != "" )
	{
		$from	=	$carspotAPI['ad_rating_reply_email_from'];
	}
	$headers = array('Content-Type: text/html; charset=UTF-8',"From: $from" );
	if( isset( $carspotAPI['ad_rating_reply_email_message'] ) &&  $carspotAPI['ad_rating_reply_email_message'] != "" )
	{
		
		$author_id = get_post_field ('post_author', $pid);
		$user_info = get_userdata($author_id);
		
		$subject = $carspotAPI['ad_rating_reply_email_subject'];

		$msg_keywords  = array('%site_name%', '%ad_title%', '%ad_link%', '%rating%', '%rating_comments%', '%author_name%', '%author_reply%');
		$msg_replaces  = array(get_bloginfo( 'name' ), get_the_title($pid), get_the_permalink($pid) . '#ad-rating', $rating, $rating_comments, $user_info->display_name, $reply );
		
		$receiver_info = get_userdata($receiver_id);
		$to = $receiver_info->user_email;
		$body = str_replace($msg_keywords, $msg_replaces, $carspotAPI['ad_rating_reply_email_message']);
		wp_mail( $to, $subject, $body, $headers );
	}
}
}