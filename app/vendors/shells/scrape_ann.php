<?php

require "phpQuery.php";

class ScrapeAnnShell extends Shell {
	
	var $Series;
	var $SeriesTitle;
	var $animeTypes;
	var $Curl;
	
	function main() {
		pr('hello');
		Configure::write('debug', 2);
		
		//Load the neccessary models
		$this->Series = ClassRegistry::init('Series');
		$this->SeriesTitle = ClassRegistry::init('SeriesTitle');

		//Scrape the Anime News Network list page
		//$url = 'http://www.animenewsnetwork.com/encyclopedia/anime-list.php?alttitles=1&limit_to=&licensed=&sort=title';
		//$url = 'http://www.animenewsnetwork.co.uk/encyclopedia/anime-list.php?alttitles=1&showdate=1&showT=1&showM=1&showO=1&showG=1&showN=1&showS=1&licensed=&sort=date&invertsort=1';
		//$url = 'http://animetoplist.cake/serieslist.html';
		$url = 'http://www.animenewsnetwork.co.uk/encyclopedia/anime-list.php?showdate=1&limit_to=1000&showT=1&showM=1&showO=1&showG=1&showN=1&showS=1&licensed=&sort=date&invertsort=1';
		$html = $this->__scrapeUrl($url);
		
		echo 'Size: ';
		echo $html;

		$this->processSeries($html);
		$this->processTitles($html);
		
		return true;
	}
	
