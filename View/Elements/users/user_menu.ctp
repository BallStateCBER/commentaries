<?php
	$user_group = $auth_user['Group']['name'];
?>

<div id="user_menu">
	<h3>
		<?php echo $auth_user['name']; ?>
	</h3>	
	<ul class="root">
		<li>
			<ul>
				<?php if ($user_group == 'Newsmedia'): ?>
					<li>
						<?php echo $this->Html->link(
							'Next Article to Publish', 
							array(
								'controller' => 'commentaries', 
								'action' => 'index', 
								'admin' => false, 
								'newsmedia' => true, 
								'plugin' => false
							)
						); ?>
					</li>
					<li>
						<?php echo $this->Html->link(
							'My Account', 
							array(
								'controller' => 'users', 
								'action' => 'my_account', 
								'admin' => false,
								'newsmedia' => true, 
								'plugin' => false
							)
						); ?>
					</li>
					<li>
						<?php echo $this->Html->link(
							'Subscribe Another User to Newsmedia Alerts',
							array(
								'controller' => 'users', 
								'action' => 'add_newsmedia', 
								'admin' => false, 
								'plugin' => false
							)
						); ?>
					</li>
				<?php else: ?>
					<li>
						<?php echo $this->Html->link(
							'My Account', 
							array(
								'controller' => 'users', 
								'action' => 'my_account', 
								'admin' => false, 
								'plugin' => false
							)
						); ?>
					</li>
				<?php endif; ?>
				<li>
					<?php echo $this->Html->link(
						'Log out', 
						array(
							'controller' => 'users', 
							'action' => 'logout', 
							'admin' => false, 
							'plugin' => false
						)
					); ?>
				</li>
			</ul>
		</li>
		
		<?php if ($user_group == 'Administrators' || $user_group == 'Commentary authors'): ?>
			<li>
				Weekly Commentaries
				<ul>
					<li>
						<?php echo $this->Html->link(
							'Add', 
							array(
								'controller' => 'commentaries', 
								'action' => 'add', 
								'admin' => false, 
								'plugin' => false
							)
						); ?>
					</li>
					<li>
						<?php echo $this->Html->link(
							'Drafts', 
							array(
								'controller' => 'commentaries', 
								'action' => 'drafts', 
								'admin' => false, 
								'plugin' => false
							)
						); ?>
					</li>
				</ul>
			</li>
		<?php endif; ?>
		
		<?php if ($user_group == 'Administrators'): ?>
			<li>
				Admin
				<ul>
					<li>
						<?php echo $this->Html->link(
							'Add a User', 
							array(
								'controller' => 'users', 
								'action' => 'add', 
								'admin' => false, 
								'plugin' => false
							)
						); ?>
					</li>
					<li>
						<?php echo $this->Html->link(
							'Edit Users', 
							array(
								'controller' => 'users', 
								'action' => 'index', 
								'admin' => true, 
								'plugin' => false
							)
						); ?>
					</li>
					<li>
						<?php echo $this->Html->link(
							'Add a Newsmedia Member', 
							array(
								'controller' => 'users', 
								'action' => 'add_newsmedia', 
								'admin' => false, 
								'plugin' => false
							)
						); ?>
					</li>
					<li>
						<?php echo $this->Html->link(
							'Next Article to Publish', 
							array(
								'controller' => 'commentaries', 
								'action' => 'index', 
								'admin' => false, 
								'newsmedia' => true, 
								'plugin' => false
							)
						); ?>
					</li>
					<li>
						<a href="/acl_manager/acl">
							Manage Permissions
						</a>
					</li>
					<li>
						<a href="/tags/manage/">
							Manage Tags
						</a>
					</li>
				</ul>
			</li>
		<?php endif; ?>
	</ul>
</div>