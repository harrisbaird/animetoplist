<?php
	Router::parseExtensions('rss');
	
	/* Users controller */
	Router::connect('/users/profile', array('controller' => 'users', 'action' => 'profile'));

	/* Series controller */
	Router::connectNamed(array('letter', 'category', 'slug'));
	
	Router::connect('/watch/anime/list/*', array('controller' => 'series', 'action' => 'index', 'anime'));
	Router::connect('/read/manga/list/*', array('controller' => 'series', 'action' => 'index', 'manga'));
	
	Router::connect('/watch/:slug', array('controller' => 'series', 'action' => 'anime'));
	Router::connect('/read/:slug', array('controller' => 'series', 'action' => 'manga'));
	
	Router::connect('/watch/anime/*', array('controller' => 'series', 'action' => 'anime'));
	Router::connect('/read/manga/*', array('controller' => 'series', 'action' => 'manga'));
	
	/* Image crop plugin */
	Router::connect('/crop', array('plugin' => 'image_crop', 'controller' => 'crop', 'action' => 'crop'));
	Router::connect('/crop/process', array('plugin' => 'image_crop', 'controller' => 'crop', 'action' => 'process'));

	Router::connect('/', array('controller' => 'sites', 'action' => 'index'));
