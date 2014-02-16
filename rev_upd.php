<?php

ini_set('display_errors', 1); 
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
error_reporting(E_ALL);

include 'config.php';
include 'sql.php';

$link = mysqli_connect($SQL_HOST.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);

if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully'."<br>";
  
  mysqli_query($link,"SET NAMES utf8");

 echo "oxwall->smf - import".$eol;
 $result = mysqli_query($link, $qReverse);
 if ($result) {echo "ok.";}
 else { echo "error! ".mysqli_error($link); }

 echo "oxwall->smf - update isFromImport=1".$eol;
 $result = mysqli_query($link, $qReverse1);
 if ($result) {echo "ok.";}
 else { echo "error! ".mysqli_error($link); }

 
 echo "oxwall->smf - update last message in topic".$eol;
 $result = mysqli_query($link, $qReverse2);
 if ($result) {echo "ok.";}
 else { echo "error! ".mysqli_error($link); }

 echo "oxwall->smf - update settings max_msg_id".$eol;
 $result = mysqli_query($link, $qReverse3);
 if ($result) {echo "ok.";}
 else { echo "error! ".mysqli_error($link); }
 
 mysqli_close($link);
?>