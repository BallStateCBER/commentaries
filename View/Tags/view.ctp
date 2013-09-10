<div class="tags view">
<h2><?php  echo __('Tag');?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($tag['Tag']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($tag['Tag']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Parent Tag'); ?></dt>
		<dd>
			<?php echo $this->Html->link($tag['ParentTag']['name'], array('controller' => 'tags', 'action' => 'view', $tag['ParentTag']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Lft'); ?></dt>
		<dd>
			<?php echo h($tag['Tag']['lft']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Rght'); ?></dt>
		<dd>
			<?php echo h($tag['Tag']['rght']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Selectable'); ?></dt>
		<dd>
			<?php echo h($tag['Tag']['selectable']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($tag['Tag']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($tag['Tag']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Tag'), array('action' => 'edit', $tag['Tag']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Tag'), array('action' => 'delete', $tag['Tag']['id']), null, __('Are you sure you want to delete # %s?', $tag['Tag']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Tags'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Tag'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tags'), array('controller' => 'tags', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Parent Tag'), array('controller' => 'tags', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Commentaries'), array('controller' => 'commentaries', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Commentary'), array('controller' => 'commentaries', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Tags');?></h3>
	<?php if (!empty($tag['ChildTag'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Parent Id'); ?></th>
		<th><?php echo __('Lft'); ?></th>
		<th><?php echo __('Rght'); ?></th>
		<th><?php echo __('Selectable'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($tag['ChildTag'] as $childTag): ?>
		<tr>
			<td><?php echo $childTag['id'];?></td>
			<td><?php echo $childTag['name'];?></td>
			<td><?php echo $childTag['parent_id'];?></td>
			<td><?php echo $childTag['lft'];?></td>
			<td><?php echo $childTag['rght'];?></td>
			<td><?php echo $childTag['selectable'];?></td>
			<td><?php echo $childTag['created'];?></td>
			<td><?php echo $childTag['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'tags', 'action' => 'view', $childTag['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'tags', 'action' => 'edit', $childTag['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'tags', 'action' => 'delete', $childTag['id']), null, __('Are you sure you want to delete # %s?', $childTag['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Child Tag'), array('controller' => 'tags', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php echo __('Related Commentaries');?></h3>
	<?php if (!empty($tag['Commentary'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Title'); ?></th>
		<th><?php echo __('Summary'); ?></th>
		<th><?php echo __('Body'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Is Published'); ?></th>
		<th><?php echo __('Delay Publishing'); ?></th>
		<th><?php echo __('Published Date'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($tag['Commentary'] as $commentary): ?>
		<tr>
			<td><?php echo $commentary['id'];?></td>
			<td><?php echo $commentary['title'];?></td>
			<td><?php echo $commentary['summary'];?></td>
			<td><?php echo $commentary['body'];?></td>
			<td><?php echo $commentary['user_id'];?></td>
			<td><?php echo $commentary['is_published'];?></td>
			<td><?php echo $commentary['delay_publishing'];?></td>
			<td><?php echo $commentary['published_date'];?></td>
			<td><?php echo $commentary['created'];?></td>
			<td><?php echo $commentary['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'commentaries', 'action' => 'view', $commentary['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'commentaries', 'action' => 'edit', $commentary['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'commentaries', 'action' => 'delete', $commentary['id']), null, __('Are you sure you want to delete # %s?', $commentary['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Commentary'), array('controller' => 'commentaries', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
