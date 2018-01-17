<div class="premium-featured" class="grid_4 alpha omega" style="background-color: #701616;">
	<a href="<?php echo $site['Site']['url']; ?>" class="external">
	<p class="description"><?php echo $site['Site']['description']; ?></p>
	<div class="premium"></div>
	<div class="icon">
		<?php
		$banner = 'sites/' . $site['Site']['id'] . '_138.jpg';

		if($site['Site']['has_banner'] != 1) {
			$banner = Configure::read('App.images.sites.large');
		}
	
		echo $this->Html->image($banner, array('alt' => $site['Site']['official_name'], 'class' => 'no-lazyload'));
		?>
	</div>
	<div class="overlay"></div>
	<div class="title">
		<h2><?php echo $site['Site']['official_name']; ?></h2>
	</div>
	</a>
</div>
