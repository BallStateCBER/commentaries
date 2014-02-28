<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<?php if (empty($commentaries)): ?>
	You currently have no commentaries saved as drafts.
<?php else: ?>
	<table class="my_commentaries">
		<thead>
			<tr>
				<th class="modified">Last Modified</th>
				<th>Title</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tfoot></tfoot>
		<tbody>
			<?php foreach ($commentaries as $key => $commentary): ?>
				<tr<?php if ($key % 2 == 1): ?> class="alternate"<?php endif; ?>>
					<td>
						<?php echo date('F j, Y', $this->Time->fromString($commentary['Commentary']['modified'])); ?>
					</td>
					<td>
						<?php echo $this->Html->link(
							$commentary['Commentary']['title'],
							array(
								'controller' => 'commentaries', 
								'action' => 'view', 
								'id' => $commentary['Commentary']['id'],
								'slug' => $commentary['Commentary']['slug']
							)
						); ?>
					</td>
					<td>
						<?php echo $this->Html->link(
							'Edit',
							array(
								'controller' => 'commentaries', 
								'action' => 'edit', 
								$commentary['Commentary']['id']
							)
						); ?>
						|
						<?php echo $this->Html->link(
							'Delete',
							array(
								'controller' => 'commentaries', 
								'action' => 'delete', 
								$commentary['Commentary']['id']
							),
							array(),
							'Are you sure that you want to delete this commentary?'
						); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>