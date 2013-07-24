<?
// Copyright 2013, Tim Wasson, PHP Podcast Creator
// An extremely basic way to keep track of podcast subscribers. 

include("../config.php"); 

mysql_connect($server,$db_user,$db_pass);
		
// select the database
mysql_select_db($database) or die ("Could not select database because ".mysql_error());

$sql = "INSERT INTO feed (
	id, 
	ip, 
	host, 
	ref,
	agent
	) VALUES (
	'',
	'".$_SERVER['REMOTE_ADDR']."',
	'".$_SERVER['REMOTE_HOST']."',
	'".$_SERVER['HTTP_REFERER']."',
	'".$_SERVER['HTTP_USER_AGENT']."'
	)";
$result = mysql_query($sql);

if(!$result)
{
	echo "Oops".mysql_error();
} else {
	$feed = file_get_contents($absoluteurl."feed.xml");

	// Log the people's information. 

	echo $feed;
}






?>