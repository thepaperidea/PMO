<?php
$host = $data['credential']['host'];
$database = $data['credential']['database'];
$username = $data['credential']['username'];
$password = $data['credential']['password'];

try
{
$dbh = new PDO("mysql:host=$host;dbname=$database", $username, $password);
}
catch(PDOException $e)
{
echo $e->getMessage();
}
