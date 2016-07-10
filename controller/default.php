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
  }
}
