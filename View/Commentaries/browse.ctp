<h1 class="page_title">
	CBER Weekly Commentaries - <?php echo $year; ?>
</h1>

<?php $years = range($latestYear, $earliestYear); ?>

<ul class="commentary_years">
	<li>
		Select a year &rarr;
	</li>
	<?php foreach ($years as $y): ?>
		<li<?php if ($y == $year): ?> class="selected"<?php endif; ?>>
			<?php echo $this->Html->link(
				$y,
				array('controller' => 'commentaries', 'action' => 'browse', $y)
			); ?>
		</li>
	<?php endforeach; ?>
</ul>

<?php if (isset($commentaries) && ! empty($commentaries)): ?> 
	<table class="commentaries">
		<?php foreach ($commentaries as $commentary): ?>
			<?php if (isset($commentary['Commentary'])) $commentary = $commentary['Commentary']; ?>
			<tr>
				<th>
					<?php echo date('F j, Y', $this->Time->fromString($commentary['published_date'])); ?>
				</th>
				<td>
					<?php 
						echo $this->Html->link(
							'<span class="title">'.$commentary['title'].'</span><span class="summary">'.$commentary['summary'].'</span>',
							array(
								'controller' => 'commentaries', 
								'action' => 'view', 
								'id' => $commentary['id'],
								'slug' => $commentary['slug']
							),
							array('escape' => false) 
						); 
					
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>