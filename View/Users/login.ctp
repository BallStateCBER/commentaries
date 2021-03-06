<h1 class="page_title">
	Log in
</h1>
<?php
	echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));
	echo $this->Form->input('email');
	echo $this->Form->input('password', array(
		'required' => true
	));
	echo $this->Form->input('auto_login', array(
		'type' => 'checkbox',
		'label' => array('text' => ' Log me in automatically', 'style' => 'display: inline;'),
		'checked' => true
	));
	echo $this->Form->end('Login');
?>

<?php echo $this->Html->link(
	'Forgot password',
	array(
		'controller' => 'users',
		'action' => 'forgot_password'
	)
); ?>