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
					$PG_mainbody .= "<p><b>$L_imgsent</b></p>"; // If upload is successful.
				}
				else { //if upload NOT successful

					$PG_mainbody .= "<p><b>$L_imgnotsent</b></p>";
					//	$temporaneo= $_FILES['image']['tmp_name'];

				}

			} else { // if image extension is NOT valid

				$PG_mainbody .= "<p><b>$L_imgnotvalidext $L_imgkeep</b></p>";
				$PG_mainbody .= "<p>$L_image_itunes_param</p>";
				$PG_mainbody .= '<br />
					<form>
					<INPUT TYPE="button" VALUE='.$L_back.' onClick="history.back()">
					</form>';
			}

		}
		
		include ("$absoluteurl"."core/admin/createconfig.php"); //regenerate config.php

		$PG_mainbody .= '<br /><br /><p>'.$L_itunescatchanged.'</p>';

		//REGENERATE FEED ...
		include ("$absoluteurl"."core/admin/feedgenerate.php");

	}
	else { // if action not set

		include ("$absoluteurl"."components/xmlparser/loadparser.php");
		
		//if (file_exists("$absoluteurl"."components/itunes_categories/itunes_categories.xml")) {

			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<PodcastGenerator>
			
			<category>
				<id></id>
				<description></description>
			</category>
			<category>
				<id>Arts</id>
				<description>Arts</description>
			</category>
			<category>
				<id>Arts:Design</id>
				<description>Arts:Design</description>
			</category>
			<category>
				<id>Arts:Fashion &amp; Beauty</id>
				<description>Arts:Fashion &amp; Beauty</description>
			</category>
			<category>
				<id>Arts:Food</id>
				<description>Arts:Food</description>
			</category>
			<category>
				<id>Arts:Literature</id>
				<description>Arts:Literature</description>
			</category>
			<category>
				<id>Arts:Performing Arts</id>
				<description>Arts:Performing Arts</description>
			</category>
			<category>
				<id>Arts:Visual Arts</id>
				<description>Arts:Visual Arts</description>
			</category>
		
			<category>
				<id>Business</id>
				<description>Business</description>
			</category>
			<category>
				<id>Business:Business News</id>
				<description>Business:Business News</description>
			</category>
			<category>
				<id>Business:Careers</id>
				<description>Business:Careers</description>
			</category>
			<category>
				<id>Business:Investing</id>
				<description>Business:Investing</description>
			</category>
			<category>
				<id>Business:Management &amp; Marketing</id>
				<description>Business:Management &amp; Marketing</description>
			</category>
			<category>
				<id>Business:Shopping</id>
				<description>Business:Shopping</description>
			</category>
		
			<category>
				<id>Comedy</id>
				<description>Comedy</description>
			</category>
		
			<category>
				<id>Education</id>
				<description>Education</description>
			</category>
			<category>
				<id>Education:Education Technology</id>
				<description>Education:Education Technology</description>
			</category>
			<category>
				<id>Education:Higher Education</id>
				<description>Education:Higher Education</description>
			</category>
			<category>
				<id>Education:K-12</id>
				<description>Education:K-12</description>
			</category>
			<category>
				<id>Education:Language Courses</id>
				<description>Education:Language Courses</description>
			</category>
			<category>
				<id>Education:Training</id>
				<description>Education:Training</description>
			</category>
		
			<category>
				<id>Games &amp; Hobbies</id>
				<description>Games &amp; Hobbies</description>
			</category>
			<category>
				<id>Games &amp; Hobbies:Automotive</id>
				<description>Games &amp; Hobbies:Automotive</description>
			</category>
			<category>
				<id>Games &amp; Hobbies:Aviation</id>
				<description>Games &amp; Hobbies:Aviation</description>
			</category>
			<category>
				<id>Games &amp; Hobbies:Hobbies</id>
				<description>Games &amp; Hobbies:Hobbies</description>
			</category>
			<category>
				<id>Games &amp; Hobbies:Other Games</id>
				<description>Games &amp; Hobbies:Other Games</description>
			</category>
			<category>
				<id>Games &amp; Hobbies:Video Games</id>
				<description>Games &amp; Hobbies:Video Games</description>
			</category>
		
			<category>
				<id>Government &amp; Organizations</id>
				<description>Government &amp; Organizations</description>
			</category>
			<category>
				<id>Government &amp; Organizations:Local</id>
				<description>Government &amp; Organizations:Local</description>
			</category>
			<category>
				<id>Government &amp; Organizations:National</id>
				<description>Government &amp; Organizations:National</description>
			</category>
			<category>
				<id>Government &amp; Organizations:Non-Profit</id>
				<description>Government &amp; Organizations:Non-Profit</description>
			</category>
			<category>
				<id>Government &amp; Organizations:Regional</id>
				<description>Government &amp; Organizations:Regional</description>
			</category>
		
			<category>
				<id>Health</id>
				<description>Health</description>
			</category>
			<category>
				<id>Health:Alternative Health</id>
				<description>Health:Alternative Health</description>
			</category>
			<category>
				<id>Health:Fitness &amp; Nutrition</id>
				<description>Health:Fitness &amp; Nutrition</description>
			</category>
			<category>
				<id>Health:Self-Help</id>
				<description>Health:Self-Help</description>
			</category>
			<category>
				<id>Health:Sexuality</id>
				<description>Health:Sexuality</description>
			</category>
		
			<category>
				<id>Kids &amp; Family</id>
				<description>Kids &amp; Family</description>
			</category>
		
			<category>
				<id>Music</id>
				<description>Music</description>
			</category>
		
			<category>
				<id>News &amp; Politics</id>
				<description>News &amp; Politics</description>
			</category>
		
			<category>
				<id>Religion &amp; Spirituality</id>
				<description>Religion &amp; Spirituality</description>
			</category>
			<category>
				<id>Religion &amp; Spirituality:Buddhism</id>
				<description>Religion &amp; Spirituality:Buddhism</description>
			</category>
			<category>
				<id>Religion &amp; Spirituality:Christianity</id>
				<description>Religion &amp; Spirituality:Christianity</description>
			</category>
			<category>
				<id>Religion &amp; Spirituality:Hinduism</id>
				<description>Religion &amp; Spirituality:Hinduism</description>
			</category>
			<category>
				<id>Religion &amp; Spirituality:Islam</id>
				<description>Religion &amp; Spirituality:Islam</description>
			</category>
			<category>
				<id>Religion &amp; Spirituality:Judaism</id>
				<description>Religion &amp; Spirituality:Judaism</description>
			</category>
			<category>
				<id>Religion &amp; Spirituality:Other</id>
				<description>Religion &amp; Spirituality:Other</description>
			</category>
			<category>
				<id>Religion &amp; Spirituality:Spirituality</id>
				<description>Religion &amp; Spirituality:Spirituality</description>
			</category>
		
			<category>
				<id>Science &amp; Medicine</id>
				<description>Science &amp; Medicine</description>
			</category>
			<category>
				<id>Science &amp; Medicine:Medicine</id>
				<description>Science &amp; Medicine:Medicine</description>
			</category>
			<category>
				<id>Science &amp; Medicine:Natural Sciences</id>
				<description>Science &amp; Medicine:Natural Sciences</description>
			</category>
			<category>
				<id>Science &amp; Medicine:Social Sciences</id>
				<description>Science &amp; Medicine:Social Sciences</description>
			</category>
		
			<category>
				<id>Society &amp; Culture</id>
				<description>Society &amp; Culture</description>
			</category>
			<category>
				<id>Society &amp; Culture:History</id>
				<description>Society &amp; Culture:History</description>
			</category>
			<category>
				<id>Society &amp; Culture:Personal Journals</id>
				<description>Society &amp; Culture:Personal Journals</description>
			</category>
			<category>
				<id>Society &amp; Culture:Philosophy</id>
				<description>Society &amp; Culture:Philosophy</description>
			</category>
			<category>
				<id>Society &amp; Culture:Places &amp; Travel</id>
				<description>Society &amp; Culture:Places &amp; Travel</description>
			</category>
		
			<category>
				<id>Sports &amp; Recreation</id>
				<description>Sports &amp; Recreation</description>
			</category>
			<category>
				<id>Sports &amp; Recreation:Amateur</id>
				<description>Sports &amp; Recreation:Amateur</description>
			</category>
			<category>
				<id>Sports &amp; Recreation:College &amp; High School</id>
				<description>Sports &amp; Recreation:College &amp; High School</description>
			</category>
			<category>
				<id>Sports &amp; Recreation:Outdoor</id>
				<description>Sports &amp; Recreation:Outdoor</description>
			</category>
			<category>
				<id>Sports &amp; Recreation:Professional</id>
				<description>Sports &amp; Recreation:Professional</description>
			</category>
		
			<category>
				<id>Technology</id>
				<description>Technology</description>
			</category>
			<category>
				<id>Technology:Gadgets</id>
				<description>Technology:Gadgets</description>
			</category>
			<category>
				<id>Technology:Tech News</id>
				<description>Technology:Tech News</description>
			</category>
			<category>
				<id>Technology:Podcasting</id>
				<description>Technology:Podcasting</description>
			</category>
			<category>
				<id>Technology:Software How-To</id>
				<description>Technology:Software How-To</description>
			</category>
		
			<category>
				<id>TV &amp; Film</id>
				<description>TV &amp; Film</description>
			</category>
		
		</PodcastGenerator>";
		//Set up the parser object
		$parser = new XMLParser($xml);
		
		//Parse the XML file with categories data...
		$parser->Parse();
		
		//}


		// define variables
		$arr = NULL;
		$arrid = NULL;
		$n = 0;

		foreach($parser->document->category as $singlecategory)
		{
			//echo $singlecategory->id[0]->tagData."<br>";
			//echo $singlecategory->description[0]->tagData;

			$arr[] .= $singlecategory->description[0]->tagData;
			$arrid[] .= $singlecategory->id[0]->tagData;
			$n++;
		}

		$PG_mainbody .=	'<form name="'.$L_itunescategories.'" method="POST" enctype="multipart/form-data" action="?p=admin&do=itunescat&action=change">';


		## CATEGORY 1

		$PG_mainbody .= "<p><strong>".$L_itunes_cat1."</strong></p>";
		$PG_mainbody .= '<select name="category1">';


		natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($arr as $key => $val) {

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
		$PG_mainbody .= '<select name="category2">';


		natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($arr as $key => $val) {

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
		$PG_mainbody .= '<select name="category3">';


		natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

		foreach ($arr as $key => $val) {

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