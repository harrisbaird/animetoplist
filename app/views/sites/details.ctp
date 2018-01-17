<?php 
$title = !empty($site['Site']['id']) ? 'Editing ' . $site['Site']['official_name'] : 'Add Site';
$this->Html->setHeader($title);
?>

<?php
$helpData = array(
	'class' => 'sidebar-edit',
	'items' => array(
		array(
			'header' => 'Site URL',
			'hoverClass' => '#SiteUrl',
			'location' => '0',
			'contents' => array(
				'Enter the URL of the site you wish to add.',
				'<strong>Anime sites only</strong> - All other sites will be removed',
			)
		),
		array(
			'header' => 'Official Name',
			'hoverClass' => '#SiteOfficialName',
			'location' => '0',
			'contents' => array(
				'Enter the actual name of your site and not what your sites contains.',
				'<strong>For example:</strong>',
				'<span class="good"><span>Good</span>Anime Toplist</span>',
				'<span class="bad"><span>Bad</span>Anime Toplist - A site containing...</span>',
				'<span class="bad"><span>Bad</span>Streaming Anime</span>'
			)
		),
		array(
			'header' => 'Description',
			'hoverClass' => '#SiteDescription',
			'location' => '0',
			'contents' => array(
				'Describe a little about your site, what makes it better than others?'
			)
		),
		array(
			'header' => 'Banner',
			'hoverClass' => '#SiteBanner, .banner',
			'location' => '0',
			'contents' => array(
				'Upload an image which defines your site.',
				'Anime Toplist uses <strong>80x80 pixel</strong> banners, you can crop your banner on the next step.',
				'For a high quality banner, we recommended that you create a 200x200 image, it will be resized automatically with no cropping required.',
				'Banners cannot contain nudity of any kind.'
			)
		),
		array(
			'header' => 'Site Ownership',
			'hoverClass' => '#SiteSiteOwner, label[for=SiteSiteOwner]',
			'location' => '0',
			'contents' => array(
				'<strong>You must</strong> own or moderate this site in order to add it to Anime Toplist.'
			)
		),
                array(
                        'header' => 'Disable Comments',
                        'hoverClass' => '#SiteDisableComments, label[for=SiteDisableComments]',
                        'location' => '0',
                        'contents' => array(
                                'Completely disables comments including hiding the comment bar when visiting this site. Voting is still enabled. <div style="font-weight: bold; color: red;">Premium Feature</div>'
                        )
                ),
                array(
                        'header' => 'Disable Bar',
                        'hoverClass' => '#SiteDisableBar, label[for=SiteDisableBar]',
                        'location' => '0',
                        'contents' => array(
                                'Disable the bar which appears at the top when visiting this site. <strong>Prevents users from voting.</strong><div style="font-weight: bold; color: red;">Premium Feature</div>'
                        )
                ),
	)
);

  echo $this->element('global/form_help', array('data' => $helpData));

?>

<?php echo $this->Form->create('Site', array('type' => 'file', 'url' => $this->passedArgs)); ?>
<?php
		echo $this->Form->input('Site.url', array('label' => 'Site URL'));
?>
	<div id="hentai-msg">Please consider also adding your site to <a href="http://hentaitoplist.org" target="_blank">Hentai Toplist</a> which provides a much more complete and accurate Hentai listings.</div>
<?php
		echo $this->Form->input('Site.official_name');
		echo $this->Form->input('Site.description');	
		echo $this->Form->input('Site.banner', array('type' => 'file'));
		if(!empty($hasBanner)) {
			echo $hasBanner;
		}

		if(empty($id)) {
			echo $this->Form->input('Site.site_owner', array('type' => 'checkbox', 'label' => 'I own or help moderate this site'));
		}
		
		echo $this->Form->hidden('Site.step', array('value' => 'details'));
		
		if($site['Site']['is_premium']) {
		  $ranking_box = $this->element('global/ranking_box', array('site' => $site, 'no_grid' => true));
		  echo "<div id=\"ranking-preview\">" . $ranking_box . "</div>";
      echo '<h3>Customize your listing</h3>';
      echo $this->Form->input('Site.premium_box_bg', array('label' => 'Background color'));
      echo $this->Form->input('Site.premium_box_text', array('label' => 'Text color'));
      echo $this->Form->input('Site.premium_box_title', array('label' => 'Title color'));
    }
	?>
	
	<div class="submit">
		<?php echo $this->element('buttons/button_submit', array('text' => 'Continue')); ?>
	</div>
	
<script type="text/javascript">
	$(document).ready( function() {
	  var ranking_text = '.ranking-box .description, .ranking-box a, .ranking-box li'
	  
	  if($("#SitePremiumBoxBg").val().length == 0) {
	    $("#SitePremiumBoxBg").val('#C30000');
	  }

		$("#SitePremiumBoxBg").miniColors({
		  change: function(hex, rgb) { $('.ranking-box').css('background-color', hex); }
		});
		$("#SitePremiumBoxText").miniColors({
		  change: function(hex, rgb) { $(ranking_text).css('color', hex); }
		});
		$("#SitePremiumBoxTitle").miniColors({
		  change: function(hex, rgb) { $('.ranking-box h4').css('color', hex); Cufon.refresh(); }
		});
	});
</script>

<?php echo $this->Form->end();?>

<style type="text/css">
#hentai-msg {
  width: 425px;
  margin-bottom: 10px;
  border: 1px solid #000;
  padding: 5px;
  display: none;
}
</style>
