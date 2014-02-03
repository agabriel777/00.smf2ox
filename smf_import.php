<?php

function update_users($link)
{
	$query = "DELETE FROM ow_base_user WHERE id >6";
	$result = mysqli_query($link, $query);

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
$query = "DELETE FROM ow_base_user WHERE id >6";
$result = mysqli_query($link, $query);

$query = "DELETE FROM ow_base_question_data WHERE id >20";
$result = mysqli_query($link, $query);


$query = "select * from smf_members order by member_name";
=======
	$query = "DELETE FROM ow_base_question_data WHERE id >20";
	$result = mysqli_query($link, $query);
>>>>>>> 787f6757a596f633b7a5e022dd7ff4ca62c8f3af
=======
	$query = "DELETE FROM ow_base_question_data WHERE id >20";
	$result = mysqli_query($link, $query);
>>>>>>> 787f6757a596f633b7a5e022dd7ff4ca62c8f3af
=======
	$query = "DELETE FROM ow_base_question_data WHERE id >20";
	$result = mysqli_query($link, $query);
>>>>>>> 787f6757a596f633b7a5e022dd7ff4ca62c8f3af
=======
	$query = "DELETE FROM ow_base_question_data WHERE id >20";
	$result = mysqli_query($link, $query);
>>>>>>> 787f6757a596f633b7a5e022dd7ff4ca62c8f3af

	$query = "select * from smf_members order by member_name";
	$result = mysqli_query($link, $query);
    
	printf("Select returned %d rows.\n <br>", mysqli_num_rows($result));
	echo $eol;
	while($row = mysqli_fetch_array($result))
	{
		importUser($link, $row);
	}
     /* free result set */
    mysqli_free_result($result);
	return; // nu e necesar
}


function importUser($link, $row)
{
	include 'config.php'; // de ce mai e nevoie de asta?
	if (!inUserDb($link, $row['member_name'])) 
		{
			echo $row['member_name'].' - insert...';
		
			$query = 'INSERT INTO `ow_base_user` (`email`, `username`, `password`, `joinStamp`, `activityStamp`, `accountType`, `emailVerify`, `joinIp`) VALUES ';
			//$ip = str_replace('.', '', $row['member_ip']);
			$ip = ip2long($row['member_ip']);
			//if (empty($ip)) { $ip='0'; echo "blank";}
			if ($ip == -1 || $ip === FALSE) {
				$ip=0;
			}
			
			$query.= "( '".$row['email_address']."', '".$row['member_name']."', '".$row['email_address']."', ".$row['date_registered'].", ".$row['last_login'].", '290365aadde35a97f11207ca7e4279cc', 1, ".$ip.")";

			$result = mysqli_query($link, $query);

     		if ($result) { echo "ok"; }
	    	else  echo "error ".$result;
	        echo $eol;
						
			$id = mysqli_insert_id($link);
			echo "new id=".$id.$eol;

			
			if ($id!=0) {
			
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
