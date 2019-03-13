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
  

add_action( 'rest_api_init', 'carspotAPI_hook_for_getting_comparison', 0 );
function carspotAPI_hook_for_getting_comparison() {
    register_rest_route(
        'carspot/v1', '/comparison/', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_get_comparison_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
    register_rest_route(
        'carspot/v1', '/comparison/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_get_comparison_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );	
}  

if( !function_exists('carspotAPI_get_comparison_get' ) )
{
	function carspotAPI_get_comparison_get( $request )
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
			'post_type' => 'comparison',
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
			$count = 1;
			while ( $posts->have_posts() ) 
			{
				$posts->the_post();
				$post_id 			= 			get_the_ID();
				$arr['post_id'] 	= 			$post_id;
				$arr['title'] 		= 			get_the_title();
				$arr['date'] 		=  			get_the_date("", $post_id);
				$arr['rating'] 		=  			carspotAPI_get_comparison_rating( $post_id);
				 $image  			= 			get_the_post_thumbnail_url( $post_id, 'medium'); 
				if( !$image ) $image= 			'';
				$arr['has_image'] 	= 			( $image ) ? true : false; 	
				$arr['image'] 	  	= 			$image; 
				$comments 		  	= 			wp_count_comments( $post_id );
				$arr['comments']  	= 			$comments->approved;
				$arr['read_more'] 	= 			__("Read More", "carspot-rest-api");
				$count++;
				if($count % 2 == 0)
				{
					$post_id 			= 			get_the_ID();
					$arr1['post_id'] 	= 			$post_id;
					$arr1['title'] 		= 			get_the_title();
					$arr1['date'] 		=  			get_the_date("", $post_id);
					$arr1['rating'] 		=  			carspotAPI_get_comparison_rating( $post_id);
					 $image1  			= 			get_the_post_thumbnail_url( $post_id, 'medium'); 
					if( !$image1 ) $imag1e= 			'';
					$arr1['has_image'] 	= 			( $image1 ) ? true : false; 	
					$arr1['image'] 	  	= 			$image1; 
					$comments 		  	= 			wp_count_comments( $post_id );
					$arr1['comments']  	= 			$comments->approved;
					$arr1['read_more'] 	= 			__("Read More", "carspot-rest-api");
					
				}
				if($count == 2)
				{
					$count == 0;
				}
				
				//final array
				if ( 'publish' == get_post_status ( $post_id )) 
				{
					$post_data[] 		= 	array($arr,$arr1) ;
				}
			
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		} 
		else
		 {
			$message = __("no posts found", "carspot-rest-api");
		 }	
		
		$data['comparison']  = $post_data;
		
		$nextPaged     = $paged + 1;
		$has_next_page = ( $nextPaged <= (int)$posts->max_num_pages ) ? true : false;
	
	$data['pagination'] = array("max_num_pages" => (int)$posts->max_num_pages, "current_page" => (int)$paged, "next_page" => (int)$nextPaged, "increment" => (int)$posts_per_page , "current_no_of_ads" =>  (int)($posts->found_posts), "has_next_page" => $has_next_page );		
		
		$extra['page_title'] = __("Comparison", "carspot-rest-api" );
		$extra['vs_txt']     = __("V/s", "carspot-rest-api" );
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


if ( ! function_exists( 'carspotAPI_get_comparison_rating' ) ) {
function carspotAPI_get_comparison_rating($post_id = '')
{
	 $comp_rating =''; $get_values = ''; $star ='';  $star1 = "";
	 if( get_post_meta($post_id, 'comparison_rating', true ) != "" )
	 {
		
		$get_values = get_post_meta($post_id, 'comparison_rating', true );
		
		 for($i=1;$i<=5;$i++)
		 {
			  if(!empty($get_values) && $i<=$get_values)
			  {
                $star = $get_values;
              }
              
		 }

	 }
	 return $star;
}
}

/*******************************/
/*Comparison details Start here*/
/*******************************/
add_action( 'rest_api_init', 'carspotAPI_hook_comparison_details', 0 );
function carspotAPI_hook_comparison_details() {
    register_rest_route(
        'carspot/v1', '/comparison/detail/', array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_comparison_details',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
}  



if( !function_exists('carspotAPI_comparison_details' ) )
{
	function carspotAPI_comparison_details( $request )
	{
		$json_data 		= $request->get_json_params();		
		$id1 		    = (isset( $json_data['car1'] ) && $json_data['car1'] != "" ) ? $json_data['car1'] : '';
		$id2 		    = (isset( $json_data['car2'] ) && $json_data['car2'] != "" ) ? $json_data['car2'] : '';	
		
		
		$args = array(
			'post_type' => 'comparison',
			'post_status' => 'publish', 
			'posts_per_page' => '-1', 
			'order'=> 'DESC', 
			'orderby' => 'date' 
		);		
		$message = '';
		$posts   = new WP_Query( $args );
		$data    = array();
		$posts_comp   = array();
		if ( $posts->have_posts() ) 
		{
			$count = 1;
			while ( $posts->have_posts() ) 
			{
				$posts->the_post();
				$post_id 			    = 			get_the_ID();
				$posts_comp[]           =           array("id" => $post_id, "name" => get_the_title());
				$count++;
			}
		}
		
		
		$data['compare_posts'] 		  = $posts_comp;
		
	
		
		$arr['images']  = carspotAPI_comparison_img($id1, $id2);
		$terms          = get_terms( array(  'taxonomy' => 'comparison_by', 'hide_empty' => false, 'parent' => 0 , 'meta_key' => 'order','orderby'=> 'order', 'order' => 'ASC' ) );
			$first_count = 0;
		    foreach($terms as $term )
			 {
					$arr['tabs'][$first_count]['tab_name']      =    esc_html($term->name);
					
					$subterms   =    get_terms( array( 'taxonomy' => 'comparison_by', 'parent'   => $term->term_id,  'hide_empty' => false, 'orderby'=> 'order', 'order' => 'ASC'  ));
					$count 				  =   0;
					$tab_info = array(); 	
					foreach ( $subterms as $subterm ) 
					{
							   $tab_info["$count"]['title']  = $subterm->name;
							   $tab_info["$count"]['col1']   = carspotAPI_get_metavalue($id1, $subterm->slug, $subterm->term_id );
							   $tab_info["$count"]['col2']   = carspotAPI_get_metavalue($id2, $subterm->slug, $subterm->term_id);
							   $count++;
					}
					$arr['tabs'][$first_count]['features'] = $tab_info;
					$first_count++;
		     }
		
		
		
		$data['post'] 		  = $arr;
		$extra['page_title']  = __("Comparison Details", "carspot-rest-api" );
		$extra['compare_txt'] = __("Select Car's", "carspot-rest-api" );
		$extra['compare_txt2'] = __("you want to compare", "carspot-rest-api" );
		$extra['compare_txt2'] = __("you want to compare", "carspot-rest-api" );
		$extra['compare_select'] = __("Select an option", "carspot-rest-api" );
		$extra['compare'] = __("Compare", "carspot-rest-api" );
		
		
		
		return $response = array( 'success' => true, 'data' => $data, 'message'  => '', 'extra' => $extra);			
		
		
		
	}
}


/*******************************/
/*Comparison details Start here*/
/*******************************/

if ( ! function_exists( 'carspotAPI_get_metavalue' ) ) {
function carspotAPI_get_metavalue($post_id = '', $term_slug = '', $term_id = '')
{
	$valHtml = array();
	if( get_post_meta($post_id, 'car_comparison_'.$term_slug, true ) != "" )
	 {
		 $val = get_post_meta( $post_id , 'car_comparison_'.$term_slug, true );
		 $type = get_term_meta( $term_id , '_carspot_comparison_field_type' , true);
		
		if($type == 1 )
		{
			if($val == 1)
			{
				$valHtml = '<i class="fa fa-check"></i>';
			}
			else
			{
				$valHtml = '<i class="fa fa-times"></i>';
			}
		}
		else
		{
		   $valHtml = $val;
		}
	 }
	 
	return  $valHtml;
}
}

/*****************/
/*Comparison image*/
/*****************/


if ( ! function_exists( 'carspotAPI_comparison_img' ) )
 {
	function carspotAPI_comparison_img($id1 = '', $id2 = '')
	{
		 $response1 = '';  $response2 = ''; $image = array();
		 $response1	=	carspot_get_feature_image( $id1, '' );
		 $response2	=	carspot_get_feature_image( $id2, '' );
		 $image['title1']  = get_the_title($id1);
		 $image['title2']  = get_the_title($id2);
		 $image['link1']   = esc_url($response1[0]);
		 $image['link2']   = esc_url($response2[0]);
		 $image['rat1']    = carspotAPI_get_comparison_rating($id1);
		 $image['rat2']    = carspotAPI_get_comparison_rating($id2);
		 
		return $image;
	}
}


if( !function_exists('carspotAPI_get_comparison_in_home' ) )
{
	function carspotAPI_get_comparison_in_home()
	{
		
		
	global $carspotAPI;
	$carspot_api_com_car1 = ( isset( $carspotAPI['carspot-api-com_car1'] ) && $carspotAPI['carspot-api-com_car1'] ) ? $carspotAPI['carspot-api-com_car1'] : array();
	
	$carspot_api_com_car2 = ( isset( $carspotAPI['carspot-api-com_car2'] ) && $carspotAPI['carspot-api-com_car2'] ) ? $carspotAPI['carspot-api-com_car2'] : array();
		

		
		
		if( isset($carspot_api_com_car1) && !empty($carspot_api_com_car1) &&  count($carspot_api_com_car1) > 0 )
		{
			foreach($carspot_api_com_car1 as $key => $value )
			{
				
				if( isset($carspot_api_com_car2) && !empty($carspot_api_com_car2) &&  count($carspot_api_com_car2) > 0 )
				{
					
					if( isset( $carspot_api_com_car2[$key] ) && $carspot_api_com_car2[$key] != "" )
					{
		
					/*Car 1*/
					$post_id        	= 	$value;
					$arr['post_id'] 	= 	$post_id;
					$arr['title'] 		= 	get_the_title($post_id);
					$arr['date'] 		=  	get_the_date("", $post_id);
					$arr['rating'] 	=  	carspotAPI_get_comparison_rating( $post_id);
					$image  			= 	get_the_post_thumbnail_url( $post_id, 'medium'); 
					if( !$image )  
					$image 				= 	'';
					$arr['has_image'] 	= 	( $image ) ? true : false; 	
					$arr['image'] 		= 	$image; 
					$comments 			= 	wp_count_comments( $post_id );
					$arr['comments'] 	= 	$comments->approved;
					$arr['read_more'] 	= 	__("Read More", "carspot-rest-api");
					/*Car 2*/
					$car2 = $carspot_api_com_car2[$key];
					$arr2['post_id'] 	= 	$car2;
					$arr2['title'] 		= 	get_the_title($car2);
					$arr2['date'] 		=  	get_the_date("", $car2);
					$arr2['rating'] 	=  	carspotAPI_get_comparison_rating( $car2);
					$image  			= 	get_the_post_thumbnail_url( $car2, 'medium'); 
					if( !$image )  
					$image 				= 	'';
					$arr2['has_image'] 	= 	( $image ) ? true : false; 	
					$arr2['image'] 		= 	$image; 
					$comments 			= 	wp_count_comments( $car2 );
					$arr2['comments'] 	= 	$comments->approved;
					$arr2['read_more'] = 	__("Read More", "carspot-rest-api");
					
					    //final array
						if ( 'publish' == get_post_status ( $value ) &&  'publish' == get_post_status ( $car2 )) 
						{
						  	$post_data[] 		= 	array($arr,$arr2) ;
						}
					}
				
				}
				
				
			}
		}

		
		$comp_is_show = (isset( $carspotAPI['posts_comparison_home'] ) && $carspotAPI['posts_comparison_home'] == 1 ) ? true :false;
		$comp_sec_txt = (isset( $carspotAPI['api_comparison_title'] ) ) ? $carspotAPI['api_comparison_title'] : '';
		$comp_sec_view = (isset( $carspotAPI['api_comparison_view_all'] ) ) ? $carspotAPI['api_comparison_view_all'] : '';
		$comp_sec_vs = (isset( $carspotAPI['api_comparison_vs_txt'] ) ) ? $carspotAPI['api_comparison_vs_txt'] : '';
		$str          =  $comp_sec_txt;
		$comp_sec_txt =  (explode("|",$str));
		$data['comp_is_show'] = $comp_is_show;
		$data['txt1'] = (isset( $comp_sec_txt[0] ) ) ? $comp_sec_txt[0]  : '';
		$data['txt2'] = (isset( $comp_sec_txt[1] ) ) ? $comp_sec_txt[1]  : '';
		$data['view_all'] = $comp_sec_view;
		$data['vs_txt'] = $comp_sec_vs;
		$data['comparison']  = $post_data;
		
		//$data['comparison2']  = $post_data2;
		
				
		
		return $data;			
	}
}