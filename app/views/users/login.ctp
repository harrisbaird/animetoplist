<?php $this->Html->setHeader('Login'); ?>

<?php
    echo $this->Form->create('User');
    echo $this->Form->input('username');
    echo $this->Form->input('password');

?>
<p>
    <?php echo $this->Html->link('No account? Create one', array('action' => 'register')); ?><br />
    <?php echo $this->Html->link('Forgotten your password?', array('action' => 'forgottenPassword')); ?>
</p>
	
<?php
	echo $this->element('buttons/button_submit', array('text' => 'Login'));
	
    echo $this->Form->end();
?>