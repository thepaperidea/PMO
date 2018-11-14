<?php

class Home
{
	static function index()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','','link_permalink');
		$sql = "SELECT `id`,`name`,`timeline_datetime`,`slug_permalink`,`featured_bool` FROM project WHERE `featured_bool` = 1 ORDER BY `od` DESC";
		$content['project'] = array();
		foreach ($dbh->query($sql) as $row)
		{
			$content['project'][] = array(
				'name' => $row['name'],
				'timeline' => $row['timeline_datetime'],
				'slug' => $row['slug_permalink'],
				'work' => Process::getSingle('work',$row['id'],'project_id'),
			);
		}
		print $twig->render('default/index.html.twig', array('data' => $data,'content' => $content));
	}

	static function service()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','service','link_permalink');
		print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
	}

	static function contact()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','contact','link_permalink');

		$sql = 'SELECT * FROM job';
		$query = $dbh->prepare($sql);
		$query->execute();
		$content['job'] = $query->fetchAll();

		print $twig->render('default/contact.html.twig', array('data' => $data,'content' => $content));
	}
}
