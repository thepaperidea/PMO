<?php

class Project
{
	static function index()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('page','project','link_permalink');
		$sql = "SELECT `id`,`name`,`timeline_datetime`,`tag_ids`,`slug_permalink`,`featured_bool` FROM project ORDER BY `od` DESC";
		$content['project'] = array();
		foreach ($dbh->query($sql) as $row)
		{
			$content['project'][] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'timeline' => $row['timeline_datetime'],
				'slug' => $row['slug_permalink'],
				'work' => Process::getSingle('work',$row['id'],'project_id','od','desc'),
				'tag' => Process::getMultiple('tag',$row['tag_ids']),
				'time' =>  strtotime($row['timeline_datetime'])
			);
		}
		$sql = 'SELECT * FROM tag';
		$query = $dbh->prepare($sql);
		$query->execute();
		$content['tag'] = $query->fetchAll();
		print $twig->render('project/index.html.twig', array('data' => $data,'content' => $content));
	}

	static function show()
	{
		global $dbh,$data,$twig;
		$content = Process::getSingle('project',$data['path']['argument'][1],'slug_permalink');
		if($content){
			$sql = 'SELECT * FROM work WHERE `project_id` = '.$content['id'];
			$query = $dbh->prepare($sql);
			$query->execute();
			$content['work'] = $query->fetchAll();

			$content['title'] = $content['name'];

			print $twig->render('project/show.html.twig', array('data' => $data,'content' => $content));
		} else {
			$content = Process::getSingle('page','404','link_permalink');
			print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
		}
	}
}
