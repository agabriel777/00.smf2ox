<?php
include 'sql.php';

function update_last_reply ($link,$update = false) {
global $q_update_last_reply;

        wlog("update last_reply...",true);
        ins($link, $q_update_last_reply);
}

function import_likes($link,$update=false) {//import thanks
global $qLikes;

  if (!$update) {	 
     $query = "TRUNCATE TABLE `ow_newsfeed_like`"; // where entityType='forum-post'";
     ins($link, $query);

	 wlog("Import likes...",true);
     ins($link, $qLikes);
  }
		
}


function import_last_read_post ($link, $update=false) {
global $last_read_post1;
global $last_read_post2;

   	$query = "TRUNCATE TABLE `ow_forum_read_topic`";
    ins($link, $query);

        wlog("Import last_read_post...",true);
		
		ins($link, $last_read_post1);
		
		ins($link, $last_read_post2);
		
}


?>