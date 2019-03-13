<?php
/*-----	Logout Starts Here	-----*/
add_action( 'rest_api_init', 'carspotAPI_logout_api_hooks', 0 );
function carspotAPI_logout_api_hooks() {
    register_rest_route(
        'wp/v2', '/logout/',
        array(
				'methods'  => 'GET',
				'callback' => 'carspotAPI_logoutMe',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
    register_rest_route(
        'carspot/v1', '/logout/',
        array(
				'methods'  => 'GET',
				'callback' => 'carspotAPI_logoutMe',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );	
}

if (!function_exists('carspotAPI_logoutMe'))
{
	function carspotAPI_logoutMe()
	{
		$logout = wp_logout_url();
		$response = array( 'success' => true, 'data' => __("You are logout successfully.", "carspot-rest-api") );
		return $response;	
	}
}