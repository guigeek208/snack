<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

echo '<h1>' . __('Add an admin user') . '</h1>';

echo $this->Form->create('Raduser');

echo '<fieldset>';
echo '<legend>' . __('User info') . '</legend>';
echo $this->element('tab_panes', array(
    'items' => array(
        __('New') => $this->Form->input('username'),
        __('Existing') => $this->Form->input(
            'existing_user',
            array('type' => 'select', 'options' => $users, 'empty' => true)
        ),
    ),
));

echo $this->Form->input('passwd', array('type' => 'password', 'label' => __('Password')));
echo $this->Form->input('confirm_password', array('type' => 'password'));
echo '</fieldset>';

echo '<fieldset>';
echo '<legend>' . __('Admin rights') . '</legend>';
echo $this->Form->input('admin', array(
    // 'type' => 'radio',
    'options' => array(
        '1' => __('Create users'),
        '2' => __('Create, Update, Delete users'),
    ),
    'legend' => false,
));
echo '</fieldset>';
echo 'Citation CdC : - créer utilisateur
– créer, modifier, supprimer + accès aux certificats';

echo $this->Form->end(__('Create'));