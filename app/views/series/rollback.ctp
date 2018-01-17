<?php $this->Html->setHeader('Rollback'); ?>

<?php
	echo $form->create('Series', array('url' => array($id, $revision)));
	echo $form->input('reason', array('label' => 'Reason for rollback'));
?>

<div class="submit">
	<?php echo $this->element('buttons/button_submit', array('text' => 'Rollback')); ?>
</div>

<?php echo $form->end();?>