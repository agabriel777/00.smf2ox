<?php
include 'sql.php';

function update_last_reply ($link,$update = false) {

global $eol;
global $q_update_last_reply;

        echo $eol."update last_reply...";
        $result = mysqli_query($link, $q_update_last_reply);
	    if ($result) { echo "ok."; }
		else { echo "error!";}
		}

function import_likes($link,$update=false) {
	 //import thanks
global $eol;
global $qLikes;
	 
  $query = "TRUNCATE TABLE `ow_newsfeed_like`";
  $result = mysqli_query($link, $query);

        echo $eol."Import likes...";
        $result = mysqli_query($link, $qLikes);
		
	    if ($result) {echo "ok.";}
		else { echo "error!"; }
}



function import_last_read_post ($link, $update=false) {
global $eol;
global $last_read_post1;
global $last_read_post2;

   	$query = "TRUNCATE TABLE `ow_forum_read_topic`";
	$result = mysqli_query($link, $query);

        echo $eol."Import last_read_post...";
		
		$result = mysqli_query($link, $last_read_post1);
	    if ($result) {echo "ok.";}
		else { echo "error!"; }
		
		$result = mysqli_query($link, $last_read_post2);
	    if ($result) {echo "ok.";}
		else { echo "error!"; }
		
		
}


?>