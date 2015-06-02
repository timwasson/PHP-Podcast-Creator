<?
// Copyright 2013, Tim Wasson, PHP Podcast Creator
// An extremely basic way to keep track of podcast subscribers. 

include("../config.php");

/* Track in Analytics */
require_once("ss-ga.class.php");


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
	  
	  // Sent to Google Analytics
	  $tostr = array("http:","/");
	  $track_url = str_replace($tostr, "", $url);
	  $ssga = new ssga($g_tracking,$track_url);
	  $ssga->set_event('Downloads', 'Download Type', $tfile);
	  
	  $ssga->set_page( $tfile );
    $ssga->set_page_title( 'Page Title' );

		$ssga->send();
		$ssga->reset();
		
		$feed = file_get_contents($absoluteurl.$folder.$tfile);
			
		if($ttable != "feed") {
			header("Content-Type: ".$fileData[1]);
			rangeDownload($absoluteurl.$folder.$tfile);
		} else { 
			echo $feed;
		}
	}

} else {
	//$feed = file_get_contents($absoluteurl.$folder.$tfile);
			
	if($ttable != "feed") {
		header("Content-Type: ".$fileData[1]);
		rangeDownload($absoluteurl.$folder.$tfile);
	}
	else {
		echo $feed;
	}
}


function rangeDownload($file) {

	$fp = @fopen($file, 'rb');

	$size   = filesize($file); // File size
	$length = $size;           // Content length
	$start  = 0;               // Start byte
	$end    = $size - 1;       // End byte
	// Now that we've gotten so far without errors we send the accept range header
	/* At the moment we only support single ranges.
	 * Multiple ranges requires some more work to ensure it works correctly
	 * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	 *
	 * Multirange support annouces itself with:
	 * header('Accept-Ranges: bytes');
	 *
	 * Multirange content must be sent with multipart/byteranges mediatype,
	 * (mediatype = mimetype)
	 * as well as a boundry header to indicate the various chunks of data.
	 */
	header("Accept-Ranges: 0-$length");
	// header('Accept-Ranges: bytes');
	// multipart/byteranges
	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	if (isset($_SERVER['HTTP_RANGE'])) {

		$c_start = $start;
		$c_end   = $end;
		// Extract the range string
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
		// Make sure the client hasn't sent us a multibyte range
		if (strpos($range, ',') !== false) {

			// (?) Shoud this be issued here, or should the first
			// range be used? Or should the header be ignored and
			// we output the whole content?
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		// If the range starts with an '-' we start from the beginning
		// If not, we forward the file pointer
		// And make sure to get the end byte if spesified
		if ($range0 == '-') {

			// The n-number of the last bytes is requested
			$c_start = $size - substr($range, 1);
		}
		else {

			$range  = explode('-', $range);
			$c_start = $range[0];
			$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
		}
		/* Check the range and make sure it's treated according to the specs.
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
		 */
		// End bytes can not be larger than $end.
		$c_end = ($c_end > $end) ? $end : $c_end;
		// Validate the requested range and return an error if it's not correct.
		if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {

			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		$start  = $c_start;
		$end    = $c_end;
		$length = $end - $start + 1; // Calculate new content length
		fseek($fp, $start);
		header('HTTP/1.1 206 Partial Content');
	}
	// Notify the client the byte range we'll be outputting
	header("Content-Range: bytes $start-$end/$size");
	header("Content-Length: $length");

	// Start buffered download
	$buffer = 1024 * 8;
	while(!feof($fp) && ($p = ftell($fp)) <= $end) {

		if ($p + $buffer > $end) {

			// In case we're only outputtin a chunk, make sure we don't
			// read past the length
			$buffer = $end - $p + 1;
		}
		set_time_limit(0); // Reset time limit for big files
		echo fread($fp, $buffer);
		flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
	}

	fclose($fp);

}






?>