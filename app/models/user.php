<?php
class User extends AppModel {

	var $name = 'User';
	var $hasMany = array('Site', 'SeriesReview');
	var $validate = array(
		'username' => array(
							'alphanumeric' => array(
  								'rule' => 'alphanumeric',
  								'message' => 'Username may only contain letter and numbers, no spaces'),
							'maxLength' => array(
  								'rule' => array('maxLength', 15),
  								'message' => 'Username may contain a maximum of 15 characters'),
							'notEmpty' => array(
								'rule' => 'notEmpty',
								'message' => 'You must enter a username'),
							'isUnique' => array(
  								'rule' => 'isUnique',
  								'message' => 'That username is already in use')
							),
		'password' => array(
							'notEmpty' => array(
								'rule' => 'notEmpty',
								'message' => 'You must enter a password')
							),
		'password_confirm' => array(
							'checkPasswords' => array(
								'rule' => 'checkPasswords',
								'message' => 'Password does not match'),
							'notEmpty' => array(
								'rule' => 'notEmpty',
								'message' => 'You must confirm your password')
							),
		'email' => array(
							'email' => array(
								'rule' => 'email',
								'message' => 'You need to enter a valid email address'),
							'notEmpty' => array(
								'rule' => 'notEmpty',
								'message' => 'You must enter an email address'),
							'isUnique' => array(
  								'rule' => 'isUnique',
  								'message' => 'This email address is already in use')
							)
	);

	/**
	 * Validation callback
	 * Compares two passwords and ensures they are correct
	 *
	 * @param array $data
	 */
	function checkPasswords($data) {
		$pass1 = trim($this->data['User']['password']);
		$pass2 = trim($this->data['User']['password_confirm']);
		if (isset($pass1) && $pass1 == $pass2) {
			return true;
		}
		return false;
	}
	
	/**
	 * Check if a username and password are valid
	 *
	 * @param string $username 
	 * @param string $password 
	 * @return boolean
	 */
	function verify($username, $password) {
		$user = $this->find('first', array('conditions' => array('User.username' => $username, 'User.password' => $password)));
		return !empty($user) ? true : false;
	}
	
	/**
	 * Get a user id from a username
	 *
	 * @param string $username 
	 * @return integer
	 */
	function userId($username) {
		$user = $this->find('first', array('conditions' => array('User.username' => $username), 'contain' => false));
		
		return $user['User']['id'];
	}

}
?>
