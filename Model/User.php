<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
/**
 * User Model
 *
 * @property Group $Group
 * @property Commentary $Commentary
 */
class User extends AppModel {
	public $name = 'User';
    public $actsAs = array(
    	'Acl' => array(
	    	'type' => 'requester',
	    	
	    	// Necessary to prevent error messages while using $this->save() http://cakephp.1045679.n5.nabble.com/ACL-is-not-working-for-groups-td4953074.html 
	    	'enabled' => false
	    )
	);
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter your name.',
				'last' => true
			)
		),
		'new_password' => array(
			'nonempty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a password.'
			)
		),
		'confirm_password' => array(
			'identicalFieldValues' => array(
				'rule' => array('_identicalFieldValues', 'new_password' ),
				'message' => 'Passwords did not match.'
			)
		),
		'email' => array(
			'is_email' => array(
				'rule' => 'email',
				'message' => 'That doesn\'t appear to be a valid email address.'
			),
			'emailUnclaimed' => array(
				'rule' => array('_isUnique'),
				'message' => 'Sorry, someone else is already using that email address.'
			)
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Commentary' => array(
			'className' => 'Commentary',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public function beforeSave($options = array()) {
		if (isset($this->data['User']['password'])) {
			App::uses('Security', 'Utility');
        	$this->data['User']['password'] = Security::hash($this->data['User']['password'], null, true);
		}
        return true;
    }
    
    // Required by ACL
	public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['group_id'])) {
            $groupId = $this->data['User']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
            return null;
        } else {
            return array('Group' => array('id' => $groupId));
        }
    }
    
	public function bindNode($user) {
	    return array('model' => 'Group', 'foreign_key' => $user['User']['group_id']);
	}
	
	public function _identicalFieldValues($field = array(), $compare_field = null) {
		foreach ($field as $key => $value) {
			if ($value !== $this->data[$this->name][$compare_field]) {
				return false;
			}
		}
		return true;
    }
    
	public function _isUnique($check) {
		$values = array_values($check);
		$value = array_pop($values);
		$fields = array_keys($check);
		$field = array_pop($fields);
		if ($field == 'email') {
			$value == strtolower($value);
		}
		if(isset($this->data[$this->name]['id'])) {
			$results = $this->field('id', array(
				"$this->name.$field" => $value, 
				"$this->name.id <>" => $this->data[$this->name]['id']
			));
		} else {
			$results = $this->field('id', array(
				"$this->name.$field" => $value
			));
		}
		return empty($results);
	}
	
	public function generatePassword() {
		$characters = str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789');
		return substr($characters, 0, 6); 	
	}

	public function sendNewsmediaIntroEmail($user) {
		App::uses('CakeEmail', 'Network/Email');
		$email = new CakeEmail('newsmedia_intro');
		$email->to($user['User']['email']);
		$newsmedia_index_url = Router::url(
			array(
				'controller' => 'commentaries',
				'action' => 'index',
				'newsmedia' => true
			),
			true
		);
		$login_url = Router::url(
			array(
				'controller' => 'users',
				'action' => 'login'
			),
			true
		);
		$email->viewVars(compact(
			'user',
			'newsmedia_index_url',
			'login_url'
		));
		return $email->send();
	}
	
	public function getUserIdWithEmail($email) {
		$result = $this->find('first', array(
			'conditions' => array(
				'User.email' => $email
			), 
			'fields' => array(
				'User.id'
			),
			'contains' => false
		));
		if ($result) {
			return $result['User']['id'];
		}
		return false;
	}
	
	public function cleanEmail($email) {
		$email = trim($email);
		$email = strtolower($email);
		return $email;
	}
}