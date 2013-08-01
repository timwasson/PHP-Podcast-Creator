<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################


$testfile = "test.txt";

### define directories
$media_directory = "../media/";
$images_directory = "../images/";
$script_directory = "../";

## include language file
//if($scriptlang!=NULL) {
	//include("lang/setup_$scriptlang.php");
	//} else {
		//include("lang/setup_en.php");
		//}


		if (file_exists("../config.php")) { //if config.php already exists stop the script

			echo "<div class=\"alert alert-error\">$SL_configexists<br />$SL_configdelete</div>";

			exit;

		} 



		###############
		############### try to set writing permissions
		$PG_mainbody .= "<h3>$SL_checkperm</h3>";

		## checking media dir
		$fp = fopen("$media_directory$testfile",'a'); //create test file
		$content = "test";
		fwrite($fp,$content);
		fclose($fp);

		if (file_exists("$media_directory$testfile")) {

			$PG_mainbody .= "<div class=\"alert alert-success\">$SL_mediadir $SL_iswritable</div>";
			unlink ("$media_directory$testfile");
			$dir1 = "ok";
		}
		else {
			$PG_mainbody .= "<div class=\"alert alert-error\">$SL_mediadir ".$media_directory." $SL_notwritable</div>";
			$dir1 = "NO";
		}


		## checking images dir
		$fp1 = fopen("$images_directory$testfile",'a'); //create test file
		$content1 = "test";
		fwrite($fp1,$content1);
		fclose($fp1);

		if (file_exists("$images_directory$testfile")) {

			$PG_mainbody .= "<div class=\"alert alert-success\">".$SL_imgdir." ".$SL_iswritable."</div>";
			unlink ("$images_directory$testfile");
			$dir2 = "ok";
		}
		else {
			$PG_mainbody .= "<div class=\"alert alert-error\">$SL_imgdir ".$images_directory." $SL_notwritable</div>";
			$dir2 = "NO";
		}


		## checking script root dir
		$fp2 = fopen("$script_directory$testfile",'a'); //create test file
		$content2 = "test";
		fwrite($fp2,$content2);
		fclose($fp2);

		if (file_exists("$script_directory$testfile")) {

			$PG_mainbody .=  "<div class=\"alert alert-success\">$SL_scriptdir $SL_iswritable</div>";
			unlink ("$script_directory$testfile");
			$dir3 = "ok";
		}
		else {
			$PG_mainbody .=  "<div class=\"alert alert-error\">$SL_scriptdir ".$script_directory." $SL_notwritable</div>";
			$dir3 = "NO";
		}


		if (isset($dir1) AND $dir1=="ok" AND isset($dir2) AND $dir2=="ok" AND isset($dir3) AND $dir3=="ok") { // OK CAN PROCEED

			$PG_mainbody .= "<h4>$SL_permok</h4>";
			$PG_mainbody .=  "<p>$SL_canproceed</p>";

			$PG_mainbody .= '
			<form method="post" action="index.php?step=4">
			<div class="form-actions">
				
				<input type="hidden" name="setuplanguage" value="'.$_POST['setuplanguage'].'">
				<input type="submit" value="'.$SL_next.'" class="btn btn-primary">
				</div>
				</form>
				';

		} else {

			$PG_mainbody .=  "<h4>$SL_trytochmod</h4><ul>";

			if (isset($dir1) AND $dir1!="ok") {
				$PG_mainbody .=  "<li>$SL_settingchmod $media_directory ($SL_mediadir)</li>";
				chmod("$media_directory", 0777);
			}
			if (isset($dir1) AND $dir2!="ok") {
				$PG_mainbody .=  "<li>$SL_settingchmod to $images_directory ($SL_imgdir)</li>";
				chmod("$images_directory", 0777);
			}
			if (isset($dir1) AND $dir3!="ok") {
				$PG_mainbody .=  "<li>$SL_settingchmod $script_directory ($SL_scriptdir)</li>";
				chmod("$script_directory", 0777);
			}

			$PG_mainbody .=  "</ul><p><b>$SL_permtried</b></p>";

			// reload button
			$PG_mainbody .= '
				<form method="post" action="index.php?step=3">
				<br />
				<div class="form-actions">
				<input type="hidden" name="setuplanguage" value="'.$_POST['setuplanguage'].'">
				<input type="submit" value="'.$SL_reload1.'" class="btn btn-primary">
				</div>
				</form>
				';

			$PG_mainbody .=  "<p>$SL_reload2</p>";
			$PG_mainbody .=  "<p>$SL_setman</p><br />";


		}



		#######
		####### end set permission
		?>