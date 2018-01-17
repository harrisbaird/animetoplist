<div class="grid_8" id="login">
	<?php if(empty($userData)): ?>
	<ul>
		<li class="noline" id="login-link"><?php echo $this->Html->link('Login', array('controller' => 'users', 'action' => 'login')); ?></li>
		<li id="register-link"><?php echo $this->Html->link('Register', array('controller' => 'users', 'action' => 'register')); ?></li>
	</ul>
	<?php else: ?>
	<ul>
		<li class="text"><p>Welcome back <strong><?php echo $userData['User']['username']; ?></strong></p></li>
		<li><?php echo $this->Html->link('My Sites', array('controller' => 'users', 'action' => 'profile')); ?></li>
		<li><?php echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout')); ?></li>
	</ul>
	<?php endif; ?>
</div>
