<div id="content" class="span10">
    <!-- start: Content -->
    <div>
        <ul class="breadcrumb">
            <li>
		<?php echo $this->Html->link('View All Social Posts', array('controller'=>'social', 'action' => 'viewposted'));?> <span class="divider">/</span>
            </li>

        </ul>
    </div>

     <?php echo $this->Session->flash(); ?> 
    <div class="row-fluid sortable">		
        <div class="box span12">
            <div class="box-header" data-original-title>
                <h2><i class="icon-list-alt"></i><span class="break"></span>All Social Posts<span class="ontopview"></span></h2>
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
                                                                            array('controller'=>'contacts','action'=>'edit',$post['SocialPosts']['id']), array('title'=>'View scheduled post','escape'=>false)).'
                                                
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
                            </table>
                            <a title="View all post history" href="csv">
                                    <i class="icon-list-alt"></i>
                                    Get all on .csv
                                </a>';
                                
                                }
                                else{
                                    echo "<p> You don't have any posted Social Media Messages</p></div>";
                                }
                            ?>         
            </div>
             <div class="modal-footer">
        <!-- <input type="submit" class="btn btn-primary" id="" value="Get data on .csv">-->    
       <?php echo $this->Html->link($this->Form->button('Go Back',array('class'=>'btn btn-inverse','name'=>'cancel')),array('controller'=>'social','action' => 'index'), array('escape'=>false,'title' => "Go back")); ?>
    </div>
            
        </div><!--/span-->

    </div><!--/row-->

</div><!-- end: Content -->



