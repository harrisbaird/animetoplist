<?php
class Site extends AppModel {

	var $name = 'Site';
	var $belongsTo = array('User');
	var $hasAndBelongsToMany = array('Series');
	var $hasMany = array(
		'SeriesSite' => array('dependant' => true),
		'Stat',
	);

	var $actsAs = array(
		'Uploader.FileValidation' => array(
				'extension' => array(
					'value'    => array('gif', 'jpg', 'png', 'jpeg'),
					'error'    => 'That filetype is not accepted',
				),
				'optional' => array(
					'value' => true
				)
			),
        'Sluggable' => array('label' => 'official_name', 'overwrite' => true),
	);

	var $validate = array(
		'url' => array(
							'url' => array(
  								'rule' => array('url', true),
  								'message' => 'Enter a valid URL including http://'),
							'isAccessible' => array(
								'rule' => 'isAccessible',
								'message' => 'Site is down'),
							'notEmpty' => array(
								'rule' => 'notEmpty',
								'message' => 'Enter a URL'),
							),
		'official_name' => array(
							'alphanumeric' => array(
  								'rule' => '/^[\\w\\s\.]+$/',
  								'message' => 'Official name may only contain letter and numbers'),
							'maxLength' => array(
								'rule' => array('maxLength', 20),
								'message' => 'official name must be less than 20 characters'),
							'notEmpty' => array(
								'rule' => 'notEmpty',
								'message' => 'You must enter an official name')
							),
		'title' => array(
							'notEmpty' => array(
								'rule' => 'notEmpty',
								'message' => 'You must enter a title')
							),
		'description' => array(
							'notEmpty' => array(
								'rule' => 'notEmpty',
								'message' => 'You must enter a description')
							),
		'streaming_url' => array(
                                                        'sameDomain' => array(
                                                                'rule' => 'sameDomain',
                                                                'message' => 'Streaming url must be on the same domain and begin with http://'),

							),
		'manga_url' => array(
                                                        'sameDomain' => array(
                                                                'rule' => 'sameDomain',
                                                                'message' => 'Streaming url must be on the same domain and begin with http://'),
							),
		'site_owner' => array(
							'equalTo' => array(
								'rule' => array('equalTo', '1'),
								'message' => 'You must own or moderate this site in order to add it to Anime Toplist')
							)
	);

	var $Curl = null;
	var $siteData = null;
	var $index = true;
	var $indexFields = array('official_name');

	/**
	 * Fetch a page using the Curl model
	 *
	 * @param string $url
	 * @return mixed string on success - false on failure
	 */
	function getSiteHtml($url) {
		return true;

		//Make sure downloads only happen once
		if(!empty($this->siteData)) {
			return $this->siteData;
		}

		App::import('Model', 'Curl');

		$this->Curl = new Curl();
		$this->Curl->url = $url;
		$this->Curl->followLocation = true;
		$this->Curl->autoReferer = true;
		$this->Curl->returnTransfer = true;
		$this->Curl->userAgent = 'Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5';

		$this->Curl->execute();

		$data = $this->Curl->return;

		if(!empty($data)) {
			$this->siteData = $data;
			return $data;
		}

		return false;
	}

	/**
	 * Check if a site can be accessed
	 * Validation callback
	 *
	 * @return mixed string on success - false on failure
	 */
	function isAccessible() {
		$data = $this->getSiteHtml($this->data['Site']['url']);
		if(!empty($data)) {
			return true;
		}

		return false;
	}

        function sameDomain($urls) {
		return true;
        }

	/**
	 * Get an array of sites which belong
	 * to a specified user
	 *
	 * @param integer $id
	 */
	function getByUserId($id) {
		return $this->find('all', array('conditions' => array('Site.user_id' => $id), 'contain' => false));
	}

	/**
	 * Find the language of a site
	 *
	 * @param string $url
	 * @return mixed array on success - false on failure
	 */
	function getLanguage($url) {
		//Include the language detection vendor
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APP. 'vendors');

		require_once 'Text/LanguageDetect.php';

		$data = $this->getSiteHtml($url);

		if(empty($data)) return false;

		//Find the language of the site
		$l = new Text_LanguageDetect;
		$result = $l->detectConfidence($data);

		if($result['confidence'] < 0.01) return false;

		return $result['language'];
	}

	/**
	 * Check if a site exists
	 *
	 * @param integer $id
	 * @return boolean
	 */
	function isValid($id) {
		$site = $this->find('first', array('conditions' => array('Site.id' => $id), 'contain' => false));
		return !empty($site) ? true : false;
	}

	/**
	 * Check if a site exists by slug
	 *
	 * @param integer $id
	 * @return boolean
	 */
	function isValidSlug($slug) {
		$site = $this->find('first', array('conditions' => array('Site.slug' => $slug), 'contain' => false));
		return !empty($site) ? true : false;
	}

	/**
	 * Check if a site belongs to a user
	 *
	 * @param integer $id
	 * @param integer $userId
	 * @return void
	 */
	function belongsTo($id, $userId) {
		$site = $this->find('first', array('conditions' => array('Site.id' => $id, 'Site.user_id' => $userId)));
		return !empty($site) ? true : false;
	}

	/**
	 * Reassign all sites from one user to another
	 *
	 * @param string $from
	 * @param string $to
	 * @param string $type username or id
	 * @return boolean true on success - false on failure
	 */
    function reassign($from, $to, $type = 'username') {
		//Check if we are using ids or usernames
		if($type == 'username') {
			$from = $this->User->userId($from);
			$to = $this->User->userId($to);
		}

        return $this->UpdateAll(array('Site.user_id' => $to), array('Site.user_id' => $from));
    }

	/**
	 * Fuzzy check to see whether a url has already been added
	 *
	 * @param string $url
	 * @return boolean
	 */
	function urlExists($url) {
		preg_match('/[A-Za-z][A-Za-z0-9+.-]{1,120}:[A-Za-z0-9\/](([A-Za-z0-9$_.+!*,;\/?:@&~=-])|%[A-Fa-f0-9]{2}){1,333}(#([a-zA-Z0-9][a-zA-Z0-9$_.+!*,;\/?:@&~=%-]{0,1000}))?/', $url, $matches);
        $url = isset($matches[0]) ? $matches[0] : $url;

		$site = $this->find('first', array('conditions' => array('Site.url LIKE' => $url . '%'), 'contain' => false));
		if(!empty($site)) {
			return true;
		}

		return false;
	}

	/**
	 * Get a sites' slug
	 *
	 * @param integer $id Site id
	 * @return string
	 */
	function getSlug($id) {
		$this->id = $id;
		return $this->field('slug');
	}

	function __strposa($haystack, $needles = array(), $offset = 0){
		$chr = array();
		foreach($needles as $needle) {
			$pos = strpos($haystack, $needle, $offset);
			if ($pos !== false) {
				return true;
			}
		}

		return false;
	}
}

function GetDomain($url) {
	$nowww = ereg_replace('www\.','',$url);
	$domain = parse_url($nowww);
	if(!empty($domain["host"])) {
		return $domain["host"];
	} else {
		return $domain["path"];
	}
}

?>
