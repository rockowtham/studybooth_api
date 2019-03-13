<?php 
 /**
 * Plugin Name: Carspot Apps API
 * Plugin URI: https://codecanyon.net/user/scriptsbundle
 * Description: This plugin is essential for the Carspot android and ios apps.
 * Version: 1.0.0
 * Author: Scripts Bundle
 * Author URI: https://codecanyon.net/user/scriptsbundle
 * License: GPL2
 * Text Domain: carspot-rest-api
 */
 	/* Get Theme Info If There Is */
	$my_theme = wp_get_theme();
	$my_theme->get( 'Name' );
	/*if( $my_theme->get( 'Name' ) != 'adforest' && $my_theme->get( 'Name' ) != 'adforest child' ) return;*/
	/*Load text domain*/
	add_action( 'plugins_loaded', 'carspot_rest_api_load_plugin_textdomain' );
	function carspot_rest_api_load_plugin_textdomain()
	{
		load_plugin_textdomain( 'carspot-rest-api', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}	
	/*For Demo Only, If It's production make if true */
	define( 'CARSPOT_API_ALLOW_EDITING', true );
		
 	/* Define Paths For The Plugin */
	define('CARSPOT_API_PLUGIN_FRAMEWORK_PATH', plugin_dir_path(__FILE__));	
	define('CARSPOT_API_PLUGIN_PATH', plugin_dir_path(__FILE__));	
	define('CARSPOT_API_PLUGIN_URL', plugin_dir_url(__FILE__));
	define('CARSPOT_API_PLUGIN_PATH_LANGS', plugin_dir_path(__FILE__). 'languages/' );	
	
	/*Theme Directry/Folder Paths */
	define( 'CARSPOT_API_THEMEURL_PLUGIN', get_template_directory_uri () . '/' );
	define( 'CARSPOT_API_IMAGES_PLUGIN', CARSPOT_API_THEMEURL_PLUGIN . 'images/');
	define( 'CARSPOT_API_CSS_PLUGIN', CARSPOT_API_THEMEURL_PLUGIN . 'css/');
	define( 'CARSPOT_API_JS_PLUGIN', CARSPOT_API_THEMEURL_PLUGIN . 'js/');

	/*Only check if plugin activate by theme */
	
 	$my_theme = wp_get_theme();
	if( $my_theme->get( 'Name' ) != 'carspot' && $my_theme->get( 'Name' ) != 'carspot child' )
	{
		require CARSPOT_API_PLUGIN_FRAMEWORK_PATH.'/tgm/tgm-init.php';	
	}
	

	/* Options Init */
	add_action('init', function(){
	  // Load the theme/plugin options
	  if (file_exists(dirname(__FILE__).'/inc/options-init.php')) {
		  require_once( dirname(__FILE__).'/inc/options-init.php' );
	  }
	});
	/*Added In Version 1.6.0 */
	if (!function_exists('getallheaders'))
	{
		function getallheaders()
		{
			   $headers = array();
	
		   foreach ($_SERVER as $name => $value)
		   {
			   if (substr($name, 0, 5) == 'HTTP_')
			   {
				   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			   }
		   }
		   
		   return $headers;
		}
	} 	
	$lang_code = $request_from = '';
	/*if(count(getallheaders()) > 0 )
	{
		foreach (getallheaders() as $name => $value)
		{
			
			if( ($name == "Adforest-Lang-Locale" || $name == "adforest-lang-locale") && $value != "" )
			{
				$lang_code =  $value;
			}
			
			if( ($name == "Adforest-Request-From" || $name == "adforest-request-from") && $value != "" ){
			{
				$request_from =  $value; 	
			}
			

		}
	}
	}
	*/
	
	if (!function_exists('carspotAPI_getSpecific_headerVal'))
	{
		function carspotAPI_getSpecific_headerVal($header_key = '')
		{
			$header_val = '';
			if(count(getallheaders()) > 0 )
			{
				foreach (getallheaders() as $name => $value)
				{
					if( ($name == $header_key || $name == strtolower($header_key)) && $value != "" )
					{
						$header_val =  $value;
						break;
					}
				}
			}
			return $header_val;
		}
	}
	
	if (!function_exists('carspotAPI_set_lang_locale'))
	{
		function carspotAPI_set_lang_locale( $locale )
		{	
			$lang = carspotAPI_getSpecific_headerVal('Carspot-Lang-Locale');
			$lang = ( $lang  != "" ) ? $lang  : 'en';
			return ( $lang === 'en') ? 'en_US' : $lang;
		}
	}	
	/*add_filter('locale','carspotAPI_set_lang_locale',10);*/
	$request_from = carspotAPI_getSpecific_headerVal('Carspot-Request-From');
	define( 'CARSPOT_API_REQUEST_FROM', $request_from );
	/*Added In Version 1.6.0 */
	 /*require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/inc/cpt.php';*/
	/* Include Function  */
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/functions.php';	
	/* Include Classes */
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/inc/basic-auth.php';	
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/inc/auth.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/inc/email-templates.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/inc/categories-images.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/inc/notifications.php';	
	
	
	/* Include Classes */

	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/settings.php';	
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/posts.php';	
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/home.php';	
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/users.php';	
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/index.php';	
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/ad_message.php';	
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/register.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/login.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/logout.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/ads.php';	
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/ad_post.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/bid.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/profile.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/woo-commerce.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/payment.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/push-notification.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/phone-verification.php';	
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/ad_rating.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/comparison.php';
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/reviews.php';
	/*Woo-Commerce Starts*/
	require CARSPOT_API_PLUGIN_FRAMEWORK_PATH . '/classes/woocommerce.php';
	
	/*Woo-Commerce ENds*/
/* Plugin Ends Here */