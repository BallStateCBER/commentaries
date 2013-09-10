<div>
	<h3>
		About
	</h3>
	<p>
		Commentaries are published weekly and distributed through the <em>Indianapolis Business Journal</em> and many other print and online publications.
		<a href="#" id="disclaimer_toggler">Disclaimer</a>
		<?php $this->Js->buffer("
			$('#disclaimer_toggler').click(function(event) {
				event.preventDefault();
				$('#commentary_disclaimer').fadeToggle();
			});
		");  ?>
	</p>
</div>
<div>
	<?php echo $this->Html->link(
		'<img src="/data_center/img/icons/feed.png" /> <span>RSS Feed</span>',
		array('controller' => 'commentaries', 'action' => 'rss', 'ext' => 'rss', 'plugin' => false, 'admin' => false),
		array('escape' => false, 'class' => 'with_icon')
	); ?>
</div>
<div id="commentary_disclaimer" style="display: none;">
	<h3>
		Disclaimer
	</h3>
	<p>
		The views expressed in these commentaries do not reflect those of Ball State University or the Center for Business and Economic Research.
	</p>
</div>
<?php echo $this->element('commentaries/recent'); ?>
<div class="top_tags">
	<h3>
		Top Tags
	</h3>
	<?php echo $this->element('commentaries/top_tags'); ?>
	<div class="browse_all">
		<?php echo $this->Html->link(
			'Browse all tags',
			array('controller' => 'commentaries', 'action' => 'tags', 'admin' => false, 'plugin' => false)
		); ?>
	</div>
</div>
<?php if ($this->Session->read('Auth.User')): ?>
	<div>
		<?php echo $this->element('users/user_menu'); ?>
	</div>
<?php else: ?>
	<div>
		<?php echo $this->Html->link('Admin login', array('controller' => 'users', 'action' => 'login', 'admin' => false, 'plugin' => false)); ?>
	</div>
<?php endif; ?>