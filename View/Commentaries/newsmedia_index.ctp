<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<?php if (empty($commentary)): ?>
	<p class="notification_message">
		The next article to publish has not yet been added. Please check back later.
	</p>
<?php else: ?>
	<?php echo $this->element('commentaries/view_commentary'); ?>
<?php endif; ?>