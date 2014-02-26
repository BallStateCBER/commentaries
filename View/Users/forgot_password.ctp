<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<p>
	If you have forgotten the password to your account, you can enter the email address 
	associated with it below. We'll send you an email with a link to reset your password.
	If you need assistance, please contact 
	<a href="mailto:<?php echo Configure::read('admin_email'); ?>"><?php echo Configure::read('admin_email'); ?></a>.
</p>

<?php
	echo $this->Form->create(
		'User',
		array(
			'controller' => 'users', 
			'action' => 'forgot_password'
		)
	);
	echo $this->Form->input(
		'email',
		array(
			'label' => false
		)
	);
	echo $this->Form->end('Send password-resetting email');
?>