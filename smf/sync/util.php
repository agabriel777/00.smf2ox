<?php
include '\..\..\..\smf2ox\config.php';

function markOxWall ($uid, $tid, $pid)
{
    global $SQL_HOST, $SQL_PORT, $SQL_USER,	$SQL_PASS, $SQL_DB, $eol;

    wlog($eol."*****markLastRead*********************".$eol,true);
	
    $link = mysqli_connect($SQL_HOST.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);
    if ($link)
    {
        $ins_id = getValue($link, "SELECT markOxWall($uid,$tid,$pid)");
		wlog("ret_id =  $ins_id",true);
    }

}

function UpdOxWall($smfid)
{
    global $SQL_HOST, $SQL_PORT, $SQL_USER,	$SQL_PASS, $SQL_DB, $eol;

    wlog($eol."*****SMF -> OxWall**********************".$eol);
    $link = mysqli_connect($SQL_HOST.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);
    if ($link)
    {
        $ins_id = getValue($link, "SELECT updOxWall(".$smfid.")");
        if ($ins_id!=0)
        {
            $result = mysqli_query($link, "SELECT text from ow_forum_post where id=".$ins_id);
            if ($result)
            {
                $row = mysqli_fetch_array($result);
				wlog($row['text'],true);
                $text = bbcode_to_html($row['text']);
				//wlog($text,true);
				//wlog(mysqli_real_escape_string($link,$text),true);
                $k = upd_log($link, "UPDATE ow_forum_post set text = '".mysqli_real_escape_string($link,$text)."' where id=".$ins_id);
                wlog("affected rows: ".$k,true);
            }
        }
    }
    return $k;
}

function ThankYou($smfUid, $smfPid)
{
    global $SQL_HOST, $SQL_PORT, $SQL_USER,	$SQL_PASS, $SQL_DB, $eol;

    wlog($eol."*****Thanks**********************".$eol);
    $link = mysqli_connect($SQL_HOST.$SQL_PORT, $SQL_USER , $SQL_PASS , $SQL_DB);
    if ($link)
    {
	// insert in ow_newsfeed_like for score>0
	    $q = "INSERT INTO `ow_newsfeed_like` (`entityType`,`entityId`,`userId`,`timeStamp`)
              SELECT 'forum-post', m.`ow_id`, u.`ow_id`, g.`log_time`
              FROM smf_log_gpbp g
              INNER JOIN smf_messages m ON m.`id_msg` = g.`id_msg`
              INNER JOIN smf_members u ON u.`id_member`=g.`id_member`
              WHERE 1=1 
              AND n.id IS NULL
              AND g.score>0 
			  AND g.id_member = $smfUid 
			  AND g.id_msg = $smfPid");
        $ins_id = ins($link, $q);
	    mysqli_close($link);
    }
	
    return $ins_id;
}

function upd_log($link, $query)
{
    global $eol;
  echo $query;
    $result = mysqli_query($link, $query);
    if (!$result)
    {
        wlog("E!: ".mysqli_error($link).$eol.$query.$eol,true);
        $result = -1;
    }
    else
    {
        $result = mysqli_affected_rows($link);
    }
    return $result;
}


function getValue ($link, $query)
{
    global $eol;

    $result = mysqli_query($link, $query);
    if (!$result)
    {
        wlog("E!: ".mysqli_error($link).$eol.$query.$eol,true);
    }
    else
    {
        $row = mysqli_fetch_array($result);
        $result = $row[0];
    }
    return $result;
}

function wlog($logstr, $onScreen = false)
{
    global $LOG_FILE_NAME;
    global $eol;

    $filename = $LOG_FILE_NAME;
    $lfile = fopen($filename,'a+');
    $logstr = PHP_EOL."[".date("Ymd H:i:s").'] '.$logstr;
    fputs($lfile,$logstr);
    fflush($lfile);
    fclose($lfile);
    if ($onScreen)
    {
        echo $logstr.$eol;
    }
}



function ins($link, $query)
{
    global $eol;

    $result = mysqli_query($link, $query);
    if (!$result)
    {
        wlog("E!: ".mysqli_error($link).$eol.$query.$eol,true);
        $result = -1;
    }
    else
    {
        $result = mysqli_insert_id($link);
    }
    return $result;
}


function upd($link, $table_name, $id_name, $old_id, $new_id)
{
    global $eol;
    $query = "update `".$table_name."` set ow_id=".$new_id." where `".$id_name."` = ".$old_id;
    $result = mysqli_query($link, $query);
    if (!$result)
    {
        wlog("E!: ".mysqli_error($link),true);
    }
    return $result;
}




