<?php
class Genre extends AppModel {

	var $name = 'Genre';

	var $hasAndBelongsToMany = array('Series');
}
?>