<?php

$path = $_GET['path'];
$routes = json_decode(file_get_contents('includes/route.json'));
foreach($routes as $route)
{
  $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_.]+)', preg_quote($route->route)) . "$@D";
  $matches = array();
  if(preg_match($pattern, $path, $matches)) {
    $data['path']['route'] = $route->route;
    $data['path']['namespace'] = $route->namespace;
    $data['path']['argument'] = $matches;
    Essential::Start();
    call_user_func($route->namespace);
    exit();
  }
  if($route->route==$path){
    $data['path']['route'] = $route->route;
    $data['path']['namespace'] = $route->namespace;
    Essential::Start();
    call_user_func($route->namespace);
    exit();
  }
}
Essential::Start();
Page::notFound();
exit();
