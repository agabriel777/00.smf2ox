<?php

function import_sections ($link,$update=false) //from categories
{
  global $eol;
  
  if (!$update) { 
     $query = "TRUNCATE TABLE ow_forum_section";
     ins($link, $query); 
  }
  
  $qSel = "select * from smf_categories ";
  if ($update) { $qSel .= "where ow_id is null "; }
  $qSel .= "order by id_cat";

  $result = mysqli_query($link, $qSel);
  wlog(sprintf("*******smf_categories: %d rows.\n", mysqli_num_rows($result)),true);
    $id=1;
	while($row = mysqli_fetch_array($result))
  {
     $qIns = "INSERT INTO ow_forum_section (name, `order`) VALUES ";
	 $qIns.= "('".$row['name']."', ".$row['cat_order'].")";
	 
	 $id_ins = ins($link, $qIns);
	 upd($link, 'smf_categories', 'id_cat', $row['id_cat'], $id_ins);
	  $id++;
	  if (! ($id % 10)) echo "$id..." ;
	  if (! ($id % 300)) echo "<br>";
  }
  
  //manual add
  if (!$update) {
    ins($link, "INSERT INTO `ow_forum_section` (`id`, `name`, `order`, `entity`, `isHidden`) VALUES (6, 'Groups', 5, 'groups', 1)");
  }
  wlog("Sections Done</br>");
  mysqli_free_result($result);
}

function get_section_id($link, $old_id) {

	$q = "select ow_id from smf_categories where id_cat=".$old_id;
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	return $row['ow_id'];
}


