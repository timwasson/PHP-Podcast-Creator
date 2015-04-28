<?php
############################################################
# PHP Podcast Creator
#
# Mostly by Tim Wasson
# 
# This is Free Software released under the GNU/GPL License.
############################################################
//Changing over to managing these variables through sessions. 
session_start();
$absoluteurl = realpath("./");
if(strpos(PHP_OS, "WIN") !== false) { //if we are in a windows environment...
  $absoluteurl = str_replace("\setup", "", $absoluteurl); // works on Win32 hosts (thanks to Hans Fraiponts for this fix)
  $absoluteurl .= "\\";
}
else{ // non windows server
  $absoluteurl = str_replace("/setup", "", $absoluteurl); //the file seth_path.php is incorporated in index.php so the sub-folder /setup is not considered - this could not work if someone renames podcast generator root folder as “setup”
  $absoluteurl .= "/";
}
if (file_exists($absoluteurl."config.php")) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ../"); // open homepage
	exit;
}
?>

<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>PHP Podcast Creator Setup</title>

	<META NAME="ROBOTS" CONTENT="NOINDEX,FOLLOW" />

	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">

</head>

<body>
<div class="container">
	<div class="row">
		<h1>PHP Podcast Creator Setup</h1>
	</div>
	<div class="row">
		<div class="span12">
  		<?php
    		if(empty($_GET['step'])) {
      ?>
			<h2>Welcome!</h2>
			<p>You're just one step away from getting your podcast set up. Just fill in the stuff below.</p>

    	<form method="post" action="index.php?step=done">
          	
    	<h2>mySQL Setup</h2>
      <p>mySQL is used for tracking episode and feed downloads.</p>
    
    	<fieldset>
        <legend>mySQL Login Information</legend>
      	
        	<label>Server</label>
        	<input type="text" name="server" value="localhost">
        	
        	<label>Database</label>
        	<input type="text" name="database" placeholder="Database">
        	
        	<label>Database User</label>
        	<input type="text" name="db_user" placeholder="Database User">
        	
        	<label>Database Password</label>
        	<input type="password" name="db_pass" placeholder="Database Password">
    	</fieldset>
    	
    	<h2>Username & Password</h2>
    	<label for="username">Username</label>
    	<input name="username" id="username" type="text" size="20" maxlength="20" value=""><hr>
    	<label for="password">Password</label>
    	<input type="password" id="password" name="password" size="20" maxlength="20"><br />
    	<label for="password2">Confirm Password</label>
    	<input type="password" id="password2" name="password2" size="20" maxlength="20">
    	
    	<div class="form-actions">
    		<input type="submit" class="btn btn-primary">
    	</div>
    	</form>
    	<?
      	}
      // End empty state
      ?>
      <?php 
  		  // done stuff
        if($_GET['step'] == "done") {				
        if (file_exists("../config.php")) { //if config.php already exists stop the script
        	echo "<font color=\"red\">$SL_configexists</font><br />$SL_configdelete";
        	exit;
        }
        
        // Verify mySQL login information. 
        if($_POST['database'] != "" AND $_POST['db_user'] != "" AND $_POST["db_pass"] != "") {
        	$link = new mysqli($_POST['server'],$_POST['db_user'],$_POST['db_pass'],$_POST['database']);
        	/* check connection */
        	if (mysqli_connect_errno()) {
        	    die("<p>Connect failed: \n". mysqli_connect_error()."</p><p>Go back and try again.</p>");
        	   //exit();
        	}
        	// Logged into mySQL. Create the tracking tables.
        	$query = "CREATE TABLE IF NOT EXISTS `feed` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `ip` varchar(50) NOT NULL,
            `host` varchar(50) NOT NULL,
            `ref` varchar(50) NOT NULL,
            `agent` varchar(500) NOT NULL,
            `file` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30222 ;";
        	
        	$query2 = "CREATE TABLE IF NOT EXISTS `media` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `ip` varchar(50) NOT NULL,
            `host` varchar(50) NOT NULL,
            `ref` varchar(500) NOT NULL,
            `agent` varchar(500) NOT NULL,
            `file` varchar(100) NOT NULL,
            `isdupe` int(11) NOT NULL,
            `lat` varchar(100) NOT NULL,
            `long` varchar(100) NOT NULL,
            `city` varchar(100) NOT NULL,
            `region` varchar(100) NOT NULL,
            `country` varchar(100) NOT NULL,
            `zip` varchar(10) NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18563 ;";
        	
        	//execute the query.
        	$result = $link->query($query)or die("For some reason, I could not create the feed tracking database.");
        	
        	$result = $link->query($query2)or die("For some reason, I could not create the episode tracking database.");
        }
        
        $currenturl = str_replace("?step=5", "", $currenturl); //set script URL to be saved in the config.php file
        $configfiletocreate = '
          <?php
          #################################################################
          # Podcast Generator
          # Improved by Tim Wasson
          # developed by Alberto Betella
          #
          # Config.php file created automatically - v.'.$podcastgen_version.'
          $podcastgen_version = "'.$podcastgen_version.'"; // Version
          $scriptlang = "en";
          $url = "'.$currenturl.'"; // Complete URL of the script (Trailing slash REQUIRED)
          $absoluteurl = "'.addslashes($absoluteurl).'"; // Absolute path on the server (Trailing slash REQUIRED)
          $theme_path = "themes/appview/";
          $username = "'.$_POST['username'].'";
          $userpassword = "'.md5($_POST['password']).'";
          $max_upload_form_size = "104857600"; //e.g.: "30000000" (about 30MB)
          $upload_dir = "media/"; // "media/" the default folder (Trailing slash required). Set chmod 755
          $img_dir = "images/";  // (Trailing slash required). Set chmod 755
          $feed_dir = ""; // Where to create feed.xml (empty value = root directory). Set chmod 755
          $max_recent = 3; // How many file to show in the home page
          $recent_episode_in_feed = "All"; // How many file to show in the XML feed (1,2,5 etc.. or "All")
          $episodeperpage = 10;
          $dateformat = "d-m-Y"; // d-m-Y OR m-d-Y OR Y-m-d 
          $strictfilenamepolicy = "yes"; // strictly rename files (just characters A to Z and numbers) 
          $firsttimehere = "yes";
          ###################
          # XML Feed elements
          # The followings specifications will be included in your podcast "feed.xml" file.
          $podcast_title = "'.$SL_podcast_title.'";
          $podcast_subtitle = "'.$SL_podcast_subtitle.'";
          $podcast_description = "'.$SL_podcast_description.'";
          $author_name = "Test"; 
          $author_email = "test@nospam.com"; 
          $itunes_category[0] = "Arts"; // iTunes categories (mainCategory:subcategory)
          $itunes_category[1] = "";
          $itunes_category[2] = "";
          $link = $url."?p=episode&amp;name="; // permalink URL of single episode (appears in the <link> and <guid> tags in the feed)
          $feed_language = "'.$setuplang.'"; // Language used in the XML feed (can differ from the script language).
          $copyright = "'.$SL_copyright.'"; // Copyright notice
          $feed_encoding = "utf-8"; // Feed Encoding (e.g. "iso-8859-1", "utf-8"). UTF-8 is strongly suggested
          $explicit_podcast = "no"; //does your podcast contain explicit language? ("yes", "no" or "clean")
          // END OF CONFIGURATION
          // beginning of mySQL integration
          $db_user = "'.$_POST['db_user'].'";	// The user that has access to your database
          $db_pass = "'.$_POST['db_pass'].'";	// The password for the user that has access to your database
          $database = "'.$_POST['database'].'";	// Database Obviously
          $server = "'.$_POST['server'].'"; // Most of the time this is localhost
          ?>';
        $createcf = fopen("$absoluteurl"."config.php",'w'); //open config file
        fwrite($createcf,$configfiletocreate); //write content into the config file
        fclose($createcf);
        ?>
        <h1>All done!</h1>
        <?
      }
  		?>
		</div>
	</div>
	
	<div class="row">
		<div class="span12">
			Powered by <a href="https://github.com/timwasson/PHP-Podcast-Creator" title="PHP Podcast Creator: open source podcast publishing solution" class="label label-info">PHP Podcast Creator</a>, an open source podcast publishing solution.
		</div>
	</div>
</div>
</body>
</html>