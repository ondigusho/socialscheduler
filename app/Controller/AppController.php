<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
//Load GeoLocation
App::uses('GeoIpLocation', 'GeoIp.Model');


/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $components = array(
        'Cookie',
        'Session',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'home', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login'),
            'authenticate' => array(
            'Form' => array(
                'fields' => array('username' => 'email','password' => 'password')
            )
         )
      )
    ,'Security');
    
    public function beforeFilter() {
        //Check for mobile
        $this->Security->blackHoleCallback = 'forceSSL';
        $this->Security->requireSecure('login', 'checkout');
        // set cookie options
        $this->Cookie->httpOnly = true;
     
        if (!$this->Auth->loggedIn() && $this->Cookie->read('rememberMe')) {
            $cookie = $this->Cookie->read('rememberMe');
 
            $this->loadModel('User'); // If the User model is not loaded already
            $user = $this->User->find('first', array(
                'conditions' => array(
                    'User.email' => $cookie['email'],
                    'User.password' => $cookie['password']
                )
         ));
     
         if ($user && !$this->Auth->login($user['User'])) {
               $this->redirect($this->Auth->redirect());
         }
     }
    }
    //Forece ssl
    function forceSSL() {
        //Uncoment on production and redirect on https
        //$this->redirect('https://' . $_SERVER['SERVER_NAME'] . $this->here);
    }
}
