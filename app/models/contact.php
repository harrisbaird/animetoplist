<?php
class Contact extends AppModel {
	var $name = 'Contact';
	var $useTable = false;
	
	var $_schema = array(
		'email' => array('type' => 'string', 'length' => 255),
		'message' => array('type' => 'text')
	);
	
	var $validate = array(
		'email' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'Enter a valid email address'),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Enter an email address')
		),
		'type' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Select a type'
			)
		),
		'message' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Enter a message'
			)
		)
	);
}
?>