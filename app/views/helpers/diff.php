<?php
/**
 * DiffHelper class
 *
 * This class is a wrapper for PEAR Text_Diff with modified renderers from Horde
 * You need the stable Text_Diff from PEAR and (if you want to use them) two
 * renderers attached with this helper (sidebyside.php and character.php)
 *
 * To use this helper you either have to a) have pear libraries in your path
 * b) can use ini_set to set the path (default is app/vendors/)
 * c) change all requires in Text_Diff ;)
 *
 * @uses                AppHelper
 * @author 		Marcin Domanski aka kabturek <blog@kabturek.info>
 * @package             Dressing
 * @subpackage          .dressing.views.helpers
 */
class DiffHelper extends AppHelper {

	/**
	 * name of the helper
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'Diff';

	/**
	 * what engine should Text_Diff use.
	 * Avaible: auto (chooses best), native, xdiff
	 *
	 * @var string
	 * @access public
	 */
	var $engine = 'auto';

	/**
	 * what renderer to use ?
	 * for avaible renderers look in Text/Diff/Renderer/*
	 * Standard: unified, context, inline
	 * Additional: sidebyside
	 *
	 * @var string
	 * @access public
	 */
	var $renderer = 'sidebyside';

	/**
	 * Do you want to use the Character diff renderer additionally to the sidebyside renderer ?
	 * sidebyside renderer is the only one supporting the additional renderer
	 *
	 * @var bool
	 * @access public
	 */
	var $character_diff = true;

	/**
	 * If the params are strings on what characters do you want to explode the string?
	 * Can be an array if you want to explode on multiple chars
	 *
	 * @var mixed
	 * @access public
	 */
	var $explode_on = "\r\n";

	/**
	 * How many context lines do you want to see around the changed line?
	 *
	 * @var int
	 * @access public
	 */
	var $context_lines = 4;


	/**
	 * construct function
	 *
	 * @param mixed $one
	 * @param mixed $two
	 * @param mixed $three
	 * @access private
	 * @return void
	 */
	function __construct($one = null, $two = null, $three = null) {
		parent::__construct($one, $two, $three);
		if(function_exists('ini_set')){
			ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APP. 'vendors');
		}
		App::import('Vendor', 'Text', array('file' => 'Text/Diff.php'));
		//App::import('Vendor', 'Renderer', array('file' => 'Text/Diff/Renderer.php'));
	}

	/**
	 * compare function
	 * Compares two strings/arrays using the specified method and renderer
	 *
	 * @param mixed $original
	 * @param mixed $changed
	 * @access public
	 * @return void
	 */
	function compare($original, $changed){
		if(!is_array($original)){
			$original = $this->__explode($original);
		}
		if(!is_array($changed)){
			$changed = $this->__explode($changed);
		}
		$rendererClassName = 'Text_Diff_Renderer_'.$this->renderer;
		if(!class_exists($rendererClassName)) {
			App::import('Vendor', $this->renderer.'Renderer', array('file' => 'Text/Diff/Renderer/'.$this->renderer.'.php'));
		}
		$renderer = new $rendererClassName(array('context_lines' => $this->context_lines, 'character_diff' =>$this->character_diff));
		$diff = new Text_Diff($this->engine, array($original, $changed));

		$output = $this->output($renderer->render($diff));
		if(empty($output)) {
			//return implode($changed);
			return false;
		}

		return $output;

	}

	/**
	 * Create a diff of each revision
	 * @param array $revisions Revisions array
	 */
	function process($revisions) {
		foreach ($revisions as $num => $revision) {
			//Tne first revision doesn't need comparing
			if($revision['Revision']['revision_number'] != 1) {
				//Replace the name and description with the diff'd version
				$revisions[$num]['Revision']['content']['Series']['synopsis'] = $this->compare($revisions[$num + 1]['Revision']['content']['Series']['synopsis'], $revision['Revision']['content']['Series']['synopsis']);
				if(!empty($revision['Revision']['content']['Series']['image_filename']) && @$revision['Revision']['content']['Series']['image_filename'] != @$revisions[$num + 1]['Revision']['content']['Series']['image_filename']) {
					$revisions[$num]['Revision']['content']['Series']['new_image'] = true;
				}
			} else {
				if(!empty($revision['Revision']['content']['Series']['image_filename'])) {
					$revisions[$num]['Revision']['content']['Series']['new_image'] = true;					
				}
			}
		}

		return $revisions;
	}

	/**
	 * Compare two genre arrays
	 *
	 * @param array $revisions
	 * @param integer $num
	 */
	function compareGenres($revisions, $num) {

		$revision_number = $revisions[$num]['Revision']['revision_number'];

		$genres = array();

		//If this is the first revision, we don't want to
		//diff, just return the results in the right format
		if($revision_number == 1) {
			$genreOriginal = $revisions[$num]['Revision']['content']['Genre']['Genre'];

			//We have genres, format them correctly
			if(!empty($genreOriginal)) {
				foreach($revisions[$num]['Revision']['content']['Genre']['Genre'] as $genre) {
					$genres[$genre] = '';
				}

				return $genres;
			}

			//No genres, display nothing
			return false;
		}

		//Get both the current revision and the one which
		//came before it
		$revisionOld = $revisions[$num +1]['Revision']['content']['Genre']['Genre'];
		$revisionNew = $revisions[$num]['Revision']['content']['Genre']['Genre'];

		if(empty($revisionOld)) $revisionOld = array();
		if(empty($revisionNew)) $revisionNew = array();

		//Do the actual diffing
		$genreDiff = $this->__arrayDiff($revisionOld, $revisionNew);

		//Display nothing if there were no changes
		if(count($genreDiff['added']) == 0 && count($genreDiff['removed']) == 0) {
			return false;
		}

		//Add the appropriate classes
		foreach($genreDiff['list'] as $genre) {
			if(in_array($genre, $genreDiff['added'])) {
				$genres[$genre] = 'diff-add';
			} else if(in_array($genre, $genreDiff['removed'])) {
				$genres[$genre] = 'diff-delete';
			} else {
				$genres[$genre] = '';
			}
		}

		return $genres;
	}

	/**
	 * explodes the string into an array
	 *
	 * @param string $text
	 * @access private
	 * @return void
	 */
	function __explode($text){
		if(is_array($this->explode_on)){
			foreach($this->explode_on as $explode_on){
				$text =  explode($explode_on, $text);
			}
			return $text;
		}
		return  explode($this->explode_on, $text);
	}

	/**
	 * Find the different between two arrays
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 * @access private
	 */
	function __arrayDiff($array1, $array2 = array()) {
		$removed = array_diff($array1, $array2);
		$added = array_diff($array2, $array1);
		$list = array_unique(am($array1, $array2));

		return array('removed' => $removed, 'added' => $added, 'list' => $list);
	}
}
?>