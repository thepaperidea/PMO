<?php

$data = array(
  'credential' => array(
		'host' => 'localhost',
		'database' => 'travrnr',
		'username' => 'root',
		'password' => 'apple'
	),
  'constant' => array(
    'name' => 'Travrnr',
    'description' => 'The travel people',
    'url' => 'http://localhost/~eegan/travrnr/',
		'domain' => 'localhost',
		'fbadmin' => '523187088',
		'captcha' => '6LcE8B8TAAAAAB_H_wJ99zviL1l0S6qdGVzMA9RT',
		'login' => 'a36f292da91457067c828ed764214618',
  ),
  'email' => array(
    'from' => array(
      'name' => '',
      'address' => 'eegan@live.com'
    ),
    'to' => array(
      'name' => '',
      'address' => 'eegan@live.com'
    )
  ),
  'file' => array(
	   'destination' => 'uploads/file/'
	),
	'image' => array(
		'width' => 1960,
		'height' => 1960,
		'thumbnail' => array(
			'size' => 400,
			'prefix' => 'thumb_'
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
      'name' => 'Blog',
      'route' => 'blog'
    ),
    array(
      'name' => 'Stay',
      'route' => 'stay'
    ),
    array(
      'name' => 'Offers',
      'route' => 'offer'
    ),
    array(
      'name' => 'Downloads',
      'route' => 'download'
    )
  ),
  'footer' => array(

  ),
  'admin' => array(
    'database' => array(
      array(
				'name' => 'Pages',
				'table' => 'page'
			),
      array(
				'name' => 'Blog',
				'table' => 'blog'
			),
      array(
				'name' => 'Offers',
				'table' => 'offer'
			),
      array(
				'name' => 'Stay',
				'table' => 'stay'
			),
      array(
				'name' => 'Stay Image',
				'table' => 'stayimage'
			),
      array(
				'name' => 'Category',
				'table' => 'category'
			),
      array(
				'name' => 'Activity',
				'table' => 'activity'
			),
      array(
				'name' => 'Facilities',
				'table' => 'facility'
			),
      array(
				'name' => 'Destination',
				'table' => 'destination'
			),
      array(
				'name' => 'Type',
				'table' => 'type'
			),
      array(
				'name' => 'Downloads',
				'table' => 'download'
			),
      array(
				'name' => 'Formats',
				'table' => 'format'
			)
    )
  ),
);
