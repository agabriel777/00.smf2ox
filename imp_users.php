<?php

function update_users($link, $update = false)
{

$query = "delete from ow_base_user where untouchables=0 ";
if ($update) { $query.=" and 1=0 ";} //sa nu stearga nimic
ins($link, $query);

$query = "Select max(id) maxid from ow_base_user";

$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result);
$maxid = $row['maxid'];
wlog("maxid=".$maxid,true);
 
$query = "UPDATE ow_base_user set id=7 where username = 'alecu'";
ins($link, $query);

$query = "ALTER TABLE ow_base_user AUTO_INCREMENT = ".$maxid;
ins($link, $query);

$query = "DELETE FROM ow_base_question_data WHERE userid NOT IN (SELECT id FROM ow_base_user)";
ins($link, $query);


	$query = "SELECT CASE WHEN email_address='' THEN member_name ELSE email_address END AS email_address2, a.* FROM smf_members a ";
	if ($update) { $query .= "where a.ow_id is null ";} 
	$query .= "ORDER BY id_member";

    $result = mysqli_query($link, $query);
	wlog($query,true);

    wlog(sprintf ("*******smf_members: %d rows.\n", mysqli_num_rows($result)),true);
	while($row = mysqli_fetch_array($result)) {
		importUser($link, $row);
		flush();
	}
    mysqli_free_result($result);
}


function importUser($link, $row)
{

        $chk = inUserDb($link, $row['member_name']);
		if ($chk==-1) 
		{
			wlog($row['member_name'].' - insert...');
		
			$query = 'INSERT INTO `ow_base_user` (`email`, `username`, `password`, `joinStamp`, `activityStamp`, `accountType`, `emailVerify`, `joinIp`) VALUES ';

			$ip = ip2long($row['member_ip']);
			if ($ip == -1 || $ip === FALSE) {
				$ip=0;
			}
			
			$user_name = str_replace('.','_',$row['member_name']);
			
			
			$query.= "( '".$row['email_address2']."', '".$user_name."', '".$row['email_address']."', ".$row['date_registered'].", ".$row['last_login'].", '290365aadde35a97f11207ca7e4279cc', 1, ".$ip.")";

			ins($link, $query);
			$id = mysqli_insert_id($link);
			
		
			if ($id!=0) {
   			  upd($link, 'smf_members', 'id_member' , $row['id_member'], $id);
			
			wlog("real_name...");
			$q2 = "INSERT INTO `ow_base_question_data` (questionName, userID, textValue, intValue, dateValue) VALUES";
			$q2.= "( 'realname', ".$id.", '".$row['real_name']."', 0, NULL)";
            ins($link, $q2);
	

			wlog("sex...");
			$q2 = "INSERT INTO `ow_base_question_data` (questionName, userID, textValue, intValue, dateValue) VALUES";
			$q2.= "( 'sex', ".$id.", '',".$row['gender'].", NULL)";
            ins($link, $q2);
			
			wlog("birthday...");
			$q2 = "INSERT INTO `ow_base_question_data` (questionName, userID, textValue, intValue, dateValue) VALUES";
			$q2.= "( 'birthday', ".$id.", '', 0,'".$row['birthdate']."')";
			ins($link, $q2);

			}
			
		}
		else
		{
		    wlog($row['member_name'].' - UPDATE...');
    		$query = "UPDATE `ow_base_user` SET `joinStamp` = ".$row['date_registered'].", `activityStamp` = ".$row['last_login']." WHERE `username` = '".$row['member_name']."'";
			 ins($link, $query);
			 upd($link, 'smf_members', 'id_member' , $row['id_member'], $chk);
			 
		}	
	
}


function inUserDb ($link, $username)
{//return -1 sau id daca exista

	if ($username=="mihaelab777")  $username="MihaSan";
	$query = "SELECT id FROM `ow_base_user` WHERE `username` = '".$username."'";
	$result = mysqli_query($link, $query);
	$row=mysqli_fetch_array($result);
	return isset($row['id'])?$row['id']:-1;
}
