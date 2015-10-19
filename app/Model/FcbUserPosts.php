<?php
/* FbUserPosts Model
 * 
 * 
 * @copyright Ondi Gusho.
 */

class FcbUserPosts extends AppModel{
    //Set table
    public $useTable = 'fcbuserposts';
    /* deletePosts 
    * 
    * Will inactivate all user's post by id
    */
    public function inactivatePosts($fcb_uid){
        //test if any 
        if ($this->hasAny(array('fcb_uid' => $fcb_uid))) {
            if ($this->updateAll(array('inactive' => 1),                    
                                array('fcb_uid' => $fcb_uid))
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