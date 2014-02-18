<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<div id="newsmedia_my_account">
	<?php echo $this->Form->create('User'); ?>
	
	<fieldset>
		<legend>
			Update Info
		</legend>
		<?php
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
		?>
	</fieldset>
	
	<fieldset>
		<legend>
			Alerts
		</legend>
		<?php echo $this->Form->input(
			'nm_email_alerts',
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
					'autocomplete' => 'off',
					'required' => false
				)
			);
			echo $this->Form->input(
				'confirm_password', 
				array(
					'type' => 'password',
					'required' => false
				)
			);
		?>
	</fieldset>
	
	<?php echo $this->Form->end('Submit'); ?>
</div>