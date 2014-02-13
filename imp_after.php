<?php

function import_likes($link) {
	 //import thanks
	 
  $query = "TRUNCATE TABLE `ow_newsfeed_like`";
  $result = mysqli_query($link, $query);

        echo "Import likes...";
	    $qLike = "INSERT INTO `ow_newsfeed_like` (`entityType`,`entityId`,`userId`,`timeStamp`) ";
		$qLike.= "SELECT 'forum-post', m.`ow_id`, u.`ow_id`, g.`log_time` ";
		$qLike.= "FROM smf_log_gpbp g ";
		$qLike.= "LEFT JOIN smf_messages m ON m.`id_msg` = g.`id_msg` ";
		$qLike.= "LEFT JOIN smf_members u ON u.`id_member`=g.`id_member` ";
		$qLike.= "WHERE m.ow_id IS NOT NULL ";

        $result = mysqli_query($link, $qLike);
		
	    if ($result) {
		echo "ok.";
		}
		else {
		echo "error!";
		}
}


function import_last_read_post ($link) {
global $eol;

	$query = "TRUNCATE TABLE `ow_forum_read_topic`";
	$result = mysqli_query($link, $query);

        echo "Import last_read_post...";
	    $qLike= "INSERT INTO `ow_forum_read_topic` (`topicId`, `userId`, `postId`) ";
	    $qLike.= "SELECT t.`ow_id`, u.ow_id, m.ow_id ";
	    $qLike.= "FROM smf_members u ";
	    $qLike.= "LEFT JOIN smf_messages m ON m.`id_msg`=u.`id_msg_last_visit` ";
	    $qLike.= "LEFT JOIN smf_topics t ON t.`id_topic`=m.`id_topic` ";
		$qLike.= "WHERE  id_msg_last_visit>0 ";
	    $qLike.= "AND m.`id_msg` IS NOT NULL";
echo $eol.$qLike.$eol;
		$result = mysqli_query($link, $qLike);
	    if ($result) {
		echo "ok.";
		}
		else {
		echo "error!";
		}
		
}

function update_last_reply ($link) {
global $eol;

        echo "Update last_reply...";
	    $qLike= "UPDATE ow_forum_topic SET lastPostId = (SELECT ow_id FROM smf_messages sm WHERE sm.`id_msg`=lastPostId)";
        $result = mysqli_query($link, $qLike);
	    if ($result) {
		echo "ok.";
		}
		else {
		echo "error!";
		}
}
?>