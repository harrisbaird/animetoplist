<div id="footer">

	<div class="grid_8 links">
		<ul>
                        <?php foreach($footerSites as $site): ?>
                            <li><?php echo $this->Html->link($site['Site']['official_name'], $site['Site']['url']); ?></li>
                        <?php endforeach; ?>
			<li><?php echo $this->Html->link('Daily Tee Deals', 'http://dailyteedeals.com'); ?></li>
		</ul>
	</div>
</div> <!-- /#footer-top -->

<style>
#footer { background: #1b1b1b url(/img/footer-bg.jpg); }
</style>
