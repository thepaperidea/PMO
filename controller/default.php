<?php

class Page
{
	static function notFound()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','404','link_permalink');
		print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
	}
}
