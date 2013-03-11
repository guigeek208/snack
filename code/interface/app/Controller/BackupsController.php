<?php

class BackupsController extends AppController {
    public $helpers = array('Html', 'Form');
    public $paginate = array('limit' => 10, 'order' => array('id' => 'desc'));
    public $uses = array('Backup');
    public $components = array(
		'Filters' => array('model' => 'Backup'),
    );

    private $git = '~snack/backups.git/';

    public function index($id = null) {
		$this->loadModel('Nas');
		$nas = $this->Nas->findById($id);

		$this->set('nasID', $nas['Nas']['id']);
		$this->set('nasIP', $nas['Nas']['nasname']);
		$this->set('nasShortname', $nas['Nas']['shortname']);

		$this->Filters->addDatesConstraint(array(
		    'column' => 'datetime', 
		    'from' => 'datefrom',
		    'to' => 'dateto',
		));

		$this->Filters->addStringConstraint(array(
		    'fields' => 'author', 
		    'input' => 'author', 
		));

		$this->Filters->addGroupConstraint('commit');

		$backups = $this->Filters->paginate();

		$users = array();

		foreach($backups AS $backup) {
		    $users[$backup['Backup']['id']] =
			$this->extendUsers($backup['Backup']['users']);
		}

		$this->set('users', $users);

		$lastWrmem = $this->Backup->find('first', array(
		    'conditions' => array(
			'nas'    => $nas['Nas']['nasname'],
			'action' => 'wrmem'
		    ),
		    'fields'     => array('id'),
		    'order'      => array('id DESC'),
		    'limit'      => 1
		));

		$noWriteMemed = $this->Backup->find('all', array(
		    'conditions' => array(
			'nas'    => $nas['Nas']['nasname'],
			'id >'   => $lastWrmem['Backup']['id']
		    ),
		    'fields'     => array('id')
		));

		$noWriteIds = array();

		foreach($noWriteMemed AS $o)
		    $noWriteIds[] = $o['Backup']['id'];

		$this->set('nowriteids', $noWriteIds);
    }

    private function extendUsers($str) {
	$strUsers = explode(',', $str);
	$users = array();

	$this->loadModel('Raduser');

	foreach($strUsers AS $strUser) {
	    $user = $this->Raduser->findByUsername($strUser);
	    
	    if(empty($user)) {
		array_push($users, array(
		    'id' => -1,
		    'username' => $strUser,
		));
	    } else {
		array_push($users, array(
		    'id' => $user['Raduser']['id'],
		    'username' => $strUser,
		));
	    }
	}

	return $users;
    }

    private function gitDiffNas($nas, $a, $b = null) {
		$backupA = $this->Backup->findById($a);

		if(!$backupA) {
		    throw new BadBackupOrNasID(
			'Please select an existant version A for this NAS.'
		    );
		}

		$commitA = $backupA['Backup']['commit'];

		$this->set('dateA', $backupA['Backup']['datetime']);
		$this->set('idA', $backupA['Backup']['id']);
		$this->set('actionA', $backupA['Backup']['action']);
		$this->set('usersA', $this->extendUsers($backupA['Backup']['users']));

		if ($b != null) {
		    $b = $this->params['url']['b'];
		    $backupB = $this->Backup->findById($b);

		    if(!$backupB) {
			throw new BadBackupOrNasID(
			    'Please select an existant version B for this NAS.'
			);
		    }

		    $commitB = $backupB['Backup']['commit'];

		    $this->set('dateB', $backupB['Backup']['datetime']);
		    $this->set('idB', $backupB['Backup']['id']);
		    $this->set('actionB', $backupB['Backup']['action']);
		    $this->set('usersB', $backupB['Backup']['users']);

		} else
		    $commitB = null; // last

		$this->loadModel('Nas');
		$nas = $this->Nas->findById($nas);

		if(!$nas) {
		    throw new BadBackupOrNasID(
			'Please select an existant NAS.'
		    );
		}

		$this->set('nasID', $nas['Nas']['id']);
		$this->set('nasIP', $nas['Nas']['nasname']);
		$this->set('nasShortname', $nas['Nas']['shortname']);

		exec("cd $this->git; git diff $commitB $commitA", $output);
		$this->set('diff', implode("\n", $output));

		return $backupA;
    }

    public function diff() {
		try {
		    if(!isset($this->params['url']['nas'])
			|| !isset($this->params['url']['a'])
			|| !isset($this->params['url']['b'])) {

				throw new BadBackupOrNasID(
				    'Please select specific versions for a specific NAS.'
				);
		    }

		    $this->gitDiffNas(
				$this->params['url']['nas'],
				$this->params['url']['a'],
				$this->params['url']['b']
		    );

		} catch(BadBackupOrNasID $e) {
		    $this->Session->setFlash(
				$e->getMessage(),
				'flash_error'
		    );
		}
    }

    public function view($id = null, $nas = null) {
		try {
		    if($id != null && $nas != null) {
				$this->loadModel('Nas');
				$nas = $this->Nas->findById($nas);

				if(!$nas) {
				    throw new BadBackupOrNasID(
					'Please select an existant NAS.'
				    );
				}

				$backup = $this->gitDiffNas($nas['Nas']['id'], $id);
				$commit = $backup['Backup']['commit'];

				exec("cd $this->git; git show $commit:{$nas['Nas']['nasname']}", $output);
				$this->set('config', implode("\n", $output));
				$this->set('backupID', $id);

				$this->Filters->addStringConstraint(array(
				    'fields' => 'commit', 
				    'input' => 'commit', 
				    'value'  => $commit,
				));

				$backups = $this->Filters->paginate();
				$users = array();

				foreach($backups AS $backup) {
				    $users[$backup['Backup']['id']] =
					$this->extendUsers($backup['Backup']['users']);
				}

				$this->set('users', $users);
		    } else {
				throw new BadBackupOrNasID(
				    'Please select a NAS and a configuration version.'
				);
		    }

		} catch(BadBackupOrNasID $e) {
		    $this->Session->setFlash(
				$e->getMessage(),
				'flash_error'
		    );
		}
    }

    public function restore($id, $nas) {
	
    }
}

?>
