<?php
$seriesDefault = Configure::read('App.images.series.small');
$siteDefault = Configure::read('App.images.sites.small');

$data = array('Sites' => array(), 'Anime' => array(), 'Manga' => array());
foreach($results as $result) {
	
	switch($result['SearchIndex']['model']) {
		case 'Site':
			$image = $siteDefault;
			if($result['Site']['has_banner'] != 1) {
				$image = $siteDefault;
			}

			$data['Sites'][] = array(
				'name' => $result['Site']['official_name'],
				'html' => $this->Text->highlight($result['Site']['official_name'], $query),
				'image' => '/img/' . $image,
				'url' => Router::url(array('controller' => 'sites', 'action' => 'view', $result['Site']['slug']))
			);
			break;
		case 'SeriesTitle':
		
			$image = $seriesDefault;

			if(!empty($result['SeriesTitle']['Series']['image_filename'])) {
				$image = 'series/' . $result['SeriesTitle']['Series']['image_filename'];
			}
	
			$type = $this->At->getType($result['SeriesTitle']['Series']);
			$data[$type][] = array(
				'name' => $result['SeriesTitle']['name'],
				'html' => $this->Text->highlight($result['SeriesTitle']['name'], $query),
				'image' => '/img/' . $image,
				'url' => Router::url(array('controller' => 'series', 'action' => low($type), $result['SeriesTitle']['Series']['slug']))
			);
			break;
	}
}

echo @json_encode($data);
?>