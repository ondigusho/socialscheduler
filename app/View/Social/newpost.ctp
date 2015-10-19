<div id="content" class="span10">
    <!-- start: Content -->
    <div>
        <ul class="breadcrumb">
            <li>
		<?php echo $this->Html->link('Create Social Post  ', array(''));?> <span class="divider">/</span>
            </li>
        </ul>
    </div>
        <?php echo $this->Session->flash(); ?> 
  <div class="row-fluid">
  <div class="span7">  
    <div class="box-content" style="width: 110%;">
        <div class="page-header">
            <h1>
                <small>Create Social Post </small>
            </h1>
        </div>
    <?php echo $this->Form->create('socialposts',array('type' => 'file',
            'url' => array('controller' => 'social', 'action' => 'index'))
            );
    ?>
       <div id="InputsWrapper">
            <div>
                <textarea id="textarea2" name="message" style="height: 100px; width: 95%;" rows="3" placeholder="Type your message here..."></textarea>
                <div class="txt-box-info">
                    characters : <span id="chars">0</span> 
                </div>
            </div>
        </div>
         <div class="alert alert-success">
            <div class="controls">
                <li style="display: inline;">
                    Upload Image 
                       <?php echo $this->Form->file('Document.submittedfile',array('label'=>FALSE,'class'=>'span6 typeahead','id'=>'focusedInput')); ?>
                </li>
                <li style="display: inline; padding-left: 15px;">
                    <a title="Insert url" href="#" id="insLink">
                        <img src="/img/Internet-url-icon.png" id="insLink">
                        Insert Link
                    </a>
                </li>
                <li style="display: inline; padding-left: 15px;">
                    <label class="radio">
                        <div id="uniform-optionsRadios2" class="radio">
                            <span class="">
                                <input id="optionsNow" type="radio" checked="checked" value="postnow" name="optionsRadios" style="opacity: 0;">
                            </span>
                        </div>
                        Post Now
                    </label>
                </li>
                <li style="display: inline; padding-left: 15px;">    
                    <label class="radio">
                        <div id="uniform-optionsRadios2" class="radio">
                            <span class="">
                                <input id="optionsSchedule" type="radio" value="postlater" name="optionsRadios" style="opacity: 0;">
                            </span>
                        </div>
                        Post Later
                    </label>
                </li>
            </div>
        </div>
        <div class="alert alert-error" id="erroroninput" style="display : none"></div>
        <div class="alert alert-info" id="showschedule" style="display : none"></div>
        <div class="alert alert-info" id="showschedule" style="display : none"></div>
        <div class="alert alert-info" id="showschedule" style="display : none"></div>
        <div class="alert alert-info" id="showlink" style="display: none">
            <button class="customclose" id="closeurl" type="button">×</button>
            <input type="text" value="http://" id="insertedUrl" style="border-radius: 4px;color: #555555;display: inline-block;font-size: 13px;height: 17px;line-height: 27px;margin-bottom: 0;padding: 4px 6px; vertical-align: middle;">
            <button class="btn btn-mini"  type="button" id="linksubmit">Insert</button>
            <div id="iferror"></div>
        </div>
        <div id="showlinkposts"></div>    
       
       <div class="box-content" align="center">
            <?php   
                    //check if any and print header
                    if(isset($fbusers) || isset($twusers)){
                        echo'<table class="table table-striped table-bordered bootstrap-datatable datatable dataTable">
                                  <thead>
                                  <tr>
                                      <th></th>
                                      <th>Name</th>
                                  </tr>
                                  </thead>
                               <tbody><fieldset id="inlineCheckbox1">';
                    if(isset($fbusers)){
                        foreach ($fbusers as $user){
                             $img = $user["Social"]["p_picture"];
                           echo '<div><tr id="item_'.$user["Social"]['fcb_id'].'">
                                    <td>
                                             <span>
                                               <img src="/img/facebook-icon.png" style="height: 23px; width: 26px;"><input type="checkbox" id="fcbuser-'.$user["Social"]['fcb_id'].'" name = "addcontacts[]" value="fcbuser-'.$user["Social"]['fcb_id'].'" name = "addcontacts[]" style="opacity: 0;">
                                             </span>   
                                            </td>
                                   <td><img src= "'.$img.'"> '.$user["Social"]["full_name"].'</td>
                                </tr></div>';
                            }
                            //now pages
                            if(isset($fbpages)){
                                foreach ($fbpages as $page){
                                $img = $page["FbPages"]["p_picture"];
                                echo '<tr id="fbpage-'.$page['FbPages']['fcb_uid'].'-'.$page["FbPages"]['fcb_page_id'].'">
                                    
                                    <td>
                                             <span>
                                               <img src="/img/facebook-icon.png" style="height: 23px; width: 26px;"> <input type="checkbox" id="fcbpage-'.$page['FbPages']['fcb_uid'].'-'.$page["FbPages"]['fcb_page_id'].'" value="fcbpage-'.$page['FbPages']['fcb_uid'].'-'.$page["FbPages"]['fcb_page_id'].'" name = "addcontacts[]" style="opacity: 0;">
                                             </span>   
                                            </td>
                                        <td><img src= "'.$img.'"> '.$page["FbPages"]["name"].'</td> 
                                        
                                    </tr>';
                                }
                            }
                            
                        }
                        //check twitter accounts
                        if(isset($twusers)){
                            foreach ($twusers as $user){
                             $img = $user["Twitter"]["p_picture"];
                           echo '<div><tr id="twuser-'.$user["Twitter"]['tw_id'].'">
                                    <td>
                                             <span>
                                              <img src="/img/twitter-icon.png" style="height: 23px; width: 26px;"> <input type="checkbox" id="twuser-'.$user["Twitter"]['tw_id'].'" name = "addcontacts[]" value="twuser-'.$user["Twitter"]['tw_id'].'" name = "addcontacts[]" style="opacity: 0;">
                                            </span>   
                                            </td>
                                   <td><img src= "'.$img.'"> '.$user["Twitter"]["name"].'</td>
                                </tr></div>';
                            }
                        }
                        //end table
                        echo'</fieldset></tbody></table>';
                        
                    } 
                    else{
                            echo "You don't have any social media account linked!";
                    }
                ?>
        </div>
        </div>
       <div class="modal-footer">
      <input type="submit" class="btn btn-primary" id="postsubmit" value="Post">    
        <?php // echo $this->Html->link('<button class="btn btn-primary">Go Back</button>', 
//                        array('controller'=>'social','action'=>'index'), array('escape'=>false)); ?>
   </div>
        
        </div>
    </div><!-- end: Box-Content -->
</div><!-- end: Content -->