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
$trackfeed = '<div id="jquery_jplayer"></div>
	
				<div id="jp_container" data-spy="affix" data-offset-top="0">
					<p>
						<span class="track-name">&nbsp;</span>
						<span class="jp-current-time"></span> | <span class="jp-duration"></span>
						
					</p>
					<div class="jp-progress progress">
						<div class="jp-seek-bar progress">
							<div class="jp-play-bar bar"></div>
						</div>
					</div>
					
					<div class="btn-holder">
					
						<div class="btn-group">
							<a class="jp-play btn" href="#"><i class="icon-play"></i></a>
							<a class="jp-pause btn" href="#"><i class="icon-pause"></i></a>
							<a class="jp-stop btn" href="#"><i class="icon-stop"></i></a>
						</div>
					
					
						<div class="volume-holder">
							<a class="jp-mute" href="#"><i class="icon-volume-off"></i></a>
							<a class="jp-unmute" href="#"><i class="icon-volume-down"></i></a>
							
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
			
							<a class="jp-volume-max" href="#"><i class="icon-volume-up"></i></a>
						</div>
					</div>
					<ul class="nav nav-list">
						<li class="nav-header">Select a track</li>';

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
			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]
				$podcast_filetype = $fileData[0];
				if ($file_multimediale[1]=="$podcast_filetype") { // if the extension is the same as specified in config.php
					$file_size = filesize("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype");
					$file_size = $file_size/1048576;
					$file_size = round($file_size, 2);
					############
					$filedescr = "$absoluteurl"."$upload_dir$file_multimediale[0].xml"; //database file

					if (file_exists("$filedescr")) { //if database file exists 
						//$file_contents=NULL; 

						# READ the XML database file and parse the fields
						include("$absoluteurl"."core/readXMLdb.php");
						
						# File details (duration, bitrate, etc...)
						$ThisFileInfo = $getID3->analyze("$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype"); //read file tags

						$file_duration = @$ThisFileInfo['playtime_string'];
						
						$episode_date = date ($dateformat, $value);
						
						if($file_duration!=NULL) { // display file duration
							$episode_details = "$L_duration ";
							$episode_details .= @$ThisFileInfo['playtime_string'];
							$episode_details .= " $L_episode_minutes - $L_episode_filetype ";
							$episode_details .= @$ThisFileInfo['fileformat'];

							if($podcast_filetype=="mp3") { //if mp3 show bitrate &co
								$episode_details .= " - $L_bitrate ";
								$episode_details .= @$ThisFileInfo['bitrate']/1000;
								$episode_details .= " $L_episode_kbps - $L_frequency ";
								$episode_details .= @$ThisFileInfo['audio']['sample_rate'] ;
								$episode_details .= " $L_episode_hz";
								$episode_date .= " <i class=\"icon-info-sign\" rel=\"tooltip\" data-toggle=\"tooltip\" title=\"".$episode_details."\"></i> ";
							} 
							$episode_details .= " - File Size: ".$file_size." ".$L_bytes;
						} 
						
						#Define episode headline
						if (isset($isvideo) AND $isvideo == "yes") {
							//$episode_date .= "<a href=\"".$url.$upload_dir."$file_multimediale[0].$podcast_filetype\" title=\"$L_viewvideo\"><span class=\"episode_download\">$L_view</span></a><span class=\"episode_download\"> - </span>";
							$isvideo = "no"; //so variable is assigned on every cicle
						}
						
						### Here the output code for the episode is created
						# Fields Legend (parsed from XML):
						# $text_title = episode title
						# $text_shortdesc = short description
						# $text_longdesc = long description
						# $text_imgpg = image (url) associated to episode
						# $text_category1, $text_category2, $text_category3 = categories
						# $text_keywordspg = keywords
						# $text_explicitpg = explicit podcast (yes or no)
						# $text_authornamepg = author's name
						# $text_authoremailpg = author's email

						$PG_mainbody .= '<article>';

						$PG_mainbody .= '<h3><a href="?p=episode&amp;name='.$file_multimediale[0].'.'.$podcast_filetype.'">'.$text_title.'</a>';

						if ($podcast_filetype=="mpg" OR $podcast_filetype=="mpeg" OR $podcast_filetype=="mov" OR $podcast_filetype=="mp4" OR $podcast_filetype=="wmv" OR $podcast_filetype=="3gp" OR $podcast_filetype=="mp4" OR $podcast_filetype=="avi" OR $podcast_filetype=="flv" OR $podcast_filetype=="m4v") { // if it is a video

							$episode_date .= '&nbsp;<i class="icon-film"></i> ';
							$isvideo = "yes"; 
						}
						$episode_date .= "<a href=\"".$url.$upload_dir.$file_multimediale[0].".".$podcast_filetype."\" title=\"$L_donloadthis\"><i class=\"icon-download\"></i></a>";
						$PG_mainbody .= '</h3><p>'.$episode_date.'</p>';
							if(isset($text_imgpg) AND $text_imgpg!=NULL AND file_exists("$img_dir$text_imgpg")) {
								$PG_mainbody .= "<img src=\"$img_dir$text_imgpg\" class=\"episode_image\" alt=\"$text_title\" />";
							}

							if(isset($text_longdesc) AND $text_longdesc!=NULL ) { // if is set long description
								$PG_mainbody .= $text_longdesc;
							} else {
								$PG_mainbody .= $text_shortdesc;	
							}

							if($podcast_filetype=="mp3") {
								//Update the tracks on the right-hand side.
								$trackfeed .= "<li><a href=\"".$url.$upload_dir.$key."\" rel=\"tooltip\" title=\"".$filepubdate."\" class=\"track\">".$text_title."</a></li>\r";
								//$PG_mainbody .= $trackfeed;

							} else {
								$PG_mainbody .= '
								<div id="jp_container_1" class="jp-video">
									<div class="jp-type-single">
										<div id="jquery_jplayer_'.$recent_count.'" class="jp-jplayer"></div>
										<div class="jp-gui">
											<div class="jp-interface">
												
												<div class="jp-title"><strong>'.$text_title.'</strong><small> <span class="jp-current-time"></span> | <span class="jp-duration"></span></small></div>
												
												<div class="jp-progress">
													<div class="jp-seek-bar progress">
														<div class="jp-play-bar bar"></div>
													</div>
												</div>
												
												<div class="jp-controls-holder">
													
													<div class="btn-group">
														<a class="jp-play btn" href="#"><i class="icon-play"></i></a>
														<a class="jp-pause btn" href="#"><i class="icon-pause"></i></a>
														<a class="jp-stop btn" href="#"><i class="icon-stop"></i></a>
													</div>
						
													<div class="btn-group">
														<a class="jp-full-screen btn" tabindex="1" title="full screen"><i class="icon-resize-full"></i></a>
														<a class="jp-restore-screen btn" tabindex="1" title="restore screen">restore screen</a>
														<a class="jp-repeat btn" tabindex="1" title="repeat"><i class="icon-retweet"></i></a>
														<a class="jp-repeat-off btn" tabindex="1" title="repeat off"><i class="icon-long-arrow-right"></i></a>
													</div>
													
													<div class="volume-holder">
														<a class="jp-mute" href="#"><i class="icon-volume-off"></i></a>
														<a class="jp-unmute" href="#"><i class="icon-volume-down"></i></a>
														
														<div class="jp-volume-bar">
															<div class="jp-volume-bar-value"></div>
														</div>
										
														<a class="jp-volume-max" href="#"><i class="icon-volume-up"></i></a>
													</div>
												</div>
											</div>
										</div>
										<div class="jp-no-solution">
											<span>Update Required</span>
											To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
										</div>
									</div>
								</div>';
							$loadjavascripts .= '
								<script type="text/javascript">
								//<![CDATA[
								$(document).ready(function(){
									$("#jquery_jplayer_'.$recent_count.'").jPlayer({
										ready: function () {
											$(this).jPlayer("setMedia", {
												m4v: "'.$url.$upload_dir.$file_multimediale[0].'.'.$podcast_filetype.'",
												poster: "'.$url.'"
											});
										},
										size: {
											width: "100%",
											height: "auto",
											cssClass: "jp-video-360p"
										},

										swfPath: "/components/player/js",
										supplied: "m4v",
										keyEnabled: true
									});
								});
								//]]>
								</script>'; 
							}

							$PG_mainbody .= "</article>";

						if ($recent_count == 0) { //use keywords of the most recent episode as meta tags in the home page
							$assignmetakeywords = $text_keywordspg;
						}
						$recent_count++; //increment recents
					}
				}
			}
		}
	
	}
	$trackfeed .= '</ul>
		</div>';
} else { 
	$PG_mainbody .= '<div class="topseparator"><p>'.$L_dir.' <b>'.$upload_dir.'</b> '.$L_empty.'</p></div>';
}

?>