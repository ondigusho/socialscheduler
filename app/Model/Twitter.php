<?php

//load twitteroauth 
App::import('Vendor', 'twitteroauth', array('file' => 'twitteroauth' . DS . 'autoload.php'));

//namespacing for Twitter
use Abraham\TwitterOAuth\TwitterOAuth;

/* twitter Model
 * 
 * 
 * @copyright Ondi Gusho.
 */

class Twitter extends AppModel {

    //Set table
    public $useTable = 'user_tw';
    //set static vars
    public $CONSUMER_KEY = '';
    public $CONSUMER_SECRET = '';
    public $OAUTH_CALLBACK = '';
    //tokens
    public $app_oauth_token = NULL;
    public $app_oauth_token_secret = NULL;

    /**
     * newProfile method.
     * Will check on db if is a new profile or the user is already registered
     * 
     */
    public function newProfile($user_id, $tw_id) {
        //check if is any
        $conditions = array('uid' => $user_id, 'tw_id' => $tw_id);
        if ($this->hasAny($conditions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /* Twitter delete profile
     * 
     */

    public function deleteUser($user_id, $tw_id) {
        //test if access 
        if ($this->hasAny(array('tw_id' => $tw_id, 'uid' => $user_id))) {
            if ($this->deleteAll(array('tw_id' => $tw_id, 'uid' => $user_id))) {
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

    /* ReadToken
     * Will read available token for user 
     * 
     */

    public function ReadToken($tw_uid, $user_id) {
        //Check and read
        if ($this->hasAny(array('uid' => $user_id, 'tw_id' => $tw_uid))) {
            //get type and id.
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('uid' => $user_id, 'tw_id' => $tw_uid)),
                'fields' => array('access_token', 'access_token_secret'),
                    //'order' => array('date'=>'desc')
            );
            $record = $this->find('first', $get);
            //token
            return $record;
        } else {
            //return false. Some type of security
            return FALSE;
        }
    }

    /* post
     * Will post data of twitter user's profile 
     * 
     */

    public function post($data, $tw_uid, $user_id) {
        //what kind of post ? 
        //link ? picture ? or simple message
        //check if is a token set
        try {
            //Get current token,
            $tokens = $this->ReadToken($tw_uid, $user_id);
            //get user's tokens
            $access_token = $tokens['Twitter']['access_token'];
            $access_token_secret = $tokens['Twitter']['access_token_secret'];
            //build session
            $connection = new TwitterOAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET, $access_token, $access_token_secret);
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
                //set media 
                $media = $connection->upload('media/upload', array('media' => $data['Document']['submittedfile']['tmp_name']));
                $parameters = array(
                    'status' => $msg,
                    'media_ids' => implode(',', array($media->media_id_string)),
                );
                $result = $connection->post('statuses/update', $parameters);
            } else {
                //build array
                if (isset($data['urlsubmit'])) {
                    //concatenate url on message
                    //concatenate url on message
                    $msg = $data['urlsubmit'] . ' ' . $data['message'];
                    //Message can't be more that 140 chars
                    $result = $connection->post("statuses/update", array("status" => $msg));
                } else {
                    //just message
                    //Message can't be more that 140 chars
                    $result = $connection->post("statuses/update", array("status" => $data['message']));
                }
            }
            //get this post id
            $post_id = $result->id;
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
    
    /* get reports by twitter post
    
     * 
     */
    public function getTwitterLookup($post_id, $tw_uid, $user_id) {
        try {
            //Get current token,
            $tokens = $this->ReadToken($tw_uid, $user_id);
            //get user's tokens
            $access_token = $tokens['Twitter']['access_token'];
            $access_token_secret = $tokens['Twitter']['access_token_secret'];
            //build session
            $connection = new TwitterOAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET, $access_token, $access_token_secret);
            //get result by id
            $result = $connection->get("statuses/lookup", array('id'=>$post_id));
            
            $data['followers_count'] = $result[0]->user->followers_count;
            $data['following_count'] = $result[0]->user->friends_count;
            $data['statuses_count'] = $result[0]->user->statuses_count;
            //return
            $return = array('status' => 'success', 'data'=>$data);
        } catch (FacebookRequestException $e) {
            //catch
            $error = $e->getMessage();
            $return = array('status' => 'error', 'message' => 'Something went wrong with Twitter Api ! ' . $error);
        }
        //return 
        return $return;
    }
    
    /* cron post
     * Will post data of twitter user's profiles 
     * 
     */

    public function cronpost($post, $tw_uid) {
        //what kind of post ? 
        //link ? picture ? or simple message
        //check if is a token set
        //get firts vars
        //user id 
        $user_id = $post['SocialPosts']['uid'];
        //try catch 
        try {
            //Get current token,
            $tokens = $this->ReadToken($tw_uid, $user_id);
            //get user's tokens
            $access_token = $tokens['Twitter']['access_token'];
            $access_token_secret = $tokens['Twitter']['access_token_secret'];
            //build session
            $connection = new TwitterOAuth($this->CONSUMER_KEY, $this->CONSUMER_SECRET, $access_token, $access_token_secret);
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
                //set media 
                $media = $connection->upload('media/upload', array('media' => $filepath));
                $parameters = array(
                    'status' => $msg,
                    'media_ids' => implode(',', array($media->media_id_string)),
                );
                $result = $connection->post('statuses/update', $parameters);
            } else {
                //build array
                if ($post['SocialPosts']['link'] != 'na') {
                    //concatenate url on message
                    //concatenate url on message
                    $msg = $post['SocialPosts']['link'] . ' ' . $post['SocialPosts']['message'];
                    //Message can't be more that 140 chars
                    $result = $connection->post("statuses/update", array("status" => $msg));
                } else {
                    //just message
                    //Message can't be more that 140 chars
                    $result = $connection->post("statuses/update", array("status" => $post['SocialPosts']['message']));
                }
            }
            //get this post id
            $post_id = $result->id;
            //success. Return post id
            $return = array('status' => 'success', 'post_id' => $post_id);
        } catch (Exception $exc) {
            //Save error message 
            $error_msg = $exc->getMessage();
            //return
            $return = array('status' => 'error', 'message' => $error_msg);
        }
        //return
        return $return;
    }

}

?>
