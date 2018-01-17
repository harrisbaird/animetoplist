<?php
class FooterComponent extends Object {
	var $controller = null;
	var $Site;
	var $Stat;

	function initialize(&$controller) {
		$this->controller =& $controller;

		App::import('Model', 'Site');
		App::import('Model', 'Stat');

		$this->Site = new Site;
		$this->Stat = new Stat;
	}

	function beforeRender() {
                $footerSites = $this->Site->find('all', array('conditions' => array('Site.is_footer_link' => true), 'limit' => 3, 'order' => 'rand()', 'contain' => false));
                $this->controller->set(compact('footerSites'));
	}
}
?>
