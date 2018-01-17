<div class="grid_9 alpha omega search-result<?php if($i == 0) echo ' search-top' ?>">
	<div class="grid_2 alpha">
		<div class="search-image">
		<?php
		$image = Configure::read('App.images.series.medium');
	
		if(!empty($result['Series']['image_filename'])) {
			$image = 'series/' . $result['Series']['image_medium_filename'];
		}
	
		echo $this->Html->image($image);
		?>
		</div>
	</div>
	<div class="grid_7 omega">
		<h3><?php echo $this->Html->link($result['Series']['name'], array('controller' => 'series', 'action' => 'view', $result['Series']['slug'])); ?></h3>
		
		<ul class="search-metadata">
			<li class="search-type">
			<?php
			if($result['Series']['is_anime']) {
				echo Inflector::singularize($result['Series']['AnimeType']['name']);
			} else { echo 'Manga'; }
			?>
			</li>
			<li><?php echo $result['Series']['site_count']; ?> sites have this</li>
		</ul>
		
		<p><?php
		if(!empty($result['Series']['synopsis'])) {
			echo $result['Series']['synopsis'];
		}
		?></p>
	</div>
</div>