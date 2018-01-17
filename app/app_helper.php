<?php
class AppHelper extends Helper {
	var $pageTitle;
	var $pageHeader;
	var $pageDescription;
	var $headerContent;
	
	/**
	 * Set the page title also return
	 * so it can be used in the page header
	 *
	 * @param string $title 
	 * @return string
	 */
	function setHeader($title) {
		$this->pageHeader = $title;
		return $this->pageTitle = $title;
	}
	
	function setTitle($title) {
		$this->pageTitle = $title;
	}
	
	/**
	 * Set the data which appears above / next to
	 * the header
	 *
	 * @param string $data 
	 * @return void
	 */
	function setHeaderContent($data) {
		$this->headerContent = $data;
	}
	
	/**
	 * Generate the page title
	 *
	 * @return string
	 */
	function getPageTitle() {
		$title = 'Anime Toplist';
		if(!empty($this->pageTitle)) {
			$title = $this->pageTitle . ' - ' . $title;
		}
		
		return $title;
	}
	
	function getPageDescription() {
		if(!empty($this->pageDescription)) {
			$title = $this->pageDescription;
		} else {
			$title = 'Find the best place to watch streaming anime across hundreds of sites.';
		}
		
		return $title;
	}
	
	/**
	 * Override url generation so plugin is set to 
	 * null if it doesn't exist, fixes bug in plugins.
	 *
	 * @param mixed $url 
	 * @param boolean $full 
	 * @return string
	 * @todo Remove this when fix available
	 */
	function url($url = null, $full = false) {
		if(!isset($url['plugin']) && !empty($this->plugin) && $this->plugin != 'forum'){
 			$url['plugin'] = null;
		}
		
		return parent::url($url, $full);
	}


	/**
	 * Generate a twitter style relative date
	 * 
	 * Note: CakePHP already has this functionality in the
	 * time helper, this one displays a much simpler version
	 *
	 * @param string $time 
	 * @return void
	 */
	function relativeDate($time) {
		$time = strtotime($time);
		
		//The Output the time is used at
		$format = "F m, Y";

		//Time presets for the lazy (Time goes by seconds)
		$timeyear = 365 * 24 * 60 * 60;
		$timemonth = 30 * 7 * 24 * 60 * 60;
		$timeweek = 7 * 24 * 60 * 60;
		$timeday = 24 * 60 * 60;
		$timehour = 60 * 60;
		$timemins = 60;
		$timeseconds = 1;

		//today's date
		$today = time();

		//Get the time from today by minusing the time looked at by today's date
		$x = $today - $time;

		//These define the out put
		if($x >= $timeyear){$x = date($format, $time); $dformat="";
		}elseif($x >= $timemonth){$x = date($format, $time); $dformat="";
		}elseif($x >= $timeday){$x = round($x / $timeday); $dformat="days ago"; $x = round($x);
		}elseif($x >= $timehour){$x = round($x / $timehour); $dformat="hours ago";
		}elseif($x >= $timemins){$x = round($x / $timemins); $dformat="minutes ago";
		}elseif($x >= $timeseconds){$x = round($x / $timeseconds); $dformat="seconds ago";
		}else{$x = 'Very'; $dformat = 'recently';}
	
		return $x." ".$dformat;
	}
	
	/**
	 * Creates a comma separated list where the last two items are
	 * joined with 'and', forming natural English
	 * 
	 * Modified from the CakePHP version so spaces are not automatically
	 * added around 'and'
	 * 
	 * @param array $list
	 * @param string $and
	 * @param string $separator
	 * @return string
	 */
	function toList($list, $and = ' and ', $separator = ', ') {
        return implode($separator, array_slice($list, null, -1)) . $and . array_pop($list);
	}
}
?>