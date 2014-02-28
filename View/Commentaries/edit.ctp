<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>
<?php 
	echo $this->Form->create(
		'Commentary', 
		array(
			'url' => array(
				'controller' => 'commentaries', 
				'action' => 'edit', 
				$commentary_id
			)
		)
	);
	echo $this->Form->input(
		'user_id', 
		array(
			'label' => 'Author', 
			'options' => $authors, 
			'style' => 'width: 400px;'
		)
	);
	echo $this->Form->input(
		'title', 
		array(
			'label' => 'Title', 
			'style' => 'width: 400px;'
		)
	);
	echo $this->Form->input(
		'summary', 
		array(
			'label' => 'Summary', 
			'style' => 'width: 400px;'
		)
	);
	echo $this->Form->input('published_date', array(
		'type' => 'date', 
		'dateFormat' => 'MDY', 
		'label' => 'Date', 
		'minYear' => 2001,
		'maxYear' => date('Y') + 1,
		'class' => 'publishing_or_date_setting'
	));
	echo $this->Form->input(
		'body', 
		array(
			'label' => 'Body', 
			'style' => 'height: 300px; width: 100%;', 
			'between' => '<div class="footnote">ENTER double-spaces. SHIFT + ENTER single-spaces.</div>'
		)
	);
	echo $this->element(
		'tags/editor', 
		compact(
			'available_tags', 
			'selected_tags'
		), 
		array(
			'plugin' => 'DataCenter'
		)
	);
?>
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
			'separator' => '<br />',
			'class' => 'publishing_or_date_setting'
		)
	); ?>
</fieldset>
<?php 
	echo $this->Form->end('Submit');
	echo $this->element(
		'rich_text_editor_init', 
		array(
			'customConfig' => Configure::read('ckeditor_custom_config')
		), 
		array(
			'plugin' => 'DataCenter'
		)
	);
	echo $this->Html->script('admin.js', array('inline' => false));
?>
<?php $this->Js->buffer("
	toggleDelayPublishing();
	$('.publishing_or_date_setting').change(function (event) {
		toggleDelayPublishing();
	});
"); ?>