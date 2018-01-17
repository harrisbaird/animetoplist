<?php
class Series extends AppModel {

	var $name = 'Series';

	var $hasAndBelongsToMany = array('Site', 'Genre', 'Tag');
	var $hasMany = array('SeriesTitle', 'SeriesSite', 'SeriesReview');
	var $belongsTo = array('AnimeType');

	var $actsAs = array(
        'Sluggable' => array('label' => 'name'),
        'Versionable' => array('Series.synopsis', 'Series.synopsis_source', 'Series.reason', 'Series.image_filename', 'Series.image_small_filename', 'Series.image_medium_filename', 'Series.image_position', 'Genre.Genre'),
		'Uploader.FileValidation' => array(
			'extension' => array(
				'value'    => array('gif', 'jpg', 'png', 'jpeg'),
				'error'    => 'That filetype is not accepted',
			),
			'optional' => array(
				'value' => true
			)
		)
    );

	var $validate = array(
		'synopsis' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1200),
				'message' => 'The synopsis may only contain a maximum of 1200 characters'
			),
		),
		'reason' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => 'Reason may contain a maximum of 50 characters'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'You must enter a reason'
			),
		)
	);
	
	var $index = true;
	var $indexFields = array('name', 'synopsis');

	var $score = 0;
	var $Search;
	
	function afterFind($results, $primary) {
		parent::afterFind($results, $primary);
		
		if(!empty($results[0]['Series']['name'])) {
			$results[0]['Series']['name'] = htmlentities($results[0]['Series']['name']);
		}
		
		return $results;
	}
	
	function afterSave($created) {
		if($this->index) {
			App::import('Model', 'SearchIndex');
			$this->Search = new SearchIndex();
		
			$this->Search->index('Series', $this->id);
		}
	}
	
	/**
	 * Check if more then 10 genres selected
	 * Used for validation
	 *
	 * @return boolean
	 */
	function limitGenres() {
		if(count($this->data['Genre']['Genre']) > 10) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get a single series
	 *
	 * @param integer $id 
	 * @return array
	 */
	function getSeries($id, $contain = false) {
		return $this->find('first', array('conditions' => array('Series.id' => $id), 'contain' => $contain));
	}
	
	/**
	 * Get a series' slug
	 *
	 * @param integer $id Series id
	 * @return string
	 */
	function getSlug($id) {
		$this->id = $id;
		return $this->field('slug');
	}
	
	/**
	 * Get a series' synopsis
	 *
	 * @param integer $id 
	 * @return string
	 */
	function getSynopsis($id) {
		$this->id = $id;
		return $this->field('synopsis');
	}
	
	/**
	 * Get the series type
	 * e.g. Anime or Manga
	 *
	 * @param integer $id 
	 * @return string
	 */
	function getType($id) {
		$series = $this->find('first', array('conditions' => array('Series.id' => $id), 'contain' => false));
		if($series['Series']['is_anime'] == 1) {
			return 'Anime';
		} else {
			return 'Manga';
		}
	}

	/**
	 * Get list of series
	 *
	 * @param string $type anime or manga
	 * @param string $letter A letter, misc or 09
	 * @param string $category A slug from the AnimeType model
	 * @return array
	 */
	function getList($type, $letter, $category) {
		$conditions = array();
		$seriesConditions = array();

		//Only display series which have sites linked
		 $conditions['Series.site_count !='] = 0;

		if($type == 'anime') {
			$conditions['Series.is_anime'] = 1;
		} else {
			$conditions['Series.is_manga'] = 1;
		}

		//$category contains a slug, find the actual id
		if(!empty($category)) {
			$animeType = $this->AnimeType->find('first', array('conditions' => array('AnimeType.slug' => $category), 'contain' => false));
			$conditions['Series.anime_type_id'] = $animeType['AnimeType']['id'];
		}

		//Narrow down the results by the first character
		if($letter =='09') {
			//Numbers
			$seriesConditions['Series.name REGEXP'] = '^[0-9]';
		} elseif ($letter =='misc') {
			//Non-alphanumeric characters
			$seriesConditions['Series.name REGEXP'] = '^[^0-9A-Za-z]';
		} else {
			//A-Z, If $letter is blank, displays all
			$seriesConditions['Series.name LIKE'] = $letter . '%';
		}

		$letters = $this->find('all',array('conditions' => $conditions, 'fields' => 'DISTINCT SUBSTRING(`name`, 1, 1) as letter', 'order' => 'letter', 'contain' => false));
		$series = $this->find('all',array('conditions' => array($seriesConditions, $conditions), 'order' => 'Series.name ASC', 'contain' => false));
		$categories = $this->AnimeType->find('all', array('conditions' => array('AnimeType.is_visible' => 1), 'contain' => false));

		return array('usedLetters' => $this->__buildMenu($letters), 'series' => $series, 'categories' => $categories);
	}
	
	/**
	 * Increment the vandalized count for a series
	 *
	 * @param integer $id 
	 * @return void
	 */
	function vandalized($id) {
		if(empty($id)) return false;
		$this->updateAll(array('Series.vandalized_count' => 'Series.vandalized_count+1'), array('Series.id' => $id));
	}

	/**
	 * Assign array of characters used into their appropriate groups
	 * E.g. '5' into '09', '#' into 'misc'
	 *
	 * @param array $characters
	 * @return array
	 * @access private
	 */
	function __buildMenu($characters) {
		$menu = array();
		foreach ($characters as $character) {
			$character = strtoupper(current($character[0]));
			if (eregi('[a-z]', $character)) {
				$menu[] = $character;
			} elseif (is_numeric($character)) {
				$menu[] = '09';
			} else {
				$menu[] = 'misc';
			}
		}

		return array_unique($menu);
	}

	/**
	 * Check if a series is locked
	 *
	 * @param integer $id
	 * @return boolean true for locked - false for unlocked
	 */
	function isLocked($id) {
		$this->id = $id;
		return $this->field('is_locked');
	}
}
?>
