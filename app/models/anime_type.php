<?php
class AnimeType extends AppModel {

	var $name = 'AnimeType';

	var $actsAs = array('Sluggable' => array('label' => 'name'));
	var $hasMany = array('Series');

}
?>