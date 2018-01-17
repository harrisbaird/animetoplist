<?php $this->Html->setHeader('Forgotten password'); ?>

<p>Enter your email address and we'll send you details on how to reset your password.</p>

<?php
    echo $form->create('User');
    echo $form->input('email');

	echo $this->element('buttons/button_submit', array('text' => 'Recover password'));

    echo $form->end();
?>