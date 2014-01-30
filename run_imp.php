<?php
include 'smf_import.php';

$link = mysqli_connect('127.0.0.1:3306', 'root', 'mysql','vaspundev');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully'."<br>";

get_smf_user_lines($link);

mysqli_close($link);
?>