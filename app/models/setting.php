<?php
class Setting extends AppModel {

	var $name = 'Setting';

	function load() {
		$settings = $this->find('all');
		
		foreach ($settings as $variable) {
			Configure::write($variable['Setting']['name'], $variable['Setting']['value']);
		}
		
		return $settings;
	}
}

?>