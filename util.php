<?php
include 'config.php';


function getLastReadInSmf ($link, $uid, $tid) {
global $eol;

    $q="select * from smf_log where owUid=".$uid." and owTid=".$tid;
    $result = mysqli_query($link, $q);
  	if (!$result) {
	   wlog("E!: ".mysqli_error($link).$eol.$query.$eol,true);
	} 
	else { 
	   $row = mysqli_fetch_array($result);
	   if (!isset($row['owPid'])) { //nu am linie, caut in board
	     $ql="SELECT MAX(m.id_msg) FROM smf_messages m
              INNER JOIN smf_topics t ON t.id_topic=m.id_topic
               INNER JOIN smf_log_mark_read mr ON mr.id_board=t.id_board 
			                                   AND mr.id_member = (SELECT id_member FROM smf_members WHERE ow_id=".$uid.")
               WHERE t.ow_id=".$tid." AND m.id_msg<=mr.id_msg";
		 $result = getValue($link, $ql);
	   } else $result = $row['owPid'];//iau ce rezulta din view
	}
	return $result;
}	


		
function getValue ($link, $query) {
global $eol;

    $result = mysqli_query($link, $query);
  	if (!$result) {
	   wlog("E!: ".mysqli_error($link).$eol.$query.$eol,true);
	} 
	else { 
	   $row = mysqli_fetch_array($result);
       $result = $row[0];
	}
	return $result;
}	
		
		



function ins($link, $query)
{
global $eol;
    
    $result = mysqli_query($link, $query);
  	if (!$result) {
	   wlog("E!: ".mysqli_error($link).$eol.$query.$eol,true);
	   $result = -1;
	} 
	else { $result = mysqli_insert_id($link); }
	return $result;
}


function upd($link, $table_name, $id_name, $old_id, $new_id)
{
global $eol;
$query = "update `".$table_name."` set ow_id=".$new_id." where `".$id_name."` = ".$old_id;
    $result = mysqli_query($link, $query);
  	if (!$result) {
	   wlog("E!: ".mysqli_error($link),true);
	}
	return $result;
}


function wlog($logstr, $onScreen = false) {
   global $LOG_FILE_NAME;
   global $eol;

  $filename = $LOG_FILE_NAME;
  $lfile = fopen($filename,'a+');
  $logstr = PHP_EOL."[".date("Ymd H:i:s").'] '.$logstr;
  fputs($lfile,$logstr);
  fflush($lfile);
  fclose($lfile);
  if ($onScreen) {
    echo $logstr.$eol;
  }
}



//======================== START OF FUNCTION ==========================//
// FUNCTION: bbcode_to_html                                            //
//=====================================================================//
function bbcode_to_html($bbtext){
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
    '[i]' => '<span style="font-style:italic;">','[/i]' => '</span>',
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
	"/\[imdb:(.*?)\](.*?)\[\/imdb:(.*?)\]/i" => "<a href=\"http://www.imdb.com/title/tt$2\">IMDB Link</a><br>", //imdb
	"/\[imdb\](.*?),(.*?)\[\/imdb\]/i" => "<a href=\"http://www.imdb.com/title/tt$1\">IMDB Link</a><br><iframe width=\"420\" height=\"315\" src=\"//www.youtube.com/embed/$2\" frameborder=\"0\" allowfullscreen></iframe>", //imdb
    "/\[quote\]\[cite\] (.*?):\[\/cite\](.*)\[\/quote\]/m" => "<blockquote class=\"ow_quote\"><span class=\"ow_quote_header\"><span class=\"ow_author\">Quote from <b>$1</b></span></span><span class=\"ow_quote_cont_wrap\"><span class=\"ow_quote_cont\">$2</span></span></blockquote>",	
	"/\[quote author=\"(.*?)\"\](.*?)\[\/quote\]/m" => "<blockquote class=\"ow_quote\"><span class=\"ow_quote_header\"><span class=\"ow_author\">Quote from <b>$1</b></span></span><span class=\"ow_quote_cont_wrap\"><span class=\"ow_quote_cont\">$2</span></span></blockquote>",
	"/\[quote author=(.*?) link=(.*?)\](.*?)\[\/quote\]/m" => "<blockquote class=\"ow_quote\"><span class=\"ow_quote_header\"><span class=\"ow_author\">Quote from <b>$1</b></span></span><span class=\"ow_quote_cont_wrap\"><span class=\"ow_quote_cont\">$3</span></span></blockquote>",	
	"/&lt;blockquote&gt;&lt;cite&gt;Posted By: (.*?)&lt;\/cite&gt;(.*?)&lt;\/blockquote&gt;/i" => "<blockquote class=\"ow_quote\"><span class=\"ow_quote_header\"><span class=\"ow_author\">Quote from <b>$1</b></span></span><span class=\"ow_quote_cont_wrap\"><span class=\"ow_quote_cont\">$2</span></span></blockquote>",	
    "/\[url](.*?)\[\/url\]/i" => "<a href=\"http://$1\" title=\"$1\">$1</a>",
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

  foreach($bbextended as $match=>$replacement){
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
//=====================================================================//
//  FUNCTION: bbcode_to_html                                           //
//========================= END OF FUNCTION ===========================//


?>