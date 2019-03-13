<?php
/*-----
	Home Screen Starts Here	
-----*/
add_action( 'rest_api_init', 'carspotAPI_homescreen_api_hooks_get', 0 );
function carspotAPI_homescreen_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/home/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_homeScreen_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
}

if (!function_exists('carspotAPI_homeScreen_get'))
{
	function carspotAPI_homeScreen_get()
	{
				
		global $carspotAPI;
		$user = wp_get_current_user();		
		$user_id = @$user->data->ID;
		
		$screenTitle = (isset( $carspotAPI['sb_home_screen_title'] ) && $carspotAPI['sb_home_screen_title'] != "" ) ? $carspotAPI['sb_home_screen_title'] : __("Home Screen", "carspot-rest-api");
		$tagline   		= (isset( $carspotAPI['hom_sec_tagline'] )) ? $carspotAPI['hom_sec_tagline'] : "";
		$headline   	= (isset( $carspotAPI['hom_sec_headline'] )) ? $carspotAPI['hom_sec_headline'] : "";
		$place_holder   = (isset( $carspotAPI['hom_sec_place_holder'] )) ? $carspotAPI['hom_sec_place_holder'] : "";
		$advance_search = (isset( $carspotAPI['hom_sec_advance_search'] )) ? $carspotAPI['hom_sec_advance_search'] : ""; 
		
		
		
		$data['page_title']     =  $screenTitle;
		$data['heading'] 	    =  $headline;
		$data['tagline'] 	    =  $tagline;
		$data['placehldr'] 	    =  $place_holder;
		$data['advance_search'] =  $advance_search;
		$data['img'] 			=  carspotAPI_home_secreen_bg();
		$catData                = array();
		$data['ads_position_sorter'] =  false;
		
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
				
		if(isset( $carspotAPI['home-screen-sortable-enable'] ) && $carspotAPI['home-screen-sortable-enable'] && $has_value == true )
		{
			if(isset( $array_sortable ) && $array_sortable > 0 )
			{
				$arrays = $array_sortable;
				$positions = array();
				$position_sorter = false;
				foreach( $arrays as $key => $val )
				{
					if( isset($val)  && $val != "" )
					{
						$position_sorter = true;
						$position[] = $key;
					}
					if( $key == "cat_icons" && $val != "")
					{
						/*Cats With icons*/	
						$cat_icon_txt = (isset( $carspotAPI['categories-section-title'] ) ) ? $carspotAPI['categories-section-title'] : '';
						$str          =  $cat_icon_txt;
                        $cat_icon_txt =  (explode("|",$str));
						$data['featured_makes_txt1'] = (isset( $cat_icon_txt[0] ) ) ? $cat_icon_txt[0]  : '';
						$data['featured_makes_txt2'] = (isset( $cat_icon_txt[1] ) ) ? $cat_icon_txt[1]  : '';
						$data['featured_makes_column'] = (isset( $carspotAPI['api_cat_columns'] ) ) ? $carspotAPI['api_cat_columns'] : 3;
						$data['featured_makes_view_all'] = (isset( $carspotAPI['categories-section-view-all'] ) ) ? $carspotAPI['categories-section-view-all'] : '';
						$data['featured_makes_is_show'] = (isset( $carspotAPI['api_home_featured_makes_switch'] ) && $carspotAPI['api_home_featured_makes_switch'] == 1 ) ? true :false;
						
						
						$data['featured_makes']	= carspotAPI_home_adsLayouts( 'cat_icons' );
						/*Cats With icons*/	
					}
					
					if( $key == "body_type" && $val != "")
					{
						/*Body type  with icons*/
						$body_types_is_show = (isset( $carspotAPI['api_home_body_type_switch'] ) && $carspotAPI['api_home_body_type_switch'] == 1 ) ? true :false;
						$body_type_icon_txt = (isset( $carspotAPI['categories-section-title'] ) ) ? $carspotAPI['categories-section-title'] : '';
						$str          =  $body_type_icon_txt; 
                        $cat_icon_txt =  (explode("|",$str));
						$data['body_type_is_show'] = $body_types_is_show;
						$data['body_type_txt1'] = (isset( $cat_icon_txt[0] ) ) ? $cat_icon_txt[0]  : '';
						$data['body_type_txt2'] = (isset( $cat_icon_txt[1] ) ) ? $cat_icon_txt[1]  : '';
						$data['body_type_icons'] = carspotAPI_home_adsLayouts( 'body_type' );
						/*Cats With icons*/	
					}
					
					
					if( $key == "featured_ads" && $val != "")
					{
						/*Featured Ads Settings Starts Here */
						$featured = carspotAPI_home_adsLayouts( 'featured' );
						$data['featured_ads']	   = $featured['featured_ads'];
						$data['is_show_featured']  = $featured['is_show_featured'];
						$data['featured_position'] = $featured['featured_position'];
						/*Featured Ads Settings Ends Here */								
					}
					
					if( $key == "latest_ads" && $val != "")
					{
						/*Latest Ads Settings Starts Here */
						$latest = carspotAPI_home_adsLayouts( 'latest' );
						$data['latest_ads']	   = $latest['latest_ads'];
						$data['is_show_latest']  = $latest['is_show_latest'];
						//$data['latest_position'] = $latest['latest_position'];
						/*Featured Ads Settings Ends Here */								
					}
					
					
					if( $key == "cat_locations" && $val != "")
					{
						/*Locations Settings*/
						$cat_icon_txt = (isset( $carspotAPI['api_location_title'] ) ) ? $carspotAPI['api_location_title'] : '';
						$str          =  $cat_icon_txt;
                        $cat_icon_txt =  (explode("|",$str));
						$data['locations_is_show'] = (isset( $carspotAPI['api_location_on_home'] ) && $carspotAPI['api_location_on_home'] == 1 ) ? true :false;
						
						
						$data['locations_txt1'] = (isset( $cat_icon_txt[0] ) ) ? $cat_icon_txt[0]  : '';
						$data['locations_txt2'] = (isset( $cat_icon_txt[1] ) ) ? $cat_icon_txt[1]  : '';
						
						$data['locations_column'] = (isset( $carspotAPI['api_location_columns'] ) ) ? $carspotAPI['api_location_columns'] : 2;
						$data['locations_type']	= 'ad_locations';
						$data['locations']	= carspotAPI_home_adsLayouts( 'locations' );

					}	

					if( $key == "blogNews" && $val != "")
					{
						/*Latest Ads Settings Starts Here */
						$latestData = carspotAPI_home_adsLayouts( 'blogNews' );
						$data['is_show_blog']        = $latestData['is_show_blog'];
						$data['latest_blog']	   	 = $latestData['latest_blog'];
						/*Latest Ads Settings Ends Here */		
					}
					if( $key == "comparison" && $val != "")
					{
						$data['comparisonData']	= 	carspotAPI_get_comparison_in_home();
						/*Latest Ads Settings Ends Here */		
					}					
					/*carspotAPI_home_adsLayouts*/					
				}
				
				$data['ads_position_sorter'] =  $position_sorter;
				$data['ads_position'] = $position;
				
			}
		}
		else
		{
			/*Featured Ads Settings Starts Here */
			$featured = carspotAPI_home_adsLayouts( 'featured' );
			$data['featured_ads']	   = $featured['featured_ads'];
			$data['is_show_featured']  = $featured['is_show_featured'];
			$data['featured_position'] = $featured['featured_position'];
			/*Featured Ads Settings Ends Here */

			/*Latest Ads Settings Starts Here *-/
			$latestData = carspotAPI_home_adsLayouts( 'latest' );
			$data['is_show_latest']  = $latestData['is_show_latest'];
			$data['latest_ads']	   	 = $latestData['latest_ads'];
			/*Latest Ads Settings Ends Here */

			/*Cats With icons*/			
			$data['cat_icons_column'] = (isset( $carspotAPI['api_cat_columns'] ) ) ? $carspotAPI['api_cat_columns'] : 3;
			$data['cat_icons']	= carspotAPI_home_adsLayouts( 'cat_icons' );
			/*Cats With icons*/	
			
			/*Locations Settings*-/
			$data['cat_locations_title'] = (isset( $carspotAPI['api_location_title'] ) ) ? $carspotAPI['api_location_title'] : __("Locations", "carspot-rest-api");
			$data['cat_locations_column'] = (isset( $carspotAPI['api_location_columns'] ) ) ? $carspotAPI['api_location_columns'] : 2;
			$data['cat_locations_type']	= 'ad_locations';
			$data['cat_locations']	= carspotAPI_home_adsLayouts( 'locations' );
			*Locations Settings*/
			
			$data['sliders'] 	= carspotAPI_home_adsLayouts( 'multi_slider' );
		}
		$data['view_all']		    =   __("View All", "carspot-rest-api");
		$data['menu'] 	            =   carspotAPI_appMenu_settings();
		$data['extra']['ad_post'] 	= __("Ad Post", "carspot-rest-api");
		$data['extra']['all_cats'] 	= __("View All Categories", "carspot-rest-api");
		
		$settings = carspotAPI_settings_data( $user_id );
		
		return $response = array( 'success' => true, 'data' => $data, 'settings' => $settings, 'message'  => ''  );
		
	}
}


