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

/* Social Model
 * 
 * 
 * @copyright Ondi Gusho.
 */

class Social extends AppModel {

    //Set table
    public $useTable = '';
    //fb vars
    public $fbAPId = "";
    public $fbAPSecret = "";

    /* FbUserProfile
     * Interact / Save user profile and access_token on database 
     * 
     */

    public function FbUserProfile($access_token, $user_profile, $user_id, $p_picture,$id=NULL) {
        //if this is an update
        if(isset($id)){
            $profile['id'] = $id;
        }
        //build array
        $profile['uid'] = $user_id;
        $profile['fcb_id'] = $user_profile->getId();
        $profile['access_token'] = $access_token;
        $profile['first_name'] = $user_profile->getFirstName();
        $profile['last_name'] = $user_profile->getLastName();
        $profile['full_name'] = $user_profile->getName();
        $profile['p_picture'] = $p_picture['url'];
        $profile['link'] = $user_profile->getLink();
        $profile['gender'] = $user_profile->getGender();
        $profile['timezone'] = $user_profile->getTimezone();
        //Save
        if ($this->save($profile)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /* post
     * Will post data of facebook user profile 
     * 
     */

    public function post($data, $fcb_uid, $user_id) {
        //what kind of post ? 
        //link ? picture ? or simple message
        //check if is a token set
        try {
            //Get current token,
            $token = $this->ReadToken($fcb_uid, $user_id);
            //build session
            $fbSession = new FacebookSession($token);
            //Will Build request depending on data submited by user
            //$send = $this->buildRequest($data);
            //check what fields/submit type 
            if ($data['Document']['submittedfile']['error'] == '0') {
                //build msg
                if (isset($data['urlsubmit'])) {
                    //concatenate url on message
                    $msg = $data['urlsubmit'] . ' ' . $data['message'];
                } else {
                    //just message with picture
                    $msg = $data['message'];
                }
                //there is a file type submit
                $response = (new FacebookRequest(
                        $fbSession, 'POST', '/me/photos', array(
                    'source' => new CURLFile($data['Document']['submittedfile']['tmp_name'], 'image/png'),
                    'message' => $msg
                        )
                        ))->execute()->getGraphObject();
            } else {
                //build array
                if (isset($data['urlsubmit'])) {
                    //concatenate url on message
                    $send = array(
                        'link' => $data['urlsubmit'],
                        'message' => $data['message']
                    );
                } else {
                    //just message with picture
                    $send = array(
                        'message' => $data['message']
                    );
                }
                //post      
                $response = (new FacebookRequest(
                        $fbSession, 'POST', '/me/feed', $send
                        ))->execute()->getGraphObject();
            }
            //get this post id
            $post_id = $response->getProperty('id');
            //success. Return post id
            $return = array('status' => 'success', 'post_id' => $post_id);
        } catch (FacebookRequestException $e) {
            //catch
            $error = $e->getMessage();
            $return = array('status' => 'error', 'message' => 'Facebook message not posted! ' . $error);
        }
        //return 
        return $return;
    }

    /* getLikesAndComments
     * Will return number of likes 
     * 
     */
    public function getLikesAndComments($post_id, $fcb_uid, $user_id) {
        //try catch
        try {
                //Get current token,
                $token = $this->ReadToken($fcb_uid, $user_id);
                
                //build session
                $fbSession = new FacebookSession($token);
                //get likes
                $request = new FacebookRequest(
                        $fbSession, 'GET', "/$post_id/likes?limit=1&summary=true"
                );
                $response = $request->execute();
                $likes = $response->getGraphObject()->asArray();

                //comments
                $request = new FacebookRequest(
                        $fbSession, 'GET', "/$post_id/comments?limit=1&summary=true"
                );
                $response = $request->execute();
                $comments = $response->getGraphObject()->asArray();
                //test
                if(!empty($comments)){
                    $comments_count = $comments['summary']->total_count;
                }else{ $comments_count= 0; }
                if(!empty($likes)){
                    $likes_count = $likes['summary']->total_count;
                }else{ $likes_count=0; }
                //return
                $return = array('status'=>'success','comments'=>$comments_count,
                                'likes'=>$likes_count);
                //return  
                return $return;
            } catch (Exception $exc) {
           //error
           return array('status'=>'error','message'=>$exc->getMessage());
        }

    }
    /* Return fb full name
     * 
     */
    public function getFbName($fb_id) {
        //get this $fbuserpost['FcbUserPosts']['fcb_uid'];
        $get = array(
            //'limit' => 10,
            'conditions' => array(array('fcb_id' => $fb_id)),
            'fields' => array('full_name'),
                //'order' => array('date'=>'desc')
        );
        //get social post. Set it for view.
        $name = $this->find('first', $get);
        $fname = $name['Social']['full_name'];
        //return
        return $fname;
    }

    /* Request a new token
     * Will post data of facebook user profile 
     * 
     */
    public function RequestNewToken() {
        $scope = array('email', 'publish_actions', 'manage_pages', 'user_likes', 'manage_notifications', 'user_friends');
        //Do the session first 
        FacebookSession::setDefaultApplication($this->fbAPId, $this->fbAPSecret);
        // login helper with redirect_uri
        $fbhelper = new FacebookRedirectLoginHelper(FACEBOOK_REDIRECT_URL);
        //url
        $url = $fbhelper->getLoginUrl($scope);
        //redirect for a new token request
        return $url;
    }
    /* post
     * Will post data of facebook user profile 
     * 
     */

    public function cronpost($post, $fcb_uid) {
        //Set fb Session default 
        FacebookSession::setDefaultApplication($this->fbAPId, $this->fbAPSecret);
        //user id
        $user_id = $post['SocialPosts']['uid'];
        //what kind of post ? 
        //link ? picture ? or simple message
        //check if is a token set
        try {
            //Get current token,
            $token = $this->ReadToken($fcb_uid, $user_id);
            //build session
            $fbSession = new FacebookSession($token);
            //Will Build request depending on data submited by user
            //$send = $this->buildRequest($data);
            //check what fields/submit type 
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
                        $fbSession, 'POST', '/me/photos', array(
                    'source' => new CURLFile($filepath, 'image/png'),
                    'message' => $msg
                        )
                        ))->execute()->getGraphObject();
            } else {
                //build array
                if ($post['SocialPosts']['link'] != 'na') {
                    //concatenate url on message
                    $send = array(
                        'link' => $post['SocialPosts']['link'],
                        'message' => $post['SocialPosts']['message']
                    );
                } else {
                    //just message
                    $send = array(
                        'message' => $post['SocialPosts']['message']
                    );
                }
                //post      
                $response = (new FacebookRequest(
                        $fbSession, 'POST', '/me/feed', $send
                        ))->execute()->getGraphObject();
            }
            //get this post id
            $post_id = $response->getProperty('id');
            //success. Return post id
            $return = array('status' => 'success', 'post_id' => $post_id);
        } catch (FacebookRequestException $e) {
            //catch
            $error = $e->getMessage();
            $return = array('status' => 'error', 'message' => 'Facebook message not posted! ' . $error);
        }
        //return 
        return $return;
    }

    /* ReadToken
     * Will read available token for user 
     * 
     */

    public function ReadToken($fcb_uid, $user_id) {
        //Check and read
        if ($this->hasAny(array('uid' => $user_id, 'fcb_id' => $fcb_uid))) {
            //get type and id.
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('uid' => $user_id, 'fcb_id' => $fcb_uid)),
                'fields' => array('access_token'),
                    //'order' => array('date'=>'desc')
            );
            $record = $this->find('first', $get);
            //token
            return $record['Social']['access_token'];
        } else {
            //return false. Some type of security
            return FALSE;
        }
    }

    /* Fb delete profile
     * 
     */

    public function deleteUser($user_id, $fcb_id) {
        //test if access 
        if ($this->hasAny(array('fcb_id' => $fcb_id, 'uid' => $user_id))) {
            if ($this->deleteAll(array('fcb_id' => $fcb_id, 'uid' => $user_id))) {
                $return = array('status' => 'success', 'message' => 'User deleted.');
            } else {
                $return = array('status' => 'error', 'message' => 'Something went wrong! Please try again.');
            }
        } else {
            //Error
            $return = array('status' => 'error', 'message' => 'You are not allowed to perform this operation!');
        }
        //return
        return $return;
    }

}

?>