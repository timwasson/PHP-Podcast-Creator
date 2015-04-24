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

if (isset($_GET['file']) AND $_GET['file']!=NULL) {

	$file = $_GET['file']; 
  $file = str_replace("/", "", $file); // Replace / in the filename.. avoid deleting of file outside media directory - AVOID EXPLOIT with register globals set to ON
	$ext = $_GET['ext'];

  // Delete the episode
	if (file_exists($absoluteurl.$upload_dir.$file)) {
		unlink ($upload_dir.$file.$ext);
		$PG_mainbody .="<p><b>".$file.$ext."</b> ".$L_deleted."</p>";
	}

  // Delete the image
	if (isset($_GET['img']) AND $_GET['img']!=NULL) { 
		$img = $_GET['img'];
		if (file_exists($absoluteurl.$img_dir.$img)) { // if associated image exists
			unlink ($absoluteurl.$img_dir.$img); // DELETE IMAGE FILE
			$PG_mainbody .="<p>".$L_del_img."</p>";
		}
	} //end if isset image
	
	// Remove the entry
	$mysqli = new mysqli($server, $db_user, $db_pass, $database);
	
	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}

  $sql = "DELETE FROM Episodes WHERE filename='".$file."'";
  
  if ($mysqli->query($sql) === TRUE) {
      $PG_mainbody .= "Record deleted successfully";
  } else {
      $PG_mainbody .= "Error deleting record: " . $conn->error;
  }


	//REGENERATE FEED
	include ($absoluteurl."core/admin/feedgenerate.php"); //(re)generate XML feed
	
	include("editdel.php");
	
} else { 
	$PG_mainbody .= $L_deletenothing;
}
?>