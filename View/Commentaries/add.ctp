<h1 class="page_title">Submit a Commentary</h1>
<?php echo $this->Form->create('Commentary', array('url' => array('controller' => 'commentaries', 'action' => 'add'))); ?>
<?php echo $this->Form->input('user_id', array('label' => 'Author', 'options' => $authors, 'style' => 'width: 400px;')); ?>
<?php echo $this->Form->input('title', array('label' => 'Title', 'style' => 'width: 400px;')); ?>
<?php echo $this->Form->input('summary', array('label' => 'Summary', 'style' => 'width: 400px;')); ?>
<?php echo $this->Form->input('published_date', array(
	'type' => 'date', 
	'dateFormat' => 'MDY', 
	'label' => 'Date', 
	'minYear' => 2001,
	'maxYear' => date('Y') + 1
)); ?>
<?php echo $this->Form->input('body', array('label' => 'Body', 'style' => 'height: 300px; width: 100%;', 'between' => '<div class="footnote">ENTER double-spaces. SHIFT + ENTER single-spaces.</div>')); ?>
<?php echo $this->element('tags/editor', compact('available_tags', 'selected_tags'), array('plugin' => 'DataCenter')); ?>
<fieldset>
	<legend>Publishing</legend>
	<?php echo $this->Form->radio(
		'is_published', 
		array(
			1 => ' Publish <span id="delayed_publishing_date"></span>', 
			0 => ' Save as Draft'
		), 
		array(
			'value' => 1, 
			'legend' => false, 
			'separator' => '<br />'
		)
	); ?>
</fieldset>
<?php echo $this->Form->end('Submit'); ?>
<?php echo $this->element('rich_text_editor_init', array(), array('plugin' => 'DataCenter')); ?>

<?php 
	echo $this->Html->script('admin.js', array('inline' => false));
	$this->Js->buffer("
		toggleDelayPublishing();
		$('#CommentaryPublishedDateMonth').change(function() {toggleDelayPublishing()});
		$('#CommentaryPublishedDateDay').change(function() {toggleDelayPublishing()});
		$('#CommentaryPublishedDateYear').change(function() {toggleDelayPublishing()});
		$('#CommentaryIsPublished1').change(function() {toggleDelayPublishing()});
		$('#CommentaryIsPublished0').change(function() {toggleDelayPublishing()});
	");
?>