if (!function_exists('carspotAPI_home_adsLayouts'))
{
	function carspotAPI_home_adsLayouts($type = '')
	{
		global $carspotAPI;
		/* Cat icons Starts */
		if( $type == 'cat_icons' )
		{
			$catData = array();
			if( isset( $carspotAPI['carspot-api-ad-cats-multi'] ) )
			{
				$cats = $carspotAPI['carspot-api-ad-cats-multi'];
				if( count( $cats ) > 0 )
				{
					foreach($cats as $cat)
					{
						$term = get_term( $cat, 'ad_cats' ); 
						$name = htmlspecialchars_decode($term->name, ENT_NOQUOTES);
						$imgUrl = carspotAPI_taxonomy_image_url( $cat, NULL, TRUE );
						$catData[] = array("cat_id" => $term->term_id, "name" => $name, "img" => $imgUrl);
					}
					
					
				}
			}
			return $catData;

		}
		/* Cat icons ends */
		/* Body type Starts */
		if( $type == 'body_type' )
		{
			$catData = array();
			if( isset( $carspotAPI['carspot-api-ad-body-types-multi'] ) )
			{
				$cats = $carspotAPI['carspot-api-ad-body-types-multi'];
				if( count( $cats ) > 0 )
				{
					foreach($cats as $cat)
					{
						$term = get_term( $cat, 'ad_body_types' ); 
						$name = htmlspecialchars_decode($term->name, ENT_NOQUOTES);
						$imgUrl = carspotAPI_taxonomy_image_url( $cat, NULL, TRUE );
						$catData[] = array("body_type_id" => $term->term_id, "name" => $name, "img" => $imgUrl);
					}
				}
			}
				return $catData;
		}
		
		
		/*Multi Slider ads options sortable ends */		
		/*Featured Ads Starts Here */
		if( $type == 'featured' )
		{
			$fads['text']       = array();
			$fads['text2']      = array();
			$fads['view_all']   = array();
			$fads['ads']        = array();
			$data = array();
			$data['featured_ads']	  = array();
			$data['featured_position'] = (isset( $carspotAPI['home_featured_position'] ) && $carspotAPI['home_featured_position'] != "" ) ? $carspotAPI['home_featured_position'] : "1";
			$data['is_show_featured'] = (isset( $carspotAPI['feature_on_home'] ) && $carspotAPI['feature_on_home'] == 1 ) ? true :false;
			
			if( isset( $carspotAPI['feature_on_home'] ) && $carspotAPI['feature_on_home'] == 1 )
			{
				$featuredAdsCount = ( $carspotAPI['home_related_posts_count'] != "" ) ?  $carspotAPI['home_related_posts_count'] : 5; 
						
				       $featuredAdsTitle = (isset( $carspotAPI['sb_home_ads_title'] ) ) ? $carspotAPI['sb_home_ads_title'] : '';
					   $featuredAdsviewall = (isset( $carspotAPI['sb_home_ads_view_all'] ) ) ? $carspotAPI['sb_home_ads_view_all'] : '';
						$str          =  $featuredAdsTitle;
                        $featuredAdsTitle =  (explode("|",$str));
						
				$featured_termID = ( isset( $json_data['ad_cats1'] ) && $json_data['ad_cats1'] != ""  ) ? $json_data['ad_cats1'] : '';
				$featuredAds = carspotApi_featuredAds_slider( '', 'active', '1', $featuredAdsCount, $featured_termID, 'publish');	
				if( isset( $featuredAds ) && count( $featuredAds ) > 0 )
				{		
					$fads['text']  = (isset( $featuredAdsTitle[0] ) ) ? $featuredAdsTitle[0]  : '';
					$fads['text2'] = (isset( $featuredAdsTitle[1] ) ) ? $featuredAdsTitle[1]  : '';
					$fads['view_all'] = $featuredAdsviewall;
					$fads['ads']   = $featuredAds;
					$data['is_show_featured'] = true;
				}
				else
				{
					$data['is_show_featured'] = false;	
				}
				
				$data['featured_ads'] = $fads;
			}
			return $data;
		}
		
		/*Featured ads ends here*/
		/*Latest Ads Start Here*/
		if( $type == 'latest' )
		{
			/*latest Layout*/
			$latest['text'] = array();
			$latest['text2'] = array();
			$latest['view_all'] = array();
			$latest['ads']  = array();
			$data = array();
			$data['latest_ads'] = array();
			$data['is_show_latest'] = (isset( $carspotAPI['latest_on_home'] ) && $carspotAPI['latest_on_home'] == 1 ) ? true : false;
			if( isset( $carspotAPI['latest_on_home'] ) && $carspotAPI['latest_on_home'] == 1 )
			{
				$latestAdsCount = ( $carspotAPI['home_latest_posts_count'] != "" ) ?  $carspotAPI['home_latest_posts_count'] : 5; 
				$latestAdsviewallTitle = ( $carspotAPI['sb_home_latest_ads_title_view_all'] != "" ) ? $carspotAPI['sb_home_latest_ads_title_view_all'] : '';
				$latestAdsTitle  = (isset( $carspotAPI['sb_home_latest_ads_title'] ) ) ? $carspotAPI['sb_home_latest_ads_title'] : '';
				$str               =  $latestAdsTitle;
                $latestAdsTitle  =  (explode("|",$str));
				$latest_termID = ( isset( $json_data['ad_cats1'] ) && $json_data['ad_cats1'] != ""  ) ? $json_data['ad_cats1'] : '';
				$latestAds = carspotApi_featuredAds_slider( '', 'active', '', $latestAdsCount, $latest_termID, 'publish');	
				if( isset( $latestAds ) && count( $latestAds ) > 0 )
				{		
					$latest['text']  = (isset( $latestAdsTitle[0] ) ) ? $latestAdsTitle[0]  : '';
					$latest['text2'] = (isset( $latestAdsTitle[1] ) ) ? $latestAdsTitle[1]  : '';
					$latest['view_all'] = $latestAdsviewallTitle;
					$latest['ads']  = $latestAds;
					$data['is_show_latest'] = true;
				}
				else
				{
					$data['is_show_latest'] = false;	
				}
				
				$data['latest_ads'] = $latest;
			}
			
			return $data;	
		}
		/*Latest Ads Ends Here*/

		/*Cat locations*/
		if( $type == 'locations' )
		{
			$loctData = array();
			if( isset( $carspotAPI['carspot-api-ad-loc-multi'] ) && count($carspotAPI['carspot-api-ad-loc-multi']) > 0 )
			{
				$loctData = array();
				$cats = $carspotAPI['carspot-api-ad-loc-multi'];
				if( count( $cats ) > 0 )
				{
					foreach($cats as $cat)
					{
						$term = get_term( $cat, 'ad_country' ); 
						if($term)
						{
							$name = htmlspecialchars_decode($term->name, ENT_NOQUOTES);
							$imgUrl = carspotAPI_taxonomy_image_url( $cat, NULL, TRUE );
							$count  = "($term->count ".  __("Ads", "carspot-rest-api").")";
							$loctData[] = array("loc_id" => $term->term_id, "name" => $name, "img" => $imgUrl, "count" => $count);
						}
					}					
				}
			}
			return $loctData;
		}
		/*Cat locations ends here */
		/*blogNews starts */
		
		if( $type == 'blogNews' )
		{
			/*latest Layout*/
			$blogs['text'] = array();
			$blogs['blogs']  = array();
			$data = array();
			$data['latest_blog'] = array();
			$data['is_show_blog'] = (isset( $carspotAPI['posts_blogNews_home'] ) && $carspotAPI['posts_blogNews_home'] ) ? true : false;
			if( isset( $carspotAPI['posts_blogNews_home'] ) && $carspotAPI['posts_blogNews_home']  )
			{
				$latestPostsCount = ( $carspotAPI['home_blogNews_posts_count'] != "" ) ?  $carspotAPI['home_blogNews_posts_count'] : 5; 
				$latestPostsAdsTitle = ( $carspotAPI['api_blogNews_title'] != "" ) ? $carspotAPI['api_blogNews_title'] : __("Blog/News", "carspot-rest-api");
		
				$latest_termID = ( isset( $carspotAPI['carspot-api-blogNews-multi'] ) && $carspotAPI['carspot-api-blogNews-multi'] ) ? $carspotAPI['carspot-api-blogNews-multi'] : array();
				
				$latestPosts = carspotAPI_blogPosts($latest_termID, $latestPostsCount);
				if( isset( $latestPosts ) && count( $latestPosts ) > 0 )
				{		
					$blogs['text'] = $latestPostsAdsTitle;
					$blogs['blogs']  = $latestPosts;
					$data['is_show_blog'] = true;
				}
				else
				{
					$data['is_show_blog'] = false;	
				}				
				$data['latest_blog'] = $blogs;
			}
			
			return $data;	
		}
		/*blogNews ends */
	}
}


