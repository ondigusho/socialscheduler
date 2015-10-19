<?php set_time_limit(0);
/* Utility Class 
 * Will contain static methods used 
 * individually in any place anytime 
 * 
 * @copyright Ondi Gusho.
 */

class Utilities {
    
    public static $key = "E4RM7h4DhS23DYfhAvtkS3Nf"; // 24 bit Key
    public static $iv = "nYfoHeDl"; // 8 bit IV
    public static $input = "Text to encrypt"; // text to encrypt
    public static $bit_check = 8; // bit amount for diff algor.

//$str = encrypt($input, $key, $iv, $bit_check);
//echo "Start: $input - Excrypted: $str - Decrypted: " . decrypt($str, $key, $iv, $bit_check);
    /* encrypt method
     *
     * @return string encrypted.  
     */    
    public static function encrypt($text) {
        $text_num = str_split($text, Utilities::$bit_check);
        $text_num = Utilities::$bit_check - strlen($text_num[count($text_num) - 1]);
        for ($i = 0; $i < $text_num; $i++) {
            $text = $text . chr($text_num);
        }
        $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'cbc', '');
        mcrypt_generic_init($cipher, Utilities::$key, Utilities::$iv);
        $decrypted = mcrypt_generic($cipher, $text);
        mcrypt_generic_deinit($cipher);
        return base64_encode($decrypted);
    }
    /* decrypt method
         *
         * @return string encrypted.  
         */ 
    public static function decrypt($encrypted_text) {
        $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'cbc', '');
        mcrypt_generic_init($cipher, Utilities::$key, Utilities::$iv);
        $decrypted = mdecrypt_generic($cipher, base64_decode($encrypted_text));
        mcrypt_generic_deinit($cipher);
        $last_char = substr($decrypted, -1);
        for ($i = 0; $i < Utilities::$bit_check - 1; $i++) {
            if (chr($i) == $last_char) {
                $decrypted = substr($decrypted, 0, strlen($decrypted) - $i);
                break;
            }
        }
        return $decrypted;
    }
    
    /* super_unique method
    *
    */ 
    function super_unique($array, $key) {

        $temp_array = array();

        foreach ($array as &$v) {

            if (!isset($temp_array[$v[$key]]))
                $temp_array[$v[$key]] = & $v;
        }

        $array = array_values($temp_array);

        return $array;
    }

    /* GenPassword method
     * Will generate a random password 
     *
     * @param  length default 6 
     * @return string random char.  
     */    
    public static function GenPassword ($length = 6)
    {
        // given a string length, returns a random password of that length
        $password = "";
        // define possible characters
        $possible = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        // add random characters to $password until $length is reached
        while ($i < $length) {
        // pick a random character from the possible ones
        $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        // we don't want this character if it's already in the password
        if (!strstr($password, $char)) {
            $password .= $char;
            $i++;
        }
     }
     return $password;
    }
    
    /*Hash method
    *  
    * Will return single identifier hash string.
    * 
    * @return string hash.
    */
    public static function Hash(){
        return md5(uniqid(rand(), true));
    }
    
    /* Truncation of a longer string 
     * 
     * call example $shortdesc = myTruncate($description, 40, " ");
     */    
    public static function myTruncate($string, $limit, $break=".", $pad="..."){
        // return with no change if string is shorter than $limit
        if(strlen($string) <= $limit) return $string;

        // is $break present between $limit and the end of the string?
        if(false !== ($breakpoint = strpos($string, $break, $limit))) {
          if($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
          }
        }
       return $string;
    }
    
    /* GetUserTime method
     */    
    public static function GetUserTime($object= NULL){
        //initiate object
        $dt = new DateTime();
        //user_id
        $user_id = $object->Session->read('Auth.User.id');
        //get tz session
        $tz = $object->Session->read("tmz_$user_id");
        //set tz
        $dt->setTimezone(new DateTimeZone($tz));
        //
        $is = $dt->format('Y-m-d H:i:s');
        //return
        return $is;
    }
    
    
    /* SetUserTime method
     */    
    public static function UserTime($user_tz){
        //initiate object
        $dt = new DateTime();
        //set tz
        $dt->setTimezone(new DateTimeZone($user_tz));
        //
        $is = $dt->format('Y-m-d H:i:s');
        //return
        return $is;
    }
    
    /**
    * do-not-reply 
    * 
    * Will send a quick email to user 
    */    
    public static function no_reply_email($to,$subject,$message){
        $Email = new CakeEmail('ebkw');
        $Email->from(array('info@emailbykeywords.com' => 'Email By Keywords'));
        $Email->to($to);
        $Email->subject($subject);
        //test email
        if($Email->send($message)){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public static function sendemail($to,$from,$full_name,$subject, $body, $email_from, $password, $smtp_server, $port,$send_type,$send_em_type,$tls){
        //by type
        if($send_type=='0'){
            //Set email object
            $Email = new CakeEmail('default');
        }
        else{
            if($send_em_type=='smtp'){
                //Set email object
                $Email = new CakeEmail($send_em_type);
                //Set smtp by case...  if gmail, yahoo, etc
                //Pass smtp, email, and pwd as var's
                //Custom instance of email for testing the connection
                $Email->config(array('username' => $email_from, 
                             'password' => $password,
                             'host' => $smtp_server,
                             'port' => $port
                    )
                );
                //check tls
                if($tls=='1'){
                    //add tls
                    $Email->config(array(
                            'tls' => true
                        )
                    );

                }
            }
            else{
                //Set email object
                $Email = new CakeEmail($send_em_type);
                //Set smtp by case...  if gmail, yahoo, etc
                //Pass smtp, email, and pwd as var's
                //Custom instance of email for testing the connection
                $Email->config(array('username' => $email_from, 
                             'password' => $password,
                          //   'host' => $smtp_server,
                          //   'port' => $port
                    )
                );
            }
        }
        //Set email vars
        $Email->from(array($email_from => "$full_name"));
        $Email->sender($email_from, "$full_name");
        $Email->subject($subject);
        $Email->replyTo($from, $full_name);
        //This will send an html email
        //$headers = "MIME-Version: 1.0\r\n";
        //$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        //set up
        //$Email->headerCharset($headers);
        $Email->charset('utf-8');
        $Email->emailFormat('html');
        //set success
        //$success = TRUE;
        //For each email send
        $Email->to($to);
        try {
            if ($Email->send($body)){
                //send successfully
                $success['status'] = TRUE;
                $success['message'] = 'Successfully Delivered';
            }else{
                //Failure without any exeption
                $success['status'] = FALSE;
                $success['message'] = 'unknown reason';
            }
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $success['status'] = FALSE;
            $success['message'] = $exc->getMessage();
        }
        //success
        return $success;
    }
    /**
    * Email method. Used on campaign email 
    * 
    * Will send a separate email to each value on the array $to_list.
    *  
    * @param  array    tolist
    * @param  string   subject
    * @param  string   message 
    * @param  string   from
    * @param  string   name
    *    
    * @return array    report   
    */    
    public static function email($to_list,$from,$name,$subject,$message,$marketing_id){
        //Get user id
        $user_id = $this->Session->read('Auth.User.id');
        //Set email object
        $Email = new CakeEmail();
        $Email->from(array($from => "$name"));
        $Email->sender($from, "$name");
        $Email->subject($subject);
        //This will send an html email
        //$headers = "MIME-Version: 1.0\r\n";
        //$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        //set up
        //$Email->headerCharset($headers);
        $Email->charset('utf-8');
        $Email->emailFormat('html');
        //set success
        $success = TRUE;
        //For each email send
        foreach ($to_list as $to) {
            //Build a unique id for each email to each recipient
            //Register it on the db.
            //Get the ip, read status etc ... update the data base.
            $hash = Utilities::Hash();
            
            $Email->to($to);
            if (!$Email->send($message)){
                $success = FALSE;
            }
        }
        return $success;
    }

}
?>
