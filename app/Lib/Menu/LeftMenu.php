<?php
/* LeftMenu Class 
 * 
 */
class LeftMenu{
 /**
    * Will Print out in html format dynamic menu
    * 
    * @param  object 
    * 
    * @return NULL   
    */
public static function view($object){
?>  
 <!-- start: Main Menu -->
 <div class="span2 main-menu-span">
 <div class="nav-collapse sidebar-nav">
	<ul class="nav nav-tabs nav-stacked main-menu">
		<li class="nav-header hidden-tablet">Navigation</li>
                <?php  
                     echo '<li>'.$object->Html->link('<i class="icon-home"></i><span class="hidden-table"> Home</span>', 
                        array('controller'=>'home','action'=>'index'), array('escape'=>false)).'</li>';
                     
                      echo '<li>'.$object->Html->link('<i class="icon-envelope"></i><span class="hidden-table"> Email Campaign</span>', 
                        array('controller'=>'campaigns','action'=>'index'), array('escape'=>false)).'</li>';
                      
                       echo '<li>'.$object->Html->link('<i class="icon-user"></i><span class="hidden-table"> Social Media</span>', 
                        array('controller'=>'social','action'=>'index'), array('escape'=>false)).'</li>';
                       
                      echo '<li>'.$object->Html->link('<i class="icon-search"></i><span class="hidden-table"> Email Extractor</span>', 
                        array('controller'=>'main','action'=>'index'), array('escape'=>false)).'</li>';
                      
                      echo '<li>'.$object->Html->link('<i class="icon-file"></i><span class="hidden-table"> My Contacts</span>', 
                        array('controller'=>'contacts','action'=>'index'), array('escape'=>false)).'</li>';
                      
                      echo '<li>'.$object->Html->link('<i class="icon-list-alt"></i><span class="hidden-table"> Reports</span>', 
                        array('controller'=>'reports','action'=>'index'), array('escape'=>false)).'</li>';
                      
                      echo '<li>'.$object->Html->link('<i class="icon-shopping-cart"></i><span class="hidden-table"> Buy Searches</span>', 
                        array('controller'=>'buy','action'=>'index'), array('escape'=>false)).'</li>';
                      
                      echo '<li>'.$object->Html->link('<i class="icon-wrench"></i><span class="hidden-table"> SMTP Configuration</span>', 
                        array('controller'=>'smtp','action'=>'index'), array('escape'=>false)).'</li>';
                      
                      echo '<li>'.$object->Html->link('<i class="icon-trash"></i><span class="hidden-table"> Recycle Bin</span>', 
                        array('controller'=>'recycle','action'=>'index'), array('escape'=>false)).'</li>';
                ?>
	</ul>
 </div><!--/.well -->
</div><!--/span-->
<!-- end: Main Menu -->
<noscript>
	<div class="alert alert-block span10">
	<h4 class="alert-heading">Warning!</h4>
		<p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
	</div>
</noscript>
<!-- <h1><?php //echo $this->Html->link($cakeDescription, 'http://cakephp.org'); 
?></h1> -->
<?php
    //echo 'Users timezone'.Utilities::GetUserTime($object);
    }
}
?>