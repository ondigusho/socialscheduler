<div id="content" class="span10">
			<!-- start: Content -->
			<div>
				<ul class="breadcrumb">
					<li>
						<?php echo $this->Html->link('Social Media', array('controller'=>'social'));?>
					</li>
				</ul>
			</div>
                        <?php echo $this->Session->flash(); ?>
                        
                         <div class="box-content">
                        <div class="row-fluid">
                            <div class="span12">
                            <h2>Message</h2>
                            <div class="tooltip-demo well">
                            <p class="muted" style="margin-bottom: 0; color: #5f5f5f;">
                                <?php echo  "<i>".$post['SocialPosts']['message']."</i>";?>
                            </p>
                            </div>
                            </div>
                        </div>    
                             
                        </div>    
                        
                       <div class="row-fluid sortable">	
				<div class="box span6">
                                    <?php if (isset($fbuserspost)){
                                        
                                        echo '<div class="box-header" data-original-title>
						<h2><img src="/img/facebook-icon.png" style="height: 23px; width: 26px;"><span class="break"></span>Facebook Users</h2>
                                                <div class="box-icon">
							<a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="icon-remove"></i></a>
						</div>
                                        </div>
                                        
                                        <div class="box-content">
       
                                        <fieldset>
                                             <table class="table table-condensed table-striped table-bordered bootstrap-datatable">
                                             
                                                <tr>
                                                    <th>User</th>
                                                    <th>Comments</th>
                                                    <th>Likes</th>
                                                </tr>';
                                        
                                        foreach ($fbuserspost as $userpost){
                                            echo '<tr> 
                                                <td><img src= "'.$userpost['profile']['p_picture'].'"> '.$userpost['profile']['full_name'].'</td>
                                                <td>'.$userpost['statistics']['comments'].'</td> 
                                                 <td>'.$userpost['statistics']['likes'].'</td>    
                                            </tr>';
                                        }
                                        
                                        echo '</table></fieldset></div><br><br>';
                                        
                                    }
                                        if (isset($fbpagespost)){
                                        
                                        echo '<div class="box-header" data-original-title>
						<h2><img src="/img/facebook-icon.png" style="height: 23px; width: 26px;"><span class="break"></span>Facebook Pages</h2>
                                                <div class="box-icon">
							<a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="icon-remove"></i></a>
						</div>
                                        </div>
                                        
                                        <div class="box-content">
       
                                        <fieldset>
                                             <table class="table table-condensed table-striped table-bordered bootstrap-datatable">
                                             
                                                <tr>
                                                    <th>Page</th>
                                                    <th>Comments</th>
                                                    <th>Likes</th>
                                                </tr>';
                                        
                                        foreach ($fbpagespost as $userpost){
                                            echo '<tr> 
                                                <td><img src= "'.$userpost['profile']['p_picture'].'"> '.$userpost['profile']['name'].'</td>
                                                <td>'.$userpost['statistics']['comments'].'</td> 
                                                 <td>'.$userpost['statistics']['likes'].'</td>    
                                            </tr>';
                                        }
                                        
                                        echo '</table></fieldset></div><br><br>';
                                        
                                    }
                                    
                                    if (isset($twuserposts)){
                                        
                                        echo '<div class="box-header" data-original-title>
						<h2><img src="/img/twitter-icon.png" style="height: 23px; width: 26px;"><span class="break"></span>Twitter</h2>
                                                <div class="box-icon">
							<a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>
							<a href="#" class="btn-close"><i class="icon-remove"></i></a>
						</div>
                                        </div>
                                        
                                        <div class="box-content">
       
                                        <fieldset>
                                             <table class="table table-condensed table-striped table-bordered bootstrap-datatable">
                                             
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Tweets</th>
                                                    <th>Following</th>
                                                    <th>Followers</th>
                                                </tr>';
                                        
                                        foreach ($twuserposts as $twitter){
                                            echo '<tr> 
                                                <td><img src= "'.$twitter['profile']['p_picture'].'"> '.$twitter['profile']['name'].'</td>
                                                <td>'.$twitter['statistics']['statuses_count'].'</td>  
                                                <td>'.$twitter['statistics']['followers_count'].'</td> 
                                                <td>'.$twitter['statistics']['following_count'].'</td>
                                                      
                                            </tr>';
                                        }
                                        echo '</table></fieldset></div><br><br>';
                                    }
                       ?>
<?php echo $this->Html->link($this->Form->button('Back',array('class'=>'btn','name'=>'cancel')),array('controller'=>'social','action' => 'index'), array('escape'=>false,'title' => "Go Back")); ?>                                    
	</div><!--/span-->
    </div><!--/row-->
</div><!-- end: Content -->