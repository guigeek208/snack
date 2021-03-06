<?php

class Radacct extends AppModel {
    public $useTable = 'radacct';
    public $primaryKey = 'radacctid';
    public $displayField = 'acctsessionid';
    public $name = 'Radacct';

    public $types = array();

    public function __construct($id = false, $table = null, $ds = null) {
        $this->types = array(
            'Async' => __('Console'),
            'Virtual' => __('Telnet/SSH'),
            'Ethernet' => __('802.1X'),
        );

        parent::__construct($id, $table, $ds);
    }
}

?>
