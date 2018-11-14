<?php

class Essential {
	static function Start() {
		API::fetchBrands();
		API::fetchCategories();
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
}
