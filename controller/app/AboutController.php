<?php

class About
{
	static function index() {
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','about','link_permalink');
		print $twig->render('about/index.html.twig', array('data' => $data,'content' => $content));
	}

	static function team()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','about/team','link_permalink');
		print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
	}

	static function philosophy()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','about/philosophy','link_permalink');
		print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
	}

	static function community()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','about/community','link_permalink');
		print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
	}

	static function collaborate()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','about/collaborate','link_permalink');
		print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
	}

	static function internship()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','about/internship','link_permalink');
		print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
	}
}
