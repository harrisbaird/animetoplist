<?php $this->Html->setHeader(ucwords($type) . ' on ' . $site['Site']['official_name']); ?>

<?php
$scores = array(
	'37' => 'Very poor',
	'40' => 'Poor',
	'45' => 'Below average',
	'55' => 'Average',
	'60' => 'Good',
	'63' => 'Great',
	'100' => 'Amazing'
);

$ratings = array(
	'6' => '< 5',
	'10' => '< 10',
	'21' => '10-20',
	'31' => '20-30',
	'41' => '30-40',
	'51' => '40-50',
	'61' => '50-60',
	'71' => '60-70',
	'81' => '70-80',
	'91' => '80-90',	
	'100' => '>100',
	'9999' => '>100'
);

$language = array('', 'Subbed', 'Dubbed', 'Raw');
?>

<?php if($site['Site']['forced_update'] < date("Y-m-d H:i:s")): ?>
<div class="force-update"><?php echo $this->element('buttons/button_submit', array('type' => 'link', 'text' => 'Force an update', 'url' => array('action' => 'update', $type, $site['Site']['slug']))); ?></div>
<?php endif; ?>

<h3>Rated series</h3>

<table style="margin-bottom: 40px;">
	<thead>
		<tr>
			<th>Name</th>
			<th>Rank</th>
			<th>Ratings</th>
			<th>Actions</th>
		</tr>
	</thead>
	<?php foreach($rated as $series): ?>
	<tr class="<?php echo $this->Cycle->cycle('odd', 'even'); ?>" style="<?php if($series['SeriesSite']['is_disabled']) echo "background: #f8dfdf !important"; ?>">
		<td><?php echo $series['Series']['name']; ?><br /><a href="<?php echo $series['SeriesSite']['url']; ?>" style="font-size: 11px;" target="_blank"><?php echo $series['SeriesSite']['url']; ?></a></td>
		<td><?php
			foreach($scores as $score => $text) {
				if($series['SeriesSite']['bayesian_rating'] < $score) {
					echo $text;
					break;
				}
			}
		?></td>
		<td><?php
			foreach($ratings as $rating => $text) {
				if($series['SeriesSite']['ratings_count'] < $rating) {
					echo $text;
					break;
				}
			}
		?></td>
		<td>
			<?php
			$disableText = $series['SeriesSite']['is_disabled'] ? 'Re-enable' : 'Mark as incorrect';
			echo $this->Html->link($disableText, array('action' => 'disableSeries', $type, $site['Site']['slug'], $series['SeriesSite']['id']));
			?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<h3>Currently unrated</h3>

<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Actions</th>
		</tr>
	</thead>
	<?php foreach($unrated as $series): ?>
	<tr class="<?php echo $this->Cycle->cycle('odd', 'even'); ?>" style="<?php if($series['SeriesSite']['is_disabled']) echo "background: #f8dfdf !important"; ?>">
		<td><?php echo $series['Series']['name']; ?><br /><a href="<?php echo $series['SeriesSite']['url']; ?>" style="font-size: 11px;" target="_blank"><?php echo $series['SeriesSite']['url']; ?></a></td>
		<td>
			<?php
			$disableText = $series['SeriesSite']['is_disabled'] ? 'Re-enable' : 'Mark as incorrect';
			echo $this->Html->link($disableText, array('action' => 'disableSeries', $type, $site['Site']['slug'], $series['SeriesSite']['id']));
			?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<style>
  table td {
    line-height: 14px;
    padding: 6px;
  }
</style>
