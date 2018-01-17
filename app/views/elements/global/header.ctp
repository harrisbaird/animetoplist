<div id="header">
	<div id="logo" class="grid_4">
		<h1><?php echo $this->Html->link('Anime Toplist', '/'); ?></h1>
	</div>

	<cake:nocache>
	<?php echo $this->element('global/login_buttons'); ?>
	</cake:nocache>
</div>

<div class="clear"></div>

<div class="navbar clearfix">
	<div class="grid_9">
		<?php
			$siteOptions = array('style' => 'color: #fff !important;');
			if(empty($userData)) $siteOptions = array('li' => array('class' => 'add-site register-dialog'));

			$links = array(
				array('Home', array('controller' => 'sites', 'action' => 'index')),
				array('Anime List', array('controller' => 'series', 'action' => 'index', 'anime')),
				array('Manga List', array('controller' => 'series', 'action' => 'index', 'manga')),
			);

			echo $this->Navigation->menu($links, array('a' => array('wrap' => 'span', 'active' => 'current')));
		?>
	</div>

	<div id="search" class="grid_3">
		<div id="search-container">
			<?php
			echo $this->Form->create('Search', array('url' => array('controller' => 'search', 'action' => 'index'), 'type' => 'get'));
			echo $this->Html->tag('div', null, array('id' => 'search-container'));
			echo $this->Form->input('Search.q', array('value' => 'Search', 'div' => false, 'label' => false, 'id' => 'SearchQuery'));
			echo $this->Form->button('Search', array('class' => 'submit'));
			echo $this->Html->tag('/div');
			echo $this->Form->end();
			?>

			<div id="autocomplete"></div>
		</div>
	</div>
</div> <!-- .navbar -->

<?php if(!empty($featured)): ?>
<div id="featured">
	<?php
	if(!empty($featuredSites)):
		unset($featuredSeries[2]);
	?>
		<div class="grid_12">
			<div class="carousel">
				<ul>
					<?php shuffle($featuredSites); ?>
					<?php foreach($featuredSites as $site): ?>
					<li><?php echo $this->element('global/featured_premium', array('site' => $site)); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>
	<?php

//	foreach($featuredSeries as $series) {
//		echo $this->element('global/featured_series', array('series' => $series));
//	}
	?>

</div> <!-- #featured -->


<?php endif; ?>
