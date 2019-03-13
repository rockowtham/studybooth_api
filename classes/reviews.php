<?php /* POSTS API HERE */
 /* add_action( 'init', 'carspotAPI_post_type_rest_support', 25 );
  function carspotAPI_post_type_rest_support() {
  	global $wp_post_types;
		be sure to set this to the name of your post type!
		$post_type_name = 'comparison';
		if( isset( $wp_post_types[ $post_type_name ] ) ) 
		{
			$wp_post_types[$post_type_name]->show_in_rest = true;
			$wp_post_types[$post_type_name]->rest_base = $post_type_name;
			$wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
		}
  }	*/
  

add_action( 'rest_api_init', 'carspotAPI_hook_for_getting_reviews', 0 );
function carspotAPI_hook_for_getting_reviews() {
    register_rest_route(
        'carspot/v1', '/reviews/', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_get_reviews_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
    register_rest_route(
        'carspot/v1', '/reviews/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_get_reviews_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );	
}  

if( !function_exists('carspotAPI_get_reviews_get' ) )
{
	function carspotAPI_get_reviews_get( $request )
	{
		$json_data = $request->get_json_params();
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		} else if ( isset( $json_data['page_number'] ) ) {
			$paged = $json_data['page_number'];
		} else {
			$paged = 1;
		}		
		$posts_per_page = get_option( 'posts_per_page' );					
		$args = array(
			'post_type' => 'reviews',
			'post_status' => 'publish', 
			'posts_per_page' => $posts_per_page, 
			'paged' => $paged, 
			'order'=> 'DESC', 
			'orderby' => 'date' 
		);		
		$message = '';
		$posts   = new WP_Query( $args );
		$data    = array();
		$arr     = array();
		$post_data = array();
		if ( $posts->have_posts() ) {
	
			while ( $posts->have_posts() ) 
			{
				$posts->the_post();
				$post_id = get_the_ID();
				$arr['post_id'] = $post_id;
				$arr['title'] = get_the_title();
				$arr['date'] =  get_the_date("", $post_id);
				$terms = get_the_terms($post_id , 'reviews_cats' );
				$cat_name  = '';
				if ( $terms != null ){
					foreach ( $terms as $term ) {
						$arr['cats'] = esc_html($term->name); 
					}
				}
				
				 $image  = get_the_post_thumbnail_url( $post_id, 'medium'); 
				 if( !$image )  
					$image = '';
					
				$arr['has_image'] = ( $image ) ? true : false; 	
				$arr['image'] = $image; 
				$comments = wp_count_comments( $post_id );
				$arr['comments'] = $comments->approved;
				$arr['read_more'] = __("Read More", "carspot-rest-api");
				$post_data[] = $arr;
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		} 
		else
		 {
			$message = __("no posts found", "carspot-rest-api");
		 }	
		
		$data['post']  = $post_data;
		
		$nextPaged     = $paged + 1;
		$has_next_page = ( $nextPaged <= (int)$posts->max_num_pages ) ? true : false;
	
	$data['pagination'] = array("max_num_pages" => (int)$posts->max_num_pages, "current_page" => (int)$paged, "next_page" => (int)$nextPaged, "increment" => (int)$posts_per_page , "current_no_of_ads" =>  (int)($posts->found_posts), "has_next_page" => $has_next_page );		
		
		$extra['page_title'] = __("Reviews", "carspot-rest-api" );
		$extra['comment_title'] = __("Comments", "carspot-rest-api" );
		$extra['load_more'] =  __("Load More", "carspot-rest-api" );
		$extra['load_more'] =  __("Load More", "carspot-rest-api" );		
		$extra['comment_form']['title'] =  __("Post your comments here", "carspot-rest-api" );
		$extra['comment_form']['textarea'] =  __("Your comment here", "carspot-rest-api" );
		$extra['comment_form']['btn_submit'] =  __("Post Comment", "carspot-rest-api" );
		$extra['comment_form']['btn_cancel'] =  __("Cancel Comment", "carspot-rest-api" );		
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => $message, 'extra' => $extra);			
	}
}

/*Post details Start here*/
add_action( 'rest_api_init', 'carspotAPI_hook_review_details', 0 );
function carspotAPI_hook_review_details() {
    register_rest_route(
        'carspot/v1', '/review/detail/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_review_details',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
}  

if( !function_exists('carspotAPI_review_details' ) )
{
	function carspotAPI_review_details( $request )
	{
		$json_data 		= $request->get_json_params();		
		$review_id 		= (isset( $json_data['review_id'] ) && $json_data['review_id'] != "" ) ? $json_data['review_id'] : '';	
	
		$review 	    = get_post( $review_id );
		
		$post_author_id = $review->post_author;
		$post_id = $review->ID;
		$arr['post_id'] = $post_id;
		$arr['author_name'] = get_the_author_meta( 'display_name', $post_author_id) ;
		$arr['title'] = $review->post_title;
		$arr['date'] = get_the_date("", $post_id);	
		
		$wrapHTML  = '<span style=" color:#a0a0a0;line-height:24px;">'.$review->post_content.'</span>';
		
		$arr['desc'] = $wrapHTML;
		$image_list = array();
		 $gallery = get_post_gallery_images( $post_id);
		 foreach( $gallery as $image_url ) 
		 {
			$arr['gallery'][] = $image_url;
		}
		
		
		
		
		$arr['like_heading'] = ($review->tab_heading);
		$arr['likes'] = ($review->tab_desc_1);
		
		$arr['unlike_heading'] = ($review->tab_heading_2);
		$arr['unlikes'] = ($review->tab_desc_2);
		
		
		
		$arr['rating'] = ($review->review_rating);
		$arr['video'] = ($review->youtube_video);	
		$arr['verdict_title'] = $review->verdict_title;
		$arr['verdict_details'] = $review->verdict_sunnmry;
		$arr['verdict'] = carspotAPI_get_meta_verdict($post_id);
		
		
		$image  = get_the_post_thumbnail_url( $post_id, 'medium'); 
		if( !$image )  
			$image = '';
	
		$arr['has_image'] = ( $image ) ? true : false; 
		$arr['image'] = $image; 					
		$arr['features'] = carspotAPI_get_meta( $post_id);
		$arr['comment_count'] =  $review->comment_count;
		
		$arr['has_comment'] = ($review->comment_count > 0  ) ? true : false;
		$comment_mesage = '';
		if( $review->comment_status == 'closed' )
		{
			$comment_mesage = __("Comment are closed", "carspot-rest-api" );
		}
		else
		{
			$comment_mesage = __("No Comment Found", "carspot-rest-api" );
		}
		$arr['comment_mesage'] = $comment_mesage;
		
		$arr['comments']  	= carspotAPI_get_post_comments( $post_id, $review->comment_count );
		$data['post'] 		= $arr;
		
		$extra['page_title'] = __("Blog Details", "carspot-rest-api" );
		$extra['comment_title'] = __("Comments", "carspot-rest-api" );
		$extra['load_more'] =  __("Load More", "carspot-rest-api" );
		$extra['gallert'] =  __("Gallery", "carspot-rest-api" );
		$extra['verdict'] =  __("Verdict", "carspot-rest-api" );
		$extra['video_review'] =  __("video review", "carspot-rest-api" );
		$extra['summary'] =  __("summary", "carspot-rest-api" );
		
		$extra['comment_form']['title'] =  __("Post your comments here", "carspot-rest-api" );
		$extra['comment_form']['textarea'] =  __("Your comment here", "carspot-rest-api" );
		$extra['comment_form']['btn_submit'] =  __("Post Comment", "carspot-rest-api" );
		$extra['comment_form']['btn_cancel'] =  __("Cancel Comment", "carspot-rest-api" );
		
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => '', 'extra' => $extra);			
		
		
		
	}
}



// Custom Fields Fetching Tables
if ( ! function_exists( 'carspotAPI_get_meta' ) )
{
	function carspotAPI_get_meta($pid) 
	{
		$features = array(); 
		$clean_string = '';
		$c_terms = get_terms('custom_fields', array('hide_empty' => false , 'orderby'=> 'id', 'order' => 'ASC' ));	
		if( count((array) $c_terms ) > 0 )
		{	
			foreach( $c_terms as $c_term)
			{
				$meta_name  =  'car_features_'.$c_term->slug;
				 $meta_value =  get_post_meta($pid, $meta_name, true);
				if( $meta_value != "" )
				{
					$features[] =   array( 'name' => $c_term->name, 'value' => $meta_value);
					
				}
			}
		}
			return $features;
	}
}
/* Get verdict */
if ( ! function_exists( 'carspotAPI_get_meta_verdict' ) ) {
	function carspotAPI_get_meta_verdict($pid) {
	$verdict = array();
	$c_terms = get_terms('verdict', array('hide_empty' => false , 'orderby'=> 'id', 'order' => 'ASC' ));	
	if( count((array) $c_terms ) > 0 )
	{
		foreach( $c_terms as $c_term)
		{
			$meta_name  =  'car_verdict_'.$c_term->slug;
			$meta_value =  get_post_meta($pid, $meta_name, true);
			if( $meta_value != "" )
			{
				
				$verdict[] =   array( 'name' => $c_term->name, 'value' => $meta_value);
				
			}
		}
	}	
	 
		return $verdict;	
}
 }