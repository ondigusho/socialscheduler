<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'Email By Keywords Dashboard');
?>
<!DOCTYPE html>
<html>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
                //css
                echo $this->Html->css('jquery-ui-timepicker-addon');
		echo $this->Html->css('custom-cake');
                echo $this->Html->css('bootstrap-responsive.min');
                echo $this->Html->css('bootstrap');
                echo $this->Html->css('fullcalendar');
                echo $this->Html->css('style-responsive');
                echo $this->Html->css('style');
                echo $this->Html->css('uniform.default');    
                
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
    
        <?php  //echo $this->element('sql_dump'); 
                //load jquery, must lodad at the end... !!! 
                // TBD why
                echo $this->Html->script('jquery');
                echo $this->Html->script('jquery-1.7.2.min');
        	echo $this->Html->script('jquery-ui-1.8.21.custom.min');
                echo $this->Html->script('jquery-ui-timepicker-addon');
		echo $this->Html->script('bootstrap');
		echo $this->Html->script('jquery.cookie');
		echo $this->Html->script('fullcalendar.min');
		echo $this->Html->script('jquery.dataTables.min');
		echo $this->Html->script('excanvas');
                echo $this->Html->script('jquery.flot.min');
                echo $this->Html->script('jquery.flot.pie.min');
                echo $this->Html->script('jquery.flot.stack');
                echo $this->Html->script('jquery.flot.resize.min');
                echo $this->Html->script('jquery.chosen.min');
		echo $this->Html->script('jquery.uniform.min');
		echo $this->Html->script('jquery.cleditor.min');
                echo $this->Html->script('jquery.noty');
		echo $this->Html->script('jquery.elfinder.min');
		echo $this->Html->script('jquery.raty.min');
		echo $this->Html->script('jquery.iphone.toggle');
                echo $this->Html->script('jquery.uploadify-3.1.min');
		echo $this->Html->script('jquery.gritter.min');
                echo $this->Html->script('jquery.imagesloaded');
                echo $this->Html->script('jquery.masonry.min');
                echo $this->Html->script('jquery.confirm');
        ?>
                <script src="https://js.stripe.com/v1/"></script>
                <script>
                    Stripe.setPublishableKey('pk_live_Z7fAKs5JPgv3rwCwkhebAydy');   // Live key!
                </script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.4/jstz.min.js"></script>
          <?php
                echo $this->Html->script('buy-controllers');
                echo $this->Html->script('custom');
        ?>
 
</head>
<body>
    	<!-- start: Header -->
	<?php Navbar::view($this);?>
       <div id="under-header"></div>
	<!-- start: Header -->
        <div class="container-fluid">
		<div class="row-fluid">
			<?php 
                          // Left menu
                          LeftMenu::view($this);
                        ?>
                        <?php echo $this->fetch('content'); ?>
               </div><!--/fluid-row-->
		
		<div class="clearfix"></div>
		<hr>
                <?php
                //echo $this->Html->link(
				//	$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
				//	'http://www.cakephp.org/',
				//	array('target' => '_blank', 'escape' => false)
				//);
                //year
		//echo $this->element('sql_dump');
                $year = date("Y");
		?>
		<footer>
			<p class="pull-left">&copy; <a href="">Email By Keywords</a> <?php echo $year;?></p>
			<p class="pull-right">Powered by: <a href="">Email By Keywords</a></p>
		</footer>
				
	</div><!--/.fluid-container-->
        
	
</body>
</html>
