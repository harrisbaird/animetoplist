<div class="grid_9 alpha omega search-result<?php if($i == 0) echo ' search-top' ?>">
	<div class="grid_2 alpha">
		<div class="search-image">
		<?php
		$image = Configure::read('App.images.sites.small');
	
		if(!empty($result['Site']['has_banner'])) {
			$image = 'sites/' . $result['Site']['id'] . '_80.jpg';
		}
	
		echo $this->Html->image($image);
		?>
		</div>
	</div>
	<div class="grid_7 omega">
		<h3><?php echo $this->Html->link($result['Site']['official_name'], array('controller' => 'sites', 'action' => 'view', $result['Site']['slug'])); ?></h3>
		
		<ul class="search-metadata">
			<li class="search-type">Site</li>
			
			<li class="comments"><span></span><?php
			$count = @$result['Site']['Comment'][0]['Comment'][0]['count'];
			echo !empty($count) ? $count : 0;
			?> comments</li>
			
			<li class="url"><?php echo $result['Site']['url']; ?></li>
		</ul>
		
		<p><?php echo $result['Site']['description']; ?></p>
	</div>
</div>