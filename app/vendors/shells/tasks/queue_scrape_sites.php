<?php

require "phpQuery.php";

class queueScrapeSitesTask extends Shell {

	var $Curl;
	var $Site;
	var $Series;
	var $SeriesTitle;
	var $scrapeUrl;
	
	function run($data) {
		//Load the neccessary models
		$this->Site = ClassRegistry::init('Site');
		$this->Series = ClassRegistry::init('Series');
		$this->SeriesTitle = ClassRegistry::init('SeriesTitle');
		
		//We need the urls to scrape
		$site = $this->Site->find('first', array('conditions' => array('Site.id' => $data['siteId']), 'contain' => false));
		
		$this->out('- Site: ' . $site['Site']['official_name']);
		
		//Set is_active = 0 on all links with this site id
		//This way we don't need to keep deleting and reinserting
		$this->Series->SeriesSite->updateAll(array('SeriesSite.is_active' => 0), array('SeriesSite.site_id' => $data['siteId'], 'SeriesSite.is_forced' => 0));

 		$streaming = explode(';', $site['Site']['streaming_url']);
		$manga = explode(';', $site['Site']['manga_url']);

		print_r($streaming);
		print_r($manga);

		if($site['Site']['id'] == 1820 && $site['Site']['is_premium'] == 1) {
			$streaming[] = 'http://hentailicio.us/anime-top-list/';
			$manga[] = 'http://hentailicio.us/manga-top-list/';
		}

		//Scrape Anime
		foreach($streaming as $url) {
			$this->scrape($url, $site, 'anime');
		}

		foreach($manga as $url) {
                        $this->scrape($url, $site, 'manga');
                }


		if(empty($site['Site']['streaming_url'])) {
			echo "Removing any incorrectly added anime";
			$this->Series->SeriesSite->updateAll(array('SeriesSite.is_active' => 0), array('SeriesSite.site_id' => $site['Site']['id'], 'Series.is_anime' => 1));
		}

                if(empty($site['Site']['manga_url'])) { 
			echo "Removing any incorrectly added manga";
                        $this->Series->SeriesSite->updateAll(array('SeriesSite.is_active' => 0), array('SeriesSite.site_id' => $site['Site']['id'], 'Series.is_manga' => 1));
                }

                if($site['Site']['id'] == 1820) {
			$this->Series->SeriesSite->updateAll(array('SeriesSite.url' => 'http://3dfuckhouse.com/', 'SeriesSite.is_active' => 1), array('SeriesSite.site_id' => 1820, 'SeriesSite.is_forced' => 0));
                } 

		$this->__updateCounts($site['Site']['id']);

		if($site['Site']['id'] == 1650 && $site['Site']['is_premium']) {
			$this->Series->SeriesSite->query("UPDATE `animetoplist`.`series_sites` SET `mean_rating` = '100', `bayesian_rating` = '100' WHERE site_id = 1650");
		}


		//Scrape Manga
//		if(!empty($site['Site']['manga_url'])) {
//			$this->scrape($site['Site']['manga_url'], $data['siteId'], 'manga');
//		} else {
//			$this->Series->SeriesSite->updateAll(array('SeriesSite.is_active' => 0), array('SeriesSite.site_id' => $site['Site']['id'], 'Series.is_manga' => 1));
//			$this->__updateCounts($site['Site']['id']);
//		}
		
		return true;
	}


