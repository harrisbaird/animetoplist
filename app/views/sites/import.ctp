<?php $this->Html->setHeader('Import sites'); ?>

<p>This lets you merge all your sites listed on Anime Toplist into a single account.</p>

<p>Enter the username and password of the account you wish to import from.</p>

<?php
    echo $this->Form->create('User', array('url' => array('controller' => 'sites', 'action' => 'import')));
    echo $this->Form->input('User.username');
    echo $this->Form->input('User.password');

	echo $this->element('buttons/button_submit', array('text' => 'Import sites'));

    echo $this->Form->end();
?>