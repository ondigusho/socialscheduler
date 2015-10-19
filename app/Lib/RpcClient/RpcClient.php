<?php
/* Rpc_Client class for api calls 
 * http get format
 * 
 * @copyright Ondi Gusho.
 */
class RpcClient{
    
    /* Method _call_get
     * Api http request
     * Parameters url
     *  
    */
    public static function _call_get($url){
        $curl = curl_init();
        // Set curl opt as array
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_RETURNTRANSFER => true,
        ));
        // Run curl
        $response = curl_exec($curl);
        //Check for errors 
        if(curl_errno($curl)){
            $errorMessage = curl_error($curl); 
            // Log error message .
            $return =  array('success'=>FALSE,'error'=>$errorMessage);
        } else {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // Log success
            $return = array('success'=>TRUE,'response'=>$response,'status'=>$statusCode);
        }
        //close request
        curl_close($curl);
        //Return
        return $return;
    }
}

?>
