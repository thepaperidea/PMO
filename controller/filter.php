<?php

class Filter {

  public function Asset($url){
    global $data;
    $info = pathinfo($url);
    $path = $info['dirname'];
    $file = $info['filename'];
    $ext = $info['extension'];
    $path = str_replace('/','_',$path);
    $url = $data['constant']['url'].'assetic/assets_'.$path.'_'.$file.'/'.$ext;
    return $url;
  }

  public function Output($value,$column){
    global $data;
    $args = Process::columnSplits($column);
    if($args[1]=='markdown')
    return "<span class='whitespacenowrap'>Markdown HTML</span>";
    elseif($args[1]=='image'){
      $filename = $data['constant']['url'].$data['image']['destination'].$data['image']['thumbnail']['prefix'].$value;
      if((pathinfo($filename, PATHINFO_EXTENSION)&&(getimagesize($filename))))
      return "<span class='whitespacenowrap hoverimg'>View Image<img src='$filename'></span>";
      else
      return $value;
    }
    elseif($args[1]=='svg'){
      if($value){
        $filename = $data['constant']['url'].$data['file']['destination'].$value;
        return "<a target='_blank' href='$filename'>Open image in new tab</a>";
      }
      else
      return null;
    }
    elseif($args[1]=='bool'){
      if($value)
      return "yes";
      else
      return "no";
    }
    elseif(is_array($value)){
      $string = "";
      foreach ($value as $each) {
        $string .= "<span class='whitespacenowrap'>$each</span>";
      }
      return $string;
    }
    else
    return $value;
  }

  public function Input($value,$column){
    $args = Process::columnSplits($column);
    if($args[1]=='markdown')
    return "<span class='relative'><textarea id='$column' name='$column' value=>$value</textarea><ul class='corner-help'><li><a href='javascript:ajaxUpload(\"$column\",0,0,1);'>Insert Image</a></li><li><a target='_blank' href='http://daringfireball.net/projects/markdown/syntax'>Syntax</a></li></ul></span>";
    elseif($args[1]=='id'){
      $sessionuser = new User();
      $sessionuserinfo = $sessionuser->getUser();
      $user = Process::getSingle('user',$sessionuserinfo['id']);
      $userid = $user['id'];
      $username = $user['name'];
      if($args[0]=='user')
      return "<input type='hidden' name='$column' value='$userid'><input type='text' name='$column' value='$username' disabled>";
      else
      return Process::generateSelect($value,$args[0],$column);
    }
    elseif($args[1]=='ids')
    return Process::generateMultiSelect($value,$args[0],$column);
    elseif($args[1]=='bool')
    return ($value)?"<label>Yes</label><input type='radio' name='$column' value=1 checked><label>No</label><input type='radio' name='$column' value=0>":"<label>Yes</label><input type='radio' name='$column' value=1><label>No</label><input type='radio' name='$column' value=0 checked>";
    elseif($args[1]=='image'){
      $dimention = ($args[2])?explode('x',$args[2]):[0,0];
      $width = $dimention[0];
      $height = $dimention[1];
      return "<span class=relative><input type=text id='$column' name='$column' value='$value'><ul class='corner-help'><li><a href='javascript:ajaxUpload(\"$column\",$width,$height);'>Insert Image</a></li></ul></span>";
    }
    elseif($args[1]=="datetime"){
      $now = date('Y-m-d H:i:s',strtotime(now));
      $value = ($value ? $value : $now);
      if($args[2]=="current")
      return "<input type='hidden' name='$column' value='$value'><input type='text' value='$value' disabled>";
      else if($args[2]=="now")
      return "<input type='hidden' name='$column' value='$now'><input type='text' name='$column' value='$now' disabled>";
      else
      return "<input type='text' class='datetimepicker' name='$column' value='$value'>";
    }
    elseif(($args[1]=="file")||($args[1]=="svg"))
    return "<input type='hidden' name='$column' value='$value'><input type='file' name='$column' value='$value'>";
    elseif($args[1]=="text")
    return "<textarea name='$column'>$value</textarea>";
    elseif($args[1]=="permalink")
    return "<input class='permalink' type=text name='$column' value='$value'>";
    else
    return "<input type=text name='$column' value='$value'>";
  }

}
