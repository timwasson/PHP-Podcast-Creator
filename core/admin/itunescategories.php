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

// check if user is already logged in
if(isset($amilogged) AND $amilogged =="true") {

	$PG_mainbody .= '<fieldset><legend>'.$L_itunescategories.'</legend>
		<p>'.$L_changecat.'</p>';

	if (isset($_GET['action']) AND $_GET['action']=="change") { // if action is set


		if (isset($_POST['category1'])) { //cat1
			$itunes_category[0] = $_POST['category1'];
		}

		if (isset($_POST['category2'])) { //cat2
			$itunes_category[1] = $_POST['category2'];
		}

		if (isset($_POST['category3'])) { //cat3
			$itunes_category[2] = $_POST['category3'];
		}
		
		if (isset($_FILES['image'] ['name']) AND $_FILES['image'] ['name'] != NULL) { 

			$img = $_FILES['image'] ['name'];

			$img_ext=explode(".",$img); // divide filename from extension

			if ($img_ext[1]=="jpg" OR $img_ext[1]=="jpeg" OR $img_ext[1]=="JPG" OR $img_ext[1]=="JPEG" OR $img_ext[1] =="png" OR $img_ext[1] =="PNG") { // check image format

				$uploadFile2 = $absoluteurl.$img_dir."itunes_image.jpg";

				if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile2))
				{
					$PG_mainbody .= "<div class=\"alert alert-error\">".$L_imgsent."</div>"; // If upload is successful.
				}
				else { //if upload NOT successful
					$PG_mainbody .= "<div class=\"alert alert-error\">".$L_imgnotsent."</div>";
					//	$temporaneo= $_FILES['image']['tmp_name'];
				}
			} else { // if image extension is NOT valid
				$PG_mainbody .= "<div class=\"alert alert-error\">".$L_imgnotvalidext." ".$L_imgkeep."</div>";
				$PG_mainbody .= "<p>$L_image_itunes_param</p>";
			}
		}
		
		include ("$absoluteurl"."core/admin/createconfig.php"); //regenerate config.php

		$PG_mainbody .= '<br /><br /><p>'.$L_itunescatchanged.'</p>';

		//REGENERATE FEED ...
		include ("$absoluteurl"."core/admin/feedgenerate.php");

	}
	else { // if action not set

		  $itunes_cats = array(
			"Arts",
			"Arts:Design",
			"Arts:Fashion &amp; Beauty",
			"Arts:Food",
			"Arts:Literature",
			"Arts:Performing Arts",
			"Arts:Visual Arts",
			"Business",
			"Business:Business News",
			"Business:Careers",
			"Business:Investing",
			"Business:Management &amp; Marketing",
			"Business:Shopping",
			"Comedy",
			"Education",
			"Education:Education Technology",
			"Education:Higher Education",
			"Education:K-12",
			"Education:Language Courses",
			"Education:Training",
			"Games &amp; Hobbies",
			"Games &amp; Hobbies:Automotive",	
			"Games &amp; Hobbies:Aviation",
			"Games &amp; Hobbies:Hobbies",
			"Games &amp; Hobbies:Other Games",
			"Games &amp; Hobbies:Video Games",
			"Government &amp; Organizations",
			"Government &amp; Organizations:Local",
			"Government &amp; Organizations:National",
			"Government &amp; Organizations:Non-Profit",
			"Government &amp; Organizations:Regional",
			"Health",
			"Health:Alternative Health",
			"Health:Fitness &amp; Nutrition",
			"Health:Self-Help",
			"Health:Sexuality",
			"Kids &amp; Family",
			"Music",
			"News &amp; Politics",
			"Religion &amp; Spirituality",
			"Religion &amp; Spirituality:Buddhism",
			"Religion &amp; Spirituality:Christianity",
			"Religion &amp; Spirituality:Hinduism",
			"Religion &amp; Spirituality:Islam",
			"Religion &amp; Spirituality:Judaism",
			"Religion &amp; Spirituality:Other",
			"Religion &amp; Spirituality:Spirituality",
			"Science &amp; Medicine",
			"Science &amp; Medicine:Medicine",
			"Science &amp; Medicine:Natural Sciences",
			"Science &amp; Medicine:Social Sciences",
			"Society &amp; Culture",
			"Society &amp; Culture:History",
			"Society &amp; Culture:Personal Journals",
			"Society &amp; Culture:Philosophy",
			"Society &amp; Culture:Places &amp; Travel",
			"Sports &amp; Recreation",
			"Sports &amp; Recreation:Amateur",
			"Sports &amp; Recreation:College &amp; High School",
			"Sports &amp; Recreation:Outdoor",
			"Sports &amp; Recreation:Professional",
			"Technology",
			"Technology:Gadgets",
			"Technology:Tech News",
			"Technology:Podcasting",
			"Technology:Software How-To",
			"TV &amp; Film");



		// define variables
		$arr = NULL;
		$arrid = NULL;
		$n = 0;

		$PG_mainbody .=	'<form name="'.$L_itunescategories.'" method="POST" enctype="multipart/form-data" action="?p=admin&do=itunescat&action=change">';


		## CATEGORY 1

		$PG_mainbody .= "<p><strong>".$L_itunes_cat1."</strong></p>";
		$PG_mainbody .= '<select name="category1" class="form-control">';


		natcasesort($itunes_cats); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($itunes_cats as $key => $val) {

			if ( $val != "" ) { //just for 1st category - cannot be empty

				$PG_mainbody .= '
					<option value="' . $val . '"';

				if ($itunes_category[0] == $val) {
					$PG_mainbody .= ' selected';
				}

				$PG_mainbody .= '>' . $val . '</option>';	
			}
		}
		$PG_mainbody .= '</select>';	



		## CATEGORY 2

		$PG_mainbody .= "<br /><br /><p><b>$L_itunes_cat2</b></p>";
		$PG_mainbody .= '<select name="category2" class="form-control">';


		foreach ($itunes_cats as $key => $val) {

			$PG_mainbody .= '
				<option value="' . $val . '"';

			if ($itunes_category[1] == $val) {
				$PG_mainbody .= ' selected';
			}

			$PG_mainbody .= '>' . $val . '</option>';	
		}
		$PG_mainbody .= '</select>';


		## CATEGORY 3

		$PG_mainbody .= "<br /><br /><p><b>$L_itunes_cat3</b></p>";
		$PG_mainbody .= '<select name="category3" class="form-control">';


		foreach ($itunes_cats as $key => $val) {

			$PG_mainbody .= '<option value="' . $val . '"';

			if ($itunes_category[2] == $val) {
				$PG_mainbody .= ' selected';
			}

			$PG_mainbody .= '>' . $val . '</option>';	
		}
		$PG_mainbody .= '</select>';

		$PG_mainbody .= '</fieldset>';
		
		$PG_mainbody .= '<h3>'.$L_image_itunes.'</h3>
		<p>'.$L_podcastimg.'</p>';
		
		$PG_mainbody .= '
			<fieldset><legend>'.$L_imagecurrent.'</legend>
			<p>	<img src="'.$url.$img_dir.'itunes_image.jpg" width="300" height="300" alt="'.$L_image_itunes.'" />
			</p><br />

			<div class="well">	
			<form name="'.$L_image_itunes.'" method="POST" enctype="multipart/form-data" action="?p=admin&do=itunesimg&action=change">

			<p><label for="'.$L_image_itunes.'">'.$L_imagenew.'</label></p>
			<input name="image" type="file">
			
			</div>
			<p>'.$L_image_itunes_param.'</p>
			</fieldset>
			';
		
		$PG_mainbody .= '<div class="form-actions">
			<input type="submit" name="'.$L_send.'" value="'.$L_send.'" class="btn btn-primary"></div>';
	}

}

?>