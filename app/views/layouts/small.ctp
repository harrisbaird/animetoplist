<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php echo $this->Html->getPageTitle(); ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<?php
	echo $this->Minify->css(array('reset', '960', 'text', 'at', 'at.login', 'jquery.miniColors', 'buttons'));

	echo $this->Minify->write('css');

	echo $this->Minify->script(array(
		'jquery.tools.js',
		'jquery.autocomplete.js',
		//'cufon-yui.js',
		//'header.font.js',
		'jquery.carousel.min.js',
		'jquery.lazyload.js',
		'at',
		'at.small.js',
		'at.login.js',
		'at.search.js',
		'at.bugfixes.js',
		'jquery.miniColors.js'
	));

	echo $this->Minify->write('script');

	echo $scripts_for_layout;
	?>

	<?php if(!empty($loginNext)): ?>
		<script type="text/javascript">
			document.location.href = '<?php echo $loginNext ?>';
		</script>
	<?php endif; ?>

	<script type="text/javascript">Cufon.replace('h2, h3, h4');</script>
</head>
<body>

	<div class="container_12">
		<?php echo $this->element('global/header', array('featured' => false)); ?>
	</div>

	<div id="small" class="<?php if(!empty($this->Html->hasSidebar)) echo 'has-sidebar'; ?>">


			<?php
			if ($session->check('Message.flash')) {
				echo $this->Session->flash();
			}
			?>


			<?php
			//Generate the header tag
			if(!empty($this->Html->pageTitle)) {
				echo $this->Html->tag('h2', $this->Html->pageTitle);
			}

			echo $content_for_layout;
			?>

		</div> <!-- /#container-register -->
	</div> <!-- /#container -->

</body>
</html>
