<?php
if (!isset($this->Paginator->params['paging'])) {
	return;
}
if (!isset($model) || $this->Paginator->params['paging'][$model]['pageCount'] < 2) {
	return;
}
if (!isset($options)) {
	$options = array();
}
 
$options['model'] = $model;
$options['url']['model'] = $model;
$this->Paginator->__defaultModel = $model;
$format_string = 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%';
?>

<?php if (isset($options['div'])): ?>
	<?php echo $options['div'] ?>
<?php else: ?>
	<div class="paginator">
<?php endif; ?>
	<?php echo $this->Paginator->prev($this->Html->image('/data_center/img/icons/arrow-180-medium.png', array('title' => 'Previous')), array_merge(array('escape' => false, 'class' => 'prev'), $options), null); ?>
	<?php if (isset($options['numbers'])): ?>
		<?php echo $this->Paginator->numbers(am($options, array('before' => false, 'after' => false, 'separator' => ' | '))); ?>
	<?php endif; ?>
	
	<?php echo $this->Paginator->next($this->Html->image('/data_center/img/icons/arrow-000-medium.png', array('title' => 'Next')), array_merge(array('escape' => false, 'class' => 'next'), $options), null); ?>
	
	<?php if (isset($options['counter'])): ?>
		<?php echo $this->Paginator->counter(array('format' => $format_string)); ?>
	<?php endif; ?>
<?php if (! isset($options['div']) || $options['div'] != ''): ?>
	</div>
<?php endif; ?>