<?php
App::uses('AppController', 'Controller');
/**
 * Commentaries Controller
 *
 * @property Commentary $Commentary
 */
class CommentariesController extends AppController {
	public $name = 'Commentaries';
	public $components = array('RequestHandler');
	public $helpers = array('Rss');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(
			'autopublish', 
			'browse', 
			'export', 
			'generate_slugs', 
			'index', 
			'newsmedia_index',
			'rss', 
			'tagged', 
			'tags', 
			'view'
		);
	}
	
/**
 * index method
 *
 * @return void
 */
	public function index() {		
		$commentary = $this->Commentary->find('first', array(
			'order' => array('Commentary.published_date DESC'),
			'conditions' => array('Commentary.is_published' => 1)
		));
		$this->set(array(
			'commentary' => $commentary, 
			'newest' => true,
			'title_for_layout' => ''
		));
	}
	
	public function rss() {
		if ($this->RequestHandler->isRss()) {
			$commentaries = $this->Commentary->find('all', array(
				'fields' => array('id', 'title', 'summary', 'body', 'slug', 'published_date'),
				'contain' => false,
				'order' => 'published_date DESC',
				'conditions' => array('is_published' => 1),
				'limit' => 10
			));
			return $this->set(array(
				'commentaries' => $commentaries,
				'title_for_layout' => 'Weekly Commentary with Michael Hicks'
			));
		}
	}
	
