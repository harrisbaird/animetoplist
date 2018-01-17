<div class="featured-series grid_4">
	<a href="<?php echo Router::url(array('controller' => 'series', 'action' => 'anime', $series['Series']['slug'])); ?>">
		<div style="margin-top: -<?php echo !empty($series['Series']['image_position']) ? $series['Series']['image_position'] : 0; ?>%"><?php echo $this->Html->image('series/' . $series['Series']['image_filename'], array('alt' => $series['Series']['name'], 'class' => 'series-image no-lazyload', 'style' => 'width: 300px;')); ?></div>
		<div class="overlay">
			<div class="overlay-top">
				<h2><?php echo !empty($series['Series']['featured_title']) ? $series['Series']['featured_title'] : $series['Series']['name']; ?></h2>
			</div>
			<div class="overlay-bottom">
				<p class="synopsis"><?php echo $series['Series']['synopsis']; ?></p>
				<ul class="footer">
					<li class="type">Anime</li>
					<li class="last"><?php echo $series['Series']['site_count']; ?> sites</li>
				</ul>
			</div>
		</div>
	</a>
</div>
