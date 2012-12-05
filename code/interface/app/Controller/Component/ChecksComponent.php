<?php

App::uses('Component', 'Controller');
App::import('Model', 'Radcheck');
App::import('Model', 'Raduser');
App::import('Model', 'Radgroupcheck');
App::import('Model', 'Radgroup');

class ChecksComponent extends Component
{

	private $displayName;
	private $checkClass;
	private $checkClassName;
	private $baseClass;
	private $baseClassName;

	public function __construct($collection, $params){
		$this->displayName = $params['displayName'];
		$this->checkClass = new $params['checkClass'];
		$this->checkClassName = $params['checkClass'];
		$this->baseClass = new $params['baseClass'];
		$this->baseClassName = $params['baseClass'];
	}

    public function getChecks($id){
        $this->baseClass->id = $id;

        // sorry for that...
        $findAllFunc = 'findAllBy' . ucfirst($this->displayName);
        return $this->checkClass->$findAllFunc($this->baseClass->field($this->displayName));
    }

    public function getType($rad, $nice = false) {
        $types = array(
            array('cisco', 'Cisco'),
            array('loginpass', 'Login / Password'),
            array('mac', 'MAC'),
            array('cert', 'Certificate')
        );

        if($rad['is_cisco'])
            return $types[0][$nice];
        if($rad['is_loginpass'])
            return $types[1][$nice];
        if($rad['is_mac'])
            return $types[2][$nice];
        if($rad['is_cert']  )
            return $types[3][$nice];
        return "";
    }

    public function index()
    {
        $rads = $this->baseClass->find('all');
        if($this->baseClassName == 'Raduser'){
            foreach ($rads as &$r) {
                $r[$this->baseClassName]['ntype'] = $this->getType($r[$this->baseClassName], true);
                $r[$this->baseClassName]['type'] = $this->getType($r[$this->baseClassName], false);
            }
        }
        return $rads;
    }

    public function view($id = null) {
        $this->baseClass->id = $id;
        return array(
        	'base' => $this->baseClass->read(), 
        	'checks' => $this->getChecks($id));
    }

    public function create_check($displayName, $attribute, $op, $value){
        if($value != "" && $attribute != ""){
            $data = array(
                $this->displayName => $displayName
                ,
                'attribute' => $attribute,
                'op' => $op,
                'value' => $value
                );
            $rad = new $this->checkClass;
            $rad->create();
            return $rad->save($data);
        } else {
            echo $attribute;
            echo $value;
            return false;
        }
    }

    public function add($request, $checks){
        if($request->is('post')){
                
            // add common checks values (expiration date and simultaneous uses) 
            $name = $request->data[$this->baseClassName][$this->displayName];
            $checks = array_merge($checks, array(
                array($name,
                    'Expiration',
                    ':=',
                    $request->data[$this->baseClassName]['expiration_date']
                    ),
                array($name,
                    'Simultaneous-Use',
                    ':=',
                    $request->data[$this->baseClassName]['simultaneous_use']
                    )
                ));

            print_r($checks);
            $this->baseClass->create();
            $success = $this->baseClass->save($request->data);

            foreach($checks as $rc)
                $success = $success && $this->create_check($rc[0], $rc[1], $rc[2], $rc[3]);

            return $success;
        }
    }

