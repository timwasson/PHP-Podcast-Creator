<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# Improved by Tim Wasson
# 
# This is Free Software released under the GNU/GPL License.
############################################################

include ('checkconfigexistence.php');

$PG_mainbody = NULL; //define
$PG_mainbody .= '
	<h3>'.$SL_checkperm.'</h3>
	<p>'.$SL_step1.'</p>
	';

include	('set_permissions.php');

// Verify mySQL login information. 
if($_POST['database'] != "" AND $_POST['db_user'] != "" AND $_POST["db_pass"] != "") {
	$link = new mysqli($_POST['server'],$_POST['db_user'],$_POST['db_pass'],$_POST['database']);

	/* check connection */
	if (mysqli_connect_errno()) {
	    die("<p>Connect failed: \n". mysqli_connect_error()."</p><p>Go back and try again.</p>");
	   //exit();
	}
	// Logged into mySQL. Create the tracking tables.
	$query = "CREATE TABLE `feed` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  `ip` varchar(50) NOT NULL,
	  `host` varchar(50) NOT NULL,
	  `ref` varchar(50) NOT NULL,
	  `agent` varchar(500) NOT NULL,
	  `file` varchar(100) NOT NULL,
	  PRIMARY KEY (`id`)
	)";
	
	$query2 = "CREATE TABLE `media` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  `ip` varchar(50) NOT NULL,
	  `host` varchar(50) NOT NULL,
	  `ref` varchar(500) NOT NULL,
	  `agent` varchar(500) NOT NULL,
	  `file` varchar(100) NOT NULL,
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `time` (`time`,`ip`,`file`),
	  UNIQUE KEY `time_2` (`time`,`ip`,`host`,`ref`,`agent`,`file`),
	  UNIQUE KEY `time_3` (`time`,`ip`)
	)";
	
	//execute the query.
	$result = $link->query($query)or die("For some reason, I could not create the feed tracking database.");
	
	$result = $link->query($query2)or die("For some reason, I could not create the episode tracking database.");
	
	// Store DB login information in a session. 
	$_SESSION['db_user'] = $_POST['db_user'];
	$_SESSION['db_pass'] = $_POST['db_pass'];
	$_SESSION['database'] = $_POST['database'];
	$_SESSION['server'] = $_POST['server'];
	
} else {
	die("You have to fill in your mySQL Login information.");
}


echo $PG_mainbody;

?>