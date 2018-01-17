<?php
class MinifyHelper extends AppHelper {
	var $helpers = array('Html');

	var $localCss = array();
	var $localScript = array();
	var $externalCss = array();
	var $externalScript = array();

	var $cssHtml = '<link type="text/css" rel="stylesheet" href="%s" />';
	var $scriptHtml = '<script type="text/javascript" src="%s"></script>';
	var $urlNames = array('css' => 'css', 'script' => 'js');

	function beforeRender() {
		parent::beforeRender();

		if(!$this->__isDebug()) {
			ob_start('ob_gzhandler');
		}
	}

	function css($files) {
		if(!$this->__isDebug()) {
			$this->__build($files, 'css');
		} else {
			return $this->Html->css($files);
		}
	}

	function script($files) {
		if(!$this->__isDebug()) {
			$this->__build($files, 'script');
		} else {
			return $this->Html->script($files);
		}
	}

	function write($type) {
		$local = 'local' . ucwords($type);
		$external = 'external' . ucwords($type);

		$htmlTag = $type . 'Html';

		$data = '';

		if(!empty($this->{$external})) {
			$data .= $this->Html->{$type}($this->{$external});
		}

		if(!empty($this->{$local})) {
			$localUrl = '/min/' . $this->urlNames[$type] . '/v' . Configure::read('App.resources.version') . '|' . implode('|', $this->{$local});
			$data .= sprintf($this->{$htmlTag}, $localUrl);
		}

		return $data;
	}

	function __build($files, $type) {
		$local = 'local' . ucwords($type);
		$external = 'external' . ucwords($type);
		$strip = array('.css', '.js');

		foreach($files as $file) {
			//External or local file
			if(strpos($file, 'http://') !== false) {
				//External file
				$this->{$external}[] = $file;
			} else {
				//Local file
				$file = str_replace($strip, '', $file);
				$this->{$local}[] = $file;
			}
		}
	}

	function __isDebug() {
		return true;
		//return Configure::read('debug') > 0 ? true : false;
	}
}
?>