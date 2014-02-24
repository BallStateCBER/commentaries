<div id="add_newsmedia">
	<h1 class="page_title">
		<?php echo $title_for_layout; ?>
	</h1>
	
	<?php if (isset($auth_user['Group']['name']) && $auth_user['Group']['name'] == 'Newsmedia'): ?>
		<p>
			Planning to be out of the office?  Sign up a coworker to be notified when upcoming commentaries are available for publication by your news organization.   Once signed up, your colleague will receive an email explaining that he/she will be notified when new Weekly Commentaries by Michael Hicks are available.
		</p>
		<p>
			This email will also include information for how to update contact information, unsubscribe, and/or add fellow members of your news organization to the notification list.
		</p>
	<?php else: ?>
		<p>
			When you subscribe members of the newsmedia to the Weekly Commentary newsmedia alert service, 
			they will immediately receive an introductory email. This will explain that they will begin 
			receiving email alerts whenever upcoming commentaries are available.
		</p>
		<p>
			The introductory email will contain login information in case the subscriber wants to change 
			his or her contact information, stop receiving emails, or add other members of the newsmedia 
			to this	service.
		</p>
	<?php endif; ?>
	
	<?php
		echo $this->Form->create('User');
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('password', array(
			'type' => 'text',
			'required' => true
		));
		if (isset($next_commentary) && ! empty($next_commentary)) {
			$article_title = $next_commentary['Commentary']['title'];
			$date = date('l, F jS', strtotime($next_commentary['Commentary']['published_date']));
			$url = Router::url(array(
				'controller' => 'commentaries', 
				'action' => 'view', 
				'id' => $next_commentary['Commentary']['id'],
				'slug' => $next_commentary['Commentary']['slug']
			));
			echo $this->Form->input('send_alert', array(
				'type' => 'checkbox',
				'label' => "Immediately send this reporter an alert for the article <a href=\"$url\">\"$article_title\"</a>, due to be published on $date"
			));
		}
		echo $this->Form->end('Add');
	?>
</div>