function import_groups ($link,$update=false) //from boards
{
  global $eol;
  
  
  if (!$update) { 
	$query = "TRUNCATE TABLE `ow_forum_group`";
	ins($link, $query); 
  }

  $qsmf = "select * from smf_boards ";
  if ($update) { $qsmf .= "where ow_id is null "; }
  $qsmf .= "order by id_board";

  
  $result = mysqli_query($link, $qsmf);
  wlog(sprintf("*******smf_boards: %d rows.\n", mysqli_num_rows($result)),true);
    $id=1;
	while($row = mysqli_fetch_array($result))
  {
     $isPrivate = "0";
	 $roles = "NULL";
	 
	 if ($row['name'] == "Sfatul Batranilor") {
	   $isPrivate = "1";
	   $roles = '\'["31"]\'';
	 }
        
     $qIns = "INSERT INTO ow_forum_group (sectionId, name, description, `order`, entityId, isPrivate, roles) VALUES ";
	 $qIns.= "('".get_section_id($link, $row['id_cat'])."', '".mysqli_real_escape_string($link,$row['name'])."', '".mysqli_real_escape_string($link,$row['description'])."', ".$row['board_order'].", NULL, ".$isPrivate.", ".$roles.")";
	 
	 $id_ins = ins($link, $qIns);
	 upd($link, 'smf_boards', 'id_board', $row['id_board'], $id_ins);
	 $id++;
	 if (! ($id % 10)) echo "$id..." ;
	 if (! ($id % 1000)) echo "<br>";
  }
  
    if (!$update) {
	  ins($link, "INSERT INTO `ow_forum_group` (`id`, `sectionId`, `name`, `description`, `order`, `entityId`, `isPrivate`, `roles`) VALUES (21, 6, 'PES', 'Pro Evolution Soccer', 1, 1, NULL, NULL);");
	  
	  ins($link, "INSERT INTO `ow_forum_group` (`id`, `sectionId`, `name`, `description`, `order`, `entityId`, `isPrivate`, `roles`) VALUES (23, 6, 'Tenis', 'Campionatul de tenis vaspun.eu', 2, 3, NULL, NULL);");
	  

	  ins($link, "INSERT INTO `ow_forum_group` (`id`, `sectionId`, `name`, `description`, `order`, `entityId`, `isPrivate`, `roles`) VALUES 
	  (24, 6, 'Le sposine', 'Maritisuri<span class=\"Apple-tab-span\" style=\"white-space:pre\"></span>', 3, 4, NULL, NULL)");
    }
	
  wlog("Groups Done</br>");
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
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	if (empty($row['ow_id'])) { wlog("E! id=".$old_id."not found"); return (-1);}
	else return $row['ow_id'];
}


function get_topic_descr($link, $smf_topic_id) {
global $eol;

	$q = "SELECT `subject`, MIN(m.id_msg) FROM smf_messages m  WHERE id_topic=".$smf_topic_id;
	$result = mysqli_query($link, $q);
	$row = mysqli_fetch_array($result);
	$subject=mysqli_real_escape_string ($link, $row['subject']);
	
	if (empty($subject)) {$subject="***blank***";}

	return $subject;
}


function import_topics ($link,$update=false) //from topics
{
  global $eol;
  
  $query = "TRUNCATE TABLE `ow_forum_topic`";
  if (!$update) { $result = mysqli_query($link, $query); }

  $qsmf = "select * from smf_topics ";
  if ($update) { $qsmf .= "where ow_id is null "; }
  $qsmf .= "order by id_topic";

  
  $result = mysqli_query($link, $qsmf);
  wlog(sprintf("*******smf_topics: %d rows.\n", mysqli_num_rows($result)),true);
    $id=1;
	while($row = mysqli_fetch_array($result))
  {
    $group = get_group_id($link, $row['id_board']);
//	echo $group."->";
	if ($row['id_topic'] == 894) {$group=21;};
	if ($row['id_topic'] == 892) {$group=23;};
	if ($row['id_board'] == 16) {$group=24;};
//    echo $group.$eol;
	
     $qIns = "INSERT INTO ow_forum_topic (groupId, userId, title, locked, sticky, temp, viewCount, lastPostId) VALUES ";
	 $qIns.= "( ".$group.", ".get_user_id($link, $row['id_member_started']).", '".get_topic_descr($link, $row['id_topic'])."', ".$row['locked'].", ".$row['is_sticky'].", 0, ".$row['num_views'].", ".$row['id_last_msg'].")";
	 
	 $id_ins = ins($link, $qIns);
	 upd($link, 'smf_topics', 'id_topic', $row['id_topic'], $id_ins);
	 $id++;
	 if (! ($id % 10)) echo "$id..." ;
	 if (! ($id % 300)) echo "<br>";
  }
    wlog("Topics Done",true);
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


function import_posts ($link,$update=false) //from topics
{
  global $eol;

  $query = "TRUNCATE TABLE `ow_forum_edit_post`";
  if (!$update) { $result = mysqli_query($link, $query); }
  
  $query = "TRUNCATE TABLE `ow_forum_post`";
  if (!$update) { $result = mysqli_query($link, $query); }
 
  $qsmf = "select * from smf_messages ";
  if ($update) { $qsmf .= "where ow_id is null "; }
  $qsmf .= "order by id_msg ";

  
  $result = mysqli_query($link, $qsmf);
  wlog(sprintf("*******smf_messages: %d rows.\n", mysqli_num_rows($result)),true);
	$id=1;
	while($row = mysqli_fetch_array($result))
  {
     $qIns = "INSERT INTO ow_forum_post (topicId, userId, text, createStamp, isFromImport) VALUES ";
	 $userID = get_user_id($link, $row['id_member']);
	 $qIns.= "(".get_topic_id($link, $row['id_topic']).", ".$userID.", '".mysqli_real_escape_string($link,bbcode_to_html($row['body']))."', ".$row['poster_time'].",1)";
	 
	 $id_ins = ins($link, $qIns);
	 upd($link, 'smf_messages', 'id_msg', $row['id_msg'], $id_ins);
	 
	 $id++;
	 if (! ($id % 1000)) echo "$id..." ;
	 if (! ($id % 5000)) echo "<br>".$eol;
	 flush();
	 ob_flush();
  }
      wlog("Posts Done</br>",true);
  mysqli_free_result($result);
  
}

?>