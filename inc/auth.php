<?php /*Some Thing */
function carspotAPI_basic_auth()
{
	$pc = $cs = false;
	global $carspotAPI;	
	$response = '';
	if( CARSPOT_API_REQUEST_FROM == 'ios' )
	{	
		$pcode = ( isset( $carspotAPI['appKey_pCode_ios'] ) && $carspotAPI['appKey_pCode_ios'] != ""  ) ? $carspotAPI['appKey_pCode_ios'] : '';
		$ccode = ( isset( $carspotAPI['appKey_Scode_ios'] ) && $carspotAPI['appKey_Scode_ios'] != ""  ) ? $carspotAPI['appKey_Scode_ios'] : '';
	}
	else
	{
		$pcode = ( isset( $carspotAPI['appKey_pCode'] ) && $carspotAPI['appKey_pCode'] != ""  ) ? $carspotAPI['appKey_pCode'] : '';
		$ccode = ( isset( $carspotAPI['appKey_Scode'] ) && $carspotAPI['appKey_Scode'] != ""  ) ? $carspotAPI['appKey_Scode'] : '';
	}
	
	if( $pcode == "" || $ccode == "" )
	{
		return false;
	}
		
	foreach (getallheaders() as $name => $value) {
		if( $name == "Authorization" || $name == "authorization" )
		{
			$adminInfo =  base64_decode( trim( str_replace("Basic", "", $value) ) ); 	
		}
	    
		
		if(  ($name == "Purchase-Code" || $name == "purchase-code" )	&& $pcode == $value ){
		    
		    $pc = true;
		}
		if( ( $name == "Custom-Security" || $name == "custom-security" ) 	&& $ccode == $value ) {$cs =  true;}			
	}
            
			return ( $pc == true && $cs == true ) ? true : false;

}


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