<?php
if(empty($data['SeriesSite']['url'])) {
	$data['SeriesSite']['url'] = $data['Site']['url'];
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title><?php
		if($data['Series']['is_anime']) {
			echo String::insert('Watch :series streaming on :site - Anime Toplist', array('series' => $data['Series']['name'], 'site' => $data['Site']['official_name']));
		} else {
			echo String::insert('Read :series on :site - Anime Toplist', array('series' => $data['Series']['name'], 'site' => $data['Site']['official_name']));
		}
		?></title>
		<?php
		echo $this->Minify->css(array('out'));
		echo $this->Minify->write('css');
		?>
		<script type="text/javascript">
			var runSurvey = true;
			var siteName = '<?php echo $data['Site']['official_name']; ?>';
			var ss = <?php echo $data['SeriesSite']['id']; ?>;
			var url = '<?php echo Router::url(array('controller' => 'out', 'action' => 'rate')); ?>';
			var siteUrl = '<?php
				$url = trim($data['SeriesSite']['url']);
				if($url == 'http://') $url = '';
				echo !empty($url) ? $data['SeriesSite']['url'] : $data['Site']['url']; 
			?>';
			var type = '<?php echo $data['Series']['is_anime'] ? 'anime' : 'manga'; ?>';
			var skip_comment = <?php echo $data['Site']['is_premium'] && $data['Site']['disable_comments'] ? 'true' : 'false' ?>;
		</script>
		<?php
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
			<div id="close"><a href="<?php echo $data['SeriesSite']['url']; ?>" rel="nofollow"><span>Close</span></a></div>

			<div id="logo"><a href="<?php echo Router::url('/'); ?>"><span>Anime Toplist</span></a></div>

			<div id="current">
				<div class="site"><?php echo $data['Site']['official_name']; ?></div>
				<div class="question"></div>
				<div class="question-comment">What do you think of this site?</div>
			</div>

			<ul id="options">

			</ul>

			<?php echo $this->element('out/comment', array('site' => $data)); ?>

			<ul id="extra">
				<?php if(!$data['Site']['is_premium'] && !$data['Site']['disable_comments']): ?>
				<li><a href="#" id="comment-link">Add comment</a></li>
				<?php endif; ?>
				<li><a href="#" id="rating-link">Add rating</a></li>
			</ul>
		</div>

		<iframe id="iframe" frameborder="0"></iframe>
	</body>
</html>
