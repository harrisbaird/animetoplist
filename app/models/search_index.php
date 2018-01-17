<?php
class SearchIndex extends AppModel {
	var $name = 'SearchIndex';
	var $useTable = 'search_index';
	private $models = array();
	
	private function bindTo($model) {
		$this->bindModel( 
			array(
				'belongsTo' => array(
					$model => array (
						'className' => $model,
						'conditions' => 'SearchIndex.model = \''.$model.'\'',
						'foreignKey' => 'association_key'
					)
				)
			),false 
		);
	}

	function searchModels($models = array()) {
		if (is_string($models)) $models = array($models);
		$this->models = $models;
		foreach ($models as $model) {
			$this->bindTo($model);
		}
	}

	function beforeFind($queryData) {
		$models_condition = false;
		if (!empty($this->models)) {
			$models_condition = array();
			foreach ($this->models as $model) {
				$Model = ClassRegistry::init($model);
				$models_condition[] = $model . '.'.$Model->primaryKey.' IS NOT NULL'; 
			}
		}

		if (isset($queryData['conditions'])) {
			if ($models_condition) {
				if (is_string($queryData['conditions'])) {
					$queryData['conditions'] .= ' AND (' . join(' OR ',$models_condition) . ')';
				} else {
					$queryData['conditions'][] = array('OR' => $models_condition);
				}
			}
		} else {
			if ($models_condition) {
				$queryData['conditions'][] = array('OR' => $models_condition);
			}
		}
		return $queryData; 	
	}
	
	/**
	 * Index the fields of a single row
	 *
	 * @param string $modelName 
	 * @param integer $id 
	 * @return void
	 */
	function index($modelName, $id) {
		$index = array();
		
		App::import('Model', $modelName);
		$model = new $modelName;
		
		if(isset($model->index) && !$model->index) return false;
		
		if(isset($model->indexFields)) {
			
			$source = $model->find('first', array('conditions' => array($modelName . '.id' => $id), 'contain' => false));
			
			foreach($source[$modelName] as $field => $value) {
				if(in_array($field, $model->indexFields)) {
					$index[] = strip_tags(html_entity_decode($value, ENT_COMPAT, 'UTF-8'));
				}
			}
			
			$index = join('. ', $index);
			$index = iconv('UTF-8', 'ASCII//TRANSLIT', $index);
			$index = preg_replace('/[\ ]+/', ' ', $index);
			
			$searchId = $this->find('first', array('conditions' => array('SearchIndex.association_key' => $id, 'SearchIndex.model' => $modelName)));
			
			$this->id = false;
			
			if(!empty($searchId)) {
				$this->id = $searchId['SearchIndex']['id'];
			}
			
			$this->save(array(
				'association_key' => $id,
				'model' => $modelName,
				'data' => $index
			));
		}
		
		unset($model);
	}
	
	/**
	 * Index multiple models
	 *
	 * @param array $models 
	 * @return void
	 */
	function indexModels($models) {
		foreach($models as $modelName) {
			App::import('Model', $modelName);
			$model = new $modelName;
			
			$results = $model->find('all', array('contain' => false));

			foreach($results as $result) {
				$this->index($modelName, $result[$modelName]['id']);
			}
			
			unset($model);
		}
	}

}
?>