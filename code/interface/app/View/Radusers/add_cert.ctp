<?php 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>

<h1><?php echo __('Add a user with a certificate'); ?></h1>
<?php
echo $this->Form->create('Raduser');

echo '<fieldset>';
echo '<legend>' . __('Checks') . '</legend>';

echo $this->Form->input('username');
echo $this->element('check_common_fields');
echo $this->element('doubleListsSelector', array('leftTitle' => __('Groups'), 'rightTitle' => __('Selected groups'), 'contents' => $groups, 'selectedContents' => array()));
echo $this->Form->input('groups', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));

echo '</fieldset>';

echo $this->element('cisco_common_fields', array('type' => 'cert'));

echo '<fieldset>';
echo '<legend>' . __('Replies') . '</legend>';
echo $this->element('reply_common_fields');
echo '</fieldset>';
echo $this->Form->end(__('Create'));
?>

