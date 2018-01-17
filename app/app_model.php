<?php
class AppModel extends Model {
	
	var $actsAs = array('Containable');
	
	var $cleanData = true;
	var $persistModel = true;
	

public function __construct($id = false, $table = null, $ds = null) { 
        parent::__construct($id, $table, $ds); 
  
	if(class_exists('CakeSession')) {
	        $this->Session = new CakeSession(); 
	}
} 


	/**
	 * Fix calculated fields so they belong to the model array
	 * [0][count] becomes [Rating][count]
	 * 
	 * Old values are kept for backward conpatibility
	 *
	 * @param array $results 
	 * @param boolean $primary 
	 * @return void
	 */
	function afterFind($results, $primary=false) {
		parent::afterFind($results);
		if($primary == true) {
			if(Set::check($results, '0.0')) {
				$fields = array_keys( $results[0][0] );
				foreach($results as $key=>$value) {
					foreach( $fields as $fieldName ) {
						$results[$key][$this->alias][$fieldName] = $value[0][$fieldName];
					}
				}
			}
		}
		
		return $results;
	}

	function paginate ($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
			$args = func_get_args();
			$uniqueCacheId = '';
			foreach ($args as $arg) {
				$uniqueCacheId .= serialize($arg);
			}
			if (!empty($extra['contain'])) {
				$contain = $extra['contain'];	
			}
			$uniqueCacheId = md5($uniqueCacheId);
			$pagination = Cache::read('pagination-'.$this->alias.'-'.$uniqueCacheId, 'paginate_cache');
			if (empty($pagination)) {
				$pagination = $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'contain'));
				Cache::write('pagination-'.$this->alias.'-'.$uniqueCacheId, $pagination, 'paginate_cache');
			}
			return $pagination;
		}

		function paginateCount ($conditions = null, $recursive = 0, $extra = array()) {
			$args = func_get_args();
			$uniqueCacheId = '';
			foreach ($args as $arg) {
				$uniqueCacheId .= serialize($arg);
			}
			$uniqueCacheId = md5($uniqueCacheId);
			if (!empty($extra['contain'])) {
				$contain = $extra['contain'];	
			}

			$paginationcount = Cache::read('paginationcount-'.$this->alias.'-'.$uniqueCacheId, 'paginate_cache');
			if (empty($paginationcount)) {
				$paginationcount = $this->find('count', compact('conditions', 'contain', 'recursive'));
				Cache::write('paginationcount-'.$this->alias.'-'.$uniqueCacheId, $paginationcount, 'paginate_cache');
			}
			return $paginationcount;
		}

function findCached($conditions = null, $fields = array(), $order = null, $recursive = null) {
	if (Configure::read('Cache.disable') === false && Configure::read('Cache.check') === true && isset($fields['cache']) && $fields['cache'] !== false) {
		$key = $fields['cache'];
		$expires = '+1 hour';
		
		if (is_array($fields['cache'])) {
			$key = $fields['cache'][0];
			
			if (isset($fields['cache'][1])) {
				$expires = $fields['cache'][1];
			}
		}
		
		// Load from cache
		$results = Cache::read($key);
		
		if (!is_array($results)) {
			$results = parent::find($conditions, $fields, $order, $recursive);
			Cache::write($key, $results);
		}
		
		return $results;
	}
	
	// Not cacheing
	return parent::find($conditions, $fields, $order, $recursive);
}
}
?>
