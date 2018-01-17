<?php
echo $this->Form->create('SeriesReview', array(
	'url' => array('controller' => 'series', 'action' => 'addReview', $series['Series']['slug']),
	'inputDefaults' => array(
		'label' => false,
	)
));
?>
<div id="review-rating">
	<p><strong>How do you rate this Anime?</strong>
	Drag the bars to set the rating. 10 = Excellent, 1 = Poor.</p>

	<div class="grid_3 alpha" name="story">
		<h4>Story</h4>
		<div class="slider"></div>
		<div class="amount">n/a</div>
	</div>
	<div class="grid_3 omega" name="characters">
		<h4>Characters</h4>
		<div class="slider"></div>
		<div class="amount">n/a</div>
	</div>
	<div class="grid_3 alpha" name="animation">
		<h4>Animation</h4>
		<div class="slider"></div>
		<div class="amount"><h4>n/a</h4></div>
	</div>
	<div class="grid_3 omega" name="sound">
		<h4>Sound &amp; Music</h4>
		<div class="slider"></div>
		<div class="amount">n/a</div>
	</div>
	
	<div class="grid_3 alpha overall">
		<h4>Overall</h4>
		<div class="slider-overall"></div>
		<div class="amount">n/a</div>
	</div>
</div>

<strong>Enter your review:</strong>

<?php
echo $this->Form->hidden('story', array('value' => 0, 'id' => 'review_story'));
echo $this->Form->hidden('characters', array('value' => 0, 'id' => 'review_characters'));
echo $this->Form->hidden('animation', array('value' => 0, 'id' => 'review_animation'));
echo $this->Form->hidden('sound', array('value' => 0, 'id' => 'review_sound'));
echo $this->Form->textarea('body');
echo $this->Form->end('Add review');
?>