	function scrape($url, $site, $type = 'anime') {
		$siteId = $site['Site']['id'];

		if(empty($url) || empty($siteId)) {
			echo 'Url or site id not specified';
			return false;
		}

		//Find the correct field to use for the next query
		$seriesType = $type == 'anime' ? 'Series.is_anime' : 'Series.is_manga';

		$html = $this->__scrapeUrl($url);
		$this->scrapeUrl = $url;
		
		if(substr($url, 0, 4) !== 'http') {
			$url = 'http://' . $url;
		}
		
		$domain = str_replace('www.', '', parse_url($url, PHP_URL_HOST));

		//Extract links from html, grouped by link
		$doc = phpQuery::newDocumentHTML($html);
		phpQuery::selectDocument($doc);

		$i = 0;
		foreach (pq('a') as $linki) {
			//Check if the link is for this domain and
			//expand partial urls into full ones
			if(!pq($linki)->attr('href')) continue;

			$link = array();

			$link['url'] = $this->validateUrl($domain, pq($linki)->attr('href'));
			$link['title'] = strip_tags(pq($linki)->text());
			if(empty($link['title'])) {
				$link['title'] = pq($linki)->attr('title');
			}

			//Check if this link is blacklisted
			if($greedy_scrape || $this->checkBlacklist($link)) {
				//Is this link already in the database
				if($exists = $this->linkExists($siteId, $link)) {
					$seriesId = array('seriesId' => $exists['SeriesSite']['series_id'], 'title' => $link['title'], 'url' => $link['url']);
				} else {
					//Find the closest match and save if found
					$match = $this->__findSimilar($link['title'], $seriesType);
					if($match != false) { 
						$seriesId = array('seriesId' => $match['seriesId'], 'match' => $match['match'], 'title' => $link['title'], 'url' => $link['url']);
					} else {

					}
				}

				if(strlen($ids['title']) < 3 && strlen($ids['match']) > 8) continue;
	                        $this->__save($url, $seriesId, $siteId);
				
				$i++;
			}
		}

		$this->__updateCounts($siteId);
	}
	
	/**
	 * Check if the url is on the same domain and
	 * make sure the link contains a full domain
	 *
	 * @param string $siteUrl 
	 * @param string $linkUrl 
	 * @return string
	 */
	function validateUrl($siteDomain, $link) {
		$linkHost = parse_url($link, PHP_URL_HOST);
		$linkDomain = r('www.', '', $linkHost);

		$link = http_build_url($link, array('scheme' => 'http', 'host' => $siteDomain));

		//If we have a full url, check if the domains match
		if(strpos($link, 'http://') !== false) {
			if($linkDomain == $siteDomain) {
				return $link;
			}

//			return false;
		}
		
		if(substr($link, 0, 1) == '/') {
			return $siteDomain . $link;
		}
		
		return $this->absoluteUrl($this->scrapeUrl, $link);
	}
	
	function linkExists($siteId, $link) {
		return $this->Series->SeriesSite->find('first', array('conditions' => array('SeriesSite.site_id' => $siteId, 'SeriesSite.url' => $link['url'], 'SeriesSite.link_text' => $link['title']), 'contain' => false));
	}

	/**
	 * Check if a title contains any banned words
	 *
	 * @param string $title 
	 * @return boolean
	 */
	function checkBlacklist($link) {
		$titleBlacklist = array('<img', 'home', 'comment', 'report', 'episode', 'privacy', 'forum', 'discuss', 'about', 'faq', 'subscribe', 'beta', 'trackback', 'pingback', 'anime movies', 'request', 'list', 'click', 'video', 'contact', 'email', 'rss', 'game', 'cartoon', 'hentai', 'drama', 'here', 'staff', 'news', 'update', 'show', 'terms', 'copyright', 'advertise', 'login', 'register', 'movies', 'myspace', 'facebook', 'twitter', 'misc', 'categor', 'news');
		$urlBlacklist = array();
		$letters = range('A', 'Z');

		//Check if title contains blacklisted words
		if($this->__strposa(strtolower($link['title']), $titleBlacklist)) {
			echo "Title blacklist";
			return false;
		}
		
		if($this->__strposa(strtolower($link['url']), $urlBlacklist)) {
			echo "Url blacklist";
			return false;
		}
		
		if(is_numeric($link['title'])) return false;

		//Check for single letters, needs sanitized title
		$titleSanitized = trim(strip_tags($link['title']));
		if(in_array(strtoupper($titleSanitized), $letters)) return false;

		//Does title parse as date
		if(strtotime($link['title']) === true) return false;

		return true;
	}

