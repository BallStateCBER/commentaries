<?php
App::uses('AppModel', 'Model');
/**
 * Commentary Model
 *
 * @property User $User
 * @property Tag $Tag
 */
class Commentary extends AppModel {
	public $actsAs = array(
		'Acl' => array(
			'type' => 'controlled'
		),
		'Containable',
		'Sluggable.Sluggable' => array(
			'label' => 'title',
			'slug' => 'slug',
			'separator' => '-',
			'overwrite' => true   
		)
	);
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Title required',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'body' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Your invisible commentary will only confuse people',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'is_published' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'delay_publishing' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'published_date' => array(
			'rule'       => array('datetime', 'ymd'),
			'message'    => 'This date appears to be invalid.',
			'allowEmpty' => false,
			'required' => true
        )
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Tag' => array(
			'className' => 'Tag',
			'joinTable' => 'commentaries_tags',
			'foreignKey' => 'commentary_id',
			'associationForeignKey' => 'tag_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);
	
	// Exports the commentary specified by ID to the Ice Miller website
	public function exportToIceMiller($id = null) {
		if (! $id) {
			$id = $this->id;
		}
		
		// Development server
		if (stripos($_SERVER['SERVER_NAME'], 'localhost') !== false) {
			$url = 'http://icemiller.localhost/articles/import_commentaries/'.$id;
		// Production server
		} else {
			$url = 'http://icemiller.cberdata.org/articles/import_commentaries/'.$id;	
		}
		$results = trim(file_get_contents($url));
		return (boolean) $results;
	}
	
	public function publish() {
		if (! $this->id) {
			return false;
		}
		$this->set('is_published', 1);
		$this->set('published_date', date('Y-m-d', time()).' 00:00:00');
		return $this->save();
	}
	
	public function getUnpublishedList() {
		return $this->find('list', array(
			'conditions' => array(
				'Commentary.is_published' => 0
			),
			'order' => array(
				'Commentary.modified DESC'
			)
		));
	}
	
	public function getNextForNewsmedia() {
		return $this->find('first', array(
			'conditions' => array(
				'Commentary.is_published' => 0,
				'Commentary.published_date >' => date('Y-m-d').' 00:00:00'
			),
			'order' => array(
				'Commentary.published_date ASC'
			)
		));
	}
}