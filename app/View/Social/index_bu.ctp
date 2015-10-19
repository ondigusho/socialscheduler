<div class="modal hide fade" id="myModal" style="width: 746px; top: 2%; margin-left: -400px; max-height: 900px; overflow-y: auto">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3> Create Social Post  
        </h3>
    </div>
                        <?php echo $this->Form->create('socialposts',array('type' => 'file'));?>
    <div class="modal-body">
        <div id="InputsWrapper">
            <div>
                <textarea id="textarea2" name="message" style="height: 100px; width: 700px; display: list-item;" rows="3" placeholder="Type your message here..."></textarea>
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
    </div>
    <div class="modal-body">
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
        <a href="#" class="btn" data-dismiss="modal">Close</a>
        <input type="submit" class="btn btn-primary" id="postsubmit" value="Post">
    </div>
    <!--</form>-->
</div>

<div id="content" class="span10">
    <!-- start: Content -->
    <div>
        <ul class="breadcrumb">
            <li>
		<?php echo $this->Html->link('Social Media', array('controller'=>'social', 'action' => 'index'));?> <span class="divider">/</span>
            </li>

        </ul>
    </div>
   <?php echo $this->Session->flash(); ?> 
 <div class="row-fluid sortable ui-sortable">
        <div class="box span12">
            <div class="box-header" data-original-title>
                <h2><i class="icon-user"></i><span class="break"></span>Social Media Management</h2>
            </div>
            <div class="box-content">
                <ul id="myTab" class="nav tab-menu nav-tabs">
                    <li class="active">
                        <a href="#profiles">Profiles</a>
                    </li>
                    <li class="">
                        <a href="#scheduled">Scheduled</a>
                    </li>
                    <li class="">
                        <a href="#posted">Posted</a>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content" style="overflow: hidden;">
                <div id="profiles" class="tab-pane active">
                <div class="row-fluid sortable ui-sortable">
                <div class="box span3">
                    <div class="box-content" align="center">
                    <?php
                        if (isset($login)){
                            echo $login;
                        }
                        ?>
                    </div>
                </div>

                <div class="box span3">
