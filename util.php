<?php

function ins($link, $query)
{
global $eol;
    
    $result = mysqli_query($link, $query);
  	if (!$result) {
	   echo "E!: ".mysqli_error($link).$eol.$eol.$query.$eol;
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
    "/\[url](.*?)\[\/url]/i" => "<a href=\"http://$1\" title=\"$1\">$1</a>",
    "/\[url=(.*?)\](.*?)\[\/url\]/i" => "<a href=\"$1\" title=\"$1\">$2</a>",
    "/\[email=(.*?)\](.*?)\[\/email\]/i" => "<a href=\"mailto:$1\">$2</a>",
    "/\[mail=(.*?)\](.*?)\[\/mail\]/i" => "<a href=\"mailto:$1\">$2</a>",
    "/\[img\]([^[]*)\[\/img\]/i" => "<img src=\"$1\" alt=\" \" />",
    "/\[image\]([^[]*)\[\/image\]/i" => "<img src=\"$1\" alt=\" \" />",
    "/\[image_left\]([^[]*)\[\/image_left\]/i" => "<img src=\"$1\" alt=\" \" class=\"img_left\" />",
    "/\[image_right\]([^[]*)\[\/image_right\]/i" => "<img src=\"$1\" alt=\" \" class=\"img_right\" />",
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
 +	
 +	*/
  
  
  
  
  
  return $bbtext;
}
//=====================================================================//
//  FUNCTION: bbcode_to_html                                           //
//========================= END OF FUNCTION ===========================//


?>