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
	    $qExec= "insert into ow_forum_read_topic (topicId, userId, postId) ";
        $qExec.="select t.ow_id, u.ow_id, m.ow_id ";
        $qExec.="from smf_log_topics l ";
        $qExec.="inner join smf_messages m on m.id_msg=l.id_msg ";
        $qExec.="inner join smf_topics t on t.id_topic = l.id_topic ";
        $qExec.="inner join smf_members u on u.id_member=l.id_member ";

		/*where u.ow_id=3
         and t.ow_id=62
         */
        
		echo $eol.$qLike.$eol;
		$result = mysqli_query($link, $qExec);
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