if (!function_exists('carspotAPI_blogPosts'))
{
	function carspotAPI_blogPosts($cats = array(), $latestPostsCount = 5 )
	{
		
		
		$trmArry = array();
		if(count($cats) > 0 )
		{
			$trmArry  =  $cats;//array('taxonomy' => 'category', 'field' => 'id',  'terms' => $cats);
		}
					
					$posts_per_page = $latestPostsCount;					
					$args = array(
						'post_type' => 'post',
						'post_status' => 'publish', 
						'posts_per_page' => $posts_per_page, 
						'order'=> 'DESC', 
						'orderby' => 'date' ,
						'cat' => $trmArry
						
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
							$arr['details'] = get_the_excerpt();
							$arr['date'] =  get_the_date("", $post_id);
							
							$list = array();
							$term_lists = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'all'  ) );
							foreach($term_lists as $term_list)  $list[] = array('id' => $term_list->term_id, 'name' => $term_list->name );
							$arr['cats'] = $list;
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
					
					
				return $post_data;
	
		
	}
}
if (!function_exists('carspotAPI_appMenu_settings'))
{
	function carspotAPI_appMenu_settings()
	{		
		global $carspotAPI;
		$is_show_message_count = ( isset($carspotAPI['api-menu-message-count'] ) && $carspotAPI['api-menu-message-count'] ) ? true : false;
		$number_of_messages = '';
		if( $is_show_message_count)
		{
			$number_of_messages = ' ('.carspotAPI_getUnreadMessageCount().')';
		}
		
		$data_menu = array();
		
		$data_menu['menu_is_show_packages'] 	= ( isset( $carspotAPI['api_woo_products_multi'] ) && count($carspotAPI['api_woo_products_multi']) > 0 ) ? true : false;
		
		
		$data_menu['is_show_menu']['blog'] = (isset( $carspotAPI['api-menu-hide-blog-menu']) && $carspotAPI['api-menu-hide-blog-menu']) ? true : false;
		$data_menu['is_show_menu']['message'] = (isset( $carspotAPI['api-menu-hide-message-menu']) && $carspotAPI['api-menu-hide-message-menu']) ? true : false;
		$data_menu['is_show_menu']['package'] = (isset( $carspotAPI['api-menu-hide-package-menu']) && $carspotAPI['api-menu-hide-package-menu']) ? true : false;
	
		if( isset($carspotAPI['api-sortable-app-switch'] ) && $carspotAPI['api-sortable-app-switch'] )
		{
			$menus = $carspotAPI['api-sortable-app-menu'];
			foreach( $menus as $m_key => $m_val )
			{
				if( $m_key != "pages" )
				{
					$append_text = ( $m_key == "messages" ) ? $number_of_messages : '';
					$data_menu[$m_key] 			=  $m_val . $append_text;
				}
				else
				{
					$pages = '';
					$data_menu['submenu']['has_page'] = false;
					$data_menu['submenu']['title'] =  $m_val;
					if( isset( $carspotAPI['app_settings_pages'] ) )
					{
						$pages = $carspotAPI['app_settings_pages'];
						$data_menu['submenu']['has_page'] = true;
						$count = 1;
						$subMenus = array();
						foreach($pages as $page)
						{
							$title 	= carspotAPI_convert_uniText( get_the_title( $page ));
							$subMenus[] = array("page_id" => (int)$page,"page_title" => $title, "icon" => $count);
							/*0 help,1 about ,2 terms ,3 page*/
							$count++;
						}
						
						$data_menu['submenu']['pages'] = $subMenus;
					}				
				}
			}
			
		}
		else
		{
			$data_menu['home'] 			=  __("Home", "carspot-rest-api");
			$data_menu['profile'] 		=  __("Profile", "carspot-rest-api");
			$data_menu['search'] 		=  __("Advance Search", "carspot-rest-api");
			$data_menu['messages'] 		=  __("Messages", "carspot-rest-api") . $number_of_messages;
			$data_menu['my_ads'] 		=  __("My Ads", "carspot-rest-api");
			$data_menu['inactive_ads'] 	=  __("Inactive Ads", "carspot-rest-api");
			$data_menu['featured_ads'] 	=  __("Featured Ads", "carspot-rest-api");
			$data_menu['fav_ads'] 		=  __("Fav Ads", "carspot-rest-api");
			$data_menu['packages'] 		=  __("Packages", "carspot-rest-api");		
	
			$pages = '';
			$data_menu['submenu']['has_page'] = false;
			$data_menu['submenu']['title'] =  __("Pages", "carspot-rest-api");
			if( isset( $carspotAPI['app_settings_pages'] ) )
			{
				$pages = $carspotAPI['app_settings_pages'];
				$data_menu['submenu']['has_page'] = true;
				$count = 1;
				$subMenus = array();
				foreach($pages as $page)
				{
					$title 	= get_the_title( $page );
					$subMenus[] = array("page_id" => (int)$page,"page_title" => $title, "icon" => $count);
					/*0 help,1 about ,2 terms ,3 page*/
					$count++;
				}
				
				$data_menu['submenu']['pages'] = $subMenus;
			}
			
			$data_menu['others'] =  	__("Others", "carspot-rest-api");
			$data_menu['blog'] =  	__("Blog", "carspot-rest-api");
			$data_menu['logout'] =  	__("Logout", "carspot-rest-api");
	
			$data_menu['login']   =  	__("Login", "carspot-rest-api");
			$data_menu['register'] =  	__("Register", "carspot-rest-api");				

		}
		return $data_menu;
	}
}



