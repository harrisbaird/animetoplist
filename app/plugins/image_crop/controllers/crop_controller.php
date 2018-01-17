<?php
class CropController extends ImageCropAppController {
	
	var $name = 'Crop';
	var $uses = null;
	var $components = array('ImageCrop');
	
	function beforeFilter() {
		parent::beforeFilter();
		
		$this->Security->disabledFields = array('Site.x1', 'Site.y1', 'Site.x2', 'Site.y2', 'Site.w', 'Site.h');
	}
	
	function crop() {
		$settings = $this->Session->read('crop');
		if(!$this->Session->check('crop')) {
			$this->redirect(array('plugin' => false, 'controller' => 'users', 'action' => 'profile'));
		}
		
		//Is the image already square
		if(($settings['aspectRatio']['decimal'] == 1) && ($settings['image']['height'] == $settings['image']['width'])) {
			$size = $settings['image']['height'];
			
			$data = array(
				'Site' => array(
					'x1' => '0',
					'y1' => 0,
					'x2' => $size,
					'y2' => $size,
					'w' => $size,
					'h' => $size
				)
			);
			
			$this->ImageCrop->process($data);
			
			$this->redirect($settings['afterComplete']['url']);
		}
		
		//Skip if cropping disabled
		if(isset($settings['crop']) && $settings['crop'] == false) {
			$this->process();
		}
		
		$this->layout = 'small';
		$this->set(compact('settings'));
	}
	
	function process() {
		$this->ImageCrop->process($this->data);
		
		$settings = $this->Session->read('crop');
		$this->redirect($settings['afterComplete']['url']);
	}
}
?>