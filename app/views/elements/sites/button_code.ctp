<p>Add one of the following to your site, you will need to do so for you site to be ranked.</p>

<?php
$altText = array(
'Anime Toplist - An Anime toplist / topsite for ranking the top Anime sites on the Internet',
'Watch Anime online', 'Read Manga online', 'Watch Naruto', 'Streaming Anime', 'Anime Toplist',
'Anime Topsite', 'Top Anime Sites', 'Streaming Naruto', 'Anime Rankings',
'Anime Toplist - An Anime toplist / topsite for ranking the top Anime sites on the Internet'
);

shuffle($altText);
?>

<div class="link-code module">
	<h3>88x31 button</h3>
	<ul>
		<li class="link-code-preview"><?php
		$url = Router::url('/', true);
		echo $this->Html->link($this->Html->image('http://animetoplist.org/img/button.gif?id=' . $id, array('class' => 'no-lazyload')), '#', array('escape' => false));
		?></li>
		<li>
			<div class="code-box">
				<?php echo h($this->Html->link($this->Html->image('http://animetoplist.org/img/button.gif?id=' .$id, array('alt' => $altText[0])), $url, array('escape' => false)));?>
			</div>
		</li>
	</ul>
</div>

<div class="link-code module">
	<h3>Text link</h3>
	<ul>
		<li class="link-code-preview"><a href="#">Anime Toplist</a></li>
		<li>
			<div class="code-box">
 				&lt;a href=&quot;http://animetoplist.org&quot; title=&quot;<?php echo $altText[0]; ?>&quot;&gt;Anime Toplist&lt;/a&gt;
			</div>
		</li>
	</ul>
</div>
