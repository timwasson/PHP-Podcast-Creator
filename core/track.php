<?
// Copyright 2013, Tim Wasson, PHP Podcast Creator
// An extremely basic way to keep track of podcast subscribers. 

include("../config.php"); 

$tfile = $_GET['file'];
$folder = NULL;



if ($tfile == "feed.xml") {
	// Logging the feed is easy. 
	$ttable = "feed";
} else {
	$ttable = "media";
	$folder = "media/";
	include "supported_media.php";
	include "functions.php";
	// Get file extension
	$ext = explode(".",$tfile);
	//$ext = $ext[1];
	$fileData = checkFileType($ext[1],$podcast_filetypes,$filemimetypes);
}

// Don't log some media files. Notably when you load up the site it loads up a ton of tracking info.
if($_GET['log'] != "no") {
	mysql_connect($server,$db_user,$db_pass);
		
	// select the database
	mysql_select_db($database) or die ("Could not select database because ".mysql_error());
	
	$sql = "INSERT IGNORE INTO ".$ttable." (
		id, 
		ip, 
		host, 
		ref,
		agent,
		file
		) VALUES (
		'',
		'".$_SERVER['REMOTE_ADDR']."',
		'".$_SERVER['REMOTE_HOST']."',
		'".$_SERVER['HTTP_REFERER']."',
		'".$_SERVER['HTTP_USER_AGENT']."',
		'".$tfile."'
		)";
	$result = mysql_query($sql);
	
	if(!$result)
	{
		echo "Oops. ".mysql_error();
	} else {
	
		$feed = file_get_contents($absoluteurl.$folder.$tfile);
			
		if($ttable != "feed") {
			$size = filesize($feed);
			$time	= date('r', filemtime($feed));
			$begin = 0;
			$end = $size - 1;
			
			header("Content-Type: ".$fileData[1]); 
			header('Cache-Control: public, must-revalidate, max-age=0');
			header('Pragma: no-cache');  
			header('Accept-Ranges: bytes');
			header('Content-Length:' . (($end - $begin) + 1));
			header("Content-Range: bytes ".$begin."-".$end."/".$size);
			header("Content-Disposition: inline; filename=".$feed);
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: $time");	
			$buff = readfile($feed);
		    echo $buff;
		} else { 
			echo $feed;
		}
	}

} else {
	$feed = file_get_contents($absoluteurl.$folder.$tfile);
			
	if($ttable != "feed") {
		//echo "Hello";
		//header("Content-Type: ".$fileData[1]);
		//header("Content-Length: ".filesize($feed));
		//header("Content-Range: bytes 0-".filesize($feed)-1.'/'.filesize($feed)); 
		
		/* Set Headers */
		
		$size = filesize($feed);
		$time	= date('r', filemtime($feed));
		$begin = 0;
		$end = $size - 1;
		
		
		header("Content-Type: ".$fileData[1]); 
		header('Cache-Control: public, must-revalidate, max-age=0');
		header('Pragma: no-cache');  
		header('Accept-Ranges: bytes');
		header('Content-Length:' . (($end - $begin) + 1));
		header("Content-Range: bytes ".$begin."-".$end."/".$size);
		header("Content-Disposition: inline; filename=".$feed);
		header("Content-Transfer-Encoding: binary");
		header("Last-Modified: $time");
		$buff = readfile($feed);
	    echo $buff;
	}
	
	else {
		echo $feed;
	}
}









?>