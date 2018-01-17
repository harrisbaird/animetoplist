<?php
class SitesController extends AppController {

	var $name = 'Sites';
	var $components = array('Security', 'RequestHandler', 'Uploader.Uploader', 'Wizard', 'ImageCrop.ImageCrop');
	var $helpers = array('Cache', 'Js' => array('Jquery'), 'Text', 'Cycle', 'Time', 'Statistics', 'Gravatar', 'Ajax');
	var $cacheAction = array('index' => '3600');

	function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow('index', 'view', 'comment', 'dead');
		$this->Security->disabledFields = array('Comment.parent_id');

		//Wizard component settings
		$this->Wizard->steps = array('details', array('extra' => array('extra')));
		$this->Wizard->autoAdvance = false;
		$this->Wizard->autoReset = true;

		if(!$this->Auth->user()) $this->cacheAction['view'] = 3600;
	}

	/**
	 * List all sites by rank
	 *
	 * @return void
	 */
	function index() {
		$this->paginate = array('fields' => array('Site.*'), 'conditions' => array('User.is_active' => 1, 'Site.is_verified' => 1, 'Site.rank !=' => 0, 'Site.is_dead' => 0), 'limit' => 51, 'order' => 'Site.is_boosted DESC, Site.is_boost_shifted ASC, Site.is_premium DESC, Site.has_banner DESC, Site.rank ASC, Site.anime_count DESC', 'contain' => 'User');
		$this->set('sites', $this->paginate());
	}

	/**
	 * View site stats, reviews and anime lists
	 * Site owners also see link code
	 *
	 * @param integer $id
	 * @return void
	 */
	function view($slug) {
		if(!$this->Site->isValidSlug($slug)) {
			$this->showMessage('Invalid site', '', 'bad');
			$this->cakeError('error404');
		}

		$site = $this->Site->find('first', array('conditions' => array('Site.slug' => $slug), 'contain' => array('User')));

		if($site['Site']['is_dead']) {
			$this->showMessage('Invalid site', '', 'bad');
			$this->cakeError('error404');
		}

		$siteOwner = $this->Site->belongsTo($site['Site']['id'], $this->Auth->user('id'));

		$anime = $this->Site->SeriesSite->find('all', array('conditions' => array('Series.is_anime' => 1, 'SeriesSite.site_id' => $site['Site']['id'], 'SeriesSite.is_active' => 1, 'SeriesSite.is_disabled' => 0), 'order' => 'Series.name ASC', 'contain' => 'Series'));
		$manga = $this->Site->SeriesSite->find('all', array('conditions' => array('Series.is_manga' => 1, 'SeriesSite.site_id' => $site['Site']['id'], 'SeriesSite.is_active' => 1, 'SeriesSite.is_disabled' => 0), 'order' => 'Series.name ASC', 'contain' => 'Series'));

		$today = $this->Site->Stat->getStats($site['Site']['id'], 'today');
		$week = $this->Site->Stat->getStats($site['Site']['id'], '7d');
		$month = $this->Site->Stat->getStats($site['Site']['id'], '3m', true);

		$this->set(compact('site', 'siteOwner',  'grid_for_layout', 'anime', 'manga', 'today', 'week', 'month'));
	}

	/**
	 * Delete a site and all associated data
	 *
	 * @param integer $id
	 * @return void
	 */
	function delete($id) {
		if(!$this->Site->isValid($id)) {
			$this->showMessage('Invalid site', '', 'bad');
			$this->redirect(array('controller' => 'users', 'action' => 'profile'));
		}

		if(!$this->Site->belongsTo($id, $this->Auth->user('id'))) {
 			$this->showMessage('Unable to delete', 'You can\'t delete a site which doesn\'t belong to you', 'bad');
			$this->redirect(array('controller' => 'users', 'action' => 'profile'));
		}

		//Delete site and all dependant data
		$this->Site->delete($id, true);

		$this->showMessage('The site has been deleted', '', 'good');
		$this->redirect(array('controller' => 'users', 'action' => 'profile'));
	}

	/**
	 * Import sites from another user and delete the
	 * old account.
	 *
	 * @return void
	 */
	function import() {
		$this->usersSidebar();

        if(!empty($this->data)) {
            $userFrom = $this->data['User']['username'];
            $userTo = $this->Auth->user('username');

			//Check if the user has entered this account
	        if($userFrom == $userTo) {
				$this->showMessage('You can\'t import from this account', 'That would be like dividing by zero', 'bad');
	            $this->redirect(array('action' => 'import'));
	        }

            if($this->Site->User->verify($userFrom, $this->data['User']['password'])) {
				//Reassign all sites to this user
                $this->Site->reassign($userFrom, $userTo);

				//Delete the old user
                $this->Site->User->deleteAll(array('User.username' => $userFrom), false);

				$this->showMessage('Successfully imported', 'Your sites have been imported successfully', 'good');
                $this->redirect(array('controller' => 'users', 'action' => 'profile'));
            } else {
				$this->showMessage('Incorrect username or password', '', 'bad');
				$this->data['User']['password'] = '';
			}
        }
	}

        function dead() {
            $sites = $this->Site->find('all', array('conditions' => array('Site.is_dead' => 1), 'order' => 'dead_date DESC', 'contain' => false));
            $total_count = $this->Site->find('all', array('fields' => array('count(*) as count', 'dead_date'), 'conditions' => array('Site.is_dead' => 1), 'group' => 'dead_date', 'order' => 'dead_date DESC', 'contain' => false));
            $anime_count = $this->Site->find('all', array('fields' => array('count(*) as count', 'dead_date'), 'conditions' => array('Site.is_dead' => 1, 'Site.streaming_url !=' => ''), 'order' => 'dead_date DESC', 'group' => 'dead_date','contain' => false));
            $pending = $this->Site->find('all', array('conditions' => array('Site.is_dead' => 0, 'Site.dead_count > ' => 0), 'order' => 'official_name ASC', 'contain' => false));

            $site_count = array();
            foreach($total_count as $count) {
				$site_count[$count['Site']['dead_date']]['total'] = $count['Site']['count'];
            }

            foreach($anime_count as $count) {
                $site_count[$count['Site']['dead_date']]['anime'] = $count['Site']['count'];
            }

            $this->set(compact('sites', 'site_count', 'pending'));
        }

	function verifyNow($id) {
		$this->autoRender = false;
		$site = $this->Site->find('first', array('conditions' => array('Site.id' => $id)));
		if(empty($site)) { echo 'Site doesn\'t exist'; return; }
		if($site['Site']['is_verified']) {  echo 'Site is already verified';  return; }

		$this->Site->id = $site['Site']['id'];
		$this->Site->saveField('is_verified', 1);
		echo $site['Site']['url'] . ' is now verified';
	}

	/**
	 * After crop is complete, update the database
	 *
	 * @return void
	 */
	function cropComplete() {
		$settings = $this->Session->read('crop');

		$slug = $this->Site->getSlug($settings['id']);

		$this->Site->id = $settings['id'];
		$this->Site->saveField('has_banner', '1');

		$this->Session->delete('crop');



		$this->showMessage($settings['afterComplete']['message'], '', 'good');
		$this->redirect(array('controller' => 'sites', 'action' => 'view', $slug));
	}

	/**
	 * Start the wizard process for either adding a new site
	 * or editing an existing one.
	 *
	 * @param string $step Current wizard step
	 * @param integer $id Site id for editing
	 * @todo On edit, check if exists and owned by user
	 */
	function wizard($step = null, $id = null) {
		if((empty($step) && empty($id)) || (empty($this->data) && $step == 'details')) {
			$this->Session->delete('Wizard');
			$this->Session->delete('siteId');
		}

		if(!empty($id)) {
			if(!$this->Site->belongsTo($id, $this->Auth->user('id'))) {
				$this->showMessage('You can\'t edit a site which doesn\'t belong to you.', '', 'bad');
	            $this->redirect(array('controller' => 'users', 'action' => 'profile'));
			}

			$this->Session->write('siteId', $id);
		}

		$this->layout = 'small';

		//Process the steps
		$this->Wizard->process($step);
	}

	//Wizard callbacks
	function _prepareDetails() {
		if(!$this->Wizard->read('details') && $this->_getSite()) {
			$this->data = $this->_getSite();
			$this->Wizard->save();
		}

		$id = $this->Session->read('siteId');
		$site = $this->_getSite(false);

		$this->set(compact('id', 'site'));
	}

	/**
	 * Processes the details step.
	 * Verifies the site exists and saves the banner if available.
	 * Called by the wizard component.
	 *
	 * @access protected
	 * @return boolean
	 */
	function _processDetails() {
		$this->Site->set($this->data);

		//Does the url already exist
		if($this->Site->urlExists($this->data['Site']['url']) && !$this->_editingSite()) {
			$this->Site->invalidate('url', 'This URL has already been added to Anime Toplist');
		}

		//Validate the form
		if($this->Site->validates()) {
			//Save banner if available
			if(!empty($this->data['Site']['banner']['tmp_name'])) {
				//Overwrite original banner info
				$this->data['Site']['bannerData'] = $this->Uploader->upload('banner');
			}

			//Check the language of the site
			$this->Wizard->branch('extra');
			return true;

		}

		return false;
	}

	/**
	 * Prepare the extra step
	 *
	 * This is very hacky because this wizard component
	 * doesn't work properly.
	 *
	 * @access protected
	 * @return boolean
	 */
    function _prepareExtra() {
            $data = $this->_getSite();
            $this->data['Site'] = array(
                    'streaming_url' => $data['Site']['streaming_url'],
                    'manga_url' => $data['Site']['manga_url'],
            );
    }

	/**
	 * Validate the extra step
	 *
	 * @access protected
	 * @return boolean
	 */
	function _processExtra() {
		$this->Site->set($this->data);

		//Validate the form
		if($this->Site->validates()) {
			//Force wizard completion
			$this->_afterComplete();
		}

		return false;
	}

	/**
	 * Saves data after the form has been completed
	 *
	 * @access protected
	 * @return void
	 * @todo rewrite banner crop message
	 */
	function _afterComplete() {
		//If we are editing, set the site id to update the current site
		if($this->Session->check('siteId')) {
			$this->Site->id = $this->Session->read('siteId');
			$this->Session->delete('siteId');
			$message = "Your site has been successfully edited.<br /><br />It may take up 15 minutes before your changes become visible.";
			$twitter = false;
		} else {
			$message = 'Your site has been successfully added to Anime Toplist.';
			$twitter = true;
		}

		//Link this site to the logged in user
		$this->Wizard->write('details.Site.user_id', $this->Auth->user('id'));

		//Save the data
		$siteData = $this->Wizard->readModel('Site');
		$site = $this->Site->save($siteData, false);

		//Merge banner data back into data array
		$siteData['bannerData'] = $this->Session->read('Wizard.Sites.details.Site.bannerData');

		//Attempt to scrape anime and manga
		$this->queue('scrape_sites', array('siteId' => $this->Site->id));

		//Reset the wizard, important as we are redirecting manually
		$this->Wizard->resetWizard();

		//Handle banner cropping
		if(!empty($siteData['bannerData']['size'])) {
			$this->ImageCrop->setOptions(array(
				'id' => $this->Site->id,
				'image' => $siteData['bannerData'],
				'sizes' => array(
					'small' => array(
						'width' => 80,
						'path' => WWW_ROOT . 'img' . DS . 'sites' . DS,
						'filename' => $this->Site->id . '_80.jpg'
					),
					'large' => array(
						'width' => 138,
						'path' => WWW_ROOT . 'img' . DS . 'sites' . DS,
						'filename' => $this->Site->id . '_138.jpg'
					)
				),
				'aspectRatio' => array(1, 1),
				'header' => 'Crop your banner',
				'message' => 'You need to crop your banner before it can be used.',
				'cancel' => array(
					'url' => array('plugin' => false, 'controller' => 'users', 'action' => 'profile'),
					'text' => 'Cancel'
				),
				'afterComplete' => array(
					'message' => $message,
					'url' => array('plugin' => false, 'controller' => 'sites', 'action' => 'cropComplete')
				)
			));
			$this->redirect(array('plugin' => 'image_crop', 'controller' => 'crop', 'action' => 'crop'));
		}

		//Redirect and display site
		$this->showMessage($message, '', 'good');
		$this->redirect(array('action' => 'view', $site['Site']['slug']));
	}

	/**
	 * Gets run when the cancel button is clicked
	 *
	 * @access protected
	 * @return void
	 */
	function _beforeCancel() {
		$this->Session->delete('siteId');
	}

	/**
	 * Get the site currently being edited
	 *
	 * @access protected
	 * @return mixed array on success - false on failure
	 */
	function _getSite($limit=true) {
		if($this->Session->check('siteId')) {
			$id = $this->Session->read('siteId');
			if($limit) {
			  return $this->Site->find('first', array('conditions' => array('Site.id' => $id), 'fields' => array('Site.user_id', 'Site.url', 'Site.official_name', 'Site.description', 'Site.streaming_url', 'Site.manga_url', 'Site.has_banner', 'Site.premium_box_bg', 'Site.premium_box_title', 'Site.premium_box_text', 'Site.disable_comments', 'Site.disable_bar'), 'contain' => false));
		  } else {
			  return $this->Site->find('first', array('conditions' => array('Site.id' => $id), 'contain' => false));
		  }
		}

		return false;
	}

	function _editingSite() {
		return $this->Session->check('siteId');
	}

	function update_ranks() {
		$this->Site->updateRanks();
	}
}
?>
