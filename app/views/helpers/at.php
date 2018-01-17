<?php
class AtHelper extends AppHelper {

	var $helpers = array('Html');
	var $cssClasses;

	/**
	 * Set the class to premium for sites with premium membership
	 *
	 * @param array $site
	 */
	function premiumClass($site) {
		if(empty($site)) return false;

		return $site['Site']['is_premium'] ? 'premium' : '';
	}

	/**
	 * Set the class to top for sites with a high rank
	 *
	 * @param array $site
	 */
	function rankingTopClass($site) {
		if(empty($site) || $site['Site']['rank'] == 0) return false;

		return $site['Site']['rank'] < 7 ? 'top3' : '';
	}

	/**
	 * Convert the episode, language, quality or host integers
	 * to their text equivalent.
	 *
	 * @param string $type
	 * @param string $value
	 * @return void
	 */
	function streamingText($type, $site) {
		$textArray = array(
			'language' => array(1 => 'Subbed', 2 => 'Dubbed', 3 => 'Raw', 4 => 'Multiple'),
			'quality' => array(1 => 'High', 2 => 'Average', 3 => 'Low'),
			'host' => array(1 => 'Megavideo', 2 => 'Veoh', 3 => 'Youtube', 4 => 'MySpace', 5 => 'Other', 6 => 'Self-hosted')
		);

		$siteValue = false;

		//Try to use SeriesSite rating, then scraped value otherwise revert to global site value
		if($site[$type] > 0) {
			$value = $site[$type];
		} else if($type == 'language' && $site['language_scraped'] > 0) {
			$value = $site['language_scraped'];
		} else {
			$value = $site['Site'][$type];
			$siteValue = true;
		}


		$text = $value != 0 ? $textArray[$type][$value] : 'Unknown';

		if($siteValue && $text != 'Unknown') $text = '<span class="est" title="Estimated"></span>' . $text;

		return $text;
	}

	/**
	 * Generate a comment list by recursively looping through
	 * the comment data array.
	 *
	 * @param array $data
	 * @param integer $level
	 * @return string
	 */
	function commentList($data, $siteOwner, $level = 1) {
		$html = '';

		//No comments exist
		if(empty($data)) {
			$html .= $this->Html->tag('h3', 'No Comments');
			$html .= $this->Html->tag('p', 'There are no comments to display, why not add one?');
			return $this->Html->tag('li', $html, array('class' => 'no-data'));
		}

		$view = ClassRegistry::getObject('view');

		foreach($data as $comment) {
			$children = '';

			//If this comment have any children, recursively add them
			if(!empty($comment['children'])) {
				$children = $this->commentList($comment['children'], $siteOwner, $level + 1);
			}

			//Render the comment
			$html .= $view->element('sites/comment_box', array('comment' => $comment, 'children' => $children, 'level' => $level));
		}

		return $html;
	}

	/**
	 * Create a list of series that start with
	 * a character within a certain range
	 *
	 * @param integer $start
	 * @param array $series
	 * @return string
	 * @todo Count series and generate equal columns - Also take headings into consideration
	 */
	function listColumn($columnTotal, $seriesOriginal, $columnWidth = 9, $site_slug = null) {
		$range = am(array('#', '0-9'), range('A', 'Z'));
		$seriesLetter = array();

		//Are we dealing with anime or manga
		$type = $seriesOriginal[0]['Series']['is_anime'] == 1 ? 'anime' : 'manga';

		//First pass: Generate series array by first letter
		foreach($seriesOriginal as $series) {
			//Get the first letter of the series name
			$letter = strtoupper($series['Series']['name']{0});

			//Make sure numeric & special characters are grouped
			$letter = $this->__characterType($letter);

			$seriesLetter[$letter][] = $series;
		}

		$headingCount = count($range);
		$seriesCount = count($seriesOriginal);
		$seriesTotal = floor(($headingCount * 1.5) + $seriesCount);
		$seriesCount = 0;

		$columnSize = $columnWidth / $columnTotal;
		$columnEach = $seriesTotal / $columnTotal;
		$columnCurrent = 0;

		$lastLetter = '';


		$html = $columnHtml = '';

		foreach($seriesLetter as $letter => $seriesArray) {

			$columnHtml .= $this->Html->tag('h3', $letter);
			$columnHtml .= '<ul>';

			foreach($seriesArray as $key => $series) {
				if($site_slug) {
					$read_types = array('anime' => 'watch', 'manga' => 'read');
					$page_link = $this->Html->link($series['Series']['name'], array('controller' => 'out', 'action' => $read_types[$type], $series['Series']['slug'], $site_slug),  array('class' => 'external', 'rel' => 'nofollow'));
				} else {
	                                $page_link = $this->Html->link($series['Series']['name'], array('controller' => 'series', 'action' => $type, $series['Series']['slug']));
				}
				$columnHtml .= $this->Html->tag('li', $page_link);

				//Create a new column
				if($seriesCount > ($columnEach * ($columnCurrent + 1))) {
					$columnCurrent++;
					$continueLetter = false;

					if($key != count($seriesArray) && $columnCurrent != 1) {
						$continueLetter = $lastLetter;
					}


					$class = $this->__columnClass($columnCurrent, $columnTotal, $columnSize);
					$html .= $this->__createColumn($columnHtml, $class, $continueLetter);

					$lastLetter = $letter;
					$columnHtml = '';

				}

				$seriesCount++;
			}

			$columnHtml .= '</ul>';
			$seriesCount = $seriesCount + 1.5;
		}

		//Check if last or no columns have been created
		if(($columnCurrent != $columnTotal && count(array_keys($seriesLetter)) != 1) || $columnCurrent == 0) {
			$columnCurrent++;
			$class = $this->__columnClass($columnCurrent, $columnTotal, $columnSize);
			$html .= $this->__createColumn($columnHtml, $class, $lastLetter);
		}

		return $html;
	}

