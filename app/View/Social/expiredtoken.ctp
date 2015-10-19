<div id="content" class="span10">
    <!-- start: Content -->
    <div>
        <ul class="breadcrumb">
            <li>
		<?php echo 'Facebook connection required!'?> <span class="divider">/</span>
            </li>

        </ul>
    </div>

     <?php echo $this->Session->flash(); ?> 
    <div class="row-fluid sortable">		
        <div class="box span12">
            <div class="box-content">
	<?php
            echo '<img style="height: 35px; width: 40px;" src="/img/facebook-icon.png"></i> <a class="btn btn-primary" href="' . $url . '">Reconnect '.$name.' Facebook Account</a>.';
        ?>         
            </div>
             <div class="modal-footer">
        <!-- <input type="submit" class="btn btn-primary" id="" value="Get data on .csv">-->    
       <?php echo $this->Html->link($this->Form->button('Go Back',array('class'=>'btn btn-inverse','name'=>'cancel')),array('controller'=>'social','action' => 'index'), array('escape'=>false,'title' => "Go back")); ?>
    </div>
            
        </div><!--/span-->

    </div><!--/row-->

</div><!-- end: Content -->



