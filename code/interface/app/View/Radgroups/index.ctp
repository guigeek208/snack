<? 
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('groups_active', 'active');
?>

<h1><? echo __('Groups'); ?></h1>
<?php
echo $this->Html->link(
    '<i class="icon-list icon-white"></i> ' . __('Add a group'),
    array('controller' => 'radgroups', 'action' => 'add'),
    array('escape' => false, 'class' => 'btn btn-primary')
);

$columns = array(
    'id' => array('text' => __('ID'), 'fit' => true),
    'groupname' => array('text' => __('Name')),
    'comment' => array('text' => __('Comment'))
);

echo $this->Form->create('Radgroups', array('action' => 'delete'));
echo $this->Form->end();

echo $this->Form->create('MultiSelection', array('class' => 'form-inline'));
?>
<table class="table">
    <thead>
	<tr>
	    <th class="fit">
<?php
echo $this->Form->select(
    'All',
    array('all' => ''),
    array(
	'class' => 'checkbox rangeAll',
	'multiple' => 'checkbox',
	'hiddenField' => false,
    )
);
?>
	    </th>
<?php
foreach ($columns as $field => $info) {
    $sort = '';

    if (preg_match("#$field$#", $this->Paginator->sortKey())) {
	$sort = '<i class="'
	    . $sortIcons[$this->Paginator->sortDir()]
	    . '"></i>';
    }

    if (isset($info['fit']) && $info['fit']) {
	echo '<th class="fit">';
    } else {
	echo '<th>';
    }

    echo $this->Paginator->sort(
	$field,
	$info['text'] . ' '. $sort,
	array('escape' => false)
    )
    . '</th>';
}
?>
	    <th class="fit"><? echo __('Edit'); ?></th>
	    <th class="fit"><? echo __('Delete'); ?></th>
	</tr>
    </thead>

    <tbody>
<?php
if (!empty($radgroups)) {
    foreach ($radgroups as $g) {
?>
	<tr>
	    <td class="fit">
<?php
	echo $this->Form->select(
	    'groups',
	    array($g['Radgroup']['id'] => ''),
	    array(
		'class' => 'checkbox range',
		'multiple' => 'checkbox',
		'hiddenField' => false,
	    )
	);
?>
	    </td>
	    <td class="fit">
<?php
	echo $this->Html->link(
	    $g['Radgroup']['id'],
	    array(
		'controller' => 'Radgroups',
		'action' => 'view',
		$g['Radgroup']['id'],
	    )
	);
?>
	    </td>
	    <td>
<?php
	echo $g['Radgroup']['groupname'];
?>
	    </td>
	    <td>
<?php
	echo $g['Radgroup']['comment'];
?>
	    </td>
	    <td class="fit">
		<i class="icon-edit"></i>
<?php
	echo $this->Html->link(
	    __('Edit'),
	    array('action' => 'edit', $g['Radgroup']['id'])
	);
?>
	    </td>
	    <td class="fit">
		<i class="icon-remove"></i>
<?php
	echo $this->Html->link(
	    __('Delete'),
	    '#',
	    array(
		'onClick' => "if (confirm('" . __('Are you sure?') . "')) {"
		. "$('#RadgroupsDeleteForm').attr('action',"
		. "$('#RadgroupsDeleteForm').attr('action') + '/"
		. $g['Radgroup']['id'] . "');"
		. "$('#RadgroupsDeleteForm').submit(); }"
	    )
	);
?>
	    </td>
	</tr>
<?php
    }
} else {
?>
	<tr>
	    <td colspan="<?php echo count($columns) + 3; ?>">
<?php
    echo __('No groups yet.');
?>
	    </td>
	</tr>
<?php
}
?>
    </tbody>
</table>

<?php
echo $this->element('dropdownButton', array(
    'buttonCount' => 1,
    'title' => 'Action',
    'icon' => '',
    'items' => array(
	$this->Html->link(
	    '<i class="icon-remove"></i> ' . __('Delete selected'),
	    '#',
	    array(
		'onClick' =>	"$('#selectionAction').attr('value', 'delete');"
		. "if (confirm('" . __('Are you sure?') . "')) {"
		. "$('#MultiSelectionIndexForm').submit();}",
		    'escape' => false,
		)
	    ),
	)
    ));
echo $this->Form->end(array(
    'id' => 'selectionAction',
    'name' => 'action',
    'type' => 'hidden',
    'value' => 'delete'
));
echo $this->element('paginator_footer');
unset($g);
?>
