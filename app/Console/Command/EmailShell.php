<?php

/*
 * Emails will be send by this process 
 */
class EmailShell extends AppShell {
    
    //Load used models
    public $uses = array('EmailStatus','Smtp');
    
    //Not sure if neded!
    public function main() {
        
    }
    //send 
    public function send(){
        //Get args
        $tostring = urldecode($this->args[0]);
        $from = urldecode($this->args[1]); 
        $full_name = urldecode($this->args[2]); 
        $subject = urldecode($this->args[3]); 
        $body = urldecode($this->args[4]);
        //Get marketing and user
        $marketing_id = $this->args[5];
        $user_id = $this->args[6];
        //smtp conf data
        $email_from = urldecode($this->args[7]);
        $password = urldecode($this->args[8]);
        $smtp_server = urldecode($this->args[9]);
        $port= urldecode($this->args[10]);
        $send_type= urldecode($this->args[11]);
        $tls= urldecode($this->args[12]);
        //argument for hash ... if false generate hash, and id's
        $all_hashes= urldecode($this->args[13]);
        $all_ids= urldecode($this->args[14]);
          
        //echo 'tls:'.$tls."\n".'hash : '.$all_hashes."\n"; echo " id ".$all_ids."\n";         
        
        //Build and send
        //echo 'To: '.$tostring.'; From : '.$email_from.'; password '.$password.'; server : '.$smtp_server.'; PORT: '.$port;
        //Get send to as array                
        $send_to = explode(',', $tostring);
        
        if($all_hashes!='false'){
           $hashes = explode(',', $all_hashes);
        }
        
        if($all_ids!='false'){
           $ids = explode(',', $all_ids);
        }
        //get email type 
        $em_type = $this->Smtp->getDomain($from);
        //set email type if is one supported
        if(in_array($em_type, $this->Smtp->emails)){
            //set email object
            $to_be_send_emtype = $em_type;
        }else{
            //Set variables 
            $to_be_send_emtype = 'smtp';
        }
        //for getting hashes
        $i = 0;
        //foreach email on the list
            foreach ($send_to as $to){
                //Do hash, only if is false -> first time sent-> it is used for resend also
                if($all_hashes=='false'){
                    $hash = Utilities::Hash();
                }
                else{
                    $hash = $hashes[$i];
                }
                //Do ids
                if($all_ids=='false'){
                    $id = '';
                }
                else{
                    $id = $ids[$i];
                }
                
                //extract to->email and unique encoded hash for Unsubscribe
                $full_to = explode(':', $to);
                //first element is email
                $to_send = $full_to[0];
                $email_id = $full_to[1];
                $lis_type = $full_to[2];
                $hash_str = $email_id.' '.$lis_type;
                $unsubscribe_url = Utilities::encrypt($hash_str);
                //with custom hash for tracking
                $to_be_added = '<div style="font-size:12px;">          
                                        <div>If you no longer wish to receive these emails you may
                                                <a rel="nofollow" target="_blank" href='.Router::fullbaseUrl().'/tracker/unsubscribe/'.$unsubscribe_url.'">Unsubscribe</a> at any time.</div>        
                                    </div>
                                </td>      
                                <td width="25%" valign="bottom" style="text-align:right;">        
                                    <div>          
                                        <a rel="nofollow" target="_blank" href="http://emailbykeywords.com">
                                            <img src="'.Router::fullbaseUrl().'/img/footer_logo.png" width="170" height="51"></a>        
                                    </div>      
                                </td>    
                            </tr>  
                        </tbody>
                    </table>
                    <div style="display:block;min-height:1px;width:1px;color:transparent;">
                        <img src="'.Router::fullbaseUrl().'/tracker/index/'.$hash.'" width="1" height="1"/></div></div></td></tr></tbody></table>';
                //set picture inside the body 
                $custom_body = $body.$to_be_added;
                //send email
                $email = Utilities::sendemail($to_send, $from, $full_name, $subject, $custom_body,$email_from,$password,$smtp_server,$port,$send_type,$to_be_send_emtype,$tls);
		//message
                
                $msg = $email['message'];	
		if($email['status']){ //just for testing
                    //Insert the status send on the database
                    $status['EmailStatus']['id'] = $id;
                    $status['EmailStatus']['user_id'] = $user_id;
                    $status['EmailStatus']['marketing_id'] = $marketing_id;
                    $status['EmailStatus']['email_to'] = $to_send;
                    $status['EmailStatus']['type'] = "$full_to[2]";
                    $status['EmailStatus']['mail_id'] = "$full_to[1]";
                    $status['EmailStatus']['hash'] = $hash;
                    $status['EmailStatus']['status'] = '1';
                    $status['EmailStatus']['status_details'] = $msg;
		    //check save
		    try {
                        $this->EmailStatus->save($status);
        	    } catch (Exception $exc) {
            		$error = $exc->getMessage();
                        //echo $error;
        	    }
                 }
                 //somethign wrong with email send
                 else{
                     //Insert the status sent on the database
                     $status['EmailStatus']['id'] = $id;
                     $status['EmailStatus']['user_id'] = $user_id;
                     $status['EmailStatus']['marketing_id'] = $marketing_id;
                     $status['EmailStatus']['email_to'] = $to_send;
                     $status['EmailStatus']['type'] = "$full_to[2]";
                     $status['EmailStatus']['mail_id'] = "$full_to[1]";
                     $status['EmailStatus']['hash'] = $hash;
                     $status['EmailStatus']['status'] = '0';
                     $status['EmailStatus']['status_details'] = $msg;
                     //check save
                     if (!$this->EmailStatus->save($status)) {
//                         $this->Session->setFlash(__("Something went terribly wrong! We can't register your email on the database. 
//                                        Please contact support at support@emailbykeywords.com.", 'cake-error'));
//                         $this->redirect(array('action' => 'index'));
//                         break;
                     }
                 }
                 //increment i
                 $i++;
             }
             echo $this->element('sql_dump');
    }
}

?>
