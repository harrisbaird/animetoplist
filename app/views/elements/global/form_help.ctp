<div class="sidebar grid_3">
<?php if(isset($ranking_box)): ?>
  <div id="customize_preview" class="grid_3 alpha omega"><?php echo $ranking_box; ?></div>
<?php endif; ?>

<?php
$data['items'][] = array(
	'header' => 'Help',
	'hoverClass' => false,
	'contents' => 'Hover over a field to get help and tips about it.',
	'default' => true
);

$jsCode = '';
foreach($data['items'] as $id => $item):

	$this->Html->scriptStart(array('safe' => false, 'inline' => false));

	if(!empty($item['default'])) {
		$jsCode .= String::insert("$('.help-:id').fadeIn();", array('id' => $id));
	}

	if(!empty($item['hoverClass'])) {
		$jsCode .= String::insert("$(':hoverClass').bind('mouseenter', function() {showBox($(this), '.help-:id', :location);});\n", array(
			'hoverClass' => $item['hoverClass'],
			'id' => $id,
			'location' => $item['location']
			)
		);
	}

	$this->Html->scriptEnd();
?>

	<div class="module form-help help-<?php echo $id; ?>">
		<h2><?php echo $item['header']; ?></h2>
		<ul>
			<?php
			if(is_array($item['contents'])) {
				foreach($item['contents'] as $li) { 
					echo $this->Html->tag('li', $li);
				}
			} else {
				echo $this->Html->tag('li', $item['contents']);
			}
			?>
		</ul>
	</div>

<?php endforeach; ?>

	<?php $this->Html->scriptStart(array('safe' => false, 'inline' => false)); ?>
		$(function() {
			<?php echo $jsCode; ?>

			$('button').click(function() {
				$('*').unbind();
			});
		});


	<?php $this->Html->scriptEnd(); ?>
</div>