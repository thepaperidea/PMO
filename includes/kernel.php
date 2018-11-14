<?php

// Error
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

//DateTime
date_default_timezone_set('Indian/Maldives');

// Twig Markdown
use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;

//Social Media
use SocialLinks\Page;
$social = new Page([
    'url' => $data['constant']['url'].$_GET['path']
]);

//Session
$session = (new \Aura\Session\SessionFactory)->newInstance($_COOKIE);
$session->setCookieParams(array('lifetime' => 300000000));

// Facebook
$fb = new Facebook\Facebook([
  'app_id' => '710284505741800',
  'app_secret' => '9bd538ca528bd9cf3fa3e170b6808816',
  'default_graph_version' => 'v2.5',
]);

//Mail
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

$mail = new Message;
$mailer = new SendmailMailer;

//Twig
$loader = new Twig_Loader_Filesystem($data['path']['template']);
$twig = new Twig_Environment($loader);

//Twig filters
$filter = new Twig_SimpleFilter('absolute', function ($url) {
    return Essential::Absolute($url);
});
$twig->addFilter($filter);

$filter = new Twig_SimpleFilter('rescape', function ($variable) {
    return str_replace(' ', '-', strtolower($variable));
});
$twig->addFilter($filter);

$filter = new Twig_SimpleFilter('ago', function ($datetime) {
    $timeAgo = new TimeAgo('Indian/Maldives');
    return $timeAgo->inWords(date('Y/n/d H:i:s',$datetime));
});
$twig->addFilter($filter);

//Twig Functions
$function = new Twig_SimpleFunction('datetime', function ($datetime,$format) {
  $set = new DateTime($datetime);
	return $set->format($format);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('asset', function ($url) {
	return Filter::Asset($url);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('input', function ($data,$column) {
	return Filter::Input($data,$column);
}, array('is_safe' => array('html')));
$twig->addFunction($function);

$function = new Twig_SimpleFunction('output', function ($data,$column) {
	return Filter::Output($data,$column);
}, array('is_safe' => array('html')));
$twig->addFunction($function);

$engine = new MarkdownEngine\MichelfMarkdownEngine();
$twig->addExtension(new MarkdownExtension($engine));
