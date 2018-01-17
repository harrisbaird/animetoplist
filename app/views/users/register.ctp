<?php $this->Html->setHeader('Create an account'); ?>

<?php
echo $this->Form->create('User');
echo $this->Form->input('username');
echo $this->Form->input('email');
echo $this->Form->input('password');
echo $this->Form->input('password_confirm', array('label' => 'Confirm Password', 'type' => 'password'));
?>

<?php echo $this->element('buttons/button_submit', array('text' => 'Create Account')); ?>

<?php echo $this->Form->end(); ?>
