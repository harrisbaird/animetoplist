<?php
class CronShell extends Shell {
	var $uses = array('SeriesSite', 'Site');
	
	function main() {

		$this->SeriesSite->updateAll(array('mean_rating' => 70, 'bayesian_rating' => 70), array('site_id' => 1650));
		$this->SeriesSite->updateAll(array('mean_rating' => 70, 'bayesian_rating' => 70), array('site_id' => 2015));
		$this->SeriesSite->updateAll(array('mean_rating' => 70, 'bayesian_rating' => 70), array('site_id' => 1821));

		echo 'Running Cron';
		if(empty($this->args[0])) $this->_stop();
		
		set_time_limit(0);

		$this->{$this->args[0]};
	}
	
	/**
	 * Convinience function for QueuedTask::createJob
	 *
	 * @param string $task_name
	 * @param string $data
	 * @return boolean
	 */
	function queue($task_name, $data = array()) {
		App::import('Model', 'Queue.QueuedTask');
		$QueuedTask = new QueuedTask;
		return $QueuedTask->createJob($task_name, $data);
	}

	function scrapeSites() {
		$sites = $this->Site->find('list', array('conditions' => array('or' => array('Site.streaming_url != ' => '', 'Site.manga_url != ' => '')), 'fields' => array('Site.id'), 'contain' => false));
		foreach($sites as $siteId) {
			$this->queue('scrape_sites', array('siteId' => $siteId));
		}
		$this->queue('scrape_sites', array('siteId' => 1820));
	}
	
	function scrapeAnn() {
		$this->queue('scrape_ann');
	}
	
	function updateSiteCount() {
		//Total count
		$siteCount = array();
		$links = $this->SeriesSite->find('all', array('fields' => array('SeriesSite.series_id', 'COUNT(*) as site_count'), 'conditions' => array('SeriesSite.is_active' => 1), 'group' => 'SeriesSite.series_id', 'contain' => false));
		foreach($links as $link) {
			$linkId = $link['SeriesSite']['series_id'];
			$siteCount[$linkId]['id'] = $linkId;
			$siteCount[$linkId]['site_count'] = $link['SeriesSite']['site_count'];
		}
		
		//Newly added
		$newCount = array();
		$links = $this->SeriesSite->find('all', array('fields' => array('SeriesSite.series_id', 'COUNT(*) as site_count'), 'conditions' => array('SeriesSite.is_active' => 1, 'SeriesSite.created >'  => date('Y-m-d', strtotime("-5 days"))), 'group' => 'SeriesSite.series_id', 'contain' => false));
		foreach($links as $link) {
			$linkId = $link['SeriesSite']['series_id'];
			$newCount[$linkId]['id'] = $linkId;
			$newCount[$linkId]['new_count'] = $link['SeriesSite']['site_count'];
		}
		
		$data = Set::merge($siteCount, $newCount);
		
		$this->SeriesSite->Series->index = false;
		$this->SeriesSite->Series->saveAll($data);
	}
	
	function updateStats() {
		$this->queue('update_stats');
	}

	function updateRanks() {
		//$this->Site->updateRanks();
	}

}
?>
