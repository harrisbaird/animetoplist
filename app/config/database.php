<?php
class DATABASE_CONFIG {

	var $prod = array(
		'driver' => 'mysqli',
		'persistent' => true,
		'host' => '127.0.0.1',
		'login' => 'root',
		'password' => '----------REMOVED----------',
		'database' => 'animetoplist',
		'prefix' => '',
	);

	var $dev = array(
		'driver' => 'mysql',
		'persistent' => true,
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'database' => 'animetoplist-dev',
		'prefix' => '',
	);

	function __construct() {
		$this->default = $this->prod;
	}
}
?>
