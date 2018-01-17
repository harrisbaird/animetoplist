<?php
$this->Statistics->setData(array('siteId' => $siteId, 'type' => $type, 'duration' => $duration, 'group' => $data['group']));
?>

<div id="stats-ajax">
	<div class="grid_6 alpha omega">
		<h3>Customize statistics</h3>
		<p><strong>Type:</strong> <?php echo $this->Statistics->links($type); ?></p>
		<p><strong>Duration:</strong> <?php echo $this->Statistics->links($duration); ?></p></p>
	</div>

	<div class="grid_6 alpha omega">
		<h3><?php echo $this->Statistics->header(); ?></h3>
		<?php echo $this->Statistics->chart($data['data']); ?>
	</div>

	<div class="grid_6 alpha omega">
		<?php echo $this->Statistics->table($data, $type); ?>
	</div>
</div>

<?php $this->Html->scriptStart(array('safe' => false)); ?>
	$(function() {
		$('#stats-ajax a').bind('click',function(e) {
			e.preventDefault();
			$.get(this.href,{},function(response) { 
				$('#stats-ajax').html(response);
			});
			return false;
		});
	});

Cufon.replace('#stats-ajax h3');
<?php echo $this->Html->scriptEnd(); ?>