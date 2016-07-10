<?php

class User {
  public $segment;
  public function __construct(){
    global $session;
    $this->segment = $session->getSegment('User');
  }
  public function getUser(){
    $info = $this->segment->get('info');
    return $info;
  }
  public function logout(){
    $this->segment->set('info', false);
  }
  public function login(){
    global $dbh;
    $info = $this->segment->get('info');
    if(isset($_POST['email'])&&isset($_POST['password'])&&(!$this->getUser())){
      $getUser = Process::getSingle('user',$_POST['email'],'email');
      if(!$getUser)
      return array('code'=>1,'name'=>'Incorrect Email.','class'=>'red');
      $hash = hash('sha1', $_POST['password'].$getUser['salt']);
      if($hash==$getUser['password'])
      $this->segment->set('info', $getUser);
      else
      return array('code'=>2,'name'=>'Incorrect Password.','class'=>'red');
    }
    else
    return false;
  }
}
