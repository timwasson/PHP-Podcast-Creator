<?php
############################################################
# PHP Podcast Creator
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
	
	if (file_exists("$absoluteurl"."setup")) {
	    $PG_mainbody .= "<div class=\"alert alert-danger\"><i class=\"icon-warning-sign\"></i> Uh oh. Your /setup/ directory still exists. You should delete this as it is a security threat.</div>";
	}
	// check if user is already logged in
	if(isset($amilogged) AND $amilogged =="true") {
	
		$admmenu .= '
		<div class="panel">
			<div class="panel-heading" style="margin-bottom:-16px;">'.$L_admin_episodes.'</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><a href="?p=admin&do=upload"><i class="icon-plus"></i> '.$L_admin_upload.'</a></li>
				<li class="list-group-item"><a href="?p=admin&do=editdel"><i class="icon-edit"></i> '.$L_admin_editdel.'</a></li>
				<li class="list-group-item"><a href="?p=admin&do=generate"><i class="icon-refresh"></i> '.$L_admin_genfeed.'</a></li>
			</ul>
		</div>
		
		<div class="panel">
			<div class="panel-heading" style="margin-bottom:-16px;">'.$L_admin_itunessettings.'</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><a href="?p=admin&do=itunescat"><i class="icon-music"></i> '.$L_changecat.'</a></li>
				<li class="list-group-item"><a href="https://phobos.apple.com/WebObjects/MZFinance.woa/wa/publishPodcast?feedURL='.$url.$feed_dir.'feed.xml" target="_blank"><i class="icon-arrow-right"></i> '.$L_submit_itunes_store.'</a></li>
			</ul>
		</div>
		
		<div class="panel">
		<div class="panel-heading" style="margin-bottom:-16px;">'.$L_admin_podcastdetails.'</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><a href="?p=admin&do=changedetails"><i class="icon-info-sign"></i> '.$L_changepodcastdetails.'</a></li>
				<li class="list-group-item"><a href="http://validator.w3.org/feed/check.cgi?url='.$url.'feed.xml" target="_blank"><i class="icon-ok"></i> '.$L_admin_feed_validate.'</a></li>
			</ul>
		</div>
		<div class="panel">
			<div class="panel-heading" style="margin-bottom:-16px;">'.$L_pgconfig.'</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><a href="?p=admin&do=config"><i class="icon-cog"></i> '.$L_admin_changeconf.'</a></li>
			</ul>
		</div>';

		
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
				<h2>'.$L_welcome.'</h2>
				<div class="alert alert-info">
				<p><i class="icon-thumbs-up"></i> '.$L_firstadminmsg.' </p><p><a href="?p=admin&do=changedetails" class="btn btn-primary"><strong><i class="icon-arrow-right"></i> '.$L_startnow.'</strong></a></p>

				</div>';
				
				$PG_mainbody .= '
				<h3>Feed Downloads</h3>
				<div id="feeddown" style="width: 100%; height: 300px;"></div>
				<p>This is the total number of feed downloads you\'ve received. </p>
				
				<h3>Episode Downloads</h3>
				<div id="epdown" style="width: 100%; height: 300px;"></div>
				<p>These are downloads of individual downloads per episode.</p>';
				
			}
		}
	}
}
?>