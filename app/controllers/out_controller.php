<?php
class OutController extends AppController {
	
	var $name = 'Out';
	
	var $uses = array('Site', 'SeriesSite');
	var $components = array('RequestHandler');
	
	function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('*');
	}

	function watch($seriesSlug, $siteSlug) {
		$this->setAction('series', $seriesSlug, $siteSlug);
	}
	
	function read($seriesSlug, $siteSlug) {
		$this->setAction('series', $seriesSlug, $siteSlug);
	}
	
	/**
	 * Redirect directly to a site
	 *
	 * @param integer $id 
	 * @return void
	 */
	function site($slug) {
		if(empty($slug)) die('Invalid site');
		
		$this->layout = false;
		
		$site = $this->Site->find('first', array('conditions' => array('Site.slug' => $slug), 'contain' => false));
		if(empty($site)) die('Invalid site');

		if($site['Site']['is_verified'] != 1) {
                        $this->showMessage('Invalid site', '', 'bad');
                        $this->cakeError('error404');
		}


		if(($site['Site']['is_premium'] && $site['Site']['disable_comments']) || $site['Site']['disable_bar']) {
			$this->redirect($site['Site']['url']);
		}

		$this->redirect($site['Site']['url']);
		
		//$this->__logStat($site['Site']['id']);
		
		$this->set(compact('site'));
	}
	
	/**
	 * Display a site linked to a series
	 * Also displays the bar so users can
	 * provide ratings.
	 *
	 * @param integer $id 
	 * @return void
	 */
	function series($seriesSlug, $siteSlug) {
		if(empty($seriesSlug) || empty($siteSlug)) die('Bad id');
		
		$this->layout = false;
		
		$data = $this->SeriesSite->find('first', array('conditions' => array('Series.slug' => $seriesSlug, 'Site.slug' => $siteSlug), 'contain' => array('Site', 'Series')));
		if(empty($data)) die('Invalid site');
		

		if($data['Site']['is_verified'] != 1) {
				$this->showMessage('Invalid site', '', 'bad');
				$this->cakeError('error404');
		}

		//Fix localhost url
		if(strpos($data['SeriesSite']['url'], 'localhost') !== false) {
			$data['SeriesSite']['url'] = $data['Site']['url'];
		}
		
		//Fix url containing no http
		if(substr($data['SeriesSite']['url'], 0, 4) !== 'http') {
			$data['SeriesSite']['url'] = 'http://' . $data['SeriesSite']['url'];
		}

		if($data['Site']['disable_bar']) {
				$this->redirect($data['SeriesSite']['url']);
		}

		$this->redirect($data['SeriesSite']['url']);
		$this->set(compact('data'));
	}
	
	/**
	 * Rate a series
	 *
	 * @return void
	 */
	function rate($id, $category, $value) {
		$this->autoRender = false;
		
		if(empty($id) || empty($category) || empty($value)) return false;
		
		$this->Rating->ipAddress = $this->RequestHandler->getClientIp();
		
		$this->Rating->add($id, $category, $value);
	}
	
	function initialUpdate() {
		$this->autoRender = false;
		$sites = $this->Site->find('list');
		foreach($sites as $site) {
			$this->Rating->updateSite($site);
		}
	}

}
?>
