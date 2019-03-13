<?php
/*----- 	Woo Products Starts Here	 -----*/
add_action( 'rest_api_init', 'carspotAPI_packages_get_hook', 0 );
function carspotAPI_packages_get_hook() {
    register_rest_route(
        		'carspot/v1', '/packages/', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_packages_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
} 

if (!function_exists('carspotAPI_packages_get'))
{
	function carspotAPI_packages_get( $request )
	{ 
		$user = wp_get_current_user();	
		$user_id = @$user->data->ID;		
		$pdata = array();
		$products = array();
		global $carspotAPI;
		$message = '';
		$success = true;
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
		
		if( isset( $carspotAPI['api_woo_products_multi'] )  )
		{
			
			$productsData = $carspotAPI['api_woo_products_multi'];
			if( count( $productsData ) > 0 )
			{
				foreach( $productsData as $product )
				{

					$productData	=	new WC_Product( $product );
					$unlimited_text = __('Unlimited','carspot-rest-api');
					$pdata['color'] = 'light';
					if( get_post_meta( $product, 'package_bg_color', true ) == 'dark' )
						$pdata['color'] = 'dark';
						
					
					$pdata['days_text'] = __('Validity','carspot-rest-api');
					$pdata['days_value'] = '0';
					
					if( get_post_meta( $product, 'package_expiry_days', true ) == "-1" )
					{
						$pdata['days_value'] = __('Lifetime','carspot-rest-api');
					}
					else if( get_post_meta( $product, 'package_expiry_days', true ) != "" )
					{
						$pdata['days_value'] = get_post_meta( $product, 'package_expiry_days', true ) .' '. __('Days','carspot-rest-api');						
					}
					
					$pdata['free_ads_text'] = __('Free Ads','carspot-rest-api');
					$pdata['free_ads_value'] = '0';					
					if( get_post_meta( $product, 'package_free_ads', true ) != "" )
					{
						
						if(get_post_meta( $product, 'package_free_ads', true ) == '-1' )
						{
							$freeValue = $unlimited_text;
						}
						else
						{
							$freeValue = get_post_meta( $product, 'package_free_ads', true );
						}							
						
						$pdata['free_ads_value'] = $freeValue;
						
					}

					$pdata['featured_ads_text'] = __('Featured Ads','carspot-rest-api');
					$pdata['featured_ads_value'] = '0';					
					if( get_post_meta( $product, 'package_featured_ads', true ) != "" )
					{
						
						if(get_post_meta( $product, 'package_featured_ads', true ) == '-1' )
						{
							$fValue = $unlimited_text;
						}
						else
						{
							$fValue = get_post_meta( $product, 'package_featured_ads', true );
						}
						$pdata['featured_ads_value'] = $fValue;
					}
					
					$pdata['bump_ads_text'] = __('Bump up Ads','carspot-rest-api');
					$pdata['bump_ads_value'] = '0';					
					if( get_post_meta( $product, 'package_bump_ads', true ) != "" )
					{
						if(get_post_meta( $product, 'package_bump_ads', true ) == '-1' )
						{
							$bValue = $unlimited_text;
						}
						else
						{
							$bValue = get_post_meta( $product, 'package_bump_ads', true );
						}
						
						$pdata['bump_ads_value'] = $bValue;
					}						
					
					
					$pdata['product_id'] 	= 	$product ;
					$pdata['product_title'] = 	get_the_title( $product );
					$pdata['product_desc']  = 	$productData->description;
					$pdata['product_price'] = 	html_entity_decode(strip_tags(wc_price($productData->get_price())));
					
					$pdata['product_amount']['value'] = html_entity_decode(strip_tags($productData->get_price()));
					$pdata['product_amount']['currency'] = html_entity_decode(strip_tags(wc_price($productData->get_price())));
					$pdata['product_amount']['symbol'] = html_entity_decode(strip_tags(get_woocommerce_currency_symbol()));
					
					$pdata['product_link']  = get_the_permalink( $product );
					$pdata['product_qty']   = 1;
					
					$pdata['product_btn']   = __('Select Plan','carspot-rest-api');
					$pdata['payment_types_value']   = __('Select Option','carspot-rest-api');
							
					/*Get Android and IOS Product code Starts*/						
					$pdata['product_appCode']['android']   = get_post_meta( $product, 'package_product_code_android', true );
					$pdata['product_appCode']['ios']   	   = get_post_meta( $product, 'package_product_code_ios', true );
					$pdata['product_appCode']['message']   = __('InApp purchase not available for this product.','carspot-rest-api');
					/*Get Android and IOS Product code Ends*/	
								
					$products[] = $pdata;	
				}				
			}
			else
			{
				$success = false;
				$message = __("No Product Found", "carspot-rest-api");
			}				
			
		}
		else
		{
			$success = false;
			$message = __("No Product Found", "carspot-rest-api");
		}			
		}
		else
		{
			$success = false;
			$message = __("No Product Found", "carspot-rest-api");
		}
		$data["products"]  =  $products;
		
		$methods = array();
		$methods[] =  array("key" => "", "value" =>__( 'Select Option', 'carspot-rest-api' ));
		
		if( CARSPOT_API_REQUEST_FROM == 'ios' )
		{
			$paymentPackages  = ( isset( $carspotAPI['api-payment-packages-ios'] ) && count($carspotAPI['api-payment-packages-ios']) > 0   )  ? $carspotAPI['api-payment-packages-ios'] : array();
		}
		else
		{
			$paymentPackages  = ( isset( $carspotAPI['api-payment-packages'] ) && count($carspotAPI['api-payment-packages']) > 0   )  ? $carspotAPI['api-payment-packages'] : array();
		}
		
		
		if( isset($paymentPackages) && count($paymentPackages) > 0   ) 
		{
			foreach( $paymentPackages as $type )
			{
				$name = carspotAPI_payment_types($type);
				if( $name != "" )
				{
					$methods[] =  array("key" => $type, "value" => $name  );
				}
				//$methods[$type] = carspotAPI_payment_types($type);
			}
		}
		
		
		
		$data["payment_types"]  =  $methods;
		$extra["page_title"]  =  __('Packages','carspot-rest-api');
	
		$extra["billing_error"]  =  __('something went wrong while billing your account.','carspot-rest-api');
		/* Paypal Account Currency Settings Starts */
		$paypalKey = ( isset( $carspotAPI['appKey_paypalKey'] ) && $carspotAPI['appKey_paypalKey'] != "" ) ? $carspotAPI['appKey_paypalKey'] : '';
		$merchant_name = ( isset( $carspotAPI['paypalKey_merchant_name'] ) && $carspotAPI['paypalKey_merchant_name'] != "" ) ? $carspotAPI['paypalKey_merchant_name'] : '';
		
		$paypal_currency = ( isset( $carspotAPI['paypalKey_currency'] ) && $carspotAPI['paypalKey_currency'] != "" ) ? $carspotAPI['paypalKey_currency'] : '';
		$privecy_url = ( isset( $carspotAPI['paypalKey_privecy_url'] ) && $carspotAPI['paypalKey_privecy_url'] != "" ) ? $carspotAPI['paypalKey_privecy_url'] : '';
		$agreement_url = ( isset( $carspotAPI['paypalKey_agreement'] ) && $carspotAPI['paypalKey_agreement'] != "" ) ? $carspotAPI['paypalKey_agreement'] : '';
		
		$appKey_paypalMode = ( isset( $carspotAPI['appKey_paypalMode'] ) && $carspotAPI['appKey_paypalMode'] != "" ) ? $carspotAPI['appKey_paypalMode'] : 'live';
		
		$has_key = ( $paypalKey == "" ) ? false : true;
		$data["is_paypal_key"] = $has_key;
		if( $has_key == true )
		{
			$data["paypal"]["mode"] 		= $appKey_paypalMode;
			$data["paypal"]["api_key"] 		= $paypalKey;
			$data["paypal"]["merchant_name"] 	= $merchant_name;
			$data["paypal"]["currency"] 		= $paypal_currency;
			$data["paypal"]["privecy_url"] 	= $privecy_url;
			$data["paypal"]["agreement_url"] 	= $agreement_url;
		}
		
			
		/*Android All InApp Settings */
		$inappAndroid = (isset( $carspotAPI['inApp_androidSecret'] ) &&  $carspotAPI['inApp_androidSecret'] != "" ) ? $carspotAPI['inApp_androidSecret'] : '';
		
		$inappAndroid_on = (isset( $carspotAPI['api-inapp-android-app'] ) &&  $carspotAPI['api-inapp-android-app'] ) ? true : false;
		
		$extra['android']['title_text'] 		  = __('InApp Purchases','carspot-rest-api');
		$extra['android']['in_app_on'] 		  	  = $inappAndroid_on;
		$extra['android']['secret_code'] 		  = $inappAndroid; /*Secret code*/
		$extra['android']['message']['no_market'] = __('Play Market app is not installed.','carspot-rest-api');
		$extra['android']['message']['one_time']  = __('One Time Purchase not Supported on your Device.','carspot-rest-api');
		
		
		/*IOS All InApp Settings */
		$inappIos = (isset( $carspotAPI['inApp_iosSecret'] ) &&  $carspotAPI['inApp_iosSecret'] != "" ) ? $carspotAPI['inApp_iosSecret'] : '';
		$iosInApp_on = (isset( $carspotAPI['api-inapp-ios-app'] ) &&  $carspotAPI['api-inapp-ios-app'] ) ? true : false;
		
		$extra['ios']['title_text'] 		 = __('InApp Purchases','carspot-rest-api');
		$extra['ios']['in_app_on'] 		  	 = $iosInApp_on;
		$extra['ios']['secret_code'] 		 = $inappIos; /*Secret code*/		
		
		$stripe_publish_key = ( isset( $carspotAPI['appKey_stripe_publishKey'] ) && $carspotAPI['appKey_stripe_publishKey'] != "" ) ? $carspotAPI['appKey_stripe_publishKey'] : '';
		
		$stripe_secret_key = ( isset( $carspotAPI['appKey_stripeSKey'] ) && $carspotAPI['appKey_stripeSKey'] != "" ) ? $carspotAPI['appKey_stripeSKey'] : '';
		
		$extra['android']['stripe_publish_key'] 		  = $stripe_publish_key; 
		$extra['android']['stripe_secret_key'] 		      = $stripe_secret_key; 
		
		/* Paypal Account Currency Settings Ends */		
		$response = array( 'success' => $success, 'data' => $data, 'message' => $message, 'extra' => $extra );
		return $response;
		
	}
}

