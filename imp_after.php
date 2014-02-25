<?php

function update_last_reply ($link,$update = false) {  //OW last reply in topic
        $q_update_last_reply = "UPDATE ow_forum_topic t SET t.lastPostId = (SELECT MAX(id) FROM ow_forum_post p WHERE p.topicId=t.id)";

        wlog("update last_reply...",true);
        ins($link, $q_update_last_reply);
}

function import_likes($link,$update=false) {//import thanks

$qLikes = "INSERT INTO `ow_newsfeed_like` (`entityType`,`entityId`,`userId`,`timeStamp`) 
			   SELECT 'forum-post', m.`ow_id`, u.`ow_id`, g.`log_time`
			   FROM smf_log_gpbp g 
			   INNER JOIN smf_messages m ON m.`id_msg` = g.`id_msg` 
			   INNER JOIN smf_members u ON u.`id_member`=g.`id_member` 
			   LEFT JOIN ow_newsfeed_like n ON n.entityID=m.ow_id AND n.userid=u.ow_id
			   WHERE 1=1 -- m.ow_id IS NOT NULL
			   AND n.id IS NULL
			   AND g.score>0
";


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



			
function ow2smf($link) {	 
global $MSG_LIMIT;
		
		//update SMF last_message in topic
		$qReverse2 = "UPDATE smf_topics t INNER JOIN (SELECT MAX(m.id_msg) maxid, m.`id_topic` FROM smf_messages m GROUP BY m.`id_topic`) z ON z.id_topic = t.`id_topic` SET t.`id_last_msg`= z.maxid "; 

		//update SMF max_msg_id
		$qReverse3 ="UPDATE smf_settings s SET `value`=(SELECT MAX(id_msg) FROM smf_messages) WHERE variable = 'maxMsgID'";

		
		
	wlog("oxwall->smf - import", true);

	//mesajele din ow care trebuie importate
	$qOW = "SELECT t.`id_topic`, t.`id_board`,  p.`createStamp`, u.`id_member`, 0, (SELECT title FROM ow_forum_topic WHERE id=p.topicid) AS `subject`, u.`member_name`, u.`email_address`, 0, 1, 0, '', p.`text`, 'xx', 1, 0, p.`id` FROM ow_forum_post p LEFT JOIN smf_topics t ON t.`ow_id` = p.`topicId` LEFT JOIN smf_members u ON u.`ow_id`=p.`userId` WHERE p.isFromImport=0 LIMIT ".$MSG_LIMIT;
		
    $result = mysqli_query($link, $qOW);

    wlog(sprintf("*******ow_forum_post: %d rows.\n", mysqli_num_rows($result)),true);
	while($row = mysqli_fetch_array($result))
  {
        if ($row['id'] != 0) {
		   // echo $eol;
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

			     $q = "UPDATE smf_log_topics set id_msg = ".$ins_id." where id_member = ".$row['id_member']." and id_topic=".$row['id_topic'];
				ins($link, $q);
				
			}
 
        }
	}
 
	wlog("oxwall->smf - update last message in topic",true);
	ins($link, $qReverse2);
	
	wlog("oxwall->smf - update settings max_msg_id",true);
	ins($link, $qReverse3);
 }
 
		
	
		
