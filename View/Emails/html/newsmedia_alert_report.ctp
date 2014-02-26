<p>
	Mike Hicks Weekly Commentary Administrator,
</p>

<p>
	Here are the results of the newsmedia alert process that just completed:
</p>

<?php if (empty($results)): ?>
	<p>
		Something's wrong. No output messages were generated. Uh... here's a dump of the session, in case it helps:
	</p>
	
	<pre><?php print_r($this->Session->read()); ?></pre>
<?php else: ?>
	<ul>
		<?php foreach ($results as $result): ?>
			<li>
				<?php echo $result['message']; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>