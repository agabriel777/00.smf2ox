<?php

function import_sections ($link) //from categories
{
  global $eol;
  
  $query = "DELETE FROM ow_forum_section";
  $result = mysqli_query($link, $query);
  
  
  $qSel = "select * from smf_categories order by id_cat";

  $result = mysqli_query($link, $qSel);
  echo $eol.$eol;
  printf("*******smf_categories: %d rows.\n <br>", mysqli_num_rows($result));
  echo $eol;
    $id=0;
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

	$q = "select ow_id from smf_categories where id_cat=".$old_id;
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	return $row['ow_id'];
}


function import_groups ($link) //from boards
{
  global $eol;
  
  $query = "DELETE FROM `ow_forum_group`";
  $result = mysqli_query($link, $query);
  

  $qsmf = "select * from smf_boards order by id_board";

  $result = mysqli_query($link, $qsmf);
  echo $eol.$eol;
  printf("*******smf_boards: %d rows.\n <br>", mysqli_num_rows($result));
  echo $eol;
    $id=0;
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




function get_group_id($link, $old_id) {

	$q = "select ow_id from smf_boards where id_cat=".$old_id;
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	return $row['ow_id'];
}


function get_user_id($link, $old_id) {

	$q = "select ow_id from smf_members where id=".$old_id;
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	return $row['ow_id'];
}

function import_topics ($link) //from topics
{
  global $eol;
  
  $query = "DELETE FROM `ow_forum_topic`";
  $result = mysqli_query($link, $query);
  

  $qsmf = "select * from smf_topics order by id_topic";

  $result = mysqli_query($link, $qsmf);
  echo $eol.$eol;
  printf("*******smf_topics: %d rows.\n <br>", mysqli_num_rows($result));
  echo $eol;
    $id=0;
	while($row = mysqli_fetch_array($result))
  {
     $qIns = "INSERT INTO ow_forum_topic (id, groupId, userId, title, locked, sticky, temp, viewCount, lastPostId) VALUES ";
	 $qIns.= "(".$id.", '".get_group_id($link, $row['id_board'])."', '".get_user_id($link, $row['id_member_started'])."', '"."descriere"."', ".$row['locked'].$row['is_sticky'].", NULL, ".$row[num_views].", ".$row[id_last_msg].")";
	 
	 ins($link, $qIns);
	 upd($link, 'smf_topics', 'id_topic', $row['id_topic'], $id);
	 $id++;
  }
  mysqli_free_result($result);
}



?>