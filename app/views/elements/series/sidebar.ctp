	<div class="image">
		<?php
		$image = Configure::read('App.images.series.medium');

		if(!empty($series['Series']['image_filename'])) {
			$image = 'series/' . $series['Series']['image_medium_filename'];
		}

		echo $this->Html->image($image, array('alt' => $series['Series']['name']));
		?>
	</div>
	
	<div class="module">
		<?php echo $this->Html->link('History', array('action' => 'revisions', $series['Series']['id']), array('class' => 'more')); ?>
		<h2>Information</h2>
		<ul>
			<?php if(count($series['SeriesTitle']) != 1): ?>
			<li>
				<strong>Also Known As:</strong>
				<?php
				foreach($series['SeriesTitle'] as $title):
					if($series['Series']['name'] != $title['name']) {
						echo $this->Html->tag('p', utf8_encode($title['name']));
					}
				endforeach;
				?>
			</li>
			<?php endif; ?>
			<li>
				<strong>Genres:</strong>
				<p>
					<?php
					$genres = array();

					foreach($series['Genre'] as $genre) {
						$genres[] = $genre['name'];
					}
					if(!empty($series['Genre'])) {
						echo $this->Html->toList($genres, ', ');
					} else {
						echo 'None added';
					}
					?>
				</p>
			</li>
		</ul>
	</div> <!-- /.module -->