<!--                    <div class="box-content" align="center">
                        <img style="height: 35px; width: 40px;" src="/img/twitter-icon.png"></i> <a class="btn btn-primary" href="social/twitter">Add Twitter Account</a>
                    </div>-->
                </div>

                <div class="box span3">
                    <div class="box-content" align="center">

                        <!--<img style="height: 35px; width: 40px;" src="/img/linkedin-icon.png"></i> <a class="btn btn-primary" href="<?php echo $linkedInUrl ?>">Add Linkedin Account</a>-->
                        <img style="height: 35px; width: 40px;" src="/img/twitter-icon.png"></i> <a class="btn btn-primary" href="social/twitter">Add Twitter Account</a>
                    </div>
                </div>
            </div>

            <div class="row-fluid sortable ui-sortable">
                <div class="box span3">
                </div>

                <div class="box span3">
                    <div class="box-content" align="center">

                        <div class="btn-group button-login" href="">
                            <?php 
                                if(!isset($fbusers) && !isset($fbpages) && !isset($twusers)){
                                    echo '<button class="btn btn-danger" disabled>
                                            <i class="icon-comment icon-white"></i></button>
                                        <button class="btn btn-danger" disabled >CREATE NEW SOCIAL POST</button>';
                                }
                                else{
                                    echo '<a class="btn btn-danger" href="/social/newpost">
                                            <i class="icon-comment icon-white"></i>
                                        </a><a class="btn btn-danger" href="/social/newpost">CREATE NEW SOCIAL POST</a>';
                                }
                            ?>
                            
                        </div>
                    </div>
                </div>
            </div>
            </div>
                    <div id="scheduled" class="tab-pane">
                        <div class="row-fluid sortable">		
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="icon-list-alt"></i><span class="break"></span>Scheduled Posts<span class="ontopview"></span></h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="icon-remove"></i></a>
						</div>
					</div>
                        <div class="box-content">
                            
				<?php
                                if(isset($scheduled)){
                                    echo '<table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th>Message</th>
                                        <th>Schedule Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>   
                                <tbody>';
                                // Loop through people and print.
                                                    foreach ($scheduled as $post){
                                                        $msg = Utilities::myTruncate($post['SocialPosts']['message'],50,' ');
                                                        echo '<tr>
                                                                  <td>'. 
                                                        $this->Html->link($msg, 
                                                                            array('controller'=>'social','action'=>'edit',$post['SocialPosts']['id']), array('title'=>'View scheduled post','escape'=>false)).'
                                                
                                                        <td class="center">'.$post['TableAlias']['datetime'].'</td>';
                                                        //set id on a var
                                                        $id='editsocialpost-'.$post['SocialPosts']['id'];
                                                        
                                                        echo'<td class="center">
                                                                        '.$this->Html->link('Edit', 
                                                                            array('controller'=>'social','action'=>'edit',$post['SocialPosts']['id']),array('title'=>'Edit this post','id'=>$id, 'escape'=>false, 'class'=>'btn btn-small btn-primary')).'
                                                                        '.$this->Html->link('Delete', 
                                                                            array('controller'=>'social','action'=>'delete',$post['SocialPosts']['id']),array('title'=>'Delete this post','escape'=>false,'class'=>'btn btn-small btn-danger'),
                                                                                array("Are you sure you wish to delete this post?")).'
								</td>                
                                                                 
                                                      </tr>';
                                                    }
                                
                                echo '</tbody>
                            </table> ';
                            }    
                                else{
                                    echo "<p> You don't have any scheduled posts</p>";
                                }
                            ?>
                        </div>
                        </div><!--/span-->
			</div><!--/row-->                
                    </div>
                    
                    
                    <div id="posted" class="tab-pane">
                       <div class="row-fluid sortable">		
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="icon-list-alt"></i><span class="break"></span>Posted<span class="ontopview"></span></h2>
						<div class="box-icon">
							<a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="icon-remove"></i></a>
						</div>
					</div>
                        <div class="box-content">
                            
				<?php
                                if(isset($posted)){
                                    echo '<table class="table table-striped table-bordered bootstrap-datatable datatable">
                                <thead>
                                    <tr>
                                        <th>Message</th>
                                        <th>Posted Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>   
                                <tbody>';
                                // Loop through people and print.
                                                    foreach ($posted as $post){
                                                        $msg = Utilities::myTruncate($post['SocialPosts']['message'],50,' ');
                                                        echo '<tr>
                                                                  <td>'. 
                                                        $this->Html->link($msg, 
                                                                            array('controller'=>'social','action'=>'view',$post['SocialPosts']['id']), array('title'=>'View scheduled post','escape'=>false)).'
                                                
                                                        <td class="center">'.$post['SocialPosts']['dateposted'].'</td>';
                                                        echo'<td class="center">
                                                                        '.$this->Html->link('View Reports', 
                                                                            array('controller'=>'social','action'=>'view',$post['SocialPosts']['id']), array('title'=>'View post','escape'=>false,'class'=>'btn btn-small btn-primary')).'
                                                            
                                                             '.$this->Html->link('Delete', 
                                                                            array('controller'=>'social','action'=>'delete',$post['SocialPosts']['id']),array('title'=>'Delete this post','escape'=>false,'class'=>'btn btn-small btn-danger'),
                                                                                array("Are you sure you wish to delete this post?")).'
								</td>  
                                                            </tr>';
                                                    }
                                
                                echo '</tbody>
                            </table> </div> <br>
                            <a title="View all post history" href="social/viewposted">
                                    <i class="icon-list-alt"></i>
                                    View all posted messages
                                </a>';
                                }
                                else{
                                    echo "<p> You don't have any posted Social Media Messages</p></div>";
                                }
                            ?>
                        
                        </div><!--/span-->
			</div><!--/row-->      
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row-fluid sortable">		
        <div class="row-fluid sortable ui-sortable">
            <div class="box span6">
                <div class="box-header">
                    <h2><img src="/img/facebook-icon.png" style="height: 23px; width: 26px;"><span class="break"></span>Facebook</h2>
                </div>
                <div class="box-content" align="center">
                    <?php
                    if(isset($fbusers)){
                        echo'<table class="table table-striped table-bordered bootstrap-datatable datatable">';
                        echo '<thead><tr>
                        <th>Name</th>
                        <th></th>
                        </tr>
                        </thead>  
                        <tbody>';
                        foreach ($fbusers as $user){
                             $img = $user["Social"]["p_picture"];
                            echo '<div><tr id="item_'.$user["Social"]['fcb_id'].'">
                                   <td><img src= "'.$img.'"> '.$user["Social"]["full_name"].'</td>
                                   <td><a class="deletefbuser" href="#" id="fbuser-'.$user["Social"]['fcb_id'].'">Unlink</a></td>
                                </tr></div>';
                            }
                            //now pages
                            if(isset($fbpages)){
                                foreach ($fbpages as $page){
                                $img = $page["FbPages"]["p_picture"];
                                echo '<tr id="fbpage-'.$page['FbPages']['id'].'-'.$page["FbPages"]['fcb_uid'].'">
                                        <td><img src= "'.$img.'"> '.$page["FbPages"]["name"].'</td> 
                                        <td><a class="deletefbpage" href="#" id="fbpage-'.$page['FbPages']['id'].'-'.$page["FbPages"]['fcb_uid'].'">Unlink</a></td>
                                    </tr>';
                                }
                            }
                            //end table
                            echo'</tbody></table>';
                        }
                        else{
                            echo "You don't have any facebook account linked!";
                            }
                        ?>
                </div>
            </div>

            <div class="box span6">
                <div class="box-header">
                    <h2><img src="/img/twitter-icon.png" style="height: 23px; width: 26px;"><span class="break"></span>Twitter</h2>
                </div>
                <div class="box-content" align="center">
                    <?php
                    if(isset($twusers)){
                        echo'<table class="table table-striped table-bordered bootstrap-datatable datatable">';
                        echo '<thead><tr>
                        <th>Name</th>
                        <th></th>
                        </tr>
                        </thead>  
                        <tbody>';
                        
                        foreach ($twusers as $user){
                             $img = $user["Twitter"]["p_picture"];
                            echo '<div><tr id="twuser-'.$user["Twitter"]['tw_id'].'">
                                   <td><img src= "'.$img.'"> '.$user["Twitter"]["name"].'</td>
                                   <td><a class="deletetwuser" href="#" id="twuser-'.$user["Twitter"]['tw_id'].'">Unlink</a></td>
                                </tr></div>';
                            }
                             //end table
                            echo'</tbody></table>';
                        }
                        else{
                            echo "You don't have any Twitter account linked!";
                        }
                        ?>
                </div>
            </div>
        </div>


<!--        <div class="row-fluid sortable ui-sortable">
            <div class="box span6">
                <div class="box-header">
                    <h2><img src="/img/linkedin-icon.png" style="height: 23px; width: 26px;"><span class="break"></span>LinkedIn</h2>
                </div>
                <div class="box-content" align="center">
                    <?php
//                    echo'<table class="table table-striped table-bordered bootstrap-datatable datatable">
//                        ';
//                    if(isset($liusers)){
//                        echo '<thead><tr>
//                        <th>Name</th>
//                        <th></th>
//                        </tr>
//                        </thead>  
//                        <tbody>';
//                        }
//                        else{
//                            echo "<tr>
//                                <td>You don't have any LinkedIn account linked!</td></tr>";
//                                //echo "<tr> You don't have any payments history yet! </tr>";
//                            }
//                        echo'</tbody></table>';
                        ?>
                </div>
            </div>
        </div>
      </div>
    </div><!--/row-->
</div><!-- end: Content -->