<?php
/* Email Class 
 * Email Send
 * 
 * @copyright Ondi Gusho.
 */

class Email {
    
    /*send method
    *  
    * 
    * @return boolean.
    */
    public function send($subject,$body,$from,$to) {
        //Build email
        $email = new CakeEmail();
        //from
        $email->from($from);
        //to
        $email->to($to);
        //subject
        $email->subject($subject);
        //Send
        if(!$email->send($body)){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
}
        
?>
