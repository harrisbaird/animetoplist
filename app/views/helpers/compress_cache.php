<?php
class CompressCacheHelper extends AppHelper {
	var $helpers = array('Session');
	var $cachableControllers = array('series', 'sites');
	var $isFlash = false;
	
	function beforeRender() {
		if($this->Session->read('Message')) {
			$this->isFlash = true;
		}
	}
  
		
	function afterLayout() {
		parent::afterLayout();
		
    	$view =& ClassRegistry::getObject('view');
		$view->output = preg_replace('/(?:(?)|(?))(\s\s+)(?=\<\/?)/','', $view->output);
		
		//Cache the html
		if(in_array($this->params['controller'], $this->cachableControllers) && empty($view->viewVars['userData']) && Configure::read('debug') == 0 && !$this->isFlash) {
			Cache::set(array('duration' => '+2 Hours'));
			
			$cacheKey = 'html_' . md5($this->here);
			
			Cache::write($cacheKey, $view->output);
		}
	}
}
?>