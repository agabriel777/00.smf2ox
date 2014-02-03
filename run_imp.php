<?php
include 'config.php';
include 'smf_import.php';


$link = mysqli_connect($SQL_HOST.':'.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);

if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully'."<br>";

update_users($link);

mysqli_close($link);
?>