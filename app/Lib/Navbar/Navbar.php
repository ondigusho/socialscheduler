<?php
/* Navbar Class 
 * 
 * 
 * @copyright Ondi Gusho.
 */

class Navbar{
 /**
    * Will Print out the top menu and
    * Account info
    * 
    * @param  object 
    * 
    * @return NULL   
    */
public static function view($object){
    //Get valid array
    $valid_array = explode(' ', $object->Session->read('Auth.User.valid_to'));
    //get now
    $dt = new DateTime();
    $now =  $dt->format('Y-m-d H:i:s');
    //compare
    if ($now>$valid_array[0]){
        $valid = $object->Html->link('<span class="label label-important hidden-phone"> <i class="icon-warning-sign"></i>  Your use period has expired since '.$valid_array[0].'. Click to buy more "Use Time".</span> 
                                                ',array('controller'=>'buy','action'=>'index'), array('title'=>'Click to buy more','escape'=>false,'class'=>'btn'));
    }else{
        $valid = $object->Html->link('<i class="icon-warning-sign"></i><span class="hidden-phone hidden-tablet"> Account Valid To Date</span> 
                                                <span class="label label-success hidden-phone">'.$valid_array[0].'</span> ',array('controller'=>'buy','action'=>'index'), array('title'=>'Click to buy more','escape'=>false,'class'=>'btn')); 
    }
    //check searches
    if ($object->Session->read('Auth.User.search_counter')<=0){
        
        $s_counter = $object->Html->link('<span class="label label-important hidden-phone"> <i class="icon-warning-sign"></i> You are out of searches. Click here to buy more "Email Searches".</span> 
                                                ',array('controller'=>'buy','action'=>'index'), array('title'=>'Click to buy more','escape'=>false,'class'=>'btn'));
    }else{
        $s_counter = $object->Html->link('<i class="icon-tasks"></i><span class="hidden-phone hidden-tablet"> Number Of Searches Available</span> <span class="label label-warning hidden-phone">
                                                '.$object->Session->read('Auth.User.search_counter').'</span>',array('controller'=>'buy','action'=>'index'), array('title'=>'Click to buy more','escape'=>false,'class'=>'btn')); 
    }
    
    echo'
        <div class="navbar">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a id="ctct-logo" href="">
                                ';
                                //Print logo
                                echo $object->Html->link(
					$object->Html->image('logo1.0.png', array('border' => '0','width'=>"220", 'height'=>"31")),
					'http://emailbykeywords.com',
					array('target' => '_blank', 'escape' => false));
                                echo '</a>
				<!-- start: Header Menu -->
				<div class="btn-group pull-right" >
                                    '.$valid.'
                                    
                                    '.$s_counter.'
                                     
                                    <!--<a class="btn" href="#">
						<i class="icon-envelope"></i><span class="hidden-phone hidden-tablet"> messages</span> <span class="label label-success hidden-phone">9</span>
					</a> 
					<a class="btn" href="#">
						<i class=""></i><span class="hidden-phone hidden-tablet"> Wellcome Ondi</span>
					</a>-->
					<!-- start: User Dropdown -->
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-user"></i>
                                                <span class="hidden-phone hidden-tablet">
                                                 '.$object->Session->read('Auth.User.fname').' '.$object->Session->read('Auth.User.lname').'
                                                </span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li>'.$object->Html->link('Edit Profile', array('controller'=>'users', 'action' => 'index')).'</li>
						<li class="divider"></li>
                                                <li>'.$object->Html->link('Buy More Searches', array('controller'=>'buy', 'action' => 'index')).'</li>
						<li class="divider"></li>
                                                <li>'.$object->Html->link('SMTP', array('controller'=>'smtp', 'action' => 'index')).'</li>
                                                <li class="divider"></li>    
                                                <li>'.$object->Html->link('Password', array('controller'=>'users', 'action' => 'chpwd')).'</li>
                                                <li class="divider"></li>    
                                                <li>'.$object->Html->link('NEED HELP ?', 'http://emailbykeywords.com/index.php?p=support',array('target'=>'_blank','escape'=>false)).'</li>    
                                               <li class="divider"></li>
						<li>'.$object->Html->link('Logout', array('controller'=>'users', 'action' => 'logout')).'</li>
					</ul>
					<!-- end: User Dropdown -->
                                </div>
				<!-- end: Header Menu -->
				
			</div>
		</div>
	</div>
';
    }
}
?>