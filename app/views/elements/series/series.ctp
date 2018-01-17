<style type="text/css"> 
  .small-url { margin-left: 10px; block; font-size: 12px; color: #999; } 
  a.external { display: block; text-decoration: none; } 
  a.external:hover .site-name { text-decoration: underline;  } 
  a.external:hover p { color: #000; }
  a.external:hover .small-url { color: #000;  } 
  /* .site-row.banner a.external:hover .language { margin-right: 70px; } */
  /* .site-row:hover img { width: 80px !important; height: 80px !important; position: absolute; right: 0; z-index: 2; top: -20px; box-shadow: 0 0 20px 5px rgba(0,0,0,.5); padding: 0 !important; } */
  .site-image { float: right; height: 20px; width: 20px; position: relative; padding-right: 5px; } 
  .site-image img { height: 20px; display: block; padding: 3px 0; } 
  .site-row.premium .site-image { height: 46px; width: 40px } 
  .site-row.premium img { width: 40px; height: 40px; padding: 6px 0; } 
  .site-row { height: 28px; vertical-align: middle; } 
  .site-row.premium { height: 48px; }   
  .site-row p { margin: 0; color: #444; font-size: 12px; }
  /*.site-row.premium .small-url { display: block; margin: 0; line-height: 10px;} */
  .language {
    float: right;
    margin: 5px 20px 5px 5px;
    border: 1px solid #000;
    padding: 2px 5px;
    border-radius: 5px;
    display: block;
    line-height: 10px;
    font-size: 10px;
    color: #fff;
  }
  .language-0 { display: none; }
  .language-1 { background-color: #008000; border-color: #004D00; }
  .language-2 { background-color: #004080; border-color: #00264D; }
  .language-3 { background-color: #800000; border-color: #4D0000; }
  .language-4 { background-color: #949400; border-color: #C7C700; }
</style>

<?php $language_text = array('Unknown', 'Subbed', 'Dubbed', 'Raw', 'Mixed'); ?>

			<table id="sitesTable" style="margin-top: 10px;">
				<thead>
					<tr>
						<th>Sites that have <?php echo $series['Series']['name']; ?></th>
						<th class="actions"></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($series['SeriesSite'] as $site):
						if(empty($site['Site']['official_name'])) continue;
					?>
					<tr class="site-row <?php
							echo $this->Cycle->cycle('odd', 'even');
							if($site['Site']['is_premium']) echo ' premium';
							if($site['Site']['is_boosted']) echo ' boost';
							if($site['Site']['has_banner']) echo ' banner';
						?>">
						<?php $url = empty($site['url']) ? $site['Site']['url'] : $site['url']; ?>
						<td>
<a href="<?php echo $this->Html->url(array('controller' => 'out', 'action' => $outAction, $series['Series']['slug'], $site['Site']['slug'])); ?>" class="external">
                                                        <div class="site-image"><?php  
                                                                $banner = 'sites/' . $site['Site']['id'] . '_80.jpg';  
								$iClass = !$site['Site']['is_boosted'] ?  "lazy" : "";
                                                  
                                                                if($site['Site']['has_banner']) {  
                                                                        echo $this->Html->image($banner, array('alt' => $site['Site']['official_name'], 'class' => $iClass));
                                                                }  
                                                        ?></div>    
							<?php $language = !empty($site['language']) ? $site['language'] : $site['Site']['language']; ?>
							<span class="language language-<?php echo $language; ?>"><?php echo $language_text[$language]?></span>
								<span class="site-name"><?php echo $site['Site']['official_name']; ?></span>
								<span class="small-url"><?php echo str_replace('www.', '',parse_url($site['Site']['url'], PHP_URL_HOST)); ?></span>
								<?php if($site['Site']['is_premium']) echo $this->Html->tag('p', $site['Site']['description']); ?>
							</a>
						</td>
						<td class="actions"><?php echo $this->Html->link('Profile', array('controller' => 'sites', 'action' => 'view', $site['Site']['slug']), array('class' => 'profile')); ?></td>

					</tr>
					<?php
					endforeach;
					echo $this->element('global/no_data', array('data' => $series['SeriesSite'], 'colspan' => 2))
					?>
				</tbody>
			</table>


