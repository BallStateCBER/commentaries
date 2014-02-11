<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<p>
	When you add members of the newsmedia, they will immediately receive an email explaining that they will
	receive email alerts whenever new commentaries are posted so that they can immediately start preparing
	them for print publication. This initial email will contain login information in case they want to 
	change their contact information, stop receiving emails, or add other members of the newsmedia to this
	service.
</p>

<?php
	echo $this->Form->create('User');
	echo $this->Form->input('name');
	echo $this->Form->input('email');
	echo $this->Form->input('password', array(
		'type' => 'text',
		'required' => true
	));
	echo $this->Form->end('Add');
?>