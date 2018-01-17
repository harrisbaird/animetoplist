<?php
class SeriesController extends AppController {

	var $name = 'Series';
	
	var $components = array('Security', 'Uploader.Uploader', 'ImageCrop.ImageCrop');
	var $helpers = array('Diff', 'Cycle', 'Cache', 'Time', 'Text');
	var $cacheAction = array('index' => '3600', 'view' => '3600');
	
	var $validTypes = array('synopsis', 'genres', 'picture');

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index', 'anime', 'manga', 'view', 'revisions', 'addReview');
		$this->Security->disabledFields = array('story', 'characters', 'animation', 'sound');
	}
	
	/**
	 * Display a list of all Anime or Manga
	 *
	 * @param string $type anime or manga
	 * @param string $letter A letter, misc or 09
	 * @param string $category A slug from the AnimeType model
	 * @return void
	 */
	function index($type) {
		$letter = @$this->passedArgs['letter'];
		$category = @$this->passedArgs['category'];

		$seriesData = $this->Series->getList($type, $letter, $category);
		
		$usedLetters = $seriesData['usedLetters'];
		$categories = $seriesData['categories'];
		$series = $seriesData['series'];

		//Character arrays, used in the menu
		$alpha = array_merge(array('misc' => '#', '09' => '0-9'), array_combine(range('A','Z'), range('A','Z')));
		$special = array('09' => '0-9', 'misc' => '#');
		
		$this->set(compact('type', 'letter', 'category', 'usedLetters', 'series', 'categories', 'alpha', 'special'));
	}
	
	function anime($slug) {
		$this->setAction('view', $slug);
	}
	
	function manga($slug) {
		$this->setAction('view', $slug);
	}
	
	/**
	 * View details of a single series
	 *
	 * @param string $slug
	 * @return void
	 */
	function view($slug) {
		$series = $this->Series->find(
			'first', array('conditions' => array('Series.slug' => $slug),
				'contain' => array(
					'SeriesSite' => array(
						'conditions' => array(
							'SeriesSite.is_active = 1',
							'SeriesSite.is_disabled = 0'
						),
						'order' => 'RAND()',
						'Site'
					)
				)
			)
		);


		if(empty($series)) {
			$this->showMessage('Invalid url entered', '',  'bad');
			$this->redirect(array('action' => 'index'));
		}
		
		//$grid_for_layout = 6;
		$validTypes = array_combine($this->validTypes, explode(' ', ucwords(implode(' ', $this->validTypes))));
		$typeText = $this->Series->getType($series['Series']['id']);
		
		$this->set(compact('series', 'validTypes', 'typeText'));
	}

	/**
	 * Modify a series and creates a new revision
	 *
	 * @param integer $id Series id
	 */
	function edit($id = null, $submitted = false) {
		//Is the logged in user activated
		if($this->Auth->user('is_active') != 1) {
			$this->showMessage('Account not activated', 'You need to activate your account by clicking on the link sent to you by email before you can make any changes', 'bad');			
			$this->redirect(array('controller' => 'users', 'action' => 'profile'));
		}
		
		//Check if the series is valid
		if (!$id && !$submitted) {
			$this->showMessage('Invalid url entered', '', 'bad');			
			$this->redirect(array('action' => 'index', 'anime'));
		}

		//Is the series currently locked?
		if($this->Series->isLocked($id)) $this->_locked($id);
		
		//Get the current data
		$series = $this->Series->find('first', array('conditions' => array('Series.id' => $id), 'contain' => 'Genre'));
		$this->data['Series']['name'] = $series['Series']['name'];

		//If we have data, attempt to save
		if ($submitted) {
			//Check if this has been vandalized
			$this->AntiVandal->setAkismetKey(Configure::read('App.akismet.key'));
			$vandalized = $this->AntiVandal->isVandalized($this->data['Series']['synopsis'], $series['Series']['synopsis']);
			if($vandalized) {
				App::import('Model', 'Queue.QueuedTask');
				$this->Event->triggerEvent('seriesEditRejected', array('slug' => $this->Series->getSlug($id), 'edit' => $this->data['Series']['synopsis']));
				$this->Series->vandalized($id);
				$this->showMessage('Your changes have been saved', 'It may take a few minutes before they are visible.', 'good');
				$this->redirect(array('action' => 'view', $this->Series->getSlug($id)));
			}
			
			//Upload image if available
			if(!empty($this->data['Series']['image']['name'])) {
				$this->data['Series']['image'] = $this->Uploader->upload('image');
				$this->Session->write('Series.image', $this->data['Series']['image']);
			}
			
			//Do we have an image already uploaded
			if($this->Session->check('Series.image')) {
				$this->data['Series']['image'] = $this->Session->read('Series.image');
			}
			
			if($this->Series->save($this->data, array('validate' => 'only'))) {
			
				$message = 'Your changes have been saved.';
			
				//Handle image cropping, data not saved until cropping complete
				if(!empty($this->data['Series']['image']['name'])) {


					$revision = $this->Series->currentRevision($id);
					$imageName = $this->Series->id . '_' . ($revision['Revision']['revision_number'] + 1) ;
				
					$this->ImageCrop->setOptions(array(
						'id' => $this->Series->id,
						'image' => $this->data['Series']['image'],
						'crop' => false,
						'sizes' => array(
							'large' => array(
								'width' => 300,
								'path' => WWW_ROOT . 'img' . DS . 'series' . DS,
								'filename' => $imageName . '.jpg'
							),
							'medium' => array(
								'width' => 208,
								'path' => WWW_ROOT . 'img' . DS . 'series' . DS,
								'filename' => $imageName . 'm.jpg'
							),
							'small' => array(
								'width' => 90,
								'path' => WWW_ROOT . 'img' . DS . 'series' . DS,
								'filename' => $imageName . 's.jpg'
							)
						),
						'aspectRatio' => array(16, 9),
						'header' => 'Crop image',
						'message' => 'You need to crop the image before it can be used.',
						'data' => $this->data,
						'afterComplete' => array(
							'message' => $message,
							'url' => array('plugin' => false, 'controller' => 'series', 'action' => 'cropComplete', $this->Series->getSlug($id))
						),
						'hidePreview' => true
					));
				
					$this->Session->delete('Series.image');
				
					$this->redirect(array('plugin' => 'image_crop', 'controller' => 'crop', 'action' => 'crop'));
				}
			
				//Make sure the versionable behavior creates a new revision
	            $this->Series->doVersion();
	
				$this->Series->save($this->data);
				$this->Event->triggerEvent('seriesEdit', array('slug' => $this->Series->getSlug($id), 'edit' => $this->data['Series']['synopsis']));
				$this->showMessage($message, '', 'good');
				$this->redirect(array('action' => 'view', $this->Series->getSlug($id)));

			} else {
				$this->showMessage('Please fix the problems below', '', 'bad');
			}
		}
		
		//Get data for the series unless the form has already
		//been submitted, used for populating fields
		if (!$submitted) {
			$this->data = Set::merge($series, $this->data);
		}
		
        $animetypes = $this->Series->AnimeType->find('list', array('conditions' => array('is_visible' => true)));
		$genres = $this->Series->Genre->find('list', array('order' => 'name ASC'));
		$typeText = $this->Series->getType($id);
		$imageUpload = !empty($this->data['Series']['image']['name']) ? $this->data['Series']['image'] : false;
		$grid_for_layout = 6;
		
		$this->layout = 'small';

		$this->set(compact('id', 'genres', 'series', 'type', 'typeText', 'imageUpload', 'grid_for_layout'));
	}
	
	/**
	 * Save the submitted data after the image
	 * has been cropped.
	 *
	 * @param string $slug 
	 * @return void
	 */
	function cropComplete($slug) {
		$settings = $this->Session->read('crop');
		
		$this->Series->id = $settings['id'];
		$data = $settings['data'];
		
		$imageData = array(
			'Series' => array(
				'image_filename' => $settings['sizes']['large']['filename'],
				'image_small_filename' => $settings['sizes']['small']['filename'],
				'image_medium_filename' => $settings['sizes']['medium']['filename']
			)
		);
		
		$data = Set::merge($data, $imageData);
		
        $this->Series->doVersion();
		$this->Series->save($data);
		
		$this->showMessage($settings['afterComplete']['message'], '', 'good');
		$this->redirect(array('controller' => 'series', 'action' => 'view', $slug));
	}

	/**
	 * Display revision history
	 * @param integer $id
	 */
    function revisions($id) {
        $revisions = $this->Series->getRevisions($id);

		//The content is automatically serialized,
		//we need to unserialize it
        foreach ($revisions as $key => $item) {
            $revisions[$key]['Revision']['content'] = unserialize($item['Revision']['content']);
        }

        $animetypes = $this->Series->AnimeType->find('list', array('conditions' => array('is_visible' => true)));
		$genres = $this->Series->Genre->find('list');

		//Number of revisions to display open
		$open = 3;

		$this->set(compact('revisions', 'genres', 'animetypes', 'open', 'id'));
    }

	/**
	 * Revert to a previous revision
	 * @param integer $id Series id
	 * @param integer $revision Revision number
	 */
    function rollback($id, $revision) {
		//Is the series currently locked?
		if($this->Series->isLocked($id)) $this->_locked($id);

		//Check if the revision exists
		$revisionData = $this->Series->getRevision($id, $revision);
		if(empty($revisionData) || empty($id) || empty($revision)) {
			$this->showMessage('Invalid revision.', '', 'bad');
			$this->redirect(array('action' => 'index'));
		}

		//Check for rollback
		if($revisionData['Revision']['is_rollback'] == 1) {
			$this->showMessage('You cannot rollback a rollback', '', 'bad');
			$this->redirect(array('action' => 'revisions', $id));
		}

		//Either perform the rollback or display the form
		if(!empty($this->data)) {
			//Do we have a reason, if not invalidate
			if(empty($this->data['Series']['reason'])) {
				 $this->Series->invalidate('reason', 'You need to enter a reason.');
			} else {
				//Perform the rollback
				$this->Series->rollback($id, $revision, $this->data['Series']['reason']);

				//Trigger event to email admin
				$this->Event->triggerEvent('seriesRollback', array('seriesId' => $id));

				$this->showMessage('The series has been rolled back.', '', 'good');
				$this->redirect(array('action' => 'revisions', $id));
			}
		}
		
		$title = 'You are about to rollback to revision ' . $revision;
		$message = 'You shouldn\'t do a rollback unless this entry contains spam or offensive content. Are you sure you want to do this?';
		
		$this->showMessage($title, $message, 'bad');

		$this->set(compact('id', 'revision'));
    }

	/**
	 * Display a list of Anime or Manga on a certain site
	 *
	 * @param string $type 
	 * @param string $siteSlug 
	 * @return void
	 */
	function moderate($type, $siteSlug) {
//		$this->usersSidebar();
		
		if(empty($type) || empty($siteSlug)) {
			$this->showMessage('', 'Bad url', 'bad');
			$this->redirect(array('controller' => 'users', 'action' => 'profile'));
		}
		
		$typeField = $type == 'anime' ? 'Series.is_anime' : 'Series.is_manga';
		$site = $this->Series->Site->find('first', array('conditions' => array('Site.slug' => $siteSlug), 'contain' => false));		
		$seriesSite = $this->Series->SeriesSite->find('all', array('conditions' => array('SeriesSite.site_id' => $site['Site']['id'], $typeField => 1), 'order' => 'Series.name ASC', 'contain' => array('Series')));
		
		if($this->Auth->user('id') != $site['Site']['user_id'] || empty($seriesSite)) {
			$this->showMessage('', 'Nothing to display', 'bad');
			$this->redirect(array('controller' => 'users', 'action' => 'profile'));
		}
		
		$rated = $this->Series->SeriesSite->find('all', array('conditions' => array('SeriesSite.site_id' => $site['Site']['id'], 'SeriesSite.is_active' => 1, $typeField => 1, 'SeriesSite.ratings_count >' => 0), 'order' => 'Series.name ASC', 'contain' => array('Series')));
		$unrated = $this->Series->SeriesSite->find('all', array('conditions' => array('SeriesSite.site_id' => $site['Site']['id'], 'SeriesSite.is_active' => 1, $typeField => 1, 'SeriesSite.ratings_count' => 0), 'order' => 'Series.name ASC', 'contain' => array('Series')));

		$this->set(compact('type', 'site', 'rated', 'unrated'));
	}

	/**
	 * Disable or reenable a series site link
	 *
	 * @param string $type 
	 * @param string $siteSlug 
	 * @param integer $id
	 * @return void
	 */
	function disableSeries($type, $siteSlug, $id) {
		$seriesSite = $this->Series->SeriesSite->find('first', array('conditions' => array('SeriesSite.id' => $id), 'contain' => array('Site')));

		if($this->Auth->user('id') != $seriesSite['Site']['user_id'] || empty($seriesSite)) {
			$this->showMessage('', 'Unable to change', 'bad');
		} else {
			$this->Series->SeriesSite->id = $id;
			$disabled = $seriesSite['SeriesSite']['is_disabled'] ? 0 : 1;
			$this->Series->SeriesSite->saveField('is_disabled', $disabled);
		}

		$this->redirect(array('controller' => 'series', 'action' => 'moderate', $type, $siteSlug));
	}
	
	/**
	 * Force an update of all Anime and/or Manga
	 * on a certain site
	 *
	 * @param string $type 
	 * @param string $siteSlug 
	 * @return void
	 */
	function update($type, $siteSlug) {
		if(empty($type) || empty($siteSlug)) {
			$this->showMessage('', 'Bad url', 'bad');
			$this->redirect(array('controller' => 'users', 'action' => 'profile'));
		}
		
		$site = $this->Series->Site->find('first', array('conditions' => array('Site.slug' => $siteSlug), 'contain' => false));
		$this->queue('scrape_sites', array('siteId' => $site['Site']['id']));
		
		//Update the date so only one update happens every 5 minutes
		$this->Series->Site->id = $site['Site']['id'];
		$this->Series->Site->saveField('forced_update', date("Y-m-d H:i:s", strtotime('+5 minutes')));
		
		$this->showMessage('', 'Your update has been queued, this may take a few minutes.', 'good');
		$this->redirect(array('action' => 'moderate', $type, $siteSlug));
	}
	
	/**
	 * Force an update of all Anime and/or Manga
	 * on a certain site
	 *
	 * @param string $type 
	 * @param string $siteSlug 
	 * @return void
	 */
	function addReview($slug) {
		$series = $this->Series->find('first', array('conditions' => array('Series.slug' => $slug), 'contain' => false));
		
		if(empty($series)) $this->redirect('/');
		
		if(!empty($this->data)) {
			$userId = $this->Auth->user('id');
			
			$this->data['SeriesReview']['series_id'] = $series['Series']['id'];
			$this->data['SeriesReview']['user_id'] = !empty($userId) ? $userId : 1;
			$this->data['SeriesReview']['overall'] = $this->Series->SeriesReview->calculateOverall($this->data);

			if ($this->Series->SeriesReview->save($this->data)) {
				$this->showMessage('', 'Your review has been added.', 'good');
				$this->redirect(array('action' => 'view', $series['Series']['slug']));
			}
		}
		
		$this->set(compact('series'));
	}

	/**
	 * Display locked message
	 *
	 * @param integer $id
	 * @return void
	 * @access protected
	 */
	function _locked($id) {
		$this->showMessage('That is currently locked and cannot be edited.', '', 'bad');
		$this->redirect(array('action' => 'view', $this->Series->getSlug($id)));
	}

}
?>
