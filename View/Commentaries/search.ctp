<div class="search_results">
	<h3>
		Search results: <em><?php echo $searchString ?></em>
	</h3>
	<?php if (empty($commentaries)): ?>
		(none)
	<?php else: ?>
		<?php echo $this->element('commentaries/collection'); ?>
	<?php endif; ?>
</div>