<?php
############################################################
# PHP Podcast Creator
#
# Created by Alberto Betella
# Improved by Tim Wasson
# 
# This is Free Software released under the GNU/GPL License.
############################################################

ini_set('max_execution_time', 300);

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

### If admin pages, start a PHP session
if (isset($_GET['p'])) if ($_GET['p']=="admin") { session_start(); }

if (!file_exists("config.php")) { //if config.php doesn't exist stop the script
	header("Location: setup/"); // open setup script
} 


include("config.php"); 

if (!isset($defined)) include("$absoluteurl"."core/functions.php"); //LOAD ONCE
include("$absoluteurl"."core/supported_media.php");
include("$absoluteurl"."core/language.php");

if (isset($_GET['p'])) {

	if ($_GET['p']=="admin") {
		include("$absoluteurl"."core/admin/admin.php");
		include("$absoluteurl"."core/themes.php");
    
    echo $theme_file_contents;
	}
}
else { // if no p= specifies, e.g. just index.php with no GET
	$output = file_get_contents($theme_path."/app.html");
  
  $output = str_replace("{{ title }}", $podcast_title, $output);
  
  $output = str_replace("{{ subtitle }}", $podcast_subtitle, $output);
  
  echo $output;
}

?>