	/**
	 * Find the closest match using a FullText search
	 *
	 * @param string $title 
	 * @param string $type 
	 * @access private
	 * @return void
	 */
	function __findSimilar($title, $type, $secondPass = false) {
		//Check in the cache first
		$title = trim(preg_replace("/\([^)]+\)/","",$title));
		$cacheName = 'siteScrape' . md5($title);
		$cache = Cache::read($cacheName);
		$title = mysql_real_escape_string($title);
		if($cache === false) {
			//Enable sort order
			$orderFt = array('relevance DESC');
			$orderExact = array();
			
			if($type == 'is_anime') {
				$orderExact = am($orderExact, array('AnimeType.priority DESC'));
				$orderFt = am($orderFt, array('AnimeType.priority DESC'));
			}
			
			//Try exact match
			$titles = $this->SeriesTitle->find('first', array('conditions' => array('SeriesTitle.name LIKE' => '%' . $title . '%', $type => 1), 'order' => $orderExact, 'contain' => array('Series' => array('AnimeType'))));
			
			//If not found, try a full text search
			if(empty($titles)) {
				$ftCondition = String::insert('MATCH (SeriesTitle.name) AGAINST (\':title\')', array('title' => $title));
				$titles = $this->SeriesTitle->find('first', array('fields' => array('name', 'series_id', $ftCondition.' AS relevance'), 'conditions' => array($ftCondition, $type => 1), 'order' => $orderFt, 'contain' => array('Series' => array('AnimeType'))));
			}

                        if(empty($titles)) {
				$metaphone = double_metaphone($title);
				$titles = $this->SeriesTitle->find('first', array('conditions' => array('SeriesTitle.metaphone' => $title, $type => 1), 'order' => $orderExact, 'contain' => array('Series' => array('AnimeType'))));
                        }

			if(empty($titles)) {
				$this->out('Unable to match: ' . $title, 'scrape_site');
				return false;
			}
			
			//Add this to the cache
			Cache::set(array('duration' => '+1 days'));
			Cache::write($cacheName, $titles);
		} else {
			$titles = $cache;
		}
		
		return array('match' => $titles['SeriesTitle']['name'], 'seriesId' => $titles['SeriesTitle']['series_id']);
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
		$this->Curl->userAgent = 'Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5';
		$this->Curl->connectTimeout = 60;
		$this->Curl->timeout = 500;

		$this->Curl->execute();

		$data = $this->Curl->return;
		$httpCode = $this->Curl->getInfo('http_code');
		echo "Page size: " . strlen($data);
		echo "Status code: " . $httpCode;

		if($httpCode != 200) return false;

		if(!empty($data)) return $this->gunzip($data);

		return false;
	}
	

	function gunzip($zipped) {
		$offset = 0;
		if (substr($zipped,0,2) == "\x1f\x8b")
			$offset = 2;
		if (substr($zipped,$offset,1) == "\x08")  {
	        	return gzinflate(substr($zipped, $offset + 8));
		}
		return $zipped;;
	}