if (!function_exists('carspotAPI_settings_data'))
{
	function carspotAPI_settings_data($user_id)
	{
		global $carspotAPI;
		/*Some App Keys From Theme Options */
		$data['appKey']['stripe']		= (isset( $carspotAPI['appKey_stripeKey'] ) ) ? $carspotAPI['appKey_stripeKey'] : '';		
		$data['appKey']['paypal']		= (isset( $carspotAPI['appKey_paypalKey'] ) ) ? $carspotAPI['appKey_paypalKey'] : '';
		$data['appKey']['youtube']		= (isset( $carspotAPI['appKey_youtubeKey'] ) ) ? $carspotAPI['appKey_youtubeKey'] : '';
		$data['ads']['show'] = false;
		
		$data['appKey']['payu']['mode']		= (isset( $carspotAPI['appKey_payuMode'] ) ) ? $carspotAPI['appKey_payuMode'] : 'sandbox';
		$data['appKey']['payu']['key']		= (isset( $carspotAPI['appKey_payumarchantKey'] ) ) ? $carspotAPI['appKey_payumarchantKey'] : '';
		$data['appKey']['payu']['salt']		= (isset( $carspotAPI['payu_salt_id'] ) ) ? $carspotAPI['payu_salt_id'] : '';
		
		 if(isset( $carspotAPI['api_ad_show'] ) && $carspotAPI['api_ad_show'] == true )
		 {
			$data['ads']['show'] 	 = true; 
			$data['ads']['type'] 	 = 'banner'; 
			$is_show_banner = (isset($carspotAPI['api_ad_type_banner']) && $carspotAPI['api_ad_type_banner']) ? true :false;
			$data['ads']['is_show_banner'] 	 = $is_show_banner; 
			if($is_show_banner)
			{
			  $ad_position = (isset($carspotAPI['api_ad_position'] ) && $carspotAPI['api_ad_position'] != "") ? $carspotAPI['api_ad_position'] : 'top';
			  $data['ads']['position'] = $ad_position;
			  /*For New Version > 1.5.0*/
			  
			  $api_ad_key_banner = ( carspot_API_REQUEST_FROM == 'ios' ) ? $carspotAPI['api_ad_key_banner'] : $carspotAPI['api_ad_key_banner_ios'];
			  $data['ads']['banner_id'] = $api_ad_key_banner;	
			}
			
			$is_show_initial = (isset($carspotAPI['api_ad_type_initial']) && $carspotAPI['api_ad_type_initial']) ? true :false;	
					
			$api_ad_key_var = ( carspot_API_REQUEST_FROM == 'ios' ) ? $carspotAPI['api_ad_key_ios'] : $carspotAPI['api_ad_key'];
			$data['ads']['is_show_initial'] = $is_show_initial; 			
			if($is_show_initial)
			{
				$data['ads']['time_initial'] 	= ($carspotAPI['api_ad_time_initial'] != "" ) ? $carspotAPI['api_ad_time_initial'] : 30;  
				$data['ads']['time'] 	 		= ($carspotAPI['api_ad_time'] != "" ) ? $carspotAPI['api_ad_time'] : 30;
				/*For New Version > 1.5.0*/
				$data['ads']['interstital_id'] = $api_ad_key_var;	
			}
			$data['ads']['ad_id'] = $api_ad_key_var;			
		 }
		
		$data['analytics']['show'] = false;
		if(isset( $carspotAPI['api_analytics_show'] ) && $carspotAPI['api_analytics_show'] == true )
		{
			$data['analytics']['show'] = true;
			$data['analytics']['id']   = ($carspotAPI['api_analytics_id'] != "" ) ? $carspotAPI['api_analytics_id'] : '';
		}
		//$f_reg_id = '';
		$f_reg_id  = get_user_meta($user_id, '_sb_user_firebase_id', true );
		$data['firebase']['reg_id'] = ( $f_reg_id != "" ) ? $f_reg_id : '';
				
		return $data;
		
	}
}


