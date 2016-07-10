<?php

class Process {
	public $sql;
	public $values;
	public $columnset;

  public function addsubmitRow($table){
    $this->processFiles();
    global $dbh;
    $columns = split(',', $_POST['columnSet']);
    $values = array();
    $columnset = array();
    $questionmarks = array();
    $sql = "INSERT INTO ".$table;
    foreach ($columns as $column) {
      if($column!=""){
        if(is_array($_POST[$column]))
        $values[] = implode(',',$_POST[$column]);
        else
        $values[] = $_POST[$column];
        $columnset[] = "`$column`";
      }
    }
    $query = implode(',', $columnset);
    foreach ($columnset as $each) {
      $questionmarks[] = "?";
    }
    $query2 = implode(',', $questionmarks);
    $sql .= "(".$query.") VALUES (".$query2.")";
    $q = $dbh->prepare($sql);
    $q->execute($values);
  }

  public function editsubmitRow($table,$id){
    $this->processFiles();
    global $dbh;
    $columns = split(',', $_POST['columnSet']);
    $values = array();
    $columnset = array();
    $sql = "UPDATE ".$table." SET ";
    foreach ($columns as $column) {
      if($column!=""){
        if(is_array($_POST[$column]))
        $values[] = implode(',',$_POST[$column]);
        else
        $values[] = $_POST[$column];
        $columnset[] = "`$column` = ?";
      }
    }
    $values[] = $id;
    $query = implode(',', $columnset);
    $sql .= $query." WHERE id = ?";
    $q = $dbh->prepare($sql);
    $q->execute($values);
  }

  public function processFiles(){
    global $data;
    $destination_folder	= $data['file']['destination'];
    if(!empty($_FILES)){
      foreach ($_FILES as $key => $value) {
        if($value['error']==0){
          $file_info = pathinfo($value['name']);
          $file_extension = strtolower($file_info["extension"]);
          $file_name_only = strtolower($this->clean($file_info["filename"]));
          $new_file_name = $file_name_only. '_' .  rand(0, 9999999999) . '.' . $file_extension;
          move_uploaded_file($value["tmp_name"], $destination_folder . $new_file_name);
          $_POST[$key] = $new_file_name;
        }
      }
    }
  }

  public function clean($string) {
     $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
     return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
  }

  public function columnSplits($data,$seperator = "_"){
    $split = explode('_', $data);
    return $split;
  }

  public function generateSelect($value,$table,$column){
    global $dbh;
    $sql = 'SELECT * FROM '.$table;
    $select = "<select name='$column'>";
    foreach ($dbh->query($sql) as $row) {
      $id = $row['id'];
      $name = $row['name'];
      if($id==$value)
      $select .= "<option value=$id selected>$name</option>";
      else
      $select .= "<option value=$id>$name</option>";
    }
    $select .= "</select>";
    return $select;
  }

  public function generateMultiSelect($value,$table,$column){
    global $dbh;
    $values = split(',', $value);
    $sql = 'SELECT * FROM '.$table;
    $select = "<select name='".$column."[]' multiple>";
    foreach ($dbh->query($sql) as $row) {
      $id = $row['id'];
      $name = $row['name'];
      if(in_array($id,$values))
      $select .= "<option value=$id selected>$name</option>";
      else
      $select .= "<option value=$id>$name</option>";
    }
    $select .= "</select>";
    return $select;
  }

  public function removeRow($table,$id){
    global $dbh;
    $sql = 'DELETE from '.$table.' where id=:id';
    $query = $dbh->prepare($sql);
    $query->execute(array(':id' => $id));
  }

  public function editRow($table,$id,$redirect){
    global $dbh;
    $sql = 'SELECT * FROM '.$table.' where id=:id';
    $query = $dbh->prepare($sql);
    $query->execute(array(':id' => $id));
    $row = $query->fetch();
    $columnset = array();
    $dataset = array();
    foreach ($row as $key => $value) {
      if((!is_numeric($key))&&($key!="id")){
        $columnset[] = $key;
        $dataset[$x][$key] = $value;
      }
    }
    $post = array('action'=>$_GET['action'],'table'=>$table,'columnset'=>$columnset,'dataset'=>$dataset,'redirect'=>$redirect);
    return $post;
  }

  public function addRow($table,$redirect){
    global $dbh;
    $sql = 'SELECT * FROM '.$table;
    $query = $dbh->prepare($sql);
    $query->execute();
    $row = $query->fetch();
    $columnset = array();
    $dataset = array();
    foreach ($row as $key => $value) {
      if((!is_numeric($key))&&($key!="id")){
        $columnset[] = $key;
        $dataset[$x][$key] = $value;
      }
    }
    $post = array('action'=>$_GET['action'],'table'=>$table,'columnset'=>$columnset,'dataset'=>$dataset,'redirect'=>$redirect);
    return $post;
  }

