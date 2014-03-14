<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<p>
	<?php echo $this->Html->link(
		'Add a New User', 
		array(
			'action' => 'add'
		)
	); ?>
</p>

<div id="manage_users_index">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th>
				<?php echo $this->Paginator->sort('name');?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('email');?>
			</th>
			<th>
				<?php echo $this->Paginator->sort('group_id');?>
			</th>
			<th class="actions">
				<?php echo __('Actions');?>
			</th>
		</tr>
		<?php foreach ($users as $user): ?>
			<tr>
				<td>
					<?php echo h($user['User']['name']); ?>&nbsp;
				</td>
				<td>
					<?php echo $this->Text->autoLinkEmails(h($user['User']['email'])); ?>&nbsp;
				</td>
				<td>
					<?php echo $user['Group']['name']; ?>
				</td>
				<td class="actions">
					<?php echo $this->Html->link(
						'Edit', 
						array(
							'action' => 'edit', 
							$user['User']['id'],
							'admin' => false
						)
					); ?>
					<?php echo $this->Form->postLink(
						'Delete', 
						array(
							'action' => 'delete', 
							$user['User']['id'],
							'admin' => false
						), 
						null, 
						'Are you sure you want to delete '.$user['User']['name'].'\'s account?'
					); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<div class="paging">
		<?php
			if ($this->Paginator->hasPrev()) {
				echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
			}
			echo $this->Paginator->numbers(array('separator' => ''));
			if ($this->Paginator->hasNext()) {
				echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
			}
		?>
	</div>
</div>