<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title><?php echo $site['Site']['official_name']; ?> - Anime Toplist</title>
		<script type="text/javascript">
			var runSurvey = true;
			var siteName = '<?php echo $site['Site']['official_name']; ?>';
			var type = 'site';
			var siteUrl = '<?php echo $site['Site']['url']; ?>';
		</script>
		<?php
		echo $this->Minify->css(array('out'));
		echo $this->Minify->write('css');

		echo $this->Minify->script(array(
			'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js',
			'jquery.cookie.js',
			'at.out.js'
		));
		echo $this->Minify->write('script');
		?>

	</head>
	<body>
		<div id="bar">
			<div id="close"><a href="<?php echo $site['Site']['url']; ?>" rel="nofollow"><span>Close</span></a></div>

			<div id="logo"><a href="<?php echo Router::url('/'); ?>"><span>Anime Toplist</span></a></div>

			<div id="current">
				<div class="site"><?php echo $site['Site']['official_name']; ?></div>
				<div class="question">What do you think of this site?</div>
			</div>

			<?php echo $this->element('out/comment', array('site' => $site)); ?>
		</div>

		<iframe id="iframe" frameborder="0"></iframe>
	</body>
</html>
