<li class="<?php
echo $this->At->cssClass(array(
	array('class' => 'depth-' . $level , 'condition' => $level, 'value' => 1, 'type' => '>'),
	array('class' => 'parent' , 'condition' => $level, 'value' => 1, 'type' => '=')
));
?>">
	<div class="comment-wrapper">
	<div class="comment-header">
		<div class="reply">
			<?php if($level < 3): ?>
			<a href="#" class="reply" onclick="return Comment.reply(<?php echo $comment['Comment']['id']; ?>, $(this))">Reply</a>
			<?php endif; ?>

			<?php if($siteOwner): ?>
				<?php
					echo $this->Html->link('Delete', array('controller' => 'sites', 'action' => 'commentVisibility', $comment['Comment']['id']), array('class' => 'reply delete-comment'));
				?>
			<?php endif; ?>
		</div>
<!--		<div class="avatar"><?php
		if(!empty($comment['User']['email'])) {
			echo $this->Gravatar->image($comment['User']['email'], array(
				'default' => 'http://animetoplist.org/img/default/avatar.png',
				'size' => 25
			));
		} else {
			echo $this->Html->image('default/avatar.png');			
		}
		?></div>-->
		<span class="author"><?php echo $comment['User']['username']; ?></span>
		<span class="date"><?php echo $this->Time->niceShort($comment['Comment']['created']); ?></span>
	</div>
	<p><?php echo str_replace('\n', '<br />', utf8_encode($comment['Comment']['body'])); ?></p>

<?php if(!empty($children)): ?>

<ul class="children">
	<?php echo $children; ?>
</ul>

<?php endif; ?>

</div>
</li>
