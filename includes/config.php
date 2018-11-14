<?php

$data = array(
	'credential' => array(
		'host' => 'localhost',
		'database' => 'pmo',
		'username' => 'root',
		'password' => 'apple'
	),
	'constant' => array(
		'name' => 'PMO',
		'description' => 'Project Management Office',
		'url' => 'http://localhost/~eegan/pmo/',
		'domain' => 'localhost'
	),
	'address' => array(
		'house' => 'H. Hasowa Building, 5A',
		'road' => 'Boduthakurufaanu Magu',
		'city' => 'Male City',
		'country' => 'Maldives',
	),
	'contact' => array(
		'T' => '+960 3318065',
		'F' => '+960 3318065',
		'E' => 'info@pmo.mv',
	),
	'email' => array(
		'from' => array(
			'name' => 'PMO',
			'address' => 'info@pmo.mv'
		),
		'to' => array(
			'name' => 'PMO',
			'address' => 'info@pmo.mv'
		)
	),
	'social' => array(
		array(
			'name' => 'facebook',
			'link' => 'http://facebook.com/',
			'username' => 'pmomv',
		),
		array(
			'name' => 'instagram',
			'link' => 'http://instagram.com/',
			'username' => 'pmomv',
		),
		array(
			'name' => 'twitter',
			'link' => 'http://twitter.com/',
			'username' => 'pmomv',
		)
	),
	'file' => array(
		'destination' => 'uploads/file/'
	),
	'image' => array(
		'width' => 1960,
		'height' => 1960,
		'thumbnail' => array(
			'size' => 600,
			'prefix' => 'thumb_'
		),
		'small' => array(
			'width' => 800,
			'height' => 800,
			'prefix' => 'small_'
		),
		'destination' => 'uploads/image/',
		'quality' => 100,
	),
	'path' => array(
		'template' => 'view',
		'cache' => 'cache'
	),
	'page' => array(
		array(
			'name' => 'Featured',
			'route' => ''
		),
		array(
			'name' => 'About us',
			'route' => 'about',
			'links' => [
				[
					'name' => 'Team',
					'route' => 'about/team'
				],
				[
					'name' => 'Philosophy',
					'route' => 'about/philosophy'
				],
				[
					'name' => 'Community Initiative',
					'route' => 'about/community-initiative'
				],
				[
					'name' => 'How we collaborate',
					'route' => 'about/how-we-collaborate'
				],
				[
					'name' => 'Internship',
					'route' => 'about/internship'
				]
			]
		),
		array(
			'name' => 'Projects',
			'route' => 'project'
		),
		array(
			'name' => 'Contact us',
			'route' => 'contact'
		)
	),
	'admin' => array(
		'database' => array(
			array(
				'name' => 'Pages',
				'table' => 'page'
			),
			array(
				'name' => 'Tags',
				'table' => 'tag'
			),
			array(
				'name' => 'Projects',
				'table' => 'project'
			),
			array(
				'name' => 'Work',
				'table' => 'work'
			),
			array(
				'name' => 'Pages',
				'table' => 'page'
			),
			array(
				'name' => 'Jobs',
				'table' => 'job'
			)
		)
	),
);
