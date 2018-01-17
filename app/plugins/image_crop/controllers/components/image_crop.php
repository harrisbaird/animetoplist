<?php
class ImageCropComponent extends Object {
	
	var $components = array('Session', 'Uploader.Uploader');
	
	/**
	 * Set the options, defaults will be used for any
	 * unset options.
	 *
	 * @param array $options 
	 * @return void
	 */
	function setOptions($settings = array()) {
		$defaults = array(
			'image' => '',
			'redirect' => '/',
			'header' => 'Crop',
			'message' => '',
			'aspectRatio' => array(1, 1),
			'sizes' => array(),
			'hidePreview' => false
		);

		//Calculate the aspect ratio
		App::import('Vendor', 'Math', array('file' => 'Math/Fraction.php'));
		$fr = new Math_Fraction($settings['aspectRatio'][0], $settings['aspectRatio'][1]);
		$settings['aspectRatio'] = array('fraction' => $fr->toString(), 'decimal' => $fr->toFloat());
		
		
		//Calculate any per size aspect ratios
		foreach($settings['sizes'] as $id => $size) {
			if(!empty($size['aspectRatio'])) {
				$fr = new Math_Fraction($size['aspectRatio'][0], $size['aspectRatio'][1]);
				$settings['sizes'][$id]['aspectRatio'] = array('fraction' => $fr->toString(), 'decimal' => $fr->toFloat());
			}
		}
		
		//Merge custom options with defaults
		$settings = Set::merge($defaults, $settings);
		
		$this->Session->write('crop', $settings);
	}
	
	/**
	 * Loop through each of the sizes and crop them
	 *
	 * @param array $data 
	 * @return void
	 */
	function process($data) {
		$settings = $this->Session->read('crop');
		
		foreach($settings['sizes'] as $size) {
			$this->cropImage($settings, $data, $size);
		}
		
		return array();
	}
	
	/**
	 * Crop a single image
	 *
	 * @param array $options 
	 * @param array $data 
	 * @param array $size 
	 * @return void
	 */
	function cropImage($settings, $data, $size) {
		
		$height = $size['width'] / $settings['aspectRatio']['decimal'];
		$width = $size['width'];
		$dest_x = 0;
		
		//Is there a custom aspect ratio for this size
		if(!empty($size['aspectRatio'])) {
			$height = $size['width'] / $size['aspectRatio']['decimal'];
			$width = $size['width'] / $size['aspectRatio']['decimal'];
			$dest_x = ($size['width'] / 2) - ($width / 2);
		}
		
		$file = WWW_ROOT . $settings['image']['path'];
		
		//Get information about the original image
		$info = getimagesize($file);
		
		switch ($info[2]) {
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif($file);
				break;
			case IMAGETYPE_JPEG:
				$image = imagecreatefromjpeg($file);
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng($file);
				break;
			default:
				return false;
		}
			
		//Just resize and skip crop
		if(isset($settings['crop']) && $settings['crop'] == false) {
			$width = $size['width'];
			$height = $settings['image']['height'];

			$originalRatio = $settings['image']['width'] / $settings['image']['height'];
			if ($width / $height > $originalRatio) {
			   $width = $height * $originalRatio;
			} else {
			   $height = $width / $originalRatio;
			}
			
			//Final image object
			$imageCropped = imagecreatetruecolor($width, $height);
			imagealphablending($imageCropped,true);
			imagealphablending($image,true);
			
			imagecopyresampled($imageCropped, $image, 0, 0, 0, 0, $width, $height, $settings['image']['width'], $settings['image']['height']);
		} else {
			//Final image object
			$imageCropped = imagecreatetruecolor($size['width'], $height);
			
			//if custom aspect ratio, create background
			if(!empty($size['aspectRatio'])) {
				imagecopyresampled($imageCropped, $image, 0, 0, $data['Site']['x1'], $data['Site']['y1'], $size['width'], $height, $settings['image']['width'], $settings['image']['height']);		
			
				//Blur background 5 times
				for($i = 0; $i < 50; $i++) {
					imagefilter($imageCropped, IMG_FILTER_GAUSSIAN_BLUR);
				}
		
				imagealphablending($imageCropped, true);
				imagefilledrectangle($imageCropped, 0, 0, $size['width'], $height, imagecolorallocatealpha($imageCropped, 255, 255, 255, 70));
			

			}
		
			imagecopyresampled($imageCropped, $image, $dest_x, 0, $data['Site']['x1'], $data['Site']['y1'], $width, $height, $data['Site']['w'], $data['Site']['h']);
		}
	
		//Save the new image
		imagejpeg($imageCropped, $size['path'] . $size['filename'], 90);
	}	
}
?>
