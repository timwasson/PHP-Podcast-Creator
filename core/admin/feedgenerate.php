<?php
############################################################
# PHP PODCAST CREATOR
#
# Created by Alberto Betella
# Improved by Tim Wasson
# 
# This is Free Software released under the GNU/GPL License.
############################################################

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

### Check if user is logged ###
	if ($amilogged != "true") { exit; }
###

if (isset($_GET['p'])) if ($_GET['p']=="admin") { // if admin is called from the script in a GET variable - security issue

	if (isset($_GET['do']) AND $_GET['do']=="generate" AND !isset($_GET['c'])) { //show "Continue" Button

	$PG_mainbody .= "<h3>$L_generate</h3>";
	$PG_mainbody .= "<p><span class=\"admin_hints\">$L_admin_genfeed</span></p>";

	$PG_mainbody .= '<br /><br />

		<form method="GET" action="index.php">
		<input type="hidden" name="p" value="'.$_GET['p'].'">
		<input type="hidden" name="do" value="'.$_GET['do'].'">
		<input type="hidden" name="c" value="ok">
		<input type="submit" value="'.$L_continue.'" onClick="showNotify(\''.$L_generating.'\');">
		</form>
		';

	#########
  } else{

  	if (isset($_GET['do']) AND $_GET['do']=="generate") {	// do not show following text if included in other php files
  
  		$PG_mainbody .= "<h3>$L_generate</h3>";
  		$PG_mainbody .= "<p>".$L_admin_genfeed."</p>";
  	}
  
  	### DEFINE FEED FILENAME
  	$feedfilename = $absoluteurl.$feed_dir."feed.xml";
  
  
  	##### CONTENT DEPURATION n.1
  	#Depurate feed content according to iTunes specifications
  	#$podcast_description = depurateContent($podcast_description); //description
  	#$copyright = depurateContent($copyright); //copyright notice
  	#$author_name = depurateContent($author_name); // author's name specified in config.php
  	$itunes_category[0] = depurateContent($itunes_category[0]);
  	$itunes_category[1] = depurateContent($itunes_category[1]);
  	$itunes_category[2] = depurateContent($itunes_category[2]);
  
  
  	######
  
  	$head_feed ="<?xml version=\"1.0\" encoding=\"".$feed_encoding."\"?>";
  	$head_feed .= "<rss xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" xml:lang=\"".$feed_language."\" version=\"2.0\">";
  	$head_feed .= "<channel>
  		<title>$podcast_title</title>
  		<link>$url</link>
  		<description>$podcast_description</description>
  		<generator>PHP Podcast Creator</generator>
      <lastBuildDate>".date("r")."</lastBuildDate>
  		<language>$feed_language</language>
  		<copyright>$copyright</copyright>
  		<itunes:image href=\"".$url.$img_dir."itunes_image.jpg\" />
  		<image>
  		<url>".$url.$img_dir."itunes_image.jpg</url>
  		<title>$podcast_title</title>
  		<link>$url</link>
  		</image>
  		<itunes:summary>$podcast_description</itunes:summary>
  		<itunes:subtitle>$podcast_subtitle</itunes:subtitle>
  		<itunes:author>$author_name</itunes:author>
  		<itunes:owner>
  		<itunes:name>$author_name</itunes:name>
  		<itunes:email>$author_email</itunes:email>
  		</itunes:owner>
  		<itunes:explicit>$explicit_podcast</itunes:explicit>
  		";
  
  	### iTunes categories:
  
  	if ($itunes_category[0]!=NULL) { //category 1
  
  		$cat1 =explode(":",$itunes_category[0]);
  		$cat1 = str_replace('&', ' &amp; ', $cat1); // depurate &
  
  
  		$head_feed.= "<itunes:category text=\"$cat1[0]\">
  			";
  
  		if (isset($cat1[1]) AND $cat1[1]!=NULL) { 
  
  			$head_feed.= "<itunes:category text=\"$cat1[1]\" />
  				";
  
  		}
  
  		$head_feed.= "</itunes:category>
  			";
  
  	} //end category 1
  
  
  	if ($itunes_category[1]!=NULL) { //category 2
  
  		$cat2 =explode(":",$itunes_category[1]);
  		$cat2 = str_replace('&', ' &amp; ', $cat2); // depurate &
  
  		$head_feed.= "<itunes:category text=\"$cat2[0]\">
  			";
  
  		if (isset ($cat2[1]) AND $cat2[1]!=NULL) { 
  
  			$head_feed.= "<itunes:category text=\"$cat2[1]\" />
  				";
  
  		}
  
  		$head_feed.= "</itunes:category>
  			";
  
  	} //end category 2
  
  
  	if ($itunes_category[2]!=NULL) { //category 3
  
  		$cat3 =explode(":",$itunes_category[2]);
  		$cat3 = str_replace('&', ' &amp; ', $cat3); // depurate &
  
  		$head_feed.= "<itunes:category text=\"$cat3[0]\">
  			";
  
  		if (isset($cat3[1]) AND $cat3[1]!=NULL) { 
  
  			$head_feed.= "<itunes:category text=\"$cat3[1]\" />
  				";
  
  		}
  
  		$head_feed.= "</itunes:category>
  			";
  
  	} //end category 2
  
  
  
  /* Open a connection */
	$mysqli = new mysqli($server, $db_user, $db_pass, $database);
	
	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	$sql = "SELECT * FROM Episodes ORDER BY `date` DESC";

  $result = $mysqli->query($sql);
  $single_file;
  
  if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      
      $file = $row['filename'];
      $filename = explode(".", $file);
      
      // cut the tags out of the description
      $description = strip_tags($row['description']);
      
      // cut it down to 4000 characters
      $description = substr($description, 0, 4000);
      
      $eplink = $url."#/".urlencode($filename[0])."/";
  							
  		if(!empty($blubrry_tracking)) {
    	  $hlurl = str_replace("http://", "", $url);
    	  $encurl = $blubrry_tracking.$hlurl.$upload_dir.$file;
  		} else {
    	  $encurl = $url.$upload_dir.$file;
  		}
      
      //Output the fucking shit to the fucking thingie.
      $single_file.="<item>
  		  <title>".$row['title']."</title>
  		  <itunes:subtitle>".$row['subtitle']."</itunes:subtitle>
  		  <itunes:summary><![CDATA[ ".$description." ]]></itunes:summary>
  		  <description>".$description."</description>
  		  <link>".$eplink."</link>
  		  <enclosure url=\"".$encurl."\" length=\"".$row['filesize']."\" type=\"".$row['type']."\"/>
  		  <guid>".$eplink."</guid>
  		  ";
  
  		if(!empty($row['length'])) { // display file duration
  		  $single_file.= "<itunes:duration>".$row['length']."</itunes:duration>
  		  	";
  		} 
  
  
  		### AUTHOR
  		if (empty($row['author'])) { //if author field is empty
  
  		  $single_file.= "<author>$author_email ($author_name)</author>
  		  	<itunes:author>$author_name</itunes:author>
  		  	";
  
  		} 
  
  		else { //if author is present
  
  		  $single_file.= "<author>".$row['authoremail']." (".$row['author'].")</author>
  		  	<itunes:author>".$row['author']."</itunes:author>
  		  	";
  		}
  
  
  		## KEYWORDS
  		if (!empty($row['keywords'])) { //if keywords are present
  
  		  $single_file.= "<itunes:keywords>".$row['keywords']."</itunes:keywords>";
  
  		} 
  
  		if ($row['explicit'] != NULL) {
  		  $single_file.= "<itunes:explicit>".$row['explicit']."</itunes:explicit>
  		  	";
  		}
      if (!empty($row['image'])) {
        $single_file .="<itunes:image href=\"".$url."images/".$row['image']."\" />";
      }
  
  		$single_file.= "<pubDate>".date("r", strtotime($row['date']))."</pubDate>
  		  </item>";
      }
    }
    
  	$tail_feed ="</channel></rss>";
  
  	#### Write the RSS feed.
  	$fp1 = fopen("$feedfilename", "w+"); //Apri il file in lettura e svuotalo (w+)
  	fclose($fp1);
  
  	$fp = fopen("$feedfilename", "a+"); //testa xml
  	fwrite($fp, "$head_feed"."$single_file"."$tail_feed"); 
  	fclose($fp);
  	
  	// Write the JSON feed for the front-end
  	//header('Content-Type: application/json');
    $feed = new DOMDocument();
    $feed->load("feed.xml");
    $json = array();
    
    $json['title'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
    $json['description'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
    $json['link'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('link')->item(0)->firstChild->nodeValue;
    $json['bimage'] = $feed->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image')->item(0)->getAttribute('href');
    
    $json['email'] = $feed->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'email')->item(0)->firstChild->nodeValue;
    
    $items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');
    
    $json['item'] = array();
    $i = 0;
    
    
    foreach($items as $item) {
    
      $title = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
      $description = $item->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'summary')->item(0)->firstChild->nodeValue;
      if($item->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image')->item(0)) {
      $image = $item->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image')->item(0)->getAttribute('href');
      } else {
      $image = $json['bimage'];
      }
      
      $pubDate = $item->getElementsByTagName('pubDate')->item(0)->firstChild->nodeValue;
      $guid = $item->getElementsByTagName('guid')->item(0)->firstChild->nodeValue;
      $enclosure = $item->getElementsByTagName('enclosure')->item(0)->getAttribute('url');
      
      $json['item'][$i]['title'] = $title;
      $json['item'][$i]['description'] = str_replace("\n", "<br />",$description);
      $json['item'][$i]['pubdate'] = date('F jS\, Y',strtotime($pubDate));
      $json['item'][$i]['guid'] = $guid;   
      $json['item'][$i]['enclosure'] = $enclosure;
      $json['item'][$i]['image'] = $image;
      $json['item'][$i]['pid'] = $i;
      //$json['item'][$i]['brief'] = substr($description, 0, 50);
      
      $i++;
      
      //echo $enclosure;
        
    }
    /*
    $fp = fopen($absoluteurl."feed.json", "a+"); //testa xml
  	fwrite($fp, json_encode($json)); 
  	fclose($fp);
  	*/
  	
  	$jsonfeed = json_encode($json);
  	/*
  	$jsonfile = $absoluteurl."feed.json";
  	file_put_contents($jsonfile, $jsonfeed);
  	*/
  	$fpjson = fopen($absoluteurl."feed.json", "w+"); //Apri il file in lettura e svuotalo (w+)
  	fclose($fpjson);
  
  	$fpjson2 = fopen($absoluteurl."feed.json", "a+"); //testa xml
  	fwrite($fpjson2, $jsonfeed); 
  	fclose($fpjson2);
  	
  	
    $PG_mainbody .= "JSON created";
  	############
  
  	$PG_mainbody .= "<p><strong>".$L_feedgenerated."</strong></p>";
  
  	if ($recent_episode_in_feed == "0") {
  
  		$PG_mainbody .= "<p><span class=\"badge\">$L_allepisodesindexed</span><p>$L_allepisodesindexed_hint</p>";	
  
  	} else {
  
  		$PG_mainbody .= "<p><span class=\"badge badge-important\">".$recent_count."</span> ".$L_episodes."</i>";
  	}
	}
}
?>
