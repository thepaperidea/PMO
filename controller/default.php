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
    $sql = "SELECT `name` FROM suggestion";
    $query = $dbh->prepare($sql);
    $query->execute();
    $suggestion = $query->fetchAll();
    print $twig->render('default/home.html.twig', array('data' => $data,'content' => $content,'suggestion' => $suggestion));
  }
  public function Blog() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','blog','link_permalink');
    $sql = "SELECT `name`,`description`,`user_id`,`main_image_600x400`,`featured_bool`,`link_permalink`,`added_datetime_current` FROM blog ORDER BY `id` DESC";
    $blog = array();
    foreach ($dbh->query($sql) as $row)
    {
      $blog[] = array(
          'name' => $row['name'],
          'description' => $row['description'],
          'user' => Process::getSingle('user',$row['user_id']),
          'image' => $row['main_image_600x400'],
          'featured' => $row['featured_bool'],
          'link' => $row['link_permalink'],
          'time' => $row['added_datetime_current']
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
    $sql = "SELECT `id`,`name`,`rating_int`,`type_id`,`destination_id`,`link_permalink` FROM stay ORDER BY `name` ASC";
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
    $sql = "SELECT `searchterms`,`price_float` FROM `finance` ORDER BY `price_float` DESC";
    $query = $dbh->prepare($sql);
    $query->execute();
    $finance = $query->fetchAll();
    $json = array();
    foreach ($stays as $stay) {
      $searchterms = array();
      $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $stay['id']));
      $image = $query->fetch();
      $sql = "SELECT `price_float` FROM room WHERE `stay_id` = :i ORDER BY `price_float` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $stay['id']));
      $price = $query->fetch();
      $type = Process::getSingle('type',$stay['type_id']);
      $category = Process::getMultiple('category',$stay['category_ids']);
      $facility = Process::getMultiple('facility',$stay['facility_ids']);
      $activity = Process::getMultiple('activity',$stay['activity_ids']);
      $destination = Process::getSingle('destination',$stay['destination_id']);
      $searchterms[] = strtolower($stay['name']);
      $searchterms[] = strtolower($type['name']);
      $searchterms[] = strtolower($destination['name']);
      foreach ($category as $each)
      $searchterms[] = strtolower($each['name']);
      foreach ($facility as $each)
      $searchterms[] = strtolower($each['name']);
      foreach ($activity as $each)
      $searchterms[] = strtolower($each['name']);
      foreach ($finance as $money) {
        if($price['price_float']>$money['price_float']){
          $searchterms[] = $money['searchterms'];
        }
      }
      $json[] = array(
        'name' => $stay['name'],
        'type' => $type,
        'category' => $category,
        'activity' => $activity,
        'destination' => $destination,
        'image' => $image,
        'price' => $price['price_float'],
        'search' => implode(' ',$searchterms),
        'link' => $stay['link_permalink'],
      );
    }
    print(json_encode($json));
  }
  public function Offer() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','offer','link_permalink');
    $sql = "SELECT `id`,`name`,`description`,`main_image`,`price_float`,`stay_id`,`from_datetime`,`to_datetime`,`link_permalink` FROM offer";
    $offer = array();
    foreach ($dbh->query($sql) as $row)
    {
      $offer[] = array(
          'name' => $row['name'],
          'description' => $row['description'],
          'main_image' => $row['main_image'],
          'price' => $row['price_float'],
          'stay' => Process::getSingle('stay',$row['stay_id']),
          'from' => strtotime($row['from_datetime']),
          'to' => strtotime($row['to_datetime']),
          'link' => $row['link_permalink']
          );
    }
    $current = strtotime(date("Y-m-d H:i:s"));
    print $twig->render('default/offer.html.twig', array('data' => $data,'content' => $content,'offer' => $offer,'current' => $current));
  }
  public function offerEach() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('offer',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];

    print $twig->render('default/offer.each.html.twig', array('data' => $data,'content' => $content));
  }
  public function Download() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','download','link_permalink');
    $sql = "SELECT `id`,`name`,`description`,`format_id`,`category_id`,`preview_image`,`count_int` FROM download";
    $download = array();
    $category = array();
    foreach ($dbh->query($sql) as $row)
    {
      $categoryrow = Process::getSingle('category',$row['category_id']);
      $download[] = array(
          'id' => $row['id'],
          'name' => $row['name'],
          'description' => $row['description'],
          'format' => Process::getSingle('format',$row['format_id']),
          'category' => $categoryrow,
          'preview' => $row['preview_image'],
          'count' => $row['count_int']
          );
      $category[$row['category_id']] = $categoryrow['name'];
    }

    $category = array_unique($category);

    print $twig->render('default/download.html.twig', array('data' => $data,'content' => $content,'download' => $download,'category' => $category));
  }
  public function downloadEach() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('download',$data['path']['argument'][1]);
    $json['download'] = $content['download'];

    $sql = "UPDATE download SET `count_int`=`count_int`+1 WHERE id = ?";
    $q = $dbh->prepare($sql);
    $q->execute(array($content['id']));

    print(json_encode($json));
  }
}
