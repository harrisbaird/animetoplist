<?php
/**
 * NavigationHelper
 *
 * This helper helps you build your menu's and adds some extra functionality to links
 *
 * PHP versions 4 and 5
 *
 * Licensed under The LGPL License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pagebakers
 * @link			http://www.pagebakers.nl
 * @version         0.1
 * @license			http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
 */
class NavigationHelper extends HtmlHelper {

    /**
     * Returns a formatted <ul> with links
     *
     * Changed so attributes can also be applied to all links as well as the UL.
     * $attributes = array('ul' => array(), 'links' => array());
     *
     * @param array $items An array containing all children for the list
     * @param array $options The html attributes for the list
     * @return string The formatted <ul> list
     */
    function menu($items, $attributes = array()) {
        if(!is_array($items) || empty($items)) {
            return;
        }

		$defaults = array(
			'ul' => array(),
			'li' => array(),
			'a' => array(
				'escape' => false
			)
		);

		//Merge attributes into defaults
		$attributes = array_merge_recursive($defaults, $attributes);

        $links = $class = array();
        
        foreach($items as $item) {
            $attributes['li_custom'] = $attributes['li'];
            
            if(count($item) == 2) {
                list($text, $url) = $item;
                $itemOptions = array();
            }else{
            	list($text, $url, $itemOptions) = $item;
            }
            
            if(!empty($itemOptions['li'])) {
                $attributes['li_custom'] = am($itemOptions['li']);
                unset($itemOptions['li']);
            }

			//Merge individual options with global, defaults to individual
			$itemOptions = am($attributes['a'], $itemOptions);

			//Changed to use NavigationHelper::link rather than parent::link
            $links[] = $this->tag('li', $this->link($text, $url, $itemOptions), $attributes['li_custom'], false);
            unset($class, $itemOptions, $attributes['li_custom']);
        }

        return sprintf($this->tags['ul'], $this->_parseAttributes($attributes['ul'], null, ' ', ''), implode("\n", $links));
    }

    /**
     * Returns a formatted <ul> with links
     * @param array $items An array containing all children for the list
     * @param array $options The html attributes for the list
     * @return string The formatted <ul> list
     */
    function breadcrumbs($items, $attributes = array()) {
        if(!is_array($items) || empty($items)) {
            return;
        }

        $links = array();
        $i = 1;
        $count = count($items);
        foreach($items as $item) {
            if(count($item) == 2) {
                list($text, $url) = $item;
                $itemOptions = array();
            } else {
                list($text, $url, $itemOptions) = $item;
            }
            if($i < $count) {
                $links[] = sprintf($this->tags['li'], '', parent::link($text, $url, $itemOptions));
            } else {
                $links[] = sprintf($this->tags['li'], ' class="last"', sprintf('<span%s>%s</span>', $this->_parseAttributes($itemOptions), $text));
            }
            $i++;
        }

        return sprintf($this->tags['ul'], $this->_parseAttributes($attributes, null, ' ', ''), implode("\n", $links));
    }

    /**
     * Returns a link with class="active" if the url is the currently active url
     * @param string $title The content to be wrapped in <a/>
     * @param string $url The url of the link
     * @param array $options Html attributes of the link
     * @return string an <a/> element
     */
    function link($title, $url, $options = array()) {
        if($this->isActive($url)) {

			if(isset($options['active'])) {
				$class = $options['active'];
			} else {
				$class = 'active';
			}

            if(isset($options['class'])) {
                $options['class'] .= ' ' . $class;
            } else {
                $options['class'] = $class;
            }
        }

		unset($options['active']);

		if(isset($options['wrap'])) {
			$title = $this->tag($options['wrap'], $title, array(), false);
			unset($options['wrap']);
		}

        $out = parent::link($title, $url, $options);

        return $out;
    }

    /**
     * Checks if a given url is currently active
     * @param mixed $url The url to check, can be and valid router string or array
     * @return boolean Returns true if the passed url is active
     */
    function isActive($url) {
        $currentRoute = Router::url(null);

        $url = Router::url($url);

        if($currentRoute == $url) {
            return true;
        }

        return false;
    }

    /**
     * Checks if a given url is currently active controller
     * @param mixed $url The url to check, can be and valid router string or array
     * @return boolean Returns true if the passed url is active
     */
    function isActiveController($url) {
        if(!is_array($url)) {
            $url = Router::parse($url);
        }

        if($url['controller'] == $this->params['controller']) {
            return true;
        }

        return false;
    }
}
?>