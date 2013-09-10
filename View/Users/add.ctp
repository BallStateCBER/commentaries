<h1 class="page_title">
	Add a New User
</h1>
<?php echo $this->Form->create('User');?>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('bio');
		echo $this->Form->input('sex', array('type' => 'select', 'options' => array('m' => 'Male', 'f' => 'Female')));
		echo $this->Form->input('password');
		echo $this->Form->input('picture', array('after' => '<br />(the filename of the picture uploaded to /webroot/img/users, if any)'));
		echo $this->Form->input('group_id', array('empty' => false));
	?>
<?php echo $this->Form->end(__('Submit'));?>