    // can be moved to RadusersController
    public function add_cisco($request)
    {
        if ($request->is('post')) {
            $name = $request->data[$this->baseClassName][$this->displayName];
            $request->data[$this->baseClassName]['is_cisco'] = 1;
            $checks = array(
                array($name,
                    'NAS-Port-Type',
                    '==',
                    $request->data[$this->baseClassName]['nas-port-type']
                ),
                array($name,
                    'Cleartext-Password',
                    ':=',
                    $request->data[$this->baseClassName]['password']
                ),
                array($name,
                    'EAP-Type',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );

            return $this->add($request, $checks);

            // TODO: add a cisco user with $request->data[$this->baseClassName][$this->displayName]/ $request->data[$this->baseClassName]['password']
        }
    }

    // can be moved to RadusersController
    public function add_loginpass($request)
    {
        if ($request->is('post')) {

            $name = $request->data[$this->baseClassName][$this->displayName];
            $request->data[$this->baseClassName]['is_loginpass'] = 1;
            $rads = array(
                array($name,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($name,
                    'Cleartext-Password',
                    ':=',
                    $request->data[$this->baseClassName]['password']
                ),
                array($name,
                    'EAP-Type',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );
            return $this->add($request, $rads);
        }
    }

    // can be moved to RadusersController
    public function add_mac($request){
        if ($request->is('post')) {

            $request->data[$this->baseClassName]['mac'] = str_replace(':', '', $request->data[$this->baseClassName]['mac']);
            $request->data[$this->baseClassName]['mac'] = str_replace('-', '', $request->data[$this->baseClassName]['mac']);
            $name = $request->data[$this->baseClassName]['mac'];
            $request->data[$this->baseClassName]['is_mac'] = 1;
            $request->data[$this->baseClassName][$this->displayName
          ] = $request->data[$this->baseClassName]['mac'];
            $rads = array(
                array($name,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($name,
                    'Cleartext-Password',
                    ':=',
                    $request->data[$this->baseClassName]['mac']
                ),
                array($name,
                    'EAP-Type',
                    ':=',
                    'MD5-CHALLENGE'
                )
            );
            return $this->add($request, $rads);
        }
    }

    // can be moved to RadusersController
    public function add_cert($request)
    {
        if ($request->is('post')) {

            $name = $request->data[$this->baseClassName][$this->displayName
          ];
            $request->data[$this->baseClassName]['is_cert'] = 1;
            $request->data[$this->baseClassName]['cert_path'] = '/var/www/cert/newcerts/' . $name . '.pem';
            $rads = array(
                array($name,
                    'NAS-Port-Type',
                    '==',
                    '15'
                ),
                array($name,
                    'EAP-Type',
                    ':=',
                    'EAP-TTLS'
                )
            );
            return $this->add($request, $rads);

            // TODO: generate a certificate
        }
    }

    // can be moved to RadgroupsController
    public function add_group($request)
    {
        if ($request->is('post')) {
            $name = $request->data[$this->baseClassName][$this->displayName];
            return $this->add($request, array());
        }
    }

    // can be moved to RadusersController
    public function edit_cisco($request, $id = null) {
        $this->baseClass->id = $id;
        if ($request->is('get')) {
            $request->data = $this->baseClass->read();

            // restore values from radchecks
            $this->restore_common_check_fields($request, $id, $cisco=true);

            return $request;
        } else {

            if ($this->baseClass->save($request->data)) {

                // update radchecks fields
                $checkClassFields = array(
                    'NAS-Port-Type' => $request->data[$this->baseClassName]['nas-port-type'],
                    'Cleartext-Password' => $request->data[$this->baseClassName]['password']);
                $this->update_radcheck_fields($id, $request, $checkClassFields);

                return true;

            } else {
                return false;
            }
        }
    }

    // can be moved to RadusersController
    public function edit_loginpass($request, $id = null) {
        $this->baseClass->id = $id;
        if ($request->is('get')) {
            $request->data = $this->baseClass->read();
            $this->restore_common_check_fields($request, $id);
            return $request;
        } else {

            if ($this->baseClass->save($request->data)) {

                // update radchecks fields
                $checkClassFields = array('Cleartext-Password' => $request->data[$this->baseClassName]['password']);
                $this->update_radcheck_fields($id, $request, $checkClassFields);

                return true;
            } else {
            	return false;
            }
        }
    }
    
    // can be moved to RadusersController
    public function edit_cert($request, $id = null) {
        $this->baseClass->id = $id;
        if ($request->is('get')) {
            $request->data = $this->baseClass->read();
            $this->restore_common_check_fields($request, $id);
            return $request;
        } else {

            if ($this->baseClass->save($request->data)) {

                $newCert = ($request->data[$this->baseClassName]['cert_gen'] == 1);

                $this->update_radcheck_fields($id, $request);
                if($newCert){
                    // TODO: generate a new cert
                }
                return true;

            } else {
            	return false;
            }
        }
    }

    // can be moved to RadusersController
    public function edit_mac($request, $id = null) {
        $this->baseClass->id = $id;
        if ($request->is('get')) {
            $request->data = $this->baseClass->read();
            $this->restore_common_check_fields($request, $id);
            return $request;
        } else {

            if ($this->baseClass->save($request->data)) {
                $this->update_radcheck_fields($id, $request);
            	return true;
            } else {
            	return false;
            }
        }
    }

    public function update_radcheck_fields($id, $request, $additionalFields = array()){
        // common fields
        $fields = array(
            'Expiration' => $request->data[$this->baseClassName]['expiration_date'],
            'Simultaneous-Use' => $request->data[$this->baseClassName]['simultaneous_use']
            );
        $fields = array_merge($fields, $additionalFields);
        $rads = $this->getChecks($id);

        foreach($fields as $key=>$value){
            foreach($rads as &$r){
                if($r[$this->checkClassName]['attribute'] == $key
                    && $r[$this->checkClassName]['value'] != ""){
                    $r[$this->checkClassName]['value'] = $value;
                    $this->checkClass->save($r);
                    break;
                }
            }
        }
    }

    public function delete($request, $id)
    {
        if ($request->is('get')) {
            throw new MethodNotAllowedException();
        }

        // delete matching radchecks
        $rads = $this->getChecks($id);
        foreach($rads as $r)
            $this->checkClass->delete($r[$this->checkClassName]['id']);

        if ($this->baseClass->delete($id)) {
        	return true;
        } else {
        	return false;
        }

        // TODO: delete certificate on filesystem if necessary
    }

    public function restore_common_check_fields(&$request, $id, $cisco=false)
    {
        // restore values from radchecks
        $rads = $this->getChecks($id);
        foreach($rads as $r){
            if($r[$this->checkClassName]['attribute'] == 'NAS-Port-Type' && $cisco){
                $request->data[$this->baseClassName]['nas-port-type'] = $r[$this->checkClassName]['value'];
            } else if($r[$this->checkClassName]['attribute'] == 'Expiration'){
                $request->data[$this->baseClassName]['expiration_date'] = $r[$this->checkClassName]['value'];
            } else if($r[$this->checkClassName]['attribute'] == 'Simultaneous-Use'){
                $request->data[$this->baseClassName]['simultaneous_use'] = $r[$this->checkClassName]['value'];
            }
        }

        return $request;
    }

    // FIXME
    public function login()
    {
        if ($request->is('post')) {
            if ($this->Auth->login()) {
                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(__('Invalid username or password, try again.'));
            }
        }
    }

    public function logout()
    {
        $this->redirect($this->Auth->logout());
    }

}

?>
