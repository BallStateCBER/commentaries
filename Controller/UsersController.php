<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
	public $components = array('DataCenter.Permissions');
	
	public function beforeFilter() {
	    parent::beforeFilter();
		$this->Auth->allow(
			'login',
			'logout'
		);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Flash->set(__('The user has been saved. Adding user to AROs table (the permissions system).'), 'success');
				$this->redirect(array('admin' => true, 'plugin' => 'acl', 'controller' => 'aros', 'action' => 'check', 'run'));
				//$this->redirect(array('controller' => 'commentaries', 'action' => 'index'));
			} else {
				$this->Flash->set(__('The user could not be saved. Please, try again.'), 'error');
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(array('title_for_layout' => 'Add User'));
		$this->set(compact('groups'));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Flash->set(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->set(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Flash->set(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Flash->set(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	public function login() {
	    if ($this->request->is('post')) {
	        if ($this->Auth->login()) {
	        	$user = $this->Auth->user();
			    $this->Permissions->storePermissions($user['group_id']);
	            $this->redirect($this->Auth->redirect());
	        } else {
	            $this->Flash->set('Your email address or password was incorrect.', 'error');
	        }
	    }
	    $this->set('title_for_layout', 'Login');
	}
	
	
	
	public function logout() {
		$this->Permissions->forgetPermissions();
		$this->Flash->set('You are now logged out.');
		$this->redirect($this->Auth->logout());
	}

	public function my_account() {
		$password_changed = false;
		$this->User->id = $this->Auth->user('id');
		if (empty($this->request->data)) {
			$this->request->data = $this->User->read();
		} else {
			// Unless if both password fields have values, unset them so they don't go through validation
			if ($this->request->data['User']['new_password'] == '' || $this->request->data['User']['confirm_password'] == '') {
				if ($this->request->data['User']['new_password'] != '') {
					$no_password_confirmation = true;
				}
				unset($this->request->data['User']['new_password']);
				unset($this->request->data['User']['confirm_password']);
			} elseif ($this->request->data['User']['new_password'] == $this->request->data['User']['confirm_password']) {
				$this->request->data['User']['password'] = $this->request->data['User']['new_password'];
				$password_changed = true;
			} else {
				$password_mismatch = true;
				// $this->User->validationErrors['new_password'] must be set 
				// after $this->User->validates() 
			}
			$this->User->set($this->request->data);
			if ($this->User->validates()) {
				// Force lowercase email
				$this->request->data['User']['email'] = strtolower(trim($this->request->data['User']['email']));
				if ($this->User->save()) {
					// Updates the username in Session, which is read in the sidebar
					$this->Session->write('Auth.User.name', $this->request->data['User']['name']);
					$this->Flash->success('Your profile has been updated.');
					if ($password_changed) {
						$this->Flash->success('Your password has been changed.');
						
						// Unset passwords so those fields aren't auto-populated
						unset($this->request->data['User']['new_password']);
						unset($this->request->data['User']['confirm_password']);
					}
				} else {
					$this->Flash->error('Error updating profile.');
				}
			}
		}
		if (isset($password_mismatch)) {
			$this->User->validationErrors['confirm_password'] = "Your passwords did not match.";	
		}
		if (isset($no_password_confirmation)) {
			$this->User->validationErrors['confirm_password'] = "You must also type in your new password here to confirm it.";
		}
		$this->set(array(
			'title_for_layout' => 'My Profile'
		));
	}
	
	// Set up ACL
	/*
	public function initDB() {
	    $group = $this->User->Group;
	    //Allow admins to everything
	    $group->id = 1;
	    $this->Acl->allow($group, 'controllers');
	
	    //allow commentary authors to do anything to commentaries and tags
	    $group->id = 2;
	    $this->Acl->deny($group, 'controllers');
	    $this->Acl->allow($group, 'controllers/Commentaries');
	    $this->Acl->allow($group, 'controllers/Tags');
	
	    //we add an exit to avoid an ugly "missing views" error message
	    echo "all done";
	    exit;
	}
	*/	
}