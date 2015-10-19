<?php

/* UsersController
 * Will load users page Dashboard
 * Will handle authetication into dashboard 
 * 
 * 
 * @copyright Ondi Gusho.
 */

class UsersController extends AppController {

    //Set Models Used
    var $uses = array('User', 'ActivateUser', 'ValidTo', 'Smtp', 'MailAddress');

    /* beforeFilter method
     * 
     * Allow user add
     * 
     */

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'recusr', 'recpwd', 'activate', 'ismobile', 'osbrowser');
    }

    /* login method
     * 
     * Authentication
     */

    public function login() {
        //get user agent and check if we support.
        $agent = $this->request->header('User-Agent');
        if (!UserAgent::isAllowed($agent)) {
            $this->redirect(array('action' => 'osbrowser'));
            exit(1);
        }
        //Remove this to allow user to use app from mobile device
        //May cause errors/bugs
        if ($this->request->is('mobile')) {
            $this->redirect(array('action' => 'ismobile'));
            exit(1);
        }
        //Load add user layout 
        $this->layout = 'login';
        if ($this->request->is('post')) {
            //make it all lower case
            $this->request->data['User']['email'] = strtolower($this->request->data['User']['email']);
            if ($this->Auth->login()) {
                if ($this->Auth->user('status') != '1') {
                    $this->Session->setFlash(__('Your user is not active yet! Check your email and activate your user. Check your spam or junk folders. It is possible that your email was automatically filtered out of your inbox.'), 'cake-error');
                    //It is not active
                    $this->logout();
                }
                if ($this->request->data['User']['rememberMe'] == 1) {
                    // After what time frame should the cookie expire
                    $cookieTime = "12 months"; // You can do e.g: 1 week, 17 weeks, 14 days
                    // remove "remember me checkbox"
                    unset($this->request->data['User']['rememberMe']);
                    // hash the user's password
                    $this->request->data['User']['password'] = $this->Auth->password($this->request->data['User']['password']);
                    // write the cookie
                    $this->Cookie->write('rememberMe', $this->request->data['User'], true, $cookieTime);
                }
                //Update Login
                $user_id = $this->Session->read('Auth.User.id');
                //build user object
                $user['User']['id'] = $user_id;
                //set dt
                $dt = new DateTime("NOW");
                $user['User']['last_login'] = $dt->format('Y-m-d H:i:s');
                //save
                $this->User->save($user);
                //Default Redirect
                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(__('Invalid username or password, try again'), 'cake-error');
            }
        }
    }

    /* isMobile
     *
     * this is not supported yet 
     */

    public function ismobile() {
        //Load ismobile 
        $this->layout = 'notsupported';
    }

    /* osbrowser
     *
     * this is not supported yet 
     */

    public function osbrowser() {
        //Load osbrowser error
        $this->layout = 'notsupported';
    }

    /* recpwd method
     * 
     * Will recover and update 
     * lost password.
     * 
     * 
     */

    public function recpwd() {
        //Load layout 
        $this->layout = 'login';
        if ($this->request->is('post')) {
            //set query conditions
            $conditions = array('email' => $this->request->data['User']['email']);
            if ($this->User->hasAny($conditions)) {
                $record = $this->User->find('first', array(
                    'conditions' => array('email' => $this->request->data['User']['email'])));
                //Generate a temporary password.
                $tmpPwd = Utilities::GenPassword();
                //Encode the new password
                $record['User']['password'] = $tmpPwd;
                //Send an email receipt
                $message = "Hi " . $firstName . ", \n\n";
                $message .= "Please use the temporary password to login. \n";
                $message .= "Temporary password : " . $tmpPwd . "\n\n\n";
                $message .= "If you did not requested a password recovery please contact support@emailbykeywords.com.\n";
                //Update 
                //send email
                if (Utilities::no_reply_email($this->request->data['User']['email'], 'Password recovery', $message) && $this->User->save($record)) {
                    $this->Session->setFlash(__('An email with new temporary password has been send.'), 'cake-success');
                    $this->redirect(array('action' => 'login'));
                } else {
                    $this->Session->setFlash(__('The operation could not be completed. Please, try again.'));
                }
            } else {
                $this->Session->setFlash(__('The address provided does not exist! Please use form below to register.'), 'cake-error');
                $this->redirect(array('action' => 'add'));
            }
        }
    }

    /* chpwd method
     * 
     * Will update password from user's profile 
     * 
     * 
     */

    public function chpwd($id = NULL) {
        //Check for cancel action
        if (isset($this->request->data['cancel'])) {
            $this->redirect(array('controller' => 'main', 'action' => 'index'));
        }
        //By passing the id and on session
        if (!isset($id)) {
            $id = $this->Session->read('Auth.User.id');
        }
        //Get id
        $this->User->id = $id;
        //Check if user?
        if (!$this->User->exists()) {
            $this->Session->setFlash(__('The user does not exist!'), 'cake-error');
            $this->redirect(array('action' => 'index'));
        }
        //Check if post of pwd change
        if ($this->request->is('post') || $this->request->is('put')) {
            //Get User
            $record = $this->User->find('first', array(
                'conditions' => array('id' => $id)));
            $decTestPwd = AuthComponent::password($this->request->data['User']['password']);
            //Test 
            if ($record['User']['password'] === $decTestPwd) {
                if ($this->request->data['User']['pass1'] === $this->request->data['User']['pass2']) {
                    //Load new password
                    $record['User']['password'] = $this->request->data['User']['pass1'];
                    //Change password
                    if ($this->User->save($record['User'])) {
                        $this->Session->setFlash(__('Password changed! Thank you.'), 'cake-success');
                        $this->redirect(array('action' => 'chpwd', $id));
                    }
                } else {
                    $this->Session->setFlash(__('Your second password does not mach! Please try again.'), 'cake-error');
                    $this->redirect(array('action' => 'chpwd', $id));
                }
            } else {
                $this->Session->setFlash(__('Your old passsword does not mach! Please try again.'), 'cake-error');
                $this->redirect(array('action' => 'chpwd', $id));
            }
        }
    }

    /* recusr method
     * 
     * Will send an email with email address.
     * email address is also the username!
     * 
     * 
     */

    public function recusr() {
        //Load layout 
        $this->layout = 'login';
        if ($this->request->is('post')) {
            //set query conditions
            $conditions = array('email' => $this->request->data['User']['email']);
            if ($this->User->hasAny($conditions)) {
                //Get User
                $record = $this->User->find('first', array(
                    'conditions' => array('email' => $this->request->data['User']['email'])
                        )
                );
                //Get first name
                $firstName = $record['User']['fname'];
                //Send an email receipt
                $message = "Hi " . $firstName . ", \n\n";
                $message .= "Your username is actually your email address : " . $this->request->data['User']['email'] . "\n\n\n";
                $message .= "If you did not requested a username recovery please contact support@emailbykeywords.com.\n";
                //Test, send email 
                if (Utilities::no_reply_email($this->request->data['User']['email'], 'Username recovery', $message)) {
                    $this->Session->setFlash(__('An email with your username was send.'), 'cake-success');
                    $this->redirect(array('action' => 'login'));
                } else {
                    $this->Session->setFlash(__('The operation could not be completed. Please, try again later.'));
                }
            } else {
                $this->Session->setFlash(__('The  email address proveded does not exist! Please use form below to register.'), 'cake-error');
                $this->redirect(array('action' => 'add'));
            }
        }
    }

    /* activate method
     * 
     * Will activate user user. 
     * 
     * 
     */

    public function activate($hash = NULL, $uid = NULL) {
        //Check hash
        if (!isset($hash) || !isset($uid)) {
            $this->Session->setFlash(__('Bad Request!'), 'cake-error');
            $this->redirect(array('action' => 'login'));
        }
        $conditions = array('hash' => $hash, 'user_id' => $uid);
        if ($this->ActivateUser->hasAny($conditions)) {
            $userCond = array('id' => $uid);
            if ($this->User->hasAny($userCond)) {
//                $record = $this->User->find('first',array('fields' => array('status'),
//                        'conditions' => array('id' => $uid)));
                //Activate account
                $record['User']['id'] = $uid;
                $record['User']['status'] = 1;
                //Add 30 days use period on first activation
                //get now
                $dt = new DateTime();
                $now = $dt->format('Y-m-d H:i:s');
                $date = strtotime("+ 30 days", strtotime($now));
                $update = date("Y-m-d H:i:s", $date);
                $record['User']['valid_to'] = $update;
                //Update 
                if ($this->User->save($record)) {
                    //remove
                    $this->ActivateUser->deleteAll(array('user_id' => $uid, 'hash' => $hash));
                    //redirect
                    $this->Session->setFlash(__('Your account is active. Use form below to login.'), 'cake-success');
                    $this->redirect(array('action' => 'login'));
                }
            } else {
                $this->Session->setFlash(__('Bad Request!'), 'cake-error');
                $this->redirect(array('action' => 'login'));
            }
        } else {
            $this->Session->setFlash(__('Bad Request!'), 'cake-error');
            $this->redirect(array('action' => 'login'));
        }
    }

    /* Add user
     * 
     * 
     */

    public function add() {
        //Load add user layout 
        $this->layout = 'adduser';
        if ($this->request->is('post')) {
            if ($this->request->data['User']['agreeterms'] == 1) {
                //Check if the emails is already used
                $conditions = array('email' => $this->request->data['User']['email']);
                if ($this->User->hasAny($conditions)) {
                    //This email is already in user
                    $email = $this->request->data['User']['email'];
                    $this->Session->setFlash(__("The email $email is already used. Please $email to login or use another email address!"), 'cake-error');
                }
                //All good. Save the data
                else {
                    $this->User->create();
                    //make it all lower case
                    $this->request->data['User']['email'] = strtolower($this->request->data['User']['email']);
                    if ($result = $this->User->save($this->request->data)) {
                        //add smtp settings default
                        $smtp['Smtp']['user_id'] = $result['User']['id'];
                        $smtp['Smtp']['email'] = $this->request->data['User']['email'];
                        //save smtp
                        $this->Smtp->save($smtp);
                        //generate unique key
                        $hash = Utilities::Hash();
                        //build data for save
                        //send email and redirect
                        $activeUrl = Router::url('/', true) . 'users/activate/' . $hash . '/' . $result['User']['id'];

                        //Send an email receipt
                        $message = "Hi " . $result['User']['fname'] . ", \n\n";
                        $message .= "Thank you and welcome to Emailbykeywords! \n\n";
                        $message .= "The username for this account is your email address. : \n\n";
                        $message .= "Please click the link below in order to activate your account.\n";
                        $message .= $activeUrl . "\n\n";
                        $message .= "If you are having problems setting up SMTP connection please click link below.\n";
                        $message .= "http://emailbykeywords.com/index.php?p=smtp \n\n";
                        //data activation
                        $activateData = array('user_id' => $result['User']['id'], 'email' => $this->request->data['User']['email'], 'hash' => $hash);
                        if ($this->ActivateUser->save($activateData) && Utilities::no_reply_email($this->request->data['User']['email'], 'Emailbykeywords User Activation', $message)) {
                            //all saved
                            $this->Session->setFlash(__("The user has been saved. Please check your email and activate user. Check your spam or junk folders. It is possible that your email was automatically filtered out of your inbox."), 'cake-success');
                            $this->redirect(array('controller' => 'users', 'action' => 'login'));
                        } else {
                            $this->Session->setFlash(__('An error ocured! Please try again later.'), 'cake-error');
                            $this->redirect(array('controller' => 'users', 'action' => 'login'));
                        }
                    }
                }
            } else {
                $this->Session->setFlash(__('You must agree with Terms And Conditions.'));
            }
        }
    }

    /* logout and redirect
     * 
     */

    public function logout() {
        $this->Cookie->delete('rememberMe');
        $this->redirect($this->Auth->logout());
    }

    /**
     * Index method.
     *
     * 
     */
    public function index($id = NULL) {
        //Check for cancel action
        if (isset($this->request->data['cancel'])) {
            $this->redirect(array('controller' => 'main', 'action' => 'index'));
        }
        //By passing the id and on session
        if (!isset($id)) {
            $id = $this->Session->read('Auth.User.id');
        }
        //Get id
        $this->User->id = $id;
        //Check if user?
        if (!$this->User->exists()) {
            $this->Session->setFlash(__('The user does not exist!'), 'cake-error');
            $this->redirect(array('action' => 'index'));
        }
        //Check if edit/save 
        if ($this->request->is('post') || $this->request->is('put')) {
            //IF address set
            if(isset($this->request->data['MailAddress'])){
                //save
                $this->MailAddress->save($this->request->data);
                $this->Session->setFlash(__('Your changes have been saved.'), 'cake-success');
                $this->redirect(array('controller' => 'users', 'action' => 'index'));
            }
            else{
                if ($this->User->save($this->request->data)) {
                    $this->Session->setFlash(__('Your changes have been saved.'), 'cake-success');
                    $this->redirect(array('controller' => 'users', 'action' => 'index'));
            } else {
                    $this->Session->setFlash(__('The user cannot be saved. Please, try again.', 'cake-error'));
                }
            }
        }
        //Load user for view
        $this->set('user', $this->User->read(null, $id));
        //Get address 
        $getaddr = array(
            'conditions' => array('user_id' => $id),
        );
        $myaddr = $this->MailAddress->find('first', $getaddr);
        //if not empty
        if (empty($myaddr)) {
            $myaddr['MailAddress']['id'] = '';
            $myaddr['MailAddress']['user_id'] = $id;
            $myaddr['MailAddress']['street_addr'] = '';
            $myaddr['MailAddress']['state'] = '';
            $myaddr['MailAddress']['city'] = '';
            $myaddr['MailAddress']['zip'] = '';
            $myaddr['MailAddress']['country'] = '';
        }
        //set on view
        $this->set('myaddr', $myaddr);
    }

}

?>
