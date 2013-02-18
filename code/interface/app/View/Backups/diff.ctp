<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('nas_active', 'active');

if(isset($diff)):
?>

<h1><?php echo __('Compare'); ?></h1>
<h2><?php echo __('Configurations'); ?></h2>

<ul>
    <li><?php echo __('<strong>%s:</strong> %s',
			__('NAS'),
			$nasShortname) ?></li>
    <li><?php echo __('<strong>%s:</strong> %s',
			__('IP'),
			$nasIP) ?></li>
    <li><?php echo __('<strong>%s:</strong> %s',
			__('From'),
			$dateB) ?></li>
    <li><?php echo __('<strong>%s:</strong> %s',
			__('To'),
			$dateA) ?></li>
</ul>

<h2><?php echo __('Differences'); ?></h2>

<pre class="well">
<?php echo $diff ?>
</pre>

<?php
endif;

echo $this->Html->link(
	'<i class="icon-arrow-left icon-white"></i> <i class="icon-camera icon-white"></i> ' . __('Go back to backups'),
	'#',
	array(
	    'onclick' => 'history.go(-1)',
	    'escape' => false,
	    'class' => 'btn btn-primary',
	)
    );
?>