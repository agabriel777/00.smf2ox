<?php
function importUsers ($lines)
{
	foreach ($lines as $line)
	{
		$user = getUserData($line);
		if (!inUserDb($user['username'])) 
		{
			$query = "INSERT INTO `ow_base_user` (`email`, `username`, `password`, `joinStamp`, `activityStamp`, `accountType`, `emailVerify`, `joinIp`) VALUES ";
			$query.= "( $user['email'], $user['username'], $user['email'], $user['joinStamp'], $user['activityStamp'], '290365aadde35a97f11207ca7e4279cc', 1, 0)";
		}
		else
		{
			$query = "UPDATE `ow_base_user` SET `joinStamp` = $user['joinStamp'], `activityStamp` = $user['activityStamp'] WHERE `username` = $user['username']";
		}	
		return runQuery($query);
	}
}

function getUserData ( $line )
{
	$user = array();
	//**COMPLETARE**//
	//smf_members
	return $user;
}

function inUserDb ($username)
{
	$query = "SELECT username FROM `ow_base_user` WHERE `username` = $username";
	// Daca nu e nici o inregistre - fals, daca exista - true.
	return runQuery($query);
}

function importTopics ($lines)
{
	foreach ($lines as $line)
	{
		$topic = getTopicData($line);
		$query = "INSERT INTO `ow_forum_topic` (`id`,`groupId`, `userId`, `title`, `locked`, `sticky`, `temp`, `viewCount`, `lastPostId`) VALUES ";
		$query.= "( $topic['id'], $topic['groupId'], $topic['userId'], $topic['title'], $topic['locked'], $topic['sticky'], $topic['viewcount'], $topic['lastPostId'])";
		return runQuery($query);
	}
}

function getTopicData ( $line )
{
	$topic = array();
	
	//**COMPLETARE**//
	// smf_topics
	// id = id_topic modificat
	// groupId = id_board modificat
	// userId = id_member_started modificat
	// title = postSubject(idFirstMsg) # idFirstMsg modificat
	// locked = locked
	// sticky = is_sticky
	// viewCount = num_views
	// lastPostId = id_last_msg modificat
	
	// Ce se intampla cand user-ul nu exista? - ar trebui sa apara ca fiind guest, sau sa le asignam altcuiva. 
	// In afara de alecu si plikaru si-a mai sters cineva contul? poate reusim sa le reasignam unde trebuie..
	// Am putea face un cross reference cu IP-ul... dar sunt vreo 2500 de mesaje :)
	
	
	
	return $topic;
}


function importPosts ($lines)
{
	foreach ($lines as $line)
	{
		$post = getPostData($line);
		$query = "INSERT INTO `ow_forum_post` (`id`,`topicId`, `userId`, `text`, `createStamp` ) VALUES ";
		$query.= "( $post['id'], $post['topicId'], $post['userId'], $post['text'], $post['createStamp'])";
		return runQuery($query);
	}
}

function getPostData( $line )
{
	$post = array();
	
	//**COMPLETARE**//
	// smf_messages
	// id = id_msg modificat
	// topicId = id_topic modificat
	// text = body modificat
	// createStamp = poster_time
	
	// Ce se intampla cand user-ul nu exista? - ar trebui sa apara ca fiind guest, sau sa le asignam altcuiva.  Aceeasi discutie ca la topics
	
	// Modificari suplimentare la text:
	/* Daca include:
	[img]LINK[/img] --> <span><img style="padding:5px;max-width:100%" src="LINK" /></span>
	[url=LINK][/url] --> <span class="ow_ws_link"><a rel="nofollow" href="LINK target="_blank">LINK</a></span>
	[url=LINK]NAME[/url] --> <span class="ow_ws_link"><a rel="nofollow" href="LINK target="_blank">NAME</a></span>
	[quote][cite]USER:[/cite]text[/quote]
	[quote author=USER link=... date=...]text[/quote]
	&lt;blockquote&gt;&lt;cite&gt;Posted By: USER&lt;/cite&gt;TEXT&lt;/blockquote&gt;
	&lt;img alt=":P" class="smiley" src="/extensions/Smile/IPB2.2/tongue.gif"&gt;&lt;/img&gt;
	[imdb]0486946,zSvwmgWCJ2s[/imdb] --> http://www.imdb.com/title/tt0486946/,  http://www.youtube.com/watch?v=zSvwmgWCJ2s
	[b]TEXT[/b]
	[u]TEXT[/u]
	[i]TEXT[/i]
	[s]TEXT[/s]
	[color]TEXT[/color]
	[sup][/sup]
	[sub][/sub]
	[ulist]
	[*]
	[/ulist]
	[hide][/hide] - 
	[move][/move] - 
	[youtube:COD]YOUTUBE[/youtube] --> YOUTUBE
	
	*/
	
	return $post;
}

?>