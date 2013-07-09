#!/usr/bin/php
<?php
$host = 'http://www.wessexmp3.co.uk/wessex/';
$file = 'recordings.php';
$url = $host . $file;
require 'vendor/autoload.php';
/**
 * set up dropbox client
 */
use \Dropbox as dbx;
$file = "db.auth";
list($appInfo, $accessToken) = dbx\AuthInfo::loadFromJsonFile($file);
$dbxConfig = new dbx\Config($appInfo, "wmcpodcaster");
list($appInfo, $accessToken) = dbx\AuthInfo::loadFromJsonFile($file);
$dbxClient = new dbx\Client($dbxConfig, $accessToken);
$accountInfo = $dbxClient->getAccountInfo();

/**
 * @checkMyDate
 * checks that a date is valid and returns today's date if not
*/
function checkMyDate($date) {

	try {

		$fields = explode('-', $date);

		if (!checkdate($fields[1], $fields[0], $fields[2])) {
			
			throw new Exception('not a date');

		}
		
		return $date;


	} catch (Exception $ex) {
		
		$newdate = Date('d-m-y');
		
		var_dump($date);

		return $newdate;

	}


}

/**
 * @function createFeed
 * converts an array of mp3 files data into an itunes podcast file
 */
function createFeed($feed) {
	/**
	 * ideas from
	 * http://www.phpeveryday.com/articles/PHP-XML-Adding-XML-Nodes-P414.html
	 */
	global $host, $file, $url, $dbxClient;
	$destination = 'tmp/wmc_rehearsals.xml';
	$xml = new DOMDocument();
	$xml -> formatOutput = true;
	/**
	 * start to create the XML file
	 */
	$rss = $xml -> createElement('rss');
	$rss -> setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd/');
	$rss -> setAttribute('version', '2.0');
	$root = $xml -> appendChild($rss);
	$channel = $xml -> createElement('channel');
	$root -> appendChild($channel);

	$item = $xml -> createElement("title");
	$itemText = $xml -> createTextNode('Wessex Male Choir Rehearsal Recordings');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$item = $xml -> createElement("link");
	$itemText = $xml -> createTextNode($url);
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$item = $xml -> createElement("language");
	$itemText = $xml -> createTextNode('en-gb');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$item = $xml -> createElement("copyright");
	$itemText = $xml -> createTextNode('2012');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$item = $xml -> createElement("itunes:subtitle");
	$itemText = $xml -> createTextNode('Rehearsal Recordings');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$item = $xml -> createElement("itunes:author");
	$itemText = $xml -> createTextNode('Wessex Male Choir');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$item = $xml -> createElement("itunes:summary");
	$itemText = $xml -> createTextNode('WMC Rehearsal Recordings for Personal Choir Practice.');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$item = $xml -> createElement("description");
	$itemText = $xml -> createTextNode('Here are some MP3 recordings of the Wessex Male Choir rehearsals that you can download for your own personal use (practice makes perfect!).');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$owner = $xml -> createElement("itunes:owner");
	$channel -> appendChild($owner);

	$item = $xml -> createElement("itunes:name");
	$itemText = $xml -> createTextNode('Wessex Male Choir');
	$item -> appendChild($itemText);
	$owner -> appendChild($item);

	$item = $xml -> createElement("itunes:email");
	$itemText = $xml -> createTextNode('membership@wessexmalechoir.co.uk');
	$item -> appendChild($itemText);
	$owner -> appendChild($item);

	$item = $xml -> createElement("itunes:image");
	$itemText = $xml -> createTextNode('http://www.wessexmalechoir.co.uk/images/logo2.jpg');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$item = $xml -> createElement("itunes:category");
	$itemText = $xml -> createTextNode('Education');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	$item = $xml -> createElement("itunes:category");
	$itemText = $xml -> createTextNode('Music');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);

	foreach ($feed as $entry) {
		$url = $host . $entry['enclosure'];
		$length = ltrim($entry['length']);
		$length = str_replace(",", "", $length);
		$length2 = substr($length, 0);
		//get last 10 characters to get date of dd-mm-yyyy
		$pubdate = substr($entry['pubDate'], -10);
		$pubdate = @checkMyDate($pubdate);

		$longSummary = $entry['title'] . ' ' . $entry['summary'];
		$item = $xml -> createElement("item");

		$title = $xml -> createElement("title");
		$titleText = $xml -> createTextNode($entry['title']);
		$title -> appendChild($titleText);

		$author = $xml -> createElement("itunes:author");
		$authorText = $xml -> createTextNode('Wessex Mail Choir');
		$author -> appendChild($authorText);

		$subtitle = $xml -> createElement("itunes:subtitle");
		$subTitleText = $xml -> createTextNode($entry['title']);
		$subtitle -> appendChild($subTitleText);

		$summary = $xml -> createElement("itunes:summary");
		$summaryText = $xml -> createTextNode($longSummary);
		$summary -> appendChild($summaryText);

		$enclosure = $xml -> createElement("enclosure");
		$enclosure -> setAttribute('url', $url);
		$enclosure -> setAttribute('length', $length);
		$enclosure -> setAttribute('type', "audio/mp3");

		$pubDate = $xml -> createElement("pubDate");
		$pubDateText = $xml -> createTextNode($pubdate);
		$pubDate -> appendChild($pubDateText);

		$duration = $xml -> createElement("itunes:duration");
		$durationText = $xml -> createTextNode($entry['duration']);
		$duration -> appendChild($durationText);

		$keywords = $xml -> createElement("itunes:keywords");
		$keywordsText = $xml -> createTextNode('Music, Training');
		$keywords -> appendChild($keywordsText);

		$guid = $xml -> createElement("guid");
		$guidText = $xml -> createTextNode($url);
		$guid -> appendChild($guidText);

		$item -> appendChild($title);
		$item -> appendChild($author);
		$item -> appendChild($subtitle);
		$item -> appendChild($summary);
		$item -> appendChild($enclosure);
		$item -> appendChild($pubDate);
		$item -> appendChild($duration);
		$item -> appendChild($keywords);
		$item -> appendChild($guid);

		/**
		 * add each item
		*/
		$channel -> appendChild($item);

	}
	$explicit = $xml -> createElement('itunes:explicit');
	$explicitText = $xml -> createTextNode('no');
	$explicit -> appendChild($explicitText);
	$channel -> appendChild($explicit);

	$item = $xml -> createElement("itunes:block");
	$itemText = $xml -> createTextNode('no');
	$item -> appendChild($itemText);
	$channel -> appendChild($item);
	/**
	 * file creation finished, now save it to disk
	*/
	$xml -> save($destination);
	/**
	 * now save it top dropbox
	*/
	$f = fopen($destination, "rb");
	$result = $dbxClient->uploadFile("/Public/wmc_rehearsals.xml", dbx\WriteMode::force(), $f);

	print_r($result);

}

