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

### Check if user is logged ###
if ($amilogged != "true") { exit; }
###
 
if (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="upload" AND isset($_GET['c']) AND $_GET['c']=="ok") { 

	// This is for a NEW episode.
	$PG_mainbody .= '<h3>'.$L_uploadpodcast.'</h3>';

	// If the episode has been uploaded via FTP
	if(isset($_POST['ftpfile']) AND !empty($_POST['ftpfile'])) {
		include("$absoluteurl"."core/admin/sendchanges.php");
	} else {
		//If this has been uploaded via the web.
		include("$absoluteurl"."core/admin/sendfile.php");
	}
	$PG_mainbody .= "</div>";

} elseif (isset($_GET['p']) AND $_GET['p']=="admin" AND isset($_GET['do']) AND $_GET['do']=="edit" AND isset($_GET['c']) AND $_GET['c']=="ok") { 
	
	//For editing an older episode.
	$PG_mainbody .= '<h3>'.$L_editpodcast.'</h3>';
	include("$absoluteurl"."core/admin/sendchanges.php");
	$PG_mainbody .= '</div>';

} else {
	// If we're in edit mode as opposed to new podcast mode.
	if (isset($_GET['name']) AND $_GET['name'] != NULL ) {
		//Go into edit mode, so it's different than new podcast mode.
		$mode = "edit";
		
		$file_multimediale = $_GET['name'];
		if (file_exists("$absoluteurl"."$upload_dir$file_multimediale")) {
			//load XML parser for PHP4 or PHP5
			include("$absoluteurl"."components/xmlparser/loadparser.php");
			$file_multimediale = explode(".",$file_multimediale); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)
			$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);
			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]
				$podcast_filetype = $fileData[0];
				if ($file_multimediale[1]=="$podcast_filetype") { // if the extension is the same as specified in config.php
					$wholeepisodefile = "$absoluteurl"."$upload_dir$file_multimediale[0].$podcast_filetype";

					// $file_size = filesize("$wholeepisodefile");
					// $file_size = $file_size/1048576;
					// $file_size = round($file_size, 2);
					// $file_time = filemtime("$wholeepisodefile");
					// $filedate = date ("$dateformat", "$file_time");

					############
					$filedescr = "$absoluteurl"."$upload_dir$file_multimediale[0].xml"; //database file

					if (file_exists("$filedescr")) { //if database file exists 
						//$file_contents=NULL; 
						# READ the XML database file and parse the fields
						include("$absoluteurl"."core/readXMLdb.php");

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

						#############################


						#### CONTENT DEPURATION (solves problem with quotes etc...)
						$text_title = depurateContent($text_title); //title
						$text_shortdesc = depurateContent($text_shortdesc); //short desc
						$text_longdesc = depurateContent($text_longdesc); //long desc
						$text_keywordspg = depurateContent($text_keywordspg); //Keywords
						$text_authornamepg = depurateContent($text_authornamepg); 

						}
					}
				}
			}
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
			<input name="title" id="title" type="text" size="50" maxlength="255" placeholder="'.$L_title.'*"  value="'.$text_title.'">
			

			<textarea name="description" id="description" type="text" size="50" maxlength="255" class="span9" rows="4" placeholder="'.$L_shortdesc.'*">'.$text_shortdesc.'</textarea>
			<div class="alert alert-info"><span class="help cdown"><span id="countdown">255</span> '.$L_remainchar.' '.$L_maxchardesc.'</span></div>';
		
		if($mode=="edit") {
			$PG_mainbody .= '<h4>'.$L_filetoedit.'</h4>
			<div class="well">
				<strong>'.$text_title.'</strong> ('.$_GET['name'].')
			</div>
			<input type="hidden" name="userfile" value="'.$_GET['name'].'">';
		} else {
			$PG_mainbody .= '<h4>'.$L_file.'*</h4>
			<div class="well"><input name="userfile" id="userfile" type="file">
				<input name="ftpfile" id="ftpfile" type="hidden">
				<p class="pull-right">
					<a href="#uploadFiles" data-toggle="modal">or check for files uploaded via FTP</a>
				</p>
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
			$fileimagetocheck = "$absoluteurl"."$img_dir$text_imgpg";
			
			if (file_exists($fileimagetocheck) AND $text_imgpg != NULL) { // if image exists
				$PG_mainbody .= '
				<input type="hidden" name="existentimage" value="'.$text_imgpg.'">
				<h4>'.$L_imagecurrent.'</h4>
				<img src="'.$url.$img_dir.$text_imgpg.'" alt="'.$L_imagecurrent.'" />
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
				
			<textarea id="keywords" name="keywords" type="text" size="50" maxlength="255">'.$text_keywordspg.'</textarea><br />
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

			<input name="auth_name" type="text" id="auth_name" size="50" maxlength="255" placeholder="'.$L_authorname.'" value="'.$text_authornamepg.'">
			<br />
			<input name="auth_email" type="text" id="auth_email" size="50" maxlength="255" placeholder="'.$L_authoremail.'" value="'.$text_authoremailpg.'">

			</fieldset>
</div>
			<div class="form-actions">
				<input type="submit" value="'.$L_send.'" class="btn btn-primary">
			</div>
			
			<!-- <div class="progress" id="fileProgress"><div class="bar" style="width:50%"></div></div> -->

		</form>
		
	<div id="uploadFiles" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
	    <h3 id="myModalLabel">Uploaded Files</h3>
	  </div>
	  <div class="modal-body">
	    <p>These are files that have been uploaded via FTP but not yet included in your feed. Select the file you\'d like associated with this episode. <strong>File names should contain no special characters. Only lower-case letters, numbers, and underscores. <em>The only period in the file name should be between the file name and the extension.</em></strong></p><ul>';
	
	// This chunk of code checks for uploaded files that don't have a database file associated with them. These can then be inserted 
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
			$file_multimediale = explode(".",$key); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)
			$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);
			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]
				$filedescr = "$absoluteurl"."$upload_dir$file_multimediale[0].xml"; //database file
				if (!file_exists("$filedescr")) { //if database file exists 
					$PG_mainbody .= "<li><a class=\"ftpupload\" data-ftpurl=\"".$key."\">".$key."</a></li>";
				}
			}
		}
	}
	$PG_mainbody .= '</ul>
	  </div>
	  <div class="modal-footer">
	    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	    <button class="btn btn-primary">Save changes</button>
	  </div>
	</div>';

} // end else . if GET variable "c" is not = "ok"
?>