function bbcode_to_html($bbtext)
{
    $bbtags = array(

                  '[heading1]' => '<h1>','[/heading1]' => '</h1>',
                  '[heading2]' => '<h2>','[/heading2]' => '</h2>',
                  '[heading3]' => '<h3>','[/heading3]' => '</h3>',
                  '[h1]' => '<h1>','[/h1]' => '</h1>',
                  '[h2]' => '<h2>','[/h2]' => '</h2>',
                  '[h3]' => '<h3>','[/h3]' => '</h3>',
                  '[hide]' => '<span>','[/hide]' => '</span>',
                  '[sub]' => '<sub>','[/sub]' => '</sub>',
                  '[sup]' => '<sup>','[/sup]' => '</sup>',
                  '[s]' => '<s>','[/s]' => '</s>',
                  //' <!-- s -->' => '<s>','[/s]' => '</s>',


                  '[paragraph]' => '<p>','[/paragraph]' => '</p>',
                  '[para]' => '<p>','[/para]' => '</p>',
                  '[p]' => '<p>','[/p]' => '</p>',
                  '[left]' => '<p style="text-align:left;">','[/left]' => '</p>',
                  '[right]' => '<p style="text-align:right;">','[/right]' => '</p>',
                  '[center]' => '<p style="text-align:center;">','[/center]' => '</p>',
                  '[justify]' => '<p style="text-align:justify;">','[/justify]' => '</p>',

                  '[bold]' => '<span style="font-weight:bold;">','[/bold]' => '</span>',
                  '[italic]' => '<span style="font-weight:bold;">','[/italic]' => '</span>',
                  '[underline]' => '<span style="text-decoration:underline;">','[/underline]' => '</span>',
                  '[b]' => '<span style="font-weight:bold;">','[/b]' => '</span>',
                  '[i]' => '<span style="font-weight:bold;">','[/i]' => '</span>',
                  '[u]' => '<span style="text-decoration:underline;">','[/u]' => '</span>',
                  '[break]' => '<br>',
                  '[br]' => '<br>',
                  '[newline]' => '<br>',
                  '[nl]' => '<br>',

                  '[unordered_list]' => '<ul>','[/unordered_list]' => '</ul>',
                  '[ulist]' => '<ul>','[/ul]' => '</ul>',
                  '[list]' => '<ul>','[/list]' => '</ul>',
                  '[ul]' => '<ul>','[/ul]' => '</ul>',

                  '[ordered_list]' => '<ol>','[/ordered_list]' => '</ol>',
                  '[ol]' => '<ol>','[/ol]' => '</ol>',
                  '[list_item]' => '<li>','[/list_item]' => '</li>',
                  '[li]' => '<li>','[/li]' => '</li>',

                  '[*]' => '<li>','[/*]' => '</li>',
                  '[code]' => '<code>','[/code]' => '</code>',
                  '[preformatted]' => '<pre>','[/preformatted]' => '</pre>',
                  '[pre]' => '<pre>','[/pre]' => '</pre>',
              );

    $bbtext = str_ireplace(array_keys($bbtags), array_values($bbtags), $bbtext);

    $bbextended = array(

                      "/\[url=https?:\/\/www\.youtube\.com\/watch\?v=(.*?)\](.*?)\[\/url\]/i" => "<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>", // youtube
                      "/\https:\/\/www\.youtube\.com\/watch\?v=(.*?)\](.*?)/i" => "<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>", //youtube
                      "/\[youtube:(.*?)\]http:\/\/www\.youtube\.com\/watch\?v=(.*?)\[\/youtube(.*?)\]/i" => "<iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$2\" frameborder=\"0\" allowfullscreen></iframe>", //youtube
                      "/\[imdb:(.*?)\](.*?)\[\/imdb:(.*?)\]/i" => "<a href=\"http://www.imdb.com/title/tt$2\ target=\"_blank\">IMDB Link</a><br>", //imdb
                      "/\[imdb\](.*?),(.*?)\[\/imdb\]/i" => "<a href=\"http://www.imdb.com/title/tt$1\" target=\"_blank\">IMDB Link</a><br><iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$2\" frameborder=\"0\" allowfullscreen></iframe>", //imdb
                      "/\[quote\]\[cite\] (.*?):\[\/cite\](.*)\[\/quote\]/m" => "<blockquote class=\"ow_quote\"><span class=\"ow_quote_header\"><span class=\"ow_author\">Quote from <b>$1</b></span></span><span class=\"ow_quote_cont_wrap\"><span class=\"ow_quote_cont\">$2</span></span></blockquote>",
                      "/\[quote author=\"(.*?)\"\](.*?)\[\/quote\]/m" => "<blockquote class=\"ow_quote\"><span class=\"ow_quote_header\"><span class=\"ow_author\">Quote from <b>$1</b></span></span><span class=\"ow_quote_cont_wrap\"><span class=\"ow_quote_cont\">$2</span></span></blockquote>",
                      "/\[quote author=(.*?) link=(.*?)\](.*?)\[\/quote\]/m" => "<blockquote class=\"ow_quote\"><span class=\"ow_quote_header\"><span class=\"ow_author\">Quote from <b>$1</b></span></span><span class=\"ow_quote_cont_wrap\"><span class=\"ow_quote_cont\">$3</span></span></blockquote>",
                      "/&lt;blockquote&gt;&lt;cite&gt;Posted By: (.*?)&lt;\/cite&gt;(.*?)&lt;\/blockquote&gt;/i" => "<blockquote class=\"ow_quote\"><span class=\"ow_quote_header\"><span class=\"ow_author\">Quote from <b>$1</b></span></span><span class=\"ow_quote_cont_wrap\"><span class=\"ow_quote_cont\">$2</span></span></blockquote>",
                      "/\[url](.*?)\[\/url\]/i" => "<a href=\"http://$1\" title=\"$1\" target=\"_blank\">$1</a>",
                      "/\[url=(.*?)\](.*?)\[\/url\]/i" => "<a href=\"$1\" title=\"$1\">$2</a>",
                      //"/\[color=white\](.*?)\[\/color\]/i" => "<span style=\"color:black\">$1</span>",
                      "/\[color=(.*?)\](.*?)\[\/color\]/i" => "<span style=\"color:$1\">$2</span>",
                      "/\[email=(.*?)\](.*?)\[\/email\]/i" => "<a href=\"mailto:$1\">$2</a>",
                      "/\[mail=(.*?)\](.*?)\[\/mail\]/i" => "<a href=\"mailto:$1\">$2</a>",
                      "/&lt;img alt=\"(.*?)\" class=\"smiley\" src=\"(.*?)\"&gt;&lt;\/img&gt;/i" => " $1 ",
                      "/\[img\]([^[]*)\[\/img\]/i" => "<img src=\"$1\" alt=\" \" />",
                      "/\[image\]([^[]*)\[\/image\]/i" => "<img src=\"$1\" alt=\" \" />",
                      "/\[image_left\]([^[]*)\[\/image_left\]/i" => "<img src=\"$1\" alt=\" \" class=\"img_left\" />",
                      "/\[image_right\]([^[]*)\[\/image_right\]/i" => "<img src=\"$1\" alt=\" \" class=\"img_right\" />",
                      '/[\r\n]/i' => '<br />',
                  );

    foreach($bbextended as $match=>$replacement)
    {
        $bbtext = preg_replace($match, $replacement, $bbtext);
    }

    // Modificari suplimentare la text:
    /* Daca include:
    +	[img]LINK[/img] --> <span><img style="padding:5px;max-width:100%" src="LINK" /></span>
    +	[url=LINK][/url] --> <span class="ow_ws_link"><a rel="nofollow" href="LINK target="_blank">LINK</a></span>
    +	[url=LINK]NAME[/url] --> <span class="ow_ws_link"><a rel="nofollow" href="LINK target="_blank">NAME</a></span>
    +	[quote][cite]USER:[/cite]text[/quote]
    +	[quote author=USER link=... date=...]text[/quote]
    +  [quote author="USER" link=... date=...]text[/quote]
    +	&lt;blockquote&gt;&lt;cite&gt;Posted By: USER&lt;/cite&gt;TEXT&lt;/blockquote&gt;
    +	&lt;img alt=":P" class="smiley" src="/extensions/Smile/IPB2.2/tongue.gif"&gt;&lt;/img&gt;
    +	[imdb]0486946,zSvwmgWCJ2s[/imdb] --> http://www.imdb.com/title/tt0486946/,  http://www.youtube.com/watch?v=zSvwmgWCJ2s
    +	[b]TEXT[/b]
    +	[u]TEXT[/u]
    +	[i]TEXT[/i]
    +	[s]TEXT[/s]
    +	[color]TEXT[/color]
    +	[sup][/sup]
    +	[sub][/sub]
    +	[ulist]
    +	[*]
    +	[/ulist]
    +	[hide][/hide] -
    +	[move][/move] -
    +	[youtube:COD]YOUTUBE[/youtube] --> YOUTUBE
    +	[url=http://www.youtube.com/watch?v=QrxPuk0JefA#]Camera falls from airplane and lands in pig pen--MUST WATCH END!![/url]
    +  <iframe width="420" height="315" src="//www.youtube.com/embed/QrxPuk0JefA" frameborder="0" allowfullscreen></iframe>

    +	*/





    return $bbtext;
}



?>