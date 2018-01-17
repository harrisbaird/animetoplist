<?php $this->Html->setHeader(ucwords($type) . ' List'); ?>

<ul class="letter-menu">
<?php

echo $this->Html->tag('li', $this->Html->link('All', array('action' => 'index', $type, 'category' => $category)));

foreach($alpha as $url => $menuLetter) {
	if(!in_array($url, $usedLetters)) {
		$link = '<span class="nolink">' . $menuLetter . '</span>';
	} else {
		$link = $this->Html->link($menuLetter, array('controller' => 'series', 'action' => 'index', $type, 'letter' => $url, 'category' => $category));
	}
	
	echo $this->Html->tag('li', $link);
}
?><br />
<?php
echo $this->Html->tag('li', $this->Html->link('All', array('action' => 'index', $type, 'letter' => $letter)));
foreach($categories as $category) {
	echo $this->Html->tag('li', $this->Html->link($category['AnimeType']['name'], array($type, 'category' => $category['AnimeType']['slug'], 'letter' => $letter)));
}
?>
</ul>

<div id="series-list" class="grid_9 alpha omega">
	<?php echo $this->At->listColumn(3, $series); ?>
</div>