<?php $this->Html->setHeader('Editing ' . $series['Series']['name']); ?>
<?php
$helpData = array(
	'class' => 'sidebar-edit',
	'items' => array(
		array(
			'header' => 'Synopsis',
			'hoverClass' => '#SeriesSynopsis',
			'location' => '0',
			'contents' => array(
				'This is not a review - Try to be as neutral as possible.',
				'Describe the ' . $typeText . ' as a whole, not just a certain ' . ife($typeText == 'Anime', 'episode', 'chapter') . '.'
			)
		),
		array(
			'header' => 'Synopsis source URL',
			'hoverClass' => '#SeriesSynopsisSource',
			'location' => '250',
			'contents' => 'If you are copying this from elsewhere, be sure to enter the url where you copied it from.'
		),
		array(
			'header' => 'Genres',
			'hoverClass' => '#SeriesGenre',
			'location' => '305',
			'contents' => 'Select the genres which describe this ' . $typeText . ' as a whole, not including any minor sub-plots.'
		),
		array(
			'header' => 'Upload a new image',
			'hoverClass' => '#SeriesImage',
			'location' => '520',
			'contents' => array(
				'Official art only, no fanart please.',
				'<strong>No nudity</strong> of any kind, even if the ' . $typeText . ' contains adult content.'
			)
		)
	)
);

echo $this->element('global/form_help', array('data' => $helpData));
$this->Html->hasSidebar = true;
?>

<div id="container-small">

<?php
	echo $this->Form->create('Series', array('url' => array($id, '1'), 'type' => 'file'));
	echo $this->Form->hidden('type');

	echo $this->Form->input('synopsis');
	echo $this->Form->input('synopsis_source', array('label' => 'Synopsis source URL <em>(leave blank for none)</em>'));
?>
	
<label>Genres <em>(select up to 10 genres)</em></label>
<?php
	echo $this->Form->input('Genre', array('multiple' => 'checkbox', 'label' => false, 'div' => array('id' => 'SeriesGenre')));
	$imageLabel = !empty($imageUpload) ? '<br />' . $imageUpload['name'] . ' was successfully uploaded' : '';
	echo $this->Form->input('image', array('type' => 'file', 'label' => 'Upload a new image' . $imageLabel));
	echo $this->Form->input('reason', array('label' => 'Reason <em>(what changes did you make)</em>'));
?>

<div id="spam-warning">Spam / Vandalization is not tolerated and will result in you being banned and all changes rolled back.</div>

<div class="submit">
	<?php echo $this->element('buttons/button_submit', array('text' => 'Save changes')); ?>
	<?php echo $this->element('buttons/button_submit', array('text' => 'Cancel', 'url' => array('action' => 'view', $series['Series']['slug']), 'type' => 'link', 'class' => 'grey')); ?>
</div>

<?php echo $this->Form->end();?>

<script type="text/javascript">
	
</script>

<?php $this->Html->script('autocolumn.js', array('inline' => false)); ?>
<?php $this->Html->scriptStart(array('safe' => false, 'inline' => false)); ?>
	$(function() {
		$('#SeriesGenre').columnize({columns: 3});
	});
<?php $this->Html->scriptEnd(); ?>