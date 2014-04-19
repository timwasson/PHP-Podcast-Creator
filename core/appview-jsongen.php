<?php
//$url=$_GET['url'];
//echo $url;

header('Content-Type: application/json');
$feed = new DOMDocument();
$feed->load("http://whiskeypodcast.localhost/feed.xml");
$json = array();

$json['title'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
$json['description'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
$json['link'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('link')->item(0)->firstChild->nodeValue;
$json['bimage'] = $feed->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image')->item(0)->getAttribute('href');

$json['email'] = $feed->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'email')->item(0)->firstChild->nodeValue;

$items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');

$json['item'] = array();
$i = 0;


foreach($items as $item) {

  $title = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
  $description = $item->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'summary')->item(0)->firstChild->nodeValue;
  if($item->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image')->item(0)) {
  $image = $item->getElementsByTagNameNS('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image')->item(0)->getAttribute('href');
  } else {
  $image = $json['bimage'];
  }
  
  $pubDate = $item->getElementsByTagName('pubDate')->item(0)->firstChild->nodeValue;
  $guid = $item->getElementsByTagName('guid')->item(0)->firstChild->nodeValue;
  $enclosure = $item->getElementsByTagName('enclosure')->item(0)->getAttribute('url');
  
  $json['item'][$i]['title'] = $title;
  $json['item'][$i]['description'] = str_replace("\n", "<br />",$description);
  $json['item'][$i]['pubdate'] = date('F jS\, Y',strtotime($pubDate));
  $json['item'][$i]['guid'] = $guid;   
  $json['item'][$i]['enclosure'] = $enclosure;
  $json['item'][$i]['image'] = $image;
  $json['item'][$i]['pid'] = $i;
  //$json['item'][$i]['brief'] = substr($description, 0, 50);
  
  $i++;
  
  //echo $enclosure;
    
}


echo json_encode($json);

?>