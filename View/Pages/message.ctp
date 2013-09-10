<?php if (isset($title_for_layout)): ?>
	<h1 class="page_title">
		<?php echo $title_for_layout; ?>
	</h1>
<?php endif; ?>

<?php if (isset($message) && $message): ?>
	<p class="<?php if (isset($class) && $class) echo $class.'_message'; ?>">
		<?php echo $message; ?>
	</p>
<?php endif; ?>

<?php if (isset($back) && $back): ?>
	<?php echo $this->Html->link('&larr; Back', $back, array('escape' => false)); ?>
<?php endif; ?>