	/**
	 * Link the anime to the site, reactivating an existing
	 * entry if available.
	 *
	 * @param array $data 
	 * @param integer $siteId 
	 * @access private
	 * @return void
	 */
	function __save($baseUrl, $seriesData, $siteId) {
		//Check if the link already exists
		$link = $this->Series->SeriesSite->find('first', array('conditions' => array('SeriesSite.series_id' => $seriesData['seriesId'], 'SeriesSite.site_id' => $siteId), 'order' => 'ratings_count',  'contain' => false));
		if($link['SeriesSite']['is_forced']) return;
		//Does the link text cnntain subbed or dubbed
		$types = array('sub', 'dub', 'raw');
		
		$language = 0;
		
		foreach($types as $id => $type) {
			if(stripos($seriesData['title'], $type) !== false || stripos($seriesData['url'], $type) !== false) {
				$language = $id + 1;
				break;
			}
		}
		
		if(!empty($link)) {
			//The link already exists, reactivate it providing it
			// hasn't been previously disabled
			if($link['SeriesSite']['is_disabled'] != 1) {
				$this->Series->SeriesSite->id = $link['SeriesSite']['id'];
				
				$data['SeriesSite']['is_active'] = 1;
				$data['SeriesSite']['language_scraped'] = $language;
				if($language != 0) $data['SeriesSite']['language'] = $language;
				
				if(!empty($seriesData['url']) && strpos('http://', $seriesData['url']) !== false) {
					$data['SeriesSite']['url'] = $seriesData['url'];
				}

				$this->Series->SeriesSite->save($data);
			}
		} else {
			//Link doesn't exist, create a new one
			$linkData = array('SeriesSite' => array('series_id' => $seriesData['seriesId'], 'site_id' => $siteId, 'link_text' => $seriesData['title'], 'url' => $seriesData['url'], 'match_text' => $seriesData['match'], 'is_active' => 1, 'language_scraped' => $language));
			//$this->Model->create() is too slow, reset it manually
			$this->Series->SeriesSite->id = false;
			$link = $this->Series->SeriesSite->save($linkData, false);
		}
	}
	
	/**
	 * Update the Anime and Manga counts
	 * for a certain site
	 *
	 * @param integer $siteId 
	 * @return void
	 */
	function __updateCounts($siteId) {
		$animeCount = $this->Series->SeriesSite->find('count', array('conditions' => array('SeriesSite.site_id' => $siteId, 'SeriesSite.is_active' => 1, 'Series.is_anime' => 1), 'contain' => 'Series'));
		$mangaCount = $this->Series->SeriesSite->find('count', array('conditions' => array('SeriesSite.site_id' => $siteId, 'SeriesSite.is_active' => 1, 'Series.is_manga' => 1), 'contain' => 'Series'));
		
		$data = array('Site' => array('anime_count' => $animeCount, 'manga_count' => $mangaCount));
		
		$this->Site->id = $siteId;
		$this->Site->save($data);
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
			' - ' => '-',
			'(' => '',
			')' => ''
		);
		$name = html_entity_decode($name);
		$name = trim(strip_tags(utf8_decode(strtr($name, $types))));
		//$name = preg_replace("/[^a-zA-Z0-9\s]/", "", $name);
		
		return $name;
	}

	/**
	 * Compare two arrays using strpos
	 *
	 * @param array $haystack 
	 * @param array $needles 
	 * @param integer $offset 
	 * @access private
	 * @return boolean
	 */
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
	
	/**
	 * Convert a relative url to an absolute one
	 *
	 * @param string $absolute 
	 * @param string $relative 
	 * @return string
	 */
    function absoluteUrl($absolute, $relative) {
        $p = parse_url($relative);
        if($p["scheme"])return $relative;
        
        extract(parse_url($absolute));
        
        $path = dirname($path); 
    
        if($relative{0} == '/') {
            $cparts = array_filter(explode("/", $relative));
        }
        else {
            $aparts = array_filter(explode("/", $path));
            $rparts = array_filter(explode("/", $relative));
            $cparts = array_merge($aparts, $rparts);
            foreach($cparts as $i => $part) {
                if($part == '.') {
                    $cparts[$i] = null;
                }
                if($part == '..') {
                    $cparts[$i - 1] = null;
                    $cparts[$i] = null;
                }
            }
            $cparts = array_filter($cparts);
        }
        $path = implode("/", $cparts);
        $url = "";
        if($scheme) {
            $url = "$scheme://";
        }
        if($user) {
            $url .= "$user";
            if($pass) {
                $url .= ":$pass";
            }
            $url .= "@";
        }
        if($host) {
            $url .= "$host/";
        }
        $url .= $path;
        return $url;
    }
	
}
?>
