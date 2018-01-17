<?php
$verification_required = false;
$paused_sites = false;
?>

<table class="table" style="margin-bottom: 20px;">
	<thead>
		<tr>
			<th>Site</th>
			<th>Anime</th>
			<th>Manga</th>
			<th></th>
		</tr>
	</thead>
	<?php foreach($sites as $site):	?>
	<?php
		if(strpos($site['Site']['url'], 'hent') !== false) $hentai_sites = true;
		if($site['Site']['is_premium'] || $site['Site']['is_footer_link']) $has_premium = true;
		if(!$site['Site']['is_verified']) $verification_required = true;
	?>
	<tr class="<?php 
		echo $this->Cycle->cycle('odd', 'even');
		if($site['Site']['is_premium']) //echo ' premium';
	?>">

               <?php if($site['Site']['is_verified'] == 2): ?>
		<td colspan="5"><strong><?php echo $site['Site']['official_name'] ?></strong> was manually checked and rejected from Anime Toplist.</td>
		<?php else: ?>

		<td><?php echo $this->Html->link($site['Site']['official_name'], array('controller' => 'sites', 'action' => 'view', $site['Site']['slug'])); ?>
		<?php if(!$site['Site']['is_verified']): ?>
		<span style="color: #666; font-weight: bold; padding-bottom: 10px; font-size: 11px; line-height: 11px; display: block;">Pending verification</span>
		<?php endif; ?>
		</td>
		<td><?php echo $this->Html->link((!empty($site['Site']['anime_count']) ? $site['Site']['anime_count'] : 'None'), array('controller' => 'series', 'action' => 'moderate', 'anime', $site['Site']['slug'])); ?></td>
		<td><?php echo $this->Html->link((!empty($site['Site']['manga_count']) ? $site['Site']['manga_count'] : 'None'), array('controller' => 'series', 'action' => 'moderate', 'manga', $site['Site']['slug'])); ?></td>
		<td style="font-size:12px;" class="<?php if($site['Site']['is_premium']) echo ' premium'; ?>"><?php
		if($site['Site']['is_premium_paused']) {
				echo '<span style="font-weight: bold">Marked as Paused</span>';
				$paused_sites = true;
                } else if($site['Site']['is_premium'] || $site['Site']['is_boosted'] || $site['Site']['is_footer_link']) {
			if($site['Site']['is_premium']) {
				$premium_expires = strtotime($site['Site']['premium_expires_at']);
				echo 'Premium: ' . date('j, M Y', $premium_expires);
			}
			if($site['Site']['is_boosted']) {
				echo '<br> Boost: ' . date('j, M Y', strtotime($site['Site']['boost_expires_at']));
			}
                        if($site['Site']['is_footer_link']) {
                                $footer_link_expires = strtotime($site['Site']['footer_link_expires_at']);
                                echo 'Footer link: ' . $this->Html->link(date('j, M Y', $footer_link_expires), array('controller' => 'premium_orders', 'action' => 'index'));
                        }
		} else {
			echo 'Premium membership is no longer available';
		}
		?></td>
		<td><?php
		echo $this->Html->link('Edit', array('controller' => 'sites', 'action' => 'wizard', 'details', $site['Site']['id']), array('style' => 'padding-right: 5px;'));
		echo $this->Html->link('X', array('controller' => 'sites', 'action' => 'delete', $site['Site']['id']), array('class' => 'delete', 'style' => 'color: red; margin-left: 5px; float: right;', 'rel' => $site['Site']['official_name']));
		?></td>

		<?php endif; ?>
	</tr>
	<?php endforeach; ?>
        <?php if(empty($sites)): ?>
        <tr>
          <td colspan="5" style="text-align: center;">No sites to display. <a href="/sites/wizard/details">Add a site</a></td>
        </tr>
        <?php endif ?>
</table>

<?php if($paused_sites): ?>
<div class="message bad"><span class="icon"></span><p><strong>One or more sites have premium membership currently paused. Contact us to unpause it.</strong></p>
</div>                  
<?php endif; ?>  

<?php if($verification_required): ?>
<?php $email = Configure::read('App.email'); ?>

<div class="message bad"><span class="icon"></span><p><strong>One or more of your sites requires manual verification, if sucessful, your site will be visible on Anime Toplist within the next few days.<br />Premium sites are activated instantly.<br><br>
If there are any problems, please contact us at <?php echo $this->Html->link($email, 'mailto:' . $email); ?></strong></p>
</div>
<?php endif; ?>


<style>
  ul.nopadding li { margin: 0 !important; }

  .add-site-big {
	float: right;
	margin: -50px 0 10px 0;
	-moz-box-shadow:inset 0px 1px 0px 0px #bbdaf7;
	-webkit-box-shadow:inset 0px 1px 0px 0px #bbdaf7;
	box-shadow:inset 0px 1px 0px 0px #bbdaf7;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #2e97ff), color-stop(1, #003a73) );
	background:-moz-linear-gradient( center top, #2e97ff 5%, #003a73 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#2e97ff', endColorstr='#003a73');
	background-color:#2e97ff;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #84bbf3;
	display:inline-block;
	color:#ffffff;
	font-family:arial;
	font-size:15px;
	font-weight:bold;
	padding:9px 23px;
	text-decoration:none;
	text-shadow:1px 1px 0px #528ecc;
}.add-site-big:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #003a73), color-stop(1, #2e97ff) );
	background:-moz-linear-gradient( center top, #003a73 5%, #2e97ff 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#003a73', endColorstr='#2e97ff');
	background-color:#003a73;
}.add-site-big:active {
	position:relative;
	top:1px;
}

h2 { padding-bottom: 0; }
</style>
