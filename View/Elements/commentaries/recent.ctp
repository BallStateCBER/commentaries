<?php if (empty($recent_commentaries)): ?>
	<!-- Error: No recent commentaries found -->
<?php else: ?>
	<div class="recent_commentaries">
		<h3>
			Recent
		</h3>
		<?php foreach ($recent_commentaries as $commentary): ?>
			<p>
				<?php echo $this->Html->link(
					'<span class="title">'.$commentary['Commentary']['title'].'</span><span class="summary">'.$commentary['Commentary']['summary'].'</span>',
					array('controller' => 'commentaries', 'action' => 'view', 'id' => $commentary['Commentary']['id'], 'slug' => $commentary['Commentary']['slug'], 'admin' => false, 'plugin' => false),
					array('escape' => false) 
				); ?>
			</p>
		<?php endforeach; ?>
		<?php echo $this->Html->link(
			'View archives',
			array('controller' => 'commentaries', 'action' => 'browse', 'admin' => false, 'plugin' => false),
			array('class' => 'more')
		); ?>
	</div>
<?php endif; ?>
