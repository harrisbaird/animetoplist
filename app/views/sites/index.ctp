<?php
$this->Html->setHeader('Welcome to Anime Toplist');
$this->Html->setTitle('Ranking the top Anime sites - Watch streaming Anime online');
?>

<?php
	$i = 0;
	foreach($sites as $site):
?>

<?php echo $this->element('global/ranking_box', array('site' => $site, 'i' => $i)); ?>

<?php
	$i++;
	endforeach;
?>

<?php
echo $this->element('global/pagination');
?>

<?php echo $this->Js->writeBuffer(); ?>

<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName('script')[0];
a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0002/6068.js?"+Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>
