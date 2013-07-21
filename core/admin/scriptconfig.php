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

// check if user is already logged in
if(isset($amilogged) AND $amilogged =="true") {

	$PG_mainbody .= '<fieldset><legend>'.$L_admin_changeconf.'</legend>';

	if (isset($_GET['action']) AND $_GET['action']=="change") { // if action is set
	
		// strict rename
		$strictfilename = $_POST['strictfilename'];
		if ($strictfilename != "") {
			$strictfilenamepolicy = $strictfilename;
		}			

		// recent in home
		$recent = $_POST['recent'];
		if ($recent != "") {
			$max_recent = $recent;
		}

		// recent in FEED
		$recentinfeed = $_POST['recentinfeed'];
		if ($recentinfeed != "") {
			$recent_episode_in_feed = $recentinfeed;
		}				

		// date format
		$selectdateformat = $_POST['selectdateformat'];
		if ($selectdateformat != "") {
			$dateformat = $selectdateformat;
		}

		// script language
		$scriptlanguage = $_POST['scriptlanguage'];
		if ($scriptlanguage != "") {
			$scriptlang = $scriptlanguage;
		}

		include ("$absoluteurl"."core/admin/createconfig.php"); //regenerate config.php

		$PG_mainbody .= '<p><b>'.$L_informationsent.'</b></p>';

		//REGENERATE FEED ...
		include ("$absoluteurl"."core/admin/feedgenerate.php");
		$PG_mainbody .= '<br /><br />';

	}
	else { // if action not set
		
		$PG_mainbody .=	'<form name="podcastdetails" method="POST" enctype="multipart/form-data" action="?p=admin&do=config&action=change">';

		########## strictfilename
		$PG_mainbody .= '<h4>'.$L_enablestrictrenamepolicy.'</h4>
			<p>'.$L_enablestrictrenamepolicy_hint.'</p>
			<label class="radio">'.$L_yes.' <input type="radio" name="strictfilename" value="yes" ';

		if ($strictfilenamepolicy == "yes") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '></label>
		<label class="radio">'.$L_no.' <input type="radio" name="strictfilename" value="no" ';

		if ($strictfilenamepolicy == "no") {
			$PG_mainbody .= 'checked';
		}

		$PG_mainbody .= '></label>';

		########## recent in home
		$PG_mainbody .= '<h4>'.$L_howmanyrecent.'</h4>

			<select name="recent" id="recent">';
			
			
			$i = 1;
			while ($i <= 50) {
				$PG_mainbody .= '<option value=\''.$i.'\'';
				if ($max_recent == $i) { $PG_mainbody .= ' selected'; }
				$PG_mainbody .= '>'.$i.'</option>';
				$i++;
			}
		$PG_mainbody .= '</select>';

		########## recent in feed
		$PG_mainbody .= '<h4>'.$L_howmanyrecentinfeed.'</h4>

			<select name="recentinfeed" id="recentinfeed">';
			$i = 1;
			while ($i <= 50) {
				$PG_mainbody .= '<option value=\''.$i.'\'';
				if ($recent_episode_in_feed == $i) { $PG_mainbody .= ' selected'; }
				$PG_mainbody .= '>'.$i.'</option>';
				$i++;
			}
			$PG_mainbody .= '<option value=\'All\'';
			if ($recent_episode_in_feed == "All") { $PG_mainbody .= ' selected'; }
			$PG_mainbody .= '>'.$L_all.'</option>';
			$PG_mainbody.='</select>';

		########## date format
		$PG_mainbody .= '<h4>'.$L_selectdateformat.'</h4>

			<select name="selectdateformat" id="selectdateformat">

			<option value=\'d-m-Y\'';
		if ($dateformat == "d-m-Y") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>'.$L_day.' / '.$L_month.' / '.$L_year.'</option>

			<option value=\'m-d-Y\'';
		if ($dateformat == "m-d-Y") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>'.$L_month.' / '.$L_day.' / '.$L_year.'</option>

			<option value=\'Y-m-d\'';
		if ($dateformat == "Y-m-d") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>'.$L_year.' / '.$L_month.' / '.$L_day.'</option>
		
			<option value=\'F jS, Y\'';
		if ($dateformat == "F jS, Y") { $PG_mainbody .= ' selected'; }
		$PG_mainbody .= '>October 3rd, 1979</option>

			</select>';

		$arr = array("ca" => "Català",
			"cy" => "Cymraeg",
			"de" => "Deutsch",
			"en" => "English",
			"es" => "Español",
			"et" => "Eesti",
			"fa" => "فارسی",
			"fr" => "Français",
			"it" => "Italiano",
			"hu" => "Magyar",
			"ja" => "日本語",
			"pt" => "Português",
			"th" => "ไทย",	
			"tr" => "Türkçe");

		## SCRIPT LANGUAGES LIST
		$PG_mainbody .= '<h4>'.$L_podcastgenlang.'</h4>
			<p><span class="admin_hints">'.$L_pglanghint.'</span></p>
			';
		$PG_mainbody .= '<select name="scriptlanguage">';

		natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($arr as $key => $val) {
			$PG_mainbody .= '
				<option value="' . $key . '"';
			if ($scriptlang == $key) {
				$PG_mainbody .= ' selected';
			}
			$PG_mainbody .= '>' . $val . '</option>';	
		}
		$PG_mainbody .= '</select></fieldset>';	
		$PG_mainbody .= '<div class="form-actions">
			<input type="submit" name="'.$L_send.'" value="'.$L_send.'" class="btn btn-primary">
			</div>';
	}
}

?>
