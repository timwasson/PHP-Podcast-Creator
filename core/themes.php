<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# Improved by Tim Wasson
# 
# This is Free Software released under the GNU/GPL License.
############################################################

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

// Load a different template for the admin panel
if (isset($_GET['p']) AND $_GET['p'] == "admin") {
	$tempfile = "admin.htm";
} else {
	$tempfile = "index.htm";
}
if(($theme_file_contents = file_get_contents($theme_path.$tempfile)) === FALSE) {
	echo "<p class=\"error\">".$L_failedopentheme."</p>";
	exit;
}

#########################
# SET PAGE TITLE
$page_title = $podcast_title; 

if (isset($_GET['p'])) {
	if ($_GET['p']=="episode" AND isset($episode_present) AND $episode_present == "yes") {
		$page_title .= " - $text_title";
	}
}

$theme_file_contents = str_replace("-----PG_PAGETITLE-----", $page_title, $theme_file_contents);  

###############################
# LOAD JAVASCRIPTS IN THE HEADER IF PAGE REQUIRES - REPLACES "-----PG_JSLOAD-----" IN THE HEADER OF THE THEME PAGE
if (isset($_GET['p']) and $_GET['p'] == "admin") {
	$loadjavascripts = "<script src=\"/core/admin/custom.js\"></script>";
}
if (isset($_GET['p']) and $_GET['p'] == "admin" and isset($_GET['do']) and $_GET['do'] == "upload" or $_GET['do'] == "edit") {

	$loadjavascripts .= "
	<script src=\"/components/tinymce/js/tinymce/tinymce.min.js\"></script>
	<script type=\"text/javascript\">
	tinymce.init({
	    selector: \".tinymce\",
	    plugins: [
	        \"advlist autolink lists link image charmap print preview anchor\",
	        \"searchreplace visualblocks code fullscreen\",
	        \"insertdatetime media table contextmenu paste\"
	    ],
	    toolbar: \"insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image\"
	});
	</script>
	";
}
$loadjavascripts .= '<script type="text/javascript" src="/components/player/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="/themes/bootstrap/js/player.js"></script>';

$theme_file_contents = str_replace("-----PG_JSLOAD-----", $loadjavascripts, $theme_file_contents); 
# Othere Theme elements replacing
$theme_file_contents = str_replace("-----PG_MAINBODY-----", $PG_mainbody, $theme_file_contents);
$theme_file_contents = str_replace("-----PG_PAGECHARSET-----", $feed_encoding, $theme_file_contents); 
$theme_file_contents = str_replace("-----PG_PODCASTTITLE-----", $podcast_title, $theme_file_contents);
$theme_file_contents = str_replace("-----PG_PODCASTSUBTITLE-----", $podcast_subtitle, $theme_file_contents);
$theme_file_contents = str_replace("-----PG_PODCASTDESC-----", $podcast_description, $theme_file_contents); 
$theme_file_contents = str_replace("-----PG_MENUHOME-----", $L_menu_home, $theme_file_contents); 
$theme_file_contents = str_replace("-----PG_MENUADMIN-----", $L_menu_admin, $theme_file_contents); 
$theme_file_contents = str_replace("-----PG_ADMINMENU-----", $admmenu, $theme_file_contents);
$theme_file_contents = str_replace("-----PG_TRACKLIST-----", $trackfeed, $theme_file_contents); 

#FOOTER
$theme_file_contents = str_replace("-----PG_FOOTER-----", $definefooter, $theme_file_contents);

# META TAGS AND FEED LINK
$metatagstoreplace = '

	<meta http-equiv="content-language" content="'.$scriptlang.'" />
	<meta name="Generator" content="Podcast Generator '.$podcastgen_version.'" />
	<meta name="Author" content="'.depuratecontent($author_name).'" />
	<meta name="Copyright" content="'.depuratecontent($copyright).'" />
	';

if (isset($_GET['p']) and $_GET['p'] == "admin" and isset($_GET['do']) and $_GET['do'] == "itunesimg") { // no cache in itunes image admin page
	$metatagstoreplace .= '<meta http-equiv="expires" content="0" />';
}


# define META KEYWORDS

// on single episode page (permalink), use itunes keywords and episode description as meta tags...
if (isset($_GET['p']) AND $_GET['p']=="episode" AND isset($episode_present) AND $episode_present == "yes") { 
	if ($text_keywordspg != NULL) { // ...if keywords exist
		$metatagstoreplace .= '<meta name="Keywords" content="'.depuratecontent($text_keywordspg).'" />';
	}
	$metatagstoreplace .= '<meta name="Description" content="'.depuratecontent($text_shortdesc).'" />'; // use episode short description
} 
else { // if not permalink page, use podcast general description as meta tag
	$metatagstoreplace .= '<meta name="Description" content="'.depuratecontent($podcast_description).'" />';
}

// on the home page (recent_list.php) use keywords of the most recent episode
if (isset($assignmetakeywords) AND $assignmetakeywords != NULL) { // the variable $assignmetakeywords is assigned in recent_list.php
	$metatagstoreplace .= '<meta name="Keywords" content="'.depuratecontent($assignmetakeywords).'" />';	
}


// general XML feed of the podcast
$metatagstoreplace .= '
<link href="'.$url.$feed_dir.'feed.xml" rel="alternate" type="application/rss+xml" title="'.$podcast_title.' RSS" />'; 

$theme_file_contents = str_replace("-----PG_METATAGS-----", $metatagstoreplace, $theme_file_contents);

# END META TAGS DEFINITION

?>