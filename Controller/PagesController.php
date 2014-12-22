<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $name = 'Pages';
	public $uses = array();

	public function beforeFilter() {
		parent::beforeFilter();
	}

	function home() {

	}
}
