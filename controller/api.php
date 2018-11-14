<?php

class API
{
	static function fetchBrands()
	{
		global $dbh,$data;
		$sql = "SELECT `name`,`logo_image_500x500`,`cover_image`,`description_markdown`,`website`,`link_permalink` FROM brand ORDER BY `name`";
		$data['brands'] = array();
		foreach ($dbh->query($sql) as $row)
		{
			$data['brands'][] = array(
				'name' => $row['name'],
				'logo' => $row['logo_image_500x500'],
				'cover' => $row['cover_image'],
				'description' => $row['description_markdown'],
				'website' => $row['website'],
				'link' => $row['link_permalink']
			);
		}
	}

	static function fetchCategories()
	{
		global $dbh,$data;
		$sql = "SELECT `id`,`name`,`icon_image_500x500`,`link_permalink` FROM category ORDER BY `name`";
		$data['categories'] = array();
		foreach ($dbh->query($sql) as $row)
		{
			$data['categories'][] = array(
				'name' => $row['name'],
				'icon' => $row['icon_image_500x500'],
				'link' => $row['link_permalink'],
				'subcategories' => Process::getMultiple('subcategory',$row['id'],'category_id')
			);
		}
	}
}
