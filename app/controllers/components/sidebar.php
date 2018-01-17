<?php
class SidebarComponent extends Object {
	var $controller = null;
	var $Site;
	var $Series;

	function initialize(&$controller) {
		$this->controller =& $controller;
		
		App::import('Model', 'Site');
		App::import('Model', 'Series');
		
		$this->Site = new Site;
		$this->Series = new Series;
	}
	
	function beforeRender() {
		//$boostedSites = $this->Site->find('all', array('conditions' => array('Site.is_boosted' => 5), 'limit' => '1', 'order' => 'rank ASC', 'contain' => false));
		$premiumSites = $this->Site->find('all', array('conditions' => array('Site.is_premium' => 1), 'contain' => false));
		$featuredSites = $this->Site->find('all', array('conditions' => array('Site.is_premium' => 1), 'order' => 'rank ASC', 'contain' => false));
		$popularAnime = $this->Series->find('all', array('order' => 'Series.site_count DESC', 'limit' => '0,18', 'contain' => false));
		$upcomingAnime = $this->Series->find('all', array('order' => 'Series.new_count DESC', 'limit' => '0,5', 'contain' => false));
		
		$this->controller->set(compact('boostedSites', 'premiumSites', 'featuredSites', 'popularAnime', 'upcomingAnime'));
	}
}
?>
