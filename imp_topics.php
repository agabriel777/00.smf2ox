<?php
include 'util.php';
include 'config.inc.php';

function import_sections ($link) //from categories
{
  global $eol;
  
  $id=10;  
  $query = "DELETE FROM ow_forum_section WHERE id >".$id;
  $result = mysqli_query($link, $query);
  
  
  $qSel = "select * from smf_categories order by id_cat";

  $result = mysqli_query($link, $qSel);
  echo $eol.$eol;
  printf("*******smf_categories: %d rows.\n <br>", mysqli_num_rows($result));
  echo $eol;
    $id++;
	while($row = mysqli_fetch_array($result))
  {
     $qIns = "INSERT INTO ow_forum_section (id, name, `order`) VALUES ";
	 $qIns.= "(".$id.", '".$row['name']."', ".$row['cat_order'].")";
	 $id++;
	 
	 ins($link, $qIns);
	 upd($link, 'smf_categories', 'id_cat', $row['id_cat'], $id);
  }
  mysqli_free_result($result);
}

function get_section_id($link, $old_id) {
$q = "select new_id from smf_categories where id_cat=".$old_id;
$result = mysqli_query($link, $q);
$row = mysqli_fetch_array($result);
return $row['new_id'];
}
function import_groups ($link) //from boards
{
  global $eol;
  
  $id=100;  
  $query = "DELETE FROM `ow_forum_group` WHERE id >".$id;
  //echo $query.$eol;
  $result = mysqli_query($link, $query);
  

  $qsmf = "select * from smf_boards order by id_board";

  $result = mysqli_query($link, $qsmf);
  echo $eol.$eol;
  printf("*******smf_boards: %d rows.\n <br>", mysqli_num_rows($result));
  echo $eol;
    $id++;
	while($row = mysqli_fetch_array($result))
  {
     $qIns = "INSERT INTO ow_forum_group (id, sectionId, name, description, `order`, entityId, isPrivate, roles) VALUES ";
	 $qIns.= "(".$id.", '".get_section_id($link, $row['id_cat'])."', '".$row['name']."', '".$row['description']."', ".$row['board_order'].", NULL, 0, NULL)";
	 
	 ins($link, $qIns);
	 upd($link, 'smf_boards', 'id_board', $row['id_board'], $id);
	 $id++;
  }
  mysqli_free_result($result);
}



?>