function import_last_read_post_the_easy_way($link, $update=false)
{
	wlog("Import last_read_post...",true);
	if (!$update
	{
		/*doar la import*/
		wlog('truncate table',true);
		$query = "TRUNCATE TABLE `ow_forum_read_topic`";
		ins($link, $query);		wlog('cross join',true);
		$last_read_cross ="insert into ow_forum_read_topic (topicId, userId, postId) SELECT t.id AS tid, u.id AS uid, MAX(m.id) pid FROM ow_forum_topic t CROSS JOIN ow_base_user u INNER JOIN ow_forum_post m ON m.topicid=t.id GROUP BY t.id, u.id ";
		ins($link, $last_read_cross);
	}
	else wlog('Running the Update Script',true);
	$cond = "WHERE z.smfPid<>z.owPid";	if ($update) $cond = "WHERE z.smfPid!=z.owPid";
	$ds="SELECT * FROM (
		SELECT topicid AS owTid, userid AS owUid,
		(SELECT id_topic FROM smf_topics WHERE ow_id=ow.topicid) AS smfTid,
		(SELECT id_member FROM smf_members WHERE ow_id=ow.userid) AS smfUid,
		postid AS owPid,  getLastReadInSmf(userid, topicid) AS smfPid
		FROM ow_forum_read_topic ow";
		/*		"-- WHERE 1=1 AND ow.userid in (2,3)". 		Nu a mers altfel! */
		$ds.=") z ".$cond;
		$result = mysqli_query($link, $ds);
	if ($result)
	{		
		wlog(sprintf("*******ow_log: %d rows different.\n", mysqli_num_rows($result)),true);
		while($row = mysqli_fetch_array($result))
		{
			if ($row['smfPid'] < $row['owPid'])
			{
				/*am in smf necitite*/
				if ($update)
				{
					/* Daca e update, atunci inseamna ca au fost citite in OW => Update SMF	*/
					SMF_MarkRead($link, $row['owUid'], $row['smfUid'], $row['owTid'], $row['smfTid'], $row['owPid'], $row['smfPid']);
				}
				else
				{
					/* Daca e import, atunci inseamna ca sunt necitite in SMF => Update in OW*/
					OW_MarkRead($link, $row['owUid'], $row['smfUid'], $row['owTid'], $row['smfTid'], $row['owPid'], $row['smfPid']);
				}
			}
			else
			{
				if ($update)
				{
					/* Daca e update, atunci inseamna ca au fost citite in SMF => Update in OW	*/
					OW_MarkRead($link, $row['owUid'], $row['smfUid'], $row['owTid'], $row['smfTid'], $row['owPid'], $row['smfPid']);
				}
				else
				{
					/*
					Daca e import, n-ar trebui sa faca nimic
					probabil nu mai era cazul, dar ca sa nu iasa vreo 	belea :)
					*/
				}
			}/*if < */
		}/*while*/
	}/*if result*/
	else	
	{		
		wlog('No result for '.$ds,true);
	}
}


function SMF_MarkRead($link, $OW_User, $SMF_User, $OW_Topic, $SMF_Topic, $OW_PID, $SMF_PID)
{

	$q = "select count(*) cnt from smf_log_topics where id_member=".$SMF_User." and id_topic=".$SMF_Topic;
	$result2 = mysqli_query($link, $q);
	if ($result2)
 	{
		$row2 = mysqli_fetch_array($result2);
		if ($row2['cnt']==0)
		{			
			/*insert smf*/
			wlog("insert SMF (U-T): ".$SMF_User."-".$SMF_Topic, true);
			$q="INSERT INTO smf_log_topics (id_member, id_topic, id_msg) VALUES (".$SMF_User.", ".$SMF_Topic.", (select id_msg from smf_messages where ow_id=".$OW_PID.") )";
			ins($link,$q);
		}
		else
 		{
			wlog("update SMF (U-T): ".$SMF_User."-".$SMF_Topic, true);
			$q="UPDATE smf_log_topics SET id_msg=(select id_msg from smf_messages where ow_id=".$OW_PID.") where id_member=".$SMF_User." and id_topic=".$SMF_Topic;
			$k=upd_log($link, $q);
			$k=0;
			wlog(sprintf("%d rows updated",$k),true);
		}
	} /*if result2*/
}

function OW_MarkRead($link, $OW_User, $SMF_User, $OW_Topic, $SMF_Topic, $OW_PID, $SMF_PID)
{
	/*wlog("update OW (U-T): ow=".$row['owUid']."-".$row['owTid'].", smf=".$row['smfUid']."-".$row['smfTid'], true);*/
	wlog("update OW (U-T): ow=".$OW_User."-".$OW_Topic.", smf=".$SMF_User."-".$SMF_Topic, true);
	/*$q = "UPDATE ow_forum_read_topic set postId = ".$row['smfPid']." where userId=".$row['owUid']." and topicId = ".$row['owTid'];*/
	$q = "UPDATE ow_forum_read_topic set postId = ".$SMF_PID." where userId=".$OW_User." and topicId = ".$OW_Topic;	$k=upd_log($link, $q);
	/*if ($k!=0) wlog($row['owPid']."->".$row['smfPid'], true);*/
	if ($k!=0) wlog("Topic Read in SMF. Updated OXWALL for U-T:".$OW_User."-".$OW_Topic.". OW_PID:".$OW_PID."|SMF Pid:".$SMF_PID."", true);
}


?>