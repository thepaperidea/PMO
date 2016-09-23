<?php

// Error
error_reporting(0);

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
$mailer = new Nette\Mail\SmtpMailer(array(
        'host' => 'smtp.gmail.com',
        'username' => 'ibrahimeegan@gmail.com',
        'password' => 'Pr@Gm@T1c',
        'secure' => 'ssl',
));

//Twig
$loader = new Twig_Loader_Filesystem($data['path']['template']);
$twig = new Twig_Environment($loader);

//Twig filters
$filter = new Twig_SimpleFilter('absolute', function ($url) {
    return Essential::Absolute($url);
});
$twig->addFilter($filter);

$filter = new Twig_SimpleFilter('ago', function ($datetime) {
    $timeAgo = new TimeAgo('Indian/Maldives');
    return $timeAgo->inWords(date('Y/n/d H:i:s',strtotime($datetime)));
});
$twig->addFilter($filter);

//Twig Functions
$function = new Twig_SimpleFunction('datetime', function ($datetime,$format) {
  $set = new DateTime($datetime);
	return $set->format($format);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('hmd', function ($seconds) {
  $dtF = new \DateTime('@0');
  $dtT = new \DateTime("@$seconds");
  return $dtF->diff($dtT)->format('%a days, %h hours and %i minutes');
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

$function = new Twig_SimpleFunction('json_encode', function ($title,$content,$script) {
	return json_encode(array("page" => $title, "content" => $content, "script" => $script));
}, array('is_safe' => array('html')));
$twig->addFunction($function);

if (isset($_GET["view_as"]) && $_GET["view_as"] == "json")
$twig->addGlobal('asJson', true);

$engine = new MarkdownEngine\MichelfMarkdownEngine();
$twig->addExtension(new MarkdownExtension($engine));
