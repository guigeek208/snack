<?php 
$this->extend('/Common/radius_sidebar'); 
$this->assign('nas_active', 'active');
?>

<h1><?php echo __('Backups (%s)', $this->data['Nas']['nasname']); ?></h1>
<?php
echo $this->Html->link(
    '<i class="icon-hdd icon-white"></i> ' . __('Add a NAS'),
    array('controller' => 'nas', 'action' => 'add'),
    array('escape' => false, 'class' => 'btn btn-primary')
);

$columns = array(
    'id' => array('text' => __('ID'), 'fit' => true),
    'nasname' => array('text' => __('Name')),
    'shortname' => array('text' => __('Short name')),
    'type' => array('text' => __('Type'), 'fit' => true),
    'ports' => array('text' => __('Ports'), 'fit' => true),
    'server' => array('text' => __('Server')),
    'community' => array('text' => __('Community')),
    'description' => array('text' => __('Description')),
);

echo $this->Form->create('Nas', array('action' => 'delete'));
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
	    <th class="fit"><? echo __('Backups'); ?></th>
	    <th class="fit"><? echo __('Edit'); ?></th>
	    <th class="fit"><? echo __('Delete'); ?></th>
	</tr>
    </thead>

    <tbody>
<?php
if (!empty($nas)) {
    foreach ($nas as $n) {
?>
	<tr>
	    <td class="fit">
<?php
	echo $this->Form->select(
	    'nas',
	    array($n['Nas']['id'] => ''),
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
	    $n['Nas']['id'],
	    array(
		'controller' => 'nas',
		'action' => 'view',
		$n['Nas']['id']
	    )
	);
?>
	    </td>
	    <td>
<?php
    echo $n['Nas']['nasname'];
?>
	    </td>
	    <td>
<?php
    echo $n['Nas']['shortname'];
?>
	    </td>
	    <td class="fit">
<?php
    echo $n['Nas']['type'];
?>
	    </td>
	    <td class="fit">
<?php
    echo $n['Nas']['ports'];
?>
	    </td>
	    <td>
<?php
    echo $n['Nas']['server'];
?>
	    </td>
	    <td>
<?php
    echo $n['Nas']['community'];
?>
	    </td>
	    <td>
<?php
    echo $n['Nas']['description'];
?>
	    </td>
	    <td class="fit">
		<i class="icon-camera"></i>
<?php
    echo $this->Html->link(
	__('Backups'),
	array('action' => 'backups', $n['Nas']['id'])
    );
?>
	    </td>
	    <td class="fit">
		<i class="icon-edit"></i>
<?php
    echo $this->Html->link(
	__('Edit'),
	array('action' => 'edit', $n['Nas']['id'])
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
	    . "$('#NasDeleteForm').attr('action',"
	    . "$('#NasDeleteForm').attr('action') + '/"
	    . $n['Nas']['id'] . "');"
	    . "$('#NasDeleteForm').submit(); }"
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
	    <td colspan="<?php echo count($columns)+3; ?>">
<?php
    echo __('No NAS yet.');
?>
	    </td>
	</tr>
<?
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
unset($n);
?>