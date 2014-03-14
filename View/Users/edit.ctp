<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<div class="users form">
	<?php
		echo $this->Form->create('User');
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('bio');
		echo $this->Form->input(
			'picture',
			array(
				'label' => 'Picture filename'
			)
		);
		echo $this->Form->input('group_id');
		echo $this->Form->end('Submit');
	?>
</div>