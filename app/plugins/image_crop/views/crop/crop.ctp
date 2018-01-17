<?php $this->Html->setHeader($settings['header']); ?>

<?php if(!empty($settings['message'])): ?>
<p><?php echo $settings['message']; ?></p>
<?php endif; ?>

<?php
echo $this->Form->create('Site', array('url' => array('controller' => 'crop', 'action' => 'process')));
?>

<div class="sidebar">
	<div class="module preview">
		<h2>Preview</h2>
		<ul>
			<li><?php echo $this->Html->div(null, $this->Html->image($settings['image']['path'], array('id' => 'preview', 'class' => 'no-lazyload'))); ?></li>
		</ul>
	</div>
</div>

<?php
echo $this->Html->image($settings['image']['path'], array('id' => 'crop', 'class' => 'no-lazyload'));
echo $this->Form->hidden('x1');
echo $this->Form->hidden('x2');
echo $this->Form->hidden('y1');
echo $this->Form->hidden('y2');
echo $this->Form->hidden('w');
echo $this->Form->hidden('h');
?>

<div class="submit">
	<?php
	echo $this->element('buttons/button_submit', array('text' => 'Crop and Finish'));
	
	if(!empty($settings['cancel'])) {
		echo $this->element('buttons/button_submit', array('url' => $settings['cancel']['url'], 'text' => $settings['cancel']['text'], 'type' => 'link', 'class' => 'grey'));
	}
	?>
</div>

<?php echo $this->Form->end(); ?>

<?php $this->Html->script('/image_crop/js/jquery.Jcrop.js', array('inline' => false)); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>

var jcropApi;
var imageWidth = <?php echo $settings['image']['width'] ?>;
var imageHeight = <?php echo $settings['image']['height'] ?>;

<?php
foreach($settings['sizes'] as $size) {
	$previewWidth = $size['width'];
	break;
}

if($previewWidth < 100) $previewWidth = 100;

$previewHeight = $previewWidth / $settings['aspectRatio']['decimal'];
$boxWidth = ife($settings['hidePreview'], 650, 440);
?>

var previewWidth = <?php echo $previewWidth; ?>;
var previewHeight = <?php echo $previewHeight; ?>;
var boxWidth = <?php echo $boxWidth; ?>;
var aspectRatioFrac = <?php echo $settings['aspectRatio']['fraction']; ?>;
var aspectRatioDec = <?php echo $settings['aspectRatio']['decimal']; ?>;

function showPreview(c)
{
 
	//console.log(c);
	var rx = previewWidth / c.w;
	var ry = previewHeight / c.h;
 
	$('#preview').css({
		width: Math.round(rx * imageWidth) + 'px',
		height: Math.round(ry * imageHeight) + 'px',
		marginLeft: '-' + Math.round(rx * c.x) + 'px',
		marginTop: '-' + Math.round(ry * c.y) + 'px'
	});
 
	$('#SiteX1').val(c.x);
	$('#SiteY1').val(c.y);
	$('#SiteX2').val(c.x2);
	$('#SiteY2').val(c.y2);
	$('#SiteW').val(c.w);
	$('#SiteH').val(c.h);
};

function initialSelection() {
	var h, w, w1, h1, left, top;
	
	w = $('#crop').width();
	h = $('#crop').height();
	
	w1 = w * 0.8;
	h1 = w1 / aspectRatioDec;
	
	if(((h / 2) - (h1 / 2)) < 0) {
		h1 = h;
		w1 = h * aspectRatioDec;
	}
	
	left = (w / 2) - (w1 / 2);
	top = (h / 2) - (h1 / 2);
	
	jcropApi.setSelect([ w/2, h/2, w/2, h/2 ]);
	
	setTimeout(function(){
		jcropApi.animateTo([ left, top, left+w1, top+h1 ]);
	}, 1000);
}

$(window).load(function() {
	<?php if($settings['hidePreview'] != true): ?>
	$('div.sidebar .module').show();
	<?php else: ?>
	$('div.sidebar').hide();		
	<?php endif;?>
	
	jcropApi = $.Jcrop('#crop', {
			onChange: showPreview,
			onSelect: showPreview,
			aspectRatio: aspectRatioFrac,
			boxWidth: boxWidth,
			boxHeight: 400
	});
	
	initialSelection();
});
 
$(document).ready(function() {
<?php if(empty($settings['hidePreview'])): ?>
	$('.sidebar').css('display', 'block');
	$('.sidebar ul li div').css({width: previewWidth + 'px', height: previewHeight + 'px', overflow: 'hidden'});
<?php endif; ?>

	$('#save_thumb').click(function() {
		var x1 = $('#SiteX1').val();
		var y1 = $('#SiteY1').val();
		var x2 = $('#SiteX2').val();
		var y2 = $('#SiteY2').val();
		var w = $('#SiteW').val();
		var h = $('#SiteH').val();
 
 
		if(x1=="" || y1=="" || x2=="" || y2==""|| w=="" || h==""){
			alert('Please choose a area to crop...');
			return false;
		}else{
			return true;
		}
	});
 
});

<?php $this->Html->scriptEnd(); ?>