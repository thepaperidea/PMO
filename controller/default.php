<?php

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
    $sql = "SELECT * FROM slides";
    $query = $dbh->prepare($sql);
    $query->execute();
    $content['slides'] = $query->fetchAll();
    $sql = "SELECT * FROM category";
    $query = $dbh->prepare($sql);
    $query->execute();
    $content['category'] = $query->fetchAll();
    print $twig->render('default/home.html.twig', array('data' => $data,'content' => $content));
  }
  static function eachCategory() {
    global $dbh,$data,$twig,$session;
    $content = Process::getSingle('category',$data['path']['argument'][1],'link_permalink');
    $sql = "SELECT * FROM stay WHERE `category_id` = :i";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $stay = $query->fetchAll();
    $sql = "SELECT * FROM type";
    $query = $dbh->prepare($sql);
    $query->execute();
    $content['type'] = $query->fetchAll();
    foreach ($stay as $row){
      $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $row['id']));
      $image = $query->fetch();
      $content['stay'][] = array(
        'name' => $row['name'],
        'rating' => $row['rating_int'],
        'image' => $image,
        'activity' => Process::getMultiple('activity',$row['activity_ids']),
        'type' => Process::getSingle('type',$row['type_id']),
        'holiday' => Process::getMultiple('holiday',$row['holiday_ids']),
        'destination' => Process::getSingle('destination',$row['destination_id']),
        'link' => $row['link_permalink'],
      );
    }
    $content['title'] = $content['name'];
    print $twig->render('default/category.each.html.twig', array('data' => $data,'content' => $content));
  }
  public function Blog() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','blog','link_permalink');
    $sql = "SELECT `name`,`description_text`,`user_id`,`header_image`,`featured_bool`,`link_permalink`,`added_datetime_current` FROM blog ORDER BY `id` DESC";
    $content['blog'] = array();
    foreach ($dbh->query($sql) as $row)
    {
      $content['blog'][] = array(
          'name' => $row['name'],
          'description' => $row['description_text'],
          'user' => Process::getSingle('user',$row['user_id']),
          'image' => $row['header_image'],
          'featured' => $row['featured_bool'],
          'link' => $row['link_permalink'],
          'time' => $row['added_datetime_current']
          );
    }
    print $twig->render('default/blog.html.twig', array('data' => $data,'content' => $content));
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

    $content['facebook'] = $content['social'];
    $content['twitter'] = $content['social'];
    $content['google'] = $content['social'];

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
    $content = Process::getSingle('stay',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];

    $sql = "SELECT * FROM room WHERE `stay_id` = :i ORDER BY `od`";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $rooms = $query->fetchAll();
    $content['room'] = array();
    foreach ($rooms as $room) {
      $sql = "SELECT `id`,`main_image` FROM roomimage WHERE `stay_id` = :j AND `room_id` = :i ORDER BY `od`";
      $query = $dbh->prepare($sql);
      $query->execute(array(':j' => $content['id'],':i' => $room['id']));
      $images = $query->fetchAll();
      $content['room'][] = array(
        'id' => $room['id'],
        'name' => $room['name'],
        'tagline' => $room['tagline'],
        'description' => $room['description_markdown'],
        'price' => $room['price_float'],
        'adults' => $room['adults_int'],
        'children' => $room['children_int'],
        'images' => $images,
        'amenity' => Process::getMultiple('amenity',$room['amenity_ids'])
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
    $content['stayimage'] = $query->fetchAll();

    print $twig->render('default/stay.each.html.twig', array('data' => $data,'content' => $content));
  }
  public function Tripadvisor(){
    global $dbh,$data,$twig;
    $content = Process::getSingle('stay',$data['path']['argument'][1],'link_permalink');
    print $twig->render('default/tripadvisor.html.twig', array('data' => $data,'content' => $content));
  }
  public function Package() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','package','link_permalink');
    $sql = "SELECT `id`,`name`,`description`,`main_image`,`activity_ids`,`price_float`,`link_permalink` FROM package";
    $content['package'] = array();
    foreach ($dbh->query($sql) as $row)
    {
      $content['package'][] = array(
          'name' => $row['name'],
          'description' => $row['description'],
          'main_image' => $row['main_image'],
          'price' => $row['price_float'],
          'link' => $row['link_permalink'],
          'activity' => Process::getMultiple('activity',$row['activity_ids'])
          );
    }
    print $twig->render('default/package.html.twig', array('data' => $data,'content' => $content,'package' => $package));
  }
  public function packageEach() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('package',$data['path']['argument'][1],'link_permalink');
    if(!$content)
    Essential::Redirect('404');
    $content['title'] = $content['name'];

    print $twig->render('default/package.each.html.twig', array('data' => $data,'content' => $content));
  }
  public function packageBook() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('package',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];

    print $twig->render('default/package.book.html.twig', array('data' => $data,'content' => $content));
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

    $sql = "SELECT `id`,`name`,`main_image` FROM stayimage WHERE `stay_id` = :i ORDER BY `od`";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $content['stayimage'] = $query->fetchAll();

    $content['room'] = $stayroom;
    $content['dates'] = $dates;

    print $twig->render('default/stay.book.html.twig', array('data' => $data,'content' => $content));
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
    print $twig->render('default/about.html.twig', array('data' => $data,'content' => $content,'team' => $team));
  }
  static function RTP() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','rtp','link_permalink');
    print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
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
    $content = Process::getSingle('page','country','link_permalink');
    $sql = "SELECT `id`,`name`,`description_text`,`main_image`,`link_permalink`,`disabled_bool` FROM country";
    $content['country'] = array();
    foreach ($dbh->query($sql) as $row)
    {
      $content['country'][] = array(
          'name' => $row['name'],
          'description' => $row['description_text'],
          'main_image' => $row['main_image'],
          'link' => $row['link_permalink'],
          'disabled' => $row['disabled_bool']
          );
    }

    print $twig->render('default/country.html.twig', array('data' => $data,'content' => $content));
  }
  public function countryEach() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('country',$data['path']['argument'][1],'link_permalink');
    $content['title'] = $content['name'];
    $sql = "SELECT `id`,`name`,`main_image` FROM countryimage WHERE `country_id` = :i ORDER BY `od`";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $content['countryimage'] = $query->fetchAll();

    $sql = "SELECT `id`,`name`,`link_permalink`,`main_image` FROM destination";
    $content['destination'] = array();
    foreach ($dbh->query($sql) as $row)
    {
      $id = $row['id'];
      $sql = "SELECT * FROM stay WHERE `destination_id` = :i";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $id));
      $stay = $query->fetchAll();
      $sql = "SELECT * FROM type";
      $query = $dbh->prepare($sql);
      $query->execute();
      $content['type'] = $query->fetchAll();
      foreach ($stay as $row){
        $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
        $query = $dbh->prepare($sql);
        $query->execute(array(':i' => $row['id']));
        $image = $query->fetch();
        $content['stay'][] = array(
          'name' => $row['name'],
          'rating' => $row['rating_int'],
          'image' => $image,
          'activity' => Process::getMultiple('activity',$row['activity_ids']),
          'type' => Process::getSingle('type',$row['type_id']),
          'holiday' => Process::getMultiple('holiday',$row['holiday_ids']),
          'destination' => Process::getSingle('destination',$row['destination_id']),
          'link' => $row['link_permalink'],
        );
      }
    }

    $sql = "SELECT `id`,`name`,`link_permalink` FROM map";
    $content['map'] = array();
    foreach ($dbh->query($sql) as $row)
    {
      $content['map'][] = array(
          'id' => $row['id'],
          'name' => $row['name'],
          'link' => $row['link_permalink']
          );
    }

    $sql = "SELECT `id`,`name`,`continent_id` FROM maplist";
    $content['maplist'] = array();
    foreach ($dbh->query($sql) as $row)
    {
      $content['maplist'][] = array(
          'id' => $row['id'],
          'name' => $row['name'],
          'continent' => $row['continent_id'],
          );
    }

    $sql = "SELECT * FROM schedule";
    $content['schedule'] = array();
    foreach ($dbh->query($sql) as $row)
    {
      $content['schedule'][] = array(
          'name' => $row['name'],
          'maplist' => $row['maplist_id'],
          'depart' => $row['depart'],
          'arrive' => $row['arrive'],
          'duration' => $row['duration'],
          'flight' => $row['flight'],
          'layover' => $row['layover'],
          'inbound' => $row['inbound']
          );
    }
    print $twig->render('default/country.each.html.twig', array('data' => $data,'content' => $content));
  }
  public function destinationEach() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('destination',$data['path']['argument'][1],'link_permalink');
    if(!$content)
    Essential::Redirect('404');
    $content['title'] = $content['name'];

    $sql = "SELECT * FROM stay WHERE `destination_id` = :i";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $stay = $query->fetchAll();
    $sql = "SELECT * FROM type";
    $query = $dbh->prepare($sql);
    $query->execute();
    $content['type'] = $query->fetchAll();
    foreach ($stay as $row){
      $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
      $query = $dbh->prepare($sql);
      $query->execute(array(':i' => $row['id']));
      $image = $query->fetch();
      $content['stay'][] = array(
        'name' => $row['name'],
        'rating' => $row['rating_int'],
        'image' => $image,
        'activity' => Process::getMultiple('activity',$row['activity_ids']),
        'type' => Process::getSingle('type',$row['type_id']),
        'holiday' => Process::getMultiple('holiday',$row['holiday_ids']),
        'link' => $row['link_permalink'],
      );
    }

    print $twig->render('default/destination.each.html.twig', array('data' => $data,'content' => $content));
  }
  public function Holiday() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','holiday','link_permalink');
    $sql = "SELECT `id`,`name`,`description_text`,`main_image`,`link_permalink` FROM holiday";
    $content['holiday'] = array();
    foreach ($dbh->query($sql) as $row)
    {
      $content['holiday'][] = array(
          'name' => $row['name'],
          'description' => $row['description_text'],
          'main_image' => $row['main_image'],
          'link' => $row['link_permalink'],
          );
    }
    print $twig->render('default/holiday.html.twig', array('data' => $data,'content' => $content));
  }
  public function holidayEach() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('holiday',$data['path']['argument'][1],'link_permalink');
    if(!$content)
    Essential::Redirect('404');
    $content['title'] = $content['name'];
    $sql = "SELECT * FROM stay";
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $content['id']));
    $stay = $query->fetchAll();
    $sql = "SELECT * FROM type";
    $query = $dbh->prepare($sql);
    $query->execute();
    $content['type'] = $query->fetchAll();
    foreach ($stay as $row){
      $holidaytypes = explode(',',$row['holiday_ids']);
      if(in_array($content['id'],$holidaytypes)){
        $sql = "SELECT `id`,`main_image`,`od` FROM stayimage WHERE `stay_id` = :i ORDER BY `od` LIMIT 0,1";
        $query = $dbh->prepare($sql);
        $query->execute(array(':i' => $row['id']));
        $image = $query->fetch();
        $content['stay'][] = array(
          'name' => $row['name'],
          'rating' => $row['rating_int'],
          'image' => $image,
          'activity' => Process::getMultiple('activity',$row['activity_ids']),
          'type' => Process::getSingle('type',$row['type_id']),
          'holiday' => Process::getMultiple('holiday',$row['holiday_ids']),
          'destination' => Process::getSingle('destination',$row['destination_id']),
          'link' => $row['link_permalink'],
        );
      }
    }

    print $twig->render('default/holiday.each.html.twig', array('data' => $data,'content' => $content));
  }
  static function Services() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','services','link_permalink');
    print $twig->render('default/service.html.twig', array('data' => $data,'content' => $content));
  }
  static function Privacy() {
    global $dbh,$data,$twig;
    $content = Process::getSingle('page','privacy-policy','link_permalink');
    print $twig->render('default/content.html.twig', array('data' => $data,'content' => $content));
  }
}
