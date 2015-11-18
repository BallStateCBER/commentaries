<?php
	$this->extend('DataCenter.default');
	$this->assign('sidebar', $this->element('sidebar'));
?>

<?php $this->start('subsite_title'); ?>
	<h1 id="subsite_title" class="max_width">
		<a href="/">
			<img src="/img/Commentary.jpg" />
		</a>
	</h1>
<?php $this->end(); ?>

<?php $this->start('flash_messages'); ?>
    <?php echo $this->element('flash_messages', array(), array('plugin' => 'DataCenter')); ?>
<?php $this->end(); ?>

<div id="content">
	<?php echo $this->fetch('content'); ?>
</div>