<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');

echo '<h1>' . __('Edit') . ' ' . $this->data['Radgroup']['groupname'] . ' ' . __('group') . '</h1>';

echo $this->Form->create('Radgroup', array('action' => 'edit'));
echo $this->element('check_common_fields');
echo $this->element('doubleListsSelector', array('leftTitle' => __('Users'), 'rightTitle' => __('Selected users'), 'contents' => $users, 'selectedContents' => $selectedUsers));
echo $this->Form->input('users', array('type' => 'select', 'id' => 'select-right', 'label' => '', 'class' => 'hidden', 'multiple' => 'multiple'));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->input('groupname', array('type' => 'hidden'));
echo $this->Form->end(__('Update'));
?>

