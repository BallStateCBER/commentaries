<?php

use Cake\Core\Configure;
use Cake\Http\Exception\InternalErrorException;

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
			'generate_slugs',
			'index',
			'newsmedia_index',
			'rss',
			'send_timed_alert',
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
				if ($this->Commentary->save()) {
					$this->Flash->success('Commentary added');
					if ($this->request->data['Commentary']['is_published']) {
					}
					$this->redirect(array(
						'controller' => 'commentaries',
						'action' => 'view',
						$this->Commentary->id
					));
				} else {
					$this->Flash->error('The commentary could not be saved. Please try again.');
				}
			}
		} else {
			$this->request->data['Commentary']['alert_media'] = 1;
		}

		// Get the list of authors (not users with permission to post, the original authors of the commentaries posted)
		$this->loadModel('User');
		$authors = $this->User->find('list', array(
			'conditions' => array(
				'author' => 1
			)
		));
		// and add current user
		$authors[$this->Auth->user('id')] = $this->Auth->user('name');

		// Sends $available_tags and $unlisted_tags to the view
		$this->TagManager->prepareEditor($this);
		$this->set(array(
			'title_for_layout' => 'Add Commentary'
		));
		$this->set(compact(
			'users',
			'tags',
			'authors'
		));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	function edit($id = false) {
		if (! $id) {
			$this->Flash->error('No ID specified. Which commentary do you want to edit?');
			$this->redirect($this->referer());
		}
		$this->Commentary->id = $id;
		if ($this->request->is('post') || $this->request->is('put')) {

			$this->__setupAutopublish();

			// Process 'custom tags' field and eliminate duplicates
			$this->TagManager->processTagInput($this->request->data);

			$this->Commentary->set($this->request->data);
			if ($this->Commentary->validates()) {
				if ($this->Commentary->save()) {
					$this->Flash->success('Commentary updated');
					$this->redirect(array(
						'controller' => 'commentaries',
						'action' => 'view',
						'id' => $id
					));
				} else {
					$this->Flash->error('There was an error updating this commentary.');
				}
			}
		} else {
			if (! $this->Commentary->exists($id)) {
				$this->Flash->error('The specified commentary (ID: '.$id.') doesn\'t exist.');
				$this->redirect($this->referer());
			}
			$this->request->data = $this->Commentary->read();
		}

		// Get the list of authors (not users with permission to post, the original authors of the commentaries posted)
		$this->loadModel('User');
		$authors = $this->User->find('list', array(
			'conditions' => array(
				'author' => 1
			)
		));
		// and add current user
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
			$this->Flash->success('Commentary deleted');
			$this->redirect($this->referer());
		}
		$this->Flash->error('Commentary was not deleted');
		$this->redirect($this->referer());
	}

	public function tagged($tag_id = null) {
		if (! is_numeric($tag_id)) {
			$this->Flash->error('Tag not found.');
			$this->redirect(array(
				'controller' => 'commentaries',
				'action' => 'tags'
			));
		}
		$this->Commentary->Tag->id = $tag_id;
		$this->Commentary->Tag->read();
		$tagName = isset($this->Commentary->Tag->data['Tag']['name'])
            ? $this->Commentary->Tag->data['Tag']['name']
            : false;
		if (! $tagName) {
			$this->Flash->error('Tag not found.');
			$this->redirect(array(
				'controller' => 'commentaries',
				'action' => 'tags'
			));
		}
		$results = $this->Commentary->Tag->find('all', array(
			'conditions' => array(
				'Tag.id' => $tag_id
			),
			'fields' => array(
				'Tag.id'
			),
			'contain' => array(
				'Commentary' => array(
					'order' => 'Commentary.published_date DESC',
					'fields' => array(
						'Commentary.id',
						'Commentary.title',
						'Commentary.created',
						'Commentary.summary',
						'Commentary.slug',
						'Commentary.published_date'
					),
					'conditions' => array(
						'Commentary.is_published' => 1
					)
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
		$tag_cloud = $this->TagManager->getCloud('Commentary');
		$occurrances = Set::extract('/occurrences', $tag_cloud);
		$this->set(array(
			'tagCloud' => $tag_cloud,
			'title_for_layout' => 'Tags',
			'min_font_size' => 10,
			'max_font_size' => 60,
			'max_occurrances' => max($occurrances)
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
			'fields' => array(
				'Commentary.id',
				'Commentary.title'
			),
			'contain' => false
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
				$this->Flash->success("Auto-published commentary #$id ($title)");
			}
		}
		$this->render('DataCenter.Common/blank');
	}

	public function drafts() {
		// Get commentaries
		$commentaries = $this->Commentary->find('all', array(
			'conditions' => array(
				'Commentary.is_published' => 0
			),
			'order' => array(
				'Commentary.modified DESC'
			),
			'fields' => array(
				'Commentary.id',
				'Commentary.title',
				'Commentary.modified',
				'Commentary.slug'
			),
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
			$this->Flash->success("<em>$title</em> has been published.");
		} else {
			$this->Flash->error("There was an error publishing <em>$title</em>.");
		}
		$this->redirect($this->referer());
	}

	public function browse($year = null) {
		$earliestYear = substr($this->Commentary->field('published_date', 'published_date > 0', 'published_date ASC'), 0, 4);
		$latestYear = substr($this->Commentary->field('published_date', 'published_date > 0', 'published_date DESC'), 0, 4);
		if (is_numeric($year) && $year >= $earliestYear && $year <= $latestYear) {
			$title_for_layout = "CBER Weekly Commentaries - $year";
		} else {
			$year = $latestYear;
			$title_for_layout = 'CBER Weekly Commentaries';
		}
		$commentaries = $this->Commentary->find('all', array(
			'conditions' => array(
				'Commentary.published_date LIKE' => "$year%",
				'Commentary.is_published' => 1
			),
			'order' => 'Commentary.published_date ASC',
			'fields' => array(
				'Commentary.id',
				'Commentary.title',
				'Commentary.summary',
				'Commentary.created',
				'Commentary.published_date',
				'Commentary.slug'
			)
		));

		// If an array is being requested by an element
        if (isset($this->params['requested'])) {
            return $commentaries;
        }

		$this->set(compact(
			'commentaries',
			'earliestYear',
			'latestYear',
			'title_for_layout',
			'year'
		));
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
					compact(
						'id',
						'title'
					),
					false,
					array('slug')
				);
			}
			$this->Flash->succes('Created slugs for '.count($commentaries).' commentaries.');
		}
		$this->render('DataCenter.Common/blank');
	}

	public function newsmedia_index() {
		$this->set(array(
			'commentary' => $this->Commentary->getNextForNewsmedia(),
			'title_for_layout' => 'Next Article to Publish'
		));
	}

	private function __alertNewsmedia() {
		$commentary = $this->Commentary->getNextForNewsmedia();

		if (empty($commentary)) {
			$this->Flash->set('No commentary available to alert newsmedia to.');
			return;
		}
		if (isset($commentary['Commentary'])) {
			$commentary = $commentary['Commentary'];
		}

		$this->loadModel('User');
		$newsmedia = $this->User->find('all', array(
			'conditions' => array(
				'User.group_id' => 3, // "Newsmedia"
				'User.nm_email_alerts' => 1,
				'OR' => array(
					'User.last_alert_article_id' => null,
					'User.last_alert_article_id <>' => $commentary['id']
				)
			),
			'contain' => false,
			'fields' => array(
				'User.id',
				'User.name',
				'User.email'
			)
		));
		$count = count($newsmedia);
		if ($count == 0) {
			$this->Flash->set('Newsmedia not alerted. No applicable members (opted in to alerts and not yet alerted) found in database.');
			return;
		}

		// Impose limit on how many emails are sent out in one batch
		$limit = 5;
		if ($count > $limit) {
			$newsmedia = array_slice($newsmedia, 0, $limit);
		}

		// Send emails
		$error_recipients = array();
		$success_recipients = array();
		foreach ($newsmedia as $user) {
			if ($this->User->sendNewsmediaAlertEmail($user, $commentary)) {
				$success_recipients[] = $user['User']['email'];
			} else {
				$error_recipients[] = $user['User']['email'];
			}
		}

		// Output results
		if (empty($success_recipients)) {
			$this->Flash->set('No newsmedia alerts were sent');
		} else {
			$email_list = implode(', ', $success_recipients);
			$this->Flash->success("Newsmedia alerted: $email_list");
			if ($count > $limit) {
				$difference = $count - $limit;
				$this->Flash->set($difference.' more '.__n('user', 'users', $difference).' left to alert');
			} else {
				$this->Flash->set('All newsmedia members have now been alerted');
			}
		}
		if (! empty($error_recipients)) {
			$this->Flash->error('Error sending newsmedia alerts to the following: '.implode(', ', $error_recipients));
		}
		$this->Flash->set('Total time spent: '.DebugTimer::requestTime());
		$this->__sendNewsmediaAlertReport();
	}

	/**
	 * Emails a report to the administrator with the results of __alertNewsmedia()
	 * (Actually, with whatever messages are in Session.FlashMessage)
	 */
	private function __sendNewsmediaAlertReport() {
		App::uses('CakeEmail', 'Network/Email');
		$email = new CakeEmail('newsmedia_alert_report');
		$recipient_email = Configure::read('admin_email');
		$email->to($recipient_email);
		$results = $this->Session->read('FlashMessage');
		$email->viewVars(array('results' => $results));
        foreach ($results as $result) {
            $this->sendSlackMessage($result);
        }
		return $email->send();
	}

	public function send_timed_alert($cron_job_password) {
		$alert_day = 'Wednesday';
		if (date('l') != $alert_day) {
			$this->Flash->error('Alerts are only sent out on '.$alert_day.'s');
		} elseif (date('Hi') < '1400') {
			$this->Flash->error('Alerts are only sent out after 2pm on '.$alert_day);
		} elseif ($cron_job_password == Configure::read('cron_job_password')) {
			$this->__alertNewsmedia();
		} else {
			$this->Flash->error('Password incorrect');
		}
		$this->render('DataCenter.Common/blank');
	}

    /**
     * Sends a message to Slack
     *
     * @param string $text Message to send
     * @return void
     * @throws \Exception
     */
    public static function sendSlackMessage($text)
    {
        $url = Configure::read('slack_webhook');
        $curlHandle = curl_init($url);
        $payload = json_encode(compact('text'));
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        if (!curl_exec($curlHandle)) {
            throw new Exception('Error sending message to Slack. Details: ' . curl_error($curlHandle));
        }
        curl_close($curlHandle);
    }
}