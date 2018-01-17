<?php
class AppController extends Controller {
	var $components = array('Session', 'Auth', 'Security', 'RequestHandler', 'AutoLogin', 'Cookie', 'Event', 'Featured', 'Sidebar', 'Footer');
	var $helpers = array('Html', 'Form', 'Session', 'Number', 'Navigation', 'Sidebar', 'At', 'Minify');

	var $usersSidebar = false;

	/**
	 * Make sure the controller is constructed
	 * properly when an error occurs
	 */
	function __construct() {
		parent::__construct();
		if ($this->name == 'CakeError') {
			$this->constructClasses();
			$this->beforeFilter();
		}
	}

	function beforeFilter() {
		parent::beforeFilter();
		Security::setHash('md5');

		if($this->Session->check('loginNext')) {
			$this->set('loginNext', $this->Session->read('loginNext'));
			$this->Session->delete('loginNext');
		}

		//Make sure the user data is up to date
		if($this->Auth->user()) {
			App::import('Model', 'User');
			$user = new User;
			$user = $user->read(null, $this->Auth->user('id'));
			$this->Session->write($this->Auth->sessionKey, $user['User']);

		}

		//Send logged in user data to the view
		$this->set('userData', $this->Auth->user());
		$this->Auth->logoutRedirect = '/';

		//Load the configuration stored in the database
		App::import('Model', 'Setting');
		$setting = new Setting;
		$this->set(array('appSettings' => $setting->load()));

		//Check referrer to log in stats
		$this->checkReferer();

		$this->Security->allowedControllers = array('Search');

		if(!empty($_GET['next'])) {
			$this->Session->write('loginNext', $_GET['next']);
		}
	}

	/**
	 * Triggered during successful automatic login
	 *
	 * @param string $cookie
	 */
	function _autoLogin($cookie) {
		$this->set('userData', $this->Auth->user());
	}

	/**
	 * Triggered during failed automatic login
	 *
	 * @param string $cookie
	 * @return void
	 */
	function _autoLoginError($cookie) {
		//$this->Session->setFlash('Autologin failed.', true, array('class' => 'error'));
	}

	/**
	 * Convenience function for setFlash
	 *
	 * @param string $message The message to be displayed
	 * @param string $type Success, error, info or warning
	 * @param string $title Message header
	 * @return void
	 */
	function showMessage($title, $message, $type = 'good') {
		$this->Session->setFlash($message, 'flash', array('class' => $type, 'title' => $title));
	}

	/**
	 * Convinience function for QueuedTask::createJob
	 *
	 * @param string $task_name
	 * @param string $data
	 * @return boolean
	 */
	function queue($task_name, $data = array(), $notBefore = null) {
                return;
		App::import('Model', 'Queue.QueuedTask');
		$QueuedTask = new QueuedTask;
		return $QueuedTask->createJob($task_name, $data, $notBefore);
	}

	/**
	 * Display a sidebar on user account pages
	 *
	 * @return void
	 */
	function usersSidebar() {
		App::import('model', 'Site');
		$site = new Site;

		$sites = $site->getByUserId($this->Auth->user('id'));
		$usersSidebar = true;
		$grid_for_layout = 6;

		$this->set(compact('usersSidebar', 'grid_for_layout'));
	}

}
?>