	function __columnClass($columnCurrent, $columnTotal, $columnSize) {
		$class = 'grid_' . $columnSize;
		if($columnCurrent == 1) $class .= ' alpha';
		if($columnCurrent == $columnTotal) $class .= ' omega';

		return $class;
	}

	function __createColumn($columnHtml, $class, $continueLetter = false) {
		//If we are not on the last result,
		//carry it over to the next column
		if(!empty($continueLetter)) {
			$columnHtml = '<h3>' . $continueLetter . ' (continued)</h3><ul>' . $columnHtml;
		}

		return $this->Html->tag('div', $columnHtml, array('class' => $class));
	}

	/**
	 * Return a list of css classes based on conditions
	 *
	 * @param array $data
	 * @return string
	 */
	function cssClass($data) {
		$this->cssClasses = null;

		foreach($data as $class) {
			switch ($class['type']) {
				case "=":
					if($class['condition'] == $class['value']) $this->__addClass($class['class']);
					break;
				case ">":
					if($class['condition'] > $class['value']) $this->__addClass($class['class']);
					break;
				case "<":
					if($class['condition'] < $class['value']) $this->__addClass($class['class']);
					break;
				case ">=":
					if($class['condition'] >= $class['value']) $this->__addClass($class['class']);
					break;
				case "<=":
					if($class['condition'] <= $class['value']) $this->__addClass($class['class']);
					break;
			}
		}

		return $this->cssClasses;
	}

	/**
	 * Return 'Yes' if true, 'No' if false
	 *
	 * @param integer $value
	 * @return string
	 */
	function yesNo($value) {
		return $value ? 'Yes' : 'No';
	}

	/**
	 * Find the series type from an array
	 *
	 * @param array $series
	 * @return string
	 */
	function getType($series) {
		return $series['is_anime'] ? 'Anime' : 'Manga';
	}

	function synopsisSource($url) {
		$types = array(
			'animenfo' => 'AnimeNFO',
			'animenewsnetwork' => 'Anime News Network',
			'myanimelist' => 'MyAnimeList'
		);

		if(strpos($url, 'http') !== false) {
			foreach($types as $type => $name) {
				if(strpos($url, $type)) {
					return $this->Html->link($name, $url, array('rel' => 'external'));
				}
			}
		}

		return $url;
	}

	function reviewText($value) {
		return $value > 0 ? $value . '/10' : 'n/a';
	}

	/**
	 * Add a new css class
	 *
	 * @access private
	 * @return void
	 */
	function __addClass($class) {
		//Multiple classes require a space in between
		if(!empty($this->cssClasses)) {
			$this->cssClasses .= ' ';
		}

		$this->cssClasses .= $class;
	}

	/**
	 * Find which type a character belongs to
	 *
	 * @param string $characters
	 * @return void
	 * @author Daniel
	 */
	function __characterType($char) {
		if (eregi('[a-z]', $char)) {
			return $char;
		} elseif (is_numeric($char)) {
			return '0-9';
		} else {
			return '#';
		}

		return false;
	}
}
?>
