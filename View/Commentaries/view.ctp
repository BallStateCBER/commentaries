<?php if ($commentary): ?>
	<?php echo $this->element('commentaries/view_commentary', array(compact('commentary'))); ?>
<?php else: ?>
	<h1 class="page_title">
		Commentary Not Found
	</h1>
	<p>
		Sorry, the commentary that you requested has been moved or deleted. 
		To continue your search, please visit our current collection of
		<?php $this->Html->link( 
			'commentaries by CBER economists',
			array('controller' => 'commentaries', 'action' => 'browse')
		); ?>. 
	</p>
<?php endif; ?>