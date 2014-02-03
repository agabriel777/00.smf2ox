<?php

function ins($link, $query)
{
global $eol;
    echo $query.$eol;
    $result = mysqli_query($link, $query);
  	if (!$result) {
	   echo "E!: ".mysqli_error($link).$eol.$eol;
	}
	return $result;
}


function upd($link, $table_name, $id_name, $old_id, $new_id)
{
global $eol;
$query = "update `".$table_name."` set ow_id=".$new_id." where `".$id_name."` = ".$old_id;
    //echo $query.$eol;
    $result = mysqli_query($link, $query);
  	if (!$result) {
	   echo "E!: ".mysqli_error($link).$eol.$eol;
	}
	return $result;
}


?>