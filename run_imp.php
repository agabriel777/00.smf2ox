<?php
require_once 'config.php';
include 'smf_import.php';

function connectDB(){

	$link = mysqli_connect($SQL_HOST.':'.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);
	if (!$link) return null;
	return $link;
}

function closeDB($link){
	mysqli_close($link);
}

$link = connectDB()


    
	
	die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully'."<br>";

update_users($link);

mysqli_close($link);
?>