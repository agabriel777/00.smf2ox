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
		$query = "TRUNCATE TABLE`ow_newsfeed_like`";
		ins($link, $query);
    }
	 else {
		$query = "DELETE FROM `ow_newsfeed_like` where entityType='forum-post'";
		ins($link, $query);
	}

	 wlog("Import likes...",true);
     ins($link, $qLikes);
  		
}


function import_last_read_post ($link, $update=false) {
global $last_read_cross;
global $eol;

    wlog("Import last_read_post...",true);
    if (!$update) {
      //doar la importu mare
      wlog('truncate table',true);
	  $query = "TRUNCATE TABLE `ow_forum_read_topic`";
      ins($link, $query);
	   
      wlog('cross join',true);	   
      ins($link, $last_read_cross);
	  
  //    wlog('citite in smf',true);	   
//      ins($link, $last_read_post);
	  
    }

    $query="SELECT smf.`id_member` AS smfUid, smf.`id_topic` AS smfTid, smf.`id_msg` AS smfPid,  
u.ow_id owUid, t.ow_id owTid, m.ow_id owPid, ow.postid 
FROM `smf_log_topics` smf
LEFT JOIN smf_topics t ON t.id_topic = smf.id_topic
LEFT JOIN smf_messages m ON m.id_msg=smf.id_msg
LEFT JOIN smf_members u ON u.id_member = smf.id_member
LEFT JOIN ow_forum_read_topic ow ON ow.topicid = t.ow_id AND ow.postid = m.ow_id
WHERE m.ow_id IS NOT NULL
AND IFNULL(m.ow_id,-1) != IFNULL(ow.postid,-2)
ORDER BY u.ow_id, t.ow_id DESC";
    $result = mysqli_query($link, $query);
    wlog(sprintf ("*******smf_log_topics: %d rows.\n", mysqli_num_rows($result)),true);
    while($row = mysqli_fetch_array($result)) {
        echo $row['postid'];
		$insOW = false;
		if (!isset($row['postid'])) $insOW = true;
		else {
		  if ($row['postid']<$row['owPid']) $insOW = true;
		}

		 if ($insOW) {
		   wlog("insert in OW (T=".$row['owTid'].", U=".$row['owUid'].", P=".$row['owPid'].")",true);
		   $q = "INSERT into ow_forum_read_topic (topicID, userId, postId) values (".$row['owTid'].", ".$row['owUid'].", ".$row['owPid'].")";
		   ins($link,$q);
		 
		 }
		 else {
		   wlog("update in SMF (T=".$row['smfTid'].", U=".$row['smfUid'].", P=MAX)",true);
		   $q = "UPDATE smf_log_topic set postid = (SELECT MAX(id_msg) FROM smf_messages) where id_member = ".$row['smfUid']." and id_topic=".$row['smfTid'];
		   ins($link, $q);
		 }

		
		echo $eol;
		flush();
		ob_flush();
	}
    mysqli_free_result($result);
}

		


			
function ow2smf($link) {	
global $MSG_LIMIT;
global $qReverse2;
global $qReverse3;

	wlog("oxwall->smf - import", true);

	//mesajele din ow care trebuie importate
	$qOW = "SELECT t.`id_topic`, t.`id_board`,  p.`createStamp`, u.`id_member`, 0, (SELECT title FROM ow_forum_topic WHERE id=p.topicid) AS `subject`, u.`member_name`, u.`email_address`, 0, 1, 0, '', p.`text`, 'xx', 1, 0, p.`id` FROM ow_forum_post p LEFT JOIN smf_topics t ON t.`ow_id` = p.`topicId` LEFT JOIN smf_members u ON u.`ow_id`=p.`userId` WHERE p.isFromImport=0 LIMIT ".$MSG_LIMIT;
		
    $result = mysqli_query($link, $qOW);

    wlog(sprintf("*******ow_forum_post: %d rows.\n", mysqli_num_rows($result)),true);
	while($row = mysqli_fetch_array($result))
  {
        if ($row['id'] != 0) {
		    echo $eol;
			wlog("se importa din OW mesajul cu id=".$row['id'],true);
			$qReverse ="INSERT INTO `vaspun`.`smf_messages` (`id_topic`,`id_board`,`poster_time`,`id_member`,`id_msg_modified`, `subject`, `poster_name`, `poster_email`, `poster_ip`,`smileys_enabled`, `modified_time`,`modified_name`,`body`,`icon`,`approved`,`gpbp_score`,`ow_id`) VALUES (";
		
			$qReverse .=$row['id_topic'].", ".$row['id_board'].", ".$row['createStamp'].", ".$row['id_member'].", 0, '".$row['subject']."', '".$row['member_name']."', '".$row['email_address']."', 'oxwall', 1, 0, '', '".mysqli_real_escape_string($link, $row['text'])."', 'xx', 1, 0, ".$row['id'].")";
		
			$ins_id = ins($link,$qReverse);
			if ($ins_id!=-1) {
			    wlog("mesaj inserat cu SMF id=".$ins_id,true);
				$q = "update smf_messages set id_msg_modified = id_msg where id_msg = ".$ins_id;
				ins($link,$q);
				
				$q =  "UPDATE ow_forum_post set isFromImport=9 where id = ".$row['id'];
				ins($link, $q);
				
				/* --scos ca se face la sincronizare
				$q = "SELECT id_msg from smf_log_topics where id_member = ".$row['id_member']." and id_topic = ".$row['id_topic'];
				$result2 =  mysqli_query($link, $q);
                if ($row2=mysqli_fetch_array($result2)) {//am linie
				    wlog("id existent = ".$row2['id_msg'],true);
					wlog("id nou = ".$ins_id, true);
				    wlog("fac update in smf_log_topics",true);
  			        $q = "UPDATE smf_log_topics SET id_msg=".$ins_id." where id_member=".$row['id_member']." and id_topic=".$row['id_topic']." and id_msg<".$ins_id;
				}
				else
				{
                    wlog("inserez in smf_log_topics",true);
				    $q = "INSERT INTO smf_log_topics (id_member, id_topic, id_msg) VALUES (".$row['id_member'].", ".$row['id_topic'].", ".$ins_id.")";
					ins($link, $q);
				}
				*/
				
			}
 
        }
	}
 
	wlog("oxwall->smf - update last message in topic",true);
	ins($link, $qReverse2);
	
	wlog("oxwall->smf - update settings max_msg_id",true);
	ins($link, $qReverse3);
 }
 
		

?>