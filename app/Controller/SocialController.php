<?php
/* SocialController
 * Will interact with facebook and twitter API's 
 * Manage social media in only one interface 
 * 
 * 
 * @copyright Ondi Gusho.
 */

//load twitteroauth 
App::import('Vendor', 'twitteroauth', array('file' => 'twitteroauth' . DS . 'autoload.php'));
//namespacing for Twitter
use Abraham\TwitterOAuth\TwitterOAuth;
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


class SocialController extends AppController {

    //Set Models Used
    var $uses = array('Social', 'FbPages', 'FcbUserPosts', 'SocialPosts', 'FcbPagePosts', 'SocialScheduled', 'Twitter', 'TwUserPosts', 'LnkIn','ValidTo');
    //These are the SECRET keys!
    public $fbAPId = "someid";
    public $fbAPSecret = "somehash";
    public $fbhelper;
    public $fbSession;
    public $c_token = NULL;

    //constructor 
    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        //set facebook session
        //Do the session first 
        FacebookSession::setDefaultApplication($this->fbAPId, $this->fbAPSecret);
        // login helper with redirect_uri
        $this->fbhelper = new FacebookRedirectLoginHelper(FACEBOOK_REDIRECT_URL);
    }

    /**
     * getWhereAccounts method.
     * Will return accounts where post is set to be used
     */
    public function getWhereAccounts($post_id) {
        $return = array();
        $get = array(
            //'limit' => 10,
            'conditions' => array(array('post_id' => $post_id, 'scheduled' => 1)),
                //'fields' => array('', ''),
                //'order' => array('date'=>'desc')
        );
        //fb users
        $fbusers = $this->FcbUserPosts->find('all', $get);
        if (!empty($fbusers)) {
            foreach ($fbusers as $user) {
                $value = 'fcbuseredit-' . $user['FcbUserPosts']['fcb_uid'];
                array_push($return, $value);
            }
        }
        //fb pages
        $fbpages = $this->FcbPagePosts->find('all', $get);
        if (!empty($fbpages)) {
            foreach ($fbpages as $page) {
                $value = 'fcbpageedit-' . $page['FcbPagePosts']['fcb_uid'] . '-' . $page['FcbPagePosts']['fcb_page_id'];
                array_push($return, $value);
            }
        }
        //twitter
        $twitter = $this->TwUserPosts->find('all', $get);
        if (!empty($twitter)) {
            foreach ($twitter as $account) {
                $value = 'twuseredit-' . $account['TwUserPosts']['tw_uid'];
                array_push($return, $value);
            }
        }
        //return 
        return $return;
    }

    /**
     * cleanProfiles method.
     * Will clean scheduled post profiles
     * 
     */
    private function cleanProfiles($post_id) {
        //clean where this post_id and user id
        $this->FcbUserPosts->deleteAll(array('post_id' => $post_id));
        $this->FcbPagePosts->deleteAll(array('post_id' => $post_id));
        $this->TwUserPosts->deleteAll(array('post_id' => $post_id));
    }

    /**
     * InsertProfiles method.
     * 
     */
    public function InsertProfiles($data, $post_id) {
        //get user id.
        $user_id = $this->Session->read('Auth.User.id');
        //Clean
        $this->cleanProfiles($post_id);
        //parse profiles, post by profile case 
        $profiles = $data['addcontacts'];
        foreach ($profiles as $profile) {
            $p_ar = explode('-', $profile);
            //first element is type
            $type = $p_ar[0];
            //switch case by profile
            switch ($type) {
                case 'fcbuseredit':
                    //get fcb id
                    $fcb_uid = $p_ar[1];
                    //save FcbUserPost
                    $userfcb['FcbUserPosts']['id'] = '';
                    $userfcb['FcbUserPosts']['post_id'] = $post_id;
                    $userfcb['FcbUserPosts']['fcb_post_id'] = '';
                    $userfcb['FcbUserPosts']['fcb_uid'] = $fcb_uid;
                    $userfcb['FcbUserPosts']['errorstatus'] = 0;
                    $userfcb['FcbUserPosts']['scheduled'] = 1;
                    $userfcb['FcbUserPosts']['msg'] = 'Waiting';
                    //save
                    $this->FcbUserPosts->save($userfcb);
                    //reste var
                    unset($userfcb);
                    break;
                case 'twuseredit':
                    //get fcb id
                    $tw_uid = $p_ar[1];
                    //save FcbUserPost
                    $user['TwUserPosts']['id'] = '';
                    $user['TwUserPosts']['post_id'] = $post_id;
                    $user['TwUserPosts']['tw_post_id'] = '';
                    $user['TwUserPosts']['tw_uid'] = $tw_uid;
                    $user['TwUserPosts']['errorstatus'] = 0;
                    $user['TwUserPosts']['scheduled'] = 1;
                    $user['TwUserPosts']['msg'] = 'Waiting';
                    //save
                    $this->TwUserPosts->save($user);
                    //reste var
                    unset($user);
                    break;
                case 'fcbpageedit':
                    //Save and post on facebook page
                    //get variables
                    $fcb_uid = $p_ar[1];
                    $fcb_page_id = $p_ar[2];
                    //Post on page
                    //save FcbUserPost
                    $pagefcb['FcbPagePosts']['id'] = '';
                    $pagefcb['FcbPagePosts']['post_id'] = $post_id;
                    $pagefcb['FcbPagePosts']['fcb_uid'] = $fcb_uid;
                    $pagefcb['FcbPagePosts']['fcb_page_id'] = $fcb_page_id;
                    $pagefcb['FcbPagePosts']['fcb_post_id'] = '';
                    $pagefcb['FcbPagePosts']['errorstatus'] = 0;
                    $pagefcb['FcbPagePosts']['scheduled'] = 1;
                    $pagefcb['FcbPagePosts']['msg'] = 'Waiting';
                    //save page post
                    $this->FcbPagePosts->save($pagefcb);
                    //reste var
                    unset($pagefcb);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * edit method.
     * Will edit user's social post
     * 
     */
    public function edit($post_id) {
        //get user id.
        $user_id = $this->Session->read('Auth.User.id');
        //If there is an ajax request
        if ($this->request->is('Ajax')) {
            //Set json header
            header('Content-type: application/json');
            //Load layout
            $this->layout = 'ajax';
            if (isset($this->request->data['scrap'])) {
                $data = ScrapSite::run($this->request->data['url']);
                if ($data['status'] == 'success') {
                    echo json_encode($data);
                    exit(1);
                }
            }
            //if delete image request
            if (isset($this->request->data['typeToDelete'])) {
                //get val
                $type = $this->request->data['typeToDelete'];
                //make switch case for each case handeling 
                switch ($type) {
                    case 'removepicture':
                        $post_id = $this->request->data['recordToDeleteId'];
                        $response = $this->SocialPosts->deleteImage($user_id, $post_id);
                        if ($response['status'] == 'success') {
                            echo json_encode($response);
                        }
                        break;
                    default:
                        break;
                }
            } else {
                //Return an error message.
                $response_array['status'] = 'error';
                echo json_encode($response_array);
            }
            //Break
            exit(1);
        }
        //check validation 
        if (!$this->ValidTo->isValid($user_id)){
            $this->Session->setFlash(__('Your USE period has expired! Please buy more searches to be able to use the system.'), 'cake-error');
            $this->redirect(array('controller'=>'buy','action' => 'index'));
            exit(1);
        }
        //if post, Request for save post    
        if ($this->request->is('post')) {

            //Check for cancel action
            if (isset($this->request->data['cancel'])) {
                $this->redirect(array('controller' => 'social', 'action' => 'index'));
            }

            //dynamically build array
            $save['SocialPosts']['id'] = $post_id;
            $save['SocialPosts']['message'] = $this->request->data['message'];
            //if url 
            if (isset($this->request->data['urlsubmit'])) {
                $save['SocialPosts']['link'] = $this->request->data['urlsubmit'];
            }
            //if new file
            if ($this->request->data['Document']['submittedfile']['error'] == '0') {
                //handle uploaded file, remove old one if exists
                $uplresult = $this->SocialScheduled->processUpload($this->request->data['Document']['submittedfile']);
                //check result
                if ($uplresult['success'] == TRUE) {
                    //get filename
                    $save['SocialPosts']['file'] = $uplresult['filename'];
                } else {
                    $this->Session->setFlash(__("Something Wrong with the file/picture uploaded." . $uplresult['message']), 'cake-error');
                    $this->redirect(array('action' => 'index'));
                }
            }
            //handle accounts
            //save what is sent
            $this->InsertProfiles($this->request->data, $post_id);

            //if datetime is changed
            if (isset($this->request->data['scheduled-edit'])) {
                $when = $this->request->data['scheduled-edit'];
                //Try update
                $this->SocialScheduled->updateAll(
                        array('datetime' => "'$when'"), array('post_id' => $post_id)
                );
            }
            //Save
            $this->SocialPosts->save($save);
        }
        //get user id.
        $user_id = $this->Session->read('Auth.User.id');
        //check if is any
        $conditions = array('id' => $post_id, 'uid' => $user_id);
        ///if access
        if ($this->SocialPosts->hasAny(array('id' => $post_id, 'uid' => $user_id))) {
            //check on what accounts? 
            $accounts = $this->getWhereAccounts($post_id);
            //get data 
            $post_data = $this->SocialPosts->getScheduledPost($post_id, $accounts);
            //post
            $this->set('post_data', $post_data);

            //get type and id.
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('uid' => $user_id)),
                    //'fields' => array('', ''),
                    //'order' => array('date'=>'desc')
            );
            //fb users
            $fbusers = $this->Social->find('all', $get);
            if (!empty($fbusers)) {
                //set for view
                $this->set('fbusers', $fbusers);
            }
            //fb pages
            $getpages = array(
                //'limit' => 10,
                'conditions' => array(array('uid' => $user_id)),
                    //'fields' => array('', ''),
                    //'order' => array('date'=>'desc')
            );
            //fb users
            $fbpages = $this->FbPages->find('all', $getpages);
            if (!empty($fbpages)) {
                //set for view
                $this->set('fbpages', $fbpages);
            }

            //twitter
            $gettw = array(
                //'limit' => 10,
                'conditions' => array(array('uid' => $user_id)),
                    //'fields' => array('', ''),
                    //'order' => array('date'=>'desc')
            );
            //fb users
            $twitterac = $this->Twitter->find('all', $gettw);
            if (!empty($twitterac)) {
                //set for view
                $this->set('twitter', $twitterac);
            }
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    /**
     * Twitter method.
     * Will interact with Twitter model. Add new user profile 
     * Create user/authorize app 
     * 
     */
    public function twitter()  {
        $user_id = $this->Session->read('Auth.User.id');
        //twitter object
        $twitter = new TwitterOAuth($this->Twitter->CONSUMER_KEY, $this->Twitter->CONSUMER_SECRET);
        //if a request for process
        if (isset($_GET['type_request']) && $_GET['type_request'] === 'twitter') {
            //new user profile request
            $user_access_data = $twitter->oauth('oauth/access_token', array('oauth_token' => $_GET['oauth_token'], 'oauth_verifier' => $_GET['oauth_verifier']));
            //create user's oauth object
            $post = new TwitterOAuth($this->Twitter->CONSUMER_KEY, $this->Twitter->CONSUMER_SECRET, $user_access_data['oauth_token'], $user_access_data['oauth_token_secret']);
            //get user's profile		
            $profile = $post->get("users/lookup", array('user_id' => $user_access_data['user_id']));
            //Save profile
            $save['uid'] = $user_id;
            $save['tw_id'] = $profile[0]->id;
            $save['access_token'] = $user_access_data['oauth_token'];
            $save['access_token_secret'] = $user_access_data['oauth_token_secret'];
            $save['name'] = $profile[0]->name;
            $save['screeen_name'] = $profile[0]->screen_name;
            $save['followers_count'] = $profile[0]->friends_count;
            $save['followedby_count'] = $profile[0]->followers_count;
            $save['p_picture'] = $profile[0]->profile_image_url;
            $save['link'] = $profile[0]->url;
            $save['timezone'] = $profile[0]->time_zone;
            //if exists
            if ($this->Twitter->newProfile($user_id, $profile[0]->id)) {
                //Save and redirect
                if ($this->Twitter->save($save)) {
                    //Redirect user added
                    $this->Session->setFlash(__("Profile saved."), 'cake-success');
                    $this->redirect(array('action' => 'index'));
                }
                //WTF happened...
                else {
                    $this->Session->setFlash(__("Something went wrong saving Twitter profile! Please try again later."), 'cake-error');
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                //User exists.
                // When validation fails or other local issues
                $this->Session->setFlash(__("User already linked! Logout from Twitter and login with a diffrent account."), 'cake-error');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            //Request for approval
            // create a link
            $tokens = $twitter->oauth('oauth/request_token', array('oauth_callback' => $this->Twitter->OAUTH_CALLBACK));
            //get token's
            $app_oauth_token = $tokens['oauth_token'];
            $app_oauth_token_secret = $tokens['oauth_token_secret'];
            //if successful
            //build url 		
            $url = $twitter->url('oauth/authorize', array('oauth_token' => $app_oauth_token, 'oauth_callback' => $this->Twitter->OAUTH_CALLBACK));
            //redirect
            $this->redirect($url);
        }
    }

     /**
    * csv method.
    *
    * Wil update "status" column into 0
    * for user_id , campaign_id
    *  
    */
    public function csv() {
        //get user id.
        if(!isset($id)){
            $user_id = $this->Session->read('Auth.User.id');
        }
        //Check if user has access
        $conditions = array(
            'uid'=>$user_id);
        //Test and run
        if ($this->SocialPosts->hasAny($conditions)){
            //Get all records and send them for .csv file
            //Build Query.
            $get = array(
              'conditions' => array('uid'=>$user_id),
              'fields'=>array('message','dateposted')  
            );
            //Data
            $data = $this->SocialPosts->find('all',$get);
            
            //prepare data
            foreach ($data as $key => $value){
                $rows[] = $value['SocialPosts']['message'];
                $rows[] = $value['SocialPosts']['dateposted'];
                $csv[] = $rows;
                $rows = array();
            }
            //Column names initiate
            //$columns = (array) NULL;
            $columns = array('Message','Date Posted');
            //Run csv download
            CsvDownloadRequest::build($csv, $columns, 'Social_Posts');
        }else{
            $this->Session->setFlash(__('The request could not be processed. Please, try again.'),'cake-error');
            $this->redirect(array('action' => 'index'));
        }
    }
    
    /**
     * Index method.
     */
    public function index() {
        //get user id.
        if (!isset($user_id)) {
            $user_id = $this->Session->read('Auth.User.id');
        }
        //If there is an ajax request
        if ($this->request->is('Ajax')) {
            //Set json header
            header('Content-type: application/json');
            //Load layout
            $this->layout = 'ajax';
            //scrap website request
            if (isset($this->request->data['scrap'])) {
                $data = ScrapSite::run($this->request->data['url']);
                if ($data['status'] == 'success') {
                    echo json_encode($data);
                    exit(1);
                }
            }
            //if delete request
            if (isset($this->request->data['typeToDelete'])) {
                //get val
                $type = $this->request->data['typeToDelete'];
                //make switch case for each case handeling 
                switch ($type) {
                    case 'fbuser':
                        $fcb_uid = $this->request->data['recordToDeleteId'];
                        $response = $this->Social->deleteUser($user_id, $fcb_uid);
                        if ($response['status'] == 'success') {
                            //test check ... delete pages
                            //delete pages linked to user
                            $this->FbPages->deletePages($user_id, $fcb_uid);
                            //inactivate posts
                            $this->FcbUserPosts->inactivatePosts($fcb_uid);
                            $this->FcbPagePosts->inactivateUserPosts($fcb_uid);
                            echo json_encode($response);
                        }
                        break;
                    case 'fbpage':
                        $page_id = $this->request->data['recordToDeleteId'];
                        $fcb_uid = $this->request->data['fcb_uid'];
                        $response = $this->FbPages->deletePages($user_id, $fcb_uid, $page_id);
                        $this->FcbPagePosts->inactivatePosts($page_id);
                        if ($response['status'] == 'success') {
                            echo json_encode($response);
                        }
                        break;
                    case 'twuser':
                        $tw_id = $this->request->data['recordToDeleteId'];
                        $response = $this->Twitter->deleteUser($user_id, $tw_id);
                        $this->TwUserPosts->inactivatePosts($tw_id);
                        if ($response['status'] == 'success') {
                            //print json result
                            echo json_encode($response);
                        }
                        break;
                    default:
                        break;
                }
            } else {
                //Return an error message.
                $response_array['status'] = 'error';
                echo json_encode($response_array);
            }
            //Break
            exit(1);
        }
        //check validation 
        if (!$this->ValidTo->isValid($user_id)){
            $this->Session->setFlash(__('Your USE period has expired! Please buy more searches to be able to use the system.'), 'cake-error');
            $this->redirect(array('controller'=>'buy','action' => 'index'));
            exit(1);
        }
        //if from facebook return
        if (isset($_GET['code'])) {
            //Try catch - set the session
            //Get user profile, register,get user pages, save token, print user profile and pages
            try {
                //Get session
                $this->fbSession = $this->fbhelper->getSessionFromRedirect();
            } catch (FacebookRequestException $ex) {
                // When Facebook returns a
                $error = $ex->getMessage();
                $this->Session->setFlash(__("Something went wrong! Error : $error"), 'cake-error');
                $this->redirect(array('action' => 'index'));
            } catch (Exception $ex) {
                // When validation fails or other local issues
                $error = $ex->getMessage();
                $this->Session->setFlash(__("Something went wrong! Error : $error"), 'cake-error');
                $this->redirect(array('action' => 'index'));
            }
            //Get profile and save. Get pages and save 
            if ($this->fbSession) {
                try {
                    //Get user profile
                    $user_profile = $this->getUserProfile();
                    //user access token
                    $access_token = $this->fbSession->getToken();
                    $fbname = $user_profile->getName();
                    $fcb_uid = $user_profile->getId();
                    //get type profile
                    $type_profile = $this->newProfile($user_id, $fcb_uid);
                    //If new user profile
                    if (isset($type_profile['new'])) {
                        //profile picture
                        $p_picture = $this->getProfilePicture();
                        //Save user profile
                        if ($this->Social->FbUserProfile($access_token, $user_profile, $user_id, $p_picture)) {
                            //get accounts 
                            $this->userAccounts($user_id, $fcb_uid);
                        } else {
                            // When validation fails or other local issues
                            $this->Session->setFlash(__("Something went wrong saving user profile"), 'cake-error');
                            $this->redirect(array('action' => 'index'));
                        }
                    } else {
                        //profile picture
                        $p_picture = $this->getProfilePicture();
                        //update user profile
                        if ($this->Social->FbUserProfile($access_token, $user_profile, $user_id, $p_picture,$type_profile['id'])) {
                            //get accounts 
                        } else {
                            // When validation fails or other local issues
                            $this->Session->setFlash(__("Something went wrong saving user profile"), 'cake-error');
                            $this->redirect(array('action' => 'index'));
                        }
                        //User exists.
                        //update curent user. Send a message that user has been updated/re-linked . If you are trying to add a new user logout from fcb
                        // When validation fails or other local issues
                        $this->Session->setFlash(__("User $fbname was Reconnected! Logout from facebook account $fbname if you need to add more accounts."), 'cake-success');
                        $this->redirect(array('action' => 'index'));
                    }
                } catch (FacebookRequestException $e) {
                    // When validation fails or other local issues
                    $error = $ex->getMessage();
                    $this->Session->setFlash(__("Something went wrong! Error : $error"), 'cake-error');
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
        //if not code from fb     
        else {
            // show login url
            $scope = array('publish_actions', 'manage_pages');
            //Set a link for posting
            $this->set('login', '<img style="height: 35px; width: 40px;" src="/img/facebook-icon.png"></i> <a class="btn btn-primary" href="' . $this->fbhelper->getLoginUrl($scope) . '">Add Facebook Account</a>
                ');
        }
        //Check for cancel action
        if (isset($this->request->data['cancel'])) {
            $this->redirect(array('action' => 'index'));
        }
        //if post. a post request    
        if ($this->request->is('post')) {
            //if a social submit 
            if (isset($this->request->data['addcontacts']) &&
                    isset($this->request->data['message'])) {
                ###################### Implement Security ###############
                ########Check if use has access for each one of the accounts sent for post
                //send data for security check first
                //if ($this->isSecure($this->request->data))
                //if post now
                if ($this->request->data['optionsRadios'] == 'postnow') {
                    //post now
                    //save post and get id 
                    $save['SocialPosts']['uid'] = $user_id;
                    $save['SocialPosts']['message'] = $this->request->data['message'];
                    //test if file build var
                    if ($this->request->data['Document']['submittedfile']['error'] == '0') {
                        $save['SocialPosts']['file'] = 'na';
                    }
                    //test if link build var
                    if (isset($this->request->data['urlsubmit'])) {
                        $save['SocialPosts']['link'] = $this->request->data['urlsubmit'];
                    } else {
                        $save['SocialPosts']['link'] = 'na';
                    }
                    // user datetime
                    $save['SocialPosts']['dateposted'] = Utilities::GetUserTime($this);
                    //posted
                    $save['SocialPosts']['posted'] = 1;
                    //save
                    $result_id = $this->SocialPosts->save($save);
                    //parse profiles, post by profile case 
                    $profiles = $this->request->data['addcontacts'];
                    foreach ($profiles as $profile) {
                        $p_ar = explode('-', $profile);
                        //first element is type
                        $type = $p_ar[0];
                        //switch case by profile
                        switch ($type) {
                            case 'fcbuser':
                                //get fcb id
                                $fcb_uid = $p_ar[1];
                                //Post of facebook
                                $postresult = $this->Social->post($this->request->data, $fcb_uid, $user_id);
                                //get id
                                if ($postresult['status'] == 'success') {
                                    //save FcbUserPost
                                    $userfcb['FcbUserPosts']['id'] = '';
                                    $userfcb['FcbUserPosts']['uid'] = $user_id;
                                    $userfcb['FcbUserPosts']['post_id'] = $result_id['SocialPosts']['id'];
                                    $userfcb['FcbUserPosts']['fcb_post_id'] = $postresult['post_id'];
                                    $userfcb['FcbUserPosts']['fcb_uid'] = $fcb_uid;
                                    $userfcb['FcbUserPosts']['errorstatus'] = 1;
                                    $userfcb['FcbUserPosts']['msg'] = 'Successfull';
                                } else {
                                    //save FcbUserPost
                                    $userfcb['FcbUserPosts']['id'] = '';
                                    $userfcb['FcbUserPosts']['uid'] = $user_id;
                                    $userfcb['FcbUserPosts']['post_id'] = $result_id['SocialPosts']['id'];
                                    $userfcb['FcbUserPosts']['fcb_post_id'] = 'na';
                                    $userfcb['FcbUserPosts']['fcb_uid'] = $fcb_uid;
                                    $userfcb['FcbUserPosts']['errorstatus'] = 0;
                                    //save error message
                                    $userfcb['FcbUserPosts']['msg'] = $postresult['message'];
                                    $this->FcbUserPosts->save($userfcb);
                                    
                                    //name
                                    $fname = $this->Social->getFbName($fcb_uid);
                                    $this->Session->setFlash(__("There was something wrong with facebook acccount $fname. Propably an expired authentication of Facebook!"), 'cake-error');
                                    $this->redirect(array('action'=>'expiredtoken',$fname));
                                }
                                //save
                                $this->FcbUserPosts->save($userfcb);
                                //reste var
                                unset($userfcb);
                                break;
                            case 'twuser':
                                //get fcb id
                                $tw_id = $p_ar[1];
                                $postresult = $this->Twitter->post($this->request->data, $tw_id, $user_id);
                                //get id
                                if ($postresult['status'] == 'success') {
                                    //save UserPost
                                    $userftw['TwUserPosts']['id'] = '';
                                    $userftw['TwUserPosts']['uid'] = $user_id;
                                    $userftw['TwUserPosts']['post_id'] = $result_id['SocialPosts']['id'];
                                    $userftw['TwUserPosts']['tw_post_id'] = $postresult['post_id'];
                                    $userftw['TwUserPosts']['tw_uid'] = $tw_id;
                                    $userftw['TwUserPosts']['errorstatus'] = 1;
                                    $userftw['TwUserPosts']['msg'] = 'Successfull';
                                } else {
                                    //save twUserPost
                                    $userftw['TwUserPosts']['id'] = '';
                                    $userftw['TwUserPosts']['uid'] = $user_id;
                                    $userftw['TwUserPosts']['post_id'] = $result_id['SocialPosts']['id'];
                                    $userftw['TwUserPosts']['tw_post_id'] = 'na';
                                    $userftw['TwUserPosts']['tw_uid'] = $tw_id;
                                    $userftw['TwUserPosts']['errorstatus'] = 0;
                                    //save error message
                                    $userftw['TwUserPosts']['msg'] = $postresult['message'];
                                }
                                //save
                                $this->TwUserPosts->save($userftw);
                                //reste var
                                unset($userftw);
                                break;
                            case 'fcbpage':
                                //Save and post on facebook page
                                //get variables
                                $fcb_uid = $p_ar[1];
                                $fcb_page_id = $p_ar[2];
                                //Post on page
                                $postresult = $this->FbPages->post($this->request->data, $fcb_uid, $fcb_page_id, $user_id);
                                //get id
                                if ($postresult['status'] == 'success') {
                                    //save FcbUserPost
                                    $pagefcb['FcbPagePosts']['id'] = '';
                                    $pagefcb['FcbPagePosts']['uid'] = $user_id;
                                    $pagefcb['FcbPagePosts']['post_id'] = $result_id['SocialPosts']['id'];
                                    $pagefcb['FcbPagePosts']['fcb_uid'] = $fcb_uid;
                                    $pagefcb['FcbPagePosts']['fcb_page_id'] = $fcb_page_id;
                                    $pagefcb['FcbPagePosts']['fcb_post_id'] = $postresult['post_id'];
                                    $pagefcb['FcbPagePosts']['errorstatus'] = 1;
                                    $pagefcb['FcbPagePosts']['msg'] = 'Successfull';
                                } else {
                                    //save FcbUserPost
                                    $pagefcb['FcbPagePosts']['id'] = '';
                                    $pagefcb['FcbPagePosts']['uid'] = $user_id;
                                    $pagefcb['FcbPagePosts']['post_id'] = $result_id['SocialPosts']['id'];
                                    $pagefcb['FcbPagePosts']['fcb_post_id'] = 'na';
                                    $pagefcb['FcbPagePosts']['fcb_uid'] = $fcb_uid;
                                    $pagefcb['FcbPagePosts']['fcb_page_id'] = $fcb_page_id;
                                    $pagefcb['FcbPagePosts']['errorstatus'] = 0;
                                    //save error message
                                    $pagefcb['FcbPagePosts']['msg'] = $postresult['message'];
                                }
                                //save page post
                                $this->FcbPagePosts->save($pagefcb);
                                //reste var
                                unset($pagefcb);
                                break;
                            default:
                                break;
                        }
                    }//end of foreach profile
                    //Print a session message if any problems or everything went well
                    $this->Session->setFlash(__("Successfully Posted."), 'cake-success');
                    //redirect for loosing resubmit on refresh
                    $this->redirect(array('action' => 'index'));
                    //post later
                } else if ($this->request->data['optionsRadios'] != 'postnow' && isset($this->request->data['scheduled'])) {
                    //post later
                    //post when ? 
                    $when = $this->request->data['scheduled'];
                    //get user timezone => Save timezone and when => run cronjob by loading (timezone datetime object) compared with when.   
                    $user_time_zone = $this->Session->read("tmz_$user_id");
                    //print $when; print $user_time_zone; die();
                    //post now
                    //save post and get id 
                    $save['SocialPosts']['uid'] = $user_id;
                    $save['SocialPosts']['message'] = $this->request->data['message'];

                    //test if file build var
                    if ($this->request->data['Document']['submittedfile']['error'] == '0') {
                        //$save['SocialPosts']['file'] = $this->request->data['Document']['submittedfile']['tmp_name'];
                        //handle uploaded file
                        $uplresult = $this->SocialScheduled->processUpload($this->request->data['Document']['submittedfile']);
                        //check result
                        if ($uplresult['success'] == TRUE) {
                            //get filename
                            $filename = $uplresult['filename'];
                        } else {
                            $this->Session->setFlash(__("Something Wrong with the file/picture uploaded." . $uplresult['message']), 'cake-error');
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                    //if no file uploaded
                    else if ($this->request->data['Document']['submittedfile']['error'] == '4') {
                        $filename = 'na';
                    } else {
                        //Unknown 
                        $this->Session->setFlash(__("Something Wrong with the file/picture uploaded."), 'cake-error');
                        $this->redirect(array('action' => 'index'));
                    }
                    //test if link build var
                    if (isset($this->request->data['urlsubmit'])) {
                        $save['SocialPosts']['link'] = $this->request->data['urlsubmit'];
                    } else {
                        $save['SocialPosts']['link'] = 'na';
                    }
                    // user datetime
                    $save['SocialPosts']['dateposted'] = Utilities::GetUserTime($this);
                    //scheduled = 0
                    $save['SocialPosts']['posted'] = 0;

                    //set filename in here to 
                    $save['SocialPosts']['file'] = $filename;

                    //save
                    $result_id = $this->SocialPosts->save($save);
                    //parse profiles, post by profile case 
                    $profiles = $this->request->data['addcontacts'];
                    foreach ($profiles as $profile) {
                        $p_ar = explode('-', $profile);
                        //first element is type
                        $type = $p_ar[0];
                        //switch case by profile
                        switch ($type) {
                            case 'fcbuser':
                                //get fcb id
                                $fcb_uid = $p_ar[1];
                                //save FcbUserPost
                                $userfcb['FcbUserPosts']['id'] = '';
                                $userfcb['FcbUserPosts']['uid'] = $user_id;
                                $userfcb['FcbUserPosts']['post_id'] = $result_id['SocialPosts']['id'];
                                $userfcb['FcbUserPosts']['fcb_post_id'] = '';
                                $userfcb['FcbUserPosts']['fcb_uid'] = $fcb_uid;
                                $userfcb['FcbUserPosts']['errorstatus'] = 0;
                                $userfcb['FcbUserPosts']['scheduled'] = 1;
                                $userfcb['FcbUserPosts']['msg'] = 'Waiting';
                                //save
                                $this->FcbUserPosts->save($userfcb);
                                //reste var
                                unset($userfcb);
                                break;
                            case 'fcbpage':
                                //Save and post on facebook page
                                //get variables
                                $fcb_uid = $p_ar[1];
                                $fcb_page_id = $p_ar[2];
                                //Post on page
                                //save FcbUserPost
                                $pagefcb['FcbPagePosts']['id'] = '';
                                $pagefcb['FcbPagePosts']['uid'] = $user_id;
                                $pagefcb['FcbPagePosts']['post_id'] = $result_id['SocialPosts']['id'];
                                $pagefcb['FcbPagePosts']['fcb_uid'] = $fcb_uid;
                                $pagefcb['FcbPagePosts']['fcb_page_id'] = $fcb_page_id;
                                $pagefcb['FcbPagePosts']['fcb_post_id'] = '';
                                $pagefcb['FcbPagePosts']['errorstatus'] = 0;
                                $pagefcb['FcbPagePosts']['scheduled'] = 1;
                                $pagefcb['FcbPagePosts']['msg'] = 'Waiting';
                                //save page post
                                $this->FcbPagePosts->save($pagefcb);
                                //reste var
                                unset($pagefcb);
                                break;
                            case 'twuser':
                                //get fcb id
                                $tw_id = $p_ar[1];
                                //get id
                                //save UserPost
                                $userftw['TwUserPosts']['id'] = '';
                                $userftw['TwUserPosts']['uid'] = $user_id;
                                $userftw['TwUserPosts']['post_id'] = $result_id['SocialPosts']['id'];
                                $userftw['TwUserPosts']['tw_uid'] = $tw_id;
                                $userftw['TwUserPosts']['tw_post_id'] = '';
                                $userftw['TwUserPosts']['errorstatus'] = 0;
                                $userftw['TwUserPosts']['scheduled'] = 1;
                                $userftw['TwUserPosts']['msg'] = 'Waiting';
                                //save
                                $this->TwUserPosts->save($userftw);
                                //reste var
                                unset($userftw);
                                break;

                            default:
                                break;
                        }
                    }//end of foreach profile
                    //Now set and save scheduled 
                    $scheduled['SocialScheduled']['id'] = '';
                    $scheduled['SocialScheduled']['post_id'] = $result_id['SocialPosts']['id'];
                    $scheduled['SocialScheduled']['datetime'] = $when;
                    $scheduled['SocialScheduled']['usertmz'] = $user_time_zone;
                    //save 
                    $this->SocialScheduled->save($scheduled);
                    //successfully scheduled
                    $this->Session->setFlash(__("Scheduled."), 'cake-success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    //not the right submit data
                    $this->Session->setFlash(__("Something went wrong! I don't recognise that kind of submit."), 'cake-error');
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Session->setFlash(__("Something went wrong! Message and/or profile missing."), 'cake-error');
                $this->redirect(array('action' => 'index'));
            }
        }
        //load data for view Fb User
        if ($this->Social->hasAny(array('uid' => $user_id))) {
            //get type and id.
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('uid' => $user_id)),
                    //'fields' => array('', ''),
                    //'order' => array('date'=>'desc')
            );
            //fb users
            $fbusers = $this->Social->find('all', $get);
            //set for view
            $this->set('fbusers', $fbusers);
            //fb pages
            if ($this->FbPages->hasAny(array('uid' => $user_id))) {
                //get type and id.
                $get = array(
                    //'limit' => 10,
                    'conditions' => array(array('uid' => $user_id)),
                        //'fields' => array('', ''),
                        //'order' => array('date'=>'desc')
                );
                //fb users
                $fbpages = $this->FbPages->find('all', $get);
                //set for view
                $this->set('fbpages', $fbpages);
            }
        }

        //load data for view Twitter User
        if ($this->Twitter->hasAny(array('uid' => $user_id))) {
            //get type and id.
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('uid' => $user_id)),
                    //'fields' => array('', ''),
                    //'order' => array('date'=>'desc')
            );
            //fb users
            $twusers = $this->Twitter->find('all', $get);
            //set for view
            $this->set('twusers', $twusers);
        }
        //load everything for DOM
        $scheduled = array();
        $posted = array();
        //check if any post for this user
        if ($this->SocialPosts->hasAny(array('uid' => $user_id))) {
            //get type and id.
            $getposted = array(
                'joins' => array(
                    array(
                        'table' => 'social_scheduled',
                        'alias' => 'TableAlias',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'SocialPosts.id = TableAlias.post_id',
                        ),
                    ),
                ),
                'limit' => 1000,
                'conditions' => array(array('uid' => $user_id, 'deleted' => 0, 'posted' => 1)),
                'fields' => array('SocialPosts.id', 'SocialPosts.id', 'SocialPosts.uid', 'SocialPosts.message', 'SocialPosts.file'
                    , 'SocialPosts.link', 'SocialPosts.dateposted', 'SocialPosts.deleted', 'SocialPosts.posted', 'TableAlias.datetime'),
                'order' => 'id DESC',
            );
            //fb users
            $posted = $this->SocialPosts->find('all', $getposted);
            //now split betwen scheduled and posted
//            foreach ($user_posts as $post) {
//                //if is scheduled
//                if ($post['SocialPosts']['posted'] == '0') {
//                    array_push($scheduled, $post);
//                }
//                //if is posted
//                else if ($post['SocialPosts']['posted'] == '1') {
//                    array_push($posted, $post);
//                }
//            }
            $getscheduled = array(
                'joins' => array(
                    array(
                        'table' => 'social_scheduled',
                        'alias' => 'TableAlias',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'SocialPosts.id = TableAlias.post_id',
                        ),
                    ),
                ),
                'conditions' => array(array('uid' => $user_id, 'deleted' => 0, 'posted' => 0)),
                'fields' => array('SocialPosts.id', 'SocialPosts.id', 'SocialPosts.uid', 'SocialPosts.message', 'SocialPosts.file'
                    , 'SocialPosts.link', 'SocialPosts.dateposted', 'SocialPosts.deleted', 'SocialPosts.posted', 'TableAlias.datetime'),
                'order' => 'id DESC',
            );
            //scheduled
            $scheduled = $this->SocialPosts->find('all', $getscheduled);
        }
        //if something on array
        if (!empty($scheduled)) {
            //set for view
            $this->set('scheduled', $scheduled);
        }
        //if something on array
        if (!empty($posted)) {
            //set for view
            $this->set('posted', $posted);
        }
//        //object
//        $linkedIn = new Happyr\LinkedIn\LinkedIn('77iv44w26klbxn', 'ZPPojDgBs3Nc1FwB');
//        
//        echo $linkedIn->getLoginUrl(array('redirect_uri' => 'http://app-dev.emailbykeywords.com/social/linkedin'));
//        
//        //LinkediIn url setup
//        $this->set('linkedInUrl',$linkedIn->getLoginUrl(array('redirect_uri' => 'http://app-dev.emailbykeywords.com/social/lnkin',
//            'state' => '309a98c893f223c4b0c15d2131762f43')));
    }
    
    /**
     * Method new post.
     * 
     * 
     */
    public function newpost(){
         //uid
        $user_id = $this->Session->read('Auth.User.id');
        //If there is an ajax request
        if ($this->request->is('Ajax')) {
            //Set json header
            header('Content-type: application/json');
            //Load layout
            $this->layout = 'ajax';
            //scrap website request
            if (isset($this->request->data['scrap'])) {
                $data = ScrapSite::run($this->request->data['url']);
                if ($data['status'] == 'success') {
                    echo json_encode($data);
                    exit(1);
                }
            }
            //Break
            exit(1);
        }
        //load data for view Fb User
        if ($this->Social->hasAny(array('uid' => $user_id))) {
            //get type and id.
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('uid' => $user_id)),
                    //'fields' => array('', ''),
                    //'order' => array('date'=>'desc')
            );
            //fb users
            $fbusers = $this->Social->find('all', $get);
            //set for view
            $this->set('fbusers', $fbusers);
            //fb pages
            if ($this->FbPages->hasAny(array('uid' => $user_id))) {
                //get type and id.
                $get = array(
                    //'limit' => 10,
                    'conditions' => array(array('uid' => $user_id)),
                        //'fields' => array('', ''),
                        //'order' => array('date'=>'desc')
                );
                //fb users
                $fbpages = $this->FbPages->find('all', $get);
                //set for view
                $this->set('fbpages', $fbpages);
            }
        }
        //load data for view Twitter User
        if ($this->Twitter->hasAny(array('uid' => $user_id))) {
            //get type and id.
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('uid' => $user_id)),
                    //'fields' => array('', ''),
                    //'order' => array('date'=>'desc')
            );
            //fb users
            $twusers = $this->Twitter->find('all', $get);
            //set for view
            $this->set('twusers', $twusers);
        }
        
    }
    
    /**
     * Load all posted social messages.
     * 
     * 
     */
    public function viewposted() {
        //uid
        $user_id = $this->Session->read('Auth.User.id');
        //check validation 
        if (!$this->ValidTo->isValid($user_id)){
            $this->Session->setFlash(__('Your USE period has expired! Please buy more searches to be able to use the system.'), 'cake-error');
            $this->redirect(array('controller'=>'buy','action' => 'index'));
            exit(1);
        }
        //check if any post for this user
        if ($this->SocialPosts->hasAny(array('uid' => $user_id))) {
            //get type and id.
            $getposted = array(
                'joins' => array(
                    array(
                        'table' => 'social_scheduled',
                        'alias' => 'TableAlias',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'SocialPosts.id = TableAlias.post_id',
                        ),
                    ),
                ),
                'conditions' => array(array('uid' => $user_id, 'deleted' => 0, 'posted' => 1)),
                'fields' => array('SocialPosts.id', 'SocialPosts.id', 'SocialPosts.uid', 'SocialPosts.message', 'SocialPosts.file'
                    , 'SocialPosts.link', 'SocialPosts.dateposted', 'SocialPosts.deleted', 'SocialPosts.posted', 'TableAlias.datetime'),
                'order' => 'id DESC',
            );
            //fb users
            $posted = $this->SocialPosts->find('all', $getposted);
            //if something on array
            if (!empty($posted)) {
                //set for view
                $this->set('posted', $posted);
            }
        }
    }
    /**
     * Load all posted social messages.
     * 
     * 
     */
    public function view($post_id) {
        //user_id
        $user_id = $this->Session->read('Auth.User.id');
        //check validation 
        if (!$this->ValidTo->isValid($user_id)){
            $this->Session->setFlash(__('Your USE period has expired! Please buy more searches to be able to use the system.'), 'cake-error');
            $this->redirect(array('controller'=>'buy','action' => 'index'));
            exit(1);
        }
        //Implement security ... if user has post_id
        //if 
        if ($this->SocialPosts->hasAny(array('uid' => $user_id, 'id' => $post_id))) {
            //fb pages
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('id' => $post_id)),
                    //'fields' => array('', ''),
                    //'order' => array('date'=>'desc')
            );
            //get social post. Set it for view.
            $post = $this->SocialPosts->find('first',$get);
            //set post for view
            $this->set('post',$post);
            
            //build get
            $get = array(
                'joins' => array(
                    array(
                        'table' => 'user_fcb',
                        'alias' => 'TableAlias',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'FcbUserPosts.fcb_uid = TableAlias.fcb_id',
                        ),
                    ),
                ),
                //'limit' => 10,
                'conditions' => array(array('post_id' => $post_id,'errorstatus' => 1,'inactive'=>0)),
                'fields' => array('FcbUserPosts.id', 'FcbUserPosts.post_id', 'FcbUserPosts.fcb_uid', 'FcbUserPosts.fcb_post_id'
                    , 'FcbUserPosts.errorstatus', 'FcbUserPosts.scheduled', 'FcbUserPosts.msg', 'TableAlias.full_name', 'TableAlias.p_picture'),
                'order' => 'id DESC',
                    //'order' => array('date'=>'desc')
            );
            //fb users
            $allfbuserpost = $this->FcbUserPosts->find('all', $get);
            if (!empty($allfbuserpost)) {
                $userdata = array();
                foreach ($allfbuserpost as $fbuserpost) {
                    // set/reset 
                    $data = array();
                    //print_r($fbuserpost);
                    $result = $this->Social->getLikesAndComments($fbuserpost['FcbUserPosts']['fcb_post_id'], $fbuserpost['FcbUserPosts']['fcb_uid'], $user_id);
                    if($result['status']=='success'){
                         //set info for this user
                        $data['profile'] = $fbuserpost['TableAlias'];
                        $data['statistics'] = $result;
                        //set data
                        $userdata[] = $data;
                    }
                    else{
                        //expired token...
                        //name
                        $fname = $this->Social->getFbName($fbuserpost['FcbUserPosts']['fcb_uid']);
                        $this->Session->setFlash(__("There was something wrong with facebook acccount $fname. Propably an expired authentication of Facebook!"), 'cake-error');
                        $this->redirect(array('action'=>'expiredtoken',$fname));
                    }
                }
                //set for view
                $this->set('fbuserspost', $userdata);
            }
            //build get
            $getpages = array(
                'joins' => array(
                    array(
                        'table' => 'fcb_pages',
                        'alias' => 'TableAlias',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'FcbPagePosts.fcb_page_id = TableAlias.fcb_page_id',
                        ),
                    ),
                ),
                //'limit' => 10,
                'conditions' => array(array('post_id' => $post_id,'errorstatus' => 1,'inactive'=>0)),
                'fields' => array('FcbPagePosts.id', 'FcbPagePosts.post_id', 'FcbPagePosts.fcb_uid', 'FcbPagePosts.fcb_post_id', 'FcbPagePosts.fcb_page_id'
                    , 'FcbPagePosts.errorstatus', 'FcbPagePosts.scheduled', 'FcbPagePosts.msg', 'TableAlias.name', 'TableAlias.p_picture'),
                'order' => 'id DESC',
                    //'order' => array('date'=>'desc')
            );

            //fb pages
            $allfbpagespost = $this->FcbPagePosts->find('all', $getpages);
            if (!empty($allfbpagespost)) {
                //parse and get info
                foreach ($allfbpagespost as $fbpagespost) {
                    // set/reset 
                    $data = array();
                    //get info
                    $result = $this->Social->getLikesAndComments($fbpagespost['FcbPagePosts']['fcb_post_id'], $fbpagespost['FcbPagePosts']['fcb_uid'], $user_id);
                    //set info 
                    $data['profile'] = $fbpagespost['TableAlias'];
                    $data['statistics'] = $result;
                    //set data
                    $pagedata[] = $data;
                }
                ///set for view
                //set for view
                $this->set('fbpagespost', $pagedata);
            }

        //build get
        $gettwitter = array(
            'joins' => array(
                    array(
                        'table' => 'user_tw',
                        'alias' => 'TableAlias',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'TwUserPosts.tw_uid = TableAlias.tw_id',
                        ),
                    ),
                ),
            //'limit' => 10,
            'conditions' => array(array('post_id' => $post_id,'errorstatus' => 1,'inactive'=>0)),
                'fields' => array('TwUserPosts.id', 'TwUserPosts.post_id', 'TwUserPosts.tw_uid', 'TwUserPosts.tw_post_id'
                    , 'TwUserPosts.errorstatus', 'TwUserPosts.scheduled', 'TwUserPosts.msg', 'TableAlias.name', 'TableAlias.p_picture'),
                'order' => 'id DESC',
                //'order' => array('date'=>'desc')
        );
        //fb users
        $alltwitterac = $this->TwUserPosts->find('all', $gettwitter);
        //if something
        if (!empty($alltwitterac)) {
            //parse and get info
            foreach ($alltwitterac as $twitterac) {
                 // set/reset 
                $data = array();
                //get info
                $result = $this->Twitter->getTwitterLookup($twitterac['TwUserPosts']['tw_post_id'], 
                                        $twitterac['TwUserPosts']['tw_uid'], $user_id);
                //check result
                if($result['status']=='success'){
                    // set info 
                    $data['profile'] = $twitterac['TableAlias']; 
                    $data['statistics'] = $result['data'];
                    // set info 
                    $twitterdata[]=$data;
                }
            }
            //set for view
            $this->set('twuserposts', $twitterdata);
        }
        } else {
            //hmmm somebody sending bad http requests 
            //$this->Session->setFlash(__("You are not authorized to perform this operation!"), 'cake-error');
            $this->redirect(array('action' => 'index'));
        }
    }
    
    //
    /**
     *expired token 
     * 
     * handdle expired toke cases on fb users 
     * 
     */
    public function expiredtoken($name){
        $url = $this->Social->RequestNewToken();
        //set some data
        $this->set('name',$name);
        $this->set('url',$url);
        
    }

    /**
     * newProfile method.
     * Will check on db if is a new profile or the user is already registered
     * 
     */
    protected function newProfile($user_id, $fcb_uid) {
        //check if is any
        $conditions = array('uid' => $user_id, 'fcb_id' => $fcb_uid);
        //fb 
        $get = array(
                'conditions' => array($conditions),
                    //'order' => array('date'=>'desc')
            );
            //get social post. Set it for view.
            $result = $this->Social->find('first',$get);
        //check if there
        if (empty($result)) {
            return array('new'=>TRUE);
        } else {
            //exists
            return array('id'=>$result['Social']['id']);
        }
    }

    /**
     * getUserProfile method.
     * Will return user profile on facebook
     * 
     */
    protected function getUserProfile() {
        $profile = (new FacebookRequest(
                $this->fbSession, 'GET', '/me'
                ))->execute()->getGraphObject(GraphUser::className());
        //return
        return $profile;
    }

    /**
     * delete method.
     * Will remove user's social post
     * 
     */
    public function delete($post_id) {
        //get user id.
        if (!isset($user_id)) {
            $user_id = $this->Session->read('Auth.User.id');
        }
        //check if is any
        $conditions = array('id' => $post_id, 'uid' => $user_id);
        if ($this->SocialPosts->hasAny($conditions)) {
            $delete_update['SocialPosts']['id'] = $post_id;
            $delete_update['SocialPosts']['deleted'] = 1;
            //remove post from everywhere 
            if ($this->SocialPosts->save($delete_update)) {
                //check if any scheduled
                $cond = array('post_id' => $post_id);
                if ($this->SocialScheduled->hasAny($cond)) {
                    $delete = array('post_id' => $post_id);
                    //remove scheduled
                    $this->SocialScheduled->deleteAll($delete);
                }
                $this->Session->setFlash(__("Post Succesfully Removed."), 'cake-success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__("Something Went Wrong! Please try again later."), 'cake-error');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            //hmmm somebody sending http requests trying to delete posts?!
            $this->Session->setFlash(__("You are not authorized to perform this operation!"), 'cake-error');
            $this->redirect(array('action' => 'index'));
        }
    }

    /**
     * getProfilePicture method.
     */
    protected function getProfilePicture() {
        //Get picture
        $picture = (new FacebookRequest(
                $this->fbSession, 'GET', '/me/picture?type=small&redirect=false'
                ))->execute()->getGraphObject()->asArray();
        //return
        return $picture;
    }

    /**
     * UserAccounts method.
     * Facebook api call for accounts managed by user 
     * 
     */
    protected function userAccounts($user_id, $fcb_uid) {
        //Check and save pages
        $request = new FacebookRequest(
                $this->fbSession, 'GET', '/me/accounts'
        );
        $response = $request->execute();
        $graphObject = $response->getGraphObject()->asArray();
        //if there is any page listed for user
        if (!empty($graphObject)) {
            try {
                //pages
                //Save user pages
                $this->FbUserPages($graphObject, $user_id, $fcb_uid);
                // When validation fails or other local issues
                $this->Session->setFlash(__("Profile succesfully saved!"), 'cake-success');
                $this->redirect(array('action' => 'index'));
            } catch (Exception $exc) {
                $error = $exc->getMessage();
                // When validation fails or other local issues
                $this->Session->setFlash(__("Something went wrong Error: $error ."), 'cake-error');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            //redirect without parsing or saving any pages
            $this->Session->setFlash(__("Profile succesfully saved!"), 'cake-success');
            $this->redirect(array('action' => 'index'));
        }
    }

    /* FbUserPages
     * Interact / Save user pages 
     * 
     */

    public function FbUserPages($user_pages, $user_id, $fcb_id) {
        //set var
        $pages = array();
        //parse build, insert
        foreach ($user_pages['data'] as $key) {
            //Get picture
            $page_id = $key->id;
            $request = new FacebookRequest(
                    $this->fbSession, "GET", "/$page_id/picture?type=small&redirect=false"
            );
            $response = $request->execute();
            $p_picture = $response->getGraphObject()->asArray();
            //build array element
            $element = array('uid' => $user_id,
                'fcb_uid' => $fcb_id,
                'fcb_page_id' => $key->id,
                'access_token' => $key->access_token,
                'name' => $key->name,
                'p_picture' => $p_picture['url'],
                'perms' => $key->perms[0], //only the first element level
                'category' => $key->category
            );
            //push on array
            array_push($pages, $element);
        }

        //Save
        if ($this->FbPages->saveMany($pages)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * WriteToken method.
     *
     * Will handle facebook token's .
     * 
     *  
     */
    public function WriteToken($token) {
        //write
        if ($this->Session->write('Auth.User.fb_token', $token)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * ReadToken method.
     *
     * Will handle facebook tokens .
     * 
     *  
     */
    public function ReadToken() {
        if ($token = $this->Session->read('Auth.User.fb_token')) {
            //Destroy current token
            $this->Session->delete('Auth.User.fb_token');
            //return
            return $token;
        } else {
            return NULL;
        }
    }

}

?>