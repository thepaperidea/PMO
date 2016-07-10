<?php

class Essential {
	static function Start() {
    Essential::History();
		Essential::Customer();
  }
	static function Redirect($url,$bool = true){
		global $data;
		$prefix = $bool ? $data['constant']['url'] : false;
		header('Location: '.$prefix.$url);
    	die();
	}
	static function Absolute($url){
		global $data;
		return $data['constant']['url'].$url;
	}
	static function Assetic(){
		global $data;
		$link = $data['path']['argument'][1];
		$type = $data['path']['argument'][2];
		$link = str_replace('_', '/', $link);
		$url = $link.'.'.$type;
		if($type=='css'){
			header("Content-type: text/css");
			$result = CssMin::minify(file_get_contents($url));
		}
		elseif($type=='js'){
			header('Content-Type: application/javascript');
			$result = \JShrink\Minifier::minify(file_get_contents($url));
		}
		print $result;
	}
	static function History() {
    global $session,$data;
    $segment = $session->getSegment('History');
    if($data['path']['namespace']!="Essential::Assetic"){
      $history = $segment->get('complete');
      $history[$data['path']['argument'][0]] = time();
      $segment->set('complete',$history);
    }
  }
	static function Customer(){
		global $data,$twig;
		$user = new Customer();
    if($user->getUser())
		$data['customer'] = $user->getUser();
	}
}
