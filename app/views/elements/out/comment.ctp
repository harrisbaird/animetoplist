<div id="comment">
	<?php
	echo $this->Form->create('Site', array('id' => 'commentForm', 'url' => array('action' => 'comment', $site['Site']['slug'], $site['Site']['id'])));
	echo $this->Form->input('Comment.body', array('value' => 'Enter comment', 'type' => 'textarea', 'label' => false, 'div' => false));
	echo $this->Form->end();
	?>
</div>
<ul id="submit">
	<li><a href="#" class="green"><span>Send</span></a></li>
</ul>