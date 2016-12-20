<?php

$data = array(
  'credential' => array(
		'host' => 'localhost',
		'database' => 'funnel',
		'username' => 'root',
		'password' => 'apple'
	),
  'constant' => array(
    'name' => 'Travrnr',
    'description' => 'The travel people',
    'url' => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/~eegan/funnel/',
		'domain' => $_SERVER['HTTP_HOST'],
		'fbadmin' => '523187088',
		'captcha' => '6LcE8B8TAAAAAB_H_wJ99zviL1l0S6qdGVzMA9RT',
		'login' => 'a36f292da91457067c828ed764214618',
		'airport' => array(
      'latitude' => 4.186678,
      'longitude' => 73.528177
    ),
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
  'social' => array(
    array(
      'name' => 'twitter',
      'link' => 'http://twitter.com/',
      'username' => 'funnel',
    ),
    array(
      'name' => 'facebook',
      'link' => 'http://facebook.com/',
      'username' => 'funnel',
    ),
    array(
      'name' => 'instagram',
      'link' => 'http://instagram.com/',
      'username' => 'funnel',
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
      'name' => 'About Us',
      'route' => 'about'
    ),
    array(
      'name' => 'Maldives',
      'route' => 'country/maldives'
    ),
    array(
      'name' => 'Destinations',
      'route' => 'country'
    ),
    array(
      'name' => 'Holiday Types',
      'route' => 'holiday'
    ),
    array(
      'name' => 'Special Offers',
      'route' => 'package'
    ),
    array(
      'name' => 'Blog',
      'route' => 'blog'
    )
  ),
  'footer' => array(
    array(
      'name' => 'Services',
      'route' => 'services'
    ),
    array(
      'name' => 'Privacy Policy',
      'route' => 'privacy-policy'
    ),
    array(
      'name' => 'FAQ',
      'route' => 'faq'
    ),
    array(
      'name' => 'Contact',
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
				'name' => 'Slides',
				'table' => 'slides'
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
				'name' => 'Finance',
				'table' => 'finance'
			),
      array(
				'name' => 'Country',
				'table' => 'country'
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
				'name' => 'Room',
				'table' => 'room'
			),
      array(
				'name' => 'Room Image',
				'table' => 'roomimage'
			),
      array(
				'name' => 'Transport',
				'table' => 'transport'
			),
      array(
				'name' => 'Transport Time',
				'table' => 'transporttime'
			),
      array(
				'name' => 'Frequently Asked Questions',
				'table' => 'faq'
			)
    )
  ),
);
