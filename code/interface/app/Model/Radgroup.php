<?

App::uses('Utils', 'Lib');

class Radgroup extends AppModel
{
    public $useTable = 'radgroup';
    public $primaryKey = 'id';
    public $displayField = 'groupname';
    public $name = 'Radgroup';

    public $validationDomain = 'validation';
    public $validate = array(
        'groupname' => array(
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Groupname already used'
            ),
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Groupname cannot be empty',
                'allowEmpty' => false,
                'required' => true,
            )
        ),
        'simultaneous_use' => array(
            'rule' => 'decimal',
            'message' => 'Simultaneous uses has to be a number.',
            'allowEmpty' => true,
        ),
        'tunnel-private-group-id' => array(
            'rule' => 'decimal',
            'message' => 'VLAN number has to be a number.',
            'allowEmpty' => true,
        ),
        'session-timeout' => array(
            'rule' => 'decimal',
            'message' => 'Session timeout has to be a number.',
            'allowEmpty' => true,
        ),
    );
}

?>
