<ul>
<?php
foreach($data as $series):
$type = $series['Series']['is_anime'] == 1 ? 'anime' : 'manga';
?>
	<li>
		<a href="<?php echo Router::url(array('controller' => 'series', 'action' => $type, $series['Series']['slug'])); ?>">
			<?php
			$icon = '/img/' . Configure::read('App.images.series.small');
			
			if(!empty($series['Series']['image_small_filename'])) {
				$icon = '/img/series/' . $series['Series']['image_small_filename'];
			}			
			?>
			<div class="icon" style="background-image: url(<?php echo $icon; ?>)"></div>
			<span>
				<strong><?php echo $series['Series']['name']; ?></strong>
				<em><?php echo $series['Series']['site_count']; ?> Sites</em>
			</span>
		</a>
	</li>
<?php endforeach; ?>
</ul>