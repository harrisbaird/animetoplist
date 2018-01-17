<?php
class Stat extends AppModel {

	var $name = 'Stat';
	var $belongsTo = array('Site');
	var $actsAs = array('Alexa');
	var $validLengths = array(
		'today' => array('time' => 'yesterday'),
		'7d' => array('time' => '-7 days'),
		'1m' => array('time' => '-1 month'),
		'3m' => array('time' => '-3 months', 'group' => 'week'),
		'6m' => array('time' => '-6 months', 'group' => 'week'),
		'1y' => array('time' => '-1 year', 'group' => 'month'),
		'max' => array('time' => '-10 years', 'group' => 'month')
	);

	/**
	 * Get statistics by duration
	 *
	 * @param integer $siteId
	 * @param string $length
	 * @param boolean $full
	 * @return array
	 */
	function getStats($siteId, $length = '7d', $full = false, $type = false) {
		if(!in_array($length, array_keys($this->validLengths))) $length = '7d';

		$dateStart = date('Y-m-d', strtotime($this->validLengths[$length]['time']));

		$stats['group'] = false;

		//Do we want full stats or sum'd ones
		if(!$full) {
			$stats['data'] = $this->findCached('first', array('fields' => array('Stat.date', 	'SUM(Stat.unique_in) as unique_in', 'SUM(Stat.total_in) as total_in', 'SUM(Stat.unique_out) as unique_out', 'SUM(Stat.total_out) as total_out', 'AVG(Stat.rank) as rank'), 'conditions' => array('Stat.site_id' => $siteId, 'Stat.date >' => $dateStart), 'group' => array('Stat.site_id'), 'order' => array('Stat.date ASC'), 'contain' => false, 'cache' => array('stats_sum_site_' . $siteId, '+24 hours')));
		} else {
			if(!empty($this->validLengths[$length]['group'])) {
				//Group by specified amount
				$group = String::insert(':type(date)', array('type' => $this->validLengths[$length]['group']));
				$stats['data'] = $this->find('all', array('fields' => array('Stat.date', 'SUM(Stat.unique_in) as unique_in', 'SUM(Stat.total_in) as total_in', 'SUM(Stat.unique_out) as unique_out', 'SUM(Stat.total_out) as total_out', 'AVG(Stat.rank) as rank'), 'conditions' => array('Stat.site_id' => $siteId, 'Stat.date >' => $dateStart), 'group' => array($group), 'order' => array('Stat.date ASC'), 'contain' => false));
				$stats['group'] = $this->validLengths[$length]['group'];
			} else {
				//No grouping, display all
				$stats['data'] = $this->find('all', array('conditions' => array('Stat.site_id' => $siteId, 'Stat.date >' => $dateStart), 'order' => array('Stat.date ASC'), 'contain' => false));
			}
		}

		return $stats;
	}

	/**
	 * Increment a stat by type and site id
	 *
	 * @param integer $siteId
	 * @param string $type
	 * @param boolean $unique
	 * @return void
	 */
	function increment($siteId, $type, $unique = false) {
		$totalType = 'Stat.total_' . $type;
		$uniqueType = 'Stat.unique_' . $type;
		$today = date("Y-m-d");

		$stat = $this->find('first', array('conditions' => array('Stat.site_id' => $siteId, 'Stat.date' => $today), 'contain' => false));
		if(empty($stat)) return false;

		$data = array($totalType => $totalType . '+1');

		if($unique) {
			$data = am(array($uniqueType => $uniqueType . '+1'), $data);
		}

		$this->updateAll($data, array('Stat.id' => $stat['Stat']['id']));
	}

	/**
	 * Create a new stat entry for today
	 *
	 * @param integer $siteId
	 * @return void
	 */
	function createStat($siteId, $alexa = false) {
		$today = date("Y-m-d");

		$data = array('site_id' => $siteId, 'date' => $today);
		$this->id = null;

		if(!empty($alexa)) {
			$data['pageviews'] = $alexa['pageviews'];
			$data['reach'] = $alexa['reach'];
			$data['links'] = $alexa['links'];
		} else {
			$yesterday = $this->find('first', array('conditions' => array('Stat.site_id' => $siteId), 'order' => 'date DESC'));

			$data['pageviews'] = $yesterday['Stat']['pageviews'];
			$data['reach'] = $yesterday['Stat']['reach'];
			$data['links'] = $yesterday['Stat']['links'];
		}

		$this->create();
		$this->save($data, false);
	}

}
?>
