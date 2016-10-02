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
    $sql = "SELECT `id`,`name`,`fancy_image_120x120` FROM activity";
    $query = $dbh->prepare($sql);
    $query->execute();
    $activity = $query->fetchAll();
    $sql = "SELECT `id`,`name`,`fancy_image_120x120` FROM category";
    $query = $dbh->prepare($sql);
    $query->execute();
    $category = $query->fetchAll();
    $sql = "SELECT `id`,`name`,`fancy_image_120x120` FROM finance";
    $query = $dbh->prepare($sql);
    $query->execute();
    $finance = $query->fetchAll();
    print $twig->render('default/home.html.twig', array('data' => $data,'content' => $content,'suggestion' => $suggestion,'category' => $category,'activity' => $activity,'category' => $category,'finance' => $finance));
  }
  public function Blog() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','blog','link_permalink');
    $sql = "SELECT `name`,`description_text`,`user_id`,`header_image`,`featured_bool`,`link_permalink`,`added_datetime_current` FROM blog ORDER BY `id` DESC";
    $blog = array();
    foreach ($dbh->query($sql) as $row)
    {
      $blog[] = array(
          'name' => $row['name'],
          'description' => $row['description_text'],
          'user' => Process::getSingle('user',$row['user_id']),
          'image' => $row['header_image'],
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
    $sql = "SELECT `id`,`name`,`rating_int`,`type_id`,`category_ids`,`activity_ids`,`destination_id`,`link_permalink` FROM stay ORDER BY `name` ASC";
    $stay = array();
    $types = array();
    foreach ($dbh->query($sql) as $row)
    {
      $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $row['id']));
      $image = $query->fetch();
      $sql = "SELECT `price_float` FROM room WHERE `stay_id` = :i ORDER BY `price_float` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $row['id']));
      $price = $query->fetch();
      $type = Process::getSingle('type',$row['type_id']);
      $types[$row['type_id']] = $type;
      $stay[] = array(
          'name' => $row['name'],
          'rating' => $row['rating_int'],
          'type' => $type,
          'destination' => Process::getSingle('destination',$row['destination_id']),
          'category' => Process::getMultiple('category',$row['category_ids']),
          'activity' => Process::getMultiple('activity',$row['activity_ids']),
          'image' => $image,
          'price' => $price,
          'link' => $row['link_permalink']
          );
    }
    print $twig->render('default/stay.html.twig', array('data' => $data,'content' => $content,'stay' => $stay,'type' => $types));
  }
  public function stayEach() {
    global $dbh,$data,$twig,$session;
    $session->start();
    $segment = $session->getSegment('booking');
    $items = $segment->get('items');

    $dates = array();
    foreach ($items as $item) {
      $dates[$item['name']] = $item['value'];
    }

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

    $sql = "SELECT `transport_id`,`duration_time` FROM transporttime WHERE `stay_id` = :i";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $transports = $query->fetchAll();
    $means = array();
    foreach ($transports as $transport) {
      $split = explode(':',$transport['duration_time']);
      $means[] = array(
        'transport' => Process::getSingle('transport',$transport['transport_id']),
        'duration' => $transport['duration_time'],
        'split' => $split,
        'dividant' => 188-(($split[0]+($split[1]/60)+($split[2]/3600))/12*188)
      );
    }

    $content['transport'] = $means;

    $sql = "SELECT `id`,`name`,`main_image` FROM stayimage WHERE `stay_id` = :i ORDER BY `od`";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $images = $query->fetchAll();

    print $twig->render('default/stay.each.html.twig', array('data' => $data,'content' => $content,'rooms' => $stayroom,'images' => $images,'dates' => $dates));
  }
  public function Tripadvisor(){
    global $dbh,$data,$twig;
    $content = Process::getSingle('stay',$data['path']['argument'][1],'link_permalink');
    print $twig->render('default/tripadvisor.html.twig', array('data' => $data,'content' => $content));
  }
  public function update(){
      global $session;
      $session->start();
      if($_POST['booking']){
        $segment = $session->getSegment('booking');
        $segment->set('items', $_POST['booking']);
      }
      elseif($_POST['preferences']){
        $segment = $session->getSegment('preferences');
        $segment->set('settings', $_POST['preferences']);
      }
      $session->commit();
  }
  public function viewBooking(){
      global $dbh,$data,$twig,$session;
      $session->start();
      $segment = $session->getSegment('booking');
      $items = $segment->get('items');
      print_r($items);
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
  public function Package() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','package','link_permalink');
    $sql = "SELECT `id`,`name`,`description`,`main_image`,`activity_ids`,`price_float`,`link_permalink` FROM package";
    $package = array();
    foreach ($dbh->query($sql) as $row)
    {
      $package[] = array(
          'name' => $row['name'],
          'description' => $row['description'],
          'main_image' => $row['main_image'],
          'price' => $row['price_float'],
          'link' => $row['link_permalink']
          );
    }
    print $twig->render('default/package.html.twig', array('data' => $data,'content' => $content,'package' => $package));
  }
  public function packageEach() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('package',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];

    print $twig->render('default/package.each.html.twig', array('data' => $data,'content' => $content));
  }
  public function packageBook() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('package',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];

    print $twig->render('default/package.book.html.twig', array('data' => $data,'content' => $content));
  }
  public function offerBook() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('offer',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];

    print $twig->render('default/offer.book.html.twig', array('data' => $data,'content' => $content));
  }
  public function stayBook() {
    global $dbh,$data,$twig,$session;
    $session->start();
    $segment = $session->getSegment('booking');
    $items = $segment->get('items');

    $dates = array();
    foreach ($items as $item) {
      $dates[$item['name']] = $item['value'];
    }

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

    $content['room'] = $stayroom;
    $content['dates'] = $dates;

    print $twig->render('default/stay.book.html.twig', array('data' => $data,'content' => $content));
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
      $category[$row['category_id']] = $categoryrow;
    }

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
  public function FAQ() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','faq','link_permalink');
    $sql = "SELECT `id`,`question_text`,`answer_markdown` FROM faq ORDER BY `od`";
    $faq = array();
    foreach ($dbh->query($sql) as $row)
    {
      $faq[] = array(
          'question' => $row['question_text'],
          'answer' => $row['answer_markdown'],
          );
    }
    print $twig->render('default/faq.html.twig', array('data' => $data,'content' => $content,'faq' => $faq));
  }
  static function About() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','about','link_permalink');
    $sql = "SELECT `name`,`designation`,`profile_image_300x300`,`description_text` FROM team";
    $team = array();
    foreach ($dbh->query($sql) as $row)
    {
      $team[] = array(
          'name' => $row['name'],
          'designation' => $row['designation'],
          'profile' => $row['profile_image_300x300'],
          'description' => $row['description_text'],
          );
    }
    print $twig->render('default/about.html.twig', array('data' => $data,'content' => $content,'team' => $team));
  }
  static function Contact() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','contact','link_permalink');
    print $twig->render('default/contact.html.twig', array('data' => $data,'content' => $content));
  }
  public function Send() {
      global $twig,$data,$mail,$mailer;
      if(isset($_POST['formdata'])){
          $to = $data['email']['to']['address'];
          $from = $data['email']['from']['address'];
          $subject = 'From website';
          $message = '';
          foreach ($_POST['formdata'] as $row) {
              $key = ucwords($row['name']);
              $value = $row['value'];
              if($row['name']=='from'||$row['name']=='to'){
                $d=new DateTime($value);
                $value = $d->format('l jS F Y');
              }
              if(is_array($value))
              $value = implode(',',$value);
              $message .= "$key: $value<br>";
          }
          $mail->setFrom($from)
              ->addTo($to)
              ->setSubject($subject)
              ->setHTMLBody($message);
          $mailer->send($mail);
          return true;
      }
  }
  public function Country() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('country',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];
    $sql = "SELECT `id`,`name`,`content_markdown` FROM tabbed WHERE `country_id` = :i";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $tabs = $query->fetchAll();
    $sql = "SELECT `id`,`name`,`link_permalink`,`highlight_bool`,`description_markdown` FROM destination";
    $destination = array();
    foreach ($dbh->query($sql) as $row)
    {
      $destination[] = array(
          'id' => $row['id'],
          'name' => $row['name'],
          'highlight' => $row['highlight_bool'],
          'description' => $row['description_markdown'],
          'link' => $row['link_permalink']
          );
    }
    $sql = "SELECT `id`,`name`,`rating_int`,`type_id`,`destination_id`,`link_permalink` FROM stay ORDER BY `name` ASC";
    $stay = array();
    foreach ($dbh->query($sql) as $row)
    {
      $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $row['id']));
      $image = $query->fetch();
      $stay[] = array(
          'id' => $row['id'],
          'name' => $row['name'],
          'rating' => $row['rating_int'],
          'type' => Process::getSingle('type',$row['type_id']),
          'destination' => Process::getSingle('destination',$row['destination_id']),
          'image' => $image,
          'link' => $row['link_permalink']
          );
    }
    $sql = "SELECT `id`,`name`,`link_permalink` FROM continent";
    $continent = array();
    foreach ($dbh->query($sql) as $row)
    {
      $continent[] = array(
          'id' => $row['id'],
          'name' => $row['name'],
          'link' => $row['link_permalink']
          );
    }
    $sql = "SELECT `name`,`continent_ids`,`website`,`logo_svg` FROM airline";
    $airline = array();
    foreach ($dbh->query($sql) as $row)
    {
      $airline[] = array(
          'name' => $row['name'],
          'continent' => explode(',',$row['continent_ids']),
          'website' => $row['website'],
          'logo' => $row['logo_svg']
          );
    }
    print $twig->render('default/country.each.html.twig', array('data' => $data,'content' => $content,'destination' => $destination,'continent' => $continent,'airline' => $airline,'stay' => $stay,'tabs' => $tabs));
  }
  public function plan() {
    global $dbh,$data,$twig,$session;
    $content = Process::getSingle('page','plan','link_permalink');
    $session->start();
    $segment = $session->getSegment('preferences');
    $preferences = $segment->get('settings');
    $sql = "SELECT `id`,`price_float` FROM `finance` ORDER BY `price_float` DESC";
    $query = $dbh->prepare($sql);
    $query->execute();
    $finance = $query->fetchAll();
    $sql = "SELECT `id`,`name`,`rating_int`,`type_id`,`category_ids`,`activity_ids`,`destination_id`,`link_permalink` FROM stay";
    $stay = array();
    $maxrelevance = 0;
    foreach ($dbh->query($sql) as $row){
      $relevance = 0;
      $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $row['id']));
      $image = $query->fetch();
      $sql = "SELECT `price_float` FROM room WHERE `stay_id` = :i ORDER BY `price_float` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $row['id']));
      $price = $query->fetch();
      foreach ($finance as $money) {
        if($price['price_float']>$money['price_float']){
          $row['finance_ids'] = $money['id'];
          break;
        }
      }
      foreach ($preferences as $each) {
        $arrayfromdb = explode(',',$row[$each['name'].'_ids']);
        foreach ($each['value'] as $spec){
          if(in_array($spec,$arrayfromdb)){
            $relevance++;
          }
        }
      }
      if($relevance>0){
        $stay[] = array(
            'name' => $row['name'],
            'rating' => $row['rating_int'],
            'type' => Process::getSingle('type',$row['type_id']),
            'destination' => Process::getSingle('destination',$row['destination_id']),
            'category' => Process::getMultiple('category',$row['category_ids']),
            'activity' => Process::getMultiple('activity',$row['activity_ids']),
            'image' => $image,
            'price' => $price,
            'relevance' => $relevance,
            'link' => $row['link_permalink']
            );
      }
      if($relevance>$maxrelevance)
      $maxrelevance = $relevance;
    }
    $content['relevance'] = $maxrelevance;
    usort($stay, function($a, $b) {
        return $b['relevance'] - $a['relevance'];
    });
    print $twig->render('default/plan.html.twig', array('data' => $data,'content' => $content,'stay' => $stay));
  }
  public function booking() {
    global $dbh,$data,$twig,$session;
    $content = Process::getSingle('page','plan','link_permalink');
    $session->start();
    $segment = $session->getSegment('preferences');
    $preferences = $segment->get('settings');

    $array = Array();

    $sql = "SELECT `id`,`name` FROM activity";
    $query = $dbh->prepare($sql);
    $query->execute();
    $array['activity'] = $query->fetchAll();

    $sql = "SELECT `id`,`name` FROM category";
    $query = $dbh->prepare($sql);
    $query->execute();
    $array['category'] = $query->fetchAll();

    $sql = "SELECT `id`,`name` FROM finance";
    $query = $dbh->prepare($sql);
    $query->execute();
    $array['finance'] = $query->fetchAll();

    $list = Array();

    foreach ($preferences as $row) {
      $values = $row['value'];
      foreach ($array[$row['name']] as $each) {
        if(in_array($each['id'],$values))
        $list[$row['name']][] = $each['name'];
      }
    }

    print $twig->render('default/plan.book.html.twig', array('data' => $data,'content' => $content,'list' => $list));
  }
}
