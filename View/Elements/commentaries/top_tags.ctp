<table>
	<?php foreach ($top_tags as $key => $tag): ?>
		<tr>
			<th>
				<?php echo $this->Html->link(
					$tag['tags']['name'],
					array(
						'controller' => 'commentaries', 
						'action' => 'tagged', 
						'id' => $tag['commentaries_tags']['tag_id'], 
						'admin' => false, 
						'plugin' => false
					) 
				); ?>
			</th>
			<td>
				<?php echo $tag[0]['occurrences']; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>