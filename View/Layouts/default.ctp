<?php $this->extend('DataCenter.default'); ?>
<?php $this->assign('sidebar', $this->element('sidebar')); ?>
<?php $this->start('subsite_title'); ?>
	<h1 id="subsite_title" class="max_width">
		<a href="/">
			<img src="/img/WkCommentary.png" />
		</a>
	</h1>
<?php $this->end(); ?>

<?php if ($user_group == 'Newsmedia'): ?>
	<?php echo $this->element('newsmedia/alert'); ?>
<?php endif; ?>

<div id="content">
	<?php echo $this->fetch('content'); ?>
</div>