<?php $this->Html->setHeader('Contact Us'); ?>

<?php
echo $this->Form->create('Contact', array('url' => array('controller' => 'contact')));
echo $this->Form->input('email', array('label' => 'Your email address'));

echo $this->Form->input('type', array('type' => 'select', 'options' => $options, 'empty' => 'Select one', 'label' => 'Reason for contacting'));

echo $this->Form->input('message');
echo $this->Html->scriptBlock('var RecaptchaOptions = {theme : \'custom\'};');

?>

<div class="input captcha <?php if(!empty($error_captcha_class)) echo $error_captcha_class; ?>" id="custom_theme_widget">
	<label>You're not a bot, are you?</label>
	<div id="recaptcha_image"></div>
	<?php echo $this->Form->input('recaptcha_response_field', array('id' => 'recaptcha_response_field', 'name' => 'recaptcha_response_field', 'div' => false, 'label' => false)); ?>
	<?php if(!empty($error_captcha)) echo $html->tag('div', $error_captcha, array('class' => 'error-message')); ?>
</div>

<?php
echo $this->Recaptcha->script();

echo $this->element('buttons/button_submit', array('text' => 'Send message'));

echo $this->Form->end();
?>