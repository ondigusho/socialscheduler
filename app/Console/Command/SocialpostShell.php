<?php
//just a default timezone
date_default_timezone_set('America/New_York');
/*
 * Social Posts will be send by this process 
 */

class SocialpostShell extends AppShell {

    //Load used models
    var $uses = array('Social', 'FbPages', 'FcbUserPosts', 'SocialPosts', 'FcbPagePosts', 'SocialScheduled',
        'Twitter', 'TwUserPosts', 'LnkIn');
    
    //Select all social scheduled 
    //if it is passed the 15 minutes post and update data
    //Not sure if neded!
    public function main() {
        $cheduled = $this->SocialScheduled->find('all');
        foreach ($cheduled as $schpost) {
            if (trim($schpost['SocialScheduled']['usertmz']) == '') {
                //Server tmz
                $usertmz = date_default_timezone_get();
            } else {
                //user's
                $usertmz = $schpost['SocialScheduled']['usertmz'];
            }
            //Set time
            $timenow = Utilities::UserTime($usertmz);
            //check date and time
            if ($timenow >= $schpost['SocialScheduled']['datetime']) {
                //maybe this can run as an exec() process... -> one post one process...
                $post_id = $schpost['SocialScheduled']['post_id'];
                $this->post($post_id);
                //echo $timenow. ' This one must be posted '.$schpost['SocialScheduled']['datetime']."\n";
            }
        }
    }

    //post 
    public function post($post_id) {
        //get type and id.
//        $get = array(
//            'joins' => array(
//                array(
//                    'table' => 'fcbuserposts',
//                    'alias' => 'fcbuserposts',
//                    'type' => 'LEFT',
//                    'conditions' => array(
//                        'SocialPosts.id = fcbuserposts.post_id',
//                    ),
//                ),
//                array(
//                    'table' => 'fcbpageposts',
//                    'alias' => 'fcbpageposts',
//                    'type' => 'LEFT',
//                    'conditions' => array(
//                        'SocialPosts.id = fcbpageposts.post_id',
//                    ),
//                ),
//                array(
//                    'table' => 'twuserposts',
//                    'alias' => 'twuserposts',
//                    'type' => 'LEFT',
//                    'conditions' => array(
//                        'SocialPosts.id = twuserposts.post_id',
//                    ),
//                ),
//            ),
//            //'limit' => 10,
//            'conditions' => array(array(array('SocialPosts.id' => $post_id,'SocialPosts.deleted' => 0))),
//            'fields' => array('SocialPosts.id', 'SocialPosts.id', 'SocialPosts.uid', 'SocialPosts.message', 'SocialPosts.file'
//                , 'SocialPosts.link', 'SocialPosts.dateposted', 'SocialPosts.deleted', 'SocialPosts.posted', 'fcbuserposts.fcb_uid', 'fcbpageposts.fcb_uid', 'fcbpageposts.fcb_page_id', 'twuserposts.tw_uid'),
//        );
        //What to be posted 
        $get = array(
            //'limit' => 10,
            'conditions' => array(array('id' => $post_id, 'deleted' => 0,'posted' => 0)),
                //'fields' => array('file'),
        );
        //get post
        $post = $this->SocialPosts->find('first', $get);
        //get accounts 
        $get_accounts = array(
            //'limit' => 10,
            'conditions' => array(array('post_id' => $post_id, 'scheduled' => 1)),
                //'fields' => array('file'),
        );
        //fcb user
        $fcb_users = $this->FcbUserPosts->find('all', $get_accounts);
        //fcb page
        $fcb_pages = $this->FcbPagePosts->find('all', $get_accounts);
        //tw user
        $tw_users = $this->TwUserPosts->find('all', $get_accounts);
        //get accounts. If is set post  
        if (!empty($fcb_users)) {
            //parse and post
            foreach ($fcb_users as $user) {
                //Send
                $result = $this->Social->cronpost($post,$user['FcbUserPosts']['fcb_uid']);
                if ($result['status'] == 'success') {
                    // update 
                    $update['FcbUserPosts']['id'] = $user['FcbUserPosts']['id'];
                    $update['FcbUserPosts']['fcb_post_id'] = $result['post_id'];
                    $update['FcbUserPosts']['errorstatus'] = 1;
                    $update['FcbUserPosts']['msg'] = 'Successfull';
                    $update['FcbUserPosts']['scheduled'] = 0;
                } else {
                    //There was an error
                    // update 
                    $update['FcbUserPosts']['id'] = $user['FcbUserPosts']['id'];
                    $update['FcbUserPosts']['errorstatus'] = 0;
                    $update['FcbUserPosts']['msg'] = $result['message'];
                }
                //save
                $this->FcbUserPosts->save($update);
            }
        }
        //Fcb pages
        if (!empty($fcb_pages)) {
            //parse and post
            foreach ($fcb_pages as $page) {
                //Send
                $result = $this->FbPages->cronpost($post,$page['FcbPagePosts']['fcb_uid'],$page['FcbPagePosts']['fcb_page_id']);
                if ($result['status'] == 'success') {
                    // update 
                    $update['FcbPagePosts']['id'] = $page['FcbPagePosts']['id'];
                    $update['FcbPagePosts']['fcb_post_id'] = $result['post_id'];
                    $update['FcbPagePosts']['errorstatus'] = 1;
                    $update['FcbPagePosts']['msg'] = 'Posted';
                    $update['FcbPagePosts']['scheduled'] = 0;
                } else {
                    //There was an error
                    // update 
                    $update['FcbPagePosts']['id'] = $page['FcbPagePosts']['id'];
                    $update['FcbPagePosts']['errorstatus'] = 0;
                    $update['FcbPagePosts']['msg'] = $result['message'];
                }
                //save
                $this->FcbPagePosts->save($update);
            }
            
        }
        //Twitter
        if (!empty($tw_users)) {
            //send for post on twitter 
            foreach ($tw_users as $account) {
                //Send
                $result = $this->Twitter->cronpost($post, $account['TwUserPosts']['tw_uid']);
                if ($result['status'] == 'success') {
                    // update 
                    $update['TwUserPosts']['id'] = $account['TwUserPosts']['id'];
                    $update['TwUserPosts']['tw_post_id'] = $result['post_id'];
                    $update['TwUserPosts']['errorstatus'] = 1;
                    $update['TwUserPosts']['msg'] = 'Posted';
                    $update['TwUserPosts']['scheduled'] = 0;
                } else {
                    //There was an error
                    // update 
                    $update['TwUserPosts']['id'] = $account['TwUserPosts']['id'];
                    $update['TwUserPosts']['errorstatus'] = 0;
                    $update['TwUserPosts']['msg'] = $result['message'];
                }
                //save
                $this->TwUserPosts->save($update);
            }
        }
        //Now remove from scheduled and update as posted...
        $delete = array('post_id' => $post_id);
        //remove scheduled
        $this->SocialScheduled->deleteAll($delete);
        //Update
        $post['SocialPosts']['posted']= 1;
        //save
        $this->SocialPosts->save($post);
        //remove file if exists
        //file path
//        if($post['SocialPosts']['file']!='na'){
//            //remove file
//            $filepath = WWW_ROOT . $post['SocialPosts']['file'];
//            //remove
//            unlink($filepath);
//        }
    }
}
?>