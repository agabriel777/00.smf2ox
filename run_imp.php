<?php
include 'config.inc.php';
include 'imp_users.php';
include 'imp_topics.php';

$link = mysqli_connect($SQL_HOST.':'.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);

if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully'."<br>";

 // update_users($link);
  
  import_sections ($link);

  import_groups ($link); 
  
  
mysqli_close($link);
?>