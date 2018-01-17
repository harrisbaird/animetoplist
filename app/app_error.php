<?php
class AppError extends ErrorHandler {

	function verificationFailed($params) {
		$this->_outputMessage('verification_failed');
	}
}
?>
