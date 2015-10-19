<?php
/* TmzController
 * Will interact with custom.js 
 * and handle client timezone 
 * 
 * 
 * @copyright Ondi Gusho.
 */
class TmzController extends AppController{
    /**
    * Index method.
     * Will write user's timezone. 
    */
    public function index() {
        //user_id
        $user_id = $this->Session->read('Auth.User.id');
        //check if session is set
        if (!$this->Session->check("tmz_$user_id")){
               $this->Session->write("tmz_$user_id",$_POST['timezone']);
        }
        exit(1);
    }
}
?>