<?php

function update_users($link, $update = false)
{
global $eol;
global $USER_SKIP_QUERY;

$query = "delete from ow_base_user where 1=1 ".$USER_SKIP_QUERY;
if ($update) { $query.=" and 1=0 ";} //sa nu stearga nimic

$result = mysqli_query($link, $query);


$query = "DELETE FROM ow_base_question_data WHERE userid NOT IN(SELECT id FROM ow_base_user)";
$result = mysqli_query($link, $query);


$query = "SELECT CASE WHEN email_address='' THEN member_name ELSE email_address END AS email_address2, a.* FROM smf_members a ";
if ($update) {
$query .= "where a.ow_id is null ";
} 
$query .= "ORDER BY date_registered";

$result = mysqli_query($link, $query);
  
    printf("*******smf_members: %d rows.\n <br>", mysqli_num_rows($result));
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
global $eol;
// 'config.php';
$chk = inUserDb($link, $row['member_name']);
		if ($chk==-1) 
		{
			echo $row['member_name'].' - insert...';
		
			$query = 'INSERT INTO `ow_base_user` (`email`, `username`, `password`, `joinStamp`, `activityStamp`, `accountType`, `emailVerify`, `joinIp`) VALUES ';

			$ip = ip2long($row['member_ip']);
			if ($ip == -1 || $ip === FALSE) {
				$ip=0;
			}
			
			$user_name = str_replace('.','_',$row['member_name']);
			
			
			$query.= "( '".$row['email_address2']."', '".$user_name."', '".$row['email_address']."', ".$row['date_registered'].", ".$row['last_login'].", '290365aadde35a97f11207ca7e4279cc', 1, ".$ip.")";

			$result = mysqli_query($link, $query);

     		if ($result) { echo "ok"; }
	    	else  echo "error ".$result.$query;
	        echo $eol;
						
			$id = mysqli_insert_id($link);
			

			
			if ($id!=0) {
   			  upd($link, 'smf_members', 'id_member' , $row['id_member'], $id);
			
			echo "real_name...";
			$q2 = "INSERT INTO `ow_base_question_data` (questionName, userID, textValue, intValue, dateValue) VALUES";
			$q2.= "( 'realname', ".$id.", '".$row['real_name']."', 0, NULL)";
			//echo $q2.$eol;
			$result = mysqli_query($link, $q2);
     		if (!$result) { echo "error ".mysqli_error($link).$eol;};
			

			echo "sex...";
			$q2 = "INSERT INTO `ow_base_question_data` (questionName, userID, textValue, intValue, dateValue) VALUES";
			$q2.= "( 'sex', ".$id.", '',".$row['gender'].", NULL)";
			//echo $eol.$q2.$eol;
			$result = mysqli_query($link, $q2);
     		if (!$result) { echo "error ".mysqli_error($link).$eol;};
			
			echo "birthday...";
			$q2 = "INSERT INTO `ow_base_question_data` (questionName, userID, textValue, intValue, dateValue) VALUES";
			$q2.= "( 'birthday', ".$id.", '', 0,'".$row['birthdate']."')";
					//	echo $eol.$q2.$eol;
			$result = mysqli_query($link, $q2);
     		if (!$result) { echo "error ".mysqli_error($link).$eol;};
			}
			
		}
		else
		{
		    echo $row['member_name'].' - UPDATE...';
    		$query = "UPDATE `ow_base_user` SET `joinStamp` = ".$row['date_registered'].", `activityStamp` = ".$row['last_login']." WHERE `username` = '".$row['member_name']."'";
			 $result = mysqli_query($link, $query);
     		if ($result) { echo "ok updating ".$row['member_name']."<br>"; }
	    	else  echo "error ".$result;
			 
			 upd($link, 'smf_members', 'id_member' , $row['id_member'], $chk);
		}	

		//echo $query;
        echo $eol;
	
}


function inUserDb ($link, $username)
{//return -1 sau id daca exista
	if ($username=="mihaelab777")  $username="MihaSan";
	$query = "SELECT id FROM `ow_base_user` WHERE `username` = '".$username."'";
//	echo $query.$eol;
	$result = mysqli_query($link, $query);
	$row=mysqli_fetch_array($result);
	return isset($row['id'])?$row['id']:-1;
}
