<?php if (isset($unpublished) && ! empty($unpublished)): ?>
	<div id="newsmedia_alert">
		<div class="notification_message">
			<?php if (count($unpublished) == 1): ?>
				Unpublished commentary:
				<?php 
					foreach ($unpublished as $commentary_id => $commentary_title) {
						echo $this->Html->link(
							$commentary_title,
							array(
								'controller' => 'commentaries',
								'action' => 'view',
								'id' => $commentary_id
							)
						);
					}
				?>
			<?php else: ?>
				Unpublished commentaries:
				<ul>
					<?php foreach ($unpublished as $commentary_id => $commentary_title): ?>
						<li>
							<?php echo $this->Html->link(
								$commentary_title,
								array(
									'controller' => 'commentaries',
									'action' => 'view',
									'id' => $commentary_id
								)
							); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>