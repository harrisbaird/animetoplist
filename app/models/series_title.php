<?php
class SeriesTitle extends AppModel {

	var $name = 'SeriesTitle';

	var $belongsTo = array('Series');
	var $index = true;
	var $indexFields = array('name');

	function afterSave() {
		if(empty($this->data['SeriesTitle']['metaphone'])) {
			$metaphone = double_metaphone($this->data['SeriesTitle']['name']);
			if(strlen($metaphone[0]) > 5) {
				$this->saveField('metaphone', $metaphone[0]);
			}
		}
		return true;
	}

	
}
?>
