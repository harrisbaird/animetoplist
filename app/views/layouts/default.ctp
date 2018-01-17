<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta property="og:title" content="<?php echo $this->Html->pageHeader; ?>"/>
		<meta property="og:site_name" content="Anime Toplist"/>
		<meta property="og:image" content="//animetoplist.org/img/icons/facebook.jpg"/>

		<title><?php echo $this->Html->getPageTitle(); ?></title>
		<link rel="shortcut icon" href="/favicon.ico" />
		<?php
		$cssArr = array('reset', '960', 'text', 'at', 'at.login', 'buttons');

		foreach($cssArr as $css) {
                        echo '<link href="/css/' . $css . '.css" rel="stylesheet" type="text/css" />';
                }

		?>

		<?php if(!empty($loginNext)): ?>
			<script type="text/javascript">
				document.location.href = '<?php echo $loginNext ?>';
			</script>
		<?php endif; ?>

	</head>
	<body>
		<div class="container_12">
			<?php
			$featured = $this->params['controller'] == 'sites' && $this->params['action'] == 'index' ? true : false;
			echo $this->element('global/header', array('featured' => $featured));
			?>

			<div id="main">
				<?php $contentClass = !empty($this->plugin) ? $this->plugin : 'none'; ?>
				<div id="content" class="grid_<?php echo !empty($grid_for_layout) ? $grid_for_layout : 9; ?> plugin_<?php echo $contentClass; ?>">

					<?php
					//Display any flash messages
					if ($this->Session->check('Message.flash')) {
						echo $this->Session->flash();
					}

					//Allow content to be placed above the header
					if(!empty($this->Html->headerContent)) {
						echo $this->Html->headerContent;
					}

					//Generate the header tag
					if(!empty($this->Html->pageTitle)) {
						echo $this->Html->tag('h2', $this->Html->pageHeader);
					}

					echo $content_for_layout;
					?>

				</div>

				<?php
					if(!empty($usersSidebar)) {
						echo $this->element('users/sidebar');
					}
				?>

				<div class="sidebar grid_3">

				<?php
					$this->Sidebar->options = array(
						'sidebar_class' => 'sidebar grid_3',
						'sidebox_class' => 'module icons',
						'title_tag' => 'h2'
					);

					if(!empty($premiumSites)) {
						$this->Sidebar->addBox(array('class' => 'premium-text', 'title' => 'Premium Sites', 'element'=>'sidebar/premium_text_links', 'params' => array('premium' => $premiumSites, 'boosted' => $boostedSites)));
					}

					//$this->Sidebar->addBox(array('title' => 'Streaming Anime', 'element'=>'sidebar/anime_list', 'class' => 'streaming-message'));
					$this->Sidebar->addBox(array('title' => 'Popular Anime', 'element'=>'sidebar/icon_links', 'params' => array('data' => $popularAnime, 'appSettings' => $appSettings)/*, 'more' => array('url' => '/')*/));
					//$this->Sidebar->addBox(array('title' => 'Upcoming Anime', 'element'=>'sidebar/icon_links', 'params' => array('data' => $upcomingAnime)));

					echo $this->Sidebar->getSidebar();

					//echo $this->element('sidebar/facebook_like');
				?>

				</div>
			</div> <!-- #main -->

			<?php echo $this->element('global/footer'); ?>
		</div>

		<div id="fb-root"></div>

		<?php if(!empty($userData) && $userData['User']['username'] == 'DanCake') echo $this->element('sql_dump'); ?>

                <link href='//fonts.googleapis.com/css?family=Duru+Sans' rel='stylesheet' type='text/css'>
                <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
                <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>


                <?php
//                $bundleFu->start();
                $jsArr = array(
                        'jquery.tools.js',
                        'jquery.autocomplete.js',
                        //'cufon-yui.js',
                        //'header.font.js',
                        'jquery.carousel.min.js',
                        'jquery.lazyload.js',
                        'jquery.tablesorter.min.js',
                        'at.js',
                        'at.login.js',
                        'at.search.js',
                        'at.bugfixes.js'
                );

                foreach($jsArr as $js) {
                        echo '<script src="/js/' . $js . '"></script>';
                }

//                $bundleFu->end();
//                echo $bundleFu;

                echo $scripts_for_layout;
                ?>


		<script type="text/javascript">

			$("img.lazy").lazyload({  effect : "fadeIn" });
			//Cufon.now();
			//Facebook API
			window.fbAsyncInit = function() {
				FB.init({appId: '103325889708967', status: true, cookie: true, xfbml: true});
			};

			(function() {
				var e = document.createElement('script'); e.async = true;
				e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
				document.getElementById('fb-root').appendChild(e);
			}());

$(function() {
        $('#tabs').tabs();
        $('.delete-comment').click(function (e) {
                e.preventDefault();
                var answer = confirm("Are you sure you want to delete this comment?");
                if(answer) {
                        $(this).parentsUntil('.comment-list').fadeOut();
                        var deleteurl = $(this).attr('href');
                        $.ajax({
                                url: deleteurl
                        });
                }
        });
});
</script>
		</script>

	</body>
</html>
