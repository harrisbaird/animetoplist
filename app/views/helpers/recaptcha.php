<?php 
class RecaptchaHelper extends AppHelper {
	var $helpers = array('form'); 
	
	function script() {
		$pubkey = Configure::read("Recaptcha.pubKey");
		$server = Configure::read('Recaptcha.apiServer');
		 
		if ($pubkey == null || $pubkey == '') {
			die ("To use reCAPTCHA you must get an API key from <a href='http://recaptcha.net/api/getkey'>http://recaptcha.net/api/getkey</a>");
		}
		
		return '<script type="text/javascript" src="'. $server . '/challenge?k=' . $pubkey . '"></script>';
	}

}
?>