<?php

$data = array(
  'credential' => array(
		'host' => 'localhost',
		'database' => '',
		'username' => 'root',
		'password' => 'apple'
	),
  'constant' => array(
    'name' => '',
    'description' => '',
    'url' => 'http://localhost/~eegan/',
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
			'size' => 600,
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

  ),
  'footer' => array(

  ),
  'admin' => array(
    'database' => array(
      array(
				'name' => 'Pages',
				'table' => 'page'
			)
    )
  ),
);
