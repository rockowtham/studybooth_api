<?php
/*----- 	Woo Products Starts Here	 -----*/
add_action( 'rest_api_init', 'carspotAPI_woocommerce_hook', 0 );
function carspotAPI_woocommerce_hook() {
    register_rest_route(
        		'carspot/v1', '/packages/', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => 'carspotAPI_woocommerce_get',
				'permission_callback' => function () { return carspotAPI_basic_auth();  },
        	)
    );
} 
if (!function_exists('carspotAPI_woocommerce_get'))
{
	function carspotAPI_woocommerce_get( $request )
	{ 
		$user = wp_get_current_user();	
		$user_id = $user->data->ID;		
		$response = array( 'success' => $success, 'data' => $data, 'message' => $message, 'extra' => $extra );
		return $response;
	}
}