<?php
class FeaturedComponent extends Object {
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
		$featuredSeries = $this->Series->find('all', array('conditions' => array('Series.image_filename !=' => '', 'Series.site_count >=' => '20'), 'limit' => 3, 'order' => 'rand()', 'contain' => false));
		$this->controller->set(compact('featuredSeries'));
	}
}
?>
