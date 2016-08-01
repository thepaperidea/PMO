<?php

use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

class Page {
  static function notFound() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','404','link_permalink');
    if (!isset($_GET["view_as"]))
      header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
  }
  static function Home() {
    global $dbh,$data,$twig,$session;
    $content = Process::getSingle('page','','link_permalink');
    print $twig->render('default/home.html.twig', array('data' => $data,'content' => $content));
  }
  public function Blog() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','blog','link_permalink');
    $sql = "SELECT `name`,`description`,`user_id`,`main_image_600x400`,`link_permalink`,`added_datetime` FROM blog ORDER BY `added_datetime`";
    $blog = array();
    foreach ($dbh->query($sql) as $row)
    {
      $blog[] = array(
          'name' => $row['name'],
          'description' => $row['description'],
          'user' => Process::getSingle('user',$row['user_id']),
          'image' => $row['main_image_600x400'],
          'link' => $row['link_permalink'],
          'time' => $row['added_datetime']
          );
    }
    print $twig->render('default/blog.html.twig', array('data' => $data,'content' => $content,'blog' => $blog));
  }
  public function blogEach() {
    global $dbh,$data,$twig,$social;
    $social->shareCount(['twitter', 'facebook', 'plus']);
    $sociallink = array(
      'twitter' => array(
        'name' => 'twitter',
        'count' => $social->twitter->shareCount,
        'link' => $social->twitter->shareUrl
      ),
      'facebook' => array(
        'name' => 'facebook',
        'count' => $social->facebook->shareCount,
        'link' => $social->facebook->shareUrl
      ),
      'plus' => array(
        'name' => 'plus',
        'count' => $social->plus->shareCount,
        'link' => $social->plus->shareUrl
      )
    );
    $content = Process::getSingle('blog',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];

    print $twig->render('default/blog.each.html.twig', array('data' => $data,'content' => $content,'sociallink' => $sociallink));
  }
  public function Stay() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','stay','link_permalink');
    $sql = "SELECT `id`,`name`,`rating_int`,`type_id`,`destination_id`,`link_permalink` FROM stay";
    $stay = array();
    foreach ($dbh->query($sql) as $row)
    {
      $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $row['id']));
      $image = $query->fetch();
      $stay[] = array(
          'name' => $row['name'],
          'rating' => $row['rating_int'],
          'type' => Process::getSingle('type',$row['type_id']),
          'destination' => Process::getSingle('destination',$row['destination_id']),
          'image' => $image,
          'link' => $row['link_permalink']
          );
    }
    print $twig->render('default/stay.html.twig', array('data' => $data,'content' => $content,'stay' => $stay));
  }
  public function stayEach() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('stay',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];

    $sql = "SELECT `id`,`name`,`price_float`,`adults_int`,`children_int` FROM room WHERE `stay_id` = :i ORDER BY `od`";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $rooms = $query->fetchAll();
    $stayroom = array();
    foreach ($rooms as $room) {
      $sql = "SELECT `id`,`main_image` FROM roomimage WHERE `stay_id` = :j AND `room_id` = :i ORDER BY `od`";
      $query = $dbh->prepare($sql);
      $query->execute(array(':j' => $content['id'],':i' => $room['id']));
      $images = $query->fetchAll();
      $stayroom[] = array(
        'id' => $room['id'],
        'name' => $room['name'],
        'description' => $room['description_markdown'],
        'price' => $room['price_float'],
        'adults' => $room['adults_int'],
        'children' => $room['children_int'],
        'images' => $images
      );
    }

    $sql = "SELECT `id`,`name`,`main_image` FROM stayimage WHERE `stay_id` = :i ORDER BY `od`";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $images = $query->fetchAll();


    print $twig->render('default/stay.each.html.twig', array('data' => $data,'content' => $content,'rooms' => $stayroom,'images' => $images));
  }
  public function Tripadvisor(){
    global $dbh,$data,$twig;
    $content = Process::getSingle('stay',$data['path']['argument'][1],'link_permalink');
    print $twig->render('default/tripadvisor.html.twig', array('data' => $data,'content' => $content));
  }
  public function updateBooking(){
      global $session;
      $session->start();
      $segment = $session->getSegment('booking');
      $segment->set('items', $_POST['booking']);
      $session->commit();
  }
  public function viewBooking(){
      global $dbh,$data,$twig,$session;
      $session->start();
      $segment = $session->getSegment('booking');
      $items = $segment->get('items');
      $values = array();
      foreach($items as $item){
        if($item['name']=='stayid')
        $id = $item['value'];
        $values[$item['name']] = $item['value'];
      }
      if($id){
        $days = (strtotime($values['checkout'])-strtotime($values['checkin']))/86400;
        $values['days'] = $days;
        $content = Process::getSingle('stay',$id);
        $content['title'] = $content['name'].' Booking';

        $sql = "SELECT `id`,`name`,`price_float`,`adults_int`,`children_int` FROM room WHERE `stay_id` = :i ORDER BY `od`";
        $query = $dbh->prepare($sql);
        $query->execute(array(':i' => $content['id']));
        $rooms = $query->fetchAll();
        $stayroom = array();
        foreach ($rooms as $room) {
          $sql = "SELECT `id`,`main_image` FROM roomimage WHERE `stay_id` = :j AND `room_id` = :i ORDER BY `od`";
          $query = $dbh->prepare($sql);
          $query->execute(array(':j' => $content['id'],':i' => $room['id']));
          $images = $query->fetchAll();
          $stayroom[] = array(
            'id' => $room['id'],
            'name' => $room['name'],
            'description' => $room['description_markdown'],
            'price' => $room['price_float'],
            'adults' => $room['adults_int'],
            'children' => $room['children_int'],
            'images' => $images
          );
        }

        print $twig->render('default/booking.html.twig', array('data' => $data,'content' => $content,'rooms' => $stayroom,'values' => $values));
      }
  }
  public function stayJSON() {
    header("Content-type: application/json");
    global $dbh,$data,$twig;
    $sql = "SELECT `id`,`name`,`type_id`,`category_ids`,`facility_ids`,`activity_ids`,`activity_ids`,`destination_id`,`link_permalink` FROM `stay`";
    $query = $dbh->prepare($sql);
    $query->execute();
    $stays = $query->fetchAll();
    $json = array();
    foreach ($stays as $stay) {
      $searchterms = array();
      $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $stay['id']));
      $image = $query->fetch();
      $type = Process::getSingle('type',$stay['type_id']);
      $category = Process::getMultiple('category',$stay['category_ids']);
      $facility = Process::getMultiple('facility',$stay['facility_ids']);
      $activity = Process::getMultiple('activity',$stay['activity_ids']);
      $destination = Process::getSingle('destination',$stay['destination_id']);
      $searchterms[] = strtolower($stay['name']);
      $searchterms[] = strtolower($destination['name']);
      foreach ($category as $each)
      $searchterms[] = strtolower($each['name']);
      foreach ($facility as $each)
      $searchterms[] = strtolower($each['name']);
      foreach ($activity as $each)
      $searchterms[] = strtolower($each['name']);
      $json[] = array(
        'name' => $stay['name'],
        'type' => $type,
        'category' => $category,
        'activity' => $activity,
        'destination' => $destination,
        'image' => $image,
        'search' => implode(' ',$searchterms),
        'link' => $stay['link_permalink'],
      );
    }
    print(json_encode($json));
  }
}