/**
 * @function fetchPage
 * fetches a remote HTML page
 */
function fetchPage($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, false);
	$html = curl_exec($ch);
	$html = str_replace("&nbsp;", "", $html);
	curl_close($ch);
	return $html;
}

/**
 * @function main
 * loads the table of mp3's, strips unwanted details from the table and creates a URL of
 * mp3s
 * passes mp3 array to rss.createFeed($songs) to create the podcast XML and save it in
 * a local (public facing) Dropbox file
 */
function main() {
	global $host, $file, $url;
	$str = fetchPage($url);
	$doc = new DOMDocument;
	$doc -> LoadHTML($str);
	$str = null;
	$newstr = null;
	$headings = Array('title', 'length', 'duration', 'pubDate', 'summary', );
	$xpath = new DOMXPath($doc);
	$songs = Array();
	$tablerows = $xpath -> query("//table/tr");
	if (!is_null($tablerows)) {
		foreach ($tablerows as $row) {
			$entry = Array();
			$depth = 0;
			if ($child = $row -> firstChild) {
				while ($child) {
					if ($child -> hasChildNodes()) {
						$kids = $child -> childNodes;
						foreach ($kids as $kid) {
							$attributes = $kid -> attributes;
							if (!is_null($attributes)) {
								foreach ($attributes as $attr) {
									if ($attr -> name == 'href') {
										@array_push($entry['enclosure'] = $attr -> value);
									}
								}
							}
						}
					}
					@array_push($entry[$headings[$depth]] = $child -> nodeValue);
					$depth++;
					$child = $child -> nextSibling;
				}
			}
			array_push($songs, $entry);
		}
	}
	// don't want the title
	array_shift($songs);
	createFeed($songs);

}

main();
?>