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
/*
	$result = mysqli_query($link, $qReverse);
	if ($result) {echo "ok.";}
	else { echo "error! ".mysqli_error($link); }


	echo "oxwall->smf - update isFromImport=1".$eol;
	$result = mysqli_query($link, $qReverse1);
	if ($result) {echo "ok.";}
	else { echo "error! ".mysqli_error($link); }
*/

    $result = mysqli_query($link, $qOW);

    wlog(sprintf("*******ow_forum_post: %d rows.\n", mysqli_num_rows($result)),true);
	while($row = mysqli_fetch_array($result))
  {
        if ($row['id'] != 0) {
		    wlog("import mesaj cu OW id=".$row['id'],true);
			$qReverse ="INSERT INTO `vaspun`.`smf_messages` (`id_topic`,`id_board`,`poster_time`,`id_member`,`id_msg_modified`, `subject`, `poster_name`, `poster_email`, `poster_ip`,`smileys_enabled`, `modified_time`,`modified_name`,`body`,`icon`,`approved`,`gpbp_score`,`ow_id`) VALUES (";
		
			$qReverse .=$row['id_topic'].", ".$row['id_board'].", ".$row['createStamp'].", ".$row['id_member'].", 0, '".$row['subject']."', '".$row['member_name']."', '".$row['member_name']."', '".$row['email_address']."', 'oxwall', 1, 0, '', '".$row['text']."', 'xx', 1, 0, ".$row['id'].")";
		
			$ins_id = ins($link,$qReverse);
			if ($ins_id!=-1) {
			    wlog("mesaj inserat cu SMF id=".$ins_id,true);
				$q = "update smf_messages set id_msg_modified = id_msg where id = ".$ins_id;
				ins($link,$q);
				
				$q =  "UPDATE ow_forum_post set isFromImport=1 where id = ".$row['id'];
				ins($link, $q);
			}
 
        }
	}
 
	wlog("oxwall->smf - update last message in topic",true);
	ins($link, $qReverse2);
	
	wlog("oxwall->smf - update settings max_msg_id",true);
	ins($link, $qReverse3);
 
	mysqli_close($link);
?>