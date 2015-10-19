<?php
/* SaveSite class  
*/
//require
require_once 'RpcClient/RpcClient.php';
require_once('UrlToAbsolute/url_to_absolute.php');

class ScrapSite{
    //Class var
    public static $url;
    public static $dom;
   /* Run method  
    * 
    * 
   */
    public static function run($url){
        //Set used vars
        self::$url = $url;
        self::$dom = new DOMDocument();
        libxml_use_internal_errors(true);
        //test if 200 and valid
        if (filter_var(self::$url, FILTER_VALIDATE_URL)){
            //curl call
            $IndexResult = Rpc_Client::_call_get(self::$url);
            //If request 200 
            if ($IndexResult['status']=='200'){
                    //get html
                    $html = $IndexResult['response'];
                    self::$dom->loadHTML($html);
                    libxml_clear_errors();
                    self::$dom->preserveWhiteSpace = false;
                    //remove javascript
                    while (($r = self::$dom->getElementsByTagName('script')) && $r->length) {
                            $r->item(0)->parentNode->removeChild($r->item(0));
                    }
                    //get images found
                    $images = self::getElement('img', 'src', 'img');
                    $titlelist = self::$dom->getElementsByTagName("title");
                    if($titlelist->length > 0){
                        $title = $titlelist->item(0)->nodeValue;
                    }
                    //build return array 
                    return array('status'=>'success','images'=>$images,'title'=>$title);
            }
            else{
                //return object; must have an error element
                return $IndexResult;
            }
        }else{
            //error
            $return =  array('success'=>FALSE,'error'=>'The site you are trying to parse does not exist or the URL used is not correct!');
            //return 
            return $return;
        }
    }
    
    /* getHtml method  
    * 
    * 
   */
    public static function getHtml($url){
        //Set used vars
        self::$url = $url;
        self::$dom = new DOMDocument();
        //test if 200 and valid
        if (filter_var(self::$url, FILTER_VALIDATE_URL)){
            //curl call
            $IndexResult = Rpc_Client::_call_get(self::$url);
            //If request 200 
            if ($IndexResult['status']=='200'){
                    $html = $IndexResult['response'];
                    self::$dom->loadHTML($html);
                    libxml_clear_errors();
                    self::$dom->preserveWhiteSpace = false;
                    //remove javascript...
                    while (($r = self::$dom->getElementsByTagName('script')) && $r->length) {
                            $r->item(0)->parentNode->removeChild($r->item(0));
                    }
                    $domHTML = self::$dom->saveHTML();
                    return array('status'=>'success','html'=>$domHTML);
            }
            else{
                //return object; must have an error element
                return array('status'=>'error','message'=>'The site you are trying to parse does not exist or the URL used is not correct!');
            }
        }else{
            //error
            $return =  array('status'=>'error','message'=>'The site you are trying to parse does not exist or the URL used is not correct!');
            //return 
            return $return;
        }
    }
    
    /* getForView on social posts method  
     * 
    */
    public static function getForView($url){
        //run 
        $result = self::run($url);
        //check return 
        if(isset($result['error'])){
            return $result;
            exit(1);
        }
        if (isset($result['success']) && $result['success']==FALSE){
            return $result;
            exit(1);
        }
        //get images found
        $images = self::getElement('img', 'src', 'img');
        $titlelist = self::$dom->getElementsByTagName("title");
        if($titlelist->length > 0){
            $title = $titlelist->item(0)->nodeValue;
        }
        //build return array 
        return array('status'=>'success','images'=>$images,'title'=>$title);
    }
    /* getElement method  
     * 
    */
    public static function getElement($tagname,$attribute,$type){
        $return_elements = array();
        //get elements
        $elements = self::$dom->getElementsByTagName($tagname);
        //start counter
        $count = 0;
        foreach ($elements as $element) {
            //get path
            $href = $element->getAttribute($attribute);
            //if is an http status
            if (Rpc_Client::_check_status($href)=='200'){
                //it is an absolute path
                $absolute_url = $href;
            }
            else{
                //set absolute path
                $absolute_url =  url_to_absolute(self::$url, $element->getAttribute($attribute));
            }
            //Test again if not 200 do nothing !
            //This removes unused css,objects etc or systems that don't allow CURL calls
            if(Rpc_Client::_check_status($absolute_url)){
                //if type iframe
                array_push($return_elements, $absolute_url);
                $count++;
                //get only 3
                if ($count==1){
                    break;
                }
            }
        }
        //return
        return $return_elements;
    }
}//end of class
?>