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

if (isset($_GET['p'])) if ($_GET['p']=="admin") { // if admin is called from the script in a GET variable - security issue


	include("$absoluteurl"."core/admin/login.php");

	include("$absoluteurl"."core/admin/checklogged.php");


	// check if user is already logged in
	if(isset($amilogged) AND $amilogged =="true") {
		
		if(empty($_GET['do'])) {
			$PG_mainbody .= "This is just intro text for the admin panel.";
		}
		
		$admmenu .= '
		<ul class="nav nav-list"> 
			<li class="nav-header">'.$L_admin_episodes.'</li>
			 
			<li><a href="?p=admin&do=upload">'.$L_admin_upload.'</a></li>
			<li><a href="?p=admin&do=editdel">'.$L_admin_editdel.'</a></li>
			<li><a href="?p=admin&do=generate">'.$L_admin_genfeed.'</a></li>
			
			<li class="divider"></li>
			<li class="nav-header">'.$L_admin_itunessettings.'</li>
			
			<li><a href="?p=admin&do=itunesimg">'.$L_change_itunesimage.'</a></li>
			<li><a href="?p=admin&do=itunescat">'.$L_changecat.'</a></li>
			<li><a href="https://phobos.apple.com/WebObjects/MZFinance.woa/wa/publishPodcast?feedURL='.$url.$feed_dir.'feed.xml" target="_blank">'.$L_submit_itunes_store.'</a></li>
			
			<li class="divider"></li>
			<li class="nav-header">'.$L_admin_podcastdetails.'</li>
			<li><a href="?p=admin&do=changedetails">'.$L_changepodcastdetails.'</a></li>
			<li><a href="http://validator.w3.org/feed/check.cgi?url='.$url.'feed.xml" target="_blank">'.$L_admin_feed_validate.'</a></li>
			
			<li class="divider"></li>
			<li class="nav-header">'.$L_pgconfig.'</li>
			<li><a href="?p=admin&do=config">'.$L_admin_changeconf.'</a></li>
		</ul>';

		
		if (isset($_GET['do']) AND $_GET['do']=="generate") {

			include("$absoluteurl"."core/admin/feedgenerate.php");
		} 
		elseif (isset($_GET['do']) AND $_GET['do']=="upload") {

			include("$absoluteurl"."core/admin/upload.php");
		} 
		elseif (isset($_GET['do']) AND $_GET['do']=="editdel") {

			include("$absoluteurl"."core/admin/editdel.php");
		} 
		elseif (isset($_GET['do']) AND $_GET['do']=="edit") {

			include("$absoluteurl"."core/admin/upload.php");
		} 
		elseif (isset($_GET['do']) AND $_GET['do']=="delete") {

			include("$absoluteurl"."core/admin/delete.php");
		} 
		elseif (isset($_GET['do']) AND $_GET['do']=="itunesimg") {

			include("$absoluteurl"."core/admin/itunesimg.php");
		}
		elseif (isset($_GET['do']) AND $_GET['do']=="itunescat") {

			include("$absoluteurl"."core/admin/itunescategories.php");
		}
		elseif (isset($_GET['do']) AND $_GET['do']=="changedetails") {

			include("$absoluteurl"."core/admin/podcastdetails.php");
		}
		elseif (isset($_GET['do']) AND $_GET['do']=="config") {

			include("$absoluteurl"."core/admin/scriptconfig.php");
		}
		else {

			if (isset($firsttimehere) AND $firsttimehere == "yes") { // if it's the first time (parameter specified in config.php)

				$PG_mainbody .= '
				<div class="topseparator"> 
				<h3>'.$L_welcome.'</h3>
				<p><i>'.$L_firstadminmsg.'</i> <a href="?p=admin&do=changedetails"><b>'.$L_startnow.'</b></a></p>

				</div>';	
			}
		}
	}
}
?>