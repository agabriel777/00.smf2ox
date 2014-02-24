<?php

ini_set('display_errors', 1); 
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('output_buffering', 0);

error_reporting(E_ALL);

include 'config.php';
include 'util.php';

$link = mysqli_connect($SQL_HOST.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);

if (!$link) { die('Could not connect: ' . mysql_error()); }
  wlog("Connected successfully",true);

  mysqli_query($link,"SET NAMES utf8");
  
  
  $q = "SELECT p.* FROM ow_forum_post "; //p WHERE TEXT LIKE '%src=\"//www.youtube.com/%' AND p.topicid = 160";
  $result = mysqli_query($link, $q);
  if ($result) {
    $n = mysqli_num_rows($result);
    wlog("rows:".$n);

  while ($row = mysqli_fetch_array($result)) 
	{
	  wlog($row['text']);
	}
  }
  

  wlog("All done!",true);
?>