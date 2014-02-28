<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>
<div id="tag_list">
	<div class="controls">
		<strong>
			View: 
		</strong>
		<a href="#" id="tag_cloud_handle">Cloud</a>
		<?php $this->Js->buffer("
			$('#tag_cloud_handle').click(function(event) {
				event.preventDefault();
				$('#tag_cloud').show();
				$('#tag_list_inner').hide();
			});
		"); ?>
		|
		<a href="#" id="tag_list_handle">List</a>
		<?php $this->Js->buffer("
			$('#tag_list_handle').click(function(event) {
				event.preventDefault();
				$('#tag_cloud').hide();
				$('#tag_list_inner').show();
			});
		"); ?>
	</div>
	
	<div id="tag_cloud" class="tag_cloud">
		<?php foreach ($tagCloud as $key => $tag): ?>
			<?php echo $this->Html->link(
				str_replace(' ', '&nbsp;', $tag['name']),
				array('controller' => 'commentaries', 'action' => 'tagged', 'id' => $tag['id']),
				array(
					'style' => 'font-size: '.ceil(10 + (.5 * ($tag['occurrences']))).'px',
					'title' => $tag['occurrences'].' item'.($tag['occurrences'] > 1 ? 's' : ''),
					'class' => ($key % 2 == 0 ? 'reverse' : ''),
					'escape' => false
				)
			); ?>
		<?php endforeach; ?>
	</div>
	<div id="tag_list_inner" style="display: none;">
		<ul>
			<?php foreach ($tagCloud as $key => $tag): ?>
				<li>
					<?php echo $this->Html->link(
						$tag['name'],
						array('controller' => 'commentaries', 'action' => 'tagged', 'id' => $tag['id']) 
					); ?>
					<span class="count">
						(<?php echo $tag['occurrences']; ?>)
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>