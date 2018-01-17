<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $components = array('Tickets', 'Recaptcha');
	var $helpers = array('Cycle');

	function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow(array('index', 'login', 'register', 'activate', 'forgottenPassword', 'recoverPassword', 'dialog', 'forceLogin'));

		//Force permanent login
		if($this->data['User']) {
			$this->data['User']['auto_login'] = 1;
		}

		//Hash the password confirmination
		if(!empty($this->data['User']['password_confirm'])) {
			$this->data['User']['password_confirm'] = $this->Auth->password($this->data['User']['password_confirm']);
		}

		//Keys for Recaptcha component
		$this->Recaptcha->publickey =  Configure::read('recaptcha.publicKey');
		$this->Recaptcha->privatekey = Configure::read('recaptcha.privateKey');

		//Captcha fields should be ignored
		$this->Security->disabledFields = array('recaptcha_response_field', 'recaptcha_challenge_field', 'auto_login', 'next');
	}

	function index() {
		$this->redirect('/');
	}

	function login() {
		$this->layout = 'small';

		//User is already logged in
		if($this->Auth->user()) {
			$this->redirect($this->Auth->redirect());
		}

		//Invalid login info entered
		if(!empty($this->data)) {
			$this->showMessage('Unable to log you in', 'The username or password you entered are incorrect.', 'bad');
		}
	}

	function logout() {
		$this->showMessage('Logged out', 'You have successfully logged out', 'good');
		$this->redirect($this->Auth->logout());
	}

	/**
	 * Creates a new account and sends an
	 * activation email.
	 *
	 * @return void
	 */
	function register() {
		$this->layout = 'small';

		//Check if the user is already logged in
		if($this->Auth->user()) {
			$this->showMessage('You are already logged in', '', 'bad');
			$this->redirect('/');
		}

		//Has the form been submitted?
		if (!empty($this->data)) {
			$this->User->create();

			if ($this->User->save($this->data)) {
				//User was saved successfully, generate a new
				//activation code & trigger event to send email
				$ticket = $this->Tickets->set($this->User->id);
				$this->Event->triggerEvent('userRegister', array('hash' => $ticket, 'data' => $this->data));

				//Automatically log the user in.
				$this->Auth->login($this->data);

				$this->showMessage('Thank you for registering', 'Your account is now active!', 'good');
				$this->redirect(array('action' => 'profile'));
			} else {
				//Validation errors, invalidate form
				$this->_invalidate();
			}
		}
	}

	/**
	 * Activates a users account
	 *
	 * @param string $hash
	 * @return void
	 * @todo Allow user to resend activation hash
	 */
	function activate($hash = null) {
		if(empty($hash)) {
			$this->showMessage('An activation code wasn\'t entered', '', 'bad');
			$this->redirect(array('action' => 'index'));
		}

		$id = $this->Tickets->get($hash);
		if(!empty($id)) {

			$this->User->id = $id;
			$this->User->saveField('is_active', 1);
			$this->showMessage('Account activated', 'You can now use all of the features of Anime Toplist', 'good');
			$this->redirect(array('action'=>'index'));
		}

		//Hash was invalid or expired
		$this->showMessage('The activation code is invalid', '','bad');
		$this->redirect(array('action'=>'index'));
	}

	/**
	 * Allows users to change their password
	 *
	 * @return void
	 */
	function changePassword() {
		$this->usersSidebar();

		if (!empty($this->data)) {
			//The password isn't automatically hashed
			$this->data['User']['password'] = $this->Auth->password($this->data['User']['password']);

			//Use the currently logged in user
			$this->User->id = $this->Auth->user('id');

			if ($this->User->save($this->data)) {
				//New password saved
				$this->showMessage('Your password has been changed', '', 'good');
				$this->redirect(array('action' => 'profile'));
			} else {
				//Validation errors: blank fields or passwords don't match
				$this->_invalidate();
			}
		}
	}

	/**
	 * Sends users an email so they can temporarily
	 * login to their account to change password
	 *
	 * @param string $hash
	 * @return void
	 */
	function forgottenPassword() {
		$this->layout = 'small';

		if(!empty($this->data)) {
			//Get the user which the email address belongs to
			$user = $this->User->findByEmail($this->data['User']['email']);

			if(!empty($user)) {
				//User exists, generate a hash and trigger
				//event to send email
				$ticket = $this->Tickets->set($user['User']['id']);
				$this->Event->triggerEvent('userForgottenPassword', array('hash' => $ticket));

				$this->showMessage('Email sent', 'Once you have received the email, follow the link to change your password.', 'good');
				$this->redirect(array('action'=>'index'));
			} else {
				//Email doesn't exist in database
				$this->data['User']['email'] = "";
				$this->showMessage('Invalid email address', 'There are no users with that email address.', 'bad');
			}
		}
	}

	/**
	 * Log user into their account so they
	 * can change their password. Second step
	 * of forgottenPassword.
	 *
	 * @param string $hash
	 * @return void
	 */
	function recoverPassword($hash = null) {
		if(!empty($hash)) {
			$id = $this->Tickets->get($hash);
			if(!empty($id)) {
				//Log the user in automatically
				$user = $this->User->findById($id);
				$this->Auth->Login($user);

				$this->showMessage('You have been automatically logged in, please change your password', '', 'bad');
				$this->redirect(array('action'=>'changePassword'));
			}
		}

		//Hash was invalid or expired
		$this->showMessage('The recovery link was either invalid or has expired', '', 'bad');
		$this->redirect(array('action'=>'forgottenPassword'));
	}

	/**
	 * Display a users account
	 *
	 * @return void
	 */
	function profile() {
		$this->layout = 'small';
	//	$this->usersSidebar();
		$sites = $this->User->Site->getByUserId($this->Auth->user('id'));

		if(!empty($sites) && $this->Auth->user('is_active') != 1) {
			$this->showMessage('Account not activated', 'You need to confirm your email address before any sites will be visible on Anime Toplist.', 'bad');
		}

		$this->set(compact('sites'));
	}

	/**
	 * Display a ajax login dialog
	 *
	 * @return void
	 * @author Daniel
	 */
	function dialog() {
		$this->layout = 'ajax';

		$this->set(array('publicKey' => $this->Recaptcha->publickey));
	}

	function validateData() {
		$this->layout = 'ajax';
	}

	function forceLogin($username, $valid = false) {
		if(!$valid) $this->cakeError('error404',array(array('url'=>'/')));

		$this->Auth->fields['password'] = 'username';

		$this->Auth->authenticate = $this->User;
		$this->Auth->loginRedirect = '/';

		$this->Auth->login(array(
			'username' => $username,
			'password' => $this->Auth->password($username)
		));

		$this->redirect(array('action' => 'profile'));
	}

	/**
	 * Invalidates password fields and displays
	 * captcha error. Used on the register action.
	 *
	 * @return void
	 */
	function _invalidate() {
		//When validation fails, the passwords need to be
		//cleared otherwise the md5 hashes are displayed.
		$this->User->invalidate('password', 'Your passwords do not match');
		$this->User->invalidate('password_confirm', 'Your passwords do not match');
		$this->data['User']['password'] = "";
		$this->data['User']['password_confirm'] = "";

		//Show captcha errors
		$this->set('error_captcha', 'Please enter both words from the image above');
		$this->set('error_captcha_class', 'error');
	}

}
?>
