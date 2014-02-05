<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $helpers = array(
		'Js' => array('Jquery'),
		'Html', 
		'Time', 
		'Text', 
		'Session', 
		'Paginator', 
		'Form',
		'DataCenter.Permission'
	);
	public $components = array(
		'DebugKit.Toolbar',
		'DataCenter.AutoLogin' => array(
			'username' => 'email',
			'expires' => '+1 year'
		),
		'DataCenter.Flash',
		'DataCenter.TagManager',
		'Acl',
        'Auth' => array(
            'authorize' => array(
                'Actions' => array('actionPath' => 'controllers')
            ),
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'email'),
            		'contain' => false
				)
			)
        ),
        'Session'
	);
	
	public function beforeFilter() {
		//Configure AuthComponent
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login', 'plugin' => false, 'admin' => false);
		$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login', 'plugin' => false, 'admin' => false);
		$this->Auth->loginRedirect = array('controller' => 'commentaries', 'action' => 'index', 'plugin' => false, 'admin' => false);
        
		// Allow all actions by default, deny them explicitly in other controllers
		$this->Auth->allow();
		
		$this->AutoLogin->settings = array(
			// Model settings
			'model' => 'User',
			'username' => 'email',
			'password' => 'password',
	 
			// Controller settings
			'plugin' => '',
			'controller' => 'users',
			'loginAction' => 'login',
			'logoutAction' => 'logout',
	 
			// Cookie settings
			'cookieName' => 'rememberMe',
			'expires' => '+1 year',
	 
			// Process logic
			'active' => true,
			'redirect' => true,
			'requirePrompt' => true
		);
	}
	
	public function beforeRender() {
		// Provide recent commentaries for the sidebar
		App::Uses('Commentary', 'Model');
		$Commentary = new Commentary();
		$this->set('recent_commentaries', $Commentary->find('all', array(
			'limit' => 4,
			'order' => 'Commentary.published_date DESC',
			'fields' => array('Commentary.id', 'Commentary.title', 'Commentary.summary', 'Commentary.slug'),
			'conditions' => array('Commentary.is_published' => 1),
			'contain' => false
		)));
		
		// Provide top tags for the sidebar
		$this->set('top_tags', $this->TagManager->getTop('Commentary', 10));
		
		if ($this->Auth->loggedIn()) {
			$user_group = $this->Auth->user('Group.name');
		} else {
			$user_group = null;
		}
		$this->set('user_group', $user_group);
		
		// Set list of unpublished commentaries for members of the newsmedia
		if ($user_group == 'Newsmedia') {
			$this->set('unpublished', $Commentary->getUnpublishedList());
		}
	}
	
/**
	 * Renders a page that displays $params['message'] 
	 * with optional $params['class'], optional title $params['title'], and
	 * optional link back to $params['back'] (which can be a URL or array).
	 * Should be called as 'return $this->renderMessage($params);'
	 * @param array $params
	 */
	protected function renderMessage($params) {
		if (isset($params['message'])) {
			$this->set('message', $params['message']);
		}
		if (isset($params['title'])) {
			$this->set('title_for_layout', $params['title']);
		}
		if (isset($params['class'])) {
			$this->set('class', $params['class']);
		}
		if (isset($params['back'])) {
			$this->set('back', $params['back']);
		}
		if (isset($params['layout'])) {
			$this->layout = $params['layout'];
		}
		$this->render('/Pages/message');
	}
}