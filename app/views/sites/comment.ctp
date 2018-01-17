<?php
echo $this->Form->create('Site', array('id' => 'commentForm', 'url' => array('action' => 'comment', $slug, $site_id)));
echo $this->Form->input('Comment.body', array('type' => 'textarea', 'label' => 'Add comment <em>(no HTML please)</em>'));
echo $this->Form->hidden('Comment.parent_id');
?>

		<div class="submit clearfix">
			<?php echo $this->element('buttons/button_submit', array('text' => 'Post Comment')); ?>
		</div>
		
		<?php echo $this->Form->end(); ?>
