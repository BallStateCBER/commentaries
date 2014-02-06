<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

Router::parseExtensions('rss');
Router::connectNamed(array('slug'));
Router::connect('/', 			array('controller' => 'commentaries', 'action' => 'index'));
Router::connect('/login', 		array('controller' => 'users', 'action' => 'login'));
Router::connect('/logout', 		array('controller' => 'users', 'action' => 'logout'));
Router::connect('/tags', 		array('controller' => 'commentaries', 'action' => 'tags'));
Router::connect(
	'/tag/:id', 	
	array('controller' => 'commentaries', 'action' => 'tagged'), 
	array('id' => '[0-9]+', 'pass' => array('id'))
);
Router::connect(
	'/:id/:slug', 
	array('controller' => 'commentaries', 'action' => 'view'),
	array('id' => '[0-9]+', 'slug' => '[-_a-z0-9]+', 'pass' => array('id', 'slug'))
);
Router::connect(
	"/:id/*", 
	array('controller' => 'commentaries', 'action' => 'view'),
	array('id' => '[0-9]+', 'pass' => array('id'))
);
Router::connect(
	"/commentary/:id/*", 
	array('controller' => 'commentaries', 'action' => 'view'),
	array('id' => '[0-9]+', 'pass' => array('id'))
);

// So /index.rss leads to the RSS feed
Router::connect('/index', 		array('controller' => 'commentaries', 'action' => 'rss'));

Router::connect('/newsmedia', 	array('controller' => 'commentaries', 'action' => 'index', 'newsmedia' => true));

CakePlugin::routes();
require CAKE . 'Config' . DS . 'routes.php';