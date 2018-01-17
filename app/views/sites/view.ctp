<?php
$go_url = Router::url(array('controller' => 'out', 'action' => 'site', $site['Site']['slug']));
$go_title = $site['Site']['official_name'];
$this->Html->headerContent = sprintf('<a href="%s" rel="external" style="float: right;" class="large awesome">Go to %s</a>', $go_url, $go_title);
 ?>

<?php
$title = $site['Site']['official_name'];

if($site['Site']['anime_count']) {
    $title = 'Watch streaming Anime at ' . $title;
} else if($site['Site']['manga_count']) {
    $title = 'Read Manga at ' . $title;
}

$this->Html->setHeader($site['Site']['official_name']);
$this->Html->setTitle($title);


if(!empty($site['Site']['is_dead'])):
?>

<div class="message bad"><span class="icon"></span><p><strong>This site is no longer available</strong>
Reason: <?php
echo $site['Site']['dead_reason'] == 1 ? 'Domain expired' : 'Parked Domain';
?>
</p>
</div>

<?php endif; ?>

<p><?php echo h($site['Site']['description']); ?></p>

<style>
.awesome, .awesome:visited {
background: #ffffff; /* Old browsers */
background: -moz-linear-gradient(top, #ffffff 0%, #f1f1f1 50%, #e1e1e1 51%, #f6f6f6 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(50%,#f1f1f1), color-stop(51%,#e1e1e1), color-stop(100%,#f6f6f6)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top, #ffffff 0%,#f1f1f1 50%,#e1e1e1 51%,#f6f6f6 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top, #ffffff 0%,#f1f1f1 50%,#e1e1e1 51%,#f6f6f6 100%); /* Opera11.10+ */
background: -ms-linear-gradient(top, #ffffff 0%,#f1f1f1 50%,#e1e1e1 51%,#f6f6f6 100%); /* IE10+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f6f6f6',GradientType=0 ); /* IE6-9 */
background: linear-gradient(top, #ffffff 0%,#f1f1f1 50%,#e1e1e1 51%,#f6f6f6 100%); /* W3C */

	display: inline-block;
	color: #000;
	text-decoration: none;
	-moz-border-radius: 5px; 
	-webkit-border-radius: 5px;
	-moz-box-shadow: 0 1px 3px rgba(0,0,0,0.5);
	-webkit-box-shadow: 0 0 1px rgba(0,0,0,0.5);
	border-bottom: 1px solid rgba(0,0,0,0.25);
	position: relative;
	cursor: pointer;
	margin-top: 10px;
}

.awesome:hover					{ background-color: #111; color: #000; text-decoration: none; }
.awesome:active					{ top: 1px; }
.awesome, .awesome:visited			{ font-size: 13px; line-height: 1; font-weight: bold; }
.large.awesome, .large.awesome:visited 		{ font-size: 14px; padding: 5px 10px 6px; }
</style>

<div id="tabs">
	<ul>
		<?php if($site['User']['id'] == $userData['User']['id']): ?>
		<li><a href="#code"><span>Code</span></a></li>
		<?php endif; ?>
		<li<?php if(empty($anime)) echo ' class="disabled"'; ?>><a href="#anime"><span>Anime List</span></a></li>
		<li<?php if(empty($manga)) echo ' class="disabled"'; ?>><a href="#manga"><span>Manga List</span></a></li>
	</ul>
	
	<?php if($site['User']['id'] == $userData['User']['id']): ?>
	<div id="code">
		<?php echo $this->element('sites/button_code', array('id' => $site['Site']['id'])); ?>
		
	</div>
	<?php endif; ?>

	<div id="anime">
		<div id="series-list" class="grid_6 alpha omega">
			<?php echo $this->At->listColumn(1, $anime, 12, $site['Site']['slug']); ?>
		</div>
	</div>
	
	<div id="manga">
		<div id="series-list" class="grid_6 alpha omega">
			<?php echo $this->At->listColumn(1, $manga, 12, $site['Site']['slug']); ?>
		</div>
	</div>
</div>



