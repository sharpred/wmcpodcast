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
	$xml = new DOMDocument;
	$xml->formatOutput = true;
	$xml->load("snippet.xml") or die("Error could not load xml");;
	// get document element
	$root   = $xml->documentElement;
	$channel  = $root->firstChild; 
	$fnode = $channel->firstChild; //this is where the items need to be added before
	$xpath = new DOMXPath($xml);
	$channels = $xpath->query("//rss/channels");
	if (!is_null($channels)) {
		echo "found\n";
		foreach ($channels as $channel) {
			if ($child = $channel->firstChild) {
				echo 'still working\n';
			}
		}
	} else {
		echo "not found";
	}
	
	foreach($feed as $entry) {
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
		$summaryText= $xml->createTextNode($entry['summary']);
		$summary->appendChild($summaryText);

		$enclosure = $xml->createElement("enclosure");
		$enclosureText= $xml->createTextNode($entry['enclosure']);
		$enclosure->appendChild($enclosureText);

		$length = $xml->createElement("length");
		$lengthText= $xml->createTextNode($entry['length']);
		$length->appendChild($lengthText);

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
		$guidText= $xml->createTextNode($entry['enclosure']);
		$guid->appendChild($guidText);
		
		$item->appendChild($title);
		$item->appendChild($author);
		$item->appendChild($subtitle);
		$item->appendChild($enclosure);
		$item->appendChild($length);
		$item->appendChild($pubDate);
		$item->appendChild($duration);
		$item->appendChild($keywords);
		$item->appendChild($guid);
		
		$root->insertBefore($item, $fnode);
		
		}
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
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, false); // tell cURL if the data is binary data or not
                $html = curl_exec($ch); // grabs the webpage from the internet
                curl_close ($ch); // closes the connection
                
	return $html;
}
$host = 'http://localhost/';
$url = 'http://localhost/bootstrap/testpage.html';
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
