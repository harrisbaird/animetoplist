<ul>
<?php
	foreach($data as $site) {
		echo $this->Html->tag('li', $this->Html->link($site['Site']['official_name'], array('controller' => 'out', 'action' => 'site', $site['Site']['slug']), array('rel' => 'external')), array('class' => 'text-link'));
	}
	
	if(empty($data)) {
		echo $this->Html->tag('li', 'No sites');
	}
?>
</ul>