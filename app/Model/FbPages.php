<?php

//Load classes with namespacing
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;
use Facebook\GraphUser;

/* FbPage Model
 * 
 * 
 * @copyright Ondi Gusho.
 */

class FbPages extends AppModel{
    //Set table
    public $useTable = 'fcb_pages';
    
     //fb vars
    public $fbAPId = "";
    public $fbAPSecret = "";
    
    
   /* Fb page delete 
     * 
    */ 
    public function deletePages($user_id,$fcb_uid,$id=NULL){
        //if id = null delete all pages from this user
        if(!isset($id)){
            $condition = array('uid'=>$user_id,'fcb_uid'=>$fcb_uid);
        }
        else{
            $condition = array('uid'=>$user_id,'fcb_uid'=>$fcb_uid,'uid'=>$user_id,'id'=>$id);
        }
        //if any
        if($this->hasAny(array($condition))){
            //Delete
            if($this->deleteAll(array($condition))){
                    $return = array('status'=>'success','message'=>'Page deleted.');
                }else{
                    $return = array('status'=>'error','message'=>'Something went wrong! Please try again.');
                }
            }
        else{
            $return = array('status'=>'error','message'=>'Something went wrong! Please try again.');
        }
        //return
        return $return;
    }
    
    /* post
     * Will post data of facebook page profile 
     * 
    */ 
    public function post($data,$fcb_uid,$fcb_page_id,$user_id){
        //what kind of post ? 
        //link ? picture ? or simple message
        //check if is a token set
        try {
            //Get current token,
            $token = $this->ReadToken($fcb_uid,$fcb_page_id,$user_id);
            //build session
            $fbSession = new FacebookSession($token);
            //Will Build request depending on data submited by user
            //$send = $this->buildRequest($data);
            //check what fields/submit type 
            if ($data['Document']['submittedfile']['error']=='0'){
                //build msg
                if (isset($data['urlsubmit'])){
                    //concatenate url on message
                    $msg = $data['urlsubmit'].' '.$data['message'];
                }
                else{
                    //just message with picture
                    $msg = $data['message'];
                }
                //there is a file type submit
                $response = (new FacebookRequest(
                $fbSession, 'POST', '/'.$fcb_page_id.'/photos', array(
                  'source' => new CURLFile($data['Document']['submittedfile']['tmp_name'], 'image/png'),
                  'message' => $msg
                )
              ))->execute()->getGraphObject();
            }else{
                //build array
                if (isset($data['urlsubmit'])){
                    //concatenate url on message
                    $send = array(
                      'link' => $data['urlsubmit'],
                      'message' => $data['message']
                    );
                }
                else{
                    //just message with picture
                    $send = array(
                      'message' => $data['message']
                    );
                } 
                //post      
                $response = (new FacebookRequest(
                    $fbSession, 'POST', '/'.$fcb_page_id.'/feed', $send
                  ))->execute()->getGraphObject();
            }
                //get this post id
                $post_id = $response->getProperty('id');
                //success. Return post id
                $return = array('status'=>'success','post_id'=>$post_id);
                
            } catch(FacebookRequestException $e) {
                //catch
                $error= $e->getMessage();
                $return = array('status'=>'error','message'=>'Facebook message not posted! '.$error);
            }
            //return 
            return $return;
    }
    
    /* cron post
     * Will post data of facebook page profile 
     * 
    */ 
    public function cronpost($post,$fcb_uid,$fcb_page_id){
        //Set fb Session default 
        FacebookSession::setDefaultApplication($this->fbAPId, $this->fbAPSecret);
        
        //what kind of post ? 
        //link ? picture ? or simple message
        //check if is a token set
        $user_id = $post['SocialPosts']['uid'];
        
        try {
            //Get current token,
            $token = $this->ReadToken($fcb_uid,$fcb_page_id,$user_id);
            
            //build session
            $fbSession = new FacebookSession($token);
            
            //Will Build request depending on data submited by user
            //$send = $this->buildRequest($data);
            //check what fields/submit type 
           if ($post['SocialPosts']['file'] != 'na') {
                //file path 
                $filepath = WWW_ROOT . $post['SocialPosts']['file'];
                //build msg
                if ($post['SocialPosts']['link'] != 'na') {
                    //concatenate url on message
                    $msg = $post['SocialPosts']['link'] . ' ' . $post['SocialPosts']['message'];
                } else {
                    //just message with picture
                    $msg = $post['SocialPosts']['message'];
                }
                //there is a file type submit
                $response = (new FacebookRequest(
                $fbSession, 'POST', '/'.$fcb_page_id.'/photos', array(
                  'source' => new CURLFile($filepath, 'image/png'),
                  'message' => $msg
                )
              ))->execute()->getGraphObject();
            }else{
                //build array
                if ($post['SocialPosts']['link']!='na'){
                    //concatenate url on message
                    $send = array(
                      'link' => $post['SocialPosts']['link'],
                      'message' => $post['SocialPosts']['message']
                    );
                }
                else{
                    //just message
                    $send = array(
                      'message' => $post['SocialPosts']['message']
                    );
                } 
                //post      
                $response = (new FacebookRequest(
                    $fbSession, 'POST', '/'.$fcb_page_id.'/feed', $send
                  ))->execute()->getGraphObject();
            }
                //get this post id
                $post_id = $response->getProperty('id');
                //success. Return post id
                $return = array('status'=>'success','post_id'=>$post_id);
                
            } catch(FacebookRequestException $e) {
                //catch
                $error= $e->getMessage();
                $return = array('status'=>'error','message'=>'Facebook message not posted! '.$error);
            }
            //return 
            return $return;
    }
    
    /* ReadToken
     * Will read available token for page 
     * 
    */ 
    public function ReadToken($fcb_uid,$fcb_page_id,$user_id){
        //Check and read
        if($this->hasAny(array('uid'=>$user_id,'fcb_page_id'=>$fcb_page_id,'fcb_uid'=>$fcb_uid))){
            //get type and id.
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('uid'=>$user_id,'fcb_page_id'=>$fcb_page_id,'fcb_uid'=>$fcb_uid)),
                'fields' => array('access_token'),
                //'order' => array('date'=>'desc')
            );
            $record = $this->find('first',$get);
            //token
            return $record['FbPages']['access_token'];
        }else{
            //return false. Some type of security
            return FALSE;
        }
    }
    
}

?>