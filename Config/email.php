<?php
/**
 * This is email configuration file.
 *
 * Use it to configure email transports of Cake.
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
 * @since         CakePHP(tm) v 2.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * In this file you set up your send email details.
 *
 * @package       cake.config
 */
/**
 * Email configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * transport => The name of a supported transport; valid options are as follows:
 *		Mail 		- Send using PHP mail function
 *		Smtp		- Send using SMTP
 *		Debug		- Do not send the email, just return the result
 *
 * You can add custom transports (or override existing transports) by adding the
 * appropriate file to app/Network/Email.  Transports should be named 'YourTransport.php',
 * where 'Your' is the name of the transport.
 *
 * from =>
 * The origin email. See CakeEmail::from() about the valid values
 *
 */
class EmailConfig {

	public $default = array(
		'transport' => 'Mail',
		'from' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'sender' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'returnPath' => 'cber@bsu.edu',
		'emailFormat' => 'both',
		'replyTo' => 'cber@bsu.edu'
		//'charset' => 'utf-8',
		//'headerCharset' => 'utf-8',
	);
	
	public $newsmedia_alert = array(
		'transport' => 'Mail',
		'from' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'sender' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'returnPath' => 'cber@bsu.edu',
		'replyTo' => 'cber@bsu.edu',
		'emailFormat' => 'both',
		'subject' => 'Mike Hicks Weekly Commentary now available',
		'template' => 'newsmedia_alert'
	);
	
	public $newsmedia_intro = array(
		'transport' => 'Mail',
		'from' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'sender' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'returnPath' => 'cber@bsu.edu',
		'replyTo' => 'cber@bsu.edu',
		'emailFormat' => 'both',
		'subject' => 'You have been subscribed to the Mike Hicks Weekly Commentary newsmedia alert service',
		'template' => 'newsmedia_intro'
	);

	public $reset_password = array(
		'transport' => 'Mail',
		'from' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'sender' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'returnPath' => 'cber@bsu.edu',
		'replyTo' => 'cber@bsu.edu',
		'emailFormat' => 'both',
		'subject' => 'Mike Hicks Weekly Commentary: Reset password',
		'template' => 'reset_password'
	);
	
	public $newsmedia_alert_report = array(
		'transport' => 'Mail',
		'from' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'sender' => array('commentaries@cberdata.org' => 'Ball State CBER'),
		'returnPath' => 'cber@bsu.edu',
		'replyTo' => 'cber@bsu.edu',
		'emailFormat' => 'html',
		'subject' => 'Weekly Commentary Newsmedia Alert Report',
		'template' => 'newsmedia_alert_report',
		'helpers' => array('Session')
	);

	public $smtp = array(
		'transport' => 'Smtp',
		'from' => array('site@localhost' => 'My Site'),
		'host' => 'localhost',
		'port' => 25,
		'timeout' => 30,
		'username' => 'user',
		'password' => 'secret',
		'client' => null,
		'log' => false,
		//'charset' => 'utf-8',
		//'headerCharset' => 'utf-8',
	);

	public $fast = array(
		'from' => 'you@localhost',
		'sender' => null,
		'to' => null,
		'cc' => null,
		'bcc' => null,
		'replyTo' => null,
		'readReceipt' => null,
		'returnPath' => null,
		'messageId' => true,
		'subject' => null,
		'message' => null,
		'headers' => null,
		'viewRender' => null,
		'template' => false,
		'layout' => false,
		'viewVars' => null,
		'attachments' => null,
		'emailFormat' => null,
		'transport' => 'Smtp',
		'host' => 'localhost',
		'port' => 25,
		'timeout' => 30,
		'username' => 'user',
		'password' => 'secret',
		'client' => null,
		'log' => true,
		//'charset' => 'utf-8',
		//'headerCharset' => 'utf-8',
	);

}
