<?php

function update_users($link)
{

$query = "DELETE FROM ow_base_user WHERE id >6";
$result = mysqli_query($link, $query);


$query = "select * from smf_members order by member_name";

$result = mysqli_query($link, $query);
    printf("Select returned %d rows.\n", mysqli_num_rows($result));
	echo "<br>";
	while($row = mysqli_fetch_array($result))
  {
   importUser($link, $row);
  }
   
    /* free result set */
    mysqli_free_result($result);
return;
}


function importUser($link, $row)
{
		if (!inUserDb($link, $row['member_name'])) 
		{
			echo $row['member_name'].' - insert...';
		
			$query = 'INSERT INTO `ow_base_user` (`email`, `username`, `password`, `joinStamp`, `activityStamp`, `accountType`, `emailVerify`, `joinIp`) VALUES ';
			$ip = str_replace('.', '', $row['member_ip']);
			
			if (empty($ip)) { $ip='0'; echo "blank";}
			
			$query.= "( '".$row['email_address']."', '".$row['member_name']."', '".$row['email_address']."', ".$row['date_registered'].", ".$row['last_login'].", '290365aadde35a97f11207ca7e4279cc', 1, ".$ip.")";

		}
		else
		{
		    echo $row['member_name'].' - UPDATE...';
    		$query = "UPDATE `ow_base_user` SET `joinStamp` = ".$row['date_registered'].", `activityStamp` = ".$row['last_login']." WHERE `username` = '".$row['member_name']."'";
		}	

		//echo $query;
		$result = mysqli_query($link, $query);

		if ($result) { echo "ok"; }
		else  echo "error ".$result;
        echo "<br>";
	
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


?>