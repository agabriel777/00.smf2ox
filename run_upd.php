<?php

ini_set('display_errors', 1); 
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('output_buffering', 0);

error_reporting(E_ALL);

include 'config.php';
include 'imp_users.php';
include 'imp_posts.php';
include 'imp_after.php';
include 'util.php';

$link = mysqli_connect($SQL_HOST.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);

if (!$link) {
    die('Could not connect: ' . mysql_error());
}
  wlog("Connected successfully",true);

  mysqli_query($link,"SET NAMES utf8");
  
  update_users($link, true);
  
  import_sections ($link, true); //smf_categories -> ow_forum_section

  import_groups ($link, true); //smf_boards -> ow_forum_group
  
  import_topics($link, true);  //smf_topics -> ow_forum_topic
  
  import_posts($link, true);  //smf_messages -> ow_forum_post
  	
  update_last_reply($link, true);
  
  import_likes($link, true);
	
  import_last_read_post($link, true);

  mysqli_close($link);
  
  wlog("All done!",true);
?>