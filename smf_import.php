<?php

function get_smf_user_lines($link)
{

$query = "select * from smf_members order by member_name";

$result = mysqli_query($link, $query);
    printf("Select returned %d rows.\n", mysqli_num_rows($result));
	echo "<br>";
	while($row = mysqli_fetch_array($result))
  {
   importUsers($link, $row);
  //echo $row['member_name'] . " " . $row['id_member'];
  //echo "<br>";

  }
   
    /* free result set */
    mysqli_free_result($result);
return;
}


function importUsers ($link, $row)
{
	//echo "linie=".$row['member_name'];
		if (!inUserDb($link, $row['member_name'])) 
		{
		/*
			$query = "INSERT INTO `ow_base_user` (`email`, `username`, `password`, `joinStamp`, `activityStamp`, `accountType`, `emailVerify`, `joinIp`) VALUES ";
			$query.= "( $user['email'], $user['username'], $user['email'], $user['joinStamp'], $user['activityStamp'], '290365aadde35a97f11207ca7e4279cc', 1, 0)";
			*/
			$query = $row['member_name'].' - insert';
		}
		else
		{
			/*
			$query = "UPDATE `ow_base_user` SET `joinStamp` = $user['joinStamp'], `activityStamp` = $user['activityStamp'] WHERE `username` = $user['username']";
			*/
			$query = $row['member_name'].' - UPDATE';
		}	
		echo $query;
		echo "<br>";
		//return runQuery($query);
	
}


function inUserDb ($link, $username)
{
	$query = "SELECT username FROM `ow_base_user` WHERE `username` = '".$username."'";
	//echo $query;
	// Daca nu e nici o inregistre - fals, daca exista - true.
	$result = mysqli_query($link, $query);
  //  printf("Select returned %d rows.\n", mysqli_num_rows($result));
	if (mysqli_num_rows($result)==1) {
	  return (true);
	}
	else {
	return(false);
	}
	
}


?>