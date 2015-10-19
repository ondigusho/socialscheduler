<?php
/* ValidTo Model
 * Will check user's valid date for account use
 * 
 * @copyright Ondi Gusho.
 */

class ValidTo extends AppModel{
    //Set table
    public $useTable = 'users';

    /* method isValid
     * 
     * @param  int user id 
     * @return boolean   
     * 
    */ 
    public function isValid($user_id){
        //get valid_to
        $get = array(
            //'limit' => 10,
                'conditions' => array('id'=>$user_id),
            );
        //Get data
        $mydata = $this->find('first',$get);
        //data valid
        $valid = $mydata['ValidTo']['valid_to'];
        //Work only with current timestamp
        //get now
        $dt = new DateTime();
        $now =  $dt->format('Y-m-d H:i:s');
        
        //check and return
        if($valid>$now){
            //echo 'Still valid ';
            return TRUE;
        }else{
            //echo 'you are out ...';
            return FALSE;
        }
    }
    /* method addDays
     * 
     * @param  int user id
     * @param  int days inc 
     * @return boolean   
     * 
    */
    public function addDays($user_id,$days_inc){
        //Get current, get now, compare, add 
        //get valid_to
        $get = array(
            //'limit' => 10,
                'conditions' => array('id'=>$user_id),
            );
        //Get data
        $mydata = $this->find('first',$get);
        //data valid
        $valid = $mydata['ValidTo']['valid_to'];
        //Work only with current timestamp
        //get now
        $dt = new DateTime();
        $now =  $dt->format('Y-m-d H:i:s');
        //compare
        if ($now>$valid){
            //add now + days 
            $date = strtotime("+".$days_inc." days", strtotime($now));
            $update = date("Y-m-d H:i:s", $date);
        }else{
            //and mydata + days
            $date = strtotime("+".$days_inc." days", strtotime($valid));
            $update = date("Y-m-d H:i:s", $date);
        }
        //return new date
        return $update;
    }
}
?>