<?php
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');

echo '<h1>' . __('Add a user with login / password') . '</h1>';

echo $this->Form->create('Raduser');

echo '<fieldset>';
echo '<legend>' . __('Checks') . '</legend>';

echo $this->Form->input('username');
echo $this->Form->input('passwd', array('type' => 'password', 'label' => __('Password')));
echo $this->Form->input('confirm_password', array('type' => 'password'));
echo $this->Form->input(
    'ttls',
    array(
	'type' => 'checkbox',
	'label' => __('Check server certificate')
    )
);
echo $this->Form->input('calling-station-id', array('label' => __('MAC address')));
echo $this->element('check_common_fields');

echo $this->element(
    'doubleListsSelector',
    array(
	'leftTitle' => __('Groups'),
	'rightTitle' => __('Selected groups'),
	'contents' => $groups,
	'selectedContents' => array()
    )
);
echo $this->Form->input(
    'groups',
    array(
	'type' => 'select',
	'id' => 'select-right',
	'label' => '',
	'class' => 'hidden',
	'multiple' => 'multiple'
    )
);
echo '</fieldset>';

echo $this->element('cisco_common_fields', array('type' => 'loginpass'));

echo '<fieldset>';
echo '<legend>' . __('Replies') . '</legend>';
echo $this->element('reply_common_fields');
echo '</fieldset>';

echo $this->Form->end(__('Create'));
?>
