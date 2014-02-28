<?php 
	echo $this->Form->create('User');
	echo $this->Form->input(
		'name', 
		array(
			'label' => 'Name'
		)
	);
	echo $this->Form->input(
		'email', 
		array(
			'label' => 'Email'
		)
	);
	echo $this->Form->input(
		'sex', 
		array(
			'label' => 'Sex', 
			'options' => array(
				'', 
				'm' => 'Male', 
				'f' => 'Female'
			)
		)
	);
	echo $this->Form->input(
		'bio', 
		array(
			'label' => 'Bio', 
			'style' => 'height: 300px; width: 400px;', 
			'between' => '<div class="footnote">ENTER double-spaces. SHIFT + ENTER single-spaces.</div>'
		)
	);
	echo $this->Form->input(
		'new_password', 
		array(
			'label' => 'Password', 
			'type' => 'password', 
			'autocomplete' => 'off'
		)
	);
	echo $this->Form->input(
		'confirm_password', 
		array(
			'type' => 'password'
		)
	);
	echo $this->Form->input(
		'id', 
		array(
			'type' => 'hidden'
		)
	);
	echo $this->Form->end('Submit');
	echo $this->element(
		'rich_text_editor_init', 
		array(
			'customConfig' => Configure::read('ckeditor_custom_config')
		), 
		array(
			'plugin' => 'DataCenter'
		)
	);