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

$PG_mainbody = NULL; //erase variable which contains episodes data

if (isset($_GET['name']) AND $_GET['name'] != NULL ) {
	$file_multimediale = $_GET['name'];
		
	// Get the date this file was made.
	$value = filemtime("$absoluteurl"."$upload_dir$file_multimediale");
		
	$file_multimediale = str_replace("/", "", $file_multimediale); // Replace / in the filename.. avoid seeing files outside podcastgenerator root directory


	if (file_exists("$absoluteurl"."$upload_dir$file_multimediale")) {

		$episode_present = "yes"; //assign presence to episode (recall in themes.php)

		require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)

		$getID3 = new getID3; //initialize getID3 engine

		//load XML parser for PHP4 or PHP5
		include("$absoluteurl"."components/xmlparser/loadparser.php");

		$file_multimediale = explode(".",$file_multimediale); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)

		$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);

		
		include("$absoluteurl"."core/viewep.php");

	} else { // if file doesn't exist
		$episode_present = "no"; 
	
		$PG_mainbody .= '<div class="topseparator"><p>'.$L_dir.' <b>'.$upload_dir.'</b> '.$L_empty.'</p></div>';
	}
}
?>