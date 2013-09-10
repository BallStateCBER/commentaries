<div class="profile_box">
	<div class="picture">
		<?php if ($user['picture']): ?>
			<img src="/img/users/<?php echo $user['picture']; ?>" />
		<?php endif; ?> 
	</div>
	<p class="about">
		About the Author
	</p>
	<h2 class="name">
		<?php echo $user['name'] ?>
		<a href="mailto:<?php echo $user['email']; ?>" class="email">
			<?php echo $user['email']; ?>
		</a>
	</h2>
	
	<div class="bio">
		<?php echo $user['bio']; ?>
	</div>
</div>