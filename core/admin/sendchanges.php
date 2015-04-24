<?php
############################################################
# PHP PODCAST CREATOR
#
# Created by Alberto Betella
# Improved by Tim Wasson
# 
# This is Free Software released under the GNU/GPL License.
############################################################

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

### Check if user is logged ###
if ($amilogged != "true") { exit; }
###

if(isset($_POST['ftpfile'])) {
	$userfile = $_POST['ftpfile'];
} else {
	$userfile = $_POST['userfile'];
}

if (isset($userfile) AND $userfile!=NULL AND isset($_POST['title']) AND $_POST['title']!=NULL AND isset($_POST['description']) AND $_POST['description']!=NULL){ //001

	$file = $userfile; //episode file
	$img = $_FILES['image']['name']; // image file
	$existentimage = $_POST['existentimage'];
	$title = $_POST['title'];
	$description = $_POST['description'];

	$long_description = $_POST['long_description'];
	$keywords = $_POST['keywords'];
	$explicit = $_POST['explicit'];
	$auth_name = $_POST['auth_name'];
	$auth_email = $_POST['auth_email'];

	// echo "<br /><br /><br />$file - err $errore - temp: $temporaneo<br /><br /><br />";
	$filesuffix = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name

	####
	## here I check lenght of long description: according to the iTunes technical specifications
	## the itunes:summary field can be up to 4000 characters, while the other fields up to 255

	$longdescmax =4000; #set max characters variable. iTunes specifications by Apple say "max 4000 characters" for long description field

	if (strlen($long_description)<$longdescmax) { // 002 (if long description IS NOT too long, go on executing...

		############### cleaning/depurate input
		###############
		//$title = stripslashes($title);
		$title = strip_tags($title);
		$title = htmlspecialchars($title); 

		//$description = stripslashes($description); // no slashes on ' and "
		$description = strip_tags($description);
		#$description = htmlspecialchars($description); 

		$long_description = stripslashes($long_description);
		#$long_description = htmlspecialchars($long_description); // long description accepts HTML

		//$keywords = stripslashes($keywords);
		$keywords = strip_tags($keywords);
		$keywords = htmlspecialchars($keywords);

		//$auth_name = stripslashes($auth_name);
		$auth_name = strip_tags($auth_name);
		$auth_name = htmlspecialchars($auth_name);


		############## end input depuration
		##############

		#### INPUT DEPURATION N.2
		$title = depurateContent($title); //title
		$description = depurateContent($description); //short desc
		//$long_description = depurateContent($long_description); //long desc
		$keywords = depurateContent($keywords); //Keywords
		$auth_name = depurateContent($auth_name); //author's name

		##############
		### processing Long Description

		#$PG_mainbody .= "QUI: $long_description<br>lunghezza:".strlen($long_description)."<br>"; //debug

		if ($long_description == NULL OR $long_description == " ") { //if user didn't input long description the long description is equal to short description
		$PG_mainbody .= "<p>$L_longdesnotpresent</p>";
		$long_description = $description;
	}

	else {
		$PG_mainbody .= "<p>$L_longdescpresent</p>";
		$long_description = str_replace("&nbsp;", " ", $long_description); 
	}

	##############
	### processing iTunes KEYWORDS

	## iTunes supports a maximum of 12 keywords for searching: don't know how many keywords u can add in a feed. Anyway it's better to add a few keyword, so we display a warning if user submits more than 12 keywords

	# $PG_mainbody .= "$keywords<br>"; /debug

	if (isset($ituneskeywords) AND $ituneskeywords != NULL) { 
		$PG_mainbody .= "<p>$L_itunes_keywords $ituneskeywords</p>";

		$singlekeyword=explode(",",$keywords); // divide filename from extension

		if ($singlekeyword[12] != NULL) { //if more than 12 keywords
			$PG_mainbody .= "<p>- $L_itunes_num_keyw</p>";

		}
	}

	##############
	### processing Author

	if (isset($auth_name) AND $auth_name != NULL) { //if a different author is specified

		$PG_mainbody .= "<p>$L_authpresent</p>";

		if (!validate_email($auth_email)) { //if author doesn't have a valid email address, just ignore it and use default author

		$PG_mainbody .= "<p>$L_noauthemail $L_authignored</p>";

		$auth_name = NULL; //ignore author
		$auth_email = NULL; //ignore email

	} 


}
else { //if author's name doesn't exist unset also email field
$auth_email = NULL; //ignore email
}

$PG_mainbody .= "<p><b>$L_processingchanges</b></p>";

// Put it all in the database
		// Get mime type.
		$fileData = checkFileType($file_ext[1],$podcast_filetypes,$filemimetypes); 
  
    if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]
      $podcast_filetype = $fileData[0];
  		$filemimetype=$fileData[1]; //define mimetype to put in the feed
    }
    
    // Get file size
    $file_size = filesize($absoluteurl.$upload_dir.$filenamechanged.$filesuffix.".".$file_ext[1]);
    
    // Get duration.
    require_once("$absoluteurl"."components/getid3/getid3.php"); //read id3 tags in media files (e.g.title, duration)
    $getID3 = new getID3; //initialize getID3 engine
  
    # File details (duration, bitrate, etc...)
    $ThisFileInfo = $getID3->analyze($absoluteurl.$upload_dir.$filenamechanged.$filesuffix.".".$file_ext[1]); //read file tags
  
    $file_duration = @$ThisFileInfo['playtime_string'];
		
		// Enter the basics into the database.
    mysql_connect($server,$db_user,$db_pass);
    		
    // select the database
    mysql_select_db($database) or die ("Could not select database because ".mysql_error());
    	
    $sql = "UPDATE Episodes SET 
    		title = '".$title."',
    		subtitle = '".$description."',
    		description = '".$long_description."',
    		author = '".$auth_name."',
    		authoremail = '".$auth_email."',
    		keywords = '".$keywords."',
    		explicit = '".$explicit."',
    		image = '".$image_new_name."',
    		type = '".$filemimetype."'
    		WHERE filename = '".$userfile."'";
    $result = mysql_query($sql);
    
    $last_id = mysql_insert_id();
    $PG_mainbody .= "<p>".$last_id."</p>";
    	
    if(!$result) {
      echo "Oops. ".mysql_error();
    }
    
						#	$PG_mainbody .= "<p><b><font color=\"green\">$L_filesent</font></b></p>"; // If upload is successful.

						########## REGENERATE FEED
						include ("$absoluteurl"."core/admin/feedgenerate.php"); //(re)generate XML feed
						##########

						$PG_mainbody .= "<p><a href=\"$url\">$L_gohome</a> - <a href=\"?p=admin&do=editdel\">$L_editotherepisodes</a></p>";

							} // 002
							else { //if long description is more than max characters allowed

								$PG_mainbody .= "<b>$L_longdesctoolong</b><p>$L_longdescmaxchar $longdescmax $L_characters - $L_actualenght <font color=red>".strlen($long_description)."</font> $L_characters.</p>
									<form>
									<INPUT TYPE=\"button\" VALUE=\"$L_back\" onClick=\"history.back()\">
									</form>";
							}
							#### end of long desc lenght checking


						} //001 
						else { //if file, description or title not present...
							$PG_mainbody .= '<p>'.$L_nofield.'
								<br />
								<form>
								<INPUT TYPE="button" VALUE='.$L_back.' onClick="history.back()">
								</form>
								</p>
								';
						}


?>