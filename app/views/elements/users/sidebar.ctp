<div class="sidebar grid_3">

	<div class="buttons">
		<ul>
			<li><span class="jcenter"><?php echo $this->element('buttons/button_submit', array('type' => 'link', 'text' => 'Add site', 'url' => array('controller' => 'sites', 'action' => 'wizard'))); ?></span></li>
		</ul>
	</div>

	<div class="module">
		<h2>My Account</h2>
		<ul>
			<li><?php echo $this->Html->link('Change Password', array('controller' => 'users', 'action' => 'changePassword')); ?></li>
			<li><?php echo $this->Html->link('Need help? Contact us', array('controller' => 'contact', 'action' => 'index'))?></li>
		</ul>
	</div> <!-- /.module -->
</div>
