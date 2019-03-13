<?php
 /*Theme Options For Carspot WordPress API Theme*/   
if ( ! class_exists( 'Redux' ) )  return;
$opt_name = "carspotAPI";
$theme = wp_get_theme();
$args = array(
	'opt_name' => 'carspotAPI',
	'dev_mode' => false,
	'display_name' => __( 'Carspot Apps API Options', "carspot-rest-api" ),
	'display_version' => '1.0.0',
	'page_title' => __( 'Carspot Apps API Options', "carspot-rest-api" ),
	'update_notice' => TRUE,
	'admin_bar' => TRUE,
	'menu_type' => 'submenu',
	'menu_title' => __( 'Apps API Options', "carspot-rest-api" ),
	'allow_sub_menu' => TRUE,
	'page_parent_post_type' => 'your_post_type',
	'customizer' => TRUE,
	'default_show' => TRUE,
	'default_mark' => '*',
	'hints' => array( 'icon_position' => 'right', 'icon_size' => 'normal', 'tip_style' => array('color' => 'light', ),
	'tip_position' => array( 'my' => 'top left', 'at' => 'bottom right',  ),
	'tip_effect' => array( 'show' => array( 'duration' => '500', 'event' => 'mouseover', ),
	'hide' => array( 'duration' => '500', 'event' => 'mouseleave unfocus',  ),),
 ),
	'output' => TRUE,
	'output_tag' => TRUE,
	'settings_api' => TRUE,
	'cdn_check_time' => '1440',
	'compiler' => TRUE,
	'global_variable' => 'carspotAPI',
	'page_permissions' => 'manage_options',
	'save_defaults' => TRUE,
	'show_import_export' => TRUE,
	'database' => 'options',
	'transient_time' => '3600',
	'network_sites' => TRUE,
);



    $args['share_icons'][] = array(
        'url'   => 'https://www.facebook.com/scriptsbundle',
        'title' => __( 'Like us on Facebook', "carspot-rest-api" ),
        'icon'  => 'el el-facebook'
    );

    Redux::setArgs( $opt_name, $args );
	
	/* ------------------ App Settings ----------------------- */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'App Settings', "carspot-rest-api" ),
        'id'         => 'api_app_settings',
        'desc'       => '',
        'icon' => 'el el-cogs',
        'fields'     => array(

			array(
                'id'       => 'app_is_open',
                'type'     => 'switch',
                'title'    => __( 'Make App Open', "carspot-rest-api" ),
				'desc'     => __( 'Make App Open For Public', "carspot-rest-api" ),
                'default'  => false,
            ),
			
            array(
                'id'       => 'app_logo',
                'type'     => 'media',
                'url'      => true,
                'title'    => __( 'Logo', 'carspot-rest-api' ),
                'compiler' => 'true',
                'desc'     => __( 'Site Logo image for the site.', 'carspot-rest-api' ),
                'subtitle' => __( 'Dimensions: 230 x 40', 'carspot-rest-api' ),
                'default'  => array( 'url' => CARSPOT_API_PLUGIN_URL."images/logo.png" ),
            ),			
			
			array(
                'id'       => 'app_settings_rtl',
                'type'     => 'switch',
                'title'    => __( 'RTL', "carspot-rest-api" ),
				'desc'     => __( 'Make app RTL', "carspot-rest-api" ),
                'default'  => false,
            ),
			array(
                'id'       => 'gmap_lang',
                'type'     => 'text',
                'title'    => __( 'App Language', 'carspot-rest-api' ),
				'desc' => carspotAPI_make_link ( 'https://developers.google.com/maps/faq#languagesupport' , __( 'List of available languages.' , 'carspot-rest-api' ) ). __( 'If you have selected RTL put language code here like for arabic ar', "carspot-rest-api" ),
				'default'  => 'en',
            ),			
		
			
			array(
                'id'       => 'app_settings_color',
                'type'     => 'color',
				'transparent' => false,
                'title'    => __( 'Select App Colour', 'redux-framework-demo' ),
                'subtitle' => __( 'Pick a title color for the app (default: #fb236a).', 'nokri-rest-api' ),
                'default'  => '#f58936',
            ),
			
            array(
                'id'       => 'app_settings_pages',
                'type'     => 'select',
                'data'     => 'pages',			
                'multi'    => true,
				'sortable' => true,
                'title'    => __( 'Select Pages', 'carspot-rest-api' ),
            ),				


			
            array(
                'id'       => 'sb_user_dp',
                'type'     => 'media',
                'url'      => true,
                'title'    => __( 'Default user picture', 'carspot-rest-api' ),
                'compiler' => 'true',
                'subtitle' => __( 'Dimensions: 200 x 200', 'carspot-rest-api' ),
                'default'  => array( 'url' => CARSPOT_API_PLUGIN_URL."images/user.jpg" ),
            ),	
            array(
                'id'       => 'sb_user_guest_dp',
                'type'     => 'media',
                'url'      => true,
                'title'    => __( 'Default guest picture', 'carspot-rest-api' ),
                'compiler' => 'true',
                'subtitle' => __( 'Dimensions: 200 x 200', 'carspot-rest-api' ),
                'default'  => array( 'url' => CARSPOT_API_PLUGIN_URL."images/user.jpg" ),
            ),					

			array(
				'id'       => 'sb_location_type',
				'type'     => 'button_set',
				'title'    => __( 'Address Type', 'carspot-rest-api' ),
				'options'  => array(
					'cities' => __('Cities', 'carspot-rest-api' ),
					'regions' => __('Adresses', 'carspot-rest-api' ),
				),
				'default'  => 'cities'
			),
			array(
                'id'       => 'app_settings_message_firebase',
                'type'     => 'switch',
                'title'    => __( 'Message Settings', "carspot-rest-api" ),
				'desc'     => __( 'Send message notification through firebase when receive new message on ad.', "carspot-rest-api" ),
                'default'  => true,
            ),	
			
			array(
                'id'       => 'app_settings_fb_btn',
                'type'     => 'switch',
                'title'    => __( 'Facebook Login/Register', "carspot-rest-api" ),
				'desc'     => __( 'Show or hide google button.', "carspot-rest-api" ),
                'default'  => true,
            ),				
			array(
                'id'       => 'app_settings_google_btn',
                'type'     => 'switch',
                'title'    => __( 'Google Login/Register', "carspot-rest-api" ),
				'desc'     => __( 'Show or hide google button.', "carspot-rest-api" ),
                'default'  => true,
            ),
			array(
                'id'       => 'app_register_tagline',
                'type'     => 'text',
                'title'    => __( 'Tagline', "carspot-rest-api" ),
				'desc'     => __( 'Show or hide register tagline.', "carspot-rest-api" ),
                'default'  => __( 'Over 25000 classified ads listing.', "carspot-rest-api" ),
            ),	
			array(
                'id'       => 'sb_enable_social_links',
                'type'     => 'switch',
                'title'    => __( 'Enable Social Profiles', 'carspot-rest-api' ),
                'subtitle' => __( 'for display', 'carspot-rest-api' ),
                'default'  => false,
            ),		
		
			array(
                'id'       => 'sb_enable_featured_slider_scroll',
                'type'     => 'switch',
                'title'    => __( 'Enable Scroll On Featured Ads', 'carspot-rest-api' ),
                'desc' => __( 'Turn on/off auto scroll on the featured ads slider', 'carspot-rest-api' ),
                'default'  => false,
            ),		
		
										
         	array(
                'id'       => 'sb_enable_featured_slider_duration',
                'type'     => 'text',
                'title'    => __( 'Slider Scroll Speed', 'carspot-rest-api' ),
				'default'  => 40,	
				'required' => array( 'sb_enable_featured_slider_scroll', '=', true ),		
				'desc'     => __( 'Enter value in milisecons 1000 is 1 second', 'carspot-rest-api' ),	
            ),	
         	array(
                'id'       => 'sb_enable_featured_slider_loop',
                'type'     => 'text',
                'title'    => __( 'Slider Loop scroll', 'carspot-rest-api' ),
				'default'  => 2000,	
				'required' => array( 'sb_enable_featured_slider_scroll', '=', true ),		
				'desc'     => __( 'Enter value in milisecons 1000 is 1 second. Once end starts again after X number of seconds', 'carspot-rest-api' ),	
            ),

			array(
                'id'       => 'allow_near_by',
                'type'     => 'switch',
                'title'    => __( 'Nearby Option', "carspot-rest-api" ),
				'desc'     => __( 'Turn on/off nearby option in app', "carspot-rest-api" ),
                'default'  => false,
            ),
	
	
			array(
                'id'       => 'allow_app_rating',
                'type'     => 'switch',
                'title'    => __( 'App Rating', "carspot-rest-api" ),
				'desc'     => __( 'Show app rating icon on the top.', "carspot-rest-api" ),
                'default'  => false,
            ),
	
         array(
                'id'       => 'allow_app_rating_title',
                'type'     => 'text',
                'title'    => __( 'Rating Title', 'carspot-rest-api' ),
				'default'  => __("App Store Rating", "carspot-rest-api"),	
				'required' => array( 'allow_app_rating', '=', '1' ),		
				'desc'     => __( 'Rating title in the popup.', 'carspot-rest-api' ),	
            ),	
         			
         array(
                'id'       => 'allow_app_rating_url',
                'type'     => 'text',
                'title'    => __( 'App URL', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( 'allow_app_rating', '=', '1' ),		
				'desc'     => __( 'Enter app URL for app rating. URL is required', 'carspot-rest-api' ),	
            ),	
			
			array(
                'id'       => 'allow_app_share',
                'type'     => 'switch',
                'title'    => __( 'App Share', "carspot-rest-api" ),
				'desc'     => __( 'Show app share icon on the top.', "carspot-rest-api" ),
                'default'  => false,
            ),
         array(
                'id'       => 'allow_app_share_title',
                'type'     => 'text',
                'title'    => __( 'Share Popup Title', 'carspot-rest-api' ),
				'default'  => __("Share this", "carspot-rest-api"),	
				'required' => array( 'allow_app_share', '=', '1' ),		
				'desc'     => __( 'title in the popup.', 'carspot-rest-api' ),	
            ),
         array(
                'id'       => 'allow_app_share_text',
                'type'     => 'text',
                'title'    => __( 'Subject', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( 'allow_app_share', '=', '1' ),		
				'desc'     => __( 'App share subject. Not required.', 'carspot-rest-api' ),	
            ),			
         array(
                'id'       => 'allow_app_share_url',
                'type'     => 'text',
                'title'    => __( 'App Share URL', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( 'allow_app_share', '=', '1' ),		
				'desc'     => __( 'Enter app share URL for app sharing. URL is required.', 'carspot-rest-api' ),	
            ),										 
											

		)
		) );
			
Redux::setSection( $opt_name, array(
        'title'      => __( 'App Key Settings', "carspot-rest-api" ),
        'id'         => 'api_key_settings',
        'desc'       => '',
        'icon' => 'el el-key',
        'fields'     => array(

            array(
                'id'     => 'api_key_settings-info1',
                'type'   => 'info',
                'notice' => false,
                'style'  => 'info',
                'title' => __( 'Alert', 'carspot-rest-api' ),
                'desc'  => __( 'Once added be carefull editing next time. Those Key Should Be Same In App Header.', 'carspot-rest-api' )
            ),			


            array(
                'id'     => 'api_key_settings-info1-1',
                'type'   => 'info',
                'notice' => false,
                'style'  => 'info',
                'title' => __( 'Info', 'carspot-rest-api' ),
                'desc'  => __( 'Below section is only for if you have purchased Android App. Then turn it on and enter the purchase code in below text field that will appears.', 'carspot-rest-api' )
            ),	
			array(
                'id'       => 'api-is-buy-android-app',
                'type'     => 'switch',
                'title'    => __( 'For Android App', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'If you have purchased the android app.', 'carspot-rest-api' ),
            ),	
						
         array(
		 		'required' 		=> array( 'api-is-buy-android-app', '=', true ),
                'id'       => 'appKey_pCode',
                'type'     => 'text',
                'title'    => __( 'Enter You Android Purchase Code Here', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'Your android item purchase code got from codecanyon. You have purchased the item seprately.', 'carspot-rest-api' ),
                'text_hint' => array(
                    'title'   => __( 'Alert', 'carspot-rest-api' ),
                    'content' => __( 'Once added be carefull editing next time. This key Should be same in app header.' ),
                ),
							
            ),
         array(
		 		'required' 		=> array( 'api-is-buy-android-app', '=', true ),
                'id'       => 'appKey_Scode',
                'type'     => 'text',
                'title'    => __( 'Enter Your Android Secret Code Here', 'carspot-rest-api' ),
				'default'  => '',
                'text_hint' => array(
                    'title'   => __( 'Alert', 'carspot-rest-api' ),
                    'content' => __( 'Once added be carefull editing next time. This key Should be same in app header.' ),
                ),
				'desc'  => __( 'Just a random number generated by you for app security.', 'carspot-rest-api' ),			
				
            ),				
            array(
                'id'     => 'api_key_settings-info1-2',
                'type'   => 'info',
                'notice' => false,
                'style'  => 'info',
                'title' => __( 'Info', 'carspot-rest-api' ),
                'desc'  => __( 'Below section is only for if you have purchased IOS App. Then turn it on and enter the purchase code in below text field that will appears.', 'carspot-rest-api' )
            ),	
			array(
                'id'       => 'api-is-buy-ios-app',
                'type'     => 'switch',
                'title'    => __( 'For IOS App', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'If you have purchased the ios app.', 'carspot-rest-api' ),
            ),			
			
						
         array(
		 		'required' 		=> array( 'api-is-buy-ios-app', '=', true ),
                'id'       => 'appKey_pCode_ios',
                'type'     => 'text',
                'title'    => __( 'Enter You IOS Purchase Code Here', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'Your IOS item purchase code got from codecanyon. You have purchased the item seprately.', 'carspot-rest-api' ),
                'text_hint' => array(
                    'title'   => __( 'Alert', 'carspot-rest-api' ),
                    'content' => __( 'Once added be carefull editing next time. This key Should be same in app header.' ),
                ),
							
            ),			
         array(
		 		'required' 		=> array( 'api-is-buy-ios-app', '=', true ),
                'id'       => 'appKey_Scode_ios',
                'type'     => 'text',
                'title'    => __( 'Enter Your IOS Secret Code Here', 'carspot-rest-api' ),
				'default'  => '',
                'text_hint' => array(
                    'title'   => __( 'Alert', 'carspot-rest-api' ),
                    'content' => __( 'Once added be carefull editing next time. This key Should be same in app header.' ),
                ),
				'desc'  => __( 'Just a random number generated by you for app security.', 'carspot-rest-api' ),			
				
            ),	
			
           	
			
         array(
                'id'       => 'appKey_youtubeKey',
                'type'     => 'text',
                'title'    => __( 'Enter Your Youtube Key', 'carspot-rest-api' ),
				'default'  => '',
                'text_hint' => array(
                    'title'   => __( 'Alert', 'carspot-rest-api' ),
                    'content' => __( 'Once added be carefull editing next time. This key Should be same in app header.' ),
                )				
				
            ),
			
		)
		) );			


Redux::setSection( $opt_name, array(
        'title'      => __( 'Stripe Settings', "carspot-rest-api" ),
        'id'         => 'api_payment_stripe',
        'desc'       => '',
        'icon' => 'el el-check',
		'subsection' => true,
        'fields'     => array(
		
			 array(
                'id'     => 'api_key_settings-info1-3',
                'type'   => 'info',
                'notice' => false,
                'style'  => 'info',
                'title' => __( 'Info', 'carspot-rest-api' ),
                'desc'  => __( 'Below section is Other API and Payment settings', 'carspot-rest-api' )
            ),			
         array(
                'id'       => 'appKey_stripe_publishKey',
                'type'     => 'text',
                'title'    => __( 'Enter Your Stripe Publishable key Here', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'This will use in app', 'carspot-rest-api' ),
                'text_hint' => array(
                    'title'   => __( 'Alert', 'carspot-rest-api' ),
                    'content' => __( 'Once added be carefull editing next time. This key Should be same in app header.' ),
                )				
				
            ),	
         array(
                'id'       => 'appKey_stripeSKey',
                'type'     => 'text',
                'title'    => __( 'Enter Your Stripe Secret key Here', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'This will use at server for varification', 'carspot-rest-api' ),
                'text_hint' => array(
                    'title'   => __( 'Alert', 'carspot-rest-api' ),
                    'content' => __( 'Once added be carefull editing next time. This key Should be same in app header.' ),
                )				
				
            ),				           
		
		
		)
	
	));



		
		
 Redux::setSection( $opt_name, array(
        'title'      => __( 'Paypal Settings', "carspot-rest-api" ),
        'id'         => 'api_payment_paypal',
        'desc'       => '',
        'icon' => 'el el-check',
		'subsection' => true,
        'fields'     => array(
		
			array(
				'id'       => 'appKey_paypalMode',
				'type'     => 'button_set',
				'title'    => __( 'Paypal Mode', 'carspot-rest-api' ),
				'options'  => array(
					'live' => __('Live', 'carspot-rest-api' ),
					'sandbox' => __('Sandbox', 'carspot-rest-api' ),
				),
				'default'  => 'live',
			),			
			array(
				'id'       => 'appKey_paypalKey',
				'type'     => 'text',
				'title'    => __( 'Enter Your Paypal Key', 'carspot-rest-api' ),
				'default'  => '',
				'text_hint' => array(
					'title'   => __( 'Alert', 'carspot-rest-api' ),
					'content' => __( 'Once added be carefull editing next time. This key Should be same in app header.' ),
				),
				'desc'  => __( 'Enter you paypal client id here', 'carspot-rest-api' ),					
				
			),		
			
			array(
				'id'       => 'appKey_paypalClientSecret',
				'type'     => 'text',
				'title'    => __( 'Enter Your Paypal Secret', 'carspot-rest-api' ),
				'default'  => '',
				'text_hint' => array(
					'title'   => __( 'Alert', 'carspot-rest-api' ),
					'content' => __( 'Once added be carefull editing next time. This key Should be same in app header.' ),
				),
				'desc'  => __( 'Enter you paypal Secret id here', 'carspot-rest-api' ),					
				
			),
			
         array(
                'id'       => 'paypalKey_merchant_name',
                'type'     => 'text',
                'title'    => __( 'Merchant Name', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'Enter the merchant name', 'carspot-rest-api' ),	
            ),	
         array(
                'id'       => 'paypalKey_currency',
                'type'     => 'text',
                'title'    => __( 'Account Currency', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'Currency name i.e. USD Supported currency list here: ', 'carspot-rest-api' ) . ' https://developer.paypal.com/docs/integration/direct/rest/currency-codes/' ,	
            ),				
         array(
                'id'       => 'paypalKey_privecy_url',
                'type'     => 'text',
                'title'    => __( 'Privecy Url', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'Example link ', 'carspot-rest-api' ) . 'https://www.example.com/privacy',	
            ),	
         array(
                'id'       => 'paypalKey_agreement',
                'type'     => 'text',
                'title'    => __( 'Agreement Url', 'carspot-rest-api' ),
				'default'  => '',	
				'desc'  => __( 'Example link ', 'carspot-rest-api' ) . 'https://www.example.com/legal',	
            ),			           
		
		
		)
	
	));				

Redux::setSection( $opt_name, array(
        'title'      => __( 'InApp Purchase Settings', "carspot-rest-api" ),
        'id'         => 'api_payment_inapp',
        'desc'       => '',
        'icon' => 'el el-check',
		'subsection' => true,
        'fields'     => array(
		
		

			array(
                'id'       => 'api-inapp-android-app',
                'type'     => 'switch',
                'title'    => __( 'Android InApp Purchase', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'If you have purchased the android app.', 'carspot-rest-api' ),
            ),	
				

            array(
				'required' 		=> array( 'api-inapp-android-app', '=', true ),
                'id'     => 'api_inapp-info1-1',
                'type'   => 'info',
                'notice' => false,
                'style'  => 'info',
                'title' => __( 'Info', 'carspot-rest-api' ),
                'desc'  => __( 'Go to Application then you will see Development tools option on the left side of menu. Click this option now navigate to Services &APIs. Now you will Licensing & in-app billing section copy the key from here.', 'carspot-rest-api' )
            ),			
				
						
         array(
		 		'required' 		=> array( 'api-inapp-android-app', '=', true ),
                'id'       => 'inApp_androidSecret',
                'type'     => 'textarea',
                'title'    => __( 'Your Android InApp Secret Code Here', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'Enter the secret code you got from store. While copy paste please make sure there is no white space.', 'carspot-rest-api' ),
                'text_hint' => array(
                    'title'   => __( 'Alert', 'carspot-rest-api' ),
                    'content' => __( 'Once added be carefull editing next time.' ),
                ),
							
            ),		
		
	
		array(
                'id'       => 'api-inapp-ios-app',
                'type'     => 'switch',
                'title'    => __( 'AppStore InApp Purchase', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'If you have purchased the AppStore app.', 'carspot-rest-api' ),
            ),	
						
         array(
		 		'required' 		=> array( 'api-inapp-ios-app', '=', true ),
                'id'       => 'inApp_iosSecret',
                'type'     => 'textarea',
                'title'    => __( 'Your AppStore InApp Secret Code Here', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'Enter the secret code you got from store. While copy paste please make sure there is no white space.', 'carspot-rest-api' ),
                'text_hint' => array(
                    'title'   => __( 'Alert', 'carspot-rest-api' ),
                    'content' => __( 'Once added be carefull editing next time.' ),
                ),
							
            ),		
	
		
			
		
		)
	
	));	



/*
Redux::setSection( $opt_name, array(
        'title'      => __( 'PayU Settings', "carspot-rest-api" ),
        'id'         => 'api_payment_payu',
        'desc'       => '',
        'icon' => 'el el-check',
		'subsection' => true,
        'fields'     => array(
		
			array(
				'id'       => 'appKey_payuMode',
				'type'     => 'button_set',
				'title'    => __( 'PayU Mode', 'carspot-rest-api' ),
				'options'  => array(
					'live' => __('Live', 'carspot-rest-api' ),
					'sandbox' => __('Sandbox', 'carspot-rest-api' ),
				),
				'default'  => 'live',
			),			
			array(
				'id'       => 'appKey_payumarchantKey',
				'type'     => 'text',
				'title'    => __( 'Enter Your PayU marchant Key', 'carspot-rest-api' ),
				'default'  => '',
				'text_hint' => array(
					'title'   => __( 'Alert', 'carspot-rest-api' ),
					'content' => __( 'Once added be carefull editing next time.' ),
				),
				'desc'  => __( 'Enter you PayU marchant key here', 'carspot-rest-api' ),					
				
			),		
         array(
                'id'       => 'payu_salt_id',
                'type'     => 'text',
                'title'    => __( 'Salt', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'Enter salt', 'carspot-rest-api' ),	
            ),	
		
		)
	
	));	
*/



 Redux::setSection( $opt_name, array(
        'title'      => __( 'Thank You Settings', "carspot-rest-api" ),
        'id'         => 'api_payment_thankyou',
        'desc'       => '',
        'icon' => 'el el-check',
		'subsection' => true,
        'fields'     => array(
		
			array(
				'id'       => 'payment_thankyou',
				'type'     => 'text',
				'title'    => __( 'Thank You Title', 'carspot-rest-api' ),
				'default'  => __( 'Thank You For Your Order', 'carspot-rest-api' ),
			),	
		)		
	));	


			
			
	
 Redux::setSection( $opt_name, array(
        'title'      => __( 'Ads/Reporting Settings', "carspot-rest-api" ),
        'id'         => 'api_ads_screen',
        'desc'       => '',
        'icon' => 'el el-picture',
        'fields'     => array(
		
		)
	
	));			
			
 Redux::setSection( $opt_name, array(
        'title'      => __( 'Ads Settings', "carspot-rest-api" ),
        'id'         => 'api_ads_screen1',
        'desc'       => '',
		'subsection' => true,
        'fields'     => array(

            array(
                'id'    => 'opt-info-warning0',
                'type'  => 'info',
                'style' => 'warning',
                'title' => __( 'Ads Setting (AdMob)', 'carspot-rest-api' ),
                'desc'  => __( 'Here you can set the AdMob settings for the app', 'carspot-rest-api' )
            ),
			
			array(
                'id'       => 'api_ad_show',
                'type'     => 'switch',
                'title'    => __( 'Show Ads', 'carspot-rest-api' ),
                'desc'    => __( 'Trun ads on or off.', 'carspot-rest-api' ),
                'default'  => false,
            ),
			
           /* array(
                'id'       => 'api_ad_type',
                'type'     => 'button_set',
                'title'    => __( 'Add Type', 'carspot-rest-api' ),
				'required' => array( 'api_ad_show', '=', '1' ),
                'options'  => array(
                    'banner' => __( 'Banner', 'carspot-rest-api' ),
                    'interstital' => __( 'Interstital', 'carspot-rest-api' ),
                ),
				'multi'    => true,
                'default'  => 'banner'
            ),*/	
			
			
          
    		array(
                'id'       => 'api_ad_type_banner',
                'type'     => 'switch',
                'title'    => __( 'Show Banner Ads', 'carspot-rest-api' ),
                'subtitle' => __( 'Turn on or off for banner ads', 'carspot-rest-api' ),
                'default'  => false,
				'required' => array( 'api_ad_show', '=', '1' ),
            ),        
				  
			
            array(
                'id'       => 'api_ad_position',
                'type'     => 'button_set',
                'title'    => __( 'Banner Ad Position', 'carspot-rest-api' ),
				'required' => array( 'api_ad_type_banner', '=', true ),
                'options'  => array(
                    'top' => __( 'Top', 'carspot-rest-api' ),
                    'bottom' => __( 'Bottom', 'carspot-rest-api' ),
                ),
				
                'default'  => 'top'
            ),	

         array(
                'id'       => 'api_ad_key_banner',
                'type'     => 'text',
                'title'    => __( 'Enter Your Ad Key (banner) Android', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( array( 'api-is-buy-android-app', '=', true ) , array('api_ad_type_banner', '=', true)),	
				'desc'     => __( 'Please make sure you are putting correct ad id your selected above banner', 'carspot-rest-api' ),	
            ),
			
			/*Added For IOS */		
			
         array(
                'id'       => 'api_ad_key_banner_ios',
                'type'     => 'text',
                'title'    => __( 'Enter Your Ad Key (banner) IOS', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( array( 'api-is-buy-ios-app', '=', true ) , array('api_ad_type_banner', '=', true)),		
				'desc'     => __( 'Please make sure you are putting correct ad id your selected above banner', 'carspot-rest-api' ),	
            ),				
			/*Added For IOS   */
			
    		array(
                'id'       => 'api_ad_type_initial',
                'type'     => 'switch',
                'title'    => __( 'Show Initial Ads', 'carspot-rest-api' ),
                'subtitle' => __( 'Turn on or off for initial ads', 'carspot-rest-api' ),
                'default'  => false,
				'required' => array( 'api_ad_show', '=', '1' ),
            ),  			
						
         array(
                'id'       => 'api_ad_key',
                'type'     => 'text',
                'title'    => __( 'Enter Your Ad Key (initial) Android', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( array( 'api-is-buy-android-app', '=', true ) , array('api_ad_type_initial', '=', true)),
				'desc'     => __( 'Please make sure you are putting correct ad id your selected above interstital', 'carspot-rest-api' ),	
            ),	
		/*For IOS */           
         array(
                'id'       => 'api_ad_key_ios',
                'type'     => 'text',
                'title'    => __( 'Enter Your Ad Key (initial) IOS', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( array( 'api-is-buy-ios-app', '=', true ) , array('api_ad_type_initial', '=', true)),			
				'desc'     => __( 'Please make sure you are putting correct ad id your selected above interstital', 'carspot-rest-api' ),	
            ),	
		/*For IOS ends */ 		
         array(
                'id'       => 'api_ad_time_initial',
                'type'     => 'text',
                'title'    => __( 'Show 1st Ad After', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( 'api_ad_type_initial', '=', true ),			
				'desc'     => __( 'Show 1st ad after specific time. In seconds 1 is for 1 second', 'carspot-rest-api' ),	
            ),				
         array(
                'id'       => 'api_ad_time',
                'type'     => 'text',
                'title'    => __( 'Show Ad After', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( 'api_ad_type_initial', '=', true ),		
				'desc'     => __( 'Show ads next time after specific time. In seconds 1 is for 1 second', 'carspot-rest-api' ),	
            ),	
			

						
			
		)
		) );


 Redux::setSection( $opt_name, array(
        'title'      => __( 'Analytics Settings', "carspot-rest-api" ),
        'id'         => 'api_ads_screen2',
        'desc'       => '',
        'subsection' => true,
        'fields'     => array(

            array(
                'id'    => 'opt-info-warning1',
                'type'  => 'info',
                'style' => 'warning',
                'title' => __( 'App Analytics', 'carspot-rest-api' ),
                'desc'  => __( 'Below you can setup analytics for the app.', 'carspot-rest-api' )
            ),
			

			array(
                'id'       => 'api_analytics_show',
                'type'     => 'switch',
                'title'    => __( 'Make Analytics', 'carspot-rest-api' ),
                'desc'    => __( 'Trun ads on or off.', 'carspot-rest-api' ),
                'default'  => false,
            ),	

         array(
                'id'       => 'api_analytics_id',
                'type'     => 'text',
                'title'    => __( 'Analytics ID', 'carspot-rest-api' ),
				'default'  => '',	
				'required' => array( 'api_analytics_show', '=', true ),		
				'desc'     => __( 'Put analytics id here i.e.', 'carspot-rest-api' ). ' UA-XXXXXXXXX-X',	
            ),					
		
	
	
		)
	
	));	
			
			
 Redux::setSection( $opt_name, array(
        'title'      => __( 'Firebase Settings', "carspot-rest-api" ),
        'id'         => 'api_ads_screen3',
        'desc'       => '',
		'subsection' => true,
        'fields'     => array(

            array(
                'id'    => 'opt-info-warning2',
                'type'  => 'info',
                'style' => 'warning',
                'title' => __( 'Puch Nofifications', 'carspot-rest-api' ),
                'desc'  => __( 'Below you can setup Puch Nofifications for the app.', 'carspot-rest-api' )
            ),
         array(
                'id'       => 'api_firebase_id',
                'type'     => 'text',
                'title'    => __( 'Firebase API KEY', 'carspot-rest-api' ),
				'default'  => '',	
				'desc'     => __( 'Put firebase api key', 'carspot-rest-api' ),
            ),			
	
	
		)
	
	));			
			
	
   Redux::setSection( $opt_name, array(
        'title'      => __( 'Menu Settings', "carspot-rest-api" ),
        'id'         => 'api_menu_settings',
        'desc'       => '',
        'icon' => 'el el-align-justify',
        'fields'     => array(			
			
			array(
                'id'       => 'api-sortable-app-switch',
                'type'     => 'switch',
                'title'    => __( 'Turn Custom Menu', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'Turn on custom menu settings', 'carspot-rest-api' ),
            ),			
					
            array(
				'required' 		=> array( 'api-sortable-app-switch', '=', true ),
                'id'       => 'api-sortable-app-menu',
                'type'     => 'text',
                'title'    => __( 'Menu Title Control', 'carspot-rest-api' ),
                'desc'     => __( 'Chnage menu title to what you want.', 'carspot-rest-api' ),
                'label'    => true,
                'options'  => array(
                    'home'   			=> __("Home", "carspot-rest-api"),
                    'profile'   		=> __("Profile", "carspot-rest-api"),
                    'search' 			=> __("Advance Search", "carspot-rest-api"),
                    'messages'   		=> __("Messages", "carspot-rest-api"),
                    'my_ads'   			=> __("My Ads", "carspot-rest-api"),
                    'inactive_ads' 		=> __("Inactive Ads", "carspot-rest-api"),
                    'featured_ads'   	=> __("Featured Ads", "carspot-rest-api"),
                    'fav_ads'   		=> __("Fav Ads", "carspot-rest-api"),
                    'packages' 			=> __("Packages", "carspot-rest-api"),
					
					'pages' 			=> __("Pages", "carspot-rest-api"),
					
					'others' 			=> __("Others", "carspot-rest-api"),
					'blog' 				=> __("Blog", "carspot-rest-api"),
					'logout' 			=> __("Logout", "carspot-rest-api"),
					'login' 			=> __("Login", "carspot-rest-api"),
					'register' 			=> __("Register", "carspot-rest-api"),
					
					
                )
            ),	
			
			array(
                'id'       => 'api-menu-message-count',
                'type'     => 'switch',
                'title'    => __( 'Show Message Count', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'Turn on/off Show Message Count in menu.', 'carspot-rest-api' ),
            ),						
			
			array(
                'id'       => 'api-menu-hide-message-menu',
                'type'     => 'switch',
                'title'    => __( 'Hide Messages From Menu', 'carspot-rest-api' ),
                'default'  => true,
            ),	
			array(
                'id'       => 'api-menu-hide-package-menu',
                'type'     => 'switch',
                'title'    => __( 'Hide Package From Menu', 'carspot-rest-api' ),
                'default'  => true,
            ),							
			array(
                'id'       => 'api-menu-hide-blog-menu',
                'type'     => 'switch',
                'title'    => __( 'Hide Blog From Menu', 'carspot-rest-api' ),
                'default'  => true,
            ),			
		)
	
	));			
				
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Home Screen', "carspot-rest-api" ),
        'id'         => 'api_home_screen',
        'desc'       => '',
        'icon' => 'el el-home',
        'fields'     => array(
			
			array(
                'id'       => 'sb_home_screen_title',
                'type'     => 'text',
                'title'    => __( 'Screen Title', 'carspot-rest-api' ),
				'default'  => __( 'Home Screen', 'carspot-rest-api' ),
				'desc'       => __( 'Set the title for homescreen', 'carspot-rest-api' ),
            ),			
			array(
                'id'       => 'hom_sec_bg',
                'type'     => 'media',
                'url'      => true,
                'title'    => __( 'Home Secreen Background', 'carspot-rest-api' ),
                'compiler' => 'true',
                'desc'     => __( 'Image for Home Secreen', 'carspot-rest-api' ),
                'subtitle' => __( 'Dimensions: 800 x 800', 'carspot-rest-api' ),
                'default'  => array( 'url' => CARSPOT_API_PLUGIN_URL."images/home-secreen.jpg" ),
            ),
			array(
                'id'       => 'hom_sec_tagline',
                'type'     => 'text',
                'title'    => __( 'Home Secreen Tagline', 'carspot-rest-api' ),
				'default'  => __( 'Find Adds', 'nokri-rest-api' ),
            ),
			
            array(
                'id'       => 'hom_sec_headline',
                'type'     => 'text',
                'title'    => __( 'Home Secreen Headline', 'carspot-rest-api' ),
				'default'  => __( 'Search From 1500+ Adds', 'carspot-rest-api' ),
            ),
			array(
                'id'       => 'hom_sec_place_holder',
                'type'     => 'text',
                'title'    => __( 'Home Secreen Place Holder', 'carspot-rest-api' ),
				'default'  => __( 'Search Keywords...', 'carspot-rest-api' ),
            ),
			array(
                'id'       => 'hom_sec_advance_search',
                'type'     => 'text',
                'title'    => __( 'Home Secreen below search bar text', 'carspot-rest-api' ),
				'default'  => __( 'Advance search', 'carspot-rest-api' ),
            ),
            array(
                'id'    => 'home-notice-info0',
                'type'  => 'info',
                'style' => 'info',
                'title' => __( 'Sort Home Screen Options', 'carspot-rest-api' ),
            ),	

			array(
                'id'       => 'home-screen-sortable-enable',
                'type'     => 'switch',
                'title'    => __( 'Home Sortable', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'Sort home sections here', 'carspot-rest-api' ),
            ),	
			
								
            array(
				'required' 		=> array( 'home-screen-sortable-enable', '=', true ),
                'id'       => 'home-screen-sortable',
                'type'     => 'sortable',
                'mode'     => 'checkbox', // checkbox or text
                'title'    => __( 'Sortable Sections', 'carspot-rest-api' ),
                'desc'     => __( 'Sort section layouts on homescreen', 'carspot-rest-api' ),
                'options'  => array(
				     'body_type' => __( 'Body Types', 'carspot-rest-api' ),
                    'cat_icons' => __( 'Category Icons', 'carspot-rest-api' ),
                    'featured_ads' => __( 'Featured Slider', 'carspot-rest-api' ),
                    'latest_ads' => __( 'Latest Ads', 'carspot-rest-api' ),
					'cat_locations' => __( 'Locations Icons', 'carspot-rest-api' ),
					'blogNews' => __( 'Blog/News', 'carspot-rest-api' ),
					'comparison' => __( 'Comparison', 'carspot-rest-api' ),
                ),
                'default'  => array(
                    'cat_icons' => __( 'Category Icons', 'carspot-rest-api' ),
					'sliders' => __( 'Simple Ads', 'carspot-rest-api' ),
				)
            ),			
			
		

      	
		)
		) );
		 
		 
		 
		 
		 
		 
		 /*************************/
		 /* Body Type Section */
		 /*************************/
         Redux::setSection( $opt_name, array(
        'title'      => __( "Body Type", "carspot-rest-api" ),
        'id'         => 'api_home_body_type',
        'desc'       => '',
		'subsection' => true,
        'fields'     => array(
			/*Body type */
			array(
                'id'       => 	'api_home_body_type_switch',
                'type'     => 	'switch',
                'title'    => 	__( 'Body Type', 'carspot-rest-api' ),
                'default'  => 	false,
				'desc'     => 	__( 'Show body type slider', 'carspot-rest-api' ),
            ),			
			array(
                'id'       =>  'api_home_body_type_section_title',
				'required' =>  array( 'api_home_body_type_switch', '=', true ),
                'type'     =>  'text',
                'title'    =>  __( 'Body Type Section Title', 'carspot-rest-api' ),
				'desc'     =>  __( 'Separate string with sign like Featured|Types', 'carspot-rest-api' ),
				'default'  =>  __( 'Featured|Types', 'carspot-rest-api'),
            ),
			array(
				'required' =>  array( 'api_home_body_type_switch', '=', true ),
                'id'       => 'carspot-api-ad-body-types-multi',
                'type'     => 'select',
                'data'     => 'terms',
				'args' => array(
					'taxonomies'=>'ad_body_types', 'hide_empty' => false,
					/*'taxonomies' => array( 'ad_cats' ),*/
				),				
                'multi'    => true,
				'sortable' => true,
                'title'    => __( 'Body Tupe Multi Select Option', 'carspot-rest-api' ),
                'desc'     => __( 'This is the description field, again good for additional info.', 'carspot-rest-api' ),
            ),

			)
		) );
		
		 /*************************/
		 /* Featured Makes */
		 /*************************/
         Redux::setSection( $opt_name, array(
        'title'      => __( "Featured Makes", "carspot-rest-api" ),
        'id'         => 'api_home_featured_makes',
        'desc'       => '',
		'subsection' => true,
        'fields'     => array(
			/*Body type */
			array(
                'id'       => 	'api_home_featured_makes_switch',
                'type'     => 	'switch',
                'title'    => 	__( 'Featured Makes', 'carspot-rest-api' ),
                'default'  => 	false,
				'desc'     => 	__( 'Show featured makes slider', 'carspot-rest-api' ),
            ),			
			array(
			    'required' =>  array( 'api_home_featured_makes_switch', '=', true ),
                'id'       => 'categories-section-title',
                'type'     => 'text',
                'title'    => __( 'Makes Section Title', 'carspot-rest-api' ),
				'default'  => __( 'Featured|Makes', 'carspot-rest-api' ),
				 'desc'    => __( 'Separate string with sign like Featured|Makes', 'carspot-rest-api' ),
            ),
			array(
			    'required' =>  array( 'api_home_featured_makes_switch', '=', true ),
                'id'       => 'categories-section-view-all',
                'type'     => 'text',
                'title'    => __( 'Makes Section View All', 'carspot-rest-api' ),
				'default'  => __( 'View All Makes', 'carspot-rest-api' ),
            ),
			 array(
			  'required'   =>  array( 'api_home_featured_makes_switch', '=', true ),
                'id'       => 'api_cat_columns',
                'type'     => 'button_set',
                'title'    => __( 'Makes Columns', 'carspot-rest-api' ),
                'desc'     => __( 'Select number of info columns', 'carspot-rest-api' ),
                'options'  => array(
                    '3'    => __( '3 Column', 'carspot-rest-api' ),
                    '4'    => __( '4 Columns', 'carspot-rest-api' ),
                ),
                'default'  => '3'
            ),		
			 array(
			  'required'   =>  array( 'api_home_featured_makes_switch', '=', true ),
                'id'       => 'carspot-api-ad-cats-multi',
                'type'     => 'select',
                'data'     => 'terms',
				'args' => array(
					'taxonomies'=>'ad_cats', 'hide_empty' => false,
					/*'taxonomies' => array( 'ad_cats' ),*/
				),				
                'multi'    => true,
				'sortable' => true,
                'title'    => __( 'Makes Multi Select Option', 'carspot-rest-api' ),
                'desc'     => __( 'Select makes you want to show', 'carspot-rest-api' ),
            ),

			)
		) );
		
		
		
    Redux::setSection( $opt_name, array(
        'title'      => __( "Featured Ads", "carspot-rest-api" ),
        'id'         => 'api_home_ads_featured',
        'desc'       => '',
		'subsection' => true,
        'fields'     => array(
			/*featured ads */
			array(
                'id'       => 'feature_on_home',
                'type'     => 'switch',
                'title'    => __( 'Featured Ads', 'carspot-rest-api' ),
                'default'  => false,
				'desc'     => __( 'Show featured ads slider', 'carspot-rest-api' ),
            ),			
			array(
                'id'       => 'sb_home_ads_title',
				'required' => array( 'feature_on_home', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Featured Ads Section Title', 'carspot-rest-api' ),
				'desc'     => __( 'Separate string with sign like Featured|Makes', 'carspot-rest-api' ),
				'default'  => 'Featured Ads',
            ),
			array(
                'id'       => 'sb_home_ads_view_all',
				'required' => array( 'feature_on_home', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Featured Ads View All Title', 'carspot-rest-api' ),
				'default'  => __( 'View All', 'carspot-rest-api' ),
            ),
            array(
                'id'            => 'home_related_posts_count',
				'required' 		=> array( 'feature_on_home', '=', true ),
                'type'          => 'slider',
                'title'         => __( 'Featured Posts', 'carspot-rest-api' ),
				'subtitle'      => __( 'On homepage', 'carspot-rest-api' ),
                'desc'          => __( 'Select Number of featured posts', 'carspot-rest-api' ),
                'default'       => 5,
                'min'           => 1,
                'step'          => 1,
                'max'           => 150,
                'display_value' => 'label'
            ),				
					
			array(
				'id'       => 'home_featured_position',
				'type'     => 'button_set',
				'title'    => __( 'Featured Ads Position', 'carspot-rest-api' ),
				'options'  => array(
					'1' => __('Top', 'carspot-rest-api' ),
					'2' => __('Middle', 'carspot-rest-api' ),
					'3' => __('Bottom', 'carspot-rest-api' ),
				),
				'default'  => '1',
				'required' 		=> array( 'feature_on_home', '=', true ),
			),					

			)
		) );



    Redux::setSection( $opt_name, array(
		
        'title'      => __( "Latest Ads", "carspot-rest-api" ),
        'id'         => 'api_home_latest',
        'desc'       => '',
		'subsection' => true,
        'fields'     => array(
			/*latets ads */
           array(
                'id'    => 'home-notice-info2',
                'type'  => 'info',
                'style' => 'info',
                'title' => __( 'Latest Ads Section', 'carspot-rest-api' ),
            ),				
			array(
				'required' 		=> array( 'home-screen-sortable-enable', '=', true ),
                'id'       => 'latest_on_home',
                'type'     => 'switch',
                'title'    => __( 'Latest Ads', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'Show latest ads slider', 'carspot-rest-api' ),
            ),			
			array(
                'id'        => 'sb_home_latest_ads_title',
				'required'  => array( 'latest_on_home', '=', true ),
                'type'      => 'text',
                'title'     => __( 'Latest Ads Section Title', 'carspot-rest-api' ),
				'desc'      => __( 'Separate string with sign like Featured|Makes', 'carspot-rest-api' ),
				'default'   => 'Latest Ads',
            ),
			array(
                'id'       => 'sb_home_latest_ads_title_view_all',
				'required' 		=> array( 'latest_on_home', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Latest Ads View All Title', 'carspot-rest-api' ),
				'default'  => __( 'View All', 'carspot-rest-api' ),
            ),
            array(
                'id'            => 'home_latest_posts_count',
				'required' 		=> array( 'latest_on_home', '=', true ),
                'type'          => 'slider',
                'title'         => __( 'Latest Ads', 'carspot-rest-api' ),
				'subtitle'    => __( 'On homepage', 'carspot-rest-api' ),
                'desc'          => __( 'Select Number of latest ads', 'carspot-rest-api' ),
                'default'       => 5,
                'min'           => 1,
                'step'          => 1,
                'max'           => 150,
                'display_value' => 'label'
            ),					
			)
		) );




    Redux::setSection( $opt_name, array(
        'title'      => __( "Ads Locations", "carspot-rest-api" ),
        'id'         => 'api_home_locations',
        'desc'       => '',
		'subsection' => true,
        'fields'     => array(
			/*locations*/
            array(
                'id'    => 'home-notice-info5',
                'type'  => 'info',
                'style' => 'info',
                'title' => __( 'Locations icons Section', 'carspot-rest-api' ),
            ),	
			array(
				'required' => array( 'home-screen-sortable-enable', '=', true ),
                'id'       => 'api_location_on_home',
                'type'     => 'switch',
                'title'    => __( 'Location', 'carspot-rest-api' ),
                'default'  => false,
				'desc'     => __( 'Show Location on home page', 'carspot-rest-api' ),
            ),	
			array(
			    'required' => array( 'api_location_on_home', '=', true ),
                'id'       => 'api_location_title',
				'required' => array( 'home-screen-sortable-enable', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Location Section Title', 'carspot-rest-api' ),
				'desc'     => __( 'Separate string with sign like Explore|city', 'carspot-rest-api' ),
				'default'  => __( 'Explore|city', 'carspot-rest-api' ),
            ),						
           					
            array(
			    'required' => array( 'api_location_on_home', '=', true ),
				'required' 		=> array( 'home-screen-sortable-enable', '=', true ),
                'id'       => 'carspot-api-ad-loc-multi',
                'type'     => 'select',
                'data'     => 'terms',
				'args' => array(
					'taxonomies'=>'ad_country', 'hide_empty' => false,
				),				
                'multi'    => true,
				'sortable' => true,
                'title'    => __( 'Select Locations Categories', 'carspot-rest-api' ),
                'desc'     => __( 'Select locations you want to show', 'carspot-rest-api' ),
            ),

			)
		) );

    
		
		Redux::setSection( $opt_name, array(
        'title'      => __( "Comparison", "carspot-rest-api" ),
        'id'         => 'api_home_comparison',
        'desc'       => '',
		'subsection' => true,
        'fields'     => array(
			/*locations*/
            array(
                'id'    => 'home-notice-info7',
                'type'  => 'info',
                'style' => 'info',
                'title' => __( 'Comparison section. (You must on Home Sortable to show comparison)', 'carspot-rest-api' ),
            ),	
			array(
				'required' 		=> array( 'home-screen-sortable-enable', '=', true ),
                'id'       => 'posts_comparison_home',
                'type'     => 'switch',
                'title'    => __( 'Comparison', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'Show Comparison', 'carspot-rest-api' ),
            ),	
					
			array(
                'id'       => 'api_comparison_title',
				'required' 		=> array( 'posts_comparison_home', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Comparison Setion Title', 'carspot-rest-api' ),
				'default'  => __( 'Comparison', 'carspot-rest-api' ),
				'desc'     => __( 'Separate string with sign like Top|Comparison', 'carspot-rest-api' ),
            ),	
			array(
                'id'       => 'api_comparison_view_all',
				'required' 		=> array( 'posts_comparison_home', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Comparison Setion View All', 'carspot-rest-api' ),
				'default'  => __( 'View All', 'carspot-rest-api' ),
            ),
			array(
                'id'       => 'api_comparison_vs_txt',
				'required' 		=> array( 'posts_comparison_home', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Comparison V/s text', 'carspot-rest-api' ),
				'default'  => __( 'V/S', 'carspot-rest-api' ),
            ),
            array(
				'required' 		=> array( 'posts_comparison_home', '=', true ),
                'id'       => 'carspot-api-com_car1',
                'type'     => 'select',
                'data'  => 'post',
				'args'     => array( 'post_type' =>  array( 'comparison'), 'numberposts' => -1 ),
                'multi'    => true,
                'title'    => __( 'Comparison Cars', 'redux-framework-demo' ),
                'desc'     => __( 'Select 1st car for comparison', 'redux-framework-demo' ),
            ),
			array(
				'required' 		=> array( 'posts_comparison_home', '=', true ),
                'id'       => 'carspot-api-com_car2',
                'type'     => 'select',
                'data'  => 'post',
				'args'     => array( 'post_type' =>  array( 'comparison'), 'numberposts' => -1 ),
                'multi'    => true,
                'title'    => __( 'Comparison V/S Car', 'redux-framework-demo' ),
                'desc'     => __( 'Select second car for comparison', 'redux-framework-demo' ),
            ),
								

			

			)
		) );
		
		Redux::setSection( $opt_name, array(
        'title'      => __( "Blog/News", "carspot-rest-api" ),
        'id'         => 'api_home_blogNews',
        'desc'       => '',
		'subsection' => true,
        'fields'     => array(
			/*locations*/
            array(
                'id'    => 'home-notice-info7',
                'type'  => 'info',
                'style' => 'info',
                'title' => __( 'Blog and news section. (You must on Home Sortable to show blogs)', 'carspot-rest-api' ),
            ),	
			array(
				'required' 		=> array( 'home-screen-sortable-enable', '=', true ),
                'id'       => 'posts_blogNews_home',
                'type'     => 'switch',
                'title'    => __( 'News/Blog', 'carspot-rest-api' ),
                'default'  => false,
				'desc'          => __( 'Show News/Blog ads slider', 'carspot-rest-api' ),
            ),			
			array(
                'id'       => 'api_blogNews_title',
				'required' 		=> array( 'posts_blogNews_home', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Blog/News Setion Title', 'carspot-rest-api' ),
				'default'  => __( 'Blog/News', 'carspot-rest-api' ),
            ),	
			

			array(
				'required' 		=> array( 'posts_blogNews_home', '=', true ),
                'id'       => 'carspot-api-blogNews-multi',
                'type'     => 'select',
                'data'     => 'terms',
				'args' => array(
					'taxonomies'=>'category', 'hide_empty' => false,
				),				
                'multi'    => true,
				'sortable' => true,
                'title'    => __( 'Select Categories', 'carspot-rest-api' ),
                'desc'     => __( 'Select categories to show in the blog/news section. Leave empty if you want to show from all.', 'carspot-rest-api' ),
            ),

            array(
                'id'            => 'home_blogNews_posts_count',
				'required' 		=> array( 'posts_blogNews_home', '=', true ),
                'type'          => 'slider',
                'title'         => __( 'Number of Posts', 'carspot-rest-api' ),
				'subtitle'    => __( 'On homepage', 'carspot-rest-api' ),
                'desc'          => __( 'Select max number of Posts to show', 'carspot-rest-api' ),
                'default'       => 5,
                'min'           => 1,
                'step'          => 1,
                'max'           => 150,
                'display_value' => 'label'
            ),					


			)
		) );
		
		
		
		
		
/*Home Complete Ends Here*/




    Redux::setSection( $opt_name, array(
        'title'      => __( "Ad's General Settings", "carspot-rest-api" ),
        'id'         => 'api_ad_posts',
        'desc'       => '',
        'icon' => 'el el-adjust-alt',
        'fields'     => array(			
		array(
			'id'       => 'sb_location_allowed',
			'type'     => 'switch',
			'title'    => __( 'Allowed all countries', 'carspot-rest-api' ),
			'default'  => true,
		),
		array(
			'id'       => 'sb_list_allowed_country',
			'type'     => 'select',
			'options'     => carspotAPI_get_all_countries(),
			'multi'    => false,
			'title'    => __( 'Select Countries', 'carspot-rest-api' ),
			'required' => array( 'sb_location_allowed', '=', array( '0' ) ),
			'desc'     => __( 'You can select only 1 country.', 'carspot-rest-api' ),
			),
		 	array(
                'id'       => 'communication_mode',
                'type'     => 'button_set',
                'title'    => __( 'Communications Mode', 'carspot-rest-api' ),
                'options'  => array(
                    'phone' => __('Phone', 'carspot-rest-api' ),
                    'message' => __('Messages', 'carspot-rest-api' ),
                    'both' => __('Both', 'carspot-rest-api' ),
                ),
                'default'  => 'both'
            ),
   			array(
                'id'       => 'sb_order_auto_approve',
                'type'     => 'switch',
                'title'    => __( 'Package order auto approval', 'carspot-rest-api' ),
                'subtitle'    => __( 'after payment', 'carspot-rest-api' ),
                'default'  => false,
            ),
			array(
                'id'       => 'sb_send_email_on_ad_post',
                'type'     => 'switch',
                'title'    => __( 'Send email on Ad Post', 'carspot-rest-api' ),
                'default'  => true,
            ),
			array(
                'id'       => 'ad_post_email_value',
                'type'     => 'text',
                'title'    => __( 'Email for notification.', 'carspot-rest-api' ),
				'required' => array( 'sb_send_email_on_ad_post', '=', '1' ),
				'default'  => get_option( 'admin_email' ),
            ),
			array(
                'id'       => 'sb_send_email_on_message',
                'type'     => 'switch',
                'title'    => __( 'Send email on message', 'carspot-rest-api' ),
                'desc'    => __( 'When someone drop a message on ad then email send to concern user.', 'carspot-rest-api' ),
                'default'  => true,
            ),

			array(
                'id'       => 'sb_currency',
                'type'     => 'text',
                'title'    => __( 'Currency', 'carspot-rest-api' ),
				'desc' => carspotAPI_make_link ( 'http://htmlarrows.com/currency/' , __( 'List of Currency' , 'carspot-rest-api' ) ) . " " . esc_attr__( 'You can use HTML code or text as well like USD etc', 'carspot-rest-api' ),
				'default'  => '$',
            ),
			array(
                'id'       => 'sb_price_direction',
                'type'     => 'select',
                'options'  => array('left' => 'Left', 'right' => 'Right' ),
                'title'    => __( 'Price direction', 'carspot-rest-api' ),
				'default'  => 'left',
            ),
			array(
                'id'       => 'sb_price_separator',
                'type'     => 'text',
                'title'    => __( 'Thousands Separator', 'carspot-rest-api' ),
				'default'  => ',',
            ),
			array(
                'id'       => 'sb_price_decimals',
                'type'     => 'text',
                'title'    => __( 'Decimals', 'carspot-rest-api'),
                'desc'    => __( 'It should be 0 for no decimals.', 'carspot-rest-api' ),
				'default'  => '2',
            ),
			array(
                'id'       => 'sb_price_decimals_separator',
                'type'     => 'text',
                'title'    => __( 'Decimals Separator', 'carspot-rest-api' ),
				'default'  => '.',
            ),
			array(
                'id'       => 'sb_ad_approval',
                'type'     => 'select',
                'options'  => array('auto' => 'Auto Approved', 'manual' => 'Admin manual approval' ),
                'title'    => __( 'Ad Approval', 'carspot-rest-api' ),
				'default'  => 'auto',
            ),
			array(
                'id'       => 'sb_update_approval',
                'type'     => 'select',
                'options'  => array('auto' => 'Auto Approved', 'manual' => 'Admin manual approval' ),
                'title'    => __( 'Ad Update Approval', 'carspot-rest-api' ),
				'default'  => 'auto',
            ),
			array(
                'id'       => 'email_on_ad_approval',
                'type'     => 'switch',
                'title'    => __( 'Email to Ad owner on approval', 'carspot-rest-api' ),
                'default'  => true,
            ),
			

			array(
                'id'       => 'report_options',
                'type'     => 'text',
                'title'    => __( 'Report ad Options', 'carspot-rest-api' ),
				'default'  => 'Spam|Offensive|Duplicated|Fake',
            ),
			array(
                'id'       => 'report_limit',
                'type'     => 'text',
                'title'    => __( 'Ad Report Limit', 'carspot-rest-api' ),
				'desc'     => __( 'Only integer value without spaces.', 'carspot-rest-api' ),
				'default'  => 10,
            ),
			array(
                'id'       => 'report_action',
                'type'     => 'select',
                'title'    => __( 'Action on Ad Report Limit', 'carspot-rest-api' ),
				'options'  => array(1 => 'Auto Inactive', 2 => 'Email to Admin'),
				'default'  => 1,
            ),
			array(
                'id'       => 'report_email',
                'type'     => 'text',
                'title'    => __( 'Email', 'carspot-rest-api'),
				'desc'     => __( 'Email where you want to get notify.', 'carspot-rest-api' ),
				'required' => array( 'report_action', '=', array( 2 ) ),
				'default'  => get_option( 'admin_email' ),
            ),			
            array(
                'id'       => 'default_related_image',
                'type'     => 'media',
                'url'      => true,
                'title'    => __( 'Default Image', 'carspot-rest-api' ),
                'compiler' => 'true',
                'desc'     => __( 'If there is no image of ad then this will be show.', 'carspot-rest-api' ),
                'subtitle' => __( 'Dimensions: 300 x 225', 'carspot-rest-api' ),
                'default'  => array( 'url' => CARSPOT_API_PLUGIN_URL."images/default-img.png" ),
            ),			
			
		 	array(
                'id'       => 'ads_images_sizes',
                'type'     => 'button_set',
                'title'    => __( 'Set Image Sizes for listings', 'carspot-rest-api' ),
                'options'  => array(
                    'default' => __('Default', 'carspot-rest-api' ),
                    'size2' => __('Size 2', 'carspot-rest-api' ),
                    'size3' => __('Size 3', 'carspot-rest-api' ),
					'size4' => __('Size 4', 'carspot-rest-api' ),
					'size5' => __('Size 5', 'carspot-rest-api' ),
                ),
                'default'  => 'default',
				'desc'     => __( 'Change with caution we only recommend default.', 'carspot-rest-api' ),
            ),	

		 	array(
                'id'       => 'ads_images_sizes_adDetils',
                'type'     => 'button_set',
                'title'    => __( 'Set Image Sizes for Ad Details', 'carspot-rest-api' ),
                'options'  => array(
                    'default' => __('Default', 'carspot-rest-api' ),
                    'size2' => __('Size 2', 'carspot-rest-api' ),
                ),
                'default'  => 'default',
				'desc'     => __( 'Change with caution we only recommend default.', 'carspot-rest-api' ),
            ),						
			
			
			)
		) );

    Redux::setSection( $opt_name, array(
        'title'      => __( "Ad Post Settings", "carspot-rest-api" ),
        'id'         => 'api_ad_post_settings',
        'desc'       => '',
        'icon' => 'el el-home',
		'subsection' => true,
        'fields'     => array(
			
			array(
                'id'       => 'adpost_cat_template',
                'type'     => 'switch',
                'title'    => __( 'Turn On Category Template', 'carspot-rest-api' ),
                'default'  => false,
            ),			
			array(
                'id'       => 'admin_allow_unlimited_ads',
                'type'     => 'switch',
                'title'    => __( 'Post unlimited free ads', 'carspot-rest-api' ),
				'subtitle'     => __( 'For Administrator', 'carspot-rest-api' ),
                'default'  => true,
            ),
			array(
                'id'       => 'sb_standard_images_size',
                'type'     => 'switch',
                'title'    => __( 'Strict image mode', 'carspot-rest-api' ),
				'subtitle'     => __( 'Not allowed less than 760x410', 'carspot-rest-api' ),
                'default'  => false,
            ),			
			array(
                'id'       => 'sb_allow_ads',
                'type'     => 'switch',
                'title'    => __( 'Free Ads', 'carspot-rest-api' ),
				'subtitle'     => __( 'For new user', 'carspot-rest-api' ),
                'default'  => true,
            ),

			array(
                'id'       => 'sb_free_ads_limit',
                'type'     => 'text',
                'title'    => __( 'Free Ads limit', 'carspot-rest-api' ),
				'required' => array( 'sb_allow_ads', '=', array( true) ),
				'subtitle'     => __( 'For new user', 'carspot-rest-api' ),
				'desc'     => __( 'It must be an inter value, -1 means unlimited.', 'carspot-rest-api' ),
				'default'  => -1,
            ),
			
			array(
                'id'       => 'sb_allow_featured_ads',
                'type'     => 'switch',
                'title'    => __( 'Free Featured Ads', 'carspot-rest-api' ),
				'subtitle'     => __( 'For new user', 'carspot-rest-api' ),
                'default'  => true,
            ),

			array(
                'id'       => 'sb_featured_ads_limit',
                'type'     => 'text',
                'title'    => __( 'Featured Ads limit', 'carspot-rest-api' ),
				'subtitle'     => __( 'For new user', 'carspot-rest-api' ),
				'required' => array( 'sb_allow_featured_ads', '=', array( true) ),
				'desc'     => __( 'It must be an inter value, -1 means unlimited.', 'carspot-rest-api' ),
				'default'  => 1,
            ),
			array(
                'id'       => 'sb_allow_bump_ads',
                'type'     => 'switch',
                'title'    => __( 'Free Bump Ads', 'carspot-rest-api' ),
				'subtitle'     => __( 'For new user', 'carspot-rest-api' ),
                'default'  => true,
            ),
			array(
                'id'       => 'sb_bump_ads_limit',
                'type'     => 'text',
                'title'    => __( 'Bump Ads limit', 'carspot-rest-api' ),
				'subtitle'     => __( 'For new user', 'carspot-rest-api' ),
				'required' => array( 'sb_allow_bump_ads', '=', array( true) ),
				'desc'     => __( 'It must be an inter value, -1 means unlimited.', 'carspot-rest-api' ),
				'default'  => 1,
            ),
			array(
                'id'       => 'sb_allow_free_bump_up',
                'type'     => 'switch',
                'title'    => __( 'Free Bump Ads for all users', 'carspot-rest-api' ),
				'subtitle'     => __( 'witout any package/restriction.', 'carspot-rest-api' ),
                'default'  => false,
            ),			



			array(
                'id'       => 'sb_package_validity',
                'type'     => 'text',
                'title'    => __( 'Free package validity', 'carspot-rest-api'),
                'subtitle'    => __( 'In days for new user', 'carspot-rest-api'),
				'required' => array( 'sb_allow_ads', '=', array( true) ),
				'desc'     => __( 'It must be an inter value, -1 means never expired.', 'carspot-rest-api'),
				'default'  => -1,
            ),
			
			array(
                'id'       => 'simple_ad_removal',
                'type'     => 'text',
                'title'    => __( 'Simple ad remove after', 'carspot-rest-api'),
				'subtitle'    => __( 'In DAYS', 'carspot-rest-api'),
				'desc'     => __( 'Only integer value without spaces -1 means never expired.', 'carspot-rest-api'),
				'default'  => -1,
            ),
			array(
                'id'       => 'featured_expiry',
                'type'     => 'text',
                'title'    => __( 'Feature Ad Expired', 'carspot-rest-api' ),
				'subtitle'    => __( 'In DAYS', 'carspot-rest-api' ),
				'desc'     => __( 'Only integer value without spaces -1 means never expired.', 'carspot-rest-api' ),
				'default'  => 7,
            ),
			array(
                'id'       => 'sb_upload_limit',
                'type'     => 'select',
                'title'    => __( 'Ad image set limit', 'carspot-rest-api' ),
				'options'  => array(1 => 1,2 => 2,3 => 3,4 => 4,5 => 5,6 => 6,7 => 7,8 => 8,9 => 9,10 => 10, 11 => 11, 12=> 12, 13 => 13, 14 => 14, 15 => 15, 16 => 16, 17 => 17, 18 => 18, 19 => 19, 20 => 20, 21 => 21, 22 => 22, 23 => 23, 24 => 24, 25 => 25),
				'default'  => 5,
            ),
			array(
                'id'       => 'sb_upload_size',
                'type'     => 'select',
                'title'    => __( 'Ad image max size', 'carspot-rest-api' ),
				'options'  => array( '307200-300kb' => '300kb', '614400-600kb' => '600kb', '819200-800kb' => '800kb', '1048576-1MB' => '1MB', '2097152-2MB' => '2MB', '3145728-3MB' => '3MB', '4194304-4MB' => '4MB', '5242880-5MB' => '5MB', '6291456-6MB' => '6MB', '7340032-7MB' => '7MB', '8388608-8MB' => '8MB', '9437184-9MB' => '9MB', '10485760-10MB' => '10MB', '11534336-11MB' => '11MB', '12582912-12MB' => '12MB', '13631488-13MB' => '13MB', '14680064-14MB' => '14MB', '15728640-15MB' => '15MB', '20971520-20MB' => '20MB', '26214400-25MB' => '25MB' ),
				'default'  => '2097152-2MB',
            ),
			array(
                'id'       => 'allow_tax_condition',
                'type'     => 'switch',
                'title'    => __( 'Display Condition Taxonomy', 'carspot-rest-api' ),
                'default'  => true,
            ),
			array(
                'id'       => 'allow_tax_warranty',
                'type'     => 'switch',
                'title'    => __( 'Display Warranty Taxonomy', 'carspot-rest-api' ),
                'default'  => true,
            ),
			array(
                'id'       => 'allow_lat_lon',
                'type'     => 'switch',
                'title'    => __( 'Latitude & Longitude', 'carspot-rest-api' ),
				'desc'     => __( 'This will be display on ad post page for pin point map', 'carspot-rest-api' ),
                'default'  => true,
            ),
			array(
                'id'       => 'sb_default_lat',
                'type'     => 'text',
                'title'    => __( 'Latitude', 'carspot-rest-api' ),
                'subtitle' => __( 'for default map.', 'carspot-rest-api' ),
				'required' => array( 'allow_lat_lon', '=', true ),
                'default'  => '40.7127837' ,
            ),
			array(
                'id'       => 'sb_default_long',
                'type'     => 'text',
                'title'    => __( 'Longitude', 'carspot-rest-api' ),
                'subtitle' => __( 'for default map.', 'carspot-rest-api' ),
				'required' => array( 'allow_lat_lon', '=', true ),
                'default'  => '-74.00594130000002' ,
            ),
			array(
                'id'       => 'allow_price_type',
                'type'     => 'switch',
                'title'    => __( 'Price Type', 'carspot-rest-api' ),
				'desc'     => __( 'Display Price type option.', 'carspot-rest-api' ),
                'default'  => true,
            ),
		array(
				'id'       => 'sb_price_types',
				'type'     => 'select',
				'options'     => array( 
					'Fixed' => __( 'Fixed', 'carspot-rest-api' ),
					'Negotiable' => __( 'Negotiable', 'carspot-rest-api' ),
					'on_call' => __( 'Price on call', 'carspot-rest-api' ),
					'auction' => __( 'Auction', 'carspot-rest-api' ),
					'free' => __( 'Free', 'carspot-rest-api' ), 
					'no_price' => __( 'No price', 'carspot-rest-api' ),
					),
				'multi'    => true,
				'sortable' => true,
				'title'    => __( 'Price Types', 'carspot-rest-api' ),
				'default'  => array( ),
			),
				array(
					'id'       => 'sb_price_types_more',
					'type'     => 'text',
					'title'    => __( 'Custom Price Type', 'carspot-rest-api' ),
					'desc'    => __( 'Separated by | like option 1|option 2', 'carspot-rest-api' ),
					'default'  => '',
				),
				
			array(
                'id'       => 'sb_ad_update_notice',
                'type'     => 'text',
                'title'    => __( 'Update Ad Notice', 'carspot-rest-api' ),
				'default'  => 'Hey, be careful you are updating this AD.',
            ),
			array(
                'id'       => 'allow_featured_on_ad',
                'type'     => 'switch',
                'title'    => __( 'Allow make featured ad', 'carspot-rest-api' ),
				'subtitle' => __( 'on ad post.', 'carspot-rest-api' ),
                'default'  => true,
            ),
			array(
                'id'       => 'sb_feature_desc',
                'type'     => 'textarea',
                'title'    => __( 'Featured ad description', 'carspot-rest-api' ),
				'subtitle' => __( 'on ad post.', 'carspot-rest-api' ),
				'required' => array( 'allow_featured_on_ad', '=', true ),
				'default'  => 'Featured AD has more attention as compare to simple ad.',
            ),
			
			array(
                'id'       => 'bad_words_filter',
                'type'     => 'textarea',
                'title'    => __( 'Bad Words Filter', 'carspot-rest-api' ),
				'subtitle' => __( 'comma separated', 'carspot-rest-api' ),
				'placeholder'   => __( 'word1,word2', 'carspot-rest-api' ),
				'desc'     => __( 'This words will be removed from AD Title and Description', 'carspot-rest-api' ),
				'default'  => '',
            ),
			array(
                'id'       => 'bad_words_replace',
                'type'     => 'text',
                'title'    => __( 'Bad Words Replace Word', 'carspot-rest-api' ),
				'desc'     => __( 'This words will be replace with above bad words list from AD Title and Description', 'carspot-rest-api' ),
				'default'  => '',
            ),



			)
		) );

    Redux::setSection( $opt_name, array(
        'title'      => __( "Ad View Settings", "carspot-rest-api" ),
        'id'         => 'api_ad_view_settings',
        'desc'       => '',
        'icon' => 'el el-home',
		'subsection' => true,
        'fields'     => array(
		
            array(
                'id'       => 'api_ad_details_info_column',
                'type'     => 'button_set',
                'title'    => __( 'Info Columns', 'carspot-rest-api' ),
				'subtitle'    => __( 'On ad details page', 'carspot-rest-api' ),
                'desc'     => __( 'Select number of info columns', 'carspot-rest-api' ),
                'options'  => array(
                    '1' => __( '1 Column', 'carspot-rest-api' ),
                    '2' => __( '2 Columns', 'carspot-rest-api' ),
                ),
                'default'  => '2'
            ),

			array(
                'id'       => 'related_ads_on',
                'type'     => 'switch',
                'title'    => __( 'Related Ads', 'carspot-rest-api' ),
                'default'  => true,
            ),
			array(
                'id'       => 'sb_related_ads_title',
				'required' 		=> array( 'related_ads_on', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Related Ads Section Title', 'carspot-rest-api'),
				'default'  => 'Similiar Ads',
            ),
            array(
                'id'            => 'api_ad_details_related_posts',
				'required' 		=> array( 'related_ads_on', '=', true ),
                'type'          => 'slider',
                'title'         => __( 'Related Posts', 'carspot-rest-api' ),
				'subtitle'    => __( 'On ad details page', 'carspot-rest-api' ),
                'desc'          => __( 'Select Number of related posts', 'carspot-rest-api' ),
                'default'       => 5,
                'min'           => 1,
                'step'          => 1,
                'max'           => 150,
                'display_value' => 'label'
            ),				
		
		
		)
	) );	
    Redux::setSection( $opt_name, array(
        'title'      => __( "Ad Search Settings", "carspot-rest-api" ),
        'id'         => 'api_ad_search_settings',
        'desc'       => '',
        'icon' => 'el el-home',
		'subsection' => true,
        'fields'     => array(
		
					
			array(
                'id'       => 'feature_on_search',
                'type'     => 'switch',
                'title'    => __( 'Featured Ads', 'carspot-rest-api' ),
                'default'  => true,
            ),
			array(
                'id'       => 'sb_search_ads_title',
				'required' 		=> array( 'feature_on_search', '=', true ),
                'type'     => 'text',
                'title'    => __( 'Featured Ads Section Title', 'carspot-rest-api' ),
				'default'  => 'Featured Ads',
            ),
            array(
                'id'            => 'search_related_posts_count',
				'required' 		=> array( 'feature_on_search', '=', true ),
                'type'          => 'slider',
                'title'         => __( 'Featured Posts', 'carspot-rest-api' ),
				'subtitle'    => __( 'On ad details page', 'carspot-rest-api' ),
                'desc'          => __( 'Select Number of featured posts', 'carspot-rest-api' ),
                'default'       => 5,
                'min'           => 1,
                'step'          => 1,
                'max'           => 150,
                'display_value' => 'label'
            ),				
		
		
		)
	) );	


	Redux::setSection( $opt_name, array(
			'title'      => __( 'Ad Rating Settings', 'carspot-rest-api' ),
			'id'         => 'sb_ad_rating_settings',
			'desc'       => '',
			'icon' => 'el el-cogs',
			'subsection' => true,
			'fields'     => array(
			
				array(
					'id'       => 'sb_ad_rating',
					'type'     => 'switch',
					'title'    => __( 'Rating on ad', 'carspot-rest-api' ),
					'default'  => false,
				),
				array(
					'id'       => 'sb_update_rating',
					'type'     => 'switch',
					'title'    => __( 'Allow update the rating', 'carspot-rest-api' ),
					'required' => array( 'sb_ad_rating', '=', array( true) ),
					'default'  => false,
				),
			  array(
					'id'       => 'sb_ad_rating_title',
					'type'     => 'text',
					'title'    => __( 'Rating section title', 'carspot-rest-api' ),
					'required' => array( 'sb_ad_rating', '=', array( true) ),
					'default'  => 'Rating & Reviews',			
				),
				array(
					'id'       => 'sb_rating_email_author',
					'type'     => 'switch',
					'title'    => __( 'Email to Author on rating', 'carspot-rest-api' ),
					'required' => array( 'sb_ad_rating', '=', array( true) ),
					'default'  => false,
				),
				array(
					'id'       => 'sb_rating_reply_email',
					'type'     => 'switch',
					'title'    => __( 'Email to Author on rating', 'carspot-rest-api' ),
					'required' => array( 'sb_ad_rating', '=', array( true) ),
					'default'  => false,
				),
				
				array(
					'id'          => 'sb_rating_max',
					'type'        => 'spinner',
					'title'    => __( 'Rating show at most', 'carspot-rest-api' ),
					'required' => array( 'sb_ad_rating', '=', array( true) ),
					'default' => '5',
					'min'     => '1',
					'step'    => '1',
					'max'     => '50',
				),
	
			
			)
		) );

	/*Only show if woocommerce plugin activated */
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	{	
		Redux::setSection( $opt_name, array(
			'title'      => __( "Woo Products", "carspot-rest-api" ),
			'id'         => 'api_woo_products_settings',
			'desc'       => '',
			'icon' => 'el el-list-alt',
			'fields'     => array(
			
						
				array(
					'id'       => 'api_woo_products_multi',
					'type'     => 'select',
					'data'     => 'post',
					'args' => array(
						'post_type' => array( 'product' ),
					),				
					'multi'    => true,
					'sortable' => true,
					'title'    => __( 'Select Products', 'carspot-rest-api' ),
				),	
				
				
            array(
                'id'   => 'opt-info-select',
                'type' => 'info',
                'desc' => __( 'Select Payment Packages', 'carspot-rest-api' ),
            ),						

            array(
				'required' 		=> array( 'api-is-buy-android-app', '=', true ),
                'id'       => 'api-payment-packages',
                'type'     => 'select',
                'multi'    => true,
				'sortable' => true,
                'title'    => __( 'Payment Methods For Android App', 'carspot-rest-api' ),
                'desc'     => __( 'Select the payment methods you want to add.', 'carspot-rest-api' ),
                'options'  => carspotAPI_payment_types(),
                'default'  => array( 'stripe' )
            ),
			
            array(
				'required' 		=> array( 'api-is-buy-ios-app', '=', true ),
                'id'       => 'api-payment-packages-ios',
                'type'     => 'select',
                'multi'    => true,
				'sortable' => true,
                'title'    => __( 'Payment Methods IOS App', 'carspot-rest-api' ),
                'desc'     => __( 'Note ios only uses InApp Purchase', 'carspot-rest-api' ),
                'options'  => carspotAPI_payment_types('', 'ios'),
                'default'  => array( 'app_inapp' )
            ),			
			
			)
		) );	
	}
	
	
 Redux::setSection( $opt_name, array(
        'title'      => __( 'Users', "carspot-rest-api" ),
        'id'         => 'api_users_screen',
        'desc'       => '',
        'icon' => 'el el-user',
        'fields'     => array(
		
		 array(
                'id'       => 'sb_phone_verification',
				'type'     => 'switch',
                'title'    => __( 'Phone verfication', 'carspot-rest-api' ),
                'default'  => false,
				'desc'		=> __( 'If phone verification is on then system put verified batch to ad details on number so other can see this number is verified.', 'carspot-rest-api' ),
            ),
			array(
                'id'       => 'sb_resend_code',
                'type'     => 'text',
                'title'    => __( 'Resend security code', 'carspot-rest-api' ),
				'subtitle'    => __( 'In seconds', 'carspot-rest-api' ),
				'desc'     => __( 'Only integer value without spaces, 30 means 30-seconds', 'carspot-rest-api' ),
				'required' => array( 'sb_phone_verification', '=', array( '1' ) ),
				'default'  => 30,
            ),
		 array(
                'id'       => 'sb_change_ph',
				'type'     => 'switch',
                'title'    => __( 'Change phone number while ad posting.', 'carspot-rest-api' ),
                'desc'    => __( 'If off then only user profile number will be display and can not be changeable.', 'carspot-rest-api' ),
                'default'  => true,
            ),		

			
		 array(
                'id'       => 'sb_new_user_email_to_admin',
				'type'     => 'switch',
                'title'    => __( 'New User Email to Admin', 'carspot-rest-api' ),
                'default'  => true
            ),			
		
		 array(
                'id'       => 'sb_new_user_email_to_user',
				'type'     => 'switch',
                'title'    => __( 'Welcome Email to User', 'carspot-rest-api' ),
                'default'  => true
            ),	

		 array(
                'id'       => 'sb_new_user_email_verification',
				'type'     => 'switch',
                'title'    => __( 'New user email verification', 'carspot-rest-api' ),
                'default'  => false,
				'desc'		=> __( 'If verfication on then please update your new user email template by verification link.', 'carspot-rest-api' ),
            ),	
		
		
            array(
                'id'       => 'sb_new_user_register_policy',
                'type'     => 'select',
                'data'     => 'pages',			
                'multi'    => false,
				'sortable' => false,
                'title'    => __( 'Select Page', 'carspot-rest-api' ),
				'subtitle'    => __( 'Terms and Conditions', 'carspot-rest-api'),
				'desc'     => __( 'Specially for General Data Protection Regulation (GDPR)', 'carspot-rest-api' ),
            ),	
        	 array(
                'id'       => 'sb_new_user_register_checkbox_text',
                'type'     => 'text',
                'title'    => __( 'Term and Condition Text', 'carspot-rest-api' ),
				'default'  => '',
				'desc'  => __( 'Terms and Condition text next to checkbox. Leave empty if you want to show default text', 'carspot-rest-api' ),	
            ),
		 	array(
                'id'       => 'sb_new_user_delete_option',
				'type'     => 'switch',
                'title'    => __( 'Show Delete button', 'carspot-rest-api' ),
                'default'  => false,
				'desc'		=> __( 'Show delete button on user profile. Due to General Data Protection Regulation (GDPR) policy. Note: This will delete the entire data from the database and can not be recover again.', 'carspot-rest-api' ),
            ),
			
         array(
		 		'required' => array( 'sb_new_user_delete_option', '=', true ),
                'id'       => 'sb_new_user_delete_option_text',
                'type'     => 'text',
                'title'    => __( 'Delete Popup Text', 'carspot-rest-api' ),
				'default'  => 'Are you sure you want to delete the account.',
				'desc'  => __( 'Popup text after delete link clicked.', 'carspot-rest-api' ),	
            ),	
			
		 	array(
                'id'       => 'sb_user_allow_block',
				'type'     => 'switch',
                'title'    => __( 'Block User', 'carspot-rest-api' ),
                'default'  => false,
				'desc'		=> __( 'Allow users to block anyone and stop seeing his ads.', 'carspot-rest-api' ),
            ),																
						
		)
	
	));		
	
   
	
	/* ------------------Email Templates Settings ----------------------- */
    Redux::setSection( $opt_name, array(
        'title'      => __( 'Email Templates', 'carspot-rest-api' ),
        'id'         => 'sb_email_templates',
        'desc'       => '',
        'icon' => 'el el-pencil',
        'fields'     => array(
          array(
                'id'       => 'sb_msg_subject_on_new_ad',
                'type'     => 'text',
                'title'    => __( 'New Ad email subject', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %ad_owner% , %ad_title% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => 'You have new Ad - carspot',				
            ),
          array(
                'id'       => 'sb_msg_from_on_new_ad',
                'type'     => 'text',
                'title'    => __( 'New Ad FROM', 'carspot-rest-api' ),
				'desc'     => __( 'FROM: NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => 'From: '.get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'sb_msg_on_new_ad',
                'type'     => 'editor',
                'title'    => __( 'New Ad Posted Message', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %ad_owner% , %ad_title% , %ad_link% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff"><br/>A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>Admin,</b></span></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">You\'ve new AD;</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Title: %ad_title%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Link: <a href="%ad_link%">%ad_title%</a></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Poster: %ad_owner%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',				
            ),
          array(
                'id'       => 'sb_message_subject_on_new_ad',
                'type'     => 'text',
                'title'    => __( 'New Message email subject', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %ad_title% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => 'You have new message - carspot',				
            ),
          array(
                'id'       => 'sb_message_from_on_new_ad',
                'type'     => 'text',
                'title'    => __( 'New Message FROM', 'carspot-rest-api' ),
				'desc'     => __( 'FROM: NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => 'From: '.get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'sb_message_on_new_ad',
                'type'     => 'editor',
                'title'    => __( 'New Message template', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %message% , %sender_name%, %ad_title% , %ad_link% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff"><br/>A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>Admin,</b></span></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">You\'ve new Message;</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Title: %ad_title%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Link: <a href="%ad_link%">%ad_title%</a></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Sender: %sender_name%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Message: %message%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',				
            ),
          array(
                'id'       => 'sb_report_ad_subject',
                'type'     => 'text',
                'title'    => __( 'Ad report email subject', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %ad_title% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => 'Ad Reported - carspot',				
            ),
          array(
                'id'       => 'sb_report_ad_from',
                'type'     => 'text',
                'title'    => __( 'Ad report email FROM', 'carspot-rest-api' ),
				'desc'     => __( 'FROM: NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => 'From: '.get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'sb_report_ad_message',
                'type'     => 'editor',
                'title'    => __( 'Ad Report template', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %ad_owner% , %ad_title% , %ad_link% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>Admin,</b></span></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Below Ad is reported.</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Title: %ad_title%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Link: <a href="%ad_link%">%ad_title%</a></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">Ad Poster: %ad_owner%</p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',				
            ),
          array(
                'id'       => 'sb_forgot_password_subject',
                'type'     => 'text',
                'title'    => __( 'Reset Password email subject', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => 'Reset Password - carspot',				
            ),
          array(
                'id'       => 'sb_forgot_password_from',
                'type'     => 'text',
                'title'    => __( 'Reset Password email FROM', 'carspot-rest-api' ),
				'desc'     => __( 'FROM: NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'sb_forgot_password_message',
                'type'     => 'editor',
                'title'    => __( 'Reset Password template', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %user% , %reset_link% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %user%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>Please use this below link to reset your password.<br/>%reset_link%<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',				
            ),
          array(
                'id'       => 'sb_new_rating_subject',
                'type'     => 'text',
                'title'    => __( 'Rating email subject', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => 'New Rating - carspot',				
            ),
          array(
                'id'       => 'sb_new_rating_from',
                'type'     => 'text',
                'title'    => __( 'New rating email FROM', 'carspot-rest-api' ),
				'desc'     => __( 'FROM: NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => 'From: '.get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'sb_new_rating_message',
                'type'     => 'editor',
                'title'    => __( 'New rating template', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %receiver% , %rator% , %rating% , %comments% , %rating_link% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %receiver%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>You got new rating;User who rated: %rator%Stars: %rating%Link: %rating_link%Comments: %comments%<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',				
            ),
          array(
                'id'       => 'sb_new_bid_subject',
                'type'     => 'text',
                'title'    => __( 'Bid email subject', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => 'New Bid - carspot',				
            ),
          array(
                'id'       => 'sb_new_bid_from',
                'type'     => 'text',
                'title'    => __( 'Bid email FROM', 'carspot-rest-api' ),
				'desc'     => __( 'FROM: NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => 'From: '.get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'sb_new_bid_message',
                'type'     => 'editor',
                'title'    => __( 'Bid email template', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %receiver% , %bidder% , %bid% , %comments% , %bid_link% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %receiver%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>You got new Bid;Bidder: %bidder%Bid: %bid%Link: %bid_link%Comments: %comments%<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',				
            ),


          array(
                'id'       => 'sb_new_user_admin_message_subject',
                'type'     => 'text',
                'title'    => __( 'New user email template subject for Admin', 'carspot-rest-api' ),
                'default'  => 'New User Registration',				
            ),
          array(
                'id'       => 'sb_new_user_admin_message_from',
                'type'     => 'text',
                'title'    => __( 'New user email FROM for Admin', 'carspot-rest-api' ),
				'desc'     => __( 'NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'sb_new_user_admin_message',
                'type'     => 'editor',
                'title'    => __( 'New user email template for Admin', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %display_name%, %email% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello Admin</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>New user has registered on your site %site_name%;Name: %display_name%Email: %email%&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',				
            ),
			
			
          array(
                'id'       => 'sb_new_user_message_subject',
                'type'     => 'text',
                'title'    => __( 'New user email template subject', 'carspot-rest-api' ),
                'default'  => 'New User Registration',				
            ),
          array(
                'id'       => 'sb_new_user_message_from',
                'type'     => 'text',
                'title'    => __( 'New user email FROM', 'carspot-rest-api' ),
				'desc'     => __( 'NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'sb_new_user_message',
                'type'     => 'editor',
                'title'    => __( 'New user email template', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %user_name% %display_name% %verification_link% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %display_name%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p>Welcome to %site_name%.<br/>Your details are below;<br/>Username: %user_name%<br/>&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',				
            ),
			
			
          array(
                'id'       => 'sb_active_ad_email_subject',
                'type'     => 'text',
                'title'    => __( 'Ad activation subject', 'carspot-rest-api' ),
                'default'  => 'You Ad has been activated.',				
            ),
          array(
                'id'       => 'sb_active_ad_email_from',
                'type'     => 'text',
                'title'    => __( 'Ad activation FROM', 'carspot-rest-api' ),
				'desc'     => __( 'NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'sb_active_ad_email_message',
                'type'     => 'editor',
                'title'    => __( 'Ad activation message', 'carspot-rest-api' ),
                'desc'     => __( '%site_name% , %user_name%, %ad_title% ,  %ad_link% will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td><td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"><div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"><table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"><tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff">A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %user_name%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p><br/>You ad has been activated.<br/>Details are below;<br/>Ad Title: %ad_title%<br/>Ad Link: %ad_link%<br/>&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',				
            ),



	  array(
                'id'       => 'ad_rating_email_subject',
                'type'     => 'text',
                'title'    => __( 'Rating email subject', 'carspot-rest-api' ),
                'default'  => 'You have a new rating',				
            ),
          array(
                'id'       => 'ad_rating_email_from',
                'type'     => 'text',
                'title'    => __( 'Rating FROM', 'carspot-rest-api' ),
				'desc'     => __( 'NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'ad_rating_email_message',
                'type'     => 'editor',
                'title'    => __( 'Rating message', 'carspot-rest-api' ),
				'args'   => array(
						'teeny'            => true,
						'textarea_rows'    => 10,
						'wpautop' => false,
					),
                'desc'     => '%site_name%, %ad_title%, %ad_link%, %rating, %rating_comments%, %author_name%'.  __( 'will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody> <tr> <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td> <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"> <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"> <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"> <tbody> <tr> <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"> <table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"> <tbody> <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"> <td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff"> A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello %author_name%</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p> <br />You have new rating, details are below; <br /> Rating: %rating% <br />Comments: %rating_comments% <br /> Ad Title: %ad_title% <br /> Ad Link: %ad_link% <br />&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp; </div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;', ),
			
			
		  array(
                'id'       => 'ad_rating_reply_email_subject',
                'type'     => 'text',
                'title'    => __( 'Rating reply email subject', 'carspot-rest-api' ),
                'default'  => 'You got a reply on your rating',				
            ),
          array(
                'id'       => 'ad_rating_reply_email_from',
                'type'     => 'text',
                'title'    => __( 'Rating reply FROM', 'carspot-rest-api' ),
				'desc'     => __( 'NAME valid@email.com is compulsory as we gave in default.', 'carspot-rest-api' ),
                'default'  => get_bloginfo( 'name' ).' <'.get_option( 'admin_email' ).'>',				
            ),
          array(
                'id'       => 'ad_rating_reply_email_message',
                'type'     => 'editor',
                'title'    => __( 'Rating reply message', 'carspot-rest-api' ),
				'args'   => array(
						'teeny'            => true,
						'textarea_rows'    => 10,
						'wpautop' => false,
					),
               'desc'     => '%site_name%, %ad_title%, %ad_link%, %rating%, %rating_comments%, %author_name%, %author_reply% '. __( 'will be translated accordingly.', 'carspot-rest-api' ),
                'default'  => '<table class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" border="0" cellspacing="0" cellpadding="0"> <tbody> <tr> <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td> <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto !important;"> <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;"> <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;"> <tbody><tr><td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"> <tbody> <tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"> <td class="alert" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #000; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #fff; margin: 0; padding: 20px;" align="center" valign="top" bgcolor="#fff"> A Designing and development company</td></tr><tr><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"> <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><span style="font-family: sans-serif; font-weight: normal;">Hello,</span><span style="font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;"><b>,</b></span></p> <br /> You have reply on your rating, details are below; <br /> Ad Title: %ad_title% <br />Ad Link: %ad_link% <br /> Ad Author: %author_name% <br />Author reply: %author_reply% <br />Your given rating: %rating% <br />Your comments: %rating_comments% <br />&nbsp;<p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>Thanks!</strong></p><p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">ScriptsBundle</p></td></tr></tbody></table></td></tr></tbody></table><div class="footer" style="clear: both; padding-top: 10px; text-align: center; width: 100%;"><table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="content-block powered-by" style="font-family: sans-serif; font-size: 12px; vertical-align: top; color: #999999; text-align: center;"><a style="color: #999999; text-decoration: underline; font-size: 12px; text-align: center;" href="https://themeforest.net/user/scriptsbundle">Scripts Bundle</a>.</td></tr></tbody></table></div>&nbsp;</div></td><td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"></td></tr></tbody></table>&nbsp;',),








        )
    ) );	