	/**
	 * Process the HTML to find series data
	 *
	 * @param string $html 
	 * @return void
	 */
	function processSeries($html) {
		//Find all series, ignoring alternative titles
		preg_match_all('%(?<!<i>)<a class=hoverline href="/encyclopedia/(?:manga|anime)\.php\?id=(?P<id>\d*)"><font color="#(?:[\w\d]*)">(?:<small>\((?P<the>.*?)\)</small> )?(?P<name>.*?)(?: \[.*?\])?(?: \((?P<type>.*?)\))?</font>%i', $html, $matches, PREG_SET_ORDER);
		
                $doc = phpQuery::newDocumentHTML($html);
                phpQuery::selectDocument($doc);

                $i = 0;
                foreach (pq('a[href*="?id="]') as $linki) {

		foreach($matches as $match) {
			//Check if the series already exists and skip if it does
			$series = $this->Series->find('first', array('conditions' => array('Series.ann_id' => $match['id']), 'contain' => false));
			if(!empty($series)) continue;
			
			$data = array();
			
			//Some titles don't have a type if it's contained
			//in the name or if it's a magazine
			if(empty($match['type'])) {
				$found = $this->__findType($match['name']);
				
				//Skip bad series
				if(!$found) {
					continue;
				}
				
				//Use the found type
				$match['type'] = $found;
			}
			
			//Is the type a Manga? if not, assume Anime
			if($match['type'] != 'manga') {
				$type = $this->Series->AnimeType->find('first', array('conditions' => array('AnimeType.matches' => $match['type']), 'contain' => false));
				$typeId = ife(!empty($type), $type['AnimeType']['id'], 99);
				$data = am(array('is_anime' => '1',	'anime_type_id' => $typeId), $data);
			} else {
				$data = am(array('is_manga' => '1', 'anime_type_id' => '0'), $data);
			}
			
			//ANN uses series begining with 'The' wrapped in tags
			//We need to handle these properly
			if($match['the'] == 'The') {
				//We have the word 'The' exactly
				$data = am(array('is_begins_the' => '1'), $data);
			} else if(!empty($match['the'])) {
				//The tags imply it exists but in another language
				//Prepend it to the name
				$match['name'] = String::insert(':the :name', array('the' => $match['the'], 'name' => $match['name']));
			}
			
			//Show name in shell for debug
			$this->out('--> New Series: ' . $this->__fixName($match['name']));
			
			$data = am(array('ann_id' => $match['id'], 'name' => $this->__fixName($match['name']), 'count' => '1'), $data);
			//$this->Model->create() is too slow, reset it manually
			$this->Series->id = false;
			$this->Series->save($data, false);
		}
	}
	
	/**
	 * Process the HTML to find main series titles plus
	 * alternative titles
	 * 
	 * Should always be run after processSeries as
	 * it relies on the series data
	 *
	 * @param string $html
	 * @return void
	 */
	function processTitles($html) {
		//Find including alternative titles
		preg_match_all('%(?P<alt><i>)?<a class=hoverline href="/encyclopedia/(?:manga|anime)\.php\?id=(?P<id>\d*)"><font color="#(?:[\w\d]*)">(?P<the><small>\(The\)</small> )?(?P<name>.*?)(?: \((?P<type>.*?)\))?</font>%i', $html, $matches, PREG_SET_ORDER);
		
		$this->SeriesTitle->Behaviors->detach('Searchable');
	
		foreach($matches as $match) {
			
			$name = $this->__fixName($match['name']);
	
			//Skip magazines
			if(substr($match['name'], 0, 1) == '[') {
				continue;
			}

			//Check if the title already exists and skip if it does
			$title = $this->SeriesTitle->find('first', array('conditions' => array('SeriesTitle.name' => $name, 'Series.ann_id' => $match['id']), 'contain' => array('Series')));
			if(!empty($title)) continue;
			
			$id = $this->Series->find('first', array('conditions' => array('Series.ann_id' => $match['id']), 'contain' => false));

			$data = array('series_id' => $id['Series']['id'], 'name' => $name);
			
			//Show name in shell for debug
			$this->out('--> New Title: ' . $this->__fixName($name));
			
			$this->SeriesTitle->data = array('SeriesTitle' => $data);
			
			//$this->Model->create() is too slow, reset it manually
			$this->SeriesTitle->id = false;
			$this->SeriesTitle->save();
		}
	}
	
	/**
	 * Attempt to find the series type
	 *
	 * @param string $name
	 * @access private
	 * @return mixed string on success - false on failure
	 */
	function __findType($name) {
		//Skip magazines
		if(substr($name, 0, 1) == '[') return false;
		
		//The possible series types and aliases
		$types = array(
			'TV' => array('TV'),
			'movie' => array('movie', 'motion picture'),
			'OAV' => array('OAV', 'OVA'),
			'ONA' => array('ONA'),
			'special' => array('special'),
			'manga' => array('manga')
		);
		
		//Attempt to find the type in the series name
		foreach($types as $type => $aliases) {
			foreach($aliases as $alias) {
				if(stripos($name, $alias) !== false) {
					return $type;
				}
			}
		}
		
		//No matches found
		return false;
	}
	
	/**
	 * Convert accented characters to their appropriate equivalents
	 *
	 * @param string $name
	 * @access private 
	 * @return string
	 */
	function __fixName($name) {
		$types = array(
			'Ō' => 'Oo',
			'ō' => 'ou',
			'ū' => 'uu',
			'·' => '-'
		);
		
		$name = str_replace(array('(', ')'), '', $name);
		
		return strip_tags(utf8_decode(strtr($name, $types)));
	}
	
	/**
	 * Scrape a url using the Curl model
	 *
	 * @param string $url 
	 * @access private
	 * @return $string
	 */
	function __scrapeUrl($url) {
		App::import('Model', 'Curl');
		
		$this->Curl = new Curl();
		$this->Curl->url = $url;
		$this->Curl->followLocation = true;
		$this->Curl->autoReferer = true;
		$this->Curl->returnTransfer = true;
		$this->Curl->userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36';
		$this->Curl->connectTimeout = 500;
		$this->Curl->timeout = 500;

		$this->Curl->execute();

		$data = $this->Curl->return;
		
		if(!empty($data)) return $data;
		
		return false;
	}


}
?>
