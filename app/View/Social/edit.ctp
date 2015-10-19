<div id="content" class="span10">
    <!-- start: Content -->
    <div>
        <ul class="breadcrumb">
            <li>
		<?php echo $this->Html->link('Scheduled Post Review', array(''));?> <span class="divider">/</span>
            </li>
        </ul>
    </div>
        <?php echo $this->Session->flash(); ?> 
  <div class="row-fluid">
  <div class="span7">  
    <div class="box-content" style="width: 110%;">
        <div class="page-header">
            <h1>
                <small>Social Post scheduled on  <?php echo $post_data['datetime']; ?> </small>
            </h1>
        </div>
    <?php echo $this->Form->create('editsocialpost',array('type' => 'file'));
          //hidden scheduled
          echo '<input type="hidden" name="currentschedule" value="'.$post_data['datetime'].'">';  
    ?>
        <div id="InputsWrapper">
            <div>
                <textarea id="textarea2" name="message" style="height: 100px; width: 650px; display: list-item;" rows="3" placeholder="Type your message here..."><?php echo $post_data['message'];?></textarea>
            </div>
            <div class="txt-box-info">
                    characters : <span id="chars">0</span> 
            </div>
        </div>
        <div class="alert alert-success">
            <div class="controls">
                <li style="display: inline;">
                    Upload New Image 
                       <?php echo $this->Form->file('Document.submittedfile',array('label'=>FALSE,'class'=>'span6 typeahead','id'=>'focusedInput')); ?>
                </li>
                <li style="display: inline; padding-left: 15px;">
                    <a title="Insert new url" href="#" id="insLink">
                        <img src="/img/Internet-url-icon.png" id="insLink">
                        Insert New Link
                    </a>
                </li>
                
                 <li style="display: inline; padding-left: 15px;"><label class="radio">
                        <div id="uniform-optionsRadios3" class="radio">
                            <span class="">
                                <input id="optionsRadiosChange" type="radio" value="postlater" name="optionsRadios" style="opacity: 0;">
                            </span>
                        </div>
                        Change DateTime 
                </label></li>
        
                
            </div>
        </div>
        <div class="alert alert-error" id="erroroninput" style="display : none"></div>
        <div class="alert alert-info" id="showschedule-edit" style="display : none"></div>
        <?php
            if(isset($post_data['file']) && $post_data['file']!='na'){
                echo '<div class="alert alert-info" id="showimage-edit-'.$post_data['id'].'"><button class="customclose" id="removeImage" type="button" style="top: -7px; right: 630px; position: static;">×</button>'
                        
                . '<img src="/'.$post_data['file'].'" id="" width="130" ><br><div class="txt-box-info">Uploading a new image will replace the current one.</div></div>';
            }
        ?>
        
        <div class="alert alert-info" id="showlink" style="display: none">
            <button class="customclose" id="closeurl" type="button">×</button>
            <input type="text" value="http://" id="insertedUrl" style="border-radius: 4px;color: #555555;display: inline-block;font-size: 13px; height: 25px;line-height: 27px;margin-bottom: 0;padding: 4px 6px; vertical-align: middle;">
            <button class="btn btn-mini"  type="button" id="linksubmit">Insert</button>
            <div id="iferror"></div>
        </div>
        
        <?php
            if(isset($post_data['link']) && $post_data['link']!='na'){
                echo '<div class="alert alert-info" id="showlinkposts"><button class="customclose" id="removeLink" type="button" style="top: -7px; right: 630px; position: static;">×</button>'
                        
                . ''.$post_data['link'].'<br><div class="txt-box-info">Inserting new link will replace the current one.</div></div>';
            }
        ?>
        
        <div class="box-content" align="center">
            <?php
                    if(isset($fbusers)){
                       echo'<table class="table table-striped table-bordered bootstrap-datatable datatable dataTable">
                                  <thead>
                                  <tr>
                                      <th></th>
                                      <th>Name</th>
                                  </tr>
                                  </thead>
                               <tbody><fieldset id="inlineCheckbox1">';
                            foreach ($fbusers as $user){
                                $img = $user["Social"]["p_picture"];
                                $user_edit = 'fcbuseredit-'.$user["Social"]['fcb_id'];
                                //set checked
                                   if(in_array($user_edit,$post_data['accounts'])){
                                       $checked = 'checked';
                                   }
                                   else{
                                       $checked = '';
                                   }

                                echo '<div><tr id="item_'.$user["Social"]['fcb_id'].'">
                                       <td>
                                          <span>
                                                  <img src="/img/facebook-icon.png" style="height: 23px; width: 26px;"><input type="checkbox" id="fcbuseredit-'.$user["Social"]['fcb_id'].'" name = "addcontacts[]" value="fcbuseredit-'.$user["Social"]['fcb_id'].'" name = "addcontacts[]" '.$checked.' style="opacity: 0;">
                                                </span>   
                                               </td>
                                      <td><img src= "'.$img.'"> '.$user["Social"]["full_name"].'</td>
                                   </tr></div>';
                            }
                            //now pages
                            if(isset($fbpages)){
                                foreach ($fbpages as $page){
                                    $img = $page["FbPages"]["p_picture"];
                                    $page_id = 'fcbpageedit-'.$page['FbPages']['fcb_uid'].'-'.$page["FbPages"]['fcb_page_id'];
                                    //set checked
                                    if(in_array($page_id,$post_data['accounts'])){
                                        $checked = 'checked';
                                    }
                                    else{
                                        $checked = '';
                                    }
                                    echo '<tr id="fbpage-'.$page['FbPages']['fcb_uid'].'-'.$page["FbPages"]['fcb_page_id'].'">
                                        <td>
                                                 <span>
                                                   <img src="/img/facebook-icon.png" style="height: 23px; width: 26px;"><input type="checkbox" id="fcbpageedit-'.$page['FbPages']['fcb_uid'].'-'.$page["FbPages"]['fcb_page_id'].'" value="fcbpageedit-'.$page['FbPages']['fcb_uid'].'-'.$page["FbPages"]['fcb_page_id'].'" name = "addcontacts[]" '.$checked.' style="opacity: 0;">
                                                 </span>   
                                                </td>
                                            <td><img src= "'.$img.'"> '.$page["FbPages"]["name"].'</td> 

                                        </tr>';
                                }
                            }
                            //now pages
                            if(isset($twitter)){
                                foreach ($twitter as $twuser){
                                    $img = $twuser["Twitter"]["p_picture"];
                                    $tw_id = 'twuseredit-'.$twuser['Twitter']['tw_id'];
                                    //set checked
                                    if(in_array($tw_id,$post_data['accounts'])){
                                        $checked = 'checked';
                                    }
                                    else{
                                        $checked = '';
                                    }
                                    echo '<tr id="twuseredit-'.$twuser['Twitter']['tw_id'].'">
                                        <td>
                                                 <span>
                                                  <img src="/img/twitter-icon.png" style="height: 23px; width: 26px;">  <input type="checkbox" id="twuseredit-'.$twuser['Twitter']['tw_id'].'" value="twuseredit-'.$twuser['Twitter']['tw_id'].'" name = "addcontacts[]" '.$checked.' style="opacity: 0;">
                                                 </span>   
                                                </td>
                                            <td><img src= "'.$img.'" width="50" heigh="50"> '.$twuser["Twitter"]["name"].'</td> 
                                        </tr>';
                                }
                            }
                            
                            //end table
                            echo'</fieldset></tbody></table>';
                        }
                        else{
                            echo "You don't have any facebook account linked!";
                            }
                        ?>
            </div>
        </div>
        
        <div class="modal-footer">
        <input type="submit" class="btn btn-primary" id="saveedit" value="Save Changes">    
       <?php echo $this->Html->link($this->Form->button('Go Back',array('class'=>'btn','name'=>'cancel')),array('controller'=>'social','action' => 'index'), array('escape'=>false,'title' => "Cancel transaction")); ?>
    </div>
        
        </div>
    </div><!-- end: Box-Content -->
</div><!-- end: Content -->