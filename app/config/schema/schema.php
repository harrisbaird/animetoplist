<?php 
/* App schema generated on: 2013-07-20 17:20:57 : 1374340857*/
class AppSchema extends CakeSchema {
	var $name = 'App';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $anime_types = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'matches' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'priority' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1),
		'is_visible' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'matches' => array('column' => 'matches', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $coupons = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1, 'comment' => '1: Trial, 2: Percentage, 3: Amount'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'note' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'amount' => array('type' => 'float', 'null' => false, 'default' => NULL, 'length' => '8,2'),
		'minimum_spend' => array('type' => 'float', 'null' => true, 'default' => '0', 'length' => 8),
		'use_limit' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 5),
		'premium_order_count' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'is_expired' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'expires' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $genres = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $genres_series = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'genre_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'series_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'genre_id' => array('column' => 'genre_id', 'unique' => 0), 'series_id' => array('column' => 'series_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $premium_items = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'premium_order_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'site_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'premium_weeks' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'boost_weeks' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'premium_expires_at' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'boost_expires_at' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created_at' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'updated_at' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'index_premium_items_on_premium_order_id' => array('column' => 'premium_order_id', 'unique' => 0), 'index_premium_items_on_site_id' => array('column' => 'site_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);
	var $premium_orders = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'discount_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'premium_weeks' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'boost_weeks' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'price_cents' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'currency' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'status' => array('type' => 'string', 'null' => true, 'default' => 'unverified', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'transaction_id' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'is_paid' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created_at' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'updated_at' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'index_premium_orders_on_user_id' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);
	var $ratings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'series_site_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'ip_address' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'episode' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1),
		'language' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1),
		'quality' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1),
		'host' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 1),
		'score' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'series_site_id' => array('column' => 'series_site_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $revisions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'node_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'content' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'revision_number' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'rollback_number' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'is_rollback' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'ip_address' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 15, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'type' => array('column' => array('type', 'node_id'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	var $search_index = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'association_key' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 128, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'data' => array('type' => 'text', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'model' => array('column' => 'model', 'unique' => 0), 'data' => array('column' => 'data', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	var $series = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'anime_type_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'ann_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'unique'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'synopsis' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'synopsis_source' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'image_filename' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'image_small_filename' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'image_medium_filename' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'image_position' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 3),
		'featured_title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'featured_position' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 1),
		'is_featured' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'is_anime' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
		'is_manga' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
		'is_begins_the' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'is_locked' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'site_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'key' => 'index'),
		'new_count' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'vandalized_count' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'image_url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ann_id' => array('column' => 'ann_id', 'unique' => 1), 'anime_type_id' => array('column' => 'anime_type_id', 'unique' => 0), 'slug' => array('column' => 'slug', 'unique' => 0), 'is_anime' => array('column' => 'is_anime', 'unique' => 0), 'is_manga' => array('column' => 'is_manga', 'unique' => 0), 'site_count' => array('column' => 'site_count', 'unique' => 0), 'image_url' => array('column' => 'image_url', 'unique' => 0), 'name' => array('column' => 'name', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $series_reviews = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'series_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'body' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'story' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2),
		'characters' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2),
		'animation' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2),
		'sound' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2),
		'overall' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'series_id' => array('column' => 'series_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $series_sites = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'series_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'site_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'link_text' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'match_text' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'episode' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1),
		'language' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1),
		'language_scraped' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1),
		'quality' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1),
		'host' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1),
		'ratings_count' => array('type' => 'integer', 'null' => true, 'default' => '0', 'key' => 'index'),
		'mean_rating' => array('type' => 'integer', 'null' => true, 'default' => '50'),
		'bayesian_rating' => array('type' => 'integer', 'null' => true, 'default' => '50'),
		'is_active' => array('type' => 'boolean', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'is_disabled' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'is_forced' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'series_id' => array('column' => 'series_id', 'unique' => 0), 'site_id' => array('column' => 'site_id', 'unique' => 0), 'is_active' => array('column' => 'is_active', 'unique' => 0), 'ratings_count' => array('column' => 'ratings_count', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $series_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'series_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'tag_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $series_titles = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 6, 'key' => 'primary'),
		'series_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 7, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'metaphone' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_hidden' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 4),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'series_id' => array('column' => 'series_id', 'unique' => 0), 'name_index' => array('column' => 'name', 'unique' => 0), 'metaphone' => array('column' => 'metaphone', 'unique' => 0), 'name' => array('column' => 'name', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'value' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'label' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'order' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	var $sites = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => NULL, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'official_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'rank' => array('type' => 'float', 'null' => false, 'default' => '200.00', 'length' => '6,2'),
		'old_username' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'anime_count' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'manga_count' => array('type' => 'integer', 'null' => true, 'default' => '0'),
		'streaming_url' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'manga_url' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'scrape_selector' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'language' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1),
		'quality' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1),
		'host' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1),
		'is_deleted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_premium' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
		'is_boosted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_boost_shifted' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'is_dead' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'is_verified' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
		'is_premium_paused' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'disable_comments' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'disable_bar' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'has_banner' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'premium_box_bg' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 7, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'premium_box_title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 7, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'premium_box_text' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 7, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'dead_count' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 2),
		'dead_reason' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 1, 'comment' => '1: Invalid domain, 2: Parked domain, 3: 404, '),
		'dead_date' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'scrape_failed' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'scrape_message' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'forced_update' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'skip_check' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'email_date' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'premium_expires_at' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'boost_expires_at' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'notes' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_id' => array('column' => 'user_id', 'unique' => 0), 'slug' => array('column' => 'slug', 'unique' => 0), 'is_premium' => array('column' => 'is_premium', 'unique' => 0), 'is_validated' => array('column' => 'is_verified', 'unique' => 0), 'id' => array('column' => 'id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	var $stats = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 8, 'key' => 'primary'),
		'site_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 5, 'key' => 'index'),
		'date' => array('type' => 'date', 'null' => false, 'default' => NULL),
		'pageviews' => array('type' => 'float', 'null' => false, 'default' => '0.00', 'length' => '3,2'),
		'reach' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 5),
		'links' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 5),
		'unique_views' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 7),
		'total_views' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 7),
		'unique_in' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 5),
		'total_in' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 5),
		'unique_out' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 5),
		'total_out' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 5),
		'rank' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 5),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'site_id' => array('column' => 'site_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'slug' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'uid' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 32, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'is_admin' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'is_active' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
}
?>
