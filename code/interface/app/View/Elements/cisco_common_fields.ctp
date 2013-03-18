<?php

echo '<fieldset>';
echo '<legend>' . __('Cisco') . '</legend>';

echo $this->Form->input(
    'cisco',
    array(
	'type' => 'checkbox',
	'label' => __('Cisco user'),
	'class' => 'switchbtn'
    )
);

if($type != 'loginpass'){
    echo $this->Form->input('passwd', array('type' => 'password',
    	'label' => __('Password')));
    echo $this->Form->input('confirm_password',	array('type' => 'password',
	    'label' => __('Confirm password')));
}

echo $this->Form->input('nas-port-type', array(
    'options' => array(
	0 => __('Console'),
	5 => __('Telnet/SSH'),
	10 => __('Both'),
    ),
    'empty' => false,
    'label' => __('NAS Port Type'),
));

echo '</fieldset>';

?>
