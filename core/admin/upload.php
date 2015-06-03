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

### Check if user is logged ###
if ($amilogged != "true") { exit; }
###
 
if (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="upload" AND isset($_GET['c']) AND $_GET['c']=="ok") { 

	// This is for a NEW episode.
	$PG_mainbody .= '<h3>'.$L_uploadpodcast.'</h3>';

	// If the episode has been uploaded via FTP
	if(isset($_POST['ftpfile']) AND !empty($_POST['ftpfile'])) {
		include($absoluteurl."core/admin/sendchanges.php");
	} else {
		//If this has been uploaded via the web.
		include($absoluteurl."core/admin/sendfile.php");
	}
	//$PG_mainbody .= "</div>";

} elseif (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="edit" AND isset($_GET['c']) AND $_GET['c']=="ok") { 
	
	//For editing an older episode.
	$PG_mainbody .= '<h3>'.$L_editpodcast.'</h3>';
	include($absoluteurl."core/admin/sendchanges.php");
	//$PG_mainbody .= '</div>';

} else {
	// If we're in edit mode as opposed to new podcast mode.
	if (isset($_GET['name']) AND $_GET['name'] != NULL ) {
		//Go into edit mode, so it's different than new podcast mode.
		$mode = "edit";
		
		// Enter the basics into the database.
    mysql_connect($server,$db_user,$db_pass);
    		
    // select the database
    mysql_select_db($database) or die ("Could not select database because ".mysql_error());
    
    $result = mysql_query("SELECT * FROM Episodes WHERE filename = '".$_GET['name']."' LIMIT 1");
    
    $row = mysql_fetch_assoc($result);
		
		$text_title = depurateContent($row['title']); //title
		$text_shortdesc = depurateContent($row['subtitle']); //short desc
		$text_longdesc = depurateContent($row['description']); //long desc
		$text_keywordspg = depurateContent($row['keywords']); //Keywords
		$text_authornamepg = depurateContent($row['author']);
		$text_authoremailpg = depurateContent($row['authoremail']); 
  }
	########### Determine max upload file size through php script reading the server parameters (and the form parameter specified in config.php. We find the minimum value: it should be the max file size allowed...

		# convert max upload size set in config.php in megabytes
		$max_upload_form_size_MB = $max_upload_form_size/1048576;
		$max_upload_form_size_MB = round($max_upload_form_size_MB, 2);

		$showmin = min($max_upload_form_size_MB, ini_get('upload_max_filesize')+0, ini_get('post_max_size')+0); // min function
		// Note: if I add +0 it eliminates the "M" (e.g. 8M, 9M) and this solves some issues with the "min" function

		$PG_mainbody .= '<h3>'.$L_uploadpodcast.'</h3>';
		
		if($mode == "edit") {
			$action = '?p=admin&amp;do=edit&amp;c=ok';
		} else {
			$action = '?p=admin&amp;do=upload&amp;c=ok';
		}
		
		$PG_mainbody .= '
			<form action="'.$action.'" method="post" enctype="multipart/form-data" name="uploadform" id="uploadform">

			<fieldset>
			<legend>'.$L_maininfo.'</legend>
			
			<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_upload_form_size.'">';


		$PG_mainbody .= '
			<input name="title" id="title" type="text" size="50" maxlength="255" placeholder="'.$L_title.'*"  value="'.$text_title.'" class="form-control">
			
			<br>

			<textarea name="description" id="description" type="text" size="50" maxlength="255" class="form-control" rows="4" placeholder="'.$L_shortdesc.'*">'.$text_shortdesc.'</textarea>
			<div class="alert alert-info"><span class="help cdown"><span id="countdown">255</span> '.$L_remainchar.' '.$L_maxchardesc.'</span></div>';
		
		if($mode=="edit") {
			$PG_mainbody .= '<h4>'.$L_filetoedit.'</h4>
			<div class="well">
				<strong>'.$text_title.'</strong> ('.$_GET['name'].')
				
			</div>
			<input type="hidden" name="userfile" value="'.$_GET['name'].'">';
		} else {
			$PG_mainbody .= '<h4>'.$L_file.'*</h4>
			<div class="well"><input name="userfile" id="userfile" type="file" style="float:left;">
			
			<p style="text-align:right"><a href="#uploadFiles" data-toggle="modal">Check FTP </a>
			
			<a data-toggle="popover" title="FTP Uploads" data-content="If you can\'t upload via the automatic uploader, you can drop a file into the /media/ folder via FTP. It will show up here for you to add it as an episode." id="ftpexpl" data-placement="left"><i class="icon-question-sign"></i></a></p>

			
					
				
				<input name="ftpfile" id="ftpfile" type="hidden">				
			</div>';
		}
				
		$PG_mainbody .= '<small><p>'.$L_fieldsrequired.'</p></small>
			</fieldset>
			
			<label class="checkbox">
				<input type="checkbox" id="moreinfo" value="'.$L_addextrainfo.'">
				  '.$L_addextrainfo.'
			</label>

			<div id="main"> 

			<fieldset>
			<legend>'.$L_extrainfo.'</legend>

			<h4>'.$L_longdesc.'</h4>
			<textarea id="long_description" name="long_description" cols="50" rows="3" class="tinymce">'.$text_longdesc.'</textarea>
			<small><span class="help-block">'.$L_htmlaccepted.'</span></small>

			<h4>'.$L_image.'</h4>
			<div class="well">';
			$fileimagetocheck = $absoluteurl.$img_dir.$row['image'];
			
			if (file_exists($fileimagetocheck) && !empty($row['image'])) { // if image exists
				$PG_mainbody .= '
				<input type="hidden" name="existentimage" value="'.$row['image'].'">
				<h4>'.$L_imagecurrent.'</h4>
				<img src="'.$url.$img_dir.$row['image'].'" alt="'.$L_imagecurrent.'" />
				<h4>'.$L_imagenew.'</h4>	
				<input name="image" type="file">';
			} else { // if image doesn't exist
				$PG_mainbody .= '
					<input name="image" type="file">
				';
			}
			
			$PG_mainbody .= '</div>
			<small><p>'.$L_imagehint.' '.$L_imageformat.'</p></small>

			<h4>'.$L_itunes_keywords.'</h4>
				
			<textarea id="keywords" name="keywords" type="text" size="50" maxlength="255" class="form-control">'.$text_keywordspg.'</textarea><br />
			<small><span class="help-block">'.$L_separatekeywords.'<span id="wordcount">0</span> '.$L_words.'</span></small>

			<h4>'.$L_explicitcontent.'</h4>
			<label class="radio">
				<input type="radio" name="explicit" value="yes">'.$L_yes.'
			</label>

			<label class="radio">
				<input type="radio" name="explicit" value="no" checked>'.$L_no.'
			</label>
								
			<small><span class="help-block">'.$L_explicithint.'</span></small>

			<h4>'.$L_author.'</h4>
			<p>'.$L_authorhint.'</p>

			<input name="auth_name" type="text" id="auth_name" size="50" maxlength="255" placeholder="'.$L_authorname.'" value="'.$text_authornamepg.'" class="form-control">
			<br />
			<input name="auth_email" type="text" id="auth_email" size="50" maxlength="255" placeholder="'.$L_authoremail.'" value="'.$text_authoremailpg.'" class="form-control">

			</fieldset>
</div>
			<div class="form-actions">
				<input type="submit" value="'.$L_send.'" class="btn btn-primary" id="submitep">
			</div>
			
			<div class="progress" id="fileProgress"><div class="progress-bar"></div></div>

		</form>

<!-- Modal -->
  <div class="modal fade" id="uploadFiles">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Uploaded Files</h4>
        </div>
        <div class="modal-body">
         <p>These are files that have been uploaded via FTP but not yet included in your feed. Select the file you\'d like associated with this episode. <strong>File names should contain no special characters. Only lower-case letters, numbers, and underscores. <em>The only period in the file name should be between the file name and the extension.</em></strong></p>
         <ul>';
	
	// Connect to the Database
  mysql_connect($server,$db_user,$db_pass);
            		
  // select the database
  mysql_select_db($database) or die ("Could not select database because ".mysql_error());
	
	// This chunk of code checks for uploaded files that don't have a database file associated with them. These can then be inserted 
	$handle = opendir ($absoluteurl.$upload_dir);
	while (($filename = readdir ($handle)) !== false)
	{
		if ($filename != '..' && $filename != '.' && $filename != 'index.htm' && $filename != '_vti_cnf' && $filename != '.DS_Store')
		{
			$file_array[$filename] = filemtime($absoluteurl.$upload_dir.$filename);
		}
	}
	
	if (!empty($file_array)) { //if directory is not empty
		# asort ($file_array);
		arsort ($file_array); //the opposite of asort (inverse order)
		$recent_count = 0; //set recents to zero
		$no_results = true;
		foreach ($file_array as $key => $value)	{
			$file_multimediale = explode(".",$key); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)
			$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);
			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]
        
        $result = mysql_query("SELECT count(*) as total from Episodes WHERE filename = '".$key."'");
        $data = mysql_fetch_assoc($result);
        
        //echo $key.": ".$data['total'];

				if (empty($data['total'])) { //if database file exists 
					$PG_mainbody .= "<li><a class=\"ftpupload\" data-ftpurl=\"".$key."\">".$key."</a></li>";
					$no_results = false;
				}
			}
		}
		if($no_results == true) {
      $PG_mainbody .= "<li>No uploaded files that are not currently associated with an episode.</li>";
    }
	} 

	$PG_mainbody .= '</ul>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link" data-dismiss="modal">Close</a>
          <!-- <a href="#" class="btn btn-primary">Save changes</a> -->
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->';

} // end else . if GET variable "c" is not = "ok"
?>