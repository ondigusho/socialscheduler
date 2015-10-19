<?php
/* TwUserPosts Model
 * 
 * 
 * @copyright Ondi Gusho.
 */

class TwUserPosts extends AppModel{
    //Set table
    public $useTable = 'twuserposts';
    
    /* deletePosts 
    * 
    * Will inactivate all user's post by id
    */
    public function inactivatePosts($tw_uid){
        //test if any 
        if ($this->hasAny(array('tw_uid' => $tw_uid))) {
            if ($this->updateAll(array('inactive' => 1),                    
                                array('tw_uid' => $tw_uid))
               ) {
                $return = array('status' => 'success', 'message' => 'Posts deleted.');
            } else {
                $return = array('status' => 'error', 'message' => 'Something went wrong! Please try again.');
            }
        } else {
            //Error
            $return = array('status' => 'success', 'message' => 'Nothing to delete!');
        }
        //return
        return $return;
    }
}
?>