<?php
  $grid_class = '';
  if(!isset($no_grid)) {
    $grid_class = 'grid_3 ';
    $grid_class .= $this->Cycle->cycle('alpha', '', 'omega');
    $grid_class .= ' ' . $this->At->rankingTopClass($site);
  }
?>				
				
				<div id="site-<?php echo $site['Site']['slug']; ?>" class="ranking-box <?php echo $this->At->premiumClass($site); ?> <?php echo $grid_class; ?>">
					<a href="<?php echo Router::url(array('controller' => 'out', 'action' => 'site', $site['Site']['slug'])); ?>" class="external">
						<div class="top">
							<div class="description">
								<?php 
								//echo $this->Text->truncate(h($site['Site']['description']), 75);
								echo h($site['Site']['description']);
								 ?>
							</div>
							<div class="icon"><?php
								$banner = 'sites/' . $site['Site']['id'] . '_80.jpg';

								if($site['Site']['has_banner'] != 1) {
									$banner = Configure::read('App.images.sites.small');
								}

								echo $this->Html->image($banner, array('alt' => $site['Site']['official_name']));
							?></div>
						</div>
						<div class="title"><h4><?php echo $site['Site']['official_name']; ?></h4></div>
					</a>
					<ul class="footer">
						<?php if(!empty($site['Site']['anime_count'])): ?>
						<li class="series" title="Anime series and movies"><span></span><?php echo $site['Site']['anime_count']; ?></li>
						<?php endif;?>
						<?php if(!empty($site['Site']['manga_count'])): ?>
						<li class="manga" title="Manga"><span></span><?php echo $site['Site']['manga_count']; ?></li>
						<?php endif; ?>
						<li class="comments" title="Comments"><span></span><?php echo $site['Site']['comment_count']; ?></li>
						<li class="right"><?php echo $this->Html->link('Profile', array('action' => 'view', $site['Site']['slug'])); ?></li>
					</ul>
				</div> <!-- /.ranking-box -->

<?php if($site['Site']['is_premium']): ?>
  <style type="text/css">
    <?php if(!empty($site['Site']['premium_box_bg'])): ?>
      #site-<?php echo $site['Site']['slug']; ?> {
        background-color: <?php echo $site['Site']['premium_box_bg']; ?>;
      }
    <?php endif; ?>
    <?php if(!empty($site['Site']['premium_box_text'])): ?>
      #site-<?php echo $site['Site']['slug']; ?> .description, #site-<?php echo $site['Site']['slug']; ?> a, #site-<?php echo $site['Site']['slug']; ?> li {
        color: <?php echo $site['Site']['premium_box_text']; ?>;
      }
    <?php endif; ?>
    <?php if(!empty($site['Site']['premium_box_title'])): ?>
      #site-<?php echo $site['Site']['slug']; ?> h4 {
        color: <?php echo $site['Site']['premium_box_title']; ?>;
      }
    <?php endif; ?>
  </style>
<?php endif; ?>
