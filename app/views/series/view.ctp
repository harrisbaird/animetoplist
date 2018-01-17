<?php
$this->Html->setHeader($series['Series']['name']);

error_reporting(0);

if($series['Series']['is_anime']) {
	$seriesType = 'anime';
	$outAction = 'watch';
	$tableHeader = 'Streaming Anime';
	$tableDescription = sprintf('The following sites contain <strong>Streaming Anime</strong> and will allow you to <strong>watch %s</strong>.', $series['Series']['name']);
	$this->Html->setTitle('Watch streaming ' . $series['Series']['name'] . ' online - ' . $series['Series']['name'] . ' Anime stream');
	$this->Html->pageDescription = 'The following sites contain Streaming Anime and will allow you to watch ' . $series['Series']['name'] . ' online.';
} else {
	$seriesType = 'manga';
	$outAction = 'read';
	$tableHeader = 'Read Manga';
	$tableDescription = sprintf('The following sites contain <strong>read Manga online</strong> and will allow you to <strong>read %s</strong>.', $series['Series']['name']);
	$this->Html->setTitle('Read ' . $series['Series']['name'] . ' online');
	$this->Html->pageDescription = 'The following sites contain online manga and will allow you to read ' . $series['Series']['name'] . ' online.';
}
?>

                <?php
                $image = Configure::read('App.images.series.medium');
        
                if(!empty($series['Series']['image_filename'])) {
                        $image = 'series/' . $series['Series']['image_medium_filename'];
                }
        
                echo $this->Html->image($image, array('alt' => $series['Series']['name'], 'style' => 'float: right; width: 208px;'));
                ?>

	<p class="synopsis" style="margin-right: 250px;">
		<?php if(!empty($series['Series']['synopsis'])){
			echo nl2br(h($series['Series']['synopsis']));
		}
		if(!empty($series['Series']['synopsis_source'])):
		?>
		<span class="source"><strong>Source:</strong> <?php echo $this->At->synopsisSource($series['Series']['synopsis_source']); ?></span>
		<?php endif; ?>
	</p>

	<h3><?php echo $tableHeader; ?></h3>

	<span style="display: block;">Below are <strong><?php echo count($series['SeriesSite']); ?> streaming sites</strong> which allow you to <strong>watch <?php echo $series['Series']['name']; ?></strong>.</span>
		
	<div id="series">
		<?php echo $this->element('series/series', array('series' => $series, 'outAction' => $outAction));  ?>
	</div>

<?php //<div class="sidebar grid_3">?>
	<?php //echo $this->element('series/sidebar', array('series' => $series, 'premiumSingle' => $premiumSingle));  ?>

