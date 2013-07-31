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

require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)

$getID3 = new getID3; //initialize getID3 engine

//load XML parser for PHP4 or PHP5
include("$absoluteurl"."components/xmlparser/loadparser.php");

$PG_mainbody = NULL; //erase variable which contains episodes data

// Trying to generate the track list on the right
$trackfeed = '<div id="jp_container">
					<div id="jquery_jplayer"></div>
					<p>
						<span class="track-name">&nbsp;</span>
						<span class="jp-current-time"></span> | <span class="jp-duration"></span>
						
					</p>
					<div class="jp-progress progress">
						<div class="jp-seek-bar progress">
							<div class="jp-play-bar progress-bar"></div>
						</div>
					</div>
					
					<div class="btn-holder">
					
						
							<a class="jp-play btn btn-primary" href="#"><i class="icon-play"></i></a>
							<a class="jp-pause btn btn-primary" href="#"><i class="icon-pause"></i></a>
							<a class="jp-stop btn btn-primary" href="#"><i class="icon-stop"></i></a>
						
					
					
						<div class="volume-holder">
							<a class="jp-mute" href="#"><i class="icon-volume-off"></i></a>
							<a class="jp-unmute" href="#"><i class="icon-volume-down"></i></a>
							
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
			
							<a class="jp-volume-max" href="#"><i class="icon-volume-up"></i></a>
						</div>
					</div>
					<ul class="list-group">';

// Open podcast directory
$handle = opendir ($absoluteurl.$upload_dir);
while (($filename = readdir ($handle)) !== false)
{
	if ($filename != '..' && $filename != '.' && $filename != 'index.htm' && $filename != '_vti_cnf' && $filename != '.DS_Store')
	{
		$file_array[$filename] = filemtime ($absoluteurl.$upload_dir.$filename);
	}
}

if (!empty($file_array)) { //if directory is not empty

	# asort ($file_array);
	arsort ($file_array); //the opposite of asort (inverse order)

	$recent_count = 0; //set recents to zero

	foreach ($file_array as $key => $value)	{
		if ($recent_count < $max_recent) { //ir recents are not more than specified in config.php
			$file_multimediale = explode(".",$key); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)
			$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);
			
			

			include("$absoluteurl"."core/viewep.php");
		}
	
	}
	$trackfeed .= '</ul>
		<a href="" class="label label-info"><i class="icon-music"></i> Add to iTunes</a>
		<a href="" class="label label-warning"><i class="icon-rss"></i> RSS feed</a>
		</div>';
} else { 
	$PG_mainbody .= '<div class="topseparator"><p>'.$L_dir.' <b>'.$upload_dir.'</b> '.$L_empty.'</p></div>';
}

?>