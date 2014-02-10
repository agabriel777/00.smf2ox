<?php
include 'config.inc.php';
include 'imp_users.php';
include 'imp_posts.php';
include 'util.php';

$link = mysqli_connect($SQL_HOST.':'.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);

if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully'."<br>";
/*
  update_users($link);
  
  import_sections ($link); //smf_categories -> ow_forum_section

  import_groups ($link); //smf_boards -> ow_forum_group

  
  import_topics($link);  //smf_topics -> ow_forum_topic

  */
  ini_set('max_execution_time', 300); //300 seconds = 5 minutes
  //import_posts($link);  //smf_messages -> ow_forum_post
  
  import_likes($link);
  
  echo "done!";
mysqli_close($link);
?>