add_action( 'rest_api_init', 'carspotAPI_homescreen_api_hooks_post', 0 );
function carspotAPI_homescreen_api_hooks_post() {

    register_rest_route( 'carspot/v1', '/home/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_homeScreen_post',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
}

if (!function_exists('carspotAPI_homeScreen_post'))
{
	function carspotAPI_homeScreen_post( $request )
	{

		$user = wp_get_current_user();	
		
		$user_id = @$user->data->ID;		

		$json_data	= $request->get_json_params();	
		$firebase_id	= (isset($json_data['firebase_id'])) ? $json_data['firebase_id'] : '';
		

		$isUpdated = update_user_meta($user_id, '_sb_user_firebase_id', $firebase_id );		
		$f_reg_id  = get_user_meta($user_id, '_sb_user_firebase_id', true );
		$data['firebase_reg_id'] = ( $f_reg_id != "" ) ? $f_reg_id : '';				
		$data['user_id'] = $user_id;
		return $response = array( 'success' => true, 'data' => $data, 'message'  => ''  );
		
	}
}

add_action( 'rest_api_init', 'carspotAPI_page_api_hooks_get', 0 );
function carspotAPI_page_api_hooks_get() {

    register_rest_route( 'carspot/v1', '/page/',
        array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_page_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );
	
    register_rest_route( 'carspot/v1', '/page/',
        array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => 'carspotAPI_page_get',
				'permission_callback' => function () {  return carspotAPI_basic_auth();  },
        	)
    );	
}

if (!function_exists('carspotAPI_page_get'))
{
	function carspotAPI_page_get( $request )
	{
		$json_data = $request->get_json_params();
		$page_id = (isset( $json_data['page_id'] ) ) ?  $json_data['page_id'] : '';		
		$post_content = get_post($page_id);
		$content = $post_content->post_content;
		$data['page_title'] = get_the_title($page_id);
		$data['page_content'] =  do_shortcode($content);
		return $response = array( 'success' => true, 'data' => $data, 'message'  => ''  );
	}	
}