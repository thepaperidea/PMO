<?php

class Admin {
  static function Home() {
    global $data,$twig;
    $user = new User();
    if($user->getUser())
    print $twig->render('admin/home.html.twig', array('data' => $data,'user' =>$user->getUser()));
    else
    Essential::Redirect('admin/login');
  }
  static function Database() {
    global $twig,$data;
    $user = new User();
    $process = new Process();
    if($user->getUser())
    print $twig->render('admin/database.html.twig', array('data' => $data,'post' => $process->getData()));
    else
    Essential::Redirect('admin/login');
  }
  static function Login() {
  	global $twig,$data;
  	$user = new User();
    $return = $user->login();
  	if($user->getUser())
  	Essential::Redirect('admin');
  	print $twig->render('admin/login.html.twig', array('data' => $data,'return'=>$return));
  }
  static function Logout(){
    $user = new User();
		if($user->getUser())
		$user->logout();
    Essential::Redirect('admin/login');
	}
  static function Upload() {
      $image = new Image();
      return $image->Process();
  }
}
