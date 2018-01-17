<div id="dialog">
	<div class="dialog-message">
		<h2>You need to login or register to <strong>post a comment</strong>.</h2>
		<hr />
	</div>

	<div id="dialog-register">
		<?php echo $this->Form->create('User', array('url' => array('action' => 'register'))); ?>
		<div class="dialog-container">
			<h2>Register</h2>
			<div class="form">
				<?php
					echo $this->Form->input('username');
					echo $this->Form->input('email');
					echo $this->Form->input('password');
					echo $this->Form->input('password_confirm', array('label' => 'Confirm Password', 'type' => 'password'));
				?>

			</div> <!-- .form -->
		</div> <!-- #dialog-container -->

		<div class="dialog-footer">
			<div class="dialog-switch login"><a href="#">Have an account? Login</a></div>
			<ul>
				<li><?php echo $this->element('buttons/button_submit', array('text' => 'Register')); ?></li>
				<li><?php echo $this->element('buttons/button_submit', array('text' => 'Cancel', 'type' => 'link', 'class' => 'grey close')); ?></li>
			</ul>
			<?php echo $form->end(); ?>
		</div> <!-- #dialog-footer -->
	</div> <!-- #dialog-register -->


	<div id="dialog-login">
		<?php echo $form->create('User', array('url' => array('action' => 'login'))); ?>
		<div class="dialog-container">
			<h2>Login to Anime Toplist</h2>

			<div class="form">
				<?php
				    echo $form->input('username');
				    echo $form->input('password');
				    echo $form->hidden('next');
				?>
				<a href="#" class="dialog-switch forgotten">Forgotten your password?</a>
			</div> <!-- .form -->
		</div> <!-- #dialog-container -->

		<div class="dialog-footer">
			<div class="dialog-switch register"><a href="#">No account? Create one</a></div>

			<ul>
				<li><?php echo $this->element('buttons/button_submit', array('text' => 'Login')); ?></li>
				<li><?php echo $this->element('buttons/button_submit', array('text' => 'Cancel', 'type' => 'link', 'class' => 'grey close')); ?></li>
			</ul>
			<?php echo $form->end(); ?>
		</div> <!-- #dialog-footer -->
	</div> <!-- #dialog-login -->

	<div id="dialog-forgotten">
		<?php echo $form->create('User', array('url' => array('action' => 'forgottenPassword'))); ?>
		<div class="dialog-container">
			<h2>Forgotten password</h2>

			<p>Enter your email address and we'll send you details on how to reset your password.</p>

			<div class="form">
				<?php
				    echo $form->input('email');
				?>
			</div> <!-- .form -->
		</div> <!-- #dialog-container -->

		<div class="dialog-footer">
			<div class="dialog-switch login"><a href="#">Back to login</a></div>

			<ul>
				<li><?php echo $this->element('buttons/button_submit', array('text' => 'Recover')); ?></li>
				<li><?php echo $this->element('buttons/button_submit', array('text' => 'Cancel', 'type' => 'link', 'class' => 'grey close')); ?></li>
			</ul>
			<?php echo $form->end(); ?>
		</div> <!-- #dialog-footer -->
	</div> <!-- #dialog-forgotten -->

</div><!-- #dialog -->

