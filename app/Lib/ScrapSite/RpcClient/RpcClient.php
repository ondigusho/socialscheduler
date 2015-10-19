<?php
/* Rpc_Client class for api calls 
 * 
 */
class Rpc_Client{
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
            // No more than 30 sec on a website
            CURLOPT_TIMEOUT=>10,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_RETURNTRANSFER => true,
        ));
        // Run curl
        $response = curl_exec($curl);
        //Check for errors 
        if(curl_errno($curl)){
            $errorMessage = curl_error($curl);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            // Log error message .
            $return =  array('success'=>FALSE,'error'=>$errorMessage,'status'=>$statusCode);
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
    
    /* Method _check_status
     * Api http request
     * Parameters url
     *  
    */
    public static function _check_status($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch,CURLOPT_TIMEOUT,5);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $retcode;
    }
    /* Method _download_file
     * Api http request
     * Parameters file_url, save_to path
     *  
    */
    public static function _download_file($file_url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 0); 
	curl_setopt($ch,CURLOPT_URL,$file_url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$file_content = curl_exec($ch);
	curl_close($ch);
        //return file content 
        return $file_content;
    }
}

?>
