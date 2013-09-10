<?php
App::uses('Commentary', 'Model');

/**
 * Commentary Test Case
 *
 */
class CommentaryTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.commentary', 'app.user', 'app.group', 'app.tag', 'app.commentaries_tag');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Commentary = ClassRegistry::init('Commentary');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Commentary);

		parent::tearDown();
	}

}
