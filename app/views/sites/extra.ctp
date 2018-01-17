<?php $this->Html->setHeader('Anime and Manga'); ?>

<p>Does your site allow the you to either watch streaming Anime or read manga online? If so, enter the URLs below to be included in the Anime and Manga lists.</p>

<p><strong>Warning</strong> - If you fill in the boxes and your site doesn't have either, your site will be <span class="warning">automatically penalized</span> and possibly removed from Anime Toplist.</p>

<?php
$helpData = array(
	'class' => 'sidebar-edit',
	'items' => array(
		array(
			'header' => 'Streaming Anime URL',
			'hoverClass' => '#SiteStreamingUrl',
			'location' => '0',
			'contents' => array(
				'Enter the URL of the page which contains your streaming Anime list.',
				'If you don\'t have one, enter the url of your frontpage'
			)
		),
		array(
			'header' => 'Manga URL',
			'hoverClass' => '#SiteMangaUrl',
			'location' => '0',
			'contents' => array(
				'Enter the URL of the page which contains your Manga list.',
				'If you don\'t have one, enter the url of your frontpage'
			)
		)
	)
);

echo $this->element('global/form_help', array('data' => $helpData));
?>

<?php echo $this->Form->create('Site', array('url' => $this->passedArgs)); ?>
<?php
	echo $this->Form->input('Site.streaming_url', array('label' => 'Streaming Anime URL', 'type' => 'text'));
	echo $this->Form->input('Site.manga_url', array('label' => 'Manga URL', 'type' => 'text'));
	echo $this->Form->hidden('Site.step', array('value' => 'extra'));
?>

<div class="submit">
	<?php echo $this->element('buttons/button_submit', array('text' => 'Continue')); ?>
</div>

<?php echo $this->Form->end();?>
