<?php

ini_set('display_errors', 1); 
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('output_buffering', 0);

error_reporting(E_ALL);

include 'config.php';
include 'sql.php';
include 'util.php';

	$link = mysqli_connect($SQL_HOST.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);

	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	wlog("Connected successfully", true);
  
	mysqli_query($link,"SET NAMES utf8");

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
				
				
			}
 
        }
	}
 
	wlog("oxwall->smf - update last message in topic",true);
	ins($link, $qReverse2);
	
	wlog("oxwall->smf - update settings max_msg_id",true);
	ins($link, $qReverse3);
 
	mysqli_close($link);
?>