<?php
App::uses('AppModel', 'Model');
/**
 * Tag Model
 *
 * @property Tag $ParentTag
 * @property Tag $ChildTag
 * @property Commentary $Commentary
 */
class Tag extends AppModel {
	public $actsAs = array(
		'Containable', 
		'Tree'
	);
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'selectable' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ParentTag' => array(
			'className' => 'Tag',
			'foreignKey' => 'parent_id',
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
		'ChildTag' => array(
			'className' => 'Tag',
			'foreignKey' => 'parent_id',
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


/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Commentary' => array(
			'className' => 'Commentary',
			'joinTable' => 'commentaries_tags',
			'foreignKey' => 'tag_id',
			'associationForeignKey' => 'commentary_id',
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

	public function getIdFromName($name) {
		$name = trim($name);
		$name = strtolower($name);
		$result = $this->find('list', array(
			'conditions' => array(
				'name' => $name
			),
			'limit' => 1
		));
		if (empty($result)) {
			return false;
		}
		$result = array_keys($result);
		return reset($result);
	}
	
	/**
	 * Used by the tag adder (in the tag manager) to determine how indented a line is
	 * @param string $name
	 * @return number
	 */
	public function getIndentLevel($name) {
		$level = 0;
		for ($i = 0; $i < strlen($name); $i++) {
			if ($name[$i] == "\t" || $name[$i] == '-') {
				$level++;	
			} else {
				break;	
			}
		}
		return $level;
    }
	
	public function parentNode() {
		return null;
	}
}