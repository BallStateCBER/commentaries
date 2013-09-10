<div class="tagged">
	<h1 class="page_title">
		Commentaries tagged with <em><?php echo $tagName ?></em>
	</h1>
	<?php if (empty($commentaries)): ?>
		(None found)
	<?php else: ?>
		<?php echo $this->element('commentaries/collection'); ?>
	<?php endif; ?>
</div>