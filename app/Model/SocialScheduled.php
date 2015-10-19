<?php
/* SocialScheduled Model
 * 
 * 
 * @copyright Ondi Gusho.
 */

class SocialScheduled extends AppModel{
    //Set table
    public $useTable = 'social_scheduled';
    
    /**
     * Process the Upload
     * @param array $check
     * @return boolean
     */
    public function processUpload($check) {
        // deal with uploaded file
        if (!empty($check['tmp_name'])) {

            // check file is uploaded
            if (!is_uploaded_file($check['tmp_name'])) {
                return array('success'=> FALSE,'message'=>'Cannot find uploaded file.');
            }
            //generate a hash name unique for that file
            $hashName = Utilities::Hash();
            // build full filename
            //WWW_ROOT . $this->uploadDir. // This will return the path og the app. Use this when Upload for cron job posts
            $filename = 'uploads'. DS. $hashName.'.' . pathinfo($check['name'], PATHINFO_EXTENSION);
            // Inflector::slug(pathinfo($check['name'], PATHINFO_FILENAME)) . '.' . pathinfo($check['name'], PATHINFO_EXTENSION)
            // try moving file
            if (!move_uploaded_file($check['tmp_name'], $filename)) {
                return array('success'=> FALSE,'message'=>'Cannot move uploaded file.');
                // file successfully uploaded
            } 
        }
        //sucessfull
        return array('success'=> TRUE,'filename'=>$filename);
    }
 
}
?>