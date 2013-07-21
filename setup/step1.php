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

include ('checkconfigexistence.php');

$PG_mainbody = NULL; //define

// define variables
$arr = NULL;
$arrid = NULL;
$n = 0;

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

$PG_mainbody .= '

	

	<fieldset><legend>Select Language</legend>
	<form method="post" action="index.php?step=2">
	<br />
	<select name="setuplanguage">';


natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive


$browserlanguage = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2); // ASSIGN BROWSER LANGUAGE into a VARIABLE

foreach ($arr as $key => $val) {
			$PG_mainbody .= '
				<option value="' . $key . '"';
			if ($scriptlang == $key) {
				$PG_mainbody .= ' selected';
			}
			$PG_mainbody .= '>' . $val . '</option>';	
		}

$PG_mainbody .= '</select>
	<br />
	<input type="submit" value="'.$SL_next.'" class="btn btn-primary">
	</form>
	</fieldset>';


//print output

echo $PG_mainbody;

?>


	</li>
	</ul>
