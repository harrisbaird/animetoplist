<?php $this->Html->setHeader('Change your password'); ?>

<?php
    echo $form->create('User');
    echo $form->input('password');
    echo $form->input('password_confirm', array('type' => 'password'));

	echo $this->element('buttons/button_submit', array('text' => 'Change password'));

    echo $form->end();
?>