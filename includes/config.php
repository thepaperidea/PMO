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
      'username' => 'travrnr',
    ),
    array(
      'name' => 'facebook',
      'link' => 'http://facebook.com/',
      'username' => 'travrnr',
    ),
    array(
      'name' => 'instagram',
      'link' => 'http://instagram.com/',
      'username' => 'travrnr',
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
      'name' => 'Maldives',
      'route' => 'country/maldives'
    ),
    array(
      'name' => 'Offers',
      'route' => 'offer'
    ),
    array(
      'name' => 'Packages',
      'route' => 'package'
    ),
    array(
      'name' => 'Downloads',
      'route' => 'download'
    )
  ),
  'footer' => array(
    array(
      'name' => 'About',
      'route' => 'about'
    ),
    array(
      'name' => 'Contact',
      'route' => 'contact'
    ),
    array(
      'name' => 'FAQ',
      'route' => 'faq'
    )
  ),
  'admin' => array(
    'database' => array(
      array(
				'name' => 'Search Suggestions',
				'table' => 'suggestion'
			),
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
        'name' => 'Packages',
        'table' => 'package'
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
				'name' => 'Finance',
				'table' => 'finance'
			),
      array(
				'name' => 'Country',
				'table' => 'country'
			),
      array(
				'name' => 'Country Tabs',
				'table' => 'tabbed'
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
			),
      array(
				'name' => 'Transport Types',
				'table' => 'transport'
			),
      array(
				'name' => 'Transport Duration',
				'table' => 'transporttime'
			),
      array(
				'name' => 'Frequently Asked Questions',
				'table' => 'faq'
			),
      array(
				'name' => 'Continents',
				'table' => 'continent'
			),
      array(
				'name' => 'Airline',
				'table' => 'airline'
			),
      array(
				'name' => 'Team',
				'table' => 'team'
			)
    )
  ),
);
