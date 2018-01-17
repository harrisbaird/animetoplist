<ul>
<?php
        foreach($boosted as $site) {
		echo $this->Html->tag('li', $this->Html->link($site['Site']['official_name'], array('controller' => 'out', 'action' => 'site', $site['Site']['slug']), array('class' => 'external')), array('class' => 'text-link'));
        }

?>
</ul>
<ul class="random">
<?php
	foreach($premium as $site) {
		echo $this->Html->tag('li', $this->Html->link($site['Site']['official_name'], array('controller' => 'out', 'action' => 'site', $site['Site']['slug']), array('class' => 'external')), array('class' => 'text-link'));
	}
	
	if(empty($premium)) {
		echo $this->Html->tag('li', 'No sites');
	}
?>
</ul>
