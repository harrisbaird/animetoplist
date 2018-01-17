<?php $this->Html->setHeader('Dead sites'); ?>

<p>This page contains a list of sites which have been marked as dead and removed from the rankings and Anime pages.</p>

<style>
#chart_div td {
padding: 0;
border: 0;
}
</style>

<div id='chart_div' style='width: 700px; height: 240px; padding: 0; margin-bottom: 30px; overflow: hidden;'></div>

<table id="sitesTable">
        <thead>
                <tr>
                        <th>Site</th>
                        <th>Anime</th>
                        <th>Manga</th>
			<th>Comments</th>
			<th>Date added</th>
                        <th>Date removed</th>
                        <th>Reason</th>
                </tr>
        </thead>


<?php foreach($sites as $site): ?>

	<tbody>
		<tr class="<?php echo $this->Cycle->cycle('odd', 'even'); ?>">
			<td><?php echo $this->Html->link($site['Site']['official_name'], array('controller' => 'sites', 'action' => 'view', $site['Site']['slug'])); ?></td>
			<td><?php echo !empty($site['Site']['streaming_url']) ? 'Yes' : 'No'; ?></td>
			<td><?php echo !empty($site['Site']['manga_url']) ? 'Yes' : 'No'; ?></td>
			<td><?php echo $site['Site']['comment_count']; ?></td>
			<td><?php echo date('M d, Y', strtotime($site['Site']['created']));  ?></td>
			<td><?php echo date('M d, Y', strtotime($site['Site']['dead_date']));  ?></td>
			<td><?php echo $site['Site']['dead_reason'] == 1 ? 'Domain expired' : 'Parked domain' ?></td>
		</tr>
	</tbody>

<?php endforeach; ?>
</table>

<h3>Recently inaccessible</h3>

<p>Sites which still appear in the rankings but were down recently.</p>

<table id="sitesTable">
        <thead>
                <tr>
                        <th>Site</th>
                        <th>Anime</th>
                        <th>Manga</th>
                        <th>Comments</th>
                        <th>Date added</th>
                </tr>
        </thead>


<?php foreach($pending as $site): ?>

        <tbody>
                <tr class="<?php echo $this->Cycle->cycle('odd', 'even'); ?>">
                        <td><?php echo $this->Html->link($site['Site']['official_name'], array('controller' => 'sites', 'action' => 'view', $site['Site']['slug'])); ?></td>
                        <td><?php echo !empty($site['Site']['streaming_url']) ? 'Yes' : 'No'; ?></td>
                        <td><?php echo !empty($site['Site']['manga_url']) ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $site['Site']['comment_count']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($site['Site']['created']));  ?></td>
                </tr>
        </tbody>

<?php endforeach; ?>
</table>


    <script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
      google.load('visualization', '1', {'packages':['annotatedtimeline']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Date');
        data.addColumn('number', 'Total sites');
        data.addColumn('string', 'title1');
        data.addColumn('string', 'text1');
        data.addColumn('number', 'Streaming sites');
        data.addColumn('string', 'title2');
        data.addColumn('string', 'text2');

        data.addRows([
            [new Date(2010, 12 ,25), 0, undefined, undefined, 0, undefined, undefined],
        ]);

        <?php
            foreach($site_count as $key => $count):
            $date = strtotime($key);
        ?>
        data.addRows([
          [new Date(<?php echo date('Y', $date); ?>, <?php echo date('n', $date); ?>, <?php echo date('d', $date); ?>), <?php echo $count['total']; ?>, undefined, undefined, <?php echo $count['anime']; ?>, undefined, undefined]
        ]);
        <?php endforeach; ?>

        var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
        chart.draw(data, {displayAnnotations: false});
      }
    </script>
