<div id="stats">
<!--
	<div class="stats-header grid_2 alpha"><strong>Current Rank</strong><em><?php echo $site['Site']['rank'] != 0 ? $site['Site']['rank'] : 'n/a' ?></em></div>
	<div class="stats-header grid_2"><strong>Awards</strong><em>0</em></div>
	<div class="stats-header grid_2 omega"><strong>Something else</strong><em>50</em></div>-->

	<div class="grid_3 alpha">
		<h3>Today so far</h3>
		<table>
			<thead>
				<tr>
					<th>Type</th>
					<th>Unique</th>
					<th>Total</th>
				</tr>
			</thead>
			<tr>
				<td>Users in</td>
				<td><?php echo !empty($today['data']['Stat']['unique_in']) ? $today['data']['Stat']['unique_in'] : 0; ?></td>
				<td><?php echo !empty($today['data']['Stat']['total_in']) ? $today['data']['Stat']['total_in'] : 0; ?></td>
			</tr>
			<tr>
				<td>Users out</td>
				<td><?php echo !empty($today['data']['Stat']['unique_out']) ? $today['data']['Stat']['unique_out'] : 0; ?></td>
				<td><?php echo !empty($today['data']['Stat']['total_out']) ? $today['data']['Stat']['total_out'] : 0; ?></td>
			</tr>
		</table>
	</div>
	<div class="grid_3 omega">
		<h3>Past Week</h3>
		<table>
			<thead>
				<tr>
					<th>Type</th>
					<th>Unique</th>
					<th>Total</th>
				</tr>
			</thead>
			<tr>
				<td>Users in</td>
				<td><?php echo !empty($week['data']['Stat']['unique_in']) ? $week['data']['Stat']['unique_in'] : 0; ?></td>
				<td><?php echo !empty($week['data']['Stat']['total_in']) ? $week['data']['Stat']['total_in'] : 0; ?></td>
			</tr>
			<tr>
				<td>Users out</td>
				<td><?php echo !empty($week['data']['Stat']['unique_out']) ? $week['data']['Stat']['unique_out'] : 0; ?></td>
				<td><?php echo !empty($week['data']['Stat']['total_out']) ? $week['data']['Stat']['total_out'] : 0; ?></td>
			</tr>
		</table>
	</div>

	<?php echo $this->element('stats/stats', array('siteId' => $site['Site']['id'], 'data' => $month, 'type' => 'rank', 'duration' => '1m')); ?>

</div>
