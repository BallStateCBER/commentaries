<div id="user_menu">
	<h3>User Menu</h3>	
	<ul class="root">
		<li>
			<?php echo $this->Session->read('Auth.User.name'); ?>
			<ul>
				<li>
					<?php echo $this->Html->link('My Account', array('controller' => 'users', 'action' => 'my_account', 'admin' => false, 'plugin' => false)); ?>
				</li>
				<li>
					<?php echo $this->Html->link('Log out', array('controller' => 'users', 'action' => 'logout', 'admin' => false, 'plugin' => false)); ?>
				</li>
			</ul>
		</li>
		
		<?php if ($user_group == 'Administrators' || $user_group == 'Commentary authors'): ?>
			<li>
				Weekly Commentaries
				<ul>
					<li>
						<?php echo $this->Html->link('Add', array('controller' => 'commentaries', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?>
					</li>
					<li>
						<?php echo $this->Html->link('Drafts', array('controller' => 'commentaries', 'action' => 'drafts', 'admin' => false, 'plugin' => false)); ?>
					</li>
					<li>
						<?php 
							// Development server
							if (stripos($_SERVER['SERVER_NAME'], 'localhost') !== false) {
								$export_url = 'http://icemiller.localhost/articles/import_commentaries';
							// Production server
							} else {
								$export_url = 'http://icemiller.cberdata.org/articles/import_commentaries';	
							}
						
							echo $this->Html->link('Export to Ice Miller', 
								$export_url,
								array('confirm' => 'This will copy any commentaries over to the Ice Miller website that haven\'t been automatically copied upon publishing. You shouldn\'t need to ever do this manually, but you can anyway.\nIf you proceed, the next page will be blank, except for a 1 (indicating success) or a 0 (meaning some catastrophic error just occurred).')
							); 
						?>
					</li>
				</ul>
			</li>
		<?php endif; ?>
		
		<?php if ($user_group == 'Administrators'): ?>
			<li>
				Admin
				<ul>
					<li><?php echo $this->Html->link('Add a User', array('controller' => 'users', 'action' => 'add', 'admin' => false, 'plugin' => false)); ?></li>
					<li><a href="/acl_manager/acl">Manage Permissions</a></li>
					<li><a href="/tags/manage/">Manage Tags</a></li>
				</ul>
			</li>
		<?php endif; ?>
		
		<?php if ($user_group == 'Newsmedia'): ?>
			<li>
				Weekly Commentaries
				<ul>
					<li>
						<?php echo $this->Html->link('Unpublished', array('controller' => 'commentaries', 'action' => 'drafts', 'admin' => false, 'plugin' => false)); ?>
					</li>
				</ul>
			</li>
		<?php endif; ?>
	</ul>
</div>