/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Commentary->id = $id;
		if (!$this->Commentary->exists()) {
			throw new NotFoundException(__('Sorry, that commentary could not be found.'));
		}
		$this->set('commentary', $this->Commentary->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			// Process 'custom tags' field and eliminate duplicates
			$this->TagManager->processTagInput($this->request->data);
			
			$this->__setupAutopublish();
			
			$this->Commentary->create($this->request->data);
			if ($this->Commentary->validates()) {
				if ($this->Commentary->save($this->request->data)) {
					$this->Flash->set('Commentary added', 'success');
					if ($this->request->data['Commentary']['is_published']) {
						$this->__exportToIceMiller();
					}
					$this->redirect(array('controller' => 'commentaries', 'action' => 'view', $this->Commentary->id));
				} else {
					$this->Flash->set('The commentary could not be saved. Please try again.', 'error');
				}
			}
		}
		
		// Get the list of authors (not users with permission to post, the original authors of the commentaries posted)
		// and add current user 
		App::uses('User', 'Model');
		$User = new User();
		$authors = $User->find('list', array('conditions' => array('author' => 1)));
		$authors[$this->Auth->user('id')] = $this->Auth->user('name');
		
		// Sends $available_tags and $unlisted_tags to the view
		$this->TagManager->prepareEditor($this);
		$this->set(array('title_for_layout' => 'Add Commentary'));
		$this->set(compact('users', 'tags', 'authors'));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	function edit($id = false) {
		if (! $id) {
			$this->Flash->set('No ID specified. Which commentary do you want to edit?', 'error');
			$this->redirect($this->referer());
		}
		$this->Commentary->id = $id;
		if ($this->request->is('post')) {
			
			$this->__setupAutopublish();
			
			// Process 'custom tags' field and eliminate duplicates
			$this->TagManager->processTagInput($this->request->data);
			
			$this->Commentary->set($this->request->data);
			if ($this->Commentary->validates()) {
				if ($this->Commentary->save($this->request->data)) {
					$this->Flash->set('Commentary updated', 'success');
					$this->redirect(Router::url(array('controller' => 'commentaries', 'action' => 'view', 'id' => $id)));
				} else {
					$this->Flash->set('There was an error updating this commentary.', 'error');
				}
			}
		} else {
			if (! $this->Commentary->exists($id)) {
				$this->Flash->set('The specified commentary (ID: '.$id.') doesn\'t exist.', 'error');
				$this->redirect($this->referer());
			}
			$this->request->data = $this->Commentary->read();
		}

		// Get the list of authors (not users with permission to post, the original authors of the commentaries posted)
		// and add current user 
		App::uses('User', 'Model');
		$User = new User();
		$authors = $User->find('list', array('conditions' => array('author' => 1)));
		$authors[$this->Auth->user('id')] = $this->Auth->user('name');
		
		// Sends $available_tags and $unlisted_tags to the view
		$this->TagManager->prepareEditor($this);
		
		$this->set(array(
			'commentary_id' => $id,
			'authors' => $authors, 
			'thisMonth' => date('m'), 
			'thisDay' => date('d'), 
			'thisYear' => date('Y'),
			'title_for_layout' => 'Edit Commentary'
		));
	}
	
	// If publishing to a future date, save to drafts and auto-publish on the appropriate day
	private function __setupAutopublish() {
		$publish = $this->request->data['Commentary']['is_published'];
		$publishing_date = $this->request->data['Commentary']['published_date'];
		$publishing_date = $publishing_date['year'].$publishing_date['month'].$publishing_date['day'];
		if ($publish && $publishing_date > date('Ymd')) {
			$this->request->data['Commentary']['delay_publishing'] = 1;
			$this->request->data['Commentary']['is_published'] = 0;
		}
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Commentary->id = $id;
		if (! $this->Commentary->exists()) {
			throw new NotFoundException(__('Invalid commentary'));
		}
		if ($this->Commentary->delete()) {
			$this->Flash->set('Commentary deleted', 'success');
			$this->redirect(array('controller' => 'commentaries', 'action' => 'index'));
		}
		$this->Flash->set('Commentary was not deleted', 'error');
		$this->redirect(array('controller' => 'commentaries', 'action' => 'index'));
	}
	
	private function __exportToIceMiller($id = null) {
		if (! $id) {
			$id = $this->Commentary->id;	
		}
		if ($this->Commentary->exportToIceMiller($id)) {
			$this->Flash->set("Commentary #$id copied to Ice Miller website.", 'success');	
		} else {
			$this->Flash->set("There was an error copying commentary #$id to Ice Miller website. Please contact site administrator for assistance.", 'error');	
		}	
	}
	
	public function tagged($tag_id = null) {
		if (! is_numeric($tag_id)) {
			$this->Flash->set('Tag not found.', 'error');
			$this->redirect('/commentaries/tags');
		}
		$this->Commentary->Tag->id = $tag_id;
		$this->Commentary->Tag->read();
		if (! $tagName = $this->Commentary->Tag->data['Tag']['name']) {
			$this->Flash->set('Tag not found.', 'error');
			$this->redirect('/commentaries/tags');
		}
		$results = $this->Commentary->Tag->find('all', array(
			'conditions' => array('Tag.id' => $tag_id),
			'fields' => array('Tag.id'),
			'contain' => array(
				'Commentary' => array(
					'order' => 'Commentary.published_date DESC',
					'fields' => array('Commentary.id', 'Commentary.title', 'Commentary.created', 'Commentary.summary', 'Commentary.slug', 'Commentary.published_date'),
					'conditions' => array('Commentary.is_published' => 1)
				)
			)
		));
		$this->set(array(
			'tagName' => $tagName, 
			'commentaries' => empty($results) ? array() : $results[0]['Commentary'], 
			'title_for_layout' => ucwords($tagName) 
		));
	}
	
	public function tags() {
		$this->set(array(
			'tagCloud' => $this->TagManager->getCloud('Commentary'),
			'title_for_layout' => 'Tags'
		));
	}
	
	// Publishes any commentaries with today (or earlier) as their published_date and delay_publishing set to 1
	// Meant to be called by a cron job at every midnight, but can be called arbitrarily 
	public function autopublish() {
		$commentaries = $this->Commentary->find('all', array(
			'conditions' => array(
				'Commentary.delay_publishing' => 1, 
				'Commentary.published_date <=' => date('Y-m-d').' 00:00:00'
			),
			'fields' => array('Commentary.id', 'Commentary.title'),
			'contain' => array()
		));
		if (empty($commentaries)) {
			$this->Flash->set('No commentaries to auto-publish today.');
		} else {
			foreach ($commentaries as $commentary) {
				$id = $commentary['Commentary']['id'];
				$title = $commentary['Commentary']['title'];
				$this->Commentary->id = $id;
				$this->Commentary->saveField('is_published', 1);
				$this->Commentary->saveField('delay_publishing', 0);
				$this->Flash->set("Auto-published commentary #$id ($title)", 'success');
				$this->__exportToIceMiller();
			}
		}
		$this->render('DataCenter.Common/blank');
	}
	
	public function drafts() {		
		// Get commentaries
		$commentaries = $this->Commentary->find('all', array(
			'conditions' => array('Commentary.is_published' => 0),
			'order' => array('Commentary.modified DESC'),
			'fields' => array('Commentary.id', 'Commentary.title', 'Commentary.modified', 'Commentary.slug'),
			'contain' => false
		));
		
		// Either return them as an array or set them as view variables
        if (isset($this->params['requested'])) {
            return $commentaries;
        }
		$this->set(array(
			'commentaries' => $commentaries,
			'title_for_layout' => 'Commentary Drafts' 
		));
	}
	
	public function publish($id = null) {
		$this->Commentary->id = $id;
		$this->Commentary->read(array('is_published', 'title'));
		$is_published = $this->Commentary->data['Commentary']['is_published'];
		$title = $this->Commentary->data['Commentary']['title'];
		if ($is_published) {
			$this->Flash->set("<em>$title</em> is already published.");
		} elseif ($this->Commentary->publish()) {
			$this->Flash->set("<em>$title</em> has been published.", 'success');
			$this->__exportToIceMiller();
		} else {
			$this->Flash->set("There was an error publishing <em>$title</em>.", 'error');
		}
		$this->redirect($this->referer());
	}
	
	/* Outputs a view with all commentaries represented in object form.
	 * This is read by the Ice Miller website's import action so that it can copy commentaries
	 * to its database. */ 
	public function export($id = null) {
		if ($id) {
			$conditions = array('Commentary.id' => $id);
		} else {
			/* IDs for authors that will not be included in this export
			 * 15 => Pat Barkey
			 * Can either be an integer or an array (but array must have more than one value) */
			$excluded_user_ids = 15;
			
			$conditions = array(
				'Commentary.is_published' => 1,
				'Commentary.user_id NOT' => $excluded_user_ids
			);
		}
		$commentaries = $this->Commentary->find('all', array(
			'order' => array('Commentary.published_date DESC'),
			'conditions' => $conditions,
			'fields' => array('Commentary.title', 'Commentary.body', 'Commentary.published_date'),
			'contain' => array(
				'Tag' => array('fields' => array('name')),
				'User' => array('fields' => array('name'))
			)
		));
		$this->layout = 'ajax';
		$this->set(array(
			'commentaries' => $commentaries,
			'title_for_layout' => ''
		));
	}
	
	public function browse($year = null) {
		$earliestYear = substr($this->Commentary->field('published_date', 'published_date > 0', 'published_date ASC'), 0, 4);
		$latestYear = substr($this->Commentary->field('published_date', 'published_date > 0', 'published_date DESC'), 0, 4);
		if (is_numeric($year) && $year >= $earliestYear && $year <= $latestYear) {
			$title_for_layout = "Browse ($year)";
		} else {
			$year = $latestYear;
			$title_for_layout = 'Browse';
		}
		$commentaries = $this->Commentary->find('all', array(
			'conditions' => array(
				'Commentary.published_date LIKE' => "$year%",
				'Commentary.is_published' => 1
			), 
			'order' => 'Commentary.published_date ASC',
			'fields' => array('Commentary.id', 'Commentary.title', 'Commentary.summary', 'Commentary.created', 'Commentary.published_date', 'Commentary.slug')
		));
		
		// If an array is being requested by an element
        if (isset($this->params['requested'])) {
            return $commentaries;
        }
        
		$this->set(compact('commentaries', 'year', 'latestYear', 'earliestYear', 'title_for_layout'));
	}
	
	public function generate_slugs() {
		$commentaries = $this->Commentary->find('list', array(
			'conditions' => array('slug' => '')
		));
		if (empty($commentaries)) {
			$this->Flash->set('All commentaries already have slugs.');
		} else {
			foreach ($commentaries as $id => $title) {
				$this->Commentary->create();
				$this->Commentary->id = $id;
				$this->Commentary->save(
					array('id' => $id, 'title' => $title), 
					false,
					array('slug')
				);
			}
			$this->Flash->set('Created slugs for '.count($commentaries).' commentaries.', 'succes');
		}
		$this->render('DataCenter.Common/blank');
	}

	public function newsmedia_index() {
		$this->set(array(
			'commentary' => $this->Commentary->getNextForNewsmedia(),
			'title_for_layout' => 'Next Article to Publish'
		));
	}
}