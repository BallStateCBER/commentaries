<?php
	echo $this->Form->create(
		'User', 
		array(
			'url' => array(
				'controller' => 'users', 
				'action' => 'reset_password',
				$user_id,
				$reset_password_hash
			)
		)
	);
	echo $this->Form->input(
		'new_password', 
		array(
			'label' => 'New Password', 
			'type' => 'password', 
			'autocomplete' => 'off'
		)
	);
	echo $this->Form->input(
		'confirm_password', 
		array(
			'label' => 'Confirm Password',
			'type' => 'password', 
			'autocomplete' => 'off'
		)
	);
	echo $this->Form->end('Reset password');