/*Whern Order Completed By Admin starts */
 	$carspot_theme = wp_get_theme();
	if( $carspot_theme->get( 'Name' ) != 'carspot' && $carspot_theme->get( 'Name' ) != 'carspot child' )
	{
		add_action( 'woocommerce_order_status_completed', 'carspotAPI_after_payment' );
	}
	if ( ! function_exists( 'carspotAPI_after_payment' ) ) {
	function carspotAPI_after_payment( $order_id )
	{
		$order = new WC_Order( $order_id );
		/* Get user Id From Order */		
		$uid		=	get_post_meta( $order_id, '_customer_user', true );
		$items = $order->get_items();
		foreach ( $items as $item )
		{
			$product_id 	= $item['product_id'];
			$ads			=	get_post_meta( $product_id, 'package_free_ads', true );
			$featured_ads	=	get_post_meta( $product_id, 'package_featured_ads', true );
			$bump_ads 		= get_post_meta( $product_id, 'package_bump_ads', true );
			$days			=	get_post_meta( $product_id, 'package_expiry_days', true );
			
			update_user_meta( $uid, '_sb_pkg_type', get_the_title( $product_id ) );
			if( $ads == '-1' )
			{
				update_user_meta( $uid, '_sb_simple_ads', '-1' );
			}
			else if( is_numeric( $ads ) &&  $ads != 0 )
			{
				$simple_ads	=	get_user_meta( $uid, '_sb_simple_ads', true );
				if( $simple_ads != '-1' )
				{
					$simple_ads	=	 $simple_ads;
					$new_ads	=	$ads + $simple_ads;
					update_user_meta( $uid, '_sb_simple_ads', $new_ads );
				}
				else if( $simple_ads == '-1' )
				{
					update_user_meta( $uid, '_sb_simple_ads', $ads );
				}
			}
			if( $featured_ads == '-1' )
			{
				update_user_meta( $uid, '_sb_featured_ads', '-1' );	
			}
			else if( is_numeric( $featured_ads ) &&  $featured_ads != 0 )
			{
				$f_ads	=	get_user_meta( $uid, '_sb_featured_ads', true );
				if( $f_ads != '-1' )
				{
					$f_ads	=	 (int)$f_ads;
					$new_f_fads	=	$featured_ads + $f_ads;
					update_user_meta( $uid, '_sb_featured_ads', $new_f_fads );
				}
				else if( $f_ads == '-1' )
				{
					update_user_meta( $uid, '_sb_featured_ads', $featured_ads );
				}
			}
			
			if( $bump_ads == '-1' )
			{
				update_user_meta( $uid, '_sb_bump_ads', '-1' ); 
			}
			else if( is_numeric( $bump_ads ) &&  $bump_ads != 0 )
			{
				$b_ads = get_user_meta( $uid, '_sb_bump_ads', true );
				if( $b_ads != '-1' )
				{
					$b_ads =  (int)$b_ads;
					$new_b_fads = $bump_ads + $b_ads;
					update_user_meta( $uid, '_sb_bump_ads', $new_b_fads );
				}
				else if( $b_ads == '-1' )
				{
					update_user_meta( $uid, '_sb_bump_ads', $bump_ads );
				}
			}			
			
			if( $days == '-1' )
			{
				update_user_meta( $uid, '_sb_expire_ads', '-1' );
			}
			else
			{
				$expiry_date	=	get_user_meta( $uid, '_sb_expire_ads', true );
				$e_date	=	strtotime( $expiry_date );	
				$today	=	strtotime( date( 'Y-m-d') );
				if( $today > $e_date )
				{
					$new_expiry	=	date('Y-m-d', strtotime("+$days days"));
				}
				else
				{
					$date	=	date_create( $expiry_date );
					date_add($date,date_interval_create_from_date_string("$days days"));
					$new_expiry	=	 date_format($date,"Y-m-d");
				}
				update_user_meta( $uid, '_sb_expire_ads', $new_expiry );				
			}
		}		
			
	}
}
/*Whern Order Completed By Admin starts */
/*Added Meta For The Android InApp Purchase*/
add_action( 'add_meta_boxes', 'carspotAPI_andrid_product_key_hook' );
function carspotAPI_andrid_product_key_hook()
{
    add_meta_box( 'carspotAPI_metaboxes_product_android_ios', __('InApp Purchase Settings For Android and IOS Apps','carspot-rest-api' ), 'carspotAPI_andrid_product_key_func', 'product', 'normal', 'high' );
}
if (!function_exists('carspotAPI_andrid_product_key_func'))
{
	function carspotAPI_andrid_product_key_func( $post )
	{
		wp_nonce_field( 'carspotAPI_metaboxes_product_android_ios', 'meta_box_nonce_product' );
		?>
			<div>
			<p><?php echo __('Android Product Code','carspot-rest-api' ); ?></p>
				<input type="text" name="package_product_code_android" class="project_meta" placeholder="<?php echo esc_attr__('Enter you android product code here.', 'carspot-rest-api' ); ?>" size="30" value="<?php echo esc_attr( get_post_meta($post->ID, "package_product_code_android", true) ); ?>" id="package_product_code_android" spellcheck="true" autocomplete="off">
		<div><?php echo __( "Please enter product code for the andrid product. Leave empty if you dont't have any. Only enter in case you have bought android app.", 'carspot-rest-api' ); ?></div>
			</div>
			<div>
			<p><?php echo __('IOS Product Code','carspot-rest-api' ); ?></p>
				<input type="text" name="package_product_code_ios" class="project_meta" placeholder="<?php echo esc_attr__('Enter you ios product code here.', 'carspot-rest-api' ); ?>" size="30" value="<?php echo esc_attr( get_post_meta($post->ID, "package_product_code_ios", true) ); ?>" id="package_product_code_ios" spellcheck="true" autocomplete="off">
				<div><?php echo __( "Please enter product code for the andrid product. Leave empty if you dont't have any. Only enter in case you have bought ios app.", 'carspot-rest-api' ); ?></div>
			</div>  
            
            <p><strong>*<?php echo __( "Please make sure you have created the **** product while create packages in AppStore/PlayStore accounts.", 'carspot-rest-api' ); ?></strong></p>      
		<?php
		
	}
}

add_action( 'save_post', 'carspotAPI_save_appProduct_ids' );
if (!function_exists('carspotAPI_save_appProduct_ids'))
{
	function carspotAPI_save_appProduct_ids( $post_id )
	{

	  	/*Bail if we're doing an auto save*/
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		 
		/*if our nonce isn't there, or we can't verify it, bail*/
		if( !isset( $_POST['meta_box_nonce_product'] ) || !wp_verify_nonce( $_POST['meta_box_nonce_product'], 'my_meta_box_nonce_product' ) ) return;
		 
		/*if our current user can't edit this post, bail*/
		if( !current_user_can( 'edit_post' ) ) return;
		
		/*Make sure your data is set before trying to save it*/
		if( isset( $_POST['package_product_code_android'] ) ){
			update_post_meta( $post_id, 'package_product_code_android', $_POST['package_product_code_android'] );		
		}
		else
		{
			update_post_meta( $post_id, 'package_product_code_android', '' );
		}
		/*For IOS */
		if( isset( $_POST['package_product_code_ios'] ) ){
			update_post_meta( $post_id, 'package_product_code_ios', $_POST['package_product_code_ios'] );		
		}
		else
		{
			update_post_meta( $post_id, 'package_product_code_ios', '' );
		}		
	}
}