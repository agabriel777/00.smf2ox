<?php

function import_sections ($link) //from categories
{
  global $eol;
  
  $query = "TRUNCATE TABLE ow_forum_section";
  $result = mysqli_query($link, $query);
  
  
  $qSel = "select * from smf_categories order by id_cat";

  $result = mysqli_query($link, $qSel);
  echo $eol.$eol;
  printf("*******smf_categories: %d rows.\n <br>", mysqli_num_rows($result));
  echo $eol;
    $id=1;
	while($row = mysqli_fetch_array($result))
  {
     $qIns = "INSERT INTO ow_forum_section (id, name, `order`) VALUES ";
	 $qIns.= "(".$id.", '".$row['name']."', ".$row['cat_order'].")";
	 
	 
	 ins($link, $qIns);
	 upd($link, 'smf_categories', 'id_cat', $row['id_cat'], $id);
	 $id++;
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
  
  $query = "TRUNCATE TABLE `ow_forum_group`";
  $result = mysqli_query($link, $query);
  

  $qsmf = "select * from smf_boards order by id_board";

  $result = mysqli_query($link, $qsmf);
  echo $eol.$eol;
  printf("*******smf_boards: %d rows.\n <br>", mysqli_num_rows($result));
  echo $eol;
    $id=1;
	while($row = mysqli_fetch_array($result))
  {
     $qIns = "INSERT INTO ow_forum_group (id, sectionId, name, description, `order`, entityId, isPrivate, roles) VALUES ";
	 $qIns.= "(".$id.", '".get_section_id($link, $row['id_cat'])."', '".mysql_real_escape_string($row['name'])."', '".mysql_real_escape_string($row['description'])."', ".$row['board_order'].", NULL, 0, NULL)";
	 
	 ins($link, $qIns);
	 upd($link, 'smf_boards', 'id_board', $row['id_board'], $id);
	 $id++;
  }
  mysqli_free_result($result);
}




function get_group_id($link, $old_id) {

	$q = "select ow_id from smf_boards where id_board=".$old_id;
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	return $row['ow_id'];
}


function get_user_id($link, $old_id) {

	$q = "select ow_id from smf_members where id_member=".$old_id;
	//echo $q;
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	if (empty($row['ow_id'])) { echo $old_id; return (-1);}
	else return $row['ow_id'];
}


function get_topic_descr($link, $smf_topic_id) {

	$q = "select max(subject) subject from smf_messages where id_topic=".$smf_topic_id;
	//echo $q;
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	if (empty($row['subject'])) {return "blank";}
	else return mysql_real_escape_string ($row['subject']);
}


function import_topics ($link) //from topics
{
  global $eol;
  
  $query = "TRUNCATE TABLE `ow_forum_topic`";
  $result = mysqli_query($link, $query);
  

  $qsmf = "select * from smf_topics order by id_topic";

  $result = mysqli_query($link, $qsmf);
  echo $eol.$eol;
  printf("*******smf_topics: %d rows.\n <br>", mysqli_num_rows($result));
  echo $eol;
    $id=1;
	while($row = mysqli_fetch_array($result))
  {
     $qIns = "INSERT INTO ow_forum_topic (id, groupId, userId, title, locked, sticky, temp, viewCount, lastPostId) VALUES ";
	 $qIns.= "(".$id.", ".get_group_id($link, $row['id_board']).", ".get_user_id($link, $row['id_member_started']).", '".get_topic_descr($link, $row['id_topic'])."', ".$row['locked'].", ".$row['is_sticky'].", 0, ".$row['num_views'].", ".$row['id_last_msg'].")";
	 
	 ins($link, $qIns);
	 upd($link, 'smf_topics', 'id_topic', $row['id_topic'], $id);
	 $id++;
  }
  mysqli_free_result($result);
}



function get_topic_id($link, $old_id) {

	$q = "select ow_id from smf_topics where id_topic=".$old_id;
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	return $row['ow_id'];
}


function stripBBCode($text_to_search) {
 $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
 $replace = '';
 return preg_replace($pattern, $replace, $text_to_search);
}


function import_posts ($link) //from topics
{
  global $eol;
  
  $query = "TRUNCATE TABLE `ow_forum_post`";
  $result = mysqli_query($link, $query);
  
 
  $qsmf = "select * from smf_messages order by id_msg LIMIT 1000000";

  $result = mysqli_query($link, $qsmf);
  echo $eol.$eol;
  printf("*******smf_messages: %d rows.\n <br>", mysqli_num_rows($result));
  echo $eol;
    $id=1;
	while($row = mysqli_fetch_array($result))
  {
  
 // echo "caut...".$row['id_topic'];
     $qIns = "INSERT INTO ow_forum_post (id, topicId, userId, text, createStamp) VALUES ";
	 $userID = get_user_id($link, $row['id_member']);
	 $qIns.= "(".$id.", ".get_topic_id($link, $row['id_topic']).", ".$userID.", '".mysql_real_escape_string(bbcode_to_html($row['body']))."', ".$row['poster_time'].")";
	 
	 ins($link, $qIns);
	 upd($link, 'smf_messages', 'id_msg', $row['id_msg'], $id);
	 
 
	 
	 $id++;
  }
  mysqli_free_result($result);
  
}




?>