  public function getSingle($table,$i,$column = 'id'){
    global $dbh;
    $sql = 'SELECT * FROM '.$table.' WHERE '.$column.' = :i';
    $query = $dbh->prepare($sql);
    $query->execute(array(':i' => $i));
    $row = $query->fetch();
    if($row){
      $array = array();
      foreach ($row as $key => $value) {
        if(!is_numeric($key)){
          $split = Process::columnSplits($key);
          if($split[1]=='id')
          $value = Process::getSingle($split[0],$value);
          elseif($split[1]=='ids')
          $value = Process::getMultiple($split[0],$value);
          $array[$split[0]] = $value;
        }
      }
      return $array;
    }
    else
    return false;
  }

  public function getMultiple($table,$i,$column = 'id'){
    global $dbh;
    $ids = split(',',$i);
    $array = array();
    $sql = 'SELECT * FROM '.$table;
    foreach ($dbh->query($sql) as $row)
    {
      if(in_array($row[$column],$ids))
      $array[] = $row;
    }
    return $array;
  }

  public function getfromId($table,$id,$column = 'name'){
		global $dbh;
		$sql = 'SELECT '.$column.' FROM '.$table.' WHERE id = :id';
		$query = $dbh->prepare($sql);
		$query->execute(array(':id' => $id));
		$row = $query->fetch();
		return $row[$column];
	}

  public function getfromIds($table,$id,$column = 'name'){
    global $dbh;
    $ids = split(',',$id);
    $array = array();
    $sql = 'SELECT id,'.$column.' FROM '.$table;
    foreach ($dbh->query($sql) as $row){
      if(in_array($row['id'],$ids))
        $array[] = $row[$column];
    }
    return $array;
  }

  public function identifyId($key,$value){
    $args = $this->columnSplits($key);
    if($args[1]=='id')
      return $this->getfromId($args[0],$value);
    elseif($args[1]=='ids')
      return $this->getfromIds($args[0],$value);
    else
      return false;
  }

  public function getData($settable = false,$setaction = false){
    global $dbh,$data;
		if ($settable)
		$_GET['table'] = $settable;
		if ($setaction)
		$_GET['action'] = $setaction;
    if($_GET['table']){
      if(!$_GET['action'])
        $_GET['action'] = 'display';
      $action = $_GET['action'];
      $table = $_GET['table'];
      $post = false;
      $check = false;
      foreach ($data['admin']['database'] as $each) {
        if($each['table']==$table){
          $check = true;
          break;
        }
      }
      if($check){
        if($action=='display'){
          if((!$_GET['page'])||(!is_numeric($_GET['page'])))
            $_GET['page'] = 1;
          $page = $_GET['page']-1;
          if((!$_GET['limit'])||(!is_numeric($_GET['limit'])))
            $_GET['limit'] = 10;
          $limit = $_GET['limit'];
          $sql = "SELECT id FROM ".$table;
          $query = $dbh->query($sql);
          $count = 0;
          foreach ($query as $row)
            $count++;
          $pagecount = ceil($count/$limit);
          $sql = "SELECT * FROM ".$table." ORDER BY id DESC LIMIT ".$page*$limit.",".$limit;
          $query = $dbh->query($sql);
          $columnset = array();
          $dataset = array();
          $x = 0;
          foreach ($query as $row) {
            foreach ($row as $key => $value) {
              if(!is_numeric($key)){
                if($x==0)
                  $columnset[] = $key;
                $dataset[$x][$key] = $this->identifyId($key,$value)?$this->identifyId($key,$value):$value;
              }
            }
            $x++;
          }
          $post = array('action'=>$_GET['action'],'table'=>$table,'columnset'=>$columnset,'dataset'=>$dataset,'page'=>$page,'pagecount'=>$pagecount,'limit'=>$limit);
        }
        elseif($action=='remove'){
          $this->removeRow($_GET['table'],$_GET['id']);
          Essential::Redirect($_SERVER['HTTP_REFERER'],false);
        }
        elseif($action=='edit'){
          if(!$_SERVER['HTTP_REFERER'])
            $_SERVER['HTTP_REFERER'] = Essential::Absolute('admin?table='.$_GET['table']);
          if(isset($_POST['submit'])){
            $this->editsubmitRow($_GET['table'],$_GET['id']);
            Essential::Redirect($_POST['redirectUri'],false);
          }
          else
            return $this->editRow($_GET['table'],$_GET['id'],$_SERVER['HTTP_REFERER']);
        }
        elseif($action=='add'){
          if(!$_SERVER['HTTP_REFERER'])
            $_SERVER['HTTP_REFERER'] = Essential::Absolute('admin?table='.$_GET['table']);
          if(isset($_POST['submit'])){
            $this->addsubmitRow($_GET['table']);
            Essential::Redirect($_POST['redirectUri'],false);
          }
          else
            return $this->addRow($_GET['table'],$_SERVER['HTTP_REFERER']);
        }
        return $post;
      }
      else
      return false;
    }
    else
    return false;
  }

}
