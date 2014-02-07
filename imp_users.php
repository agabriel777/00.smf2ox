<?php

function update_users($link)
{
global $eol;
$query = "delete from ow_base_user where id <> 999";
$result = mysqli_query($link, $query);


$query = "DELETE FROM ow_base_question_data WHERE userid NOT IN(SELECT id FROM ow_base_user)";
$result = mysqli_query($link, $query);


$query = "SELECT CASE WHEN email_address='' THEN member_name ELSE email_address END AS email_address2, a.* FROM smf_members a ORDER BY date_registered";

$result = mysqli_query($link, $query);
    printf("Select returned %d rows.\n <br>", mysqli_num_rows($result));
	echo $eol;
	while($row = mysqli_fetch_array($result))
  {
   importUser($link, $row);
  }
   
    /* free result set */
    mysqli_free_result($result);
}


function importUser($link, $row)
{
include 'config.inc.php';
		if (!inUserDb($link, $row['member_name'])) 
		{
			echo $row['member_name'].' - insert...';
		
			$query = 'INSERT INTO `ow_base_user` (`email`, `username`, `password`, `joinStamp`, `activityStamp`, `accountType`, `emailVerify`, `joinIp`) VALUES ';

			$ip = ip2long($row['member_ip']);
			if ($ip == -1 || $ip === FALSE) {
				$ip=0;
			}
			
			$user_name = str_replace('.','_',$row['member_name']);
			
			
			$query.= "( '".$row['email_address2']."', '".$user_name."', '".$row['email_address']."', ".$row['date_registered'].", ".$row['last_login'].", '290365aadde35a97f11207ca7e4279cc', 1, ".$ip.")";
//echo $query;
			$result = mysqli_query($link, $query);

     		if ($result) { echo "ok"; }
	    	else  echo "error ".$result;
	        echo $eol;
						
			$id = mysqli_insert_id($link);
			//echo "ow_id=".$id.$eol;

			
			if ($id!=0) {
   			  upd($link, 'smf_members', 'id_member' , $row['id_member'], $id);
			
			echo "real_name...";
			$q2 = "INSERT INTO `ow_base_question_data` (questionName, userID, textValue, intValue, dateValue) VALUES";
			$q2.= "( 'realname', ".$id.", '".$row['real_name']."', 0, NULL)";
			//echo $q2.$eol;
			$result = mysqli_query($link, $q2);
     		if ($result) { echo "ok"; }
	    	else  echo "error ".mysqli_error($link);
			echo $eol;
			

			echo "sex...";
			$q2 = "INSERT INTO `ow_base_question_data` (questionName, userID, textValue, intValue, dateValue) VALUES";
			$q2.= "( 'sex', ".$id.", '',".$row['gender'].", NULL)";
			//echo $eol.$q2.$eol;
			$result = mysqli_query($link, $q2);
     		if ($result) { echo "ok"; }
	    	else  echo "error ".$result;
			echo $eol;
			
			echo "birthday...";
			$q2 = "INSERT INTO `ow_base_question_data` (questionName, userID, textValue, intValue, dateValue) VALUES";
			$q2.= "( 'birthday', ".$id.", '', 0,'".$row['birthdate']."')";
					//	echo $eol.$q2.$eol;
			$result = mysqli_query($link, $q2);
     		if ($result) { echo "ok"; }
	    	else  echo "error ".$result;
			echo $eol;
			}
			
		}
		else
		{
		    echo $row['member_name'].' - UPDATE...';
    		$query = "UPDATE `ow_base_user` SET `joinStamp` = ".$row['date_registered'].", `activityStamp` = ".$row['last_login']." WHERE `username` = '".$row['member_name']."'";
		}	

		//echo $query;
        echo $eol;
	
}


function inUserDb ($link, $username)
{
	$query = "SELECT username FROM `ow_base_user` WHERE `username` = '".$username."'";
	// Daca nu e nici o inregistre - fals, daca exista - true.
	$result = mysqli_query($link, $query);
	if (mysqli_num_rows($result)==1) {
	  return (true);
	}
	else {
	return(false);
	}
	
}
