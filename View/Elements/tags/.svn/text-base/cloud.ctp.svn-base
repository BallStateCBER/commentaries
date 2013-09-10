<style>
	.tag_cloud2 {font-size: 25px; line-height:13px; text-align: center;}
	.tag_cloud2 a {vertical-align: middle;}
</style>
<div class="tag_cloud2" style="">
	<?php
		$tag_cloud = $this->requestAction('tags/cloud');
		$i = 0; 
		foreach ($tag_cloud as $tag_name => $tag_info):
			$i++;
	?>
		<?php $font_size = ceil($tag_info['size_percent'] * 100); ?>
		<?php if ($font_size > 10): ?>
			<a 
				href="/tags/view/<?php echo $tag_info['id']; ?>" 
				style="font-size: <?php echo $font_size; ?>%" 
				title="<?php echo $tag_info['count'] ?> item<?php if ($tag_info['count'] > 1) echo 's';?>"
				<?php if ($i % 2 == 0): ?>class="reverse"<?php endif; ?>
			><?php echo $tag_name;	?></a>
		<?php endif; ?>
	<?php endforeach; ?>
</div>