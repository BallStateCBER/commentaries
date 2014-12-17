<?php
if (! isset($newest)) $newest = false;
?>

<p>
	<strong>
		To accommodate holiday closings, the commentary will be made available to newsmedia on Monday, December 22<sup>nd</sup> and Monday, December 29<sup>th</sup>.
	</strong>
</p>

<div class="commentary">
	<div class="header">
		<?php if ($this->Session->check('Auth.User.id')): ?>
			<div class="controls">
				<?php if ($acl->check(array('User' => $auth_user), 'controllers/commentaries/edit')): ?>
					<?php echo $this->Html->link(
						$this->Html->image('/data_center/img/icons/pencil.png').'Edit',
						array(
							'controller' => 'commentaries',
							'action' => 'edit',
							$commentary['Commentary']['id'],
							'admin' => false,
							'newsmedia' => false
						),
						array('escape' => false)
					); ?>
				<?php endif; ?>
				<?php if ($acl->check(array('User' => $auth_user), 'controllers/commentaries/delete')): ?>
					&nbsp; <?php echo $this->Html->link(
						$this->Html->image('/data_center/img/icons/cross.png').'Delete',
						array(
							'controller' => 'commentaries',
							'action' => 'delete',
							$commentary['Commentary']['id'],
							'admin' => false,
							'newsmedia' => false
						),
						array('escape' => false),
						'Are you sure that you want to delete this commentary?'
					); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<p class="time_posted">
			<?php echo date('F j, Y', $this->Time->fromString($commentary['Commentary']['published_date'])); ?>
			<?php if ($newest): ?>
				&nbsp;&nbsp;| &nbsp;&nbsp;Latest Commentary
			<?php endif; ?>
		</p>
		<h3 class="title">
			<?php echo $this->Html->link($commentary['Commentary']['title'], array(
				'controller' => 'commentaries',
				'action' => 'view',
				'id' => $commentary['Commentary']['id'],
				'slug' =>  $commentary['Commentary']['slug'],
				'admin' => false,
				'newsmedia' => false
			)); ?>
		</h3>
		<h4 class="summary">
			<?php echo $commentary['Commentary']['summary']; ?>
		</h4>
	</div>
	<div class="body">
		<?php echo $this->Text->autoLink($commentary['Commentary']['body'], array('escape' => false)); ?>
	</div>
	<div class="footer">
		<p class="link">
			<?php
				$permalink = Router::url(array(
					'controller' => 'commentaries',
					'action' => 'view',
					'id' => $commentary['Commentary']['id'],
					'slug' => $commentary['Commentary']['slug'],
					'admin' => false,
					'newsmedia' => false
				), true);
			?>
			Link to this commentary: <?php echo $this->Html->link($permalink, $permalink); ?>
		</p>
		<?php if (! empty($commentary['Tag'])): ?>
			<p class="tags">
				<strong>Tags:</strong>
				<?php
					$linked_tags = array();
					foreach ($commentary['Tag'] as $key => $tag) {
						$linked_tags[] = $this->Html->link($tag['name'], array(
							'controller' => 'commentaries',
							'action' => 'tagged',
							'id' => $tag['id'],
							'admin' => false,
							'newsmedia' => false
						));
					}
					echo implode(', ', $linked_tags);
				?>
			</p>
		<?php endif; ?>
	</div>
	<?php if (isset($commentary['User']) && ! empty($commentary['User']['name'])): ?>
		<hr />
		<?php echo $this->element('users/profile', array('user' => $commentary['User'])) ?>
	<?php endif; ?>
</div>