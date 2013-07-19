<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# Improved by Tim Wasson
# 
# This is Free Software released under the GNU/GPL License.
############################################################

include ('set_path.php'); //define URL and absolute path on the server
include ('../core/admin/VERSION.php'); //define Podcast Generator Version

include ('checkconfigexistence.php');

################ LAGUAGES: 1/2
//assigned below in english before language choice, when language has been chosen they will be read in the language files and the below variables "overwritten" (see 2/2)
$SL_pg = "Podcast Generator";
$SL_pgsetup = "- Setup"; 
$SL_welcome = "Welcome!";
$SL_next = "Next";
################ 

################ LAGUAGES: 2/2
if (isset($_POST['setuplanguage'])) {

  $setuplang = $_POST['setuplanguage'];	
	//	echo "lang/setup_".$setuplang;

	if (file_exists("lang/setup_".$setuplang.".php")) {
		include ("lang/setup_".$setuplang.".php");
	}
}
################ 

$SL_pgsetuptext = $SL_pg." ".$podcastgen_version." ".$SL_pgsetup;

?>

<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $SL_pgsetuptext; ?></title>

	<META NAME="ROBOTS" CONTENT="NOINDEX,FOLLOW" />

	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">

</head>

<body>
<div class="container">
	<div class="row">
		<h1><?php echo $SL_pgsetuptext; ?></h1>
	</div>
	<div class="row">
		<div class="span12">
			<div class="progress">
			<?php
			if (!isset($_GET['step'])){
				echo "<div class=\"bar\" style=\"width: 20%;\">Step 1/5</div>";
			}
			elseif (isset($_GET['step']) AND $_GET['step'] == 2) {
				echo "<div class=\"bar\" style=\"width: 40%;\">Step 2/5</div>";
			}
			elseif (isset($_GET['step']) AND $_GET['step'] == 3) {
				echo "<div class=\"bar\" style=\"width: 60%;\">Step 3/5</div>";
			}
			elseif (isset($_GET['step']) AND $_GET['step'] == 4) {
				echo "<div class=\"bar\" style=\"width: 80%;\">Step 4/5</div>";
			}
			elseif (isset($_GET['step']) AND $_GET['step'] == 5) {
				echo "<div class=\"bar\" style=\"width: 100%;\">Step 5/5</div>";
			}
			?>		
					
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span12">
			<h2><?php echo $SL_welcome; ?></h2>
			<p>
				<?php
					if (isset($SL_welcometext) AND $SL_welcometext != NULL) {
						echo $SL_welcometext;
					} 
				?>	
			</p>
	
				<?php
				########## INCLUDE INSTALLATION STEPS
				
				if (!isset($_GET['step'])) {
					include ('step1.php');
				} elseif (isset($_GET['step']) AND $_GET['step'] == 2) {
					include ('step2.php');
				} elseif (isset($_GET['step']) AND $_GET['step'] == 3) {
					include ('step3.php');
				} elseif (isset($_GET['step']) AND $_GET['step'] == 4) {
					include ('step4.php');
				} elseif (isset($_GET['step']) AND $_GET['step'] == 5) {
					include ('step5.php');
				}
	
				?>
		</div>
	</div>
	
	<div class="row">
		<div class="span12">
			<span class="label label-warning">Podcast Generator</span> Powered by <a href="https://github.com/timwasson/Podcast-Generator" title="Podcast Generator: open source podcast publishing solution">Podcast Generator</a>, an open source podcast publishing solution.
		</div>
	</div>
</div>
</body>
</html>
