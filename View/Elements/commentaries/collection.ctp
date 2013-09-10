<?php
$this->Paginator->options['url']['controller'] = 'commentaries';
$this->Paginator->options['url']['action'] = 'browse';
$this->Paginator->options['url']['model'] = 'Commentary';
$this->Paginator->options['model'] = 'Commentary';
$this->Paginator->options['escape'] = false; // Allows the » characters to show up correctly
$this->Paginator->__defaultModel = 'Commentary';
$this->Paginator->options['update'] = 'page_content';
?>

<?php if (isset($commentaries) && ! empty($commentaries)): ?> 
	<?php echo $this->element('paging', array('model' => 'Commentary', 'options' => array('numbers' => true))); ?>
	<table class="commentaries">
		<?php foreach ($commentaries as $commentary): ?>
			<?php if (isset($commentary['Commentary'])) $commentary = $commentary['Commentary']; ?>
			<tr>
				<th>
					<?php echo date('F j, Y', $this->Time->fromString($commentary['published_date'])); ?>
				</th>
				<td>
					<?php echo $this->Html->link(
						'<span class="title">'.$commentary['title'].'</span><span class="summary">'.$commentary['summary'].'</span>',
						array('controller' => 'commentaries', 'action' => 'view', 'id' => $commentary['id'], 'slug' => $commentary['slug']),
						array('escape' => false) 
					); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php echo $this->element('paging', array('model' => 'Commentary', 'options' => array('numbers' => true))); ?>
<?php endif; ?>