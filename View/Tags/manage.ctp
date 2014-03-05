<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<div id="tag_management_tabs">
	<ul>
		<li><a href="#tab-arrange">Arrange</a></li>
		<li><a href="#tab-add">Add</a></li>
		<li><a href="#tab-remove">Remove</a></li>
		<li><a href="#tab-edit">Rename</a></li>
		<li><a href="#tab-merge">Merge</a></li>
		<li><a href="#tab-fix">Fix</a></li>
	</ul>
	<div id="tab-arrange">
		<div id="tree-div" style="height: 400px; overflow: auto;"></div>
		<ul>
			<li>
				PROTIP: Move a tag by clicking to the right of it and dragging
				it to the right of another tag, rather than clicking on its name and
				dragging it on top of another tag's name. For some reason, this 
				is the only way to do it when root-level tags are involved.
			</li>
		</ul>
	</div>
	
	<div id="tab-add">
		<?php echo $this->Form->create('Tag', array('url' => array('controller' => 'tags', 'action' => 'add'))); ?>
		<strong>Tag</strong>(s)<br />
		Multiple tags go on separate lines. Child-tags can be indented under parent-tags with one hyphen or tab per level. Example:
	<pre style="background-color: #eee; font-size: 80%; margin-left: 20px; width: 200px;">Fruits
-Apples
--Granny Smith
--Red Delicious
-Nanners
Vegetables
-Taters</pre>
		<?php echo $this->Form->input('name', array('type' => 'textarea', 'label' => false, 'style' => 'width: 100%;')); ?>
		<?php echo $this->Form->input('parent_name', array('label' => 'Parent Tag (optional)', 'type' => 'text', 'class' => 'search_field')); ?>
		<?php echo $this->Form->end('Add'); ?>
		
		<div id="add_results"></div>
	</div>
	
	<div id="tab-remove">
		<p class="notification_message">
			Warning: If a tag is removed, all child-tags will also be removed. This cannot be undone.
		</p>
		<p>
			Start typing a tag name:
		</p>
		<form id="tag_remove_form">
			<input type="text" id="tag_remove_field" class="search_field" />
			<input type="submit" value="Remove" />
		</form>
		<div class="results"></div>
	</div>
	
	<div id="tab-edit">
		<p>
			Start typing a tag name:
		</p>
		<div>
			<form id="tag_edit_search_form">
				<input type="text" class="search_field" />
				<br />
				<input type="submit" value="Rename this tag" />
			</form>
		</div>
		<div class="results" id="edit_results"></div>
	</div>
	
	<div id="tab-merge">
		<p>
			Start typing tag names:
		</p>
		<form id="tag_merge_form">
			Merge 
			<input type="text" id="tag_merge_from_field" class="search_field"/>
			into
			<input type="text" id="tag_merge_into_field" class="search_field"/>
			
			<span class="footnote">(The first tag will be <strong>removed</strong>.)</span>
			<br />
			<input type="submit" value="Merge" />
		</form>
		<div class="results" id="merge_results"></div>
	</div>
	
	<div id="tab-fix">
		<p>
			These functions are safe to use at any time, and should be used to fix relevant problems
			that come up.
		</p>
		<ul>
			<li>
				<?php echo $this->Html->link('Recover tag tree', array('controller' => 'tags', 'action' => 'recover')); ?>
				<br />If the tree structure in the database (lft and rght fields) has gotten screwed up
			</li>
			<li>
				<?php echo $this->Html->link('Remove broken associations', array('controller' => 'tags', 'action' => 'remove_broken_associations')); ?>
				<br />Associations in the commentaries_tags table involving either nonexistent tags or commentaries.
			</li>
		</ul>
		<div class="results"></div>
	</div>
</div>

<h2>Notes:</h2>
<ul>
	<li>
		Working with any tag that has a slash (/) in its name (and possibly other punctuation) may cause errors.
		This is because the <a href="http://httpd.apache.org/docs/2.2/mod/core.html#allowencodedslashes">AllowEncodedSlashes directive</a> in Apache creates a 404 error when an
		encoded slash (%2F) is in a URL, e.g. when the name of such a tag is included in a URL by
		an AJAX request.
	</li>
</ul>

<?php 
	echo $this->Html->css('/ext-2.0.1/resources/css/ext-custom.css', null, array('inline' => false));
	echo $this->Html->script('/ext-2.0.1/ext-custom.js', array('inline' => false));
	echo $this->Html->css('/jquery_ui/css/smoothness/jquery-ui-1.10.4.custom.min.css', null, array('inline' => false));
	echo $this->Html->script('/jquery_ui/js/jquery-ui-1.10.4.custom.js', array('inline' => false));
	
	echo $this->Html->script('jquery.form.js', array('inline' => false));
	echo $this->Html->script('admin.js', array('inline' => false));
	$this->Js->buffer("
		setupTagManager();
	");
?>