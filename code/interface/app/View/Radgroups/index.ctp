<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');
?>
<h1><? echo __('Groups'); ?></h1>
<p>
<?php
echo $this->Html->link(__('Add a group'),
array('controller' => 'radgroups', 'action' => 'add'),
array('class' => 'btn'));

$columns = array(
    'id' => __('ID'),
    'groupname' => __('Name'),
    'comment' => __('Comment')
);

?>
</p>

<table class="table">
    <thead>
    <tr>
        <?
        foreach($columns as $field => $text){
            $sort = preg_match("#$field$#", $this->Paginator->sortKey()) ?  $this->Html->tag('i', '', array('class' => $sortIcons[$this->Paginator->sortDir()])) : '';

            echo "<th>";
            echo $this->Paginator->sort($field, "$text $sort", array('escape' => false));
            echo "</th>";
        }
        ?>
        <th><? echo __('Edit'); ?></th>
        <th><? echo __('Delete'); ?></th>
    </tr>
    </thead>

    <tbody>
    <?
    if(!empty($radgroups)){
        foreach ($radgroups as $g): ?>
    <tr>
        <td>
            <? echo $this->Html->link($g['Radgroup']['id'],
            array('controller' => 'Radgroups', 'action' => 'view', $g['Radgroup']['id'])); ?>
        </td>
        <td>
            <? echo $g['Radgroup']['groupname']; ?>
        </td>
        <td>
            <? echo $g['Radgroup']['comment']; ?>
        </td>
        <td>
            <i class="icon-edit"></i>
            <? echo $this->Html->link(__('Edit'), array('action' => 'edit', $g['Radgroup']['id'])); ?>

        </td>
        <td>
            <i class="icon-remove"></i>
            <? echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $g['Radgroup']['id']),
            array('confirm' => __('Are you sure?'))); ?>
        </td>
    </tr>
        <? endforeach;
    } else {
        ?>
        <tr>
            <td colspan="5"><? echo __('No groups yet'); ?>.</td>
        </tr>
    <?
    }
    unset($g);
    ?>
    </tbody>
</table>
<? echo $this->element('paginator_footer'); ?>