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

if (isset($_GET['p'])) if ($_GET['p']=="admin") { // if admin is called from the script in a GET variable - security issue

	$PG_mainbody .= "<h3>$L_admin_editdel</h3>";

	//load XML parser for PHP4 or PHP5
	include("$absoluteurl"."components/xmlparser/loadparser.php");

	// Open podcast directory
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
		$PG_mainbody .= '<table class="table table-striped">
				<tr>
					<th>Image</th>
					<th>Title</th>
					<th>Episode Date</th>
					<th>View</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>';


		foreach ($file_array as $key => $value)	{
			$file_multimediale = explode(".",$key); //divide filename from extension [1]=extension (if there is another point in the filename... it's a problem)
			$fileData = checkFileType($file_multimediale[1],$podcast_filetypes,$filemimetypes);

			if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]
				$podcast_filetype = $fileData[0];
				
				if ($file_multimediale[1]=="$podcast_filetype") { // if the extension is the same as specified in config.php

					$file_size = filesize("$upload_dir$file_multimediale[0].$podcast_filetype");
					$file_size = $file_size/1048576;
					$file_size = round($file_size, 2);

					############
					$filedescr = "$absoluteurl"."$upload_dir$file_multimediale[0].xml"; //database file

					if (file_exists("$filedescr")) { //if database file exists 
						# READ the XML database file and parse the fields
						include("$absoluteurl"."core/readXMLdb.php");

						#Define episode headline
						$episode_date = "<a name=\"$file_multimediale[0]\"></a>
							<a href=\"".$url."download.php?filename=$file_multimediale[0].$podcast_filetype\">
							</a> &nbsp;".date ($dateformat, $value)."";

						### Here the output code for the episode is created

						# Fields Legend (parsed from XML):
						# $text_title = episode title
						# $text_shortdesc = short description
						# $text_longdesc = long description
						# $text_imgpg = image (url) associated to episode
						# $text_categoriespg = categories
						# $text_keywordspg = keywords
						# $text_explicitpg = explicit podcast (yes or no)
						# $text_authorpg = author

						####### delete quotes and apostrophes
						$text_title2 = str_replace('\'', '', $text_title); //$text_title2 replace apostrophe, otherwise delete doesn't work
						$text_title2 = str_replace('"', '', $text_title2); //$text_title2 replace quotes, otherwise delete doesn't work
						####### 

						$PG_mainbody .= '
							<tr>
							<td>';
							
							if(!empty($text_imgpg)) {
								$PG_mainbody .= '<img src="/images/'.$text_imgpg.'" style="height:100px" />';
							} else {
								$PG_mainbody .= '<img src="http://placehold.it/100x100&text=No+Image">';
							}
						$file_multimediale[0] = urlencode($file_multimediale[0]);
						$PG_mainbody .= '</td>
							<td>'.$text_title.'</td>
							<td>'.$episode_date.'</td>
							<td><a href="?p=episode&amp;name='.$file_multimediale[0].'.'.$podcast_filetype.'"><i class="icon-eye-open"></i></a></td>
							<td><a href="?p=admin&do=edit&amp;name='.$file_multimediale[0].'.'.$podcast_filetype.'"><i class="icon-edit"></i></a></td>';
									
							// Generate the Delete URL
							$delURL = '?p=admin&do=delete&file='.$file_multimediale[0].'&ext='.$podcast_filetype;

							if ($text_imgpg!=NULL) {
								$delURL .= '&img='.$text_imgpg;
							}

						$PG_mainbody .= '
							<td><a href="#myModal" data-toggle="modal" class="delep" data-delurl='.$delURL.'><i class="icon-remove"></i></a></td>
							</tr>';

					} 
				}
			}
		}
		$PG_mainbody .= "</table>";
	} else { 
		$PG_mainbody .= '<div class="topseparator"><p>'.$L_dir.' <b>'.$upload_dir.'</b> '.$L_empty.'</p><p><a href="?p=admin&do=upload">'.$L_uploadanepisode.'</a></p></div>';
	}
} //end if admin

$PG_mainbody .= '

<!-- Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">'.$L_deleteconfirmation.'</h4>
        </div>
        <div class="modal-body">
         <p>You cannot undo this.</p>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-link" data-dismiss="modal">Close</a>
          <a id="delurl" class="btn btn-danger" href=""><i class="icon-warning-sign"></i> '.$L_delete.'</a>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->';

?>