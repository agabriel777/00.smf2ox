<?php

$q_update_last_reply = "UPDATE ow_forum_topic t SET t.lastPostId = (SELECT MAX(id) FROM ow_forum_post p WHERE p.topicId=t.id)";


        $qLikes = "INSERT INTO `ow_newsfeed_like` (`entityType`,`entityId`,`userId`,`timeStamp`) ";
		$qLikes.= "SELECT 'forum-post', m.`ow_id`, u.`ow_id`, g.`log_time` ";
		$qLikes.= "FROM smf_log_gpbp g ";
		$qLikes.= "LEFT JOIN smf_messages m ON m.`id_msg` = g.`id_msg` ";
		$qLikes.= "LEFT JOIN smf_members u ON u.`id_member`=g.`id_member` ";
		$qLikes.= "WHERE m.ow_id IS NOT NULL ";

		// toate topicurile pt toti userii
$last_read_post1 =		    "insert into ow_forum_read_topic (topicId, userId, postId) ";
$last_read_post1 .= 		"SELECT t.id AS tid, u.id AS uid, MAX(m.id) pid ";
$last_read_post1 .= 		"FROM ow_forum_topic t ";
$last_read_post1 .= 		"CROSS JOIN ow_base_user u ";
$last_read_post1 .= 		"INNER JOIN ow_forum_post m ON m.topicid=t.id ";
$last_read_post1 .= 		"GROUP BY t.id, u.id ";
//omise topicurile din grupul 

		$last_read_post2 = 		"UPDATE ow_forum_read_topic r INNER JOIN ( ";
		$last_read_post2 .= 		"	SELECT t.ow_id AS tid, u.ow_id AS uid, m.ow_id AS pid ";
		$last_read_post2 .= 		"	 FROM (	SELECT  l.id_topic AS tid, l.id_member AS uid, ";
		$last_read_post2 .= 		"		(SELECT MAX(id_msg) FROM smf_messages m2 WHERE m2.id_msg<=l.id_msg AND m2.id_topic=l.`id_topic`) AS pid ";
		$last_read_post2 .= 		"		FROM smf_log_topics l ";
		$last_read_post2 .= 		"		) AS x ";
		$last_read_post2 .= 		"INNER JOIN smf_messages m ON m.`id_msg`=x.pid ";
		$last_read_post2 .= 		"INNER JOIN smf_topics t ON t.`id_topic` = x.tid ";
		$last_read_post2 .= 		"INNER JOIN smf_members u ON u.`id_member`=x.uid ";
		$last_read_post2 .= 		"WHERE m.ow_id IS NOT NULL ";
		$last_read_post2 .= 		"    ) z ON z.tid=r.`topicId` AND z.uid=r.`userId` SET r.`postId`=z.pid WHERE r.`topicId`=z.tid  AND r.`userId`=z.uid ";

		/*
		conditii neimportate
		cu ow_id= null in SMF
		cu isFromImport = 0 in OW
		*/
		
		$qReverse ="INSERT INTO `vaspun`.`smf_messages` (`id_topic`,`id_board`,`poster_time`,`id_member`,`id_msg_modified`, `subject`, `poster_name`, `poster_email`, `poster_ip`,`smileys_enabled`, `modified_time`,`modified_name`,`body`,`icon`,`approved`,`gpbp_score`,`ow_id`) SELECT t.`id_topic`, t.`id_board`,  p.`createStamp`, u.`id_member`, 0, (SELECT `subject` FROM(SELECT `subject` FROM smf_messages m  WHERE id_topic=113 ORDER BY id_msg ASC ) x LIMIT 1) AS `subject`, u.`member_name`, u.`email_address`, 0, 1, 0, '', p.`text`, 'xx', 1, 0, p.`id` FROM ow_forum_post p LEFT JOIN smf_topics t ON t.`ow_id` = p.`topicId` LEFT JOIN smf_members u ON u.`ow_id`=p.`userId` WHERE p.isFromImport=0 "." LIMIT 1";

		$qReverse2 = "UPDATE smf_topics t INNER JOIN (SELECT MAX(m.id_msg) maxid, m.`id_topic` FROM smf_messages m GROUP BY m.`id_topic`) z ON z.id_topic = t.`id_topic` SET t.`id_last_msg`= z.maxid; ";

		$qReverse3 ="UPDATE smf_settings s SET `value`=(SELECT MAX(id_msg) FROM smf_messages) WHERE variable = 'max_msg_id'";
		

?>