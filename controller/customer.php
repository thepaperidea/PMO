<?php

class Customer {
  public $segment;
  public function __construct(){
    global $session;
    $this->segment = $session->getSegment('Customer');
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
    if(isset($_POST['login-email'])&&isset($_POST['login-password'])&&(!$this->getUser())){
      $getUser = Process::getSingle('customer',$_POST['login-email'],'email');
      if(!$getUser)
      return array('code'=>1,'name'=>'Incorrect Email','class'=>'red');
      $hash = hash('sha1', $_POST['login-password'].$getUser['salt']);
      if($getUser['active']==false)
      return array('code'=>2,'name'=>'User is not yet verified','class'=>'red');
      else if($hash==$getUser['password'])
      $this->segment->set('info', $getUser);
      else
      return array('code'=>2,'name'=>'Incorrect Password','class'=>'red');
    }
    else
    return false;
  }
}
