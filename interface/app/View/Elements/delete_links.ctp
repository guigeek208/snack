<?php
switch ($action) {
    case 'form':
	echo $this->Form->create($model, array('action' => 'delete'))
	    . $this->Form->hidden('id')
	    . $this->Form->end();

	break;

    case 'link':
	echo "<a href='#confirm$id' data-toggle='modal'>" . __('Delete') . '</a>';

	echo $this->element('modalDelete', array(
	    'id'   => $id,
	    'link' => $this->Html->link(
		    '<i class="icon-remove icon-white"></i> ' . __('Delete'),
		    '#',
		    array(
			'onclick' => "$('#{$model}DeleteForm input').val('$id');"
			    . "$('#{$model}DeleteForm').submit()",
			'escape' => false,
			'class' => 'btn btn-primary btn-danger'
		    )
		)
	));

	break;
}
?>
