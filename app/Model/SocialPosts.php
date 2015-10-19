<?php

/* SocialPosts Model
 * 
 * 
 * @copyright Ondi Gusho.
 */

class SocialPosts extends AppModel {

    //Set table
    public $useTable = 'social_posts';

    /* getScheduledPost
     * 
     */

    public function getScheduledPost($post_id,$accounts) {
        //get type and id.
        $get = array(
            'joins' => array(
                array(
                    'table' => 'social_scheduled',
                    'alias' => 'TableAlias',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'SocialPosts.id = TableAlias.post_id',
                    ),
                ),
            ),
            //'limit' => 10,
            'conditions' => array(array('SocialPosts.id' => $post_id)),
            'fields' => array('SocialPosts.id', 'SocialPosts.id', 'SocialPosts.uid', 'SocialPosts.message', 'SocialPosts.file'
                , 'SocialPosts.link', 'SocialPosts.dateposted', 'SocialPosts.deleted', 'SocialPosts.posted', 'TableAlias.datetime'),
        );
        //echo 'Got here'; die();
        $user_post = $this->find('first',$get);
        //format and return
        //build return array 
        return array('status'=>'success','id'=>$user_post['SocialPosts']['id']
                        ,'uid'=>$user_post['SocialPosts']['uid']
                        ,'message'=>$user_post['SocialPosts']['message']
                        ,'file'=>$user_post['SocialPosts']['file']
                        ,'link'=>$user_post['SocialPosts']['link']
                        ,'dateposted'=>$user_post['SocialPosts']['dateposted']
                        ,'datetime'=>$user_post['TableAlias']['datetime']
                        ,'accounts'=>$accounts
                    ); 
        }
        
        
    /**
     * Delete Image
     * @param post id, user_id
     * @return array
     */
    public function deleteImage($user_id, $post_id) {
        //test if access 
        if ($this->hasAny(array('id' => $post_id, 'uid' => $user_id))) {
            //get
            $get = array(
                //'limit' => 10,
                'conditions' => array(array('id' => $post_id, 'uid' => $user_id)),
                'fields' => array('file'),
            );
            $user_post = $this->find('first', $get);
            //Remove from server
            $filename = WWW_ROOT . $this->uploadDir . DS . $user_post['SocialPosts']['file'];
            //just remove
            try {
                //just remove
                unlink($filename);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }

            $update['SocialPosts']['id'] = $post_id;
            //update to na
            $update['SocialPosts']['file'] = 'na';
            //Update status MyMarketing
            if ($this->save($update)) {
                $return = array('status' => 'success', 'message' => 'Image removed.');
            } else {
                $return = array('status' => 'error', 'message' => 'Something went wrong! Please try again.');
            }
        } else {
            //Error
            $return = array('status' => 'error', 'message' => 'You are not allowed to perform this operation!');
        }
        //return
        return $return;
    }

}

?>