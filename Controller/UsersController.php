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
			'forgot_password',
			'login',
			'logout',
			'reset_password'
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
				$this->Flash->success('The user has been added.');
				
				// Clear form
				$this->request->data = array();
			} else {
				$this->Flash->error('There was an error adding the user.');
			}
		}
		$this->set(array(
			'groups' => $this->User->Group->find('list'),
			'title_for_layout' => 'Add User'
		));
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
	        	$this->User->invalidate('email', 'Your email address or password was incorrect.');
				unset($this->request->data['User']['password']);
	        }
	    }
	    $this->set('title_for_layout', 'Login');
	}
	
	
	
	public function logout() {
		$this->Permissions->forgetPermissions();
		$this->Flash->success('You are now logged out.');
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

	public function newsmedia_my_account() {
		$this->User->id = $this->Auth->user('id');
		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data['User'];
			$this->User->set($data);
			
			if ($data['new_password'] == '') {
				// Remove validation for both fields
				unset($this->User->validate['new_password']);
				unset($this->User->validate['confirm_password']);
			}
			
			// Invalidate email only if it changes to another user's email  
			unset($this->User->validate['email']['emailUnclaimed']);
			$data['email'] = $this->User->cleanEmail($data['email']);
			$email_lookup = $this->User->getUserIdWithEmail($data['email']);
			if ($email_lookup && $email_lookup !== $this->User->id) {
				$this->User->invalidate('email', 'Sorry, another account is already using that email address.');
			}
			
			if ($this->User->validates()) {
				if ($data['new_password'] != '') {
					$this->User->set('password', $data['new_password']);
				}
				$saved = $this->User->save(null, true, array(
					'name',
					'email',
					'password',
					'nm_email_alerts'
				));
				if ($saved) {
					$message = 'Your information has been updated';
					if ($data['new_password'] != '') {
						$message .= ' and password changed';
					}
					$this->Flash->success($message.'.');
				} else {
					$this->Flash->error('There was an error updating your information.');
				}
			}
			
			// Unset passwords so those fields aren't auto-populated
			unset($this->request->data['User']['new_password']);
			unset($this->request->data['User']['confirm_password']);
		} else {
			$this->request->data = $this->User->read();
		}
		$this->set(array(
			'title_for_layout' => 'My Account'
		));
	}

	public function add_newsmedia() {
		/* Set information about the next commentary to be published 
		 * if alerts have already been sent out for it and this user 
		 * needs to be caught up. */
		$this->loadModel('Commentary');
		$next_commentary = $this->Commentary->getNextForNewsmedia();
		if (! empty($next_commentary)) {
			$commentary_id = $next_commentary['Commentary']['id'];
			$alerts_sent = $this->Commentary->isMostRecentAlert($commentary_id);
			if ($alerts_sent) {
				$this->set(compact('next_commentary'));
			}
		}
		
		if ($this->request->is('post')) {
			$user = $this->request->data;
			
			// Make sure password isn't blank
			$password = $user['User']['password'];
			if ($password === '') {
				$password = $this->User->generatePassword();
			}
			
			$this->User->create($user);
			$this->User->set(array(
				'group_id' => 3,
				'nm_email_alerts' => 1,
				'password' => $password,
				'email' => $this->User->cleanEmail($user['User']['email'])
			));
			
			if ($this->User->save()) {
				$this->Flash->success('Newsmedia member added.');
				$user['User']['id'] = $this->User->id;
				
				if (! $this->User->sendNewsmediaIntroEmail($user)) {
					$this->Flash->error('There was an error sending the introductory email.');
				}
				
				if ($user['User']['send_alert'] && ! empty($next_commentary) && $alerts_sent) {
					if (! $this->User->sendNewsmediaAlertEmail($user, $next_commentary)) {
						$this->Flash->error('There was an error sending an alert message for the article "'.$next_commentary['Commentary']['title'].'".');
					}
				}
				
				// Clear form
				$this->request->data = array();
				$this->request->data['User']['send_alert'] = true;
			} else {
				$this->Flash->error('There was an error adding the user.');
			}
		} else {
			$this->request->data['User']['send_alert'] = true;
		}
		
		// Show a randomly-generated password instead of a blank field
		if (! isset($this->request->data['User']['password']) || empty($this->request->data['User']['password'])) {
			$this->request->data['User']['password'] = $this->User->generatePassword();
		}
		
		if ($this->Auth->user('Group.name') == 'Newsmedia') {
			$title = 'Add a Reporter to Newsmedia Alerts';
		} else {
			$title = 'Add Newsmedia Member';
		}
		$this->set(array(
			'title_for_layout' => $title
		));
	}

	public function forgot_password() {
		if ($this->request->is('post')) {
			$admin_email = Configure::read('admin_email');
			$email = $this->User->cleanEmail($this->request->data['User']['email']);
			if (empty($email)) {
				$this->Flash->error('Please enter the email address associated with your account to have your password reset. Email <a href="mailto:'.$admin_email.'">'.$admin_email.'</a> if you need any assistance.');
			} else {
				$user_id = $this->User->getUserIdWithEmail($email);
				if ($user_id) {
					if ($this->User->sendPasswordResetEmail($user_id, $email)) {
						$this->Flash->success('You should be receiving an email shortly with a link to reset your password.');
					} else {
						$this->Flash->error('There was an error sending your password-resetting email out. Please try again, and if it continues to not work, email <a href="mailto:'.$admin_email.'">'.$admin_email.'</a> if you need any assistance.');
					}
				} else {
					$this->Flash->error('We couldn\'t find an account registered with the email address <strong>'.$email.'</strong>. Make sure you spelled it correctly. Email <a href="mailto:'.$admin_email.'">'.$admin_email.'</a> if you need any assistance.');	
				}
			}
		}
		$this->set(array(
			'title_for_layout' => 'Forgot Password'
		));
	}
	
	public function reset_password($user_id, $reset_password_hash) {
		$this->User->id = $user_id;
		$email = $this->User->field('email');
		$expected_hash = $this->User->getResetPasswordHash($user_id, $email);
		if ($reset_password_hash != $expected_hash) {
			$this->Flash->error('Invalid password-resetting code. Make sure that you entered the correct address and that the link emailed to you hasn\'t expired.');
			$this->redirect('/');	
		}
		if ($this->request->is('post')) {
			$this->User->set($this->request->data);
			if ($this->User->validates()) {
				$this->User->set('password', $this->data['User']['new_password']);
				if ($this->User->save()) {
					
					// Attempt to log the user in
					$credentials = $this->User->find(
						'first',
						array(
							'conditions' => array(
								'User.id' => $user_id
							)
						)
					);
					$credentials['User']['password'] = $this->request->data['User']['new_password'];
					
					/* Modify credentials so that the session's Auth.User variable is 
					 * formatted the same as if this user logged in normally. */
					$credentials = array_merge($credentials, $credentials['User']);
					unset($credentials['User']); 
					
					if ($this->Auth->login($credentials)) {
						$this->Flash->success('Password changed. You are now logged in.');
						$this->redirect('/');
					} else {
						$this->Flash->success('Password changed. You may now log in.');
						$this->redirect(array(
							'controller' => 'users', 
							'action' => 'login'
						));
					}
				} else {
					$this->Flash->error('There was an error changing your password.');
				}
			}
			unset($this->request->data['User']['new_password']);
			unset($this->request->data['User']['confirm_password']);
		}
		$this->set(array(
			'title_for_layout' => 'Reset Password',
			'user_id' => $user_id,
			'reset_password_hash' => $reset_password_hash
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