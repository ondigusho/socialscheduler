<?php 
/**
 * Handles and checks the OS and Browser of the user 
 * Will block the wrong combinations
 * 
 * This was implemented because og the chrome on MAC OS is not working properly
 * 
 * 
 * @copyright  Ondi Gusho
 */
class UserAgent{
    /**Array of combinations to be blocked
    */
    public static $block = array(
               //array('Mac OS','Chrome') // add more combinations here. Will not allow use of the respective combination
        );
    /**
     * Parse agent string, 
     * check for keywords mach 
     *  
     * 
     * @param   string  Query object.
     * @return  boolean value 
    */
    public static function isAllowed($agent = NULL){
        //always true
        $response = TRUE;
        foreach (UserAgent::$block as $combination){
            $response = UserAgent::check($combination,$agent);
        }
        return $response;
    }
    
    public static function check($combination,$agent){
        //always true
        $return = TRUE;
        //Check for each
        foreach ($combination as $string){
            if (preg_match("/$string/",$agent)){
                $return = FALSE;
            }
            else{
                $return = TRUE;
                break;
            }
        }
        return $return;
    }
} // End