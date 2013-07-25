<?php

	#################################################################
	# PHP Podcast Creator
	# developed by Tim Wasson
	#
	# Config.php file created automatically - v..1


	$podcastgen_version = ".1"; // Version

	$scriptlang = "en";

	$url = "http://pcg.localhost:904/"; // Complete URL of the script (Trailing slash REQUIRED)

	$absoluteurl = "/Users/twasson/Documents/Projects/podcastgen/Podcast Generator/"; // Absolute path on the server (Trailing slash REQUIRED)

	$theme_path = "themes/bootstrap/";

	$username = "timwasson";

	$userpassword = "88cd3ac5ef7c7e6ebe1e8f409a95ee1a";

	$max_upload_form_size = "1048576000"; //e.g.: "30000000" (about 30MB)

	$upload_dir = "media/"; // "media/" the default folder (Trailing slash required). Set chmod 755

	$img_dir = "images/";  // (Trailing slash required). Set chmod 755

	$feed_dir = ""; // Where to create feed.xml (empty value = root directory). Set chmod 755

	$max_recent = 20; // How many file to show in the home page

	$recent_episode_in_feed = "All"; // How many file to show in the XML feed (1,2,5 etc.. or "All")

	$episodeperpage = 10;

	$dateformat = "F jS, Y"; // d-m-Y OR m-d-Y OR Y-m-d 

	$strictfilenamepolicy = "yes"; // strictly rename files (just characters A to Z and numbers) 

	###################
	# XML Feed elements
	# The followings specifications will be included in your podcast "feed.xml" file.


	$podcast_title = "Atomicast";

	$podcast_subtitle = "Useless, totally useless crap.";

	$podcast_description = "The world's most useless podcast ever made.";

	$author_name = "Atomicast"; 

	$author_email = "mail@atomicast.com"; 

	$itunes_category[0] = "Comedy"; // iTunes categories (mainCategory:subcategory)
	$itunes_category[1] = "Society&Culture";
	$itunes_category[2] = "TV&Film";

	$link = $url."episode/"; // permalink URL of single episode (appears in the <link> and <guid> tags in the feed)

	$feed_language = "en"; // Language used in the XML feed (can differ from the script language).

	$copyright = "Copyright 2013"; // Copyright notice

	$feed_encoding = "utf-8"; // Feed Encoding (e.g. "iso-8859-1", "utf-8"). UTF-8 is strongly suggested

	$explicit_podcast = "yes"; //does your podcast contain explicit language? ("yes", "no" or "clean")

	// END OF CONFIGURATION
	$db_user = "root";	// The user that has access to your database
	$db_pass = "root";	// The password for the user that has access to your database
	$database = "pdcstgn";	

	?>