<?php
$this->Html->setHeader('History');

$this->Diff->renderer = 'inline';
$revisions = $this->Diff->process($revisions);
?>

<?php if(empty($revisions)): ?>

<p>This series doesn't have any revisions</p>

<?php endif; ?>

<?php foreach($revisions as $num => $revision): ?>

<?php
	//Should this revision be open by default
	$openClass = $num < $open ? 'open' : '';

	//Rollbacks have their own class
	$revisionClass = $revision['Revision']['is_rollback'] ? 'rollback' : '';
?>

<div class="revision <?php echo $openClass . ' ' . $revisionClass ?>">
	<div class="header grid_9 alpha omega">

		<div class="grid_1 alpha toggle">
			<span class="arrow"></span>
			<h3><?php echo $revision['Revision']['revision_number']; ?></h3>
		</div>

		<div class="details grid_8 omega">
			<ul class="links">
				<?php if(!$revision['Revision']['is_rollback'] && $revision['Revision']['revision_number'] != 1): ?>
				<li><?php echo $this->Html->link('Rollback', array('action' => 'rollback', $id, $revision['Revision']['revision_number'])); ?></li>
				<?php endif; ?>
			</ul>
			<span class="reason"><?php echo $revision['Revision']['content']['Series']['reason']; ?></span>
		</div>
	</div>

	<div class="contents grid_9 alpha omega">
		<div>
			<?php
			if(!empty($revision['Revision']['content']['Series']['synopsis'])) {
				echo $this->Html->tag('h3', 'Synopsis');
				
				echo $this->Html->para(null, $revision['Revision']['content']['Series']['synopsis']);
			}

			$genreDiff = $this->Diff->compareGenres($revisions, $num);
			if(!empty($genreDiff)) {
				echo $this->Html->tag('h3', 'Genres');

				$genreList = array();
				foreach($genreDiff as $genre => $status) {
					$genreList[] = $this->Html->link($this->Html->tag('span', $genres[$genre], array('class' => $status)), '', array('class' => 'post-tag', 'escape' => false));
				}
				
				echo $this->Html->toList($genreList, ', ');
			}
			?>
			
			<?php if(!empty($revision['Revision']['content']['Series']['new_image'])) {
				echo $this->Html->tag('h3', 'Image');
				echo $this->Html->image('series/' . $revision['Revision']['content']['Series']['image_filename']);
			}
			?>
		</div>
	</div>

</div>

<?php endforeach; ?>