<?php
/**
 * 
 */
class rssFeed {
	/**
	 * 
	 */
	function rss($feed) {
		/**
		 * ideas from
		 * http://www.phpeveryday.com/articles/PHP-XML-Adding-XML-Nodes-P414.html
		 */
	$host = 'http://www.tuffers.co.uk/wessex/';
	$xml = new DOMDocument();
	$xml->formatOutput = true;
	// get document element
	$rss = $xml->createElement('rss');
	$rss->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd/');
	$rss->setAttribute('version', '2.0');
	//xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0"
	$root   = $xml->appendChild($rss); 
	$channel = $xml->createElement('channel');
	$root->appendChild($channel);
	
	$item = $xml->createElement("title");
	$itemText = $xml->createTextNode('Wessex Male Choir Rehearsal Recordings');
	$item->appendChild($itemText);
	$channel->appendChild($item);
	
	$item = $xml->createElement("link");
	$itemText = $xml->createTextNode('http://www.tuffers.co.uk/wessex/recordings.php');
	$item->appendChild($itemText);
	$channel->appendChild($item);
	
	$item = $xml->createElement("language");
	$itemText = $xml->createTextNode('en-gb');
	$item->appendChild($itemText);
	$channel->appendChild($item);
		
	$item = $xml->createElement("copyright");
	$itemText = $xml->createTextNode('2012');
	$item->appendChild($itemText);
	$channel->appendChild($item);
	
	$item = $xml->createElement("itunes:subtitle");
	$itemText = $xml->createTextNode('Rehearsal Recordings');
	$item->appendChild($itemText);
	$channel->appendChild($item);
	
	$item = $xml->createElement("itunes:author");
	$itemText = $xml->createTextNode('Wessex Male Choir');
	$item->appendChild($itemText);
	$channel->appendChild($item);
		
	$item = $xml->createElement("itunes:summary");
	$itemText = $xml->createTextNode('WMC Rehearsal Recordings for Personal Choir Practice.');
	$item->appendChild($itemText);
	$channel->appendChild($item);
	
	$item = $xml->createElement("description");
	$itemText = $xml->createTextNode('Here are some MP3 recordings of the Wessex Male Choir rehearsals that you can download for your own personal use (practice makes perfect!).');
	$item->appendChild($itemText);
	$channel->appendChild($item);
	
	$owner = $xml->createElement("itunes:owner");
	$channel->appendChild($owner);
	
	$item = $xml->createElement("itunes:name");
	$itemText = $xml->createTextNode('Wessex Male Choir');
	$item->appendChild($itemText);
	$owner->appendChild($item);
	
	$item = $xml->createElement("itunes:email");
	$itemText = $xml->createTextNode('membership@wessexmalechoir.co.uk');
	$item->appendChild($itemText);
	$owner->appendChild($item);
	
	$item = $xml->createElement("itunes:image");
	$itemText = $xml->createTextNode('http://www.wessexmalechoir.co.uk/images/logo2.jpg');
	$item->appendChild($itemText);
	$channel->appendChild($item);
	
	$item = $xml->createElement("itunes:category");
	$itemText = $xml->createTextNode('Education');
	$item->appendChild($itemText);
	$channel->appendChild($item);
	
	$item = $xml->createElement("itunes:category");
	$itemText = $xml->createTextNode('Music');
	$item->appendChild($itemText);
	$channel->appendChild($item);

	foreach($feed as $entry) {
		$url = $host .$entry['enclosure'];
		$length = ltrim($entry['length']);
		$length = str_replace(",", "", $length);
		$length2 = substr($length, 0);
		//echo $length . ' ' .$length2  ."\n";
		
		
		
		$longSummary = $entry['title'] . ' ' . $entry['summary'];
		$item = $xml->createElement("item");

		$title = $xml->createElement("title");
		$titleText= $xml->createTextNode($entry['title']);
		$title->appendChild($titleText);

		$author = $xml->createElement("itunes:author");
		$authorText= $xml->createTextNode('Wessex Mail Choir');
		$author->appendChild($authorText);

		$subtitle = $xml->createElement("itunes:subtitle");
		$subTitleText= $xml->createTextNode($entry['title']);
		$subtitle->appendChild($subTitleText);

		$summary = $xml->createElement("itunes:summary");
		$summaryText= $xml->createTextNode($longSummary);
		$summary->appendChild($summaryText);

		$enclosure = $xml->createElement("enclosure");
		$enclosure->setAttribute('url', $url);
		$enclosure->setAttribute('length', $length);
		$enclosure->setAttribute('type', "audio/mp3");
		
		$pubDate = $xml->createElement("pubDate");
		$pubDateText= $xml->createTextNode($entry['pubDate']);
		$pubDate->appendChild($pubDateText);
		
		$duration = $xml->createElement("itunes:duration");
		$durationText= $xml->createTextNode($entry['duration']);
		$duration->appendChild($durationText);

		$keywords = $xml->createElement("itunes:keywords");
		$keywordsText= $xml->createTextNode('Music, Training');
		$keywords->appendChild($keywordsText);

		$guid = $xml->createElement("guid");
		$guidText= $xml->createTextNode($url);
		$guid->appendChild($guidText);
		
		$item->appendChild($title);
		$item->appendChild($author);
		$item->appendChild($subtitle);
		$item->appendChild($summary);
		$item->appendChild($enclosure);
		$item->appendChild($pubDate);
		$item->appendChild($duration);
		$item->appendChild($keywords);
		$item->appendChild($guid);
		
		/**
		 * add each item
		 */
		$channel->appendChild($item);
		
		}
		$explicit = $xml->createElement('itunes:explicit');
		$explicitText = $xml->createTextNode('no');
		$explicit->appendChild($explicitText);
		$channel->appendChild($explicit);

		$item = $xml->createElement("itunes:block");
		$itemText = $xml->createTextNode('no');
		$item->appendChild($itemText);
		$channel->appendChild($item);

		//print ($xml);
		$xml->save('rssout.xml');
	}
}
function fetchPage($url)
{
                $ch = curl_init (); // start cURL instance
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); // this tells cUrl to return the data
                curl_setopt ($ch, CURLOPT_URL, $url); // set the url to download
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // follow redirects if any
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // tell cURL if the data is binary data or not
                $html = curl_exec($ch); // grabs the webpage from the internet
                curl_close ($ch); // closes the connection
                utf8_encode($html);
                
	return $html;
}
$url = 'http://www.tuffers.co.uk/wessex/recordings.php';
$str = fetchPage($url);
$doc = new DOMDocument;
@$doc->LoadHTML($str);
$headings = Array(
	'title',
	'length',
	'duration',
	'pubDate',
	'summary',
);
$xpath = new DOMXPath($doc);
$pod = Array();
$tablerows = $xpath->query("//table/tr");
if (!is_null($tablerows)) {
	foreach ($tablerows as $row) {
		$entry = Array();
		$depth = 0;
		if ($child = $row->firstChild) {
			while ($child) {
				if ($child->hasChildNodes()) {
					$kids = $child->childNodes;
					foreach($kids as $kid) {
						$attributes = $kid->attributes;
						if (!is_null($attributes)){
							foreach ($attributes as $attr){ 
                             if($attr->name == 'href'){
                                 array_push($entry['enclosure'] = $attr->value);
                             } 
                         } 
						}
					}
				}
				echo $headings[$depth];
				array_push($entry[$headings[$depth]] = $child->nodeValue);
				$depth++;
				$child = $child->nextSibling;
			}
		}
		array_push($pod, $entry);
	}
}
array_shift($pod); // don't want the title
$feed = new rssFeed;
var_dump($pod);
$feed->rss($pod);
?>
