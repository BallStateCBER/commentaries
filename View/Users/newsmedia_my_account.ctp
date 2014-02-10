<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<div id="newsmedia_my_account">
	<?php echo $this->Form->create('User'); ?>
	
	<fieldset>
		<legend>
			Update Contact Info
		</legend>
		<?php
			echo $this->Form->input(
				'email', 
				array(
					'label' => 'Email'
				)
			);
		?>
	</fieldset>
	
	<fieldset>
		<legend>
			Alerts
		</legend>
		<?php echo $this->Form->input(
			'receive_emails',
			array(
				'label' => 'Receive email alerts when new commentaries are available',
				'type' => 'checkbox'
			)
		); ?>
	</fieldset>
	
	<fieldset>
		<legend>
			Change password
		</legend>
		<?php
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
		?>
	</fieldset>
	
	<?php echo $this->Form->end('